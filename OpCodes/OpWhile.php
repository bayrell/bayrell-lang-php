<?php
/*!
 *  Bayrell Language
 *
 *  (c) Copyright 2016-2020 "Ildar Bikmamatov" <support@bayrell.org>
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
class OpWhile extends \Bayrell\Lang\OpCodes\BaseOpCode
{
	public $__op;
	public $__condition;
	public $__value;
	/* ======================= Class Init Functions ======================= */
	function _init($ctx)
	{
		parent::_init($ctx);
		$this->__op = "op_while";
		$this->__condition = null;
		$this->__value = null;
	}
	function assignObject($ctx,$o)
	{
		if ($o instanceof \Bayrell\Lang\OpCodes\OpWhile)
		{
			$this->__op = $o->__op;
			$this->__condition = $o->__condition;
			$this->__value = $o->__value;
		}
		parent::assignObject($ctx,$o);
	}
	function assignValue($ctx,$k,$v)
	{
		if ($k == "op")$this->__op = $v;
		else if ($k == "condition")$this->__condition = $v;
		else if ($k == "value")$this->__value = $v;
		else parent::assignValue($ctx,$k,$v);
	}
	function takeValue($ctx,$k,$d=null)
	{
		if ($k == "op")return $this->__op;
		else if ($k == "condition")return $this->__condition;
		else if ($k == "value")return $this->__value;
		return parent::takeValue($ctx,$k,$d);
	}
	function getClassName()
	{
		return "Bayrell.Lang.OpCodes.OpWhile";
	}
	static function getCurrentNamespace()
	{
		return "Bayrell.Lang.OpCodes";
	}
	static function getCurrentClassName()
	{
		return "Bayrell.Lang.OpCodes.OpWhile";
	}
	static function getParentClassName()
	{
		return "Bayrell.Lang.OpCodes.BaseOpCode";
	}
	static function getClassInfo($ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.OpCodes.OpWhile",
			"name"=>"Bayrell.Lang.OpCodes.OpWhile",
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
			$a[] = "condition";
			$a[] = "value";
		}
		return \Runtime\Collection::from($a);
	}
	static function getFieldInfoByName($ctx,$field_name)
	{
		if ($field_name == "op") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpWhile",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "condition") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpWhile",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "value") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpWhile",
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