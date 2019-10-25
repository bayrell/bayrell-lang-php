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
class OpDeclareFunction extends \Bayrell\Lang\OpCodes\BaseOpCode
{
	public $__op;
	public $__name;
	public $__annotations;
	public $__comments;
	public $__args;
	public $__vars;
	public $__result_type;
	public $__expression;
	public $__value;
	public $__flags;
	public $__is_context;
	/**
	 * Returns true if static function
	 */
	function isStatic($__ctx)
	{
		return $this->flags != null && $this->flags->isFlag($__ctx, "static") || $this->flags->isFlag($__ctx, "lambda");
	}
	/**
	 * Returns true if is flag
	 */
	function isFlag($__ctx, $flag_name)
	{
		return $this->flags != null && $this->flags->isFlag($__ctx, $flag_name);
	}
	/* ======================= Class Init Functions ======================= */
	function _init($__ctx)
	{
		parent::_init($__ctx);
		$this->__op = "op_function";
		$this->__name = "";
		$this->__annotations = null;
		$this->__comments = null;
		$this->__args = null;
		$this->__vars = null;
		$this->__result_type = null;
		$this->__expression = null;
		$this->__value = null;
		$this->__flags = null;
		$this->__is_context = true;
	}
	function assignObject($__ctx,$o)
	{
		if ($o instanceof \Bayrell\Lang\OpCodes\OpDeclareFunction)
		{
			$this->__op = $o->__op;
			$this->__name = $o->__name;
			$this->__annotations = $o->__annotations;
			$this->__comments = $o->__comments;
			$this->__args = $o->__args;
			$this->__vars = $o->__vars;
			$this->__result_type = $o->__result_type;
			$this->__expression = $o->__expression;
			$this->__value = $o->__value;
			$this->__flags = $o->__flags;
			$this->__is_context = $o->__is_context;
		}
		parent::assignObject($__ctx,$o);
	}
	function assignValue($__ctx,$k,$v)
	{
		if ($k == "op")$this->__op = $v;
		else if ($k == "name")$this->__name = $v;
		else if ($k == "annotations")$this->__annotations = $v;
		else if ($k == "comments")$this->__comments = $v;
		else if ($k == "args")$this->__args = $v;
		else if ($k == "vars")$this->__vars = $v;
		else if ($k == "result_type")$this->__result_type = $v;
		else if ($k == "expression")$this->__expression = $v;
		else if ($k == "value")$this->__value = $v;
		else if ($k == "flags")$this->__flags = $v;
		else if ($k == "is_context")$this->__is_context = $v;
		else parent::assignValue($__ctx,$k,$v);
	}
	function takeValue($__ctx,$k,$d=null)
	{
		if ($k == "op")return $this->__op;
		else if ($k == "name")return $this->__name;
		else if ($k == "annotations")return $this->__annotations;
		else if ($k == "comments")return $this->__comments;
		else if ($k == "args")return $this->__args;
		else if ($k == "vars")return $this->__vars;
		else if ($k == "result_type")return $this->__result_type;
		else if ($k == "expression")return $this->__expression;
		else if ($k == "value")return $this->__value;
		else if ($k == "flags")return $this->__flags;
		else if ($k == "is_context")return $this->__is_context;
		return parent::takeValue($__ctx,$k,$d);
	}
	function getClassName()
	{
		return "Bayrell.Lang.OpCodes.OpDeclareFunction";
	}
	static function getCurrentNamespace()
	{
		return "Bayrell.Lang.OpCodes";
	}
	static function getCurrentClassName()
	{
		return "Bayrell.Lang.OpCodes.OpDeclareFunction";
	}
	static function getParentClassName()
	{
		return "Bayrell.Lang.OpCodes.BaseOpCode";
	}
	static function getClassInfo($__ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($__ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.OpCodes.OpDeclareFunction",
			"name"=>"Bayrell.Lang.OpCodes.OpDeclareFunction",
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
			$a[] = "name";
			$a[] = "annotations";
			$a[] = "comments";
			$a[] = "args";
			$a[] = "vars";
			$a[] = "result_type";
			$a[] = "expression";
			$a[] = "value";
			$a[] = "flags";
			$a[] = "is_context";
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