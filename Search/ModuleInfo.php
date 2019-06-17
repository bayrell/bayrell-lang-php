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
use BayrellLang\Utils as BayrellLangUtils;
use BayrellLang\LangBay\ParserBayFactory;
use BayrellLang\OpCodes\BaseOpCode;
use BayrellLang\OpCodes\OpClassDeclare;
use BayrellLang\OpCodes\OpFunctionDeclare;
use BayrellLang\OpCodes\OpNamespace;
use BayrellLang\OpCodes\OpNope;
use BayrellLang\OpCodes\OpUse;
use BayrellLang\OpCodes\OpString;
use BayrellLang\OpCodes\OpMap;
use BayrellLang\OpCodes\OpVector;
class ModuleInfo extends CoreStruct{
	protected $__module_name;
	protected $__version;
	protected $__submodule;
	protected $__module_path;
	protected $__parent_module_name;
	protected $__sub_module_name;
	protected $__interfaces;
	protected $__required_modules;
	protected $__sub_modules;
	protected $__files;
	protected $__entities;
	protected $__op_code;
	/**
	 * Find module info from collection by module_name
	 */
	static function findModuleInfo($modules, $module_name){
		$__memorize_value = rtl::_memorizeValue("BayrellLang.Search.ModuleInfo::findModuleInfo", func_get_args());
		if ($__memorize_value != rtl::$_memorize_not_found) return $__memorize_value;
		$pos = $modules->find(function ($item, $module_name){
			return $item->module_name == $module_name;
		}, $module_name);
		if ($pos == -1){
			$__memorize_value = null;
			rtl::_memorizeSave("BayrellLang.Search.ModuleInfo::findModuleInfo", func_get_args(), $__memorize_value);
			return $__memorize_value;
		}
		$__memorize_value = $modules->item($pos);
		rtl::_memorizeSave("BayrellLang.Search.ModuleInfo::findModuleInfo", func_get_args(), $__memorize_value);
		return $__memorize_value;
	}
	/**
	 * Returns required modules
	 */
	static function getRequiredModules($info){
		$__memorize_value = rtl::_memorizeValue("BayrellLang.Search.ModuleInfo::getRequiredModules", func_get_args());
		if ($__memorize_value != rtl::$_memorize_not_found) return $__memorize_value;
		$__memorize_value = $info->requiredModules->keys();
		rtl::_memorizeSave("BayrellLang.Search.ModuleInfo::getRequiredModules", func_get_args(), $__memorize_value);
		return $__memorize_value;
	}
	/**
	 * Returns module version
	 * @params OpFunctionDeclare op_code
	 * @return string
	 */
	static function analyzeModuleVersion($op_code){
		if ($op_code->is_lambda == true){
			$op_item = $op_code->childs->get(0, null);
			if ($op_item instanceof OpString){
				return $op_item->value;
			}
		}
		return "";
	}
	/**
	 * Returns required modules
	 * @params OpFunctionDeclare op_code
	 * @return Dict<string>
	 */
	static function analyzeRequiredModules($op_code){
		$modules = new Map();
		if ($op_code->is_lambda == true){
			$op_item = $op_code->childs->get(0, null);
			if ($op_item instanceof OpMap){
				$values = $op_item->values;
				$keys = $values->keys();
				for ($i = 0; $i < $keys->count(); $i++){
					$module_name = $keys->item($i);
					$value = $values->item($module_name);
					if ($value instanceof OpString){
						$modules->set($module_name, $value->value);
					}
				}
			}
		}
		return $modules->toDict();
	}
	/**
	 * Returns module files
	 * @params OpFunctionDeclare op_code
	 * @return Dict<string>
	 */
	static function analyzeModuleFiles($op_code){
		$files = new Vector();
		if ($op_code->is_lambda == true){
			$items = $op_code->childs->get(0, null);
			for ($i = 0; $i < $items->values->count(); $i++){
				$op_item = $items->values->item($i);
				if ($op_item instanceof OpString){
					$files->push($op_item->value);
				}
			}
		}
		return $files->toCollection();
	}
	/**
	 * Analyze Module OpCode ClassDeclare
	 */
	static function analyzeOpClassDeclare($info, $op_code, $space, $uses, $modules){
		$interfaces = new Vector();
		/* Parse module interfaces */
		for ($i = 0; $i < $op_code->class_implements->count(); $i++){
			$name = $op_code->class_implements->item($i);
			if ($modules->has($name)){
				$interfaces->push($modules->item($name));
			}
		}
		/* Parse module childs */
		for ($i = 0; $i < $op_code->childs->count(); $i++){
			$op_item = $op_code->childs->item($i);
			if ($op_item instanceof OpFunctionDeclare){
				if ($op_item->name == "getModuleVersion"){
					$version = static::analyzeModuleVersion($op_item);
					$info = $info->copy( new Map([ "version" => $version ])  );
				}
				else if ($op_item->name == "requiredModules"){
					$modules = static::analyzeRequiredModules($op_item);
					$info = $info->copy( new Map([ "required_modules" => $modules ])  );
				}
				else if ($op_item->name == "getModuleFiles"){
					$files = static::analyzeModuleFiles($op_item);
					$info = $info->copy( new Map([ "files" => $files ])  );
				}
			}
		}
		$info = $info->copy((new Map())->set("interfaces", $interfaces->toCollection()));
		return $info;
	}
	/**
	 * Analyze Module OpCode
	 */
	static function analyzeOpCode($info){
		$op_code = $info->op_code;
		if (!($op_code instanceof OpNope)){
			return $info;
		}
		$space = "";
		$uses = new Vector();
		$modules = new Map();
		for ($i = 0; $i < $op_code->childs->count(); $i++){
			$item = $op_code->childs->item($i);
			if ($item instanceof OpNamespace){
				$space = $item->value;
			}
			else if ($item instanceof OpUse){
				$alias_name = $item->alias_name;
				if ($alias_name == ""){
					$arr = rs::explode(".", $item->value);
					$alias_name = $arr->last();
				}
				$uses->push($item->value);
				$modules->set($alias_name, $item->value);
			}
			else if ($item instanceof OpClassDeclare){
				$info = static::analyzeOpClassDeclare($info, $item, $space, $uses->toCollection(), $modules->toDict());
			}
		}
		return $info;
	}
	/* ======================= Class Init Functions ======================= */
	public function getClassName(){return "BayrellLang.Search.ModuleInfo";}
	public static function getCurrentNamespace(){return "BayrellLang.Search";}
	public static function getCurrentClassName(){return "BayrellLang.Search.ModuleInfo";}
	public static function getParentClassName(){return "Runtime.CoreStruct";}
	protected function _init(){
		parent::_init();
		$this->__module_name = "";
		$this->__version = "";
		$this->__submodule = false;
		$this->__module_path = "";
		$this->__parent_module_name = "";
		$this->__sub_module_name = "";
		$this->__interfaces = null;
		$this->__required_modules = null;
		$this->__sub_modules = null;
		$this->__files = null;
		$this->__entities = null;
		$this->__op_code = null;
	}
	public function assignObject($obj){
		if ($obj instanceof ModuleInfo){
			$this->__module_name = $obj->__module_name;
			$this->__version = $obj->__version;
			$this->__submodule = $obj->__submodule;
			$this->__module_path = $obj->__module_path;
			$this->__parent_module_name = $obj->__parent_module_name;
			$this->__sub_module_name = $obj->__sub_module_name;
			$this->__interfaces = $obj->__interfaces;
			$this->__required_modules = $obj->__required_modules;
			$this->__sub_modules = $obj->__sub_modules;
			$this->__files = $obj->__files;
			$this->__entities = $obj->__entities;
			$this->__op_code = $obj->__op_code;
		}
		parent::assignObject($obj);
	}
	public function assignValue($variable_name, $value, $sender = null){
		if ($variable_name == "module_name")$this->__module_name = rtl::convert($value,"string","","");
		else if ($variable_name == "version")$this->__version = rtl::convert($value,"string","","");
		else if ($variable_name == "submodule")$this->__submodule = rtl::convert($value,"bool",false,"");
		else if ($variable_name == "module_path")$this->__module_path = rtl::convert($value,"string","","");
		else if ($variable_name == "parent_module_name")$this->__parent_module_name = rtl::convert($value,"string","","");
		else if ($variable_name == "sub_module_name")$this->__sub_module_name = rtl::convert($value,"string","","");
		else if ($variable_name == "interfaces")$this->__interfaces = rtl::convert($value,"Runtime.Collection",null,"string");
		else if ($variable_name == "required_modules")$this->__required_modules = rtl::convert($value,"Runtime.Dict",null,"string");
		else if ($variable_name == "sub_modules")$this->__sub_modules = rtl::convert($value,"Runtime.Dict",null,"string");
		else if ($variable_name == "files")$this->__files = rtl::convert($value,"Runtime.Collection",null,"string");
		else if ($variable_name == "entities")$this->__entities = rtl::convert($value,"Runtime.Collection",null,"Runtime.Dict");
		else if ($variable_name == "op_code")$this->__op_code = rtl::convert($value,"BayrellLang.OpCodes.BaseOpCode",null,"");
		else parent::assignValue($variable_name, $value, $sender);
	}
	public function takeValue($variable_name, $default_value = null){
		if ($variable_name == "module_name") return $this->__module_name;
		else if ($variable_name == "version") return $this->__version;
		else if ($variable_name == "submodule") return $this->__submodule;
		else if ($variable_name == "module_path") return $this->__module_path;
		else if ($variable_name == "parent_module_name") return $this->__parent_module_name;
		else if ($variable_name == "sub_module_name") return $this->__sub_module_name;
		else if ($variable_name == "interfaces") return $this->__interfaces;
		else if ($variable_name == "required_modules") return $this->__required_modules;
		else if ($variable_name == "sub_modules") return $this->__sub_modules;
		else if ($variable_name == "files") return $this->__files;
		else if ($variable_name == "entities") return $this->__entities;
		else if ($variable_name == "op_code") return $this->__op_code;
		return parent::takeValue($variable_name, $default_value);
	}
	public static function getFieldsList($names, $flag=0){
		if (($flag | 3)==3){
			$names->push("module_name");
			$names->push("version");
			$names->push("submodule");
			$names->push("module_path");
			$names->push("parent_module_name");
			$names->push("sub_module_name");
			$names->push("interfaces");
			$names->push("required_modules");
			$names->push("sub_modules");
			$names->push("files");
			$names->push("entities");
			$names->push("op_code");
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