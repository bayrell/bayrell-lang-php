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
class OpFor extends BaseOpCode{
	public $op;
	public $loop_condition;
	public $loop_init;
	public $loop_inc;
	public $childs;
	public function getClassName(){return "BayrellLang.OpCodes.OpFor";}
	public static function getParentClassName(){return "BayrellLang.OpCodes.BaseOpCode";}
	protected function _init(){
		parent::_init();
		$this->op = "op_for";
		$this->loop_condition = null;
		$this->loop_init = null;
		$this->loop_inc = null;
		$this->childs = null;
	}
	public function assignValue($variable_name, $value){
		if ($variable_name == "op") $this->op = rtl::correct($value, "string", "op_for", "");
		else if ($variable_name == "loop_condition") $this->loop_condition = rtl::correct($value, "BayrellLang.OpCodes.BaseOpCode", null, "");
		else if ($variable_name == "loop_init") $this->loop_init = rtl::correct($value, "BayrellLang.OpCodes.BaseOpCode", null, "");
		else if ($variable_name == "loop_inc") $this->loop_inc = rtl::correct($value, "BayrellLang.OpCodes.BaseOpCode", null, "");
		else if ($variable_name == "childs") $this->childs = rtl::correct($value, "Runtime.Vector", null, "BayrellLang.OpCodes.BaseOpCode");
		else parent::assignValue($variable_name, $value);
	}
	public function takeValue($variable_name, $default_value = null){
		if ($variable_name == "op") return $this->op;
		else if ($variable_name == "loop_condition") return $this->loop_condition;
		else if ($variable_name == "loop_init") return $this->loop_init;
		else if ($variable_name == "loop_inc") return $this->loop_inc;
		else if ($variable_name == "childs") return $this->childs;
		return parent::takeValue($variable_name, $default_value);
	}
	public function getVariablesNames($names){
		parent::getVariablesNames($names);
		$names->push("op");
		$names->push("loop_condition");
		$names->push("loop_init");
		$names->push("loop_inc");
		$names->push("childs");
	}
	/**
	 * Returns classname of the object
	 * @return string
	 */
	function getClassName(){
		return "BayrellLang.OpCodes.OpFor";
	}
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
}