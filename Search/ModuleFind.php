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
class ModuleFind extends CoreStruct{
	protected $__module_name;
	protected $__module_path;
	protected $__module_path_description;
	protected $__parent_module_name;
	protected $__sub_module_name;
	protected $__submodule;
	/* Item search */
	protected $__item_name;
	protected $__file_name;
	protected $__file_path;
	/**
	 * Returns ModuleDescriptionPath
	 */
	static function getParentModule($module_name){
		$__memorize_value = rtl::_memorizeValue("BayrellLang.Search.ModuleFind::getParentModule", func_get_args());
		if ($__memorize_value != rtl::$_memorize_not_found) return $__memorize_value;
		$pos = rs::strpos($module_name, "/");
		$parent_module_name = $module_name;
		if ($pos >= 0){
			$parent_module_name = rs::substr($module_name, 0, $pos);
		}
		$__memorize_value = $parent_module_name;
		rtl::_memorizeSave("BayrellLang.Search.ModuleFind::getParentModule", func_get_args(), $__memorize_value);
		return $__memorize_value;
	}
	/**
	 * Returns ModuleDescriptionPath
	 */
	static function getModuleDescriptionPath($item){
		if ($item->submodule){
			return rtl::toString($item->module_path) . "/ModuleDescription.bay";
		}
		return rtl::toString($item->module_path) . "/bay/ModuleDescription.bay";
	}
	/**
	 * Split module
	 */
	static function create($search_path, $module_name){
		$submodule = false;
		$parent_module_name = "";
		$sub_module_name = "";
		$module_path = "";
		$module_path_description = "";
		$arr = rs::explode("/", $module_name);
		if ($arr->count() >= 2){
			$submodule = true;
			$parent_module_name = $arr->item(0);
			$arr = $arr->removeFirstIm();
			$sub_module_name = rs::implode("/", $arr);
			$module_path = rtl::toString($search_path) . "/" . rtl::toString($parent_module_name) . "/bay/" . rtl::toString($sub_module_name);
			$module_path_description = rtl::toString($module_path) . "/ModuleDescription.bay";
		}
		else {
			$parent_module_name = $arr->item(0);
			$module_path = rtl::toString($search_path) . "/" . rtl::toString($module_name);
			$module_path_description = rtl::toString($module_path) . "/bay/ModuleDescription.bay";
		}
		return new ModuleFind((new Map())->set("module_name", $module_name)->set("module_path", $module_path)->set("submodule", $submodule)->set("parent_module_name", $parent_module_name)->set("sub_module_name", $sub_module_name)->set("module_path_description", $module_path_description));
	}
	/* ======================= Class Init Functions ======================= */
	public function getClassName(){return "BayrellLang.Search.ModuleFind";}
	public static function getCurrentNamespace(){return "BayrellLang.Search";}
	public static function getCurrentClassName(){return "BayrellLang.Search.ModuleFind";}
	public static function getParentClassName(){return "Runtime.CoreStruct";}
	protected function _init(){
		parent::_init();
		$this->__module_name = "";
		$this->__module_path = "";
		$this->__module_path_description = "";
		$this->__parent_module_name = "";
		$this->__sub_module_name = "";
		$this->__submodule = false;
		$this->__item_name = "";
		$this->__file_name = "";
		$this->__file_path = "";
	}
	public function assignObject($obj){
		if ($obj instanceof ModuleFind){
			$this->__module_name = $obj->__module_name;
			$this->__module_path = $obj->__module_path;
			$this->__module_path_description = $obj->__module_path_description;
			$this->__parent_module_name = $obj->__parent_module_name;
			$this->__sub_module_name = $obj->__sub_module_name;
			$this->__submodule = $obj->__submodule;
			$this->__item_name = $obj->__item_name;
			$this->__file_name = $obj->__file_name;
			$this->__file_path = $obj->__file_path;
		}
		parent::assignObject($obj);
	}
	public function assignValue($variable_name, $value, $sender = null){
		if ($variable_name == "module_name")$this->__module_name = rtl::convert($value,"string","","");
		else if ($variable_name == "module_path")$this->__module_path = rtl::convert($value,"string","","");
		else if ($variable_name == "module_path_description")$this->__module_path_description = rtl::convert($value,"string","","");
		else if ($variable_name == "parent_module_name")$this->__parent_module_name = rtl::convert($value,"string","","");
		else if ($variable_name == "sub_module_name")$this->__sub_module_name = rtl::convert($value,"string","","");
		else if ($variable_name == "submodule")$this->__submodule = rtl::convert($value,"bool",false,"");
		else if ($variable_name == "item_name")$this->__item_name = rtl::convert($value,"string","","");
		else if ($variable_name == "file_name")$this->__file_name = rtl::convert($value,"string","","");
		else if ($variable_name == "file_path")$this->__file_path = rtl::convert($value,"string","","");
		else parent::assignValue($variable_name, $value, $sender);
	}
	public function takeValue($variable_name, $default_value = null){
		if ($variable_name == "module_name") return $this->__module_name;
		else if ($variable_name == "module_path") return $this->__module_path;
		else if ($variable_name == "module_path_description") return $this->__module_path_description;
		else if ($variable_name == "parent_module_name") return $this->__parent_module_name;
		else if ($variable_name == "sub_module_name") return $this->__sub_module_name;
		else if ($variable_name == "submodule") return $this->__submodule;
		else if ($variable_name == "item_name") return $this->__item_name;
		else if ($variable_name == "file_name") return $this->__file_name;
		else if ($variable_name == "file_path") return $this->__file_path;
		return parent::takeValue($variable_name, $default_value);
	}
	public static function getFieldsList($names, $flag=0){
		if (($flag | 3)==3){
			$names->push("module_name");
			$names->push("module_path");
			$names->push("module_path_description");
			$names->push("parent_module_name");
			$names->push("sub_module_name");
			$names->push("submodule");
			$names->push("item_name");
			$names->push("file_name");
			$names->push("file_path");
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