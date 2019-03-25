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
class OpFor extends BaseOpCode{
	public $op;
	public $loop_condition;
	public $loop_init;
	public $loop_inc;
	public $childs;
	/**
	 * Constructor
	 */
	function __construct($loop_condition = null, $loop_init = null, $loop_inc = null, $childs = null){
		parent::__construct();
		$this->loop_condition = $loop_condition;
		$this->loop_init = $loop_init;
		$this->loop_inc = $loop_inc;
		$this->childs = $childs;
	}
	/**
	 * Destructor
	 */
	function __destruct(){
		parent::__destruct();
	}
	/* ======================= Class Init Functions ======================= */
	public function getClassName(){return "BayrellLang.OpCodes.OpFor";}
	public static function getCurrentClassName(){return "BayrellLang.OpCodes.OpFor";}
	public static function getParentClassName(){return "BayrellLang.OpCodes.BaseOpCode";}
	protected function _init(){
		parent::_init();
	}
	public function assignObject($obj){
		if ($obj instanceof OpFor){
			$this->op = rtl::_clone($obj->op);
			$this->loop_condition = rtl::_clone($obj->loop_condition);
			$this->loop_init = rtl::_clone($obj->loop_init);
			$this->loop_inc = rtl::_clone($obj->loop_inc);
			$this->childs = rtl::_clone($obj->childs);
		}
		parent::assignObject($obj);
	}
	public function assignValue($variable_name, $value, $sender = null){
		if ($variable_name == "op")$this->op = rtl::convert($value,"string","op_for","");
		else if ($variable_name == "loop_condition")$this->loop_condition = rtl::convert($value,"BayrellLang.OpCodes.BaseOpCode",null,"");
		else if ($variable_name == "loop_init")$this->loop_init = rtl::convert($value,"BayrellLang.OpCodes.BaseOpCode",null,"");
		else if ($variable_name == "loop_inc")$this->loop_inc = rtl::convert($value,"BayrellLang.OpCodes.BaseOpCode",null,"");
		else if ($variable_name == "childs")$this->childs = rtl::convert($value,"Runtime.Vector",null,"BayrellLang.OpCodes.BaseOpCode");
		else parent::assignValue($variable_name, $value, $sender);
	}
	public function takeValue($variable_name, $default_value = null){
		if ($variable_name == "op") return $this->op;
		else if ($variable_name == "loop_condition") return $this->loop_condition;
		else if ($variable_name == "loop_init") return $this->loop_init;
		else if ($variable_name == "loop_inc") return $this->loop_inc;
		else if ($variable_name == "childs") return $this->childs;
		return parent::takeValue($variable_name, $default_value);
	}
	public static function getFieldsList($names, $flag=0){
		if (($flag | 3)==3){
			$names->push("op");
			$names->push("loop_condition");
			$names->push("loop_init");
			$names->push("loop_inc");
			$names->push("childs");
		}
	}
	public static function getFieldInfoByName($field_name){
		return null;
	}
}