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
class OpHtmlTag extends \Bayrell\Lang\OpCodes\BaseOpCode
{
	public $__op;
	public $__tag_name;
	public $__op_code_name;
	public $__attrs;
	public $__spreads;
	public $__items;
	/* ======================= Class Init Functions ======================= */
	function _init($__ctx)
	{
		parent::_init($__ctx);
		$this->__op = "op_html_tag";
		$this->__tag_name = "";
		$this->__op_code_name = null;
		$this->__attrs = null;
		$this->__spreads = null;
		$this->__items = null;
	}
	function assignObject($__ctx,$o)
	{
		if ($o instanceof \Bayrell\Lang\OpCodes\OpHtmlTag)
		{
			$this->__op = $o->__op;
			$this->__tag_name = $o->__tag_name;
			$this->__op_code_name = $o->__op_code_name;
			$this->__attrs = $o->__attrs;
			$this->__spreads = $o->__spreads;
			$this->__items = $o->__items;
		}
		parent::assignObject($__ctx,$o);
	}
	function assignValue($__ctx,$k,$v)
	{
		if ($k == "op")$this->__op = $v;
		else if ($k == "tag_name")$this->__tag_name = $v;
		else if ($k == "op_code_name")$this->__op_code_name = $v;
		else if ($k == "attrs")$this->__attrs = $v;
		else if ($k == "spreads")$this->__spreads = $v;
		else if ($k == "items")$this->__items = $v;
		else parent::assignValue($__ctx,$k,$v);
	}
	function takeValue($__ctx,$k,$d=null)
	{
		if ($k == "op")return $this->__op;
		else if ($k == "tag_name")return $this->__tag_name;
		else if ($k == "op_code_name")return $this->__op_code_name;
		else if ($k == "attrs")return $this->__attrs;
		else if ($k == "spreads")return $this->__spreads;
		else if ($k == "items")return $this->__items;
		return parent::takeValue($__ctx,$k,$d);
	}
	function getClassName()
	{
		return "Bayrell.Lang.OpCodes.OpHtmlTag";
	}
	static function getCurrentNamespace()
	{
		return "Bayrell.Lang.OpCodes";
	}
	static function getCurrentClassName()
	{
		return "Bayrell.Lang.OpCodes.OpHtmlTag";
	}
	static function getParentClassName()
	{
		return "Bayrell.Lang.OpCodes.BaseOpCode";
	}
	static function getClassInfo($__ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($__ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.OpCodes.OpHtmlTag",
			"name"=>"Bayrell.Lang.OpCodes.OpHtmlTag",
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
			$a[] = "tag_name";
			$a[] = "op_code_name";
			$a[] = "attrs";
			$a[] = "spreads";
			$a[] = "items";
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