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
namespace Bayrell\Lang;
class CoreTranslator extends \Runtime\CoreStruct
{
	public $__current_namespace_name;
	public $__current_class_name;
	public $__current_class_full_name;
	public $__current_class_extends_name;
	public $__current_class;
	public $__current_function;
	public $__modules;
	public $__vars;
	public $__save_vars;
	public $__save_op_codes;
	public $__save_op_code_inc;
	public $__is_static_function;
	public $__is_operation;
	public $__opcode_level;
	public $__indent_level;
	public $__indent;
	public $__crlf;
	public $__flag_struct_check_types;
	public $__preprocessor_flags;
	/**
	 * Find save op code
	 */
	function findSaveOpCode($ctx, $op_code)
	{
		return $this->save_op_codes->findItem($ctx, \Runtime\lib::equalAttr($ctx, "op_code", $op_code));
	}
	/**
	 * Increment indent level
	 */
	function levelInc($ctx)
	{
		return $this->copy($ctx, \Runtime\Dict::from(["indent_level"=>$this->indent_level + 1]));
	}
	/**
	 * Decrease indent level
	 */
	function levelDec($ctx)
	{
		return $this->copy($ctx, \Runtime\Dict::from(["indent_level"=>$this->indent_level - 1]));
	}
	/**
	 * Output content with indent
	 */
	function s($ctx, $s, $content=null)
	{
		if ($s == "")
		{
			return "";
		}
		if ($content === "")
		{
			return $s;
		}
		return $this->crlf . \Runtime\rtl::toStr(\Runtime\rs::str_repeat($ctx, $this->indent, $this->indent_level)) . \Runtime\rtl::toStr($s);
	}
	/**
	 * Output content with indent
	 */
	function s2($ctx, $s)
	{
		return $this->crlf . \Runtime\rtl::toStr(\Runtime\rs::str_repeat($ctx, $this->indent, $this->indent_level)) . \Runtime\rtl::toStr($s);
	}
	/**
	 * Output content with opcode level
	 */
	function o($ctx, $s, $opcode_level_in, $opcode_level_out)
	{
		if ($opcode_level_in < $opcode_level_out)
		{
			return "(" . \Runtime\rtl::toStr($s) . \Runtime\rtl::toStr(")");
		}
		return $s;
	}
	/**
	 * Translate BaseOpCode
	 */
	static function translate($ctx, $t, $op_code)
	{
		return "";
	}
	/**
	 * Inc save op code
	 */
	static function nextSaveOpCode($ctx, $t)
	{
		return "__v" . \Runtime\rtl::toStr($t->save_op_code_inc);
	}
	/**
	 * Inc save op code
	 */
	static function incSaveOpCode($ctx, $t)
	{
		$var_name = static::nextSaveOpCode($ctx, $t);
		$save_op_code_inc = $t->save_op_code_inc + 1;
		$t = $t->copy($ctx, \Runtime\Dict::from(["save_op_code_inc"=>$save_op_code_inc]));
		return \Runtime\Collection::from([$t,$var_name]);
	}
	/**
	 * Add save op code
	 */
	static function addSaveOpCode($ctx, $t, $data)
	{
		$var_name = $data->get($ctx, "var_name", "");
		$content = $data->get($ctx, "content", "");
		$var_content = $data->get($ctx, "var_content", "");
		$save_op_code_inc = $t->save_op_code_inc;
		if ($var_name == "" && $content == "")
		{
			$var_name = static::nextSaveOpCode($ctx, $t);
			$data = $data->setIm($ctx, "var_name", $var_name);
			$save_op_code_inc += 1;
		}
		$s = new \Bayrell\Lang\SaveOpCode($ctx, $data);
		$t = $t->copy($ctx, \Runtime\Dict::from(["save_op_codes"=>$t->save_op_codes->pushIm($ctx, $s),"save_op_code_inc"=>$save_op_code_inc]));
		return \Runtime\Collection::from([$t,$var_name]);
	}
	/**
	 * Clear save op code
	 */
	static function clearSaveOpCode($ctx, $t)
	{
		$t = $t->copy($ctx, ["save_op_codes"=>new \Runtime\Collection($ctx)]);
		$t = $t->copy($ctx, ["save_op_code_inc"=>0]);
		return $t;
	}
	/**
	 * Output save op code content
	 */
	static function outputSaveOpCode($ctx, $t, $save_op_code_value=0)
	{
		$content = "";
		for ($i = 0;$i < $t->save_op_codes->count($ctx);$i++)
		{
			if ($i < $save_op_code_value)
			{
				continue;
			}
			$save = $t->save_op_codes->item($ctx, $i);
			$s = ($save->content == "") ? $t->s($ctx, "var " . \Runtime\rtl::toStr($save->var_name) . \Runtime\rtl::toStr(" = ") . \Runtime\rtl::toStr($save->var_content) . \Runtime\rtl::toStr(";")) : $save->content;
			$content .= \Runtime\rtl::toStr($s);
		}
		return $content;
	}
	/**
	 * Call f and return result with save op codes
	 */
	static function saveOpCodeCall($ctx, $t, $f, $args)
	{
		/* Clear save op codes */
		$save_op_codes = $t->save_op_codes;
		$save_op_code_inc = $t->save_op_code_inc;
		$res = \Runtime\rtl::apply($ctx, $f, $args->unshiftIm($ctx, $t));
		$t = $res[0];
		$value = $res[1];
		/* Output save op code */
		$save = $t::outputSaveOpCode($ctx, $t, $save_op_codes->count($ctx));
		/* Restore save op codes */
		$t = $t->copy($ctx, ["save_op_codes"=>$save_op_codes]);
		$t = $t->copy($ctx, ["save_op_code_inc"=>$save_op_code_inc]);
		return \Runtime\Collection::from([$t,$save,$value]);
	}
	/* ======================= Class Init Functions ======================= */
	function _init($ctx)
	{
		parent::_init($ctx);
		$this->__current_namespace_name = "";
		$this->__current_class_name = "";
		$this->__current_class_full_name = "";
		$this->__current_class_extends_name = "";
		$this->__current_class = null;
		$this->__current_function = null;
		$this->__modules = null;
		$this->__vars = null;
		$this->__save_vars = null;
		$this->__save_op_codes = null;
		$this->__save_op_code_inc = 0;
		$this->__is_static_function = false;
		$this->__is_operation = false;
		$this->__opcode_level = 0;
		$this->__indent_level = 0;
		$this->__indent = "\t";
		$this->__crlf = "\n";
		$this->__flag_struct_check_types = false;
		$this->__preprocessor_flags = null;
	}
	function assignObject($ctx,$o)
	{
		if ($o instanceof \Bayrell\Lang\CoreTranslator)
		{
			$this->__current_namespace_name = $o->__current_namespace_name;
			$this->__current_class_name = $o->__current_class_name;
			$this->__current_class_full_name = $o->__current_class_full_name;
			$this->__current_class_extends_name = $o->__current_class_extends_name;
			$this->__current_class = $o->__current_class;
			$this->__current_function = $o->__current_function;
			$this->__modules = $o->__modules;
			$this->__vars = $o->__vars;
			$this->__save_vars = $o->__save_vars;
			$this->__save_op_codes = $o->__save_op_codes;
			$this->__save_op_code_inc = $o->__save_op_code_inc;
			$this->__is_static_function = $o->__is_static_function;
			$this->__is_operation = $o->__is_operation;
			$this->__opcode_level = $o->__opcode_level;
			$this->__indent_level = $o->__indent_level;
			$this->__indent = $o->__indent;
			$this->__crlf = $o->__crlf;
			$this->__flag_struct_check_types = $o->__flag_struct_check_types;
			$this->__preprocessor_flags = $o->__preprocessor_flags;
		}
		parent::assignObject($ctx,$o);
	}
	function assignValue($ctx,$k,$v)
	{
		if ($k == "current_namespace_name")$this->__current_namespace_name = $v;
		else if ($k == "current_class_name")$this->__current_class_name = $v;
		else if ($k == "current_class_full_name")$this->__current_class_full_name = $v;
		else if ($k == "current_class_extends_name")$this->__current_class_extends_name = $v;
		else if ($k == "current_class")$this->__current_class = $v;
		else if ($k == "current_function")$this->__current_function = $v;
		else if ($k == "modules")$this->__modules = $v;
		else if ($k == "vars")$this->__vars = $v;
		else if ($k == "save_vars")$this->__save_vars = $v;
		else if ($k == "save_op_codes")$this->__save_op_codes = $v;
		else if ($k == "save_op_code_inc")$this->__save_op_code_inc = $v;
		else if ($k == "is_static_function")$this->__is_static_function = $v;
		else if ($k == "is_operation")$this->__is_operation = $v;
		else if ($k == "opcode_level")$this->__opcode_level = $v;
		else if ($k == "indent_level")$this->__indent_level = $v;
		else if ($k == "indent")$this->__indent = $v;
		else if ($k == "crlf")$this->__crlf = $v;
		else if ($k == "flag_struct_check_types")$this->__flag_struct_check_types = $v;
		else if ($k == "preprocessor_flags")$this->__preprocessor_flags = $v;
		else parent::assignValue($ctx,$k,$v);
	}
	function takeValue($ctx,$k,$d=null)
	{
		if ($k == "current_namespace_name")return $this->__current_namespace_name;
		else if ($k == "current_class_name")return $this->__current_class_name;
		else if ($k == "current_class_full_name")return $this->__current_class_full_name;
		else if ($k == "current_class_extends_name")return $this->__current_class_extends_name;
		else if ($k == "current_class")return $this->__current_class;
		else if ($k == "current_function")return $this->__current_function;
		else if ($k == "modules")return $this->__modules;
		else if ($k == "vars")return $this->__vars;
		else if ($k == "save_vars")return $this->__save_vars;
		else if ($k == "save_op_codes")return $this->__save_op_codes;
		else if ($k == "save_op_code_inc")return $this->__save_op_code_inc;
		else if ($k == "is_static_function")return $this->__is_static_function;
		else if ($k == "is_operation")return $this->__is_operation;
		else if ($k == "opcode_level")return $this->__opcode_level;
		else if ($k == "indent_level")return $this->__indent_level;
		else if ($k == "indent")return $this->__indent;
		else if ($k == "crlf")return $this->__crlf;
		else if ($k == "flag_struct_check_types")return $this->__flag_struct_check_types;
		else if ($k == "preprocessor_flags")return $this->__preprocessor_flags;
		return parent::takeValue($ctx,$k,$d);
	}
	function getClassName()
	{
		return "Bayrell.Lang.CoreTranslator";
	}
	static function getCurrentNamespace()
	{
		return "Bayrell.Lang";
	}
	static function getCurrentClassName()
	{
		return "Bayrell.Lang.CoreTranslator";
	}
	static function getParentClassName()
	{
		return "Runtime.CoreStruct";
	}
	static function getClassInfo($ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.CoreTranslator",
			"name"=>"Bayrell.Lang.CoreTranslator",
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
	}
	static function getFieldsList($ctx,$f)
	{
		$a = [];
		if (($f|3)==3)
		{
			$a[] = "current_namespace_name";
			$a[] = "current_class_name";
			$a[] = "current_class_full_name";
			$a[] = "current_class_extends_name";
			$a[] = "current_class";
			$a[] = "current_function";
			$a[] = "modules";
			$a[] = "vars";
			$a[] = "save_vars";
			$a[] = "save_op_codes";
			$a[] = "save_op_code_inc";
			$a[] = "is_static_function";
			$a[] = "is_operation";
			$a[] = "opcode_level";
			$a[] = "indent_level";
			$a[] = "indent";
			$a[] = "crlf";
			$a[] = "flag_struct_check_types";
			$a[] = "preprocessor_flags";
		}
		return \Runtime\Collection::from($a);
	}
	static function getFieldInfoByName($ctx,$field_name)
	{
		if ($field_name == "current_namespace_name") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.CoreTranslator",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "current_class_name") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.CoreTranslator",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "current_class_full_name") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.CoreTranslator",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "current_class_extends_name") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.CoreTranslator",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "current_class") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.CoreTranslator",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "current_function") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.CoreTranslator",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "modules") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.CoreTranslator",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "vars") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.CoreTranslator",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "save_vars") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.CoreTranslator",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "save_op_codes") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.CoreTranslator",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "save_op_code_inc") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.CoreTranslator",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "is_static_function") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.CoreTranslator",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "is_operation") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.CoreTranslator",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "opcode_level") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.CoreTranslator",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "indent_level") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.CoreTranslator",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "indent") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.CoreTranslator",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "crlf") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.CoreTranslator",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "flag_struct_check_types") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.CoreTranslator",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "preprocessor_flags") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.CoreTranslator",
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