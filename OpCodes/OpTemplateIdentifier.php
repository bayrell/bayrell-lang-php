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
class OpTemplateIdentifier extends BaseOpCode{
	public $op;
	public $t;
	public $childs;
	public function getClassName(){return "BayrellLang.OpCodes.OpTemplateIdentifier";}
	public static function getParentClassName(){return "BayrellLang.OpCodes.BaseOpCode";}
	protected function _init(){
		parent::_init();
		$this->op = "op_template_identifier";
		$this->t = null;
		$this->childs = null;
	}
	/**
	 * Returns classname of the object
	 * @return string
	 */
	function getClassName(){
		return "BayrellLang.OpCodes.OpTemplateIdentifier";
	}
	/**
	 * Constructor
	 */
	function __construct($t = null, $childs = null){
		parent::__construct();
		$this->t = $t;
		$this->childs = $childs;
	}
	/**
	 * Destructor
	 */
	function __destruct(){
		parent::__destruct();
	}
	/**
	 * Returns name of variables to serialization
	 * @return Vector<string>
	 */
	function getVariablesNames($names){
		parent::getVariablesNames($names);
		$names->push("type");
		$names->push("childs");
	}
	/**
	 * Returns instance of the value by variable name
	 * @param string variable_name
	 * @return var
	 */
	function takeValue($variable_name, $default_value = null){
		if ($variable_name == "type"){
			return $this->t;
		}
		else if ($variable_name == "childs"){
			return $this->childs;
		}
		return parent::takeValue($variable_name, $default_value);
	}
	/**
	 * Set new value instance by variable name
	 * @param string variable_name
	 * @param var value
	 */
	function assignValue($variable_name, $value){
		if ($variable_name == "type"){
			$this->t = $value;
		}
		else if ($variable_name == "childs"){
			$this->childs = $value;
		}
		else {
			parent::assignValue($variable_name, $value);
		}
	}
}