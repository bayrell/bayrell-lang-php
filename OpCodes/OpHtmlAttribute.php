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
use Runtime\Vector;
use BayrellLang\OpCodes\BaseOpCode;
class OpHtmlAttribute extends BaseOpCode{
	public $op;
	public $key;
	public $value;
	/**
	 * Constructor
	 */
	function __construct(){
		parent::__construct();
	}
	/**
	 * Destructor
	 */
	function __destruct(){
		parent::__destruct();
	}
	/* ======================= Class Init Functions ======================= */
	public function getClassName(){return "BayrellLang.OpCodes.OpHtmlAttribute";}
	public static function getParentClassName(){return "BayrellLang.OpCodes.BaseOpCode";}
	protected function _init(){
		parent::_init();
	}
	public function assignObject($obj){
		if ($obj instanceof OpHtmlAttribute){
			$this->op = rtl::_clone($obj->op);
			$this->key = rtl::_clone($obj->key);
			$this->value = rtl::_clone($obj->value);
		}
		parent::assignObject($obj);
	}
	public function assignValue($variable_name, $value, $sender = null){
		if ($variable_name == "op")$this->op = rtl::correct($value,"string","op_html_attribute","");
		else if ($variable_name == "key")$this->key = rtl::correct($value,"string","","");
		else if ($variable_name == "value")$this->value = rtl::correct($value,"BayrellLang.OpCodes.BaseOpCode",null,"");
		else parent::assignValue($variable_name, $value, $sender);
	}
	public function takeValue($variable_name, $default_value = null){
		if ($variable_name == "op") return $this->op;
		else if ($variable_name == "key") return $this->key;
		else if ($variable_name == "value") return $this->value;
		return parent::takeValue($variable_name, $default_value);
	}
	public static function getFieldsList($names, $flag=0){
		if (($flag | 3)==3){
			$names->push("op");
			$names->push("key");
			$names->push("value");
		}
	}
	public static function getFieldInfoByName($field_name){
		return null;
	}
}