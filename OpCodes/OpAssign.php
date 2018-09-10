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
class OpAssign extends BaseOpCode{
	public $op;
	public $ident;
	public $value;
	public $op_name;
	public function getClassName(){return "BayrellLang.OpCodes.OpAssign";}
	public static function getParentClassName(){return "BayrellLang.OpCodes.BaseOpCode";}
	protected function _init(){
		parent::_init();
		$this->op = "op_assign";
		$this->ident = null;
		$this->value = null;
		$this->op_name = "";
	}
	public function assignValue($variable_name, $value){
		if ($variable_name == "op") $this->op = rtl::correct($value, "string", "op_assign", "");
		else if ($variable_name == "ident") $this->ident = rtl::correct($value, "BayrellLang.OpCodes.BaseOpCode", null, "");
		else if ($variable_name == "value") $this->value = rtl::correct($value, "BayrellLang.OpCodes.BaseOpCode", null, "");
		else if ($variable_name == "op_name") $this->op_name = rtl::correct($value, "string", "", "");
		else parent::assignValue($variable_name, $value);
	}
	public function takeValue($variable_name, $default_value = null){
		if ($variable_name == "op") return $this->op;
		else if ($variable_name == "ident") return $this->ident;
		else if ($variable_name == "value") return $this->value;
		else if ($variable_name == "op_name") return $this->op_name;
		return parent::takeValue($variable_name, $default_value);
	}
	public function getVariablesNames($names){
		parent::getVariablesNames($names);
		$names->push("op");
		$names->push("ident");
		$names->push("value");
		$names->push("op_name");
	}
	/**
	 * Returns classname of the object
	 * @return string
	 */
	function getClassName(){
		return "BayrellLang.OpCodes.OpAssign";
	}
	/**
	 * Constructor
	 */
	function __construct($ident = null, $value = null, $op_name = ""){
		parent::__construct();
		$this->ident = $ident;
		$this->value = $value;
		$this->op_name = $op_name;
	}
	/**
	 * Destructor
	 */
	function __destruct(){
		parent::__destruct();
	}
}