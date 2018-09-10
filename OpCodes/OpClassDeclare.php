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
use Runtime\rtl;
use Runtime\Map;
use Runtime\Vector;
use BayrellLang\OpCodes\BaseOpCode;
use BayrellLang\OpCodes\OpFlags;
class OpClassDeclare extends BaseOpCode{
	public $op;
	public $class_name;
	public $class_extends;
	public $class_implements;
	public $class_variables;
	public $childs;
	public $class_template;
	public $flags;
	public function getClassName(){return "BayrellLang.OpCodes.OpClassDeclare";}
	public static function getParentClassName(){return "BayrellLang.OpCodes.BaseOpCode";}
	protected function _init(){
		parent::_init();
		$this->op = "op_class";
		$this->class_name = "";
		$this->class_extends = "";
		$this->class_implements = null;
		$this->class_variables = null;
		$this->childs = null;
		$this->class_template = null;
		$this->flags = null;
	}
	public function assignValue($variable_name, $value){
		if ($variable_name == "op") $this->op = rtl::correct($value, "string", "op_class", "");
		else if ($variable_name == "class_name") $this->class_name = rtl::correct($value, "string", "", "");
		else if ($variable_name == "class_extends") $this->class_extends = rtl::correct($value, "BayrellLang.OpCodes.BaseOpCode", "", "");
		else if ($variable_name == "class_implements") $this->class_implements = rtl::correct($value, "Runtime.Vector", null, "string");
		else if ($variable_name == "class_variables") $this->class_variables = rtl::correct($value, "Runtime.Vector", null, "OpAssignDeclare");
		else if ($variable_name == "childs") $this->childs = rtl::correct($value, "Runtime.Vector", null, "BayrellLang.OpCodes.BaseOpCode");
		else if ($variable_name == "class_template") $this->class_template = rtl::correct($value, "Runtime.Vector", null, "BayrellLang.OpCodes.BaseOpCode");
		else if ($variable_name == "flags") $this->flags = rtl::correct($value, "BayrellLang.OpCodes.OpFlags", null, "");
		else parent::assignValue($variable_name, $value);
	}
	public function takeValue($variable_name, $default_value = null){
		if ($variable_name == "op") return $this->op;
		else if ($variable_name == "class_name") return $this->class_name;
		else if ($variable_name == "class_extends") return $this->class_extends;
		else if ($variable_name == "class_implements") return $this->class_implements;
		else if ($variable_name == "class_variables") return $this->class_variables;
		else if ($variable_name == "childs") return $this->childs;
		else if ($variable_name == "class_template") return $this->class_template;
		else if ($variable_name == "flags") return $this->flags;
		return parent::takeValue($variable_name, $default_value);
	}
	public function getVariablesNames($names){
		parent::getVariablesNames($names);
		$names->push("op");
		$names->push("class_name");
		$names->push("class_extends");
		$names->push("class_implements");
		$names->push("class_variables");
		$names->push("childs");
		$names->push("class_template");
		$names->push("flags");
	}
	/**
	 * Returns classname of the object
	 * @return string
	 */
	function getClassName(){
		return "BayrellLang.OpCodes.OpClassDeclare";
	}
	/**
	 * Read is Flag
	 */
	function isFlag($name){
		if ($this->flags == null){
			return false;
		}
		if (!OpFlags::hasFlag($name)){
			return false;
		}
		return $this->flags->takeValue($name);
	}
	/**
	 * Constructor
	 */
	function __construct(){
		parent::__construct();
		$this->class_implements = new Vector();
		$this->class_variables = new Vector();
		$this->class_template = new Vector();
		$this->childs = new Vector();
	}
	/**
	 * Destructor
	 */
	function __destruct(){
		parent::__destruct();
	}
}