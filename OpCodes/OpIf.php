<?php
/*!
 *  Bayrell Language
 *
 *  (c) Copyright 2016-2019 "Ildar Bikmamatov" <support@bayrell.org>
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
class OpIf extends \Bayrell\Lang\OpCodes\BaseOpCode
{
	public $__op;
	public $__condition;
	public $__if_true;
	public $__if_false;
	public $__if_else;
	/* ======================= Class Init Functions ======================= */
	function _init($__ctx)
	{
		parent::_init($__ctx);
		$this->__op = "op_if";
		$this->__condition = null;
		$this->__if_true = null;
		$this->__if_false = null;
		$this->__if_else = null;
	}
	function assignObject($__ctx,$o)
	{
		if ($o instanceof \Bayrell\Lang\OpCodes\OpIf)
		{
			$this->__op = $o->__op;
			$this->__condition = $o->__condition;
			$this->__if_true = $o->__if_true;
			$this->__if_false = $o->__if_false;
			$this->__if_else = $o->__if_else;
		}
		parent::assignObject($__ctx,$o);
	}
	function assignValue($__ctx,$k,$v)
	{
		if ($k == "op")$this->__op = $v;
		else if ($k == "condition")$this->__condition = $v;
		else if ($k == "if_true")$this->__if_true = $v;
		else if ($k == "if_false")$this->__if_false = $v;
		else if ($k == "if_else")$this->__if_else = $v;
		else parent::assignValue($__ctx,$k,$v);
	}
	function takeValue($__ctx,$k,$d=null)
	{
		if ($k == "op")return $this->__op;
		else if ($k == "condition")return $this->__condition;
		else if ($k == "if_true")return $this->__if_true;
		else if ($k == "if_false")return $this->__if_false;
		else if ($k == "if_else")return $this->__if_else;
		return parent::takeValue($__ctx,$k,$d);
	}
	function getClassName()
	{
		return "Bayrell.Lang.OpCodes.OpIf";
	}
	static function getCurrentNamespace()
	{
		return "Bayrell.Lang.OpCodes";
	}
	static function getCurrentClassName()
	{
		return "Bayrell.Lang.OpCodes.OpIf";
	}
	static function getParentClassName()
	{
		return "Bayrell.Lang.OpCodes.BaseOpCode";
	}
	static function getClassInfo($__ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($__ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.OpCodes.OpIf",
			"name"=>"Bayrell.Lang.OpCodes.OpIf",
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
	}
	static function getFieldsList($__ctx,$f)
	{
		$a = [];
		if (($f|3)==3)
		{
			$a[] = "op";
			$a[] = "condition";
			$a[] = "if_true";
			$a[] = "if_false";
			$a[] = "if_else";
		}
		return \Runtime\Collection::from($a);
	}
	static function getFieldInfoByName($__ctx,$field_name)
	{
		return null;
	}
	static function getMethodsList($__ctx)
	{
		$a = [
		];
		return \Runtime\Collection::from($a);
	}
	static function getMethodInfoByName($__ctx,$field_name)
	{
		return null;
	}
}