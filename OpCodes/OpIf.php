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
use BayrellLang\OpCodes\OpIfElse;
class OpIf extends BaseOpCode{
	public $op;
	public $condition;
	public $if_true;
	public $if_false;
	public $if_else;
	public function getClassName(){return "BayrellLang.OpCodes.OpIf";}
	public static function getParentClassName(){return "BayrellLang.OpCodes.BaseOpCode";}
	protected function _init(){
		parent::_init();
		$this->op = "op_if";
		$this->condition = null;
		$this->if_true = null;
		$this->if_false = null;
		$this->if_else = null;
	}
	public function assignValue($variable_name, $value){
		if ($variable_name == "op") $this->op = rtl::correct($value, "string", "op_if", "");
		else if ($variable_name == "condition") $this->condition = rtl::correct($value, "BayrellLang.OpCodes.BaseOpCode", null, "");
		else if ($variable_name == "if_true") $this->if_true = rtl::correct($value, "Runtime.Vector", null, "BayrellLang.OpCodes.BaseOpCode");
		else if ($variable_name == "if_false") $this->if_false = rtl::correct($value, "Runtime.Vector", null, "BayrellLang.OpCodes.BaseOpCode");
		else if ($variable_name == "if_else") $this->if_else = rtl::correct($value, "Runtime.Vector", null, "BayrellLang.OpCodes.OpIfElse");
		else parent::assignValue($variable_name, $value);
	}
	public function takeValue($variable_name, $default_value = null){
		if ($variable_name == "op") return $this->op;
		else if ($variable_name == "condition") return $this->condition;
		else if ($variable_name == "if_true") return $this->if_true;
		else if ($variable_name == "if_false") return $this->if_false;
		else if ($variable_name == "if_else") return $this->if_else;
		return parent::takeValue($variable_name, $default_value);
	}
	public function getVariablesNames($names){
		parent::getVariablesNames($names);
		$names->push("op");
		$names->push("condition");
		$names->push("if_true");
		$names->push("if_false");
		$names->push("if_else");
	}
	/**
	 * Returns classname of the object
	 * @return string
	 */
	function getClassName(){
		return "BayrellLang.OpCodes.OpIf";
	}
	/**
	 * Constructor
	 */
	function __construct($condition = null, $if_true = null, $if_false = null, $if_else){
		parent::__construct();
		$this->condition = $condition;
		$this->if_true = $if_true;
		$this->if_false = $if_false;
		$this->if_else = $if_else;
	}
	/**
	 * Destructor
	 */
	function __destruct(){
		parent::__destruct();
	}
}