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
namespace Bayrell\Lang;
class CoreTranslator extends \Runtime\CoreStruct
{
	public $__current_namespace_name;
	public $__current_class_name;
	public $__current_class_full_name;
	public $__current_class_extends_name;
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
	function findSaveOpCode($__ctx, $op_code)
	{
		return $this->save_op_codes->findItem($__ctx, \Runtime\lib::equalAttr($__ctx, "op_code", $op_code));
	}
	/**
	 * Increment indent level
	 */
	function levelInc($__ctx)
	{
		return $this->copy($__ctx, \Runtime\Dict::from(["indent_level"=>$this->indent_level + 1]));
	}
	/**
	 * Decrease indent level
	 */
	function levelDec($__ctx)
	{
		return $this->copy($__ctx, \Runtime\Dict::from(["indent_level"=>$this->indent_level - 1]));
	}
	/**
	 * Output content with indent
	 */
	function s($__ctx, $s, $content=null)
	{
		if ($s == "")
		{
			return "";
		}
		if ($content === "")
		{
			return $s;
		}
		return $this->crlf . \Runtime\rtl::toStr(\Runtime\rs::str_repeat($__ctx, $this->indent, $this->indent_level)) . \Runtime\rtl::toStr($s);
	}
	/**
	 * Output content with opcode level
	 */
	function o($__ctx, $s, $opcode_level_in, $opcode_level_out)
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
	static function translate($__ctx, $t, $op_code)
	{
		return "";
	}
	/**
	 * Inc save op code
	 */
	static function nextSaveOpCode($__ctx, $t)
	{
		return "__v" . \Runtime\rtl::toStr($t->save_op_code_inc);
	}
	/**
	 * Inc save op code
	 */
	static function incSaveOpCode($__ctx, $t)
	{
		$var_name = static::nextSaveOpCode($__ctx, $t);
		$save_op_code_inc = $t->save_op_code_inc + 1;
		$t = $t->copy($__ctx, \Runtime\Dict::from(["save_op_code_inc"=>$save_op_code_inc]));
		return \Runtime\Collection::from([$t,$var_name]);
	}
	/**
	 * Add save op code
	 */
	static function addSaveOpCode($__ctx, $t, $data)
	{
		$var_name = $data->get($__ctx, "var_name", "");
		$content = $data->get($__ctx, "content", "");
		$var_content = $data->get($__ctx, "var_content", "");
		$save_op_code_inc = $t->save_op_code_inc;
		if ($var_name == "")
		{
			$var_name = static::nextSaveOpCode($__ctx, $t);
			$save_op_code_inc += 1;
		}
		$data = $data->setIm($__ctx, "var_name", $var_name);
		$s = new \Bayrell\Lang\SaveOpCode($__ctx, $data);
		$t = $t->copy($__ctx, \Runtime\Dict::from(["save_op_codes"=>$t->save_op_codes->pushIm($__ctx, $s),"save_op_code_inc"=>$save_op_code_inc]));
		return \Runtime\Collection::from([$t,$var_name]);
	}
	/**
	 * Clear save op code
	 */
	static function clearSaveOpCode($__ctx, $t)
	{
		$t = $t->copy($__ctx, ["save_op_codes"=>new \Runtime\Collection($__ctx)]);
		$t = $t->copy($__ctx, ["save_op_code_inc"=>0]);
		return $t;
	}
	/**
	 * Output save op code content
	 */
	static function outputSaveOpCode($__ctx, $t, $save_op_code_value=0)
	{
		$content = "";
		for ($i = 0;$i < $t->save_op_codes->count($__ctx);$i++)
		{
			if ($i < $save_op_code_value)
			{
				continue;
			}
			$save = $t->save_op_codes->item($__ctx, $i);
			$s = ($save->content == "") ? $t->s($__ctx, "var " . \Runtime\rtl::toStr($save->var_name) . \Runtime\rtl::toStr(" = ") . \Runtime\rtl::toStr($save->var_content) . \Runtime\rtl::toStr(";")) : $save->content;
			$content .= \Runtime\rtl::toStr($s);
		}
		return $content;
	}
	/**
	 * Call f and return result with save op codes
	 */
	static function saveOpCodeCall($__ctx, $t, $f, $args)
	{
		/* Clear save op codes */
		$save_op_codes = $t->save_op_codes;
		$save_op_code_inc = $t->save_op_code_inc;
		$res = \Runtime\rtl::apply($__ctx, $f, $args->unshiftIm($__ctx, $t));
		$t = $res[0];
		$value = $res[1];
		/* Output save op code */
		$save = $t->staticMethod("outputSaveOpCode")($__ctx, $t, $save_op_codes->count($__ctx));
		/* Restore save op codes */
		$t = $t->copy($__ctx, ["save_op_codes"=>$save_op_codes]);
		$t = $t->copy($__ctx, ["save_op_code_inc"=>$save_op_code_inc]);
		return \Runtime\Collection::from([$t,$save,$value]);
	}
	/* ======================= Class Init Functions ======================= */
	function _init($__ctx)
	{
		parent::_init($__ctx);
		$this->__current_namespace_name = "";
		$this->__current_class_name = "";
		$this->__current_class_full_name = "";
		$this->__current_class_extends_name = "";
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
	function assignObject($__ctx,$o)
	{
		if ($o instanceof \Bayrell\Lang\CoreTranslator)
		{
			$this->__current_namespace_name = $o->__current_namespace_name;
			$this->__current_class_name = $o->__current_class_name;
			$this->__current_class_full_name = $o->__current_class_full_name;
			$this->__current_class_extends_name = $o->__current_class_extends_name;
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
		parent::assignObject($__ctx,$o);
	}
	function assignValue($__ctx,$k,$v)
	{
		if ($k == "current_namespace_name")$this->__current_namespace_name = $v;
		else if ($k == "current_class_name")$this->__current_class_name = $v;
		else if ($k == "current_class_full_name")$this->__current_class_full_name = $v;
		else if ($k == "current_class_extends_name")$this->__current_class_extends_name = $v;
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
		else parent::assignValue($__ctx,$k,$v);
	}
	function takeValue($__ctx,$k,$d=null)
	{
		if ($k == "current_namespace_name")return $this->__current_namespace_name;
		else if ($k == "current_class_name")return $this->__current_class_name;
		else if ($k == "current_class_full_name")return $this->__current_class_full_name;
		else if ($k == "current_class_extends_name")return $this->__current_class_extends_name;
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
		return parent::takeValue($__ctx,$k,$d);
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
	static function getClassInfo($__ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($__ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.CoreTranslator",
			"name"=>"Bayrell.Lang.CoreTranslator",
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
	}
	static function getFieldsList($__ctx,$f)
	{
		$a = [];
		if (($f|3)==3)
		{
			$a[] = "current_namespace_name";
			$a[] = "current_class_name";
			$a[] = "current_class_full_name";
			$a[] = "current_class_extends_name";
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