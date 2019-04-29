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
use BayrellLang\OpCodes\OpFlags;
class OpClassDeclare extends BaseOpCode{
	public $op;
	public $class_name;
	public $class_extends;
	public $class_implements;
	/*public serializable Vector<OpAssignDeclare> class_variables = null;*/
	public $childs;
	public $class_template;
	public $flags;
	public $annotations;
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
	 * Has Annotations
	 */
	function hasAnnotations(){
		return $this->annotations != null && $this->annotations->count() > 0;
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
	/* ======================= Class Init Functions ======================= */
	public function getClassName(){return "BayrellLang.OpCodes.OpClassDeclare";}
	public static function getCurrentClassName(){return "BayrellLang.OpCodes.OpClassDeclare";}
	public static function getParentClassName(){return "BayrellLang.OpCodes.BaseOpCode";}
	protected function _init(){
		parent::_init();
	}
	public function assignObject($obj){
		if ($obj instanceof OpClassDeclare){
			$this->op = rtl::_clone($obj->op);
			$this->class_name = rtl::_clone($obj->class_name);
			$this->class_extends = rtl::_clone($obj->class_extends);
			$this->class_implements = rtl::_clone($obj->class_implements);
			$this->childs = rtl::_clone($obj->childs);
			$this->class_template = rtl::_clone($obj->class_template);
			$this->flags = rtl::_clone($obj->flags);
			$this->annotations = rtl::_clone($obj->annotations);
		}
		parent::assignObject($obj);
	}
	public function assignValue($variable_name, $value, $sender = null){
		if ($variable_name == "op")$this->op = rtl::convert($value,"string","op_class","");
		else if ($variable_name == "class_name")$this->class_name = rtl::convert($value,"string","","");
		else if ($variable_name == "class_extends")$this->class_extends = rtl::convert($value,"BayrellLang.OpCodes.BaseOpCode","","");
		else if ($variable_name == "class_implements")$this->class_implements = rtl::convert($value,"Runtime.Vector",null,"string");
		else if ($variable_name == "childs")$this->childs = rtl::convert($value,"Runtime.Vector",null,"BayrellLang.OpCodes.BaseOpCode");
		else if ($variable_name == "class_template")$this->class_template = rtl::convert($value,"Runtime.Vector",null,"BayrellLang.OpCodes.BaseOpCode");
		else if ($variable_name == "flags")$this->flags = rtl::convert($value,"BayrellLang.OpCodes.OpFlags",null,"");
		else if ($variable_name == "annotations")$this->annotations = rtl::convert($value,"Runtime.Vector",null,"OpAnnotation");
		else parent::assignValue($variable_name, $value, $sender);
	}
	public function takeValue($variable_name, $default_value = null){
		if ($variable_name == "op") return $this->op;
		else if ($variable_name == "class_name") return $this->class_name;
		else if ($variable_name == "class_extends") return $this->class_extends;
		else if ($variable_name == "class_implements") return $this->class_implements;
		else if ($variable_name == "childs") return $this->childs;
		else if ($variable_name == "class_template") return $this->class_template;
		else if ($variable_name == "flags") return $this->flags;
		else if ($variable_name == "annotations") return $this->annotations;
		return parent::takeValue($variable_name, $default_value);
	}
	public static function getFieldsList($names, $flag=0){
		if (($flag | 3)==3){
			$names->push("op");
			$names->push("class_name");
			$names->push("class_extends");
			$names->push("class_implements");
			$names->push("childs");
			$names->push("class_template");
			$names->push("flags");
			$names->push("annotations");
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