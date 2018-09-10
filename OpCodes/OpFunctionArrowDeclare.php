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
use BayrellLang\OpCodes\OpFunctionDeclare;
use BayrellLang\OpCodes\OpFlags;
class OpFunctionArrowDeclare extends OpFunctionDeclare{
	public $op;
	public $return_function;
	public function getClassName(){return "BayrellLang.OpCodes.OpFunctionArrowDeclare";}
	public static function getParentClassName(){return "BayrellLang.OpCodes.OpFunctionDeclare";}
	protected function _init(){
		parent::_init();
		$this->op = "op_arrow_function";
		$this->return_function = null;
	}
	public function assignValue($variable_name, $value){
		if ($variable_name == "op") $this->op = rtl::correct($value, "string", "op_arrow_function", "");
		else if ($variable_name == "return_function") $this->return_function = rtl::correct($value, "BayrellLang.OpCodes.OpFunctionDeclare", null, "");
		else parent::assignValue($variable_name, $value);
	}
	public function takeValue($variable_name, $default_value = null){
		if ($variable_name == "op") return $this->op;
		else if ($variable_name == "return_function") return $this->return_function;
		return parent::takeValue($variable_name, $default_value);
	}
	public function getVariablesNames($names){
		parent::getVariablesNames($names);
		$names->push("op");
		$names->push("return_function");
	}
	/**
	 * Returns classname of the object
	 * @return string
	 */
	function getClassName(){
		return "BayrellLang.OpCodes.OpFunctionArrowDeclare";
	}
}