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
use BayrellLang\OpCodes\OpHtmlAttribute;
class OpHtmlTag extends BaseOpCode{
	public $op;
	public $tag_name;
	public $attributes;
	public $spreads;
	public $childs;
	public $is_plain;
	/**
	 * Find attribute by attr_name
	 * @param string attr_name
	 * @return OpHtmlAttribute
	 */
	function findAttribute($attr_name){
		if ($this->attributes == null){
			return null;
		}
		for ($i = 0; $i < $this->attributes->count(); $i++){
			$item = $this->attributes->item($i);
			if ($item->key == $attr_name){
				return $item;
			}
		}
		return null;
	}
	/**
	 * Remove attribute by attr_name
	 * @param string attr_name
	 */
	function removeAttribute($attr_name){
		$this->attributes = $this->attributes->filter(function ($item) use (&$attr_name){
			return $item->key != $attr_name;
		});
	}
	/**
	 * Set attribute by attr_name
	 * @param string attr_name
	 * @param mixed value
	 */
	function setAttribute($attr_name, $value){
		if ($this->attributes == null){
			return ;
		}
		for ($i = 0; $i < $this->attributes->count(); $i++){
			$item = $this->attributes->item($i);
			if ($item->key == $attr_name){
				$item->value = $value;
				return ;
			}
		}
		$this->attributes->push(new OpHtmlAttribute((new Map())->set("key", $attr_name)->set("value", $value)));
	}
	/* ======================= Class Init Functions ======================= */
	public function getClassName(){return "BayrellLang.OpCodes.OpHtmlTag";}
	public static function getCurrentNamespace(){return "BayrellLang.OpCodes";}
	public static function getCurrentClassName(){return "BayrellLang.OpCodes.OpHtmlTag";}
	public static function getParentClassName(){return "BayrellLang.OpCodes.BaseOpCode";}
	protected function _init(){
		parent::_init();
	}
	public function assignObject($obj){
		if ($obj instanceof OpHtmlTag){
			$this->op = rtl::_clone($obj->op);
			$this->tag_name = rtl::_clone($obj->tag_name);
			$this->attributes = rtl::_clone($obj->attributes);
			$this->spreads = rtl::_clone($obj->spreads);
			$this->childs = rtl::_clone($obj->childs);
			$this->is_plain = rtl::_clone($obj->is_plain);
		}
		parent::assignObject($obj);
	}
	public function assignValue($variable_name, $value, $sender = null){
		if ($variable_name == "op")$this->op = rtl::convert($value,"string","op_html_tag","");
		else if ($variable_name == "tag_name")$this->tag_name = rtl::convert($value,"string","","");
		else if ($variable_name == "attributes")$this->attributes = rtl::convert($value,"Runtime.Vector",null,"BayrellLang.OpCodes.OpHtmlAttribute");
		else if ($variable_name == "spreads")$this->spreads = rtl::convert($value,"Runtime.Vector",null,"mixed");
		else if ($variable_name == "childs")$this->childs = rtl::convert($value,"Runtime.Vector",null,"BayrellLang.OpCodes.BaseOpCode");
		else if ($variable_name == "is_plain")$this->is_plain = rtl::convert($value,"bool",false,"");
		else parent::assignValue($variable_name, $value, $sender);
	}
	public function takeValue($variable_name, $default_value = null){
		if ($variable_name == "op") return $this->op;
		else if ($variable_name == "tag_name") return $this->tag_name;
		else if ($variable_name == "attributes") return $this->attributes;
		else if ($variable_name == "spreads") return $this->spreads;
		else if ($variable_name == "childs") return $this->childs;
		else if ($variable_name == "is_plain") return $this->is_plain;
		return parent::takeValue($variable_name, $default_value);
	}
	public static function getFieldsList($names, $flag=0){
		if (($flag | 3)==3){
			$names->push("op");
			$names->push("tag_name");
			$names->push("attributes");
			$names->push("spreads");
			$names->push("childs");
			$names->push("is_plain");
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
}