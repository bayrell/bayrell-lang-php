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
class OpFlags extends BaseOpCode{
	public $op;
	public $p_async;
	public $p_export;
	public $p_static;
	public $p_const;
	public $p_public;
	public $p_private;
	public $p_protected;
	public $p_declare;
	public $p_serializable;
	public $p_cloneable;
	public $p_assignable;
	public $p_memorize;
	public $p_lambda;
	public $p_pure;
	/**
	 * Read is Flag
	 */
	function isFlag($name){
		if (!OpFlags::hasFlag($name)){
			return false;
		}
		return $this->takeValue($name);
	}
	/**
	 * Returns name of variables to serialization
	 * @return Vector<string>
	 */
	function getVariablesNames($names){
		parent::getVariablesNames($names);
		$names->push("async");
		$names->push("export");
		$names->push("static");
		$names->push("const");
		$names->push("public");
		$names->push("private");
		$names->push("declare");
		$names->push("protected");
		$names->push("serializable");
		$names->push("cloneable");
		$names->push("assignable");
		$names->push("memorize");
		$names->push("lambda");
		$names->push("pure");
	}
	/**
	 * Returns instance of the value by variable name
	 * @param string variable_name
	 * @return var
	 */
	function takeValue($variable_name, $default_value = null){
		if ($variable_name == "async"){
			return $this->p_async;
		}
		else if ($variable_name == "export"){
			return $this->p_export;
		}
		else if ($variable_name == "static"){
			return $this->p_static;
		}
		else if ($variable_name == "const"){
			return $this->p_const;
		}
		else if ($variable_name == "public"){
			return $this->p_public;
		}
		else if ($variable_name == "private"){
			return $this->p_private;
		}
		else if ($variable_name == "declare"){
			return $this->p_declare;
		}
		else if ($variable_name == "protected"){
			return $this->p_protected;
		}
		else if ($variable_name == "serializable"){
			return $this->p_serializable;
		}
		else if ($variable_name == "cloneable"){
			return $this->p_cloneable;
		}
		else if ($variable_name == "assignable"){
			return $this->p_assignable;
		}
		else if ($variable_name == "memorize"){
			return $this->p_memorize;
		}
		else if ($variable_name == "lambda"){
			return $this->p_lambda;
		}
		else if ($variable_name == "pure"){
			return $this->p_pure;
		}
		return parent::takeValue($variable_name, $default_value);
	}
	/**
	 * Set new value instance by variable name
	 * @param string variable_name
	 * @param var value
	 */
	function assignValue($variable_name, $value){
		if ($variable_name == "async"){
			$this->p_async = $value;
		}
		else if ($variable_name == "export"){
			$this->p_export = $value;
		}
		else if ($variable_name == "static"){
			$this->p_static = $value;
		}
		else if ($variable_name == "const"){
			$this->p_const = $value;
		}
		else if ($variable_name == "public"){
			$this->p_public = $value;
		}
		else if ($variable_name == "private"){
			$this->p_private = $value;
		}
		else if ($variable_name == "declare"){
			$this->p_declare = $value;
		}
		else if ($variable_name == "protected"){
			$this->p_protected = $value;
		}
		else if ($variable_name == "serializable"){
			$this->p_serializable = $value;
		}
		else if ($variable_name == "cloneable"){
			$this->p_cloneable = $value;
		}
		else if ($variable_name == "assignable"){
			$this->p_assignable = $value;
		}
		else if ($variable_name == "memorize"){
			$this->p_memorize = $value;
		}
		else if ($variable_name == "lambda"){
			$this->p_lambda = $value;
		}
		else if ($variable_name == "pure"){
			$this->p_pure = $value;
		}
		else {
			parent::assignValue($variable_name, $value);
		}
	}
	/**
	 * Assign flag
	 */
	function assignFlag($flag_name){
		if ((new \Runtime\Callback(self::class, "hasFlag"))($flag_name)){
			$this->assignValue($flag_name, true);
			return true;
		}
		return false;
	}
	/**
	 * Get flags
	 */
	static function getFlags(){
		return (new Vector())->push("async")->push("export")->push("static")->push("const")->push("public")->push("private")->push("declare")->push("protected")->push("serializable")->push("cloneable")->push("assignable")->push("memorize")->push("lambda")->push("pure");
	}
	/**
	 * Get flags
	 */
	static function hasFlag($flag_name){
		if ($flag_name == "async" || $flag_name == "export" || $flag_name == "static" || $flag_name == "const" || $flag_name == "public" || $flag_name == "private" || $flag_name == "declare" || $flag_name == "protected" || $flag_name == "serializable" || $flag_name == "cloneable" || $flag_name == "assignable" || $flag_name == "memorize" || $flag_name == "lambda" || $flag_name == "pure"){
			return true;
		}
		return false;
	}
	/* ======================= Class Init Functions ======================= */
	public function getClassName(){return "BayrellLang.OpCodes.OpFlags";}
	public static function getCurrentClassName(){return "BayrellLang.OpCodes.OpFlags";}
	public static function getParentClassName(){return "BayrellLang.OpCodes.BaseOpCode";}
	protected function _init(){
		parent::_init();
	}
	public static function getFieldsList($names, $flag=0){
	}
	public static function getFieldInfoByName($field_name){
		return null;
	}
	public static function getMethodsList($names){
	}
	public static function getMethodInfoByName($method_name){
		return null;
	}
}