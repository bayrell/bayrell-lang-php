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
class OpIfElse extends BaseOpCode{
	public $op;
	public $condition;
	public $if_true;
	/**
	 * Constructor
	 */
	function __construct($condition = null, $if_true = null){
		parent::__construct();
		$this->condition = $condition;
		$this->if_true = $if_true;
	}
	/**
	 * Destructor
	 */
	function __destruct(){
		parent::__destruct();
	}
	/* ======================= Class Init Functions ======================= */
	public function getClassName(){return "BayrellLang.OpCodes.OpIfElse";}
	public static function getCurrentClassName(){return "BayrellLang.OpCodes.OpIfElse";}
	public static function getParentClassName(){return "BayrellLang.OpCodes.BaseOpCode";}
	protected function _init(){
		parent::_init();
	}
	public function assignObject($obj){
		if ($obj instanceof OpIfElse){
			$this->op = rtl::_clone($obj->op);
			$this->condition = rtl::_clone($obj->condition);
			$this->if_true = rtl::_clone($obj->if_true);
		}
		parent::assignObject($obj);
	}
	public function assignValue($variable_name, $value, $sender = null){
		if ($variable_name == "op")$this->op = rtl::convert($value,"string","op_if_else","");
		else if ($variable_name == "condition")$this->condition = rtl::convert($value,"BayrellLang.OpCodes.BaseOpCode",null,"");
		else if ($variable_name == "if_true")$this->if_true = rtl::convert($value,"Runtime.Vector",null,"BayrellLang.OpCodes.BaseOpCode");
		else parent::assignValue($variable_name, $value, $sender);
	}
	public function takeValue($variable_name, $default_value = null){
		if ($variable_name == "op") return $this->op;
		else if ($variable_name == "condition") return $this->condition;
		else if ($variable_name == "if_true") return $this->if_true;
		return parent::takeValue($variable_name, $default_value);
	}
	public static function getFieldsList($names, $flag=0){
		if (($flag | 3)==3){
			$names->push("op");
			$names->push("condition");
			$names->push("if_true");
		}
	}
	public static function getFieldInfoByName($field_name){
		return null;
	}
}