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
use BayrellLang\OpCodes\OpClassDeclare;
class OpInterfaceDeclare extends OpClassDeclare{
	public $op;
	/* ======================= Class Init Functions ======================= */
	public function getClassName(){return "BayrellLang.OpCodes.OpInterfaceDeclare";}
	public static function getParentClassName(){return "BayrellLang.OpCodes.OpClassDeclare";}
	protected function _init(){
		parent::_init();
		$this->op = "op_interace";
	}
	public function assignObject($obj){
		if ($obj instanceof OpInterfaceDeclare){
			$this->op = rtl::_clone($obj->op);
		}
		parent::assignObject($obj);
	}
	public function assignValue($variable_name, $value){
		if ($variable_name == "op") $this->op = rtl::correct($value, "string", "op_interace", "");
		else parent::assignValue($variable_name, $value);
	}
	public function takeValue($variable_name, $default_value = null){
		if ($variable_name == "op") return $this->op;
		return parent::takeValue($variable_name, $default_value);
	}
	public static function getFieldsList($names){
		$names->push("op");
	}
	public static function getFieldInfoByName($field_name){
		return null;
	}
}