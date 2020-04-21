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
class OpDeclareClass extends \Bayrell\Lang\OpCodes\BaseOpCode
{
	const KIND_CLASS="class";
	const KIND_STRUCT="struct";
	const KIND_INTERFACE="interface";
	public $__op;
	public $__kind;
	public $__name;
	public $__extend_name;
	public $__annotations;
	public $__comments;
	public $__template;
	public $__flags;
	public $__fn_create;
	public $__fn_destroy;
	public $__class_extends;
	public $__class_implements;
	public $__vars;
	public $__functions;
	public $__items;
	public $__is_static;
	public $__is_declare;
	/* ======================= Class Init Functions ======================= */
	function _init($ctx)
	{
		parent::_init($ctx);
		$this->__op = "op_class";
		$this->__kind = "";
		$this->__name = "";
		$this->__extend_name = "";
		$this->__annotations = null;
		$this->__comments = null;
		$this->__template = null;
		$this->__flags = null;
		$this->__fn_create = null;
		$this->__fn_destroy = null;
		$this->__class_extends = null;
		$this->__class_implements = null;
		$this->__vars = null;
		$this->__functions = null;
		$this->__items = null;
		$this->__is_static = false;
		$this->__is_declare = false;
	}
	function assignObject($ctx,$o)
	{
		if ($o instanceof \Bayrell\Lang\OpCodes\OpDeclareClass)
		{
			$this->__op = $o->__op;
			$this->__kind = $o->__kind;
			$this->__name = $o->__name;
			$this->__extend_name = $o->__extend_name;
			$this->__annotations = $o->__annotations;
			$this->__comments = $o->__comments;
			$this->__template = $o->__template;
			$this->__flags = $o->__flags;
			$this->__fn_create = $o->__fn_create;
			$this->__fn_destroy = $o->__fn_destroy;
			$this->__class_extends = $o->__class_extends;
			$this->__class_implements = $o->__class_implements;
			$this->__vars = $o->__vars;
			$this->__functions = $o->__functions;
			$this->__items = $o->__items;
			$this->__is_static = $o->__is_static;
			$this->__is_declare = $o->__is_declare;
		}
		parent::assignObject($ctx,$o);
	}
	function assignValue($ctx,$k,$v)
	{
		if ($k == "op")$this->__op = $v;
		else if ($k == "kind")$this->__kind = $v;
		else if ($k == "name")$this->__name = $v;
		else if ($k == "extend_name")$this->__extend_name = $v;
		else if ($k == "annotations")$this->__annotations = $v;
		else if ($k == "comments")$this->__comments = $v;
		else if ($k == "template")$this->__template = $v;
		else if ($k == "flags")$this->__flags = $v;
		else if ($k == "fn_create")$this->__fn_create = $v;
		else if ($k == "fn_destroy")$this->__fn_destroy = $v;
		else if ($k == "class_extends")$this->__class_extends = $v;
		else if ($k == "class_implements")$this->__class_implements = $v;
		else if ($k == "vars")$this->__vars = $v;
		else if ($k == "functions")$this->__functions = $v;
		else if ($k == "items")$this->__items = $v;
		else if ($k == "is_static")$this->__is_static = $v;
		else if ($k == "is_declare")$this->__is_declare = $v;
		else parent::assignValue($ctx,$k,$v);
	}
	function takeValue($ctx,$k,$d=null)
	{
		if ($k == "op")return $this->__op;
		else if ($k == "kind")return $this->__kind;
		else if ($k == "name")return $this->__name;
		else if ($k == "extend_name")return $this->__extend_name;
		else if ($k == "annotations")return $this->__annotations;
		else if ($k == "comments")return $this->__comments;
		else if ($k == "template")return $this->__template;
		else if ($k == "flags")return $this->__flags;
		else if ($k == "fn_create")return $this->__fn_create;
		else if ($k == "fn_destroy")return $this->__fn_destroy;
		else if ($k == "class_extends")return $this->__class_extends;
		else if ($k == "class_implements")return $this->__class_implements;
		else if ($k == "vars")return $this->__vars;
		else if ($k == "functions")return $this->__functions;
		else if ($k == "items")return $this->__items;
		else if ($k == "is_static")return $this->__is_static;
		else if ($k == "is_declare")return $this->__is_declare;
		return parent::takeValue($ctx,$k,$d);
	}
	function getClassName()
	{
		return "Bayrell.Lang.OpCodes.OpDeclareClass";
	}
	static function getCurrentNamespace()
	{
		return "Bayrell.Lang.OpCodes";
	}
	static function getCurrentClassName()
	{
		return "Bayrell.Lang.OpCodes.OpDeclareClass";
	}
	static function getParentClassName()
	{
		return "Bayrell.Lang.OpCodes.BaseOpCode";
	}
	static function getClassInfo($ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.OpCodes.OpDeclareClass",
			"name"=>"Bayrell.Lang.OpCodes.OpDeclareClass",
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
			$a[] = "kind";
			$a[] = "name";
			$a[] = "extend_name";
			$a[] = "annotations";
			$a[] = "comments";
			$a[] = "template";
			$a[] = "flags";
			$a[] = "fn_create";
			$a[] = "fn_destroy";
			$a[] = "class_extends";
			$a[] = "class_implements";
			$a[] = "vars";
			$a[] = "functions";
			$a[] = "items";
			$a[] = "is_static";
			$a[] = "is_declare";
		}
		return \Runtime\Collection::from($a);
	}
	static function getFieldInfoByName($ctx,$field_name)
	{
		if ($field_name == "KIND_CLASS") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpDeclareClass",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "KIND_STRUCT") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpDeclareClass",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "KIND_INTERFACE") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpDeclareClass",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "op") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpDeclareClass",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "kind") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpDeclareClass",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "name") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpDeclareClass",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "extend_name") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpDeclareClass",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "annotations") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpDeclareClass",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "comments") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpDeclareClass",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "template") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpDeclareClass",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "flags") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpDeclareClass",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "fn_create") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpDeclareClass",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "fn_destroy") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpDeclareClass",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "class_extends") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpDeclareClass",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "class_implements") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpDeclareClass",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "vars") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpDeclareClass",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "functions") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpDeclareClass",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "items") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpDeclareClass",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "is_static") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpDeclareClass",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "is_declare") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.OpCodes.OpDeclareClass",
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