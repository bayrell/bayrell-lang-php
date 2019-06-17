<?php
/*!
 *  Bayrell Common Languages Transcompiler
 *
 *  (c) Copyright 2016-2018 "Ildar Bikmamatov" <support@bayrell.org>
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *      https://www.bayrell.org/licenses/APACHE-LICENSE-2.0.html
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */
namespace BayrellLang\Search;
use Runtime\rs;
use Runtime\rtl;
use Runtime\Map;
use Runtime\Vector;
use Runtime\Dict;
use Runtime\Collection;
use Runtime\IntrospectionInfo;
use Runtime\UIStruct;
use Runtime\CoreStruct;
use Runtime\RuntimeUtils;
use Runtime\Interfaces\ContextInterface;
use BayrellLang\Utils as BayrellLangUtils;
use BayrellLang\LangBay\ParserBayFactory;
use BayrellLang\Search\ModuleFind;
use BayrellLang\Search\ModuleInfo;
class ModuleSearchProvider extends CoreStruct{
	protected $__cache_path;
	protected $__search_path;
	/**
	 * Init ModuleSearchProvider
	 */
	static function init($context, $provider){
		$config = $context->getConfig();
		$base_path = $context->getBasePath();
		$cache = RuntimeUtils::getItem($config, (new Vector())->push("BayrellLang")->push("cache"), "", "string");
		$search = RuntimeUtils::getItem($config, (new Vector())->push("BayrellLang")->push("search"), (new Vector()), "Runtime.Collection", "string");
		$search = $search->map(function ($item) use (&$base_path){
			return rtl::toString($base_path) . rtl::toString($item);
		});
		$provider = $provider->copy((new Map())->set("cache_path", rtl::toString($base_path) . rtl::toString($cache))->set("search_path", $search));
		return $provider;
	}
	/**
	 * Find module by path
	 * @param string search_path
	 * @param string module_name
	 */
	static function findModuleByPath($context, $search_path, $module_name){
		$flag = false;
		$fs = $context->getProvider("default.fs");
		$find = ModuleFind::create($search_path, $module_name);
		$flag = (new \Runtime\Callback($fs->getClassName(), "fileExists"))($context, $fs, $find->module_path_description);
		if ($flag){
			return $find;
		}
		return null;
	}
	/**
	 * Find module in search path
	 * @param ContextInterface context
	 * @param ModuleSearchProvider provider
	 * @param string module_name
	 * @return string Path to module
	 */
	static function findModule($context, $provider, $module_name){
		$res = null;
		$search_path = $provider->search_path;
		for ($i = 0; $i < $search_path->count(); $i++){
			$path = $search_path->item($i);
			$module_find = static::findModuleByPath($context, $path, $module_name);
			if ($module_find != null){
				$res = $module_find;
				break;
			}
		}
		return $res;
	}
	/**
	 * Find item in search path
	 * @param ContextInterface context
	 * @param ModuleSearchProvider provider
	 * @param string module_name
	 * @return string Path to module
	 */
	static function findModuleItem($context, $provider, $item_name){
		$sz = rs::strlen($item_name);
		$i = $sz;
		while ($i > 0){
			$ch = mb_substr($item_name, $i, 1);
			if ($ch == "." || $ch == "/"){
				$module_name = rs::substr($item_name, 0, $i);
				$file_name = rs::substr($item_name, $i + 1);
				$find = static::findModule($context, $provider, $module_name);
				if ($find != null){
					$arr = rs::split((new Vector())->push(".")->push("/"), $file_name);
					$file_name = rs::implode("/", $arr);
					$find = $find->copy((new Map())->set("item_name", $item_name)->set("file_name", $file_name)->set("file_path", rtl::toString($find->module_path) . "/bay/" . rtl::toString($file_name) . ".bay"));
					return $find;
				}
			}
			$i--;
		}
		return null;
	}
	/**
	 * Read module and return ModuleInfo
	 * @param ContextInterface context
	 * @param ModuleSearchProvider provider
	 * @param string module_name
	 * @return ModuleInfo
	 */
	static function readModuleFromCache($context, $provider, $module_name){
		$fs = $context->getProvider("default.fs");
		$module_cache_name = rs::replace("/", "|", $module_name);
		$file_path = rtl::toString($provider->cache_path) . "/" . rtl::toString($module_cache_name) . ".json";
		$file_exists = (new \Runtime\Callback($fs->getClassName(), "fileExists"))($context, $fs, $file_path);
		/*return null;*/
		if ($file_exists){
			$json_str = (new \Runtime\Callback($fs->getClassName(), "readFile"))($context, $fs, $file_path);
			$info = RuntimeUtils::json_decode($json_str);
			if ($info != null && $info instanceof ModuleInfo){
				if ($info->op_code != null){
					$info = ModuleInfo::analyzeOpCode($info);
				}
				return $info;
			}
		}
		return null;
	}
	/**
	 * Save module to file cache
	 * @param ContextInterface context
	 * @param ModuleSearchProvider provider
	 * @param string module_name
	 * @return ModuleInfo
	 */
	static function saveModuleToCache($context, $provider, $info){
		$fs = $context->getProvider("default.fs");
		$module_cache_name = rs::replace("/", "|", $info->module_name);
		$file_path = rtl::toString($provider->cache_path) . "/" . rtl::toString($module_cache_name) . ".json";
		/* Create cache folder */
		(new \Runtime\Callback($fs->getClassName(), "makeDir"))($context, $fs, $provider->cache_path, true);
		/* clear opcode */
		/*info <= op_code <= null;*/
		/* Save module info */
		$json_str = RuntimeUtils::json_encode($info, 1);
		(new \Runtime\Callback($fs->getClassName(), "saveFile"))($context, $fs, $file_path, $json_str);
	}
	/**
	 * Read module and return ModuleInfo
	 * @param ContextInterface context
	 * @param ModuleSearchProvider provider
	 * @param string module_name
	 * @return ModuleInfo
	 */
	static function readModule($context, $provider, $module_name){
		$info = null;
		/* Read module info from cache */
		$info = static::readModuleFromCache($context, $provider, $module_name);
		if ($info != null){
			return $info;
		}
		$module_find = static::findModule($context, $provider, $module_name);
		if ($module_find == null){
			return null;
		}
		$fs = $context->getProvider("default.fs");
		$file_description_path = (new \Runtime\Callback($module_find->getClassName(), "getModuleDescriptionPath"))($module_find);
		/* Parse module */
		$source = (new \Runtime\Callback($fs->getClassName(), "readFile"))($context, $fs, $file_description_path);
		$op_code = BayrellLangUtils::getAST($context, new ParserBayFactory(), $source);
		/* Create new ModuleInfo */
		$info = new ModuleInfo();
		$info = $info->copy( new Map([ "op_code" => $op_code ])  );
		$info = $info->copy( new Map([ "module_name" => $module_name ])  );
		$info = $info->copy( new Map([ "module_path" => $module_find->module_path ])  );
		$info = $info->copy( new Map([ "parent_module_name" => $module_find->parent_module_name ])  );
		$info = $info->copy( new Map([ "sub_module_name" => $module_find->sub_module_name ])  );
		$info = $info->copy( new Map([ "submodule" => $module_find->submodule ])  );
		/* Analyze op code */
		$info = ModuleInfo::analyzeOpCode($info);
		/*info <= op_code <= null;*/
		/* Save to cache */
		static::saveModuleToCache($context, $provider, $info);
		return $info;
	}
	/**
	 * Returns list of the modules
	 */
	static function _scanModules($context, $provider, $modules_list, $res_list){
		for ($i = 0; $i < $modules_list->count(); $i++){
			$module_name = $modules_list->item($i);
			$pos = $res_list->find(function ($info, $module_name){
				return $info->module_name == $module_name;
			}, $module_name);
			if ($pos != -1){
				continue;
			}
			$info = static::readModule($context, $provider, $module_name);
			if ($info != null){
				$res_list->push($info);
				if ($info->required_modules != null){
					static::_scanModules($context, $provider, $info->required_modules->keys(), $res_list);
				}
			}
		}
	}
	/**
	 * Returns list of the modules
	 */
	static function scanModules($context, $provider, $modules_list){
		$res_list = new Vector();
		static::_scanModules($context, $provider, $modules_list, $res_list);
		/* Add submodules */
		$submodules = static::getSubmodules($res_list->toCollection());
		$submodules_keys = $submodules->keys();
		for ($i = 0; $i < $submodules_keys->count(); $i++){
			$module_name = $submodules_keys->item($i);
			$pos = $res_list->find(function ($info, $module_name){
				return $info->module_name == $module_name;
			}, $module_name);
			if ($pos != -1){
				$info = $res_list->item($pos);
				$info = $info->copy( new Map([ "sub_modules" => $submodules->item($module_name) ])  );
				$res_list->set($pos, $info);
			}
		}
		/*rtl::dump(res_list);*/
		return $res_list->toCollection();
	}
	/**
	 * Returns submodules
	 */
	static function getSubmodules($modules){
		$res = new Map();
		for ($i = 0; $i < $modules->count(); $i++){
			$info = $modules->item($i);
			if ($info->submodule){
				$arr = $res->get($info->parent_module_name, null);
				if ($arr == null){
					$arr = new Vector();
				}
				$arr->push($info->module_name);
				$res->set($info->parent_module_name, $arr);
			}
		}
		$res = $res->map(function ($key, $item){
			return $item->toCollection();
		});
		return $res->toDict();
	}
	/* ======================= Class Init Functions ======================= */
	public function getClassName(){return "BayrellLang.Search.ModuleSearchProvider";}
	public static function getCurrentNamespace(){return "BayrellLang.Search";}
	public static function getCurrentClassName(){return "BayrellLang.Search.ModuleSearchProvider";}
	public static function getParentClassName(){return "Runtime.CoreStruct";}
	protected function _init(){
		parent::_init();
		$this->__cache_path = "";
		$this->__search_path = null;
	}
	public function assignObject($obj){
		if ($obj instanceof ModuleSearchProvider){
			$this->__cache_path = $obj->__cache_path;
			$this->__search_path = $obj->__search_path;
		}
		parent::assignObject($obj);
	}
	public function assignValue($variable_name, $value, $sender = null){
		if ($variable_name == "cache_path")$this->__cache_path = rtl::convert($value,"string","","");
		else if ($variable_name == "search_path")$this->__search_path = rtl::convert($value,"Runtime.Collection",null,"string");
		else parent::assignValue($variable_name, $value, $sender);
	}
	public function takeValue($variable_name, $default_value = null){
		if ($variable_name == "cache_path") return $this->__cache_path;
		else if ($variable_name == "search_path") return $this->__search_path;
		return parent::takeValue($variable_name, $default_value);
	}
	public static function getFieldsList($names, $flag=0){
		if (($flag | 3)==3){
			$names->push("cache_path");
			$names->push("search_path");
		}
	}
	public static function getFieldInfoByName($field_name){
		return null;
	}
	public static function getMethodsList($names){
	}
	public static function getMethodInfoByName($method_name){
		return null;
	}
	public function __get($key){ return $this->takeValue($key); }
	public function __set($key, $value){throw new \Runtime\Exceptions\AssignStructValueError($key);}
}