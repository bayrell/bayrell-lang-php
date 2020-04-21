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
namespace Bayrell\Lang\LangES6;
class TranslatorES6Expression extends \Runtime\CoreStruct
{
	/**
	 * Returns string
	 */
	static function toString($ctx, $s)
	{
		$s = \Runtime\re::replace($ctx, "\\\\", "\\\\", $s);
		$s = \Runtime\re::replace($ctx, "\"", "\\\"", $s);
		$s = \Runtime\re::replace($ctx, "\n", "\\n", $s);
		$s = \Runtime\re::replace($ctx, "\r", "\\r", $s);
		$s = \Runtime\re::replace($ctx, "\t", "\\t", $s);
		return "\"" . \Runtime\rtl::toStr($s) . \Runtime\rtl::toStr("\"");
	}
	/**
	 * To pattern
	 */
	static function toPattern($ctx, $t, $pattern)
	{
		$names = static::findModuleNames($ctx, $t, $pattern->entity_name->names);
		$e = \Runtime\rs::join($ctx, ".", $names);
		$a = ($pattern->template != null) ? $pattern->template->map($ctx, function ($ctx, $pattern) use (&$t)
		{
			return static::toPattern($ctx, $t, $pattern);
		}) : null;
		$b = ($a != null) ? ",\"t\":[" . \Runtime\rtl::toStr(\Runtime\rs::join($ctx, ",", $a)) . \Runtime\rtl::toStr("]") : "";
		return "{\"e\":" . \Runtime\rtl::toStr(static::toString($ctx, $e)) . \Runtime\rtl::toStr($b) . \Runtime\rtl::toStr("}");
	}
	/**
	 * Returns string
	 */
	static function rtlToStr($ctx, $t, $s)
	{
		$module_name = static::findModuleName($ctx, $t, "rtl");
		return $module_name . \Runtime\rtl::toStr(".toStr(") . \Runtime\rtl::toStr($s) . \Runtime\rtl::toStr(")");
	}
	/**
	 * Find module name
	 */
	static function findModuleName($ctx, $t, $module_name)
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
			return "";
		}
		else if ($t->modules->has($ctx, $module_name))
		{
			return $t->modules->item($ctx, $module_name);
		}
		return $module_name;
	}
	/**
	 * Returns module name
	 */
	static function findModuleNames($ctx, $t, $names)
	{
		if ($names->count($ctx) > 0)
		{
			$module_name = $names->first($ctx);
			$module_name = static::findModuleName($ctx, $t, $module_name);
			if ($module_name != "")
			{
				$names = $names->setIm($ctx, 0, $module_name);
			}
		}
		return $names;
	}
	/**
	 * Use module name
	 */
	static function useModuleName($ctx, $t, $module_name)
	{
		$module_name = static::findModuleName($ctx, $t, $module_name);
		return $module_name;
	}
	/**
	 * OpTypeIdentifier
	 */
	static function OpTypeIdentifier($ctx, $t, $op_code)
	{
		$names = static::findModuleNames($ctx, $t, $op_code->entity_name->names);
		$s = \Runtime\rs::join($ctx, ".", $names);
		return \Runtime\Collection::from([$t,$s]);
	}
	/**
	 * OpIdentifier
	 */
	static function OpIdentifier($ctx, $t, $op_code)
	{
		if ($op_code->value == "@")
		{
			return \Runtime\Collection::from([$t,"ctx"]);
		}
		if ($op_code->value == "_")
		{
			return \Runtime\Collection::from([$t,"ctx.constructor.translate"]);
		}
		if ($op_code->value == "log")
		{
			return \Runtime\Collection::from([$t,"console.log"]);
		}
		if ($t->modules->has($ctx, $op_code->value) || $op_code->kind == \Bayrell\Lang\OpCodes\OpIdentifier::KIND_SYS_TYPE)
		{
			$module_name = $op_code->value;
			$new_module_name = static::findModuleName($ctx, $t, $module_name);
			return \Runtime\Collection::from([$t,$new_module_name]);
		}
		$content = $op_code->value;
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpNumber
	 */
	static function OpNumber($ctx, $t, $op_code)
	{
		$content = $op_code->value;
		if ($op_code->negative)
		{
			$content = "-" . \Runtime\rtl::toStr($content);
			$t = $t->copy($ctx, ["opcode_level"=>15]);
		}
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpString
	 */
	static function OpString($ctx, $t, $op_code)
	{
		return \Runtime\Collection::from([$t,static::toString($ctx, $op_code->value)]);
	}
	/**
	 * OpCollection
	 */
	static function OpCollection($ctx, $t, $op_code)
	{
		$content = "";
		$values = $op_code->values->map($ctx, function ($ctx, $op_code) use (&$t)
		{
			$res = static::Expression($ctx, $t, $op_code);
			$t = $res[0];
			$s = $res[1];
			return $s;
		});
		$values = $values->filter($ctx, function ($ctx, $s)
		{
			return $s != "";
		});
		$module_name = static::findModuleName($ctx, $t, "Collection");
		$content = $module_name . \Runtime\rtl::toStr(".from([") . \Runtime\rtl::toStr(\Runtime\rs::join($ctx, ",", $values)) . \Runtime\rtl::toStr("])");
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpDict
	 */
	static function OpDict($ctx, $t, $op_code)
	{
		$content = "";
		$values = $op_code->values->transition($ctx, function ($ctx, $op_code, $key) use (&$t)
		{
			$res = static::Expression($ctx, $t, $op_code);
			$t = $res[0];
			$s = $res[1];
			return static::toString($ctx, $key) . \Runtime\rtl::toStr(":") . \Runtime\rtl::toStr($s);
		});
		$values = $values->filter($ctx, function ($ctx, $s)
		{
			return $s != "";
		});
		$module_name = static::findModuleName($ctx, $t, "Dict");
		$content = $module_name . \Runtime\rtl::toStr(".from({") . \Runtime\rtl::toStr(\Runtime\rs::join($ctx, ",", $values)) . \Runtime\rtl::toStr("})");
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * Dynamic
	 */
	static function Dynamic($ctx, $t, $op_code)
	{
		if ($op_code instanceof \Bayrell\Lang\OpCodes\OpIdentifier)
		{
			return static::OpIdentifier($ctx, $t, $op_code);
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpAttr)
		{
			$attrs = new \Runtime\Vector($ctx);
			$op_code_item = $op_code;
			$op_code_first = $op_code;
			$prev_kind = "";
			$s = "";
			while ($op_code_first instanceof \Bayrell\Lang\OpCodes\OpAttr)
			{
				$attrs->push($ctx, $op_code_first);
				$op_code_item = $op_code_first;
				$op_code_first = $op_code_first->obj;
			}
			$attrs = $attrs->reverseIm($ctx);
			if ($op_code_first instanceof \Bayrell\Lang\OpCodes\OpCall)
			{
				$prev_kind = "var";
				$res = static::OpCall($ctx, $t, $op_code_first);
				$t = $res[0];
				$s = $res[1];
			}
			else if ($op_code_first instanceof \Bayrell\Lang\OpCodes\OpNew)
			{
				$prev_kind = "var";
				$res = static::OpNew($ctx, $t, $op_code_first);
				$t = $res[0];
				$s = "(" . \Runtime\rtl::toStr($res[1]) . \Runtime\rtl::toStr(")");
			}
			else if ($op_code_first instanceof \Bayrell\Lang\OpCodes\OpIdentifier)
			{
				if ($op_code_first->kind == \Bayrell\Lang\OpCodes\OpIdentifier::KIND_CLASSREF)
				{
					if ($op_code_first->value == "static")
					{
						$s = "this" . \Runtime\rtl::toStr(((!$t->is_static_function) ? ".constructor" : ""));
						$prev_kind = "static";
					}
					else if ($op_code_first->value == "parent")
					{
						$s = $t->current_class_extends_name;
						$prev_kind = "static";
					}
					else if ($op_code_first->value == "self")
					{
						$prev_kind = "static";
						$s = $t->current_class_full_name;
					}
					else if ($op_code_first->value == "this")
					{
						$prev_kind = "var";
						$s = "this";
					}
				}
				else
				{
					$res = static::OpIdentifier($ctx, $t, $op_code_first);
					$t = $res[0];
					$s = $res[1];
					$prev_kind = "var";
					if ($t->modules->has($ctx, $op_code_first->value) || $op_code_first->kind == \Bayrell\Lang\OpCodes\OpIdentifier::KIND_SYS_TYPE)
					{
						$prev_kind = "static";
					}
				}
			}
			for ($i = 0;$i < $attrs->count($ctx);$i++)
			{
				$attr = $attrs->item($ctx, $i);
				if ($attr->kind == \Bayrell\Lang\OpCodes\OpAttr::KIND_ATTR)
				{
					$s .= \Runtime\rtl::toStr("." . \Runtime\rtl::toStr($attr->value->value));
				}
				else if ($attr->kind == \Bayrell\Lang\OpCodes\OpAttr::KIND_STATIC)
				{
					if ($prev_kind == "var")
					{
						$s .= \Runtime\rtl::toStr(".constructor." . \Runtime\rtl::toStr($attr->value->value));
					}
					else
					{
						$s .= \Runtime\rtl::toStr("." . \Runtime\rtl::toStr($attr->value->value));
					}
					$prev_kind = "static";
				}
				else if ($attr->kind == \Bayrell\Lang\OpCodes\OpAttr::KIND_DYNAMIC)
				{
					$res = static::Expression($ctx, $t, $attr->value);
					$t = $res[0];
					$s .= \Runtime\rtl::toStr("[" . \Runtime\rtl::toStr($res[1]) . \Runtime\rtl::toStr("]"));
				}
			}
			return \Runtime\Collection::from([$t,$s]);
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpCall)
		{
			return static::OpCall($ctx, $t, $op_code);
		}
		return \Runtime\Collection::from([$t,""]);
	}
	/**
	 * OpInc
	 */
	static function OpInc($ctx, $t, $op_code)
	{
		$content = "";
		$res = static::Expression($ctx, $t, $op_code->value);
		$t = $res[0];
		$s = $res[1];
		if ($op_code->kind == \Bayrell\Lang\OpCodes\OpInc::KIND_PRE_INC)
		{
			$content = "++" . \Runtime\rtl::toStr($s);
		}
		else if ($op_code->kind == \Bayrell\Lang\OpCodes\OpInc::KIND_PRE_DEC)
		{
			$content = "--" . \Runtime\rtl::toStr($s);
		}
		else if ($op_code->kind == \Bayrell\Lang\OpCodes\OpInc::KIND_POST_INC)
		{
			$content = $s . \Runtime\rtl::toStr("++");
		}
		else if ($op_code->kind == \Bayrell\Lang\OpCodes\OpInc::KIND_POST_DEC)
		{
			$content = $s . \Runtime\rtl::toStr("--");
		}
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpMath
	 */
	static function OpMath($ctx, $t, $op_code)
	{
		$res = static::Expression($ctx, $t, $op_code->value1);
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
			$content = $op . \Runtime\rtl::toStr($t->o($ctx, $s1, $opcode_level1, $opcode_level));
		}
		else
		{
			$res = static::Expression($ctx, $t, $op_code->value2);
			$t = $res[0];
			$opcode_level2 = $res[0]->opcode_level;
			$s2 = $res[1];
			$op1 = $t->o($ctx, $s1, $opcode_level1, $opcode_level);
			$op2 = $t->o($ctx, $s2, $opcode_level2, $opcode_level);
			if ($op_math == "~")
			{
				$content = $op1 . \Runtime\rtl::toStr(" ") . \Runtime\rtl::toStr($op) . \Runtime\rtl::toStr(" ") . \Runtime\rtl::toStr(static::rtlToStr($ctx, $t, $op2));
			}
			else if ($op_math == "implements")
			{
				$rtl_name = static::findModuleName($ctx, $t, "rtl");
				$content = $rtl_name . \Runtime\rtl::toStr(".is_implements(") . \Runtime\rtl::toStr($op1) . \Runtime\rtl::toStr(", ") . \Runtime\rtl::toStr($op2) . \Runtime\rtl::toStr(")");
			}
			else
			{
				$content = $op1 . \Runtime\rtl::toStr(" ") . \Runtime\rtl::toStr($op) . \Runtime\rtl::toStr(" ") . \Runtime\rtl::toStr($op2);
			}
		}
		$t = $t->copy($ctx, ["opcode_level"=>$opcode_level]);
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpNew
	 */
	static function OpNew($ctx, $t, $op_code)
	{
		$content = "new ";
		$res = static::OpTypeIdentifier($ctx, $t, $op_code->value);
		$t = $res[0];
		$content .= \Runtime\rtl::toStr($res[1]);
		$flag = false;
		$content .= \Runtime\rtl::toStr("(");
		if ($t->current_function == null || $t->current_function->is_context)
		{
			$content .= \Runtime\rtl::toStr("ctx");
			$flag = true;
		}
		for ($i = 0;$i < $op_code->args->count($ctx);$i++)
		{
			$item = $op_code->args->item($ctx, $i);
			$res = $t->expression::Expression($ctx, $t, $item);
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
	static function OpCall($ctx, $t, $op_code, $is_expression=true)
	{
		if ($t->current_function->isFlag($ctx, "async") && $op_code->is_await)
		{
			return $t->async_await::OpCall($ctx, $t, $op_code, $is_expression);
		}
		$s = "";
		$flag = false;
		$res = $t->expression::Dynamic($ctx, $t, $op_code->obj);
		$t = $res[0];
		$s = $res[1];
		if ($s == "parent")
		{
			$s = static::useModuleName($ctx, $t, $t->current_class_extends_name);
			if ($t->current_function->name != "constructor")
			{
				if ($t->current_function->isStatic($ctx))
				{
					$s .= \Runtime\rtl::toStr("." . \Runtime\rtl::toStr($t->current_function->name));
				}
				else
				{
					$s .= \Runtime\rtl::toStr(".prototype." . \Runtime\rtl::toStr($t->current_function->name));
				}
			}
			$s .= \Runtime\rtl::toStr(".call(this");
			$flag = true;
		}
		else
		{
			$s .= \Runtime\rtl::toStr("(");
		}
		$content = $s;
		if ($op_code->obj instanceof \Bayrell\Lang\OpCodes\OpIdentifier && $op_code->obj->value == "_")
		{
			$content .= \Runtime\rtl::toStr((($flag) ? ", " : "") . \Runtime\rtl::toStr("ctx, ctx"));
			$flag = true;
		}
		else if ($t->current_function->is_context && $op_code->is_context)
		{
			$content .= \Runtime\rtl::toStr((($flag) ? ", " : "") . \Runtime\rtl::toStr("ctx"));
			$flag = true;
		}
		for ($i = 0;$i < $op_code->args->count($ctx);$i++)
		{
			$item = $op_code->args->item($ctx, $i);
			$res = $t->expression::Expression($ctx, $t, $item);
			$t = $res[0];
			$s = $res[1];
			$content .= \Runtime\rtl::toStr((($flag) ? ", " : "") . \Runtime\rtl::toStr($s));
			$flag = true;
		}
		$content .= \Runtime\rtl::toStr(")");
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpClassOf
	 */
	static function OpClassOf($ctx, $t, $op_code)
	{
		$names = static::findModuleNames($ctx, $t, $op_code->entity_name->names);
		$s = \Runtime\rs::join($ctx, ".", $names);
		return \Runtime\Collection::from([$t,static::toString($ctx, $s)]);
	}
	/**
	 * OpTernary
	 */
	static function OpTernary($ctx, $t, $op_code)
	{
		$content = "";
		$t = $t->copy($ctx, ["opcode_level"=>100]);
		$res = $t->expression::Expression($ctx, $t, $op_code->condition);
		$t = $res[0];
		$condition = $res[1];
		$res = $t->expression::Expression($ctx, $t, $op_code->if_true);
		$t = $res[0];
		$if_true = $res[1];
		$res = $t->expression::Expression($ctx, $t, $op_code->if_false);
		$t = $res[0];
		$if_false = $res[1];
		$content .= \Runtime\rtl::toStr("(" . \Runtime\rtl::toStr($condition) . \Runtime\rtl::toStr(") ? ") . \Runtime\rtl::toStr($if_true) . \Runtime\rtl::toStr(" : ") . \Runtime\rtl::toStr($if_false));
		$t = $t->copy($ctx, ["opcode_level"=>11]);
		/* OpTernary */
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpPipe
	 */
	static function OpPipe($ctx, $t, $op_code)
	{
		$content = "";
		$var_name = "";
		$value = "";
		$res = $t::incSaveOpCode($ctx, $t);
		$t = $res[0];
		$var_name = $res[1];
		$items = new \Runtime\Vector($ctx);
		$op_code_item = $op_code;
		while ($op_code_item instanceof \Bayrell\Lang\OpCodes\OpPipe)
		{
			$items->push($ctx, $op_code_item);
			$op_code_item = $op_code_item->obj;
		}
		$items = $items->reverseIm($ctx);
		/* First item */
		$res = $t->expression::Expression($ctx, $t, $op_code_item);
		$t = $res[0];
		$value = $res[1];
		$res = $t::addSaveOpCode($ctx, $t, \Runtime\Dict::from(["content"=>$t->s($ctx, "var " . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr(" = new Runtime.Monad(ctx, ") . \Runtime\rtl::toStr($value) . \Runtime\rtl::toStr(");"))]));
		$t = $res[0];
		/* Output items */
		for ($i = 0;$i < $items->count($ctx);$i++)
		{
			$s1 = "";
			$s2 = "";
			$op_item = $items->item($ctx, $i);
			if ($op_item->kind == \Bayrell\Lang\OpCodes\OpPipe::KIND_ATTR)
			{
				$res = static::Expression($ctx, $t, $op_item->value);
				$t = $res[0];
				$value = $res[1];
				$s1 = $var_name . \Runtime\rtl::toStr(".attr(ctx, ") . \Runtime\rtl::toStr($value) . \Runtime\rtl::toStr(")");
			}
			else if ($op_item->kind == \Bayrell\Lang\OpCodes\OpPipe::KIND_METHOD)
			{
				$value_attrs = "";
				$value = static::toString($ctx, $op_item->value->obj->value);
				if ($op_item->value->args != null)
				{
					$flag = false;
					/*if ((t.current_function == null or t.current_function.is_context) and op_item.value.is_context)
					{
						value_attrs ~= "ctx";
						flag = true;
					}*/
					for ($i = 0;$i < $op_item->value->args->count($ctx);$i++)
					{
						$item = $op_item->value->args->item($ctx, $i);
						$res = static::Expression($ctx, $t, $item);
						$t = $res[0];
						$s = $res[1];
						$value_attrs .= \Runtime\rtl::toStr((($flag) ? ", " : "") . \Runtime\rtl::toStr($s));
						$flag = true;
					}
				}
				if ($op_item->value->args != null)
				{
					$value_attrs = "Runtime.Collection.from([" . \Runtime\rtl::toStr($value_attrs) . \Runtime\rtl::toStr("])");
				}
				else
				{
					$value_attrs = "null";
				}
				if (!$op_item->is_async)
				{
					$s1 = $var_name . \Runtime\rtl::toStr(".callMethod(ctx, ") . \Runtime\rtl::toStr($value) . \Runtime\rtl::toStr(", ") . \Runtime\rtl::toStr($value_attrs) . \Runtime\rtl::toStr(")");
				}
				else if ($op_item->is_async && $t->current_function->isFlag($ctx, "async"))
				{
					$s2 = $var_name . \Runtime\rtl::toStr(".callMethodAsync(ctx, ") . \Runtime\rtl::toStr($value) . \Runtime\rtl::toStr(", ") . \Runtime\rtl::toStr($value_attrs) . \Runtime\rtl::toStr(")");
				}
			}
			else if ($op_item->kind == \Bayrell\Lang\OpCodes\OpPipe::KIND_CALL)
			{
				$res = static::Dynamic($ctx, $t, $op_item->value);
				$t = $res[0];
				$value = $res[1];
				if (!$op_item->is_async)
				{
					$s1 = $var_name . \Runtime\rtl::toStr(".call(ctx, ") . \Runtime\rtl::toStr($value) . \Runtime\rtl::toStr(")");
				}
				else if ($op_item->is_async && $t->current_function->isFlag($ctx, "async"))
				{
					$s2 = $var_name . \Runtime\rtl::toStr(".callAsync(ctx, ") . \Runtime\rtl::toStr($value) . \Runtime\rtl::toStr(")");
				}
			}
			else if ($op_item->kind == \Bayrell\Lang\OpCodes\OpPipe::KIND_MONAD)
			{
				$res = static::Dynamic($ctx, $t, $op_item->value);
				$t = $res[0];
				$value = $res[1];
				$s1 = $var_name . \Runtime\rtl::toStr(".monad(ctx, ") . \Runtime\rtl::toStr($value) . \Runtime\rtl::toStr(")");
			}
			if ($s1 != "")
			{
				$res = $t::addSaveOpCode($ctx, $t, \Runtime\Dict::from(["content"=>$t->s($ctx, $var_name . \Runtime\rtl::toStr(" = ") . \Runtime\rtl::toStr($s1) . \Runtime\rtl::toStr(";"))]));
				$t = $res[0];
			}
			if ($s2 != "")
			{
				$res = $t->async_await::nextPos($ctx, $t);
				$t = $res[0];
				$next_pos = $res[1];
				$async_t = $t->async_await->async_t;
				$s3 = $t->s($ctx, "return " . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".jump(ctx, ") . \Runtime\rtl::toStr($next_pos) . \Runtime\rtl::toStr(")") . \Runtime\rtl::toStr(".call(ctx, ") . \Runtime\rtl::toStr($s2) . \Runtime\rtl::toStr(",") . \Runtime\rtl::toStr($t->expression::toString($ctx, $var_name)) . \Runtime\rtl::toStr(");"));
				$t = $t->levelDec($ctx);
				$s3 .= \Runtime\rtl::toStr($t->s($ctx, "}"));
				$s3 .= \Runtime\rtl::toStr($t->s($ctx, "else if (" . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".pos(ctx) == ") . \Runtime\rtl::toStr($next_pos) . \Runtime\rtl::toStr(")")));
				$s3 .= \Runtime\rtl::toStr($t->s($ctx, "{"));
				$t = $t->levelInc($ctx);
				$s3 .= \Runtime\rtl::toStr($t->s($ctx, "var " . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr(" = ") . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".getVar(ctx, ") . \Runtime\rtl::toStr($t->expression::toString($ctx, $var_name)) . \Runtime\rtl::toStr(");")));
				$res = $t::addSaveOpCode($ctx, $t, \Runtime\Dict::from(["content"=>$s3]));
				$t = $res[0];
			}
		}
		return \Runtime\Collection::from([$t,$var_name . \Runtime\rtl::toStr(".value(ctx)")]);
	}
	/**
	 * OpTypeConvert
	 */
	static function OpTypeConvert($ctx, $t, $op_code)
	{
		$content = "";
		$res = static::Expression($ctx, $t, $op_code->value);
		$t = $res[0];
		$value = $res[1];
		$content = static::useModuleName($ctx, $t, "rtl") . \Runtime\rtl::toStr(".to(") . \Runtime\rtl::toStr($value) . \Runtime\rtl::toStr(", ") . \Runtime\rtl::toStr(static::toPattern($ctx, $t, $op_code->pattern)) . \Runtime\rtl::toStr(")");
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpTernary
	 */
	static function OpDeclareFunction($ctx, $t, $op_code)
	{
		$content = "";
		/* Set function name */
		$save_f = $t->current_function;
		$t = $t->copy($ctx, ["current_function"=>$op_code]);
		$res = $t->operator::OpDeclareFunctionArgs($ctx, $t, $op_code);
		$args = $res[1];
		$content .= \Runtime\rtl::toStr("(" . \Runtime\rtl::toStr($args) . \Runtime\rtl::toStr(") => "));
		$res = $t->operator::OpDeclareFunctionBody($ctx, $t, $op_code);
		$content .= \Runtime\rtl::toStr($res[1]);
		/* Restore function */
		$t = $t->copy($ctx, ["current_function"=>$save_f]);
		/* OpTernary */
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * Expression
	 */
	static function Expression($ctx, $t, $op_code)
	{
		$content = "";
		$t = $t->copy($ctx, ["opcode_level"=>100]);
		if ($op_code instanceof \Bayrell\Lang\OpCodes\OpIdentifier)
		{
			$res = static::OpIdentifier($ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpTypeIdentifier)
		{
			$res = static::OpTypeIdentifier($ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpNumber)
		{
			$res = static::OpNumber($ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpString)
		{
			$res = static::OpString($ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpCollection)
		{
			$res = static::OpCollection($ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpDict)
		{
			$res = static::OpDict($ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpInc)
		{
			$t = $t->copy($ctx, ["opcode_level"=>16]);
			$res = static::OpInc($ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpMath)
		{
			$res = static::OpMath($ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpNew)
		{
			$res = static::OpNew($ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpAttr)
		{
			$res = static::Dynamic($ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpCall)
		{
			$res = static::OpCall($ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpClassOf)
		{
			$res = static::OpClassOf($ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpPipe)
		{
			$res = static::OpPipe($ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpTernary)
		{
			$res = static::OpTernary($ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpTypeConvert)
		{
			$res = static::OpTypeConvert($ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpDeclareFunction)
		{
			$res = static::OpDeclareFunction($ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpHtmlItems)
		{
			$res = $t->html::OpHtmlItems($ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpPreprocessorIfDef)
		{
			$res = $t->operator::OpPreprocessorIfDef($ctx, $t, $op_code, \Bayrell\Lang\OpCodes\OpPreprocessorIfDef::KIND_EXPRESSION);
			$t = $res[0];
			$content = $res[1];
		}
		return \Runtime\Collection::from([$t,$content]);
	}
	/* ======================= Class Init Functions ======================= */
	function assignObject($ctx,$o)
	{
		if ($o instanceof \Bayrell\Lang\LangES6\TranslatorES6Expression)
		{
		}
		parent::assignObject($ctx,$o);
	}
	function assignValue($ctx,$k,$v)
	{
		parent::assignValue($ctx,$k,$v);
	}
	function takeValue($ctx,$k,$d=null)
	{
		return parent::takeValue($ctx,$k,$d);
	}
	function getClassName()
	{
		return "Bayrell.Lang.LangES6.TranslatorES6Expression";
	}
	static function getCurrentNamespace()
	{
		return "Bayrell.Lang.LangES6";
	}
	static function getCurrentClassName()
	{
		return "Bayrell.Lang.LangES6.TranslatorES6Expression";
	}
	static function getParentClassName()
	{
		return "Runtime.CoreStruct";
	}
	static function getClassInfo($ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.LangES6.TranslatorES6Expression",
			"name"=>"Bayrell.Lang.LangES6.TranslatorES6Expression",
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
	}
	static function getFieldsList($ctx,$f)
	{
		$a = [];
		return \Runtime\Collection::from($a);
	}
	static function getFieldInfoByName($ctx,$field_name)
	{
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