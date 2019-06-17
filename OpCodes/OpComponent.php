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
namespace BayrellLang\OpCodes;
use Runtime\rs;
use Runtime\rtl;
use Runtime\Map;
use Runtime\Vector;
use Runtime\Dict;
use Runtime\Collection;
use Runtime\IntrospectionInfo;
use Runtime\UIStruct;
use BayrellLang\OpCodes\BaseOpCode;
class OpComponent extends BaseOpCode{
	public $op;
	public $name;
	public $alias;
	public $args;
	/**
	 * Assign all data from other object
	 * @param CoreObject obj
	 */
	function assign($obj){
		if ($obj instanceof $OpChilds){
			$this->name = rtl::_clone($obj->name);
			$this->alias = rtl::_clone($obj->alias);
			$this->args = rtl::_clone($obj->args);
		}
		parent::assign($obj);
	}
	/**
	 * Constructor
	 */
	function __construct($name = "", $alias = "", $args = null){
		parent::__construct();
		$this->name = $name;
		$this->alias = $alias;
		$this->args = $args;
	}
	/**
	 * Destructor
	 */
	function __destruct(){
		parent::__destruct();
	}
	/* ======================= Class Init Functions ======================= */
	public function getClassName(){return "BayrellLang.OpCodes.OpComponent";}
	public static function getCurrentNamespace(){return "BayrellLang.OpCodes";}
	public static function getCurrentClassName(){return "BayrellLang.OpCodes.OpComponent";}
	public static function getParentClassName(){return "BayrellLang.OpCodes.BaseOpCode";}
	protected function _init(){
		parent::_init();
	}
	public function assignObject($obj){
		if ($obj instanceof OpComponent){
			$this->op = rtl::_clone($obj->op);
			$this->name = rtl::_clone($obj->name);
			$this->alias = rtl::_clone($obj->alias);
			$this->args = rtl::_clone($obj->args);
		}
		parent::assignObject($obj);
	}
	public function assignValue($variable_name, $value, $sender = null){
		if ($variable_name == "op")$this->op = rtl::convert($value,"string","op_component","");
		else if ($variable_name == "name")$this->name = rtl::convert($value,"BayrellLang.OpCodes.BaseOpCode","","");
		else if ($variable_name == "alias")$this->alias = rtl::convert($value,"string","","");
		else if ($variable_name == "args")$this->args = rtl::convert($value,"Runtime.Map",null,"BayrellLang.OpCodes.BaseOpCode");
		else parent::assignValue($variable_name, $value, $sender);
	}
	public function takeValue($variable_name, $default_value = null){
		if ($variable_name == "op") return $this->op;
		else if ($variable_name == "name") return $this->name;
		else if ($variable_name == "alias") return $this->alias;
		else if ($variable_name == "args") return $this->args;
		return parent::takeValue($variable_name, $default_value);
	}
	public static function getFieldsList($names, $flag=0){
		if (($flag | 3)==3){
			$names->push("op");
			$names->push("name");
			$names->push("alias");
			$names->push("args");
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
}