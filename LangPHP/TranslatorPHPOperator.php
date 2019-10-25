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
class TranslatorPHPOperator
{
	/**
	 * OpAssign
	 */
	static function OpAssignStruct($__ctx, $t, $op_code, $pos=0)
	{
		if ($op_code->names->count($__ctx) <= $pos)
		{
			return $t->expression->staticMethod("Expression")($__ctx, $t, $op_code->expression);
		}
		$names = $op_code->names->slice($__ctx, 0, $pos)->unshiftIm($__ctx, $op_code->var_name);
		$s = "$" . \Runtime\rtl::toStr(\Runtime\rs::join($__ctx, "->", $names));
		$name = $op_code->names->item($__ctx, $pos);
		$res = static::OpAssignStruct($__ctx, $t, $op_code, $pos + 1);
		$t = $res[0];
		$s .= \Runtime\rtl::toStr("->copy($__ctx, [\"" . \Runtime\rtl::toStr($name) . \Runtime\rtl::toStr("\"=>") . \Runtime\rtl::toStr($res[1]) . \Runtime\rtl::toStr("])"));
		return \Runtime\Collection::from([$t,$s]);
	}
	/**
	 * OpAssign
	 */
	static function OpAssign($__ctx, $t, $op_code, $flag_indent=true)
	{
		$content = "";
		if ($op_code->kind == \Bayrell\Lang\OpCodes\OpAssign::KIND_ASSIGN || $op_code->kind == \Bayrell\Lang\OpCodes\OpAssign::KIND_DECLARE)
		{
			for ($i = 0;$i < $op_code->values->count($__ctx);$i++)
			{
				$s = "";
				$op = "=";
				$item = $op_code->values->item($__ctx, $i);
				if ($item->expression == null)
				{
					continue;
				}
				if ($op_code->kind == \Bayrell\Lang\OpCodes\OpAssign::KIND_DECLARE)
				{
					$s = "$" . \Runtime\rtl::toStr($item->var_name);
				}
				else
				{
					$res = $t->expression->staticMethod("Dynamic")($__ctx, $t, $item->op_code);
					$t = $res[0];
					$s = $res[1];
					$op = $item->op;
				}
				if ($item->expression != null)
				{
					$res = $t->expression->staticMethod("Expression")($__ctx, $t, $item->expression);
					$t = $res[0];
					if ($op == "~=")
					{
						$s .= \Runtime\rtl::toStr(" .= " . \Runtime\rtl::toStr($t->expression->staticMethod("rtlToStr")($__ctx, $t, $res[1])));
					}
					else
					{
						$s .= \Runtime\rtl::toStr(" " . \Runtime\rtl::toStr($op) . \Runtime\rtl::toStr(" ") . \Runtime\rtl::toStr($res[1]));
					}
				}
				$content .= \Runtime\rtl::toStr(($flag_indent) ? $t->s($__ctx, $s . \Runtime\rtl::toStr(";")) : $s . \Runtime\rtl::toStr(";"));
				if ($item->var_name != "" && $t->save_vars->indexOf($__ctx, $item->var_name) == -1)
				{
					$t = $t->copy($__ctx, ["save_vars"=>$t->save_vars->pushIm($__ctx, $item->var_name)]);
				}
			}
		}
		else if ($op_code->kind == \Bayrell\Lang\OpCodes\OpAssign::KIND_STRUCT)
		{
			$s = "$" . \Runtime\rtl::toStr($op_code->var_name) . \Runtime\rtl::toStr(" = ");
			$res = static::OpAssignStruct($__ctx, $t, $op_code, 0);
			$t = $res[0];
			$content = $t->s($__ctx, $s . \Runtime\rtl::toStr($res[1]) . \Runtime\rtl::toStr(";"));
		}
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpDelete
	 */
	static function OpDelete($__ctx, $t, $op_code)
	{
		$content = "";
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpFor
	 */
	static function OpFor($__ctx, $t, $op_code)
	{
		$content = "";
		$s1 = "";
		$s2 = "";
		$s3 = "";
		if ($op_code->expr1 instanceof \Bayrell\Lang\OpCodes\OpAssign)
		{
			$res = static::OpAssign($__ctx, $t, $op_code->expr1, false);
			$t = $res[0];
			$s1 = $res[1];
		}
		else
		{
			$res = $t->expression->staticMethod("Expression")($__ctx, $t, $op_code->expr1);
			$t = $res[0];
			$s1 = $res[1];
		}
		$res = $t->expression->staticMethod("Expression")($__ctx, $t, $op_code->expr2);
		$t = $res[0];
		$s2 = $res[1];
		$res = $t->expression->staticMethod("Expression")($__ctx, $t, $op_code->expr3);
		$t = $res[0];
		$s3 = $res[1];
		$content = $t->s($__ctx, "for (" . \Runtime\rtl::toStr($s1) . \Runtime\rtl::toStr($s2) . \Runtime\rtl::toStr(";") . \Runtime\rtl::toStr($s3) . \Runtime\rtl::toStr(")"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
		$t = $t->levelInc($__ctx);
		$res = static::Operators($__ctx, $t, $op_code->value);
		$t = $res[0];
		$content .= \Runtime\rtl::toStr($res[1]);
		$t = $t->levelDec($__ctx);
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpIf
	 */
	static function OpIf($__ctx, $t, $op_code)
	{
		$content = "";
		$res = $t->expression->staticMethod("Expression")($__ctx, $t, $op_code->condition);
		$t = $res[0];
		$s1 = $res[1];
		$content = $t->s($__ctx, "if (" . \Runtime\rtl::toStr($s1) . \Runtime\rtl::toStr(")"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
		$t = $t->levelInc($__ctx);
		$res = static::Operators($__ctx, $t, $op_code->if_true);
		$t = $res[0];
		$content .= \Runtime\rtl::toStr($res[1]);
		$t = $t->levelDec($__ctx);
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
		for ($i = 0;$i < $op_code->if_else->count($__ctx);$i++)
		{
			$if_else = $op_code->if_else->item($__ctx, $i);
			$res = $t->expression->staticMethod("Expression")($__ctx, $t, $if_else->condition);
			$t = $res[0];
			$s2 = $res[1];
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "else if (" . \Runtime\rtl::toStr($s2) . \Runtime\rtl::toStr(")")));
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
			$t = $t->levelInc($__ctx);
			$res = static::Operators($__ctx, $t, $if_else->if_true);
			$t = $res[0];
			$content .= \Runtime\rtl::toStr($res[1]);
			$t = $t->levelDec($__ctx);
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
		}
		if ($op_code->if_false != null)
		{
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "else"));
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
			$t = $t->levelInc($__ctx);
			$res = static::Operators($__ctx, $t, $op_code->if_false);
			$t = $res[0];
			$content .= \Runtime\rtl::toStr($res[1]);
			$t = $t->levelDec($__ctx);
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
		}
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpReturn
	 */
	static function OpReturn($__ctx, $t, $op_code)
	{
		$content = "";
		$s1 = "";
		if ($op_code->expression)
		{
			$res = $t->expression->staticMethod("Expression")($__ctx, $t, $op_code->expression);
			$t = $res[0];
			$s1 = $res[1];
		}
		if ($t->current_function->flags != null && $t->current_function->flags->isFlag($__ctx, "memorize"))
		{
			$content = "$__memorize_value = " . \Runtime\rtl::toStr($s1) . \Runtime\rtl::toStr(";");
			$content .= \Runtime\rtl::toStr($t->s($__ctx, $t->expression->staticMethod("getModuleName")($__ctx, $t, "Runtime.rtl") . \Runtime\rtl::toStr("::_memorizeSave(\"") . \Runtime\rtl::toStr($t->current_class_full_name) . \Runtime\rtl::toStr(".") . \Runtime\rtl::toStr($t->current_function->name) . \Runtime\rtl::toStr("\", func_get_args(), $__memorize_value);")));
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "return $__memorize_value;"));
			return \Runtime\Collection::from([$t,$content]);
		}
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "return " . \Runtime\rtl::toStr($s1) . \Runtime\rtl::toStr(";")));
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpThrow
	 */
	static function OpThrow($__ctx, $t, $op_code)
	{
		$res = $t->expression->staticMethod("Expression")($__ctx, $t, $op_code->expression);
		$t = $res[0];
		$content = $t->s($__ctx, "throw " . \Runtime\rtl::toStr($res[1]) . \Runtime\rtl::toStr(";"));
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpTryCatch
	 */
	static function OpTryCatch($__ctx, $t, $op_code)
	{
		$content = "";
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "try"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
		$t = $t->levelInc($__ctx);
		$res = static::Operators($__ctx, $t, $op_code->op_try);
		$t = $res[0];
		$content .= \Runtime\rtl::toStr($t->s($__ctx, $res[1]));
		$t = $t->levelDec($__ctx);
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "catch (\\Exception $_ex)"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
		$t = $t->levelInc($__ctx);
		for ($i = 0;$i < $op_code->items->count($__ctx);$i++)
		{
			$s = "";
			$pattern = "";
			$item = $op_code->items->item($__ctx, $i);
			$res = $t->expression->staticMethod("OpTypeIdentifier")($__ctx, $t, $item->pattern);
			$t = $res[0];
			$pattern .= \Runtime\rtl::toStr($res[1]);
			if ($pattern != "\\var")
			{
				$s = "if ($_ex instanceof " . \Runtime\rtl::toStr($pattern) . \Runtime\rtl::toStr(")");
			}
			else
			{
				$s = "";
			}
			$flag = true;
			if ($s == "")
			{
				$flag = false;
			}
			if ($flag || $i > 0)
			{
				$s .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
				$t = $t->levelInc($__ctx);
			}
			$s .= \Runtime\rtl::toStr(($s != "") ? $t->s($__ctx, "$" . \Runtime\rtl::toStr($item->name) . \Runtime\rtl::toStr(" = $_ex;")) : "$" . \Runtime\rtl::toStr($item->name) . \Runtime\rtl::toStr(" = $_ex;"));
			$res = static::Operators($__ctx, $t, $item->value);
			$t = $res[0];
			$s .= \Runtime\rtl::toStr($res[1]);
			if ($flag || $i > 0)
			{
				$t = $t->levelDec($__ctx);
				$s .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
			}
			if ($i != 0)
			{
				$s = "else " . \Runtime\rtl::toStr($s);
			}
			$content .= \Runtime\rtl::toStr($t->s($__ctx, $s));
		}
		$t = $t->levelDec($__ctx);
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpWhile
	 */
	static function OpWhile($__ctx, $t, $op_code)
	{
		$content = "";
		$res = $t->expression->staticMethod("Expression")($__ctx, $t, $op_code->condition);
		$t = $res[0];
		$s1 = $res[1];
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "while (" . \Runtime\rtl::toStr($s1) . \Runtime\rtl::toStr(")")));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
		$t = $t->levelInc($__ctx);
		$res = static::Operators($__ctx, $t, $op_code->value);
		$t = $res[0];
		$content .= \Runtime\rtl::toStr($res[1]);
		$t = $t->levelDec($__ctx);
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpPreprocessorIfCode
	 */
	static function OpPreprocessorIfCode($__ctx, $t, $op_code)
	{
		$content = "";
		if ($t->preprocessor_flags->has($__ctx, $op_code->condition->value))
		{
			$content = \Runtime\rs::trim($__ctx, $op_code->content);
		}
		return \Runtime\Collection::from([$t,$t->s($__ctx, $content)]);
	}
	/**
	 * OpComment
	 */
	static function OpComment($__ctx, $t, $op_code)
	{
		$content = $t->s($__ctx, "/*" . \Runtime\rtl::toStr($op_code->value) . \Runtime\rtl::toStr("*/"));
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpComments
	 */
	static function OpComments($__ctx, $t, $comments)
	{
		$content = "";
		for ($i = 0;$i < $comments->count($__ctx);$i++)
		{
			$res = static::OpComment($__ctx, $t, $comments->item($__ctx, $i));
			$content .= \Runtime\rtl::toStr($res[1]);
		}
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpComments
	 */
	static function AddComments($__ctx, $t, $comments, $content)
	{
		if ($comments && $comments->count($__ctx) > 0)
		{
			$res = static::OpComments($__ctx, $t, $comments);
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
	static function Operator($__ctx, $t, $op_code)
	{
		$content = "";
		/* Clear save op codes */
		$save_op_codes = $t->save_op_codes;
		$save_op_code_inc = $t->save_op_code_inc;
		if ($op_code instanceof \Bayrell\Lang\OpCodes\OpAssign)
		{
			$res = static::OpAssign($__ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
			/* Output save op code */
			$save = $t->staticMethod("outputSaveOpCode")($__ctx, $t, $save_op_codes->count($__ctx));
			if ($save != "")
			{
				$content = $save . \Runtime\rtl::toStr($content);
			}
			$t = $t->copy($__ctx, ["save_op_codes"=>$save_op_codes]);
			$t = $t->copy($__ctx, ["save_op_code_inc"=>$save_op_code_inc]);
			return \Runtime\Collection::from([$t,$content]);
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpAssignStruct)
		{
			$res = static::OpAssignStruct($__ctx, $t, $op_code);
			$t = $res[0];
			$s1 = $res[1];
			/* Output save op code */
			$save = $t->staticMethod("outputSaveOpCode")($__ctx, $t, $save_op_codes->count($__ctx));
			if ($save != "")
			{
				$content = $save;
			}
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "$" . \Runtime\rtl::toStr($op_code->var_name) . \Runtime\rtl::toStr(" = ") . \Runtime\rtl::toStr($s1) . \Runtime\rtl::toStr(";")));
			$t = $t->copy($__ctx, ["save_op_codes"=>$save_op_codes]);
			$t = $t->copy($__ctx, ["save_op_code_inc"=>$save_op_code_inc]);
			return \Runtime\Collection::from([$t,$content]);
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpBreak)
		{
			$content = $t->s($__ctx, "break;");
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpCall)
		{
			$res = $t->expression->staticMethod("OpCall")($__ctx, $t, $op_code);
			$t = $res[0];
			$content = $t->s($__ctx, $res[1] . \Runtime\rtl::toStr(";"));
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpContinue)
		{
			$content = $t->s($__ctx, "continue;");
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpDelete)
		{
			$res = static::OpDelete($__ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpFor)
		{
			$res = static::OpFor($__ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpIf)
		{
			$res = static::OpIf($__ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpPipe)
		{
			$res = $t->expression->staticMethod("OpPipe")($__ctx, $t, $op_code, false);
			$t = $res[0];
			$content = $t->s($__ctx, $res[1] . \Runtime\rtl::toStr(";"));
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpReturn)
		{
			$res = static::OpReturn($__ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpThrow)
		{
			$res = static::OpThrow($__ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpTryCatch)
		{
			$res = static::OpTryCatch($__ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpWhile)
		{
			$res = static::OpWhile($__ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpInc)
		{
			$res = $t->expression->staticMethod("OpInc")($__ctx, $t, $op_code);
			$t = $res[0];
			$content = $t->s($__ctx, $res[1] . \Runtime\rtl::toStr(";"));
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpPreprocessorIfCode)
		{
			$res = static::OpPreprocessorIfCode($__ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpPreprocessorSwitch)
		{
			for ($i = 0;$i < $op_code->items->count($__ctx);$i++)
			{
				$res = static::OpPreprocessorIfCode($__ctx, $t, $op_code->items->item($__ctx, $i));
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
			$res = static::OpComment($__ctx, $t, $op_code);
			$t = $res[0];
			$content = $res[1];
		}
		/* Output save op code */
		$save = $t->staticMethod("outputSaveOpCode")($__ctx, $t, $save_op_codes->count($__ctx));
		if ($save != "")
		{
			$content = $save . \Runtime\rtl::toStr($content);
		}
		/* Restore save op codes */
		$t = $t->copy($__ctx, ["save_op_codes"=>$save_op_codes]);
		$t = $t->copy($__ctx, ["save_op_code_inc"=>$save_op_code_inc]);
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * Operators
	 */
	static function Operators($__ctx, $t, $op_code)
	{
		$content = "";
		$f1 = function ($__ctx, $op_code)
		{
			return $op_code instanceof \Bayrell\Lang\OpCodes\OpBreak || $op_code instanceof \Bayrell\Lang\OpCodes\OpCall || $op_code instanceof \Bayrell\Lang\OpCodes\OpContinue || $op_code instanceof \Bayrell\Lang\OpCodes\OpReturn || $op_code instanceof \Bayrell\Lang\OpCodes\OpThrow;
		};
		if ($op_code instanceof \Bayrell\Lang\OpCodes\OpItems)
		{
			for ($i = 0;$i < $op_code->items->count($__ctx);$i++)
			{
				$item = $op_code->items->item($__ctx, $i);
				$res = static::Operator($__ctx, $t, $item);
				$t = $res[0];
				$content .= \Runtime\rtl::toStr($res[1]);
			}
		}
		else
		{
			$res = static::Operator($__ctx, $t, $op_code);
			$t = $res[0];
			$content .= \Runtime\rtl::toStr($res[1]);
		}
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpDeclareFunction Arguments
	 */
	static function OpDeclareFunctionArgs($__ctx, $t, $f)
	{
		$content = "";
		if ($f->args != null)
		{
			$flag = false;
			if ($f->is_context)
			{
				$content .= \Runtime\rtl::toStr("$__ctx");
				$flag = true;
			}
			for ($i = 0;$i < $f->args->count($__ctx, $i);$i++)
			{
				$arg = $f->args->item($__ctx, $i);
				$name = $arg->name;
				$expr = "";
				if ($arg->expression != null)
				{
					$res = $t->expression->staticMethod("Expression")($__ctx, $t, $arg->expression);
					$t = $res[0];
					$expr = $res[1];
				}
				$content .= \Runtime\rtl::toStr((($flag) ? ", " : "") . \Runtime\rtl::toStr("$") . \Runtime\rtl::toStr($name) . \Runtime\rtl::toStr((($expr != "") ? "=" . \Runtime\rtl::toStr($expr) : "")));
				$flag = true;
			}
		}
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpDeclareFunction Body
	 */
	static function OpDeclareFunctionBody($__ctx, $t, $f)
	{
		$save_t = $t;
		$content = "";
		$t = $t->levelInc($__ctx);
		if ($f->value)
		{
			$res = $t->operator->staticMethod("Operators")($__ctx, $t, $f->value);
			$t = $res[0];
			$content .= \Runtime\rtl::toStr($res[1]);
		}
		else if ($f->expression)
		{
			/* Clear save op codes */
			$t = $t->staticMethod("clearSaveOpCode")($__ctx, $t);
			$res = $t->expression->staticMethod("Expression")($__ctx, $t, $f->expression);
			$t = $res[0];
			$expr = $res[1];
			$s = "";
			if ($f->flags != null && $f->flags->isFlag($__ctx, "memorize"))
			{
				$s = "$__memorize_value = " . \Runtime\rtl::toStr($expr) . \Runtime\rtl::toStr(";");
				$s .= \Runtime\rtl::toStr($t->s($__ctx, $t->expression->staticMethod("getModuleName")($__ctx, $t, "Runtime.rtl") . \Runtime\rtl::toStr("::_memorizeSave(\"") . \Runtime\rtl::toStr($t->current_class_full_name) . \Runtime\rtl::toStr(".") . \Runtime\rtl::toStr($f->name) . \Runtime\rtl::toStr("\", func_get_args(), $__memorize_value);")));
				$s .= \Runtime\rtl::toStr($t->s($__ctx, "return $__memorize_value;"));
			}
			else
			{
				$s = $t->s($__ctx, "return " . \Runtime\rtl::toStr($expr) . \Runtime\rtl::toStr(";"));
			}
			/* Output save op code */
			$save = $t->staticMethod("outputSaveOpCode")($__ctx, $t);
			if ($save != "")
			{
				$content .= \Runtime\rtl::toStr($save);
			}
			$content .= \Runtime\rtl::toStr($s);
		}
		if ($f->flags != null && $f->flags->isFlag($__ctx, "memorize"))
		{
			$s = "";
			$s .= \Runtime\rtl::toStr($t->s($__ctx, "$__memorize_value = " . \Runtime\rtl::toStr($t->expression->staticMethod("getModuleName")($__ctx, $t, "Runtime.rtl")) . \Runtime\rtl::toStr("::_memorizeValue(\"") . \Runtime\rtl::toStr($t->current_class_full_name) . \Runtime\rtl::toStr(".") . \Runtime\rtl::toStr($f->name) . \Runtime\rtl::toStr("\", func_get_args());")));
			$s .= \Runtime\rtl::toStr($t->s($__ctx, "if ($__memorize_value != " . \Runtime\rtl::toStr($t->expression->staticMethod("getModuleName")($__ctx, $t, "Runtime.rtl")) . \Runtime\rtl::toStr("::$_memorize_not_found) return $__memorize_value;")));
			$content = $s . \Runtime\rtl::toStr($content);
		}
		$t = $t->levelDec($__ctx);
		$content = $t->s($__ctx, "{") . \Runtime\rtl::toStr($content);
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
		return \Runtime\Collection::from([$save_t,$content]);
	}
	/* ======================= Class Init Functions ======================= */
	function getClassName()
	{
		return "Bayrell.Lang.LangPHP.TranslatorPHPOperator";
	}
	static function getCurrentNamespace()
	{
		return "Bayrell.Lang.LangPHP";
	}
	static function getCurrentClassName()
	{
		return "Bayrell.Lang.LangPHP.TranslatorPHPOperator";
	}
	static function getParentClassName()
	{
		return "";
	}
	static function getClassInfo($__ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($__ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.LangPHP.TranslatorPHPOperator",
			"name"=>"Bayrell.Lang.LangPHP.TranslatorPHPOperator",
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