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
use Runtime\IntrospectionInfo;
use Runtime\UIStruct;
use BayrellLang\OpCodes\BaseOpCode;
class OpTryCatchChilds extends BaseOpCode{
	public $op;
	public $op_type;
	public $op_ident;
	public $childs;
	/**
	 * Constructor
	 */
	function __construct($op_type = null, $op_ident = null, $childs = null){
		parent::__construct();
		$this->op_type = $op_type;
		$this->op_ident = $op_ident;
		$this->childs = $childs;
	}
	/**
	 * Destructor
	 */
	function __destruct(){
		parent::__destruct();
	}
	/* ======================= Class Init Functions ======================= */
	public function getClassName(){return "BayrellLang.OpCodes.OpTryCatchChilds";}
	public static function getParentClassName(){return "BayrellLang.OpCodes.BaseOpCode";}
	protected function _init(){
		parent::_init();
	}
	public function assignObject($obj){
		if ($obj instanceof OpTryCatchChilds){
			$this->op = rtl::_clone($obj->op);
			$this->op_type = rtl::_clone($obj->op_type);
			$this->op_ident = rtl::_clone($obj->op_ident);
			$this->childs = rtl::_clone($obj->childs);
		}
		parent::assignObject($obj);
	}
	public function assignValue($variable_name, $value, $sender = null){
		if ($variable_name == "op")$this->op = rtl::correct($value,"string","op_try_catch_childs","");
		else if ($variable_name == "op_type")$this->op_type = rtl::correct($value,"BayrellLang.OpCodes.BaseOpCode",null,"");
		else if ($variable_name == "op_ident")$this->op_ident = rtl::correct($value,"BayrellLang.OpCodes.BaseOpCode",null,"");
		else if ($variable_name == "childs")$this->childs = rtl::correct($value,"Runtime.Vector",null,"BayrellLang.OpCodes.BaseOpCode");
		else parent::assignValue($variable_name, $value, $sender);
	}
	public function takeValue($variable_name, $default_value = null){
		if ($variable_name == "op") return $this->op;
		else if ($variable_name == "op_type") return $this->op_type;
		else if ($variable_name == "op_ident") return $this->op_ident;
		else if ($variable_name == "childs") return $this->childs;
		return parent::takeValue($variable_name, $default_value);
	}
	public static function getFieldsList($names, $flag=0){
		if (($flag | 3)==3){
			$names->push("op");
			$names->push("op_type");
			$names->push("op_ident");
			$names->push("childs");
		}
	}
	public static function getFieldInfoByName($field_name){
		return null;
	}
}