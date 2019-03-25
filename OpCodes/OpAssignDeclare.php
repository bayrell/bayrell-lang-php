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
use BayrellLang\OpCodes\OpAnnotation;
use BayrellLang\OpCodes\OpDynamic;
use BayrellLang\OpCodes\OpFlags;
use BayrellLang\OpCodes\OpIdentifier;
use BayrellLang\OpCodes\OpTemplateIdentifier;
class OpAssignDeclare extends BaseOpCode{
	public $op;
	public $tp;
	public $name;
	public $value;
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
	function __construct($tp = null, $name = null, $value = null){
		parent::__construct();
		$this->tp = $tp;
		$this->name = $name;
		$this->value = $value;
	}
	/**
	 * Destructor
	 */
	function __destruct(){
		parent::__destruct();
	}
	/* ======================= Class Init Functions ======================= */
	public function getClassName(){return "BayrellLang.OpCodes.OpAssignDeclare";}
	public static function getCurrentClassName(){return "BayrellLang.OpCodes.OpAssignDeclare";}
	public static function getParentClassName(){return "BayrellLang.OpCodes.BaseOpCode";}
	protected function _init(){
		parent::_init();
	}
	public function assignObject($obj){
		if ($obj instanceof OpAssignDeclare){
			$this->op = rtl::_clone($obj->op);
			$this->tp = rtl::_clone($obj->tp);
			$this->name = rtl::_clone($obj->name);
			$this->value = rtl::_clone($obj->value);
			$this->flags = rtl::_clone($obj->flags);
			$this->annotations = rtl::_clone($obj->annotations);
		}
		parent::assignObject($obj);
	}
	public function assignValue($variable_name, $value, $sender = null){
		if ($variable_name == "op")$this->op = rtl::convert($value,"string","op_assign_declare","");
		else if ($variable_name == "tp")$this->tp = rtl::convert($value,"BayrellLang.OpCodes.BaseOpCode",null,"");
		else if ($variable_name == "name")$this->name = rtl::convert($value,"string",null,"");
		else if ($variable_name == "value")$this->value = rtl::convert($value,"BayrellLang.OpCodes.BaseOpCode",null,"");
		else if ($variable_name == "flags")$this->flags = rtl::convert($value,"BayrellLang.OpCodes.OpFlags",null,"");
		else if ($variable_name == "annotations")$this->annotations = rtl::convert($value,"Runtime.Vector",null,"BayrellLang.OpCodes.OpAnnotation");
		else parent::assignValue($variable_name, $value, $sender);
	}
	public function takeValue($variable_name, $default_value = null){
		if ($variable_name == "op") return $this->op;
		else if ($variable_name == "tp") return $this->tp;
		else if ($variable_name == "name") return $this->name;
		else if ($variable_name == "value") return $this->value;
		else if ($variable_name == "flags") return $this->flags;
		else if ($variable_name == "annotations") return $this->annotations;
		return parent::takeValue($variable_name, $default_value);
	}
	public static function getFieldsList($names, $flag=0){
		if (($flag | 3)==3){
			$names->push("op");
			$names->push("tp");
			$names->push("name");
			$names->push("value");
			$names->push("flags");
			$names->push("annotations");
		}
	}
	public static function getFieldInfoByName($field_name){
		return null;
	}
}