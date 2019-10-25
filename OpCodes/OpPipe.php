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
class OpPipe extends \Bayrell\Lang\OpCodes\BaseOpCode
{
	const KIND_METHOD="method";
	const KIND_LAMBDA="lambda";
	public $__op;
	public $__kind;
	public $__class_name;
	public $__method_name;
	public $__obj;
	public $__args;
	public $__is_await;
	public $__is_context;
	/* ======================= Class Init Functions ======================= */
	function _init($__ctx)
	{
		parent::_init($__ctx);
		$this->__op = "op_pipe";
		$this->__kind = "";
		$this->__class_name = "";
		$this->__method_name = "";
		$this->__obj = null;
		$this->__args = null;
		$this->__is_await = false;
		$this->__is_context = true;
	}
	function assignObject($__ctx,$o)
	{
		if ($o instanceof \Bayrell\Lang\OpCodes\OpPipe)
		{
			$this->__op = $o->__op;
			$this->__kind = $o->__kind;
			$this->__class_name = $o->__class_name;
			$this->__method_name = $o->__method_name;
			$this->__obj = $o->__obj;
			$this->__args = $o->__args;
			$this->__is_await = $o->__is_await;
			$this->__is_context = $o->__is_context;
		}
		parent::assignObject($__ctx,$o);
	}
	function assignValue($__ctx,$k,$v)
	{
		if ($k == "op")$this->__op = $v;
		else if ($k == "kind")$this->__kind = $v;
		else if ($k == "class_name")$this->__class_name = $v;
		else if ($k == "method_name")$this->__method_name = $v;
		else if ($k == "obj")$this->__obj = $v;
		else if ($k == "args")$this->__args = $v;
		else if ($k == "is_await")$this->__is_await = $v;
		else if ($k == "is_context")$this->__is_context = $v;
		else parent::assignValue($__ctx,$k,$v);
	}
	function takeValue($__ctx,$k,$d=null)
	{
		if ($k == "op")return $this->__op;
		else if ($k == "kind")return $this->__kind;
		else if ($k == "class_name")return $this->__class_name;
		else if ($k == "method_name")return $this->__method_name;
		else if ($k == "obj")return $this->__obj;
		else if ($k == "args")return $this->__args;
		else if ($k == "is_await")return $this->__is_await;
		else if ($k == "is_context")return $this->__is_context;
		return parent::takeValue($__ctx,$k,$d);
	}
	function getClassName()
	{
		return "Bayrell.Lang.OpCodes.OpPipe";
	}
	static function getCurrentNamespace()
	{
		return "Bayrell.Lang.OpCodes";
	}
	static function getCurrentClassName()
	{
		return "Bayrell.Lang.OpCodes.OpPipe";
	}
	static function getParentClassName()
	{
		return "Bayrell.Lang.OpCodes.BaseOpCode";
	}
	static function getClassInfo($__ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($__ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.OpCodes.OpPipe",
			"name"=>"Bayrell.Lang.OpCodes.OpPipe",
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
			$a[] = "kind";
			$a[] = "class_name";
			$a[] = "method_name";
			$a[] = "obj";
			$a[] = "args";
			$a[] = "is_await";
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