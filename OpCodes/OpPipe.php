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
class OpPipe extends BaseOpCode{
	protected $__op;
	protected $__value;
	protected $__items;
	protected $__is_return_value;
	/* ======================= Class Init Functions ======================= */
	public function getClassName(){return "BayrellLang.OpCodes.OpPipe";}
	public static function getCurrentNamespace(){return "BayrellLang.OpCodes";}
	public static function getCurrentClassName(){return "BayrellLang.OpCodes.OpPipe";}
	public static function getParentClassName(){return "BayrellLang.OpCodes.BaseOpCode";}
	protected function _init(){
		parent::_init();
		$this->__op = "op_pipe";
		$this->__value = null;
		$this->__items = null;
		$this->__is_return_value = false;
	}
	public function assignObject($obj){
		if ($obj instanceof OpPipe){
			$this->__op = $obj->__op;
			$this->__value = $obj->__value;
			$this->__items = $obj->__items;
			$this->__is_return_value = $obj->__is_return_value;
		}
		parent::assignObject($obj);
	}
	public function assignValue($variable_name, $value, $sender = null){
		if ($variable_name == "op")$this->__op = rtl::convert($value,"string","op_pipe","");
		else if ($variable_name == "value")$this->__value = rtl::convert($value,"BayrellLang.OpCodes.BaseOpCode",null,"");
		else if ($variable_name == "items")$this->__items = rtl::convert($value,"Runtime.Vector",null,"BayrellLang.OpCodes.BaseOpCode");
		else if ($variable_name == "is_return_value")$this->__is_return_value = rtl::convert($value,"bool",false,"");
		else parent::assignValue($variable_name, $value, $sender);
	}
	public function takeValue($variable_name, $default_value = null){
		if ($variable_name == "op") return $this->__op;
		else if ($variable_name == "value") return $this->__value;
		else if ($variable_name == "items") return $this->__items;
		else if ($variable_name == "is_return_value") return $this->__is_return_value;
		return parent::takeValue($variable_name, $default_value);
	}
	public static function getFieldsList($names, $flag=0){
		if (($flag | 3)==3){
			$names->push("op");
			$names->push("value");
			$names->push("items");
			$names->push("is_return_value");
		}
	}
	public static function getFieldInfoByName($field_name){
		return null;
	}
	public static function getMethodsList($names){
	}
	public static function getMethodInfoByName($method_name){
		return null;
	}
	public function __get($key){ return $this->takeValue($key); }
	public function __set($key, $value){throw new \Runtime\Exceptions\AssignStructValueError($key);}
}