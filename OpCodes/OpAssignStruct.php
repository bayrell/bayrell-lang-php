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
class OpAssignStruct extends \Bayrell\Lang\OpCodes\BaseOpCode
{
	public $__var_name;
	public $__annotations;
	public $__comments;
	public $__names;
	public $__expression;
	/* ======================= Class Init Functions ======================= */
	function _init($__ctx)
	{
		parent::_init($__ctx);
		$this->__var_name = "";
		$this->__annotations = null;
		$this->__comments = null;
		$this->__names = null;
		$this->__expression = null;
	}
	function assignObject($__ctx,$o)
	{
		if ($o instanceof \Bayrell\Lang\OpCodes\OpAssignStruct)
		{
			$this->__var_name = $o->__var_name;
			$this->__annotations = $o->__annotations;
			$this->__comments = $o->__comments;
			$this->__names = $o->__names;
			$this->__expression = $o->__expression;
		}
		parent::assignObject($__ctx,$o);
	}
	function assignValue($__ctx,$k,$v)
	{
		if ($k == "var_name")$this->__var_name = $v;
		else if ($k == "annotations")$this->__annotations = $v;
		else if ($k == "comments")$this->__comments = $v;
		else if ($k == "names")$this->__names = $v;
		else if ($k == "expression")$this->__expression = $v;
		else parent::assignValue($__ctx,$k,$v);
	}
	function takeValue($__ctx,$k,$d=null)
	{
		if ($k == "var_name")return $this->__var_name;
		else if ($k == "annotations")return $this->__annotations;
		else if ($k == "comments")return $this->__comments;
		else if ($k == "names")return $this->__names;
		else if ($k == "expression")return $this->__expression;
		return parent::takeValue($__ctx,$k,$d);
	}
	function getClassName()
	{
		return "Bayrell.Lang.OpCodes.OpAssignStruct";
	}
	static function getCurrentNamespace()
	{
		return "Bayrell.Lang.OpCodes";
	}
	static function getCurrentClassName()
	{
		return "Bayrell.Lang.OpCodes.OpAssignStruct";
	}
	static function getParentClassName()
	{
		return "Bayrell.Lang.OpCodes.BaseOpCode";
	}
	static function getClassInfo($__ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($__ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.OpCodes.OpAssignStruct",
			"name"=>"Bayrell.Lang.OpCodes.OpAssignStruct",
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
	}
	static function getFieldsList($__ctx,$f)
	{
		$a = [];
		if (($f|3)==3)
		{
			$a[] = "var_name";
			$a[] = "annotations";
			$a[] = "comments";
			$a[] = "names";
			$a[] = "expression";
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