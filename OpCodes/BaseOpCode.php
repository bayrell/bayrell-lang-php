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
class BaseOpCode extends \Runtime\CoreStruct
{
	const op="";
	public $__caret_start;
	public $__caret_end;
	/* ======================= Class Init Functions ======================= */
	function _init($__ctx)
	{
		parent::_init($__ctx);
		$this->__caret_start = null;
		$this->__caret_end = null;
	}
	function assignObject($__ctx,$o)
	{
		if ($o instanceof \Bayrell\Lang\OpCodes\BaseOpCode)
		{
			$this->__caret_start = $o->__caret_start;
			$this->__caret_end = $o->__caret_end;
		}
		parent::assignObject($__ctx,$o);
	}
	function assignValue($__ctx,$k,$v)
	{
		if ($k == "caret_start")$this->__caret_start = $v;
		else if ($k == "caret_end")$this->__caret_end = $v;
		else parent::assignValue($__ctx,$k,$v);
	}
	function takeValue($__ctx,$k,$d=null)
	{
		if ($k == "caret_start")return $this->__caret_start;
		else if ($k == "caret_end")return $this->__caret_end;
		return parent::takeValue($__ctx,$k,$d);
	}
	function getClassName()
	{
		return "Bayrell.Lang.OpCodes.BaseOpCode";
	}
	static function getCurrentNamespace()
	{
		return "Bayrell.Lang.OpCodes";
	}
	static function getCurrentClassName()
	{
		return "Bayrell.Lang.OpCodes.BaseOpCode";
	}
	static function getParentClassName()
	{
		return "Runtime.CoreStruct";
	}
	static function getClassInfo($__ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($__ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.OpCodes.BaseOpCode",
			"name"=>"Bayrell.Lang.OpCodes.BaseOpCode",
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
	}
	static function getFieldsList($__ctx,$f)
	{
		$a = [];
		if (($f|3)==3)
		{
			$a[] = "caret_start";
			$a[] = "caret_end";
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