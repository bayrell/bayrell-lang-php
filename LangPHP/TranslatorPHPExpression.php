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
namespace Bayrell\Lang\LangPHP;
class TranslatorPHPExpression
{
	/**
	 * Returns string
	 */
	static function toString($__ctx, $s)
	{
		$s = \Runtime\re::replace($__ctx, "\\\\", "\\\\", $s);
		$s = \Runtime\re::replace($__ctx, "\"", "\\\"", $s);
		$s = \Runtime\re::replace($__ctx, "\n", "\\n", $s);
		$s = \Runtime\re::replace($__ctx, "\r", "\\r", $s);
		$s = \Runtime\re::replace($__ctx, "\t", "\\t", $s);
		return "\"" . \Runtime\rtl::toStr($s) . \Runtime\rtl::toStr("\"");
	}
	/**
	 * To pattern
	 */
	static function toPattern($__ctx, $t, $pattern)
	{
		$names = static::findModuleNames($__ctx, $t, $pattern->entity_name->names);
		$e = \Runtime\rs::join($__ctx, ".", $names);
		$a = ($pattern->template != null) ? $pattern->template->map($__ctx, function ($__ctx, $pattern) use (&$t)
		{
			return static::toPattern($__ctx, $t, $pattern);
		}) : null;
		$b = ($a != null) ? ",\"t\":[" . \Runtime\rtl::toStr(\Runtime\rs::join($__ctx, ",", $a)) . \Runtime\rtl::toStr("]") : "";
		return "[\"e\"=>" . \Runtime\rtl::toStr(static::toString($__ctx, $e)) . \Runtime\rtl::toStr($b) . \Runtime\rtl::toStr("]");
	}
	/**
	 * Returns string
	 */
	static function rtlToStr($__ctx, $t, $s)
	{
		$module_name = static::getModuleName($__ctx, $t, "rtl");
		return $module_name . \Runtime\rtl::toStr("::toStr(") . \Runtime\rtl::toStr($s) . \Runtime\rtl::toStr(")");
	}
	/**
	 * Find module name
	 */
	static function findModuleName($__ctx, $t, $module_name)
	{
		if ($module_name == "Collection")
		{
			return "Runtime.Collection";
		}
		else if ($module_name == "Dict")
		{
			return "Runtime.Dict";
		}
		else if ($module_name == "Map")
		{
			return "Runtime.Map";
		}
		else if ($module_name == "Vector")
		{
			return "Runtime.Vector";
		}
		else if ($module_name == "rs")
		{
			return "Runtime.rs";
		}
		else if ($module_name == "rtl")
		{
			return "Runtime.rtl";
		}
		else if ($module_name == "ArrayInterface")
		{
			return "ArrayAccess";
		}
		else if ($t->modules->has($__ctx, $module_name))
		{
			return $t->modules->item($__ctx, $module_name);
		}
		return $module_name;
	}
	/**
	 * Returns module name
	 */
	static function findModuleNames($__ctx, $t, $names)
	{
		if ($names->count($__ctx) > 0)
		{
			$module_name = $names->first($__ctx);
			$module_name = static::findModuleName($__ctx, $t, $module_name);
			if ($module_name != "")
			{
				$names = $names->removeFirstIm($__ctx)->prependCollectionIm($__ctx, \Runtime\rs::split($__ctx, "\\.", $module_name));
			}
		}
		return $names;
	}
	/**
	 * Return module name
	 */
	static function getModuleName($__ctx, $t, $module_name)
	{
		$module_name = static::findModuleName($__ctx, $t, $module_name);
		$module_name = \Runtime\rs::replace($__ctx, "\\.", "\\", $module_name);
		return "\\" . \Runtime\rtl::toStr($module_name);
	}
	/**
	 * Return module name
	 */
	static function getModuleNames($__ctx, $t, $names)
	{
		return "\\" . \Runtime\rtl::toStr(\Runtime\rs::join($__ctx, "\\", static::findModuleNames($__ctx, $t, $names)));
	}
	/**
	 * OpTypeIdentifier
	 */
	static function OpTypeIdentifier($__ctx, $t, $op_code)
	{
		$names = static::findModuleNames($__ctx, $t, $op_code->entity_name->names);
		$s = "\\" . \Runtime\rtl::toStr(\Runtime\rs::join($__ctx, "\\", $names));
		return \Runtime\Collection::from([$t,$s]);
	}
	/**
	 * OpIdentifier
	 */
	static function OpIdentifier($__ctx, $t, $op_code)
	{
		if ($t->modules->has($__ctx, $op_code->value) || $op_code->kind == \Bayrell\Lang\OpCodes\OpIdentifier::KIND_SYS_TYPE)
		{
			$module_name = $op_code->value;
			$new_module_name = static::getModuleName($__ctx, $t, $module_name);
			return \Runtime\Collection::from([$t,$new_module_name]);
		}
		else if ($op_code->kind == \Bayrell\Lang\OpCodes\OpIdentifier::KIND_VARIABLE)
		{
			$content = $op_code->value;
			return \Runtime\Collection::from([$t,"$" . \Runtime\rtl::toStr($content)]);
		}
		else if ($op_code->kind == \Bayrell\Lang\OpCodes\OpIdentifier::KIND_CLASSREF)
		{
			$content = $op_code->value;
			if ($content == "this")
			{
				$content = "$this";
			}
			return \Runtime\Collection::from([$t,$content]);
		}
		else if ($op_code->kind == \Bayrell\Lang\OpCodes\OpIdentifier::KIND_CONTEXT && $op_code->value == "@")
		{
			return \Runtime\Collection::from([$t,"$__ctx"]);
		}
		else if ($op_code->kind == \Bayrell\Lang\OpCodes\OpIdentifier::KIND_CONTEXT && $op_code->value == "_")
		{
			return \Runtime\Collection::from([$t,"$__ctx->translate"]);
		}
		$content = $op_code->value;
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpNumber
	 */
	static function OpNumber($__ctx, $t, $op_code)
	{
		$content = $op_code->value;
		if ($op_code->negative)
		{
			$content = "-" . \Runtime\rtl::toStr($content);
			$t = $t->copy($__ctx, ["opcode_level"=>15]);
		}
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpString
	 */
	static function OpString($__ctx, $t, $op_code)
	{
		return \Runtime\Collection::from([$t,static::toString($__ctx, $op_code->value)]);
	}
	/**
	 * OpCollection
	 */
	static function OpCollection($__ctx, $t, $op_code)
	{
		$content = "";
		$values = $op_code->values->map($__ctx, function ($__ctx, $op_code) use (&$t)
		{
			$res = static::Expression($__ctx, $t, $op_code);
			$t = $res[0];
			$s = $res[1];
			return $s;
		});
		$module_name = static::getModuleName($__ctx, $t, "Collection");
		$content = $module_name . \Runtime\rtl::toStr("::from([") . \Runtime\rtl::toStr(\Runtime\rs::join($__ctx, ",", $values)) . \Runtime\rtl::toStr("])");
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpDict
	 */
	static function OpDict($__ctx, $t, $op_code, $flag_array=false)
	{
		$content = "";
		$values = $op_code->values->transition($__ctx, function ($__ctx, $op_code, $key) use (&$t)
		{
			$res = static::Expression($__ctx, $t, $op_code);
			$t = $res[0];
			$s = $res[1];
			return static::toString($__ctx, $key) . \Runtime\rtl::toStr("=>") . \Runtime\rtl::toStr($s);
		});
		$module_name = static::getModuleName($__ctx, $t, "Dict");
		if (!$flag_array)
		{
			$content = $module_name . \Runtime\rtl::toStr("::from([") . \Runtime\rtl::toStr(\Runtime\rs::join($__ctx, ",", $values)) . \Runtime\rtl::toStr("])");
		}
		else
		{
			$content = "[" . \Runtime\rtl::toStr(\Runtime\rs::join($__ctx, ",", $values)) . \Runtime\rtl::toStr("]");
		}
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * Dynamic
	 */
	static function Dynamic($__ctx, $t, $op_code, $next_op_code=null)
	{
		if ($op_code instanceof \Bayrell\Lang\OpCodes\OpIdentifier)
		{
			return static::OpIdentifier($__ctx, $t, $op_code);
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpAttr)
		{
			$attrs = new \Runtime\Vector($__ctx);
			$op_code_item = $op_code;
			$op_code_next = $op_code;
			$prev_kind = "";
			$s = "";
			while ($op_code_next instanceof \Bayrell\Lang\OpCodes\OpAttr)
			{
				$attrs->push($__ctx, $op_code_next);
				$op_code_item = $op_code_next;
				$op_code_next = $op_code_next->obj;
			}
			$attrs = $attrs->reverseIm($__ctx);
			if ($op_code_next instanceof \Bayrell\Lang\OpCodes\OpCall)
			{
				$prev_kind = "var";
				$res = static::OpCall($__ctx, $t, $op_code_next);
				$t = $res[0];
				$s = $res[1];
			}
			else if ($op_code_next instanceof \Bayrell\Lang\OpCodes\OpNew)
			{
				$prev_kind = "var";
				$res = static::OpNew($__ctx, $t, $op_code_next);
				$t = $res[0];
				$s = "(" . \Runtime\rtl::toStr($res[1]) . \Runtime\rtl::toStr(")");
			}
			else if ($op_code_next instanceof \Bayrell\Lang\OpCodes\OpIdentifier)
			{
				if ($t->modules->has($__ctx, $op_code_next->value) || $op_code_next->kind == \Bayrell\Lang\OpCodes\OpIdentifier::KIND_SYS_TYPE)
				{
					$prev_kind = "static";
					$res = static::OpIdentifier($__ctx, $t, $op_code_next);
					$t = $res[0];
					$s = $res[1];
				}
				else if ($op_code_next->kind == \Bayrell\Lang\OpCodes\OpIdentifier::KIND_CLASSREF)
				{
					if ($op_code_next->value == "static")
					{
						$s = "static";
						$prev_kind = "static";
					}
					else if ($op_code_next->value == "self")
					{
						$prev_kind = "static";
						$s = static::getModuleName($__ctx, $t, $t->current_class_full_name);
					}
					else if ($op_code_next->value == "this")
					{
						$prev_kind = "var";
						$s = "$this";
					}
				}
				else if ($op_code_next->kind == \Bayrell\Lang\OpCodes\OpIdentifier::KIND_CONTEXT && $op_code_next->value == "@")
				{
					$prev_kind = "var";
					$s = "$__ctx";
				}
				else if ($op_code_next->kind == \Bayrell\Lang\OpCodes\OpIdentifier::KIND_CONTEXT && $op_code_next->value == "_")
				{
					$prev_kind = "var";
					$s = "$__ctx->translate";
				}
				else
				{
					$prev_kind = "var";
					$s = "$" . \Runtime\rtl::toStr($op_code_next->value);
				}
			}
			$attrs_sz = $attrs->count($__ctx);
			for ($i = 0;$i < $attrs->count($__ctx);$i++)
			{
				$attr = $attrs->item($__ctx, $i);
				$next_attr = $attrs->get($__ctx, $i + 1, null);
				if ($attr->kind == \Bayrell\Lang\OpCodes\OpAttr::KIND_ATTR)
				{
					$s .= \Runtime\rtl::toStr("->" . \Runtime\rtl::toStr($attr->value->value));
				}
				else if ($attr->kind == \Bayrell\Lang\OpCodes\OpAttr::KIND_STATIC)
				{
					if ($prev_kind == "static")
					{
						$attr_val = $attr->value->value;
						if ($i == $attrs_sz - 1 && $next_op_code instanceof \Bayrell\Lang\OpCodes\OpCall)
						{
							$s .= \Runtime\rtl::toStr("::" . \Runtime\rtl::toStr($attr_val));
						}
						else if (\Runtime\rs::strtoupper($__ctx, $attr_val) == $attr_val)
						{
							$s .= \Runtime\rtl::toStr("::" . \Runtime\rtl::toStr($attr_val));
						}
						else
						{
							$s .= \Runtime\rtl::toStr("::$" . \Runtime\rtl::toStr($attr_val));
						}
					}
					else
					{
						$s .= \Runtime\rtl::toStr("->staticMethod(" . \Runtime\rtl::toStr(static::toString($__ctx, $attr->value->value)) . \Runtime\rtl::toStr(")"));
					}
					$prev_kind = "static";
				}
				else if ($attr->kind == \Bayrell\Lang\OpCodes\OpAttr::KIND_DYNAMIC)
				{
					$res = static::Expression($__ctx, $t, $attr->value);
					$t = $res[0];
					$s .= \Runtime\rtl::toStr("[" . \Runtime\rtl::toStr($res[1]) . \Runtime\rtl::toStr("]"));
				}
				else if ($attr->kind == \Bayrell\Lang\OpCodes\OpAttr::KIND_PIPE)
				{
					$s .= \Runtime\rtl::toStr("->" . \Runtime\rtl::toStr($attr->value->value));
				}
			}
			return \Runtime\Collection::from([$t,$s]);
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpCall)
		{
			return static::OpCall($__ctx, $t, $op_code);
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpPipe)
		{
			return static::OpPipe($__ctx, $t, $op_code);
		}
		return \Runtime\Collection::from([$t,""]);
	}
	/**
	 * OpInc
	 */
	static function OpInc($__ctx, $t, $op_code)
	{
		$content = "";
		$res = static::Expression($__ctx, $t, $op_code->value);
		$t = $res[0];
		$s = $res[1];
		if ($op_code->kind == \Bayrell\Lang\OpCodes\OpInc::KIND_PRE_INC)
		{
			$content = "++$" . \Runtime\rtl::toStr($s);
		}
		else if ($op_code->kind == \Bayrell\Lang\OpCodes\OpInc::KIND_PRE_DEC)
		{
			$content = "--$" . \Runtime\rtl::toStr($s);
		}
		else if ($op_code->kind == \Bayrell\Lang\OpCodes\OpInc::KIND_POST_INC)
		{
			$content = "$" . \Runtime\rtl::toStr($s) . \Runtime\rtl::toStr("++");
		}
		else if ($op_code->kind == \Bayrell\Lang\OpCodes\OpInc::KIND_POST_DEC)
		{
			$content = "$" . \Runtime\rtl::toStr($s) . \Runtime\rtl::toStr("--");
		}
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpMath
	 */
	static function OpMath($__ctx, $t, $op_code)
	{
		$res = static::Expression($__ctx, $t, $op_code->value1);
		$t = $res[0];
		$opcode_level1 = $res[0]->opcode_level;
		$s1 = $res[1];
		$op = "";
		$op_math = $op_code->math;
		$opcode_level = 0;
		if ($op_code->math == "!")
		{
			$opcode_level = 16;
			$op = "!";
		}
		if ($op_code->math == ">>")
		{
			$opcode_level = 12;
			$op = ">>";
		}
		if ($op_code->math == "<<")
		{
			$opcode_level = 12;
			$op = "<<";
		}
		if ($op_code->math == "&")
		{
			$opcode_level = 9;
			$op = "&";
		}
		if ($op_code->math == "xor")
		{
			$opcode_level = 8;
			$op = "^";
		}
		if ($op_code->math == "|")
		{
			$opcode_level = 7;
			$op = "|";
		}
		if ($op_code->math == "*")
		{
			$opcode_level = 14;
			$op = "*";
		}
		if ($op_code->math == "/")
		{
			$opcode_level = 14;
			$op = "/";
		}
		if ($op_code->math == "%")
		{
			$opcode_level = 14;
			$op = "%";
		}
		if ($op_code->math == "div")
		{
			$opcode_level = 14;
			$op = "div";
		}
		if ($op_code->math == "mod")
		{
			$opcode_level = 14;
			$op = "mod";
		}
		if ($op_code->math == "+")
		{
			$opcode_level = 13;
			$op = "+";
		}
		if ($op_code->math == "-")
		{
			$opcode_level = 13;
			$op = "-";
		}
		if ($op_code->math == "~")
		{
			$opcode_level = 13;
			$op = "+";
		}
		if ($op_code->math == "!")
		{
			$opcode_level = 13;
			$op = "!";
		}
		if ($op_code->math == "===")
		{
			$opcode_level = 10;
			$op = "===";
		}
		if ($op_code->math == "!==")
		{
			$opcode_level = 10;
			$op = "!==";
		}
		if ($op_code->math == "==")
		{
			$opcode_level = 10;
			$op = "==";
		}
		if ($op_code->math == "!=")
		{
			$opcode_level = 10;
			$op = "!=";
		}
		if ($op_code->math == ">=")
		{
			$opcode_level = 10;
			$op = ">=";
		}
		if ($op_code->math == "<=")
		{
			$opcode_level = 10;
			$op = "<=";
		}
		if ($op_code->math == ">")
		{
			$opcode_level = 10;
			$op = ">";
		}
		if ($op_code->math == "<")
		{
			$opcode_level = 10;
			$op = "<";
		}
		if ($op_code->math == "is")
		{
			$opcode_level = 10;
			$op = "instanceof";
		}
		if ($op_code->math == "instanceof")
		{
			$opcode_level = 10;
			$op = "instanceof";
		}
		if ($op_code->math == "implements")
		{
			$opcode_level = 10;
			$op = "implements";
		}
		if ($op_code->math == "not")
		{
			$opcode_level = 16;
			$op = "!";
		}
		if ($op_code->math == "and")
		{
			$opcode_level = 6;
			$op = "&&";
		}
		if ($op_code->math == "&&")
		{
			$opcode_level = 6;
			$op = "&&";
		}
		if ($op_code->math == "or")
		{
			$opcode_level = 5;
			$op = "||";
		}
		if ($op_code->math == "||")
		{
			$opcode_level = 5;
			$op = "||";
		}
		$content = "";
		if ($op_code->math == "!" || $op_code->math == "not")
		{
			$content = $op . \Runtime\rtl::toStr($t->o($__ctx, $s1, $opcode_level1, $opcode_level));
		}
		else
		{
			$res = static::Expression($__ctx, $t, $op_code->value2);
			$t = $res[0];
			$opcode_level2 = $res[0]->opcode_level;
			$s2 = $res[1];
			$op1 = $t->o($__ctx, $s1, $opcode_level1, $opcode_level);
			$op2 = $t->o($__ctx, $s2, $opcode_level2, $opcode_level);
			if ($op_math == "~")
			{
				$content = $op1 . \Runtime\rtl::toStr(" . ") . \Runtime\rtl::toStr(static::rtlToStr($__ctx, $t, $op2));
			}
			else if ($op_math == "implements")
			{
				$content = $op1 . \Runtime\rtl::toStr(" instanceof ") . \Runtime\rtl::toStr($op2);
			}
			else
			{
				$content = $op1 . \Runtime\rtl::toStr(" ") . \Runtime\rtl::toStr($op) . \Runtime\rtl::toStr(" ") . \Runtime\rtl::toStr($op2);
			}
		}
		$t = $t->copy($__ctx, ["opcode_level"=>$opcode_level]);
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpNew
	 */
	static function OpNew($__ctx, $t, $op_code)
	{
		$content = "new ";
		$res = static::OpTypeIdentifier($__ctx, $t, $op_code->value);
		$t = $res[0];
		$content .= \Runtime\rtl::toStr($res[1]);
		$flag = false;
		$content .= \Runtime\rtl::toStr("(");
		if ($t->current_function == null || $t->current_function->is_context)
		{
			$content .= \Runtime\rtl::toStr("$__ctx");
			$flag = true;
		}
		for ($i = 0;$i < $op_code->args->count($__ctx);$i++)
		{
			$item = $op_code->args->item($__ctx, $i);
			$res = $t->expression->staticMethod("Expression")($__ctx, $t, $item);
			$t = $res[0];
			$s = $res[1];
			$content .= \Runtime\rtl::toStr((($flag) ? ", " : "") . \Runtime\rtl::toStr($s));
			$flag = true;
		}
		$content .= \Runtime\rtl::toStr(")");
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpCall
	 */
	static function OpCall($__ctx, $t, $op_code)
	{
		$s = "";
		$flag = false;
		$res = static::Dynamic($__ctx, $t, $op_code->obj, $op_code);
		$t = $res[0];
		$s = $res[1];
		if ($s == "parent")
		{
			$f_name = $t->current_function->name;
			if ($f_name == "constructor")
			{
				$f_name = "__construct";
			}
			$s = "parent::" . \Runtime\rtl::toStr($f_name) . \Runtime\rtl::toStr("(");
		}
		else
		{
			$s .= \Runtime\rtl::toStr("(");
		}
		$content = $s;
		if (($t->current_function == null || $t->current_function->is_context) && $op_code->is_context)
		{
			$content .= \Runtime\rtl::toStr((($flag) ? ", " : "") . \Runtime\rtl::toStr("$__ctx"));
			$flag = true;
		}
		for ($i = 0;$i < $op_code->args->count($__ctx);$i++)
		{
			$item = $op_code->args->item($__ctx, $i);
			$res = static::Expression($__ctx, $t, $item);
			$t = $res[0];
			$s = $res[1];
			$content .= \Runtime\rtl::toStr((($flag) ? ", " : "") . \Runtime\rtl::toStr($s));
			$flag = true;
		}
		$content .= \Runtime\rtl::toStr(")");
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpPipe
	 */
	static function OpPipe($__ctx, $t, $op_code)
	{
		$content = "";
		$var_name = "";
		$res = static::Expression($__ctx, $t, $op_code->obj, $op_code);
		$t = $res[0];
		if ($op_code->obj instanceof \Bayrell\Lang\OpCodes\OpIdentifier)
		{
			$var_name = $res[1];
		}
		else
		{
			$res = $t->staticMethod("addSaveOpCode")($__ctx, $t, \Runtime\Dict::from(["op_code"=>$op_code->obj,"var_content"=>$res[1]]));
			$t = $res[0];
			$var_name = $res[1];
		}
		if ($op_code->kind == \Bayrell\Lang\OpCodes\OpPipe::KIND_METHOD)
		{
			$content = "(" . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr("->staticMethod(") . \Runtime\rtl::toStr(static::toString($__ctx, $op_code->method_name->value)) . \Runtime\rtl::toStr("))");
		}
		else
		{
			$res = static::OpTypeIdentifier($__ctx, $t, $op_code->class_name);
			$t = $res[0];
			$content = $res[1] . \Runtime\rtl::toStr("::") . \Runtime\rtl::toStr($op_code->method_name->value);
		}
		$flag = false;
		$content .= \Runtime\rtl::toStr("(");
		if ($t->current_function->is_context && $op_code->is_context)
		{
			$content .= \Runtime\rtl::toStr("$__ctx");
			$flag = true;
		}
		for ($i = 0;$i < $op_code->args->count($__ctx);$i++)
		{
			$item = $op_code->args->item($__ctx, $i);
			$res = static::Expression($__ctx, $t, $item);
			$t = $res[0];
			$s1 = $res[1];
			$content .= \Runtime\rtl::toStr((($flag) ? ", " : "") . \Runtime\rtl::toStr($s1));
			$flag = true;
		}
		$content .= \Runtime\rtl::toStr((($flag) ? ", " : "") . \Runtime\rtl::toStr($var_name));
		$content .= \Runtime\rtl::toStr(")");
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpClassOf
	 */
	static function OpClassOf($__ctx, $t, $op_code)
	{
		$names = static::findModuleNames($__ctx, $t, $op_code->entity_name->names);
		$s = \Runtime\rs::join($__ctx, ".", $names);
		return \Runtime\Collection::from([$t,static::toString($__ctx, $s)]);
	}
	/**
	 * OpTernary
	 */
	static function OpTernary($__ctx, $t, $op_code)
	{
		$content = "";
		$t = $t->copy($__ctx, ["opcode_level"=>100]);
		$res = static::Expression($__ctx, $t, $op_code->condition);
		$t = $res[0];
		$condition = $res[1];
		$res = static::Expression($__ctx, $t, $op_code->if_true);
		$t = $res[0];
		$if_true = $res[1];
		$res = static::Expression($__ctx, $t, $op_code->if_false);
		$t = $res[0];
		$if_false = $res[1];
		$content .= \Runtime\rtl::toStr("(" . \Runtime\rtl::toStr($condition) . \Runtime\rtl::toStr(") ? ") . \Runtime\rtl::toStr($if_true) . \Runtime\rtl::toStr(" : ") . \Runtime\rtl::toStr($if_false));
		$t = $t->copy($__ctx, ["opcode_level"=>11]);
		/* OpTernary */
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpTypeConvert
	 */
	static function OpTypeConvert($__ctx, $t, $op_code)
	{
		$content = "";
		$res = static::Expression($__ctx, $t, $op_code->value);
		$t = $res[0];
		$value = $res[1];
		$content = "\\Runtime\\rtl::to(" . \Runtime\rtl::toStr($value) . \Runtime\rtl::toStr(", ") . \Runtime\rtl::toStr(static::toPattern($__ctx, $t, $op_code->pattern)) . \Runtime\rtl::toStr(")");
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpTernary
	 */
	static function OpDeclareFunction($__ctx, $t, $op_code)
	{
		$content = "";
		/* Set function name */
		$save_f = $t->current_function;
		$t = $t->copy($__ctx, ["current_function"=>$op_code]);
		$res = $t->operator->staticMethod("OpDeclareFunctionArgs")($__ctx, $t, $op_code);
		$args = $res[1];
		$content .= \Runtime\rtl::toStr("function (" . \Runtime\rtl::toStr($args) . \Runtime\rtl::toStr(")"));
		if ($op_code->vars != null && $op_code->vars->count($__ctx) > 0)
		{
			$vars = $op_code->vars->map($__ctx, function ($__ctx, $s)
			{
				return "&$" . \Runtime\rtl::toStr($s);
			});
			$content .= \Runtime\rtl::toStr(" use (" . \Runtime\rtl::toStr(\Runtime\rs::join($__ctx, ",", $vars)) . \Runtime\rtl::toStr(")"));
		}
		$res = $t->operator->staticMethod("OpDeclareFunctionBody")($__ctx, $t, $op_code);
		$content .= \Runtime\rtl::toStr($res[1]);
		/* Restore function */
		$t = $t->copy($__ctx, ["current_function"=>$save_f]);
		/* OpTernary */
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * Expression
	 */
	static function Expression($__ctx, $t, $op_code)
	{
		$content = "";
		$t = $t->copy($__ctx, ["opcode_level"=>100]);
		if ($op_code instanceof \Bayrell\Lang\OpCodes\OpIdentifier)
		{
			$res = static::OpIdentifier($__ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpTypeIdentifier)
		{
			$res = static::OpTypeIdentifier($__ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpNumber)
		{
			$res = static::OpNumber($__ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpString)
		{
			$res = static::OpString($__ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpCollection)
		{
			$res = static::OpCollection($__ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpDict)
		{
			$res = static::OpDict($__ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpInc)
		{
			$t = $t->copy($__ctx, ["opcode_level"=>16]);
			$res = static::OpInc($__ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpMath)
		{
			$res = static::OpMath($__ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpNew)
		{
			$res = static::OpNew($__ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpAttr || $op_code instanceof \Bayrell\Lang\OpCodes\OpPipe)
		{
			$res = static::Dynamic($__ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpCall)
		{
			$res = static::OpCall($__ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpClassOf)
		{
			$res = static::OpClassOf($__ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpTernary)
		{
			$res = static::OpTernary($__ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpTypeConvert)
		{
			$res = static::OpTypeConvert($__ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpDeclareFunction)
		{
			$res = static::OpDeclareFunction($__ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpHtmlItems)
		{
			$res = $t->html->staticMethod("OpHtmlItems")($__ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		return \Runtime\Collection::from([$t,$content]);
	}
	/* ======================= Class Init Functions ======================= */
	function getClassName()
	{
		return "Bayrell.Lang.LangPHP.TranslatorPHPExpression";
	}
	static function getCurrentNamespace()
	{
		return "Bayrell.Lang.LangPHP";
	}
	static function getCurrentClassName()
	{
		return "Bayrell.Lang.LangPHP.TranslatorPHPExpression";
	}
	static function getParentClassName()
	{
		return "";
	}
	static function getClassInfo($__ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($__ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.LangPHP.TranslatorPHPExpression",
			"name"=>"Bayrell.Lang.LangPHP.TranslatorPHPExpression",
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
	}
	static function getFieldsList($__ctx,$f)
	{
		$a = [];
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