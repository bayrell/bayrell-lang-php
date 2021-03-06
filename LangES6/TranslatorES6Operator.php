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
class TranslatorES6Operator extends \Runtime\CoreStruct
{
	/**
	 * Returns true if op_code contains await
	 */
	static function isAwait($ctx, $op_code)
	{
		$__memorize_value = \Runtime\rtl::_memorizeValue("Bayrell.Lang.LangES6.TranslatorES6Operator.isAwait", func_get_args());
		if ($__memorize_value != \Runtime\rtl::$_memorize_not_found) return $__memorize_value;
		if ($op_code == null)
		{
			$__memorize_value = false;
			\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangES6.TranslatorES6Operator.isAwait", func_get_args(), $__memorize_value);
			return $__memorize_value;
		}
		if ($op_code instanceof \Bayrell\Lang\OpCodes\OpAssign)
		{
			if ($op_code->kind == \Bayrell\Lang\OpCodes\OpAssign::KIND_ASSIGN || $op_code->kind == \Bayrell\Lang\OpCodes\OpAssign::KIND_DECLARE)
			{
				for ($i = 0;$i < $op_code->values->count($ctx);$i++)
				{
					$item = $op_code->values->item($ctx, $i);
					$flag = static::isAwait($ctx, $item->expression);
					if ($flag)
					{
						$__memorize_value = true;
						\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangES6.TranslatorES6Operator.isAwait", func_get_args(), $__memorize_value);
						return $__memorize_value;
					}
				}
			}
			else if ($op_code->kind == \Bayrell\Lang\OpCodes\OpAssign::KIND_STRUCT)
			{
				$flag = static::isAwait($ctx, $op_code->expression);
				if ($flag)
				{
					$__memorize_value = true;
					\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangES6.TranslatorES6Operator.isAwait", func_get_args(), $__memorize_value);
					return $__memorize_value;
				}
			}
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpAssignStruct)
		{
			$flag = static::isAwait($ctx, $op_code->expression);
			if ($flag)
			{
				$__memorize_value = true;
				\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangES6.TranslatorES6Operator.isAwait", func_get_args(), $__memorize_value);
				return $__memorize_value;
			}
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpAttr)
		{
			$op_code_next = $op_code;
			while ($op_code_next instanceof \Bayrell\Lang\OpCodes\OpAttr)
			{
				$op_code_next = $op_code_next->obj;
			}
			$__memorize_value = static::isAwait($ctx, $op_code_next);
			\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangES6.TranslatorES6Operator.isAwait", func_get_args(), $__memorize_value);
			return $__memorize_value;
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpCall)
		{
			$__memorize_value = $op_code->is_await;
			\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangES6.TranslatorES6Operator.isAwait", func_get_args(), $__memorize_value);
			return $__memorize_value;
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpPipe)
		{
			$__memorize_value = $op_code->is_await;
			\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangES6.TranslatorES6Operator.isAwait", func_get_args(), $__memorize_value);
			return $__memorize_value;
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpFor)
		{
			$__memorize_value = static::isAwait($ctx, $op_code->expr2) || static::isAwait($ctx, $op_code->value);
			\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangES6.TranslatorES6Operator.isAwait", func_get_args(), $__memorize_value);
			return $__memorize_value;
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpIf)
		{
			$flag = false;
			$flag = static::isAwait($ctx, $op_code->condition);
			if ($flag)
			{
				$__memorize_value = true;
				\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangES6.TranslatorES6Operator.isAwait", func_get_args(), $__memorize_value);
				return $__memorize_value;
			}
			$flag = static::isAwait($ctx, $op_code->if_true);
			if ($flag)
			{
				$__memorize_value = true;
				\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangES6.TranslatorES6Operator.isAwait", func_get_args(), $__memorize_value);
				return $__memorize_value;
			}
			$flag = static::isAwait($ctx, $op_code->if_false);
			if ($flag)
			{
				$__memorize_value = true;
				\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangES6.TranslatorES6Operator.isAwait", func_get_args(), $__memorize_value);
				return $__memorize_value;
			}
			for ($i = 0;$i < $op_code->if_else->count($ctx);$i++)
			{
				$if_else = $op_code->if_else->item($ctx, $i);
				$flag = static::isAwait($ctx, $if_else->condition);
				if ($flag)
				{
					$__memorize_value = true;
					\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangES6.TranslatorES6Operator.isAwait", func_get_args(), $__memorize_value);
					return $__memorize_value;
				}
				$flag = static::isAwait($ctx, $if_else->if_true);
				if ($flag)
				{
					$__memorize_value = true;
					\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangES6.TranslatorES6Operator.isAwait", func_get_args(), $__memorize_value);
					return $__memorize_value;
				}
			}
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpItems)
		{
			for ($i = 0;$i < $op_code->items->count($ctx);$i++)
			{
				$item = $op_code->items->item($ctx, $i);
				$flag = static::isAwait($ctx, $item);
				if ($flag)
				{
					$__memorize_value = true;
					\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangES6.TranslatorES6Operator.isAwait", func_get_args(), $__memorize_value);
					return $__memorize_value;
				}
			}
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpMath)
		{
			if ($op_code->math == "!" || $op_code->math == "not")
			{
				$__memorize_value = static::isAwait($ctx, $op_code->value1);
				\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangES6.TranslatorES6Operator.isAwait", func_get_args(), $__memorize_value);
				return $__memorize_value;
			}
			else
			{
				$__memorize_value = static::isAwait($ctx, $op_code->value1) || static::isAwait($ctx, $op_code->value2);
				\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangES6.TranslatorES6Operator.isAwait", func_get_args(), $__memorize_value);
				return $__memorize_value;
			}
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpReturn)
		{
			$flag = static::isAwait($ctx, $op_code->expression);
			if ($flag)
			{
				$__memorize_value = true;
				\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangES6.TranslatorES6Operator.isAwait", func_get_args(), $__memorize_value);
				return $__memorize_value;
			}
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpTryCatch)
		{
			$__memorize_value = static::isAwait($ctx, $op_code->op_try);
			\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangES6.TranslatorES6Operator.isAwait", func_get_args(), $__memorize_value);
			return $__memorize_value;
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpWhile)
		{
			$__memorize_value = static::isAwait($ctx, $op_code->condition) || static::isAwait($ctx, $op_code->value);
			\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangES6.TranslatorES6Operator.isAwait", func_get_args(), $__memorize_value);
			return $__memorize_value;
		}
		$__memorize_value = false;
		\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangES6.TranslatorES6Operator.isAwait", func_get_args(), $__memorize_value);
		return $__memorize_value;
	}
	/**
	 * OpAssign
	 */
	static function OpAssignStruct($ctx, $t, $op_code, $pos=0)
	{
		if ($op_code->names->count($ctx) <= $pos)
		{
			return $t->expression::Expression($ctx, $t, $op_code->expression);
		}
		$names = $op_code->names->slice($ctx, 0, $pos)->unshiftIm($ctx, $op_code->var_name);
		$s = \Runtime\rs::join($ctx, ".", $names);
		$name = $op_code->names->item($ctx, $pos);
		$res = static::OpAssignStruct($ctx, $t, $op_code, $pos + 1);
		$t = $res[0];
		$s .= \Runtime\rtl::toStr(".copy(ctx, { \"" . \Runtime\rtl::toStr($name) . \Runtime\rtl::toStr("\": ") . \Runtime\rtl::toStr($res[1]) . \Runtime\rtl::toStr(" })"));
		return \Runtime\Collection::from([$t,$s]);
	}
	/**
	 * OpAssign
	 */
	static function OpAssign($ctx, $t, $op_code, $flag_indent=true)
	{
		$content = "";
		if ($op_code->kind == \Bayrell\Lang\OpCodes\OpAssign::KIND_ASSIGN || $op_code->kind == \Bayrell\Lang\OpCodes\OpAssign::KIND_DECLARE)
		{
			for ($i = 0;$i < $op_code->values->count($ctx);$i++)
			{
				$s = "";
				$op = "=";
				$item = $op_code->values->item($ctx, $i);
				if ($op_code->kind == \Bayrell\Lang\OpCodes\OpAssign::KIND_DECLARE)
				{
					if ($t->current_function->isFlag($ctx, "async"))
					{
						$s = $item->var_name;
					}
					else
					{
						$s = "var " . \Runtime\rtl::toStr($item->var_name);
					}
				}
				else
				{
					$res = $t->expression::Dynamic($ctx, $t, $item->op_code);
					$t = $res[0];
					$s = $res[1];
					$op = $item->op;
				}
				if ($item->expression != null)
				{
					$res = $t->expression::Expression($ctx, $t, $item->expression);
					$t = $res[0];
					if ($op == "~=")
					{
						$s .= \Runtime\rtl::toStr(" += " . \Runtime\rtl::toStr($t->expression::rtlToStr($ctx, $t, $res[1])));
					}
					else
					{
						$s .= \Runtime\rtl::toStr(" " . \Runtime\rtl::toStr($op) . \Runtime\rtl::toStr(" ") . \Runtime\rtl::toStr($res[1]));
					}
				}
				if (!($item->expression == null && $op_code->kind == \Bayrell\Lang\OpCodes\OpAssign::KIND_DECLARE && $t->current_function->isFlag($ctx, "async")))
				{
					$content .= \Runtime\rtl::toStr(($flag_indent) ? $t->s($ctx, $s . \Runtime\rtl::toStr(";")) : $s . \Runtime\rtl::toStr(";"));
				}
				if ($item->var_name != "" && $t->save_vars->indexOf($ctx, $item->var_name) == -1)
				{
					$t = $t->copy($ctx, ["save_vars"=>$t->save_vars->pushIm($ctx, $item->var_name)]);
				}
			}
		}
		else if ($op_code->kind == \Bayrell\Lang\OpCodes\OpAssign::KIND_STRUCT)
		{
			$s = $op_code->var_name . \Runtime\rtl::toStr(" = ");
			$res = static::OpAssignStruct($ctx, $t, $op_code, 0);
			$t = $res[0];
			$content = $t->s($ctx, $s . \Runtime\rtl::toStr($res[1]) . \Runtime\rtl::toStr(";"));
		}
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpDelete
	 */
	static function OpDelete($ctx, $t, $op_code)
	{
		$content = "";
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpFor
	 */
	static function OpFor($ctx, $t, $op_code)
	{
		if ($t->current_function->isFlag($ctx, "async"))
		{
			if (static::isAwait($ctx, $op_code))
			{
				return $t->async_await::OpFor($ctx, $t, $op_code);
			}
		}
		$content = "";
		$s1 = "";
		$s2 = "";
		$s3 = "";
		if ($op_code->expr1 instanceof \Bayrell\Lang\OpCodes\OpAssign)
		{
			$res = static::OpAssign($ctx, $t, $op_code->expr1, false);
			$t = $res[0];
			$s1 = $res[1];
		}
		else
		{
			$res = $t->expression::Expression($ctx, $t, $op_code->expr1);
			$t = $res[0];
			$s1 = $res[1];
		}
		$res = $t->expression::Expression($ctx, $t, $op_code->expr2);
		$t = $res[0];
		$s2 = $res[1];
		$res = $t->expression::Expression($ctx, $t, $op_code->expr3);
		$t = $res[0];
		$s3 = $res[1];
		$content = $t->s($ctx, "for (" . \Runtime\rtl::toStr($s1) . \Runtime\rtl::toStr($s2) . \Runtime\rtl::toStr(";") . \Runtime\rtl::toStr($s3) . \Runtime\rtl::toStr(")"));
		$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
		$t = $t->levelInc($ctx);
		$res = static::Operators($ctx, $t, $op_code->value);
		$t = $res[0];
		$content .= \Runtime\rtl::toStr($res[1]);
		$t = $t->levelDec($ctx);
		$content .= \Runtime\rtl::toStr($t->s($ctx, "}"));
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpIf
	 */
	static function OpIf($ctx, $t, $op_code)
	{
		if ($t->current_function->isFlag($ctx, "async"))
		{
			if (static::isAwait($ctx, $op_code))
			{
				return $t->async_await::OpIf($ctx, $t, $op_code);
			}
		}
		$content = "";
		$res = $t->expression::Expression($ctx, $t, $op_code->condition);
		$t = $res[0];
		$s1 = $res[1];
		$content = $t->s($ctx, "if (" . \Runtime\rtl::toStr($s1) . \Runtime\rtl::toStr(")"));
		$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
		$t = $t->levelInc($ctx);
		$res = static::Operators($ctx, $t, $op_code->if_true);
		$t = $res[0];
		$content .= \Runtime\rtl::toStr($res[1]);
		$t = $t->levelDec($ctx);
		$content .= \Runtime\rtl::toStr($t->s($ctx, "}"));
		for ($i = 0;$i < $op_code->if_else->count($ctx);$i++)
		{
			$if_else = $op_code->if_else->item($ctx, $i);
			$res = $t->expression::Expression($ctx, $t, $if_else->condition);
			$t = $res[0];
			$s2 = $res[1];
			$content .= \Runtime\rtl::toStr($t->s($ctx, "else if (" . \Runtime\rtl::toStr($s2) . \Runtime\rtl::toStr(")")));
			$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
			$t = $t->levelInc($ctx);
			$res = static::Operators($ctx, $t, $if_else->if_true);
			$t = $res[0];
			$content .= \Runtime\rtl::toStr($res[1]);
			$t = $t->levelDec($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "}"));
		}
		if ($op_code->if_false != null)
		{
			$content .= \Runtime\rtl::toStr($t->s($ctx, "else"));
			$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
			$t = $t->levelInc($ctx);
			$res = static::Operators($ctx, $t, $op_code->if_false);
			$t = $res[0];
			$content .= \Runtime\rtl::toStr($res[1]);
			$t = $t->levelDec($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "}"));
		}
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpReturn
	 */
	static function OpReturn($ctx, $t, $op_code)
	{
		if ($t->current_function->isFlag($ctx, "async"))
		{
			return $t->async_await::OpReturn($ctx, $t, $op_code);
		}
		$content = "";
		$s1 = "";
		if ($op_code->expression)
		{
			$res = $t->expression::Expression($ctx, $t, $op_code->expression);
			$t = $res[0];
			$s1 = $res[1];
		}
		if ($t->current_function->flags != null && $t->current_function->flags->isFlag($ctx, "memorize"))
		{
			$content = $t->s($ctx, "var __memorize_value = " . \Runtime\rtl::toStr($s1) . \Runtime\rtl::toStr(";"));
			$content .= \Runtime\rtl::toStr($t->s($ctx, $t->expression::useModuleName($ctx, $t, "Runtime.rtl") . \Runtime\rtl::toStr("._memorizeSave(\"") . \Runtime\rtl::toStr($t->current_class_full_name) . \Runtime\rtl::toStr(".") . \Runtime\rtl::toStr($t->current_function->name) . \Runtime\rtl::toStr("\", arguments, __memorize_value);")));
			$content .= \Runtime\rtl::toStr($t->s($ctx, "return __memorize_value;"));
			return \Runtime\Collection::from([$t,$content]);
		}
		$content .= \Runtime\rtl::toStr($t->s($ctx, "return " . \Runtime\rtl::toStr($s1) . \Runtime\rtl::toStr(";")));
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpThrow
	 */
	static function OpThrow($ctx, $t, $op_code)
	{
		$res = $t->expression::Expression($ctx, $t, $op_code->expression);
		$t = $res[0];
		$content = $t->s($ctx, "throw " . \Runtime\rtl::toStr($res[1]));
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpTryCatch
	 */
	static function OpTryCatch($ctx, $t, $op_code)
	{
		if ($t->current_function->isFlag($ctx, "async"))
		{
			if (static::isAwait($ctx, $op_code))
			{
				return $t->async_await::OpTryCatch($ctx, $t, $op_code);
			}
		}
		$content = "";
		$content .= \Runtime\rtl::toStr($t->s($ctx, "try"));
		$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
		$t = $t->levelInc($ctx);
		$res = static::Operators($ctx, $t, $op_code->op_try);
		$t = $res[0];
		$content .= \Runtime\rtl::toStr($res[1]);
		$t = $t->levelDec($ctx);
		$content .= \Runtime\rtl::toStr($t->s($ctx, "}"));
		$content .= \Runtime\rtl::toStr($t->s($ctx, "catch (_ex)"));
		$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
		$t = $t->levelInc($ctx);
		for ($i = 0;$i < $op_code->items->count($ctx);$i++)
		{
			$s = "";
			$pattern = "";
			$item = $op_code->items->item($ctx, $i);
			$res = $t->expression::OpTypeIdentifier($ctx, $t, $item->pattern);
			$t = $res[0];
			$pattern .= \Runtime\rtl::toStr($res[1]);
			if ($pattern != "var")
			{
				$s = "if (_ex instanceof " . \Runtime\rtl::toStr($pattern) . \Runtime\rtl::toStr(")");
			}
			else
			{
				$s = "if (true)";
			}
			$s .= \Runtime\rtl::toStr($t->s($ctx, "{"));
			$t = $t->levelInc($ctx);
			$s .= \Runtime\rtl::toStr(($s != "") ? $t->s($ctx, "var " . \Runtime\rtl::toStr($item->name) . \Runtime\rtl::toStr(" = _ex;")) : "var " . \Runtime\rtl::toStr($item->name) . \Runtime\rtl::toStr(" = _ex;"));
			$res = $t->operator::Operators($ctx, $t, $item->value);
			$t = $res[0];
			$s .= \Runtime\rtl::toStr($t->s($ctx, $res[1]));
			$t = $t->levelDec($ctx);
			$s .= \Runtime\rtl::toStr($t->s($ctx, "}"));
			if ($i != 0)
			{
				$s = "else " . \Runtime\rtl::toStr($s);
			}
			$content .= \Runtime\rtl::toStr($t->s($ctx, $s));
		}
		$content .= \Runtime\rtl::toStr($t->s($ctx, "else"));
		$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
		$t = $t->levelInc($ctx);
		$content .= \Runtime\rtl::toStr($t->s($ctx, "throw _ex;"));
		$t = $t->levelDec($ctx);
		$content .= \Runtime\rtl::toStr($t->s($ctx, "}"));
		$t = $t->levelDec($ctx);
		$content .= \Runtime\rtl::toStr($t->s($ctx, "}"));
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpWhile
	 */
	static function OpWhile($ctx, $t, $op_code)
	{
		if ($t->current_function->isFlag($ctx, "async"))
		{
			if (static::isAwait($ctx, $op_code))
			{
				return $t->async_await::OpWhile($ctx, $t, $op_code);
			}
		}
		$content = "";
		$res = $t->expression::Expression($ctx, $t, $op_code->condition);
		$t = $res[0];
		$s1 = $res[1];
		$content .= \Runtime\rtl::toStr($t->s($ctx, "while (" . \Runtime\rtl::toStr($s1) . \Runtime\rtl::toStr(")")));
		$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
		$t = $t->levelInc($ctx);
		$res = static::Operators($ctx, $t, $op_code->value);
		$t = $res[0];
		$content .= \Runtime\rtl::toStr($res[1]);
		$t = $t->levelDec($ctx);
		$content .= \Runtime\rtl::toStr($t->s($ctx, "}"));
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpPreprocessorIfCode
	 */
	static function OpPreprocessorIfCode($ctx, $t, $op_code)
	{
		$content = "";
		if ($t->preprocessor_flags->has($ctx, $op_code->condition->value))
		{
			$content = \Runtime\rs::trim($ctx, $op_code->content);
		}
		return \Runtime\Collection::from([$t,$t->s($ctx, $content)]);
	}
	/**
	 * OpPreprocessorIfDef
	 */
	static function OpPreprocessorIfDef($ctx, $t, $op_code, $kind)
	{
		if (!$t->preprocessor_flags->has($ctx, $op_code->condition->value))
		{
			return \Runtime\Collection::from([$t,""]);
		}
		if ($kind == \Bayrell\Lang\OpCodes\OpPreprocessorIfDef::KIND_OPERATOR)
		{
			return static::Operators($ctx, $t, $op_code->items);
		}
		else if ($kind == \Bayrell\Lang\OpCodes\OpPreprocessorIfDef::KIND_EXPRESSION)
		{
			return $t->expression::Expression($ctx, $t, $op_code->items);
		}
		$content = "";
		for ($i = 0;$i < $op_code->items->count($ctx);$i++)
		{
			$item = $op_code->items->item($ctx, $i);
			if ($item instanceof \Bayrell\Lang\OpCodes\OpComment)
			{
				$res = $t->operator::OpComment($ctx, $t, $item);
				$t = $res[0];
				$content .= \Runtime\rtl::toStr($res[1]);
			}
			else if ($item instanceof \Bayrell\Lang\OpCodes\OpDeclareFunction)
			{
				$res = $t->program::OpDeclareFunction($ctx, $t, $item);
				$t = $res[0];
				$content .= \Runtime\rtl::toStr($res[1]);
			}
		}
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpComment
	 */
	static function OpComment($ctx, $t, $op_code)
	{
		$content = $t->s($ctx, "/*" . \Runtime\rtl::toStr($op_code->value) . \Runtime\rtl::toStr("*/"));
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpComments
	 */
	static function OpComments($ctx, $t, $comments)
	{
		$content = "";
		for ($i = 0;$i < $comments->count($ctx);$i++)
		{
			$res = static::OpComment($ctx, $t, $comments->item($ctx, $i));
			$content .= \Runtime\rtl::toStr($res[1]);
		}
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpComments
	 */
	static function AddComments($ctx, $t, $comments, $content)
	{
		if ($comments && $comments->count($ctx) > 0)
		{
			$res = static::OpComments($ctx, $t, $comments);
			$s = $res[1];
			if ($s != "")
			{
				$content = $s . \Runtime\rtl::toStr($content);
			}
		}
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * Operator
	 */
	static function Operator($ctx, $t, $op_code)
	{
		$content = "";
		/* Clear save op codes */
		$save_op_codes = $t->save_op_codes;
		$save_op_code_inc = $t->save_op_code_inc;
		if ($op_code instanceof \Bayrell\Lang\OpCodes\OpAssign)
		{
			$res = static::OpAssign($ctx, $t, $op_code);
			$t = $res[0];
			/* Output save op code */
			$save = $t::outputSaveOpCode($ctx, $t, $save_op_codes->count($ctx));
			if ($save != "")
			{
				$content = $save . \Runtime\rtl::toStr($content);
			}
			$content .= \Runtime\rtl::toStr($res[1]);
			$t = $t->copy($ctx, ["save_op_codes"=>$save_op_codes]);
			$t = $t->copy($ctx, ["save_op_code_inc"=>$save_op_code_inc]);
			return \Runtime\Collection::from([$t,$content]);
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpAssignStruct)
		{
			$res = static::OpAssignStruct($ctx, $t, $op_code);
			$t = $res[0];
			$s1 = $res[1];
			/* Output save op code */
			$save = $t::outputSaveOpCode($ctx, $t, $save_op_codes->count($ctx));
			if ($save != "")
			{
				$content = $save;
			}
			$content .= \Runtime\rtl::toStr($t->s($ctx, $op_code->var_name . \Runtime\rtl::toStr(" = ") . \Runtime\rtl::toStr($s1) . \Runtime\rtl::toStr(";")));
			$t = $t->copy($ctx, ["save_op_codes"=>$save_op_codes]);
			$t = $t->copy($ctx, ["save_op_code_inc"=>$save_op_code_inc]);
			return \Runtime\Collection::from([$t,$content]);
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpBreak)
		{
			$content = $t->s($ctx, "break;");
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpCall)
		{
			$res = $t->expression::OpCall($ctx, $t, $op_code, false);
			$t = $res[0];
			if ($res[1] != "")
			{
				$content = $t->s($ctx, $res[1] . \Runtime\rtl::toStr(";"));
			}
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpContinue)
		{
			$content = $t->s($ctx, "continue;");
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpDelete)
		{
			$res = static::OpDelete($ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpFor)
		{
			$res = static::OpFor($ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpIf)
		{
			$res = static::OpIf($ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpPipe)
		{
			$res = $t->expression::OpPipe($ctx, $t, $op_code, false);
			$t = $res[0];
			$content = $t->s($ctx, $res[1] . \Runtime\rtl::toStr(";"));
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpReturn)
		{
			$res = static::OpReturn($ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpThrow)
		{
			$res = static::OpThrow($ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpTryCatch)
		{
			$res = static::OpTryCatch($ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpWhile)
		{
			$res = static::OpWhile($ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpInc)
		{
			$res = $t->expression::OpInc($ctx, $t, $op_code);
			$t = $res[0];
			$content = $t->s($ctx, $res[1] . \Runtime\rtl::toStr(";"));
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpPreprocessorIfCode)
		{
			$res = static::OpPreprocessorIfCode($ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpPreprocessorIfDef)
		{
			$res = static::OpPreprocessorIfDef($ctx, $t, $op_code, \Bayrell\Lang\OpCodes\OpPreprocessorIfDef::KIND_OPERATOR);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpPreprocessorSwitch)
		{
			for ($i = 0;$i < $op_code->items->count($ctx);$i++)
			{
				$res = static::OpPreprocessorIfCode($ctx, $t, $op_code->items->item($ctx, $i));
				$s = $res[1];
				if ($s == "")
				{
					continue;
				}
				$content .= \Runtime\rtl::toStr($s);
			}
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpComment)
		{
			$res = static::OpComment($ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		/* Output save op code */
		$save = $t::outputSaveOpCode($ctx, $t, $save_op_codes->count($ctx));
		if ($save != "")
		{
			$content = $save . \Runtime\rtl::toStr($content);
		}
		/* Restore save op codes */
		$t = $t->copy($ctx, ["save_op_codes"=>$save_op_codes]);
		$t = $t->copy($ctx, ["save_op_code_inc"=>$save_op_code_inc]);
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * Operators
	 */
	static function Operators($ctx, $t, $op_code)
	{
		$content = "";
		if ($op_code instanceof \Bayrell\Lang\OpCodes\OpItems)
		{
			for ($i = 0;$i < $op_code->items->count($ctx);$i++)
			{
				$item = $op_code->items->item($ctx, $i);
				$res = static::Operator($ctx, $t, $item);
				$t = $res[0];
				$content .= \Runtime\rtl::toStr($res[1]);
			}
		}
		else
		{
			$res = static::Operator($ctx, $t, $op_code);
			$t = $res[0];
			$content .= \Runtime\rtl::toStr($res[1]);
		}
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpDeclareFunction Arguments
	 */
	static function OpDeclareFunctionArgs($ctx, $t, $f)
	{
		$content = "";
		if ($f->args != null)
		{
			$flag = false;
			if ($f->is_context)
			{
				$content .= \Runtime\rtl::toStr("ctx");
				$flag = true;
			}
			for ($i = 0;$i < $f->args->count($ctx, $i);$i++)
			{
				$arg = $f->args->item($ctx, $i);
				$name = $arg->name;
				$content .= \Runtime\rtl::toStr((($flag) ? ", " : "") . \Runtime\rtl::toStr($name));
				$flag = true;
			}
		}
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpDeclareFunction Body
	 */
	static function OpDeclareFunctionBody($ctx, $t, $f)
	{
		$save_t = $t;
		if ($f->isFlag($ctx, "async"))
		{
			return $t->async_await::OpDeclareFunctionBody($ctx, $t, $f);
		}
		/* Save op codes */
		$save_op_codes = $t->save_op_codes;
		$save_op_code_inc = $t->save_op_code_inc;
		$t = $t::clearSaveOpCode($ctx, $t);
		$content = "";
		$t = $t->levelInc($ctx);
		if ($f->args)
		{
			for ($i = 0;$i < $f->args->count($ctx);$i++)
			{
				$arg = $f->args->item($ctx, $i);
				if ($arg->expression == null)
				{
					continue;
				}
				$res = $t->expression::Expression($ctx, $t, $arg->expression);
				$t = $res[0];
				$s = $res[1];
				$s = "if (" . \Runtime\rtl::toStr($arg->name) . \Runtime\rtl::toStr(" == undefined) ") . \Runtime\rtl::toStr($arg->name) . \Runtime\rtl::toStr(" = ") . \Runtime\rtl::toStr($s) . \Runtime\rtl::toStr(";");
				$content .= \Runtime\rtl::toStr($t->s($ctx, $s));
			}
		}
		if ($f->value)
		{
			$res = $t->operator::Operators($ctx, $t, $f->value);
			$t = $res[0];
			$content .= \Runtime\rtl::toStr($res[1]);
		}
		else if ($f->expression)
		{
			$res = $t->expression::Expression($ctx, $t, $f->expression);
			$t = $res[0];
			$expr = $res[1];
			$s = "";
			if ($f->flags != null && $f->flags->isFlag($ctx, "memorize"))
			{
				$s = $t->s($ctx, "var __memorize_value = " . \Runtime\rtl::toStr($expr) . \Runtime\rtl::toStr(";"));
				$s .= \Runtime\rtl::toStr($t->s($ctx, $t->expression::useModuleName($ctx, $t, "Runtime.rtl") . \Runtime\rtl::toStr("._memorizeSave(\"") . \Runtime\rtl::toStr($t->current_class_full_name) . \Runtime\rtl::toStr(".") . \Runtime\rtl::toStr($f->name) . \Runtime\rtl::toStr("\", arguments, __memorize_value);")));
				$s .= \Runtime\rtl::toStr($t->s($ctx, "return __memorize_value;"));
			}
			else
			{
				$s = $t->s($ctx, "return " . \Runtime\rtl::toStr($expr) . \Runtime\rtl::toStr(";"));
			}
			/* Output save op code */
			$save = $t::outputSaveOpCode($ctx, $t, $save_op_codes->count($ctx));
			if ($save != "")
			{
				$content .= \Runtime\rtl::toStr($save);
			}
			$content .= \Runtime\rtl::toStr($s);
		}
		if ($f->flags != null && $f->flags->isFlag($ctx, "memorize"))
		{
			$s = "";
			$s .= \Runtime\rtl::toStr($t->s($ctx, "var __memorize_value = " . \Runtime\rtl::toStr($t->expression::useModuleName($ctx, $t, "Runtime.rtl")) . \Runtime\rtl::toStr("._memorizeValue(\"") . \Runtime\rtl::toStr($t->current_class_full_name) . \Runtime\rtl::toStr(".") . \Runtime\rtl::toStr($f->name) . \Runtime\rtl::toStr("\", arguments);")));
			$s .= \Runtime\rtl::toStr($t->s($ctx, "if (__memorize_value != " . \Runtime\rtl::toStr($t->expression::useModuleName($ctx, $t, "Runtime.rtl")) . \Runtime\rtl::toStr("._memorize_not_found) return __memorize_value;")));
			$content = $s . \Runtime\rtl::toStr($content);
		}
		$t = $t->levelDec($ctx);
		$content = $t->s($ctx, "{") . \Runtime\rtl::toStr($content);
		$content .= \Runtime\rtl::toStr($t->s($ctx, "}"));
		/* Restore save op codes */
		$t = $t->copy($ctx, ["save_op_codes"=>$save_op_codes]);
		$t = $t->copy($ctx, ["save_op_code_inc"=>$save_op_code_inc]);
		return \Runtime\Collection::from([$save_t,$content]);
	}
	/* ======================= Class Init Functions ======================= */
	function assignObject($ctx,$o)
	{
		if ($o instanceof \Bayrell\Lang\LangES6\TranslatorES6Operator)
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
		return "Bayrell.Lang.LangES6.TranslatorES6Operator";
	}
	static function getCurrentNamespace()
	{
		return "Bayrell.Lang.LangES6";
	}
	static function getCurrentClassName()
	{
		return "Bayrell.Lang.LangES6.TranslatorES6Operator";
	}
	static function getParentClassName()
	{
		return "Runtime.CoreStruct";
	}
	static function getClassInfo($ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.LangES6.TranslatorES6Operator",
			"name"=>"Bayrell.Lang.LangES6.TranslatorES6Operator",
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