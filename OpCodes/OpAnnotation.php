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
use BayrellLang\OpCodes\BaseOpCode;
use BayrellLang\OpCodes\OpMap;
class OpAnnotation extends BaseOpCode{
	public $op;
	public $kind;
	public $options;
	/* ======================= Class Init Functions ======================= */
	public function getClassName(){return "BayrellLang.OpCodes.OpAnnotation";}
	public static function getParentClassName(){return "BayrellLang.OpCodes.BaseOpCode";}
	protected function _init(){
		parent::_init();
	}
	public function assignObject($obj){
		if ($obj instanceof OpAnnotation){
			$this->op = rtl::_clone($obj->op);
			$this->kind = rtl::_clone($obj->kind);
			$this->options = rtl::_clone($obj->options);
		}
		parent::assignObject($obj);
	}
	public function assignValue($variable_name, $value, $sender = null){
		if ($variable_name == "op")$this->op = rtl::correct($value,"string","op_annotation","");
		else if ($variable_name == "kind")$this->kind = rtl::correct($value,"BayrellLang.OpCodes.BaseOpCode",null,"");
		else if ($variable_name == "options")$this->options = rtl::correct($value,"BayrellLang.OpCodes.OpMap",null,"");
		else parent::assignValue($variable_name, $value, $sender);
	}
	public function takeValue($variable_name, $default_value = null){
		if ($variable_name == "op") return $this->op;
		else if ($variable_name == "kind") return $this->kind;
		else if ($variable_name == "options") return $this->options;
		return parent::takeValue($variable_name, $default_value);
	}
	public static function getFieldsList($names, $flag=0){
		if (($flag | 3)==3){
			$names->push("op");
			$names->push("kind");
			$names->push("options");
		}
	}
	public static function getFieldInfoByName($field_name){
		return null;
	}
}