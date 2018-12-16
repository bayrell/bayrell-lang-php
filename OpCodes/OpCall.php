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
use Runtime\IntrospectionInfo;
use BayrellLang\OpCodes\BaseOpCode;
class OpCall extends BaseOpCode{
	public $op;
	public $value;
	public $args;
	public $is_await;
	/**
	 * Constructor
	 */
	function __construct($value = null, $args = null){
		parent::__construct();
		$this->value = $value;
		$this->args = $args;
	}
	/**
	 * Destructor
	 */
	function __destruct(){
		parent::__destruct();
	}
	/* ======================= Class Init Functions ======================= */
	public function getClassName(){return "BayrellLang.OpCodes.OpCall";}
	public static function getParentClassName(){return "BayrellLang.OpCodes.BaseOpCode";}
	protected function _init(){
		parent::_init();
		$this->op = "op_call";
		$this->value = null;
		$this->args = null;
		$this->is_await = false;
	}
	public function assignObject($obj){
		if ($obj instanceof OpCall){
			$this->op = rtl::_clone($obj->op);
			$this->value = rtl::_clone($obj->value);
			$this->args = rtl::_clone($obj->args);
			$this->is_await = rtl::_clone($obj->is_await);
		}
		parent::assignObject($obj);
	}
	public function assignValue($variable_name, $value){
		if ($variable_name == "op") $this->op = rtl::correct($value, "string", "op_call", "");
		else if ($variable_name == "value") $this->value = rtl::correct($value, "BayrellLang.OpCodes.BaseOpCode", null, "");
		else if ($variable_name == "args") $this->args = rtl::correct($value, "Runtime.Vector", null, "BayrellLang.OpCodes.BaseOpCode");
		else if ($variable_name == "is_await") $this->is_await = rtl::correct($value, "bool", false, "");
		else parent::assignValue($variable_name, $value);
	}
	public function takeValue($variable_name, $default_value = null){
		if ($variable_name == "op") return $this->op;
		else if ($variable_name == "value") return $this->value;
		else if ($variable_name == "args") return $this->args;
		else if ($variable_name == "is_await") return $this->is_await;
		return parent::takeValue($variable_name, $default_value);
	}
	public static function getFieldsList($names){
		$names->push("op");
		$names->push("value");
		$names->push("args");
		$names->push("is_await");
	}
	public static function getFieldInfoByName($field_name){
		return null;
	}
}