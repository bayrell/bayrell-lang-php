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
namespace Bayrell\Lang\LangES6;
class TranslatorES6AsyncAwait extends \Runtime\CoreStruct
{
	public $__async_stack;
	public $__pos;
	public $__async_t;
	public $__async_var;
	/**
	 * Returns current pos
	 */
	static function currentPos($__ctx, $t)
	{
		return $t->expression->staticMethod("toString")($__ctx, \Runtime\rs::join($__ctx, ".", $t->async_await->pos));
	}
	/**
	 * Returns current pos
	 */
	static function nextPos($__ctx, $t)
	{
		$pos = $t->async_await->pos;
		$t = $t->copy($__ctx, ["async_await"=>$t->async_await->copy($__ctx, ["pos"=>$pos->setIm($__ctx, $pos->count($__ctx) - 1, $pos->last($__ctx) + 1)])]);
		$res = $t->expression->staticMethod("toString")($__ctx, \Runtime\rs::join($__ctx, ".", $t->async_await->pos));
		return \Runtime\Collection::from([$t,$res]);
	}
	/**
	 * Returns push pos
	 */
	static function pushPos($__ctx, $t)
	{
		$pos = $t->async_await->pos;
		$t = $t->copy($__ctx, ["async_await"=>$t->async_await->copy($__ctx, ["pos"=>$pos->setIm($__ctx, $pos->count($__ctx) - 1, $pos->last($__ctx) + 1)->pushIm($__ctx, 0)])]);
		$res = $t->expression->staticMethod("toString")($__ctx, \Runtime\rs::join($__ctx, ".", $t->async_await->pos));
		return \Runtime\Collection::from([$t,$res]);
	}
	/**
	 * Returns inc pos
	 */
	static function levelIncPos($__ctx, $t)
	{
		$pos = $t->async_await->pos;
		$t = $t->copy($__ctx, ["async_await"=>$t->async_await->copy($__ctx, ["pos"=>$pos->setIm($__ctx, $pos->count($__ctx) - 1, $pos->last($__ctx))->pushIm($__ctx, 0)])]);
		$res = $t->expression->staticMethod("toString")($__ctx, \Runtime\rs::join($__ctx, ".", $t->async_await->pos));
		return \Runtime\Collection::from([$t,$res]);
	}
	/**
	 * Returns pop pos
	 */
	static function popPos($__ctx, $t)
	{
		$pos = $t->async_await->pos->removeLastIm($__ctx);
		$t = $t->copy($__ctx, ["async_await"=>$t->async_await->copy($__ctx, ["pos"=>$pos->setIm($__ctx, $pos->count($__ctx) - 1, $pos->last($__ctx) + 1)])]);
		$res = $t->expression->staticMethod("toString")($__ctx, \Runtime\rs::join($__ctx, ".", $t->async_await->pos));
		return \Runtime\Collection::from([$t,$res]);
	}
	/**
	 * OpCall
	 */
	static function OpCall($__ctx, $t, $op_code, $is_expression=true)
	{
		$s = "";
		$flag = false;
		if ($s == "")
		{
			$res = $t->expression->staticMethod("Dynamic")($__ctx, $t, $op_code->obj);
			$t = $res[0];
			$s = $res[1];
			if ($s == "parent")
			{
				$s = $t->expression->staticMethod("useModuleName")($__ctx, $t, $t->current_class_extends_name);
				if ($t->current_function->name != "constructor")
				{
					if ($t->current_function->isStatic($__ctx))
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
		}
		$content = $s;
		if ($t->current_function->is_context && $op_code->is_context)
		{
			$content .= \Runtime\rtl::toStr("__ctx");
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
		$res = $t->staticMethod("incSaveOpCode")($__ctx, $t);
		$t = $res[0];
		$var_name = $res[1];
		$res = static::nextPos($__ctx, $t);
		$t = $res[0];
		$next_pos = $res[1];
		$async_t = $t->async_await->async_t;
		$content = $t->s($__ctx, "return " . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".jump(") . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(",") . \Runtime\rtl::toStr($next_pos) . \Runtime\rtl::toStr(")") . \Runtime\rtl::toStr(".call(") . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(",") . \Runtime\rtl::toStr($content) . \Runtime\rtl::toStr(",") . \Runtime\rtl::toStr($t->expression->staticMethod("toString")($__ctx, $var_name)) . \Runtime\rtl::toStr(");"));
		$t = $t->levelDec($__ctx);
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "else if (" . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".pos() == ") . \Runtime\rtl::toStr($next_pos) . \Runtime\rtl::toStr(")")));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
		$t = $t->levelInc($__ctx);
		$res = $t->staticMethod("addSaveOpCode")($__ctx, $t, \Runtime\Dict::from(["op_code"=>$op_code,"var_name"=>$var_name,"content"=>$content]));
		$t = $res[0];
		if ($is_expression)
		{
			return \Runtime\Collection::from([$t,$async_t . \Runtime\rtl::toStr(".getVar(") . \Runtime\rtl::toStr($t->expression->staticMethod("toString")($__ctx, $var_name)) . \Runtime\rtl::toStr(")")]);
		}
		return \Runtime\Collection::from([$t,""]);
	}
	/**
	 * OpPipe
	 */
	static function OpPipe($__ctx, $t, $op_code, $is_expression=true)
	{
		$content = "";
		$var_name = "";
		$flag = false;
		$res = $t->expression->staticMethod("Expression")($__ctx, $t, $op_code->obj);
		$t = $res[0];
		$var_name = $res[1];
		if ($op_code->kind == \Bayrell\Lang\OpCodes\OpPipe::KIND_METHOD)
		{
			$content = $var_name . \Runtime\rtl::toStr(".constructor.") . \Runtime\rtl::toStr($op_code->method_name->value);
		}
		else
		{
			$res = $t->expression->staticMethod("OpTypeIdentifier")($__ctx, $t, $op_code->class_name);
			$t = $res[0];
			$content = $res[1] . \Runtime\rtl::toStr(".") . \Runtime\rtl::toStr($op_code->method_name->value);
		}
		$flag = false;
		$content .= \Runtime\rtl::toStr("(");
		if ($t->current_function->is_context && $op_code->is_context)
		{
			$content .= \Runtime\rtl::toStr("__ctx");
			$flag = true;
		}
		for ($i = 0;$i < $op_code->args->count($__ctx);$i++)
		{
			$item = $op_code->args->item($__ctx, $i);
			$res = $t->expression->staticMethod("Expression")($__ctx, $t, $item);
			$t = $res[0];
			$s1 = $res[1];
			$content .= \Runtime\rtl::toStr((($flag) ? ", " : "") . \Runtime\rtl::toStr($s1));
			$flag = true;
		}
		$content .= \Runtime\rtl::toStr((($flag) ? ", " : "") . \Runtime\rtl::toStr($var_name));
		$content .= \Runtime\rtl::toStr(")");
		$res = $t->staticMethod("incSaveOpCode")($__ctx, $t);
		$t = $res[0];
		$var_name = $res[1];
		$res = static::nextPos($__ctx, $t);
		$t = $res[0];
		$next_pos = $res[1];
		$async_t = $t->async_await->async_t;
		$content = $t->s($__ctx, "return " . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".jump(") . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(",") . \Runtime\rtl::toStr($next_pos) . \Runtime\rtl::toStr(")") . \Runtime\rtl::toStr(".call(") . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(",") . \Runtime\rtl::toStr($content) . \Runtime\rtl::toStr(",") . \Runtime\rtl::toStr($t->expression->staticMethod("toString")($__ctx, $var_name)) . \Runtime\rtl::toStr(");"));
		$t = $t->levelDec($__ctx);
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "else if (" . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".pos() == ") . \Runtime\rtl::toStr($next_pos) . \Runtime\rtl::toStr(")")));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
		$t = $t->levelInc($__ctx);
		$res = $t->staticMethod("addSaveOpCode")($__ctx, $t, \Runtime\Dict::from(["op_code"=>$op_code,"var_name"=>$var_name,"content"=>$content]));
		$t = $res[0];
		if ($is_expression)
		{
			return \Runtime\Collection::from([$t,$async_t . \Runtime\rtl::toStr(".getVar(") . \Runtime\rtl::toStr($t->expression->staticMethod("toString")($__ctx, $var_name)) . \Runtime\rtl::toStr(")")]);
		}
		return \Runtime\Collection::from([$t,""]);
	}
	/**
	 * OpFor
	 */
	static function OpFor($__ctx, $t, $op_code)
	{
		$save_t = null;
		$async_t = $t->async_await->async_t;
		$async_var = $t->async_await->async_var;
		$content = "";
		$res = static::pushPos($__ctx, $t);
		$t = $res[0];
		$start_pos = $res[1];
		$res = static::popPos($__ctx, $t);
		$save_t = $res[0];
		$end_pos = $res[1];
		$t = $t->copy($__ctx, ["async_await"=>$t->async_await->copy($__ctx, ["async_stack"=>$t->async_await->async_stack->pushIm($__ctx, new \Bayrell\Lang\LangES6\AsyncAwait($__ctx, \Runtime\Dict::from(["start_pos"=>$start_pos,"end_pos"=>$end_pos])))])]);
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "return " . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".jump(") . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(", ") . \Runtime\rtl::toStr($start_pos) . \Runtime\rtl::toStr(");")));
		$t = $t->levelDec($__ctx);
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "/* Start Loop */"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "else if (" . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".pos() == ") . \Runtime\rtl::toStr($start_pos) . \Runtime\rtl::toStr(")")));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
		$t = $t->levelInc($__ctx);
		/* Loop Assign */
		if ($op_code->expr1 instanceof \Bayrell\Lang\OpCodes\OpAssign)
		{
			$res = $t->staticMethod("saveOpCodeCall")($__ctx, $t, $t->operator->staticMethod("OpAssign"), \Runtime\Collection::from([$op_code->expr1]));
			$t = $res[0];
			$save = $res[1];
			$value = $res[2];
			if ($save != "")
			{
				$content .= \Runtime\rtl::toStr($save);
			}
			$content .= \Runtime\rtl::toStr($value);
		}
		else
		{
			$res = $t->staticMethod("saveOpCodeCall")($__ctx, $t, $t->expression->staticMethod("Expression"), \Runtime\Collection::from([$op_code->expr1]));
			$t = $res[0];
			$save = $res[1];
			$value = $res[2];
			if ($save != "")
			{
				$content .= \Runtime\rtl::toStr($save);
			}
			$content .= \Runtime\rtl::toStr($value);
		}
		/* Loop Expression */
		$res = static::nextPos($__ctx, $t);
		$t = $res[0];
		$loop_expression = $res[1];
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "return " . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".jump(") . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(", ") . \Runtime\rtl::toStr($loop_expression) . \Runtime\rtl::toStr(");")));
		$t = $t->levelDec($__ctx);
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "/* Loop Expression */"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "else if (" . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".pos() == ") . \Runtime\rtl::toStr($loop_expression) . \Runtime\rtl::toStr(")")));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
		$t = $t->levelInc($__ctx);
		/* Call condition expression */
		$res = $t->staticMethod("saveOpCodeCall")($__ctx, $t, $t->expression->staticMethod("Expression"), \Runtime\Collection::from([$op_code->expr2]));
		$t = $res[0];
		$save = $res[1];
		$value = $res[2];
		if ($save != "")
		{
			$content .= \Runtime\rtl::toStr($save);
		}
		/* Loop condition */
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "var " . \Runtime\rtl::toStr($async_var) . \Runtime\rtl::toStr(" = ") . \Runtime\rtl::toStr($value) . \Runtime\rtl::toStr(";")));
		$res = static::nextPos($__ctx, $t);
		$t = $res[0];
		$start_loop = $res[1];
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "if (async_var)"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
		$t = $t->levelInc($__ctx);
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "return " . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".jump(") . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(", ") . \Runtime\rtl::toStr($start_loop) . \Runtime\rtl::toStr(");")));
		$t = $t->levelDec($__ctx);
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "return " . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".jump(") . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(", ") . \Runtime\rtl::toStr($end_pos) . \Runtime\rtl::toStr(");")));
		/* Start Loop */
		$t = $t->levelDec($__ctx);
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "/* Loop */"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "else if (" . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".pos() == ") . \Runtime\rtl::toStr($start_loop) . \Runtime\rtl::toStr(")")));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
		$t = $t->levelInc($__ctx);
		$res = $t->expression->staticMethod("Expression")($__ctx, $t, $op_code->expr3);
		$t = $res[0];
		$content .= \Runtime\rtl::toStr($t->s($__ctx, $res[1] . \Runtime\rtl::toStr(";")));
		$res = $t->operator->staticMethod("Operators")($__ctx, $t, $op_code->value);
		$t = $res[0];
		$content .= \Runtime\rtl::toStr($res[1]);
		/* End Loop */
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "return " . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".jump(") . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(", ") . \Runtime\rtl::toStr($loop_expression) . \Runtime\rtl::toStr(");")));
		$t = $t->levelDec($__ctx);
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "/* End Loop */"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "else if (" . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".pos() == ") . \Runtime\rtl::toStr($end_pos) . \Runtime\rtl::toStr(")")));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
		$t = $t->levelInc($__ctx);
		$t = $t->copy($__ctx, ["async_await"=>$t->async_await->copy($__ctx, ["async_stack"=>$t->async_await->async_stack->removeLastIm($__ctx)])]);
		$t = $t->copy($__ctx, ["async_await"=>$t->async_await->copy($__ctx, ["pos"=>$save_t->async_await->pos])]);
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpIfBlock
	 */
	static function OpIfBlock($__ctx, $t, $condition, $op_code, $end_pos)
	{
		$content = "";
		$async_t = $t->async_await->async_t;
		$async_var = $t->async_await->async_var;
		/* Call condition expression */
		$res = $t->staticMethod("saveOpCodeCall")($__ctx, $t, $t->expression->staticMethod("Expression"), \Runtime\Collection::from([$condition]));
		$t = $res[0];
		$save = $res[1];
		$value = $res[2];
		if ($save != "")
		{
			$content .= \Runtime\rtl::toStr($save);
		}
		$res = static::nextPos($__ctx, $t);
		$t = $res[0];
		$start_if = $res[1];
		$res = static::nextPos($__ctx, $t);
		$t = $res[0];
		$next_if = $res[1];
		/* If condition */
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "var " . \Runtime\rtl::toStr($async_var) . \Runtime\rtl::toStr(" = ") . \Runtime\rtl::toStr($value) . \Runtime\rtl::toStr(";")));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "if (async_var)"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
		$t = $t->levelInc($__ctx);
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "return " . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".jump(") . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(", ") . \Runtime\rtl::toStr($start_if) . \Runtime\rtl::toStr(");")));
		$t = $t->levelDec($__ctx);
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "return " . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".jump(") . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(", ") . \Runtime\rtl::toStr($next_if) . \Runtime\rtl::toStr(");")));
		/* Start Loop */
		$t = $t->levelDec($__ctx);
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "/* If true */"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "else if (" . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".pos() == ") . \Runtime\rtl::toStr($start_if) . \Runtime\rtl::toStr(")")));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
		$t = $t->levelInc($__ctx);
		$res = $t->operator->staticMethod("Operators")($__ctx, $t, $op_code);
		$t = $res[0];
		$content .= \Runtime\rtl::toStr($res[1]);
		/* End if */
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "return " . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".jump(") . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(", ") . \Runtime\rtl::toStr($end_pos) . \Runtime\rtl::toStr(");")));
		$t = $t->levelDec($__ctx);
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "/* Next If */"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "else if (" . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".pos() == ") . \Runtime\rtl::toStr($next_if) . \Runtime\rtl::toStr(")")));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
		$t = $t->levelInc($__ctx);
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpIf
	 */
	static function OpIf($__ctx, $t, $op_code)
	{
		$save_t = null;
		$async_t = $t->async_await->async_t;
		$async_var = $t->async_await->async_var;
		$content = "";
		$if_true_pos = "";
		$if_false_pos = "";
		$res = static::pushPos($__ctx, $t);
		$t = $res[0];
		$start_pos = $res[1];
		$res = static::popPos($__ctx, $t);
		$save_t = $res[0];
		$end_pos = $res[1];
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "return " . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".jump(") . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(", ") . \Runtime\rtl::toStr($start_pos) . \Runtime\rtl::toStr(");")));
		$t = $t->levelDec($__ctx);
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "/* Start if */"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "else if (" . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".pos() == ") . \Runtime\rtl::toStr($start_pos) . \Runtime\rtl::toStr(")")));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
		$t = $t->levelInc($__ctx);
		/* If true */
		$res = static::OpIfBlock($__ctx, $t, $op_code->condition, $op_code->if_true, $end_pos);
		$t = $res[0];
		$content .= \Runtime\rtl::toStr($res[1]);
		/* If else */
		for ($i = 0;$i < $op_code->if_else->count($__ctx);$i++)
		{
			$if_else = $op_code->if_else->item($__ctx, $i);
			$res = static::OpIfBlock($__ctx, $t, $if_else->condition, $if_else->if_true, $end_pos);
			$t = $res[0];
			$content .= \Runtime\rtl::toStr($res[1]);
		}
		/* Else */
		if ($op_code->if_false)
		{
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "/* If false */"));
			$res = $t->operator->staticMethod("Operators")($__ctx, $t, $op_code->if_false);
			$t = $res[0];
			$content .= \Runtime\rtl::toStr($res[1]);
		}
		/* End if */
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "return " . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".jump(") . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(", ") . \Runtime\rtl::toStr($end_pos) . \Runtime\rtl::toStr(");")));
		$t = $t->levelDec($__ctx);
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "/* End if */"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "else if (" . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".pos() == ") . \Runtime\rtl::toStr($end_pos) . \Runtime\rtl::toStr(")")));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
		$t = $t->levelInc($__ctx);
		$t = $t->copy($__ctx, ["async_await"=>$t->async_await->copy($__ctx, ["pos"=>$save_t->async_await->pos])]);
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
		else
		{
			$s1 = "null";
		}
		$async_t = $t->async_await->async_t;
		$content = $t->s($__ctx, "return " . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".ret(") . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(", ") . \Runtime\rtl::toStr($s1) . \Runtime\rtl::toStr(");"));
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpTryCatch
	 */
	static function OpTryCatch($__ctx, $t, $op_code)
	{
		$save_t = null;
		$content = "";
		$async_t = $t->async_await->async_t;
		$async_var = $t->async_await->async_var;
		$res = static::nextPos($__ctx, $t);
		$t = $res[0];
		$start_pos = $res[1];
		$res = static::nextPos($__ctx, $t);
		$save_t = $res[0];
		$end_pos = $res[1];
		$t = $t->copy($__ctx, ["async_await"=>$t->async_await->copy($__ctx, ["async_stack"=>$t->async_await->async_stack->pushIm($__ctx, new \Bayrell\Lang\LangES6\AsyncAwait($__ctx, \Runtime\Dict::from(["start_pos"=>$start_pos,"end_pos"=>$end_pos])))])]);
		/* Start Try Catch */
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "return " . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".jump(") . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(", ") . \Runtime\rtl::toStr($start_pos) . \Runtime\rtl::toStr(");")));
		$t = $t->levelDec($__ctx);
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "/* Start Try */"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "else if (" . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".pos() == ") . \Runtime\rtl::toStr($start_pos) . \Runtime\rtl::toStr(")")));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
		$t = $t->levelInc($__ctx);
		$res = static::levelIncPos($__ctx, $t);
		$t = $res[0];
		$start_catch = $res[1];
		$content .= \Runtime\rtl::toStr($t->s($__ctx, $async_t . \Runtime\rtl::toStr(" = ") . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".catch_push(") . \Runtime\rtl::toStr($start_catch) . \Runtime\rtl::toStr(");")));
		$res = $t->operator->staticMethod("Operators")($__ctx, $t, $op_code->op_try);
		$t = $res[0];
		$content .= \Runtime\rtl::toStr($res[1]);
		/* Start Catch */
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "return " . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".catch_pop().jump(") . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(", ") . \Runtime\rtl::toStr($end_pos) . \Runtime\rtl::toStr(");")));
		$t = $t->levelDec($__ctx);
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "/* Start Catch */"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "else if (" . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".pos() == ") . \Runtime\rtl::toStr($start_catch) . \Runtime\rtl::toStr(")")));
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
			if ($pattern != "var")
			{
				$s = "if (_ex instanceof " . \Runtime\rtl::toStr($pattern) . \Runtime\rtl::toStr(")");
			}
			else
			{
				$s = "if (true)";
			}
			$s .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
			$t = $t->levelInc($__ctx);
			$s .= \Runtime\rtl::toStr(($s != "") ? $t->s($__ctx, "var " . \Runtime\rtl::toStr($item->name) . \Runtime\rtl::toStr(" = _ex;")) : "var " . \Runtime\rtl::toStr($item->name) . \Runtime\rtl::toStr(" = _ex;"));
			$res = $t->operator->staticMethod("Operators")($__ctx, $t, $item->value);
			$t = $res[0];
			$s .= \Runtime\rtl::toStr($t->s($__ctx, $res[1]));
			$t = $t->levelDec($__ctx);
			$s .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
			if ($i != 0)
			{
				$s = "else " . \Runtime\rtl::toStr($s);
			}
			$content .= \Runtime\rtl::toStr($t->s($__ctx, $s));
		}
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "else"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
		$t = $t->levelInc($__ctx);
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "throw _ex;"));
		$t = $t->levelDec($__ctx);
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
		/* End Try Catch */
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "return " . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".jump(") . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(", ") . \Runtime\rtl::toStr($end_pos) . \Runtime\rtl::toStr(");")));
		$t = $t->levelDec($__ctx);
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "/* End Catch */"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "else if (" . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".pos() == ") . \Runtime\rtl::toStr($end_pos) . \Runtime\rtl::toStr(")")));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
		$t = $t->levelInc($__ctx);
		$t = $t->copy($__ctx, ["async_await"=>$t->async_await->copy($__ctx, ["async_stack"=>$t->async_await->async_stack->removeLastIm($__ctx)])]);
		$t = $t->copy($__ctx, ["async_await"=>$t->async_await->copy($__ctx, ["pos"=>$save_t->async_await->pos])]);
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpWhile
	 */
	static function OpWhile($__ctx, $t, $op_code)
	{
		$save_t = null;
		$async_t = $t->async_await->async_t;
		$async_var = $t->async_await->async_var;
		$content = "";
		$res = static::pushPos($__ctx, $t);
		$t = $res[0];
		$start_pos = $res[1];
		$res = static::popPos($__ctx, $t);
		$save_t = $res[0];
		$end_pos = $res[1];
		$t = $t->copy($__ctx, ["async_await"=>$t->async_await->copy($__ctx, ["async_stack"=>$t->async_await->async_stack->pushIm($__ctx, new \Bayrell\Lang\LangES6\AsyncAwait($__ctx, \Runtime\Dict::from(["start_pos"=>$start_pos,"end_pos"=>$end_pos])))])]);
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "return " . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".jump(") . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(", ") . \Runtime\rtl::toStr($start_pos) . \Runtime\rtl::toStr(");")));
		$t = $t->levelDec($__ctx);
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "/* Start while */"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "else if (" . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".pos() == ") . \Runtime\rtl::toStr($start_pos) . \Runtime\rtl::toStr(")")));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
		$t = $t->levelInc($__ctx);
		/* Call condition expression */
		$res = $t->staticMethod("saveOpCodeCall")($__ctx, $t, $t->expression->staticMethod("Expression"), \Runtime\Collection::from([$op_code->condition]));
		$t = $res[0];
		$save = $res[1];
		$value = $res[2];
		if ($save != "")
		{
			$content .= \Runtime\rtl::toStr($save);
		}
		/* Loop condition */
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "var " . \Runtime\rtl::toStr($async_var) . \Runtime\rtl::toStr(" = ") . \Runtime\rtl::toStr($value) . \Runtime\rtl::toStr(";")));
		$res = static::nextPos($__ctx, $t);
		$t = $res[0];
		$start_loop = $res[1];
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "if (async_var)"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
		$t = $t->levelInc($__ctx);
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "return " . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".jump(") . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(", ") . \Runtime\rtl::toStr($start_loop) . \Runtime\rtl::toStr(");")));
		$t = $t->levelDec($__ctx);
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "return " . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".jump(") . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(", ") . \Runtime\rtl::toStr($end_pos) . \Runtime\rtl::toStr(");")));
		/* Start Loop */
		$t = $t->levelDec($__ctx);
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "/* Loop while */"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "else if (" . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".pos() == ") . \Runtime\rtl::toStr($start_loop) . \Runtime\rtl::toStr(")")));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
		$t = $t->levelInc($__ctx);
		$res = $t->operator->staticMethod("Operators")($__ctx, $t, $op_code->value);
		$t = $res[0];
		$content .= \Runtime\rtl::toStr($res[1]);
		/* End Loop */
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "return " . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".jump(") . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(", ") . \Runtime\rtl::toStr($start_pos) . \Runtime\rtl::toStr(");")));
		$t = $t->levelDec($__ctx);
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "/* End while */"));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "else if (" . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".pos() == ") . \Runtime\rtl::toStr($end_pos) . \Runtime\rtl::toStr(")")));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
		$t = $t->levelInc($__ctx);
		$t = $t->copy($__ctx, ["async_await"=>$t->async_await->copy($__ctx, ["async_stack"=>$t->async_await->async_stack->removeLastIm($__ctx)])]);
		$t = $t->copy($__ctx, ["async_await"=>$t->async_await->copy($__ctx, ["pos"=>$save_t->async_await->pos])]);
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpDeclareFunction Body
	 */
	static function OpDeclareFunctionBody($__ctx, $t, $f)
	{
		$save_t = $t;
		/* Save op codes */
		$save_vars = $t->save_vars;
		$save_op_codes = $t->save_op_codes;
		$save_op_code_inc = $t->save_op_code_inc;
		$t = $t->staticMethod("clearSaveOpCode")($__ctx, $t);
		$async_t = $t->async_await->async_t;
		$t = $t->levelInc($__ctx);
		$s1 = $t->s($__ctx, "return (" . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(") =>"));
		$s1 .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
		$t = $t->levelInc($__ctx);
		$s1 .= \Runtime\rtl::toStr($t->s($__ctx, "if (" . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".pos() == ") . \Runtime\rtl::toStr(static::currentPos($__ctx, $t)) . \Runtime\rtl::toStr(")")));
		$s1 .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
		$t = $t->levelInc($__ctx);
		if ($f->value)
		{
			$res = $t->operator->staticMethod("Operators")($__ctx, $t, $f->value);
			$t = $res[0];
			$s1 .= \Runtime\rtl::toStr($res[1]);
		}
		else if ($f->expression)
		{
			$res = $t->expression->staticMethod("Expression")($__ctx, $t, $f->expression);
			$t = $res[0];
			$expr = $res[1];
			$s1 .= \Runtime\rtl::toStr($t->s($__ctx, "return " . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".ret(") . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(", ") . \Runtime\rtl::toStr($expr) . \Runtime\rtl::toStr(");")));
		}
		$t = $t->levelDec($__ctx);
		$s1 .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
		$s1 .= \Runtime\rtl::toStr($t->s($__ctx, "return " . \Runtime\rtl::toStr($async_t) . \Runtime\rtl::toStr(".ret_void();")));
		$t = $t->levelDec($__ctx);
		$s1 .= \Runtime\rtl::toStr($t->s($__ctx, "};"));
		$t = $t->levelDec($__ctx);
		/* Content */
		$content = "";
		$content = $t->s($__ctx, "{");
		$t = $t->levelInc($__ctx);
		if ($t->save_vars->count($__ctx) > 0)
		{
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "var " . \Runtime\rtl::toStr(\Runtime\rs::join($__ctx, ",", $t->save_vars)) . \Runtime\rtl::toStr(";")));
		}
		$content .= \Runtime\rtl::toStr($s1);
		$t = $t->levelDec($__ctx);
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
		/* Restore save op codes */
		$t = $t->copy($__ctx, ["save_vars"=>$save_vars]);
		$t = $t->copy($__ctx, ["save_op_codes"=>$save_op_codes]);
		$t = $t->copy($__ctx, ["save_op_code_inc"=>$save_op_code_inc]);
		return \Runtime\Collection::from([$save_t,$content]);
	}
	/* ======================= Class Init Functions ======================= */
	function _init($__ctx)
	{
		parent::_init($__ctx);
		$this->__async_stack = new \Runtime\Collection($__ctx);
		$this->__pos = \Runtime\Collection::from([0]);
		$this->__async_t = "__async_t";
		$this->__async_var = "__async_var";
	}
	function assignObject($__ctx,$o)
	{
		if ($o instanceof \Bayrell\Lang\LangES6\TranslatorES6AsyncAwait)
		{
			$this->__async_stack = $o->__async_stack;
			$this->__pos = $o->__pos;
			$this->__async_t = $o->__async_t;
			$this->__async_var = $o->__async_var;
		}
		parent::assignObject($__ctx,$o);
	}
	function assignValue($__ctx,$k,$v)
	{
		if ($k == "async_stack")$this->__async_stack = $v;
		else if ($k == "pos")$this->__pos = $v;
		else if ($k == "async_t")$this->__async_t = $v;
		else if ($k == "async_var")$this->__async_var = $v;
		else parent::assignValue($__ctx,$k,$v);
	}
	function takeValue($__ctx,$k,$d=null)
	{
		if ($k == "async_stack")return $this->__async_stack;
		else if ($k == "pos")return $this->__pos;
		else if ($k == "async_t")return $this->__async_t;
		else if ($k == "async_var")return $this->__async_var;
		return parent::takeValue($__ctx,$k,$d);
	}
	function getClassName()
	{
		return "Bayrell.Lang.LangES6.TranslatorES6AsyncAwait";
	}
	static function getCurrentNamespace()
	{
		return "Bayrell.Lang.LangES6";
	}
	static function getCurrentClassName()
	{
		return "Bayrell.Lang.LangES6.TranslatorES6AsyncAwait";
	}
	static function getParentClassName()
	{
		return "Runtime.CoreStruct";
	}
	static function getClassInfo($__ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($__ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.LangES6.TranslatorES6AsyncAwait",
			"name"=>"Bayrell.Lang.LangES6.TranslatorES6AsyncAwait",
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
	}
	static function getFieldsList($__ctx,$f)
	{
		$a = [];
		if (($f|3)==3)
		{
			$a[] = "async_stack";
			$a[] = "pos";
			$a[] = "async_t";
			$a[] = "async_var";
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