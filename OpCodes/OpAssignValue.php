<?php
/*!
 *  Bayrell Language
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
namespace Bayrell\Lang\OpCodes;
class OpAssignValue extends \Bayrell\Lang\OpCodes\BaseOpCode
{
	public $__op;
	public $__var_name;
	public $__op_code;
	public $__expression;
	/* ======================= Class Init Functions ======================= */
	function _init($ctx)
	{
		parent::_init($ctx);
		$this->__op = "";
		$this->__var_name = "";
		$this->__op_code = null;
		$this->__expression = null;
	}
	function assignObject($ctx,$o)
	{
		if ($o instanceof \Bayrell\Lang\OpCodes\OpAssignValue)
		{
			$this->__op = $o->__op;
			$this->__var_name = $o->__var_name;
			$this->__op_code = $o->__op_code;
			$this->__expression = $o->__expression;
		}
		parent::assignObject($ctx,$o);
	}
	function assignValue($ctx,$k,$v)
	{
		if ($k == "op")$this->__op = $v;
		else if ($k == "var_name")$this->__var_name = $v;
		else if ($k == "op_code")$this->__op_code = $v;
		else if ($k == "expression")$this->__expression = $v;
		else parent::assignValue($ctx,$k,$v);
	}
	function takeValue($ctx,$k,$d=null)
	{
		if ($k == "op")return $this->__op;
		else if ($k == "var_name")return $this->__var_name;
		else if ($k == "op_code")return $this->__op_code;
		else if ($k == "expression")return $this->__expression;
		return parent::takeValue($ctx,$k,$d);
	}
	function getClassName()
	{
		return "Bayrell.Lang.OpCodes.OpAssignValue";
	}
	static function getCurrentNamespace()
	{
		return "Bayrell.Lang.OpCodes";
	}
	static function getCurrentClassName()
	{
		return "Bayrell.Lang.OpCodes.OpAssignValue";
	}
	static function getParentClassName()
	{
		return "Bayrell.Lang.OpCodes.BaseOpCode";
	}
	static function getClassInfo($ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.OpCodes.OpAssignValue",
			"name"=>"Bayrell.Lang.OpCodes.OpAssignValue",
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
	}
	static function getFieldsList($ctx,$f)
	{
		$a = [];
		if (($f|3)==3)
		{
			$a[] = "op";
			$a[] = "var_name";
			$a[] = "op_code";
			$a[] = "expression";
		}
		return \Runtime\Collection::from($a);
	}
	static function getFieldInfoByName($ctx,$field_name)
	{
		if ($field_name == "op") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpAssignValue",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "var_name") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpAssignValue",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "op_code") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpAssignValue",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "expression") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpAssignValue",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		return null;
	}
	static function getMethodsList($ctx)
	{
		$a = [
		];
		return \Runtime\Collection::from($a);
	}
	static function getMethodInfoByName($ctx,$field_name)
	{
		return null;
	}
}