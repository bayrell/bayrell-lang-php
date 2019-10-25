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
class OpMath extends \Bayrell\Lang\OpCodes\BaseOpCode
{
	public $__op;
	public $__value1;
	public $__value2;
	public $__math;
	/* ======================= Class Init Functions ======================= */
	function _init($__ctx)
	{
		parent::_init($__ctx);
		$this->__op = "op_math";
		$this->__value1 = null;
		$this->__value2 = null;
		$this->__math = "";
	}
	function assignObject($__ctx,$o)
	{
		if ($o instanceof \Bayrell\Lang\OpCodes\OpMath)
		{
			$this->__op = $o->__op;
			$this->__value1 = $o->__value1;
			$this->__value2 = $o->__value2;
			$this->__math = $o->__math;
		}
		parent::assignObject($__ctx,$o);
	}
	function assignValue($__ctx,$k,$v)
	{
		if ($k == "op")$this->__op = $v;
		else if ($k == "value1")$this->__value1 = $v;
		else if ($k == "value2")$this->__value2 = $v;
		else if ($k == "math")$this->__math = $v;
		else parent::assignValue($__ctx,$k,$v);
	}
	function takeValue($__ctx,$k,$d=null)
	{
		if ($k == "op")return $this->__op;
		else if ($k == "value1")return $this->__value1;
		else if ($k == "value2")return $this->__value2;
		else if ($k == "math")return $this->__math;
		return parent::takeValue($__ctx,$k,$d);
	}
	function getClassName()
	{
		return "Bayrell.Lang.OpCodes.OpMath";
	}
	static function getCurrentNamespace()
	{
		return "Bayrell.Lang.OpCodes";
	}
	static function getCurrentClassName()
	{
		return "Bayrell.Lang.OpCodes.OpMath";
	}
	static function getParentClassName()
	{
		return "Bayrell.Lang.OpCodes.BaseOpCode";
	}
	static function getClassInfo($__ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($__ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.OpCodes.OpMath",
			"name"=>"Bayrell.Lang.OpCodes.OpMath",
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
			$a[] = "value1";
			$a[] = "value2";
			$a[] = "math";
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