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
class OpFlags extends \Runtime\CoreStruct
{
	public $__p_async;
	public $__p_export;
	public $__p_static;
	public $__p_const;
	public $__p_public;
	public $__p_private;
	public $__p_protected;
	public $__p_declare;
	public $__p_serializable;
	public $__p_cloneable;
	public $__p_assignable;
	public $__p_memorize;
	public $__p_lambda;
	public $__p_pure;
	/**
	 * Read is Flag
	 */
	function isFlag($ctx, $name)
	{
		if (!\Bayrell\Lang\OpCodes\OpFlags::hasFlag($ctx, $name))
		{
			return false;
		}
		return $this->takeValue($ctx, "p_" . \Runtime\rtl::toStr($name));
	}
	/**
	 * Get flags
	 */
	static function getFlags($ctx)
	{
		return \Runtime\Collection::from(["async","export","static","const","public","private","declare","protected","serializable","cloneable","assignable","memorize","pure"]);
	}
	/**
	 * Get flags
	 */
	static function hasFlag($ctx, $flag_name)
	{
		if ($flag_name == "async" || $flag_name == "export" || $flag_name == "static" || $flag_name == "const" || $flag_name == "public" || $flag_name == "private" || $flag_name == "declare" || $flag_name == "protected" || $flag_name == "serializable" || $flag_name == "cloneable" || $flag_name == "assignable" || $flag_name == "memorize" || $flag_name == "lambda" || $flag_name == "pure")
		{
			return true;
		}
		return false;
	}
	/* ======================= Class Init Functions ======================= */
	function _init($ctx)
	{
		parent::_init($ctx);
		$this->__p_async = false;
		$this->__p_export = false;
		$this->__p_static = false;
		$this->__p_const = false;
		$this->__p_public = false;
		$this->__p_private = false;
		$this->__p_protected = false;
		$this->__p_declare = false;
		$this->__p_serializable = false;
		$this->__p_cloneable = false;
		$this->__p_assignable = false;
		$this->__p_memorize = false;
		$this->__p_lambda = false;
		$this->__p_pure = false;
	}
	function assignObject($ctx,$o)
	{
		if ($o instanceof \Bayrell\Lang\OpCodes\OpFlags)
		{
			$this->__p_async = $o->__p_async;
			$this->__p_export = $o->__p_export;
			$this->__p_static = $o->__p_static;
			$this->__p_const = $o->__p_const;
			$this->__p_public = $o->__p_public;
			$this->__p_private = $o->__p_private;
			$this->__p_protected = $o->__p_protected;
			$this->__p_declare = $o->__p_declare;
			$this->__p_serializable = $o->__p_serializable;
			$this->__p_cloneable = $o->__p_cloneable;
			$this->__p_assignable = $o->__p_assignable;
			$this->__p_memorize = $o->__p_memorize;
			$this->__p_lambda = $o->__p_lambda;
			$this->__p_pure = $o->__p_pure;
		}
		parent::assignObject($ctx,$o);
	}
	function assignValue($ctx,$k,$v)
	{
		if ($k == "p_async")$this->__p_async = $v;
		else if ($k == "p_export")$this->__p_export = $v;
		else if ($k == "p_static")$this->__p_static = $v;
		else if ($k == "p_const")$this->__p_const = $v;
		else if ($k == "p_public")$this->__p_public = $v;
		else if ($k == "p_private")$this->__p_private = $v;
		else if ($k == "p_protected")$this->__p_protected = $v;
		else if ($k == "p_declare")$this->__p_declare = $v;
		else if ($k == "p_serializable")$this->__p_serializable = $v;
		else if ($k == "p_cloneable")$this->__p_cloneable = $v;
		else if ($k == "p_assignable")$this->__p_assignable = $v;
		else if ($k == "p_memorize")$this->__p_memorize = $v;
		else if ($k == "p_lambda")$this->__p_lambda = $v;
		else if ($k == "p_pure")$this->__p_pure = $v;
		else parent::assignValue($ctx,$k,$v);
	}
	function takeValue($ctx,$k,$d=null)
	{
		if ($k == "p_async")return $this->__p_async;
		else if ($k == "p_export")return $this->__p_export;
		else if ($k == "p_static")return $this->__p_static;
		else if ($k == "p_const")return $this->__p_const;
		else if ($k == "p_public")return $this->__p_public;
		else if ($k == "p_private")return $this->__p_private;
		else if ($k == "p_protected")return $this->__p_protected;
		else if ($k == "p_declare")return $this->__p_declare;
		else if ($k == "p_serializable")return $this->__p_serializable;
		else if ($k == "p_cloneable")return $this->__p_cloneable;
		else if ($k == "p_assignable")return $this->__p_assignable;
		else if ($k == "p_memorize")return $this->__p_memorize;
		else if ($k == "p_lambda")return $this->__p_lambda;
		else if ($k == "p_pure")return $this->__p_pure;
		return parent::takeValue($ctx,$k,$d);
	}
	function getClassName()
	{
		return "Bayrell.Lang.OpCodes.OpFlags";
	}
	static function getCurrentNamespace()
	{
		return "Bayrell.Lang.OpCodes";
	}
	static function getCurrentClassName()
	{
		return "Bayrell.Lang.OpCodes.OpFlags";
	}
	static function getParentClassName()
	{
		return "Runtime.CoreStruct";
	}
	static function getClassInfo($ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.OpCodes.OpFlags",
			"name"=>"Bayrell.Lang.OpCodes.OpFlags",
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
	}
	static function getFieldsList($ctx,$f)
	{
		$a = [];
		if (($f|3)==3)
		{
			$a[] = "p_async";
			$a[] = "p_export";
			$a[] = "p_static";
			$a[] = "p_const";
			$a[] = "p_public";
			$a[] = "p_private";
			$a[] = "p_protected";
			$a[] = "p_declare";
			$a[] = "p_serializable";
			$a[] = "p_cloneable";
			$a[] = "p_assignable";
			$a[] = "p_memorize";
			$a[] = "p_lambda";
			$a[] = "p_pure";
		}
		return \Runtime\Collection::from($a);
	}
	static function getFieldInfoByName($ctx,$field_name)
	{
		if ($field_name == "p_async") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpFlags",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "p_export") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpFlags",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "p_static") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpFlags",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "p_const") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpFlags",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "p_public") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpFlags",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "p_private") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpFlags",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "p_protected") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpFlags",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "p_declare") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpFlags",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "p_serializable") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpFlags",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "p_cloneable") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpFlags",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "p_assignable") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpFlags",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "p_memorize") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpFlags",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "p_lambda") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpFlags",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "p_pure") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpFlags",
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