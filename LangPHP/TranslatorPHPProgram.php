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
namespace Bayrell\Lang\LangPHP;
class TranslatorPHPProgram
{
	/**
	 * OpNamespace
	 */
	static function OpNamespace($ctx, $t, $op_code)
	{
		$arr = \Runtime\rs::split($ctx, "\\.", $op_code->name);
		$t = $t->copy($ctx, ["current_namespace_name"=>$op_code->name]);
		return \Runtime\Collection::from([$t,$t->s($ctx, "namespace " . \Runtime\rtl::toStr(\Runtime\rs::join($ctx, "\\", $arr)) . \Runtime\rtl::toStr(";"))]);
	}
	/**
	 * OpDeclareFunction
	 */
	static function OpDeclareFunction($ctx, $t, $op_code)
	{
		if ($op_code->isFlag($ctx, "declare"))
		{
			return \Runtime\Collection::from([$t,""]);
		}
		$content = "";
		/* Set current function */
		$t = $t->copy($ctx, ["current_function"=>$op_code]);
		$s1 = "";
		$s2 = "";
		if ($op_code->isStatic($ctx))
		{
			$s1 .= \Runtime\rtl::toStr("static ");
			$t = $t->copy($ctx, ["is_static_function"=>true]);
		}
		else
		{
			$t = $t->copy($ctx, ["is_static_function"=>false]);
		}
		$res = $t->operator::OpDeclareFunctionArgs($ctx, $t, $op_code);
		$args = $res[1];
		$s1 .= \Runtime\rtl::toStr("function " . \Runtime\rtl::toStr($op_code->name) . \Runtime\rtl::toStr("(") . \Runtime\rtl::toStr($args) . \Runtime\rtl::toStr(")"));
		if ($t->current_class->kind != \Bayrell\Lang\OpCodes\OpDeclareClass::KIND_INTERFACE)
		{
			$res = $t->operator::OpDeclareFunctionBody($ctx, $t, $op_code);
			$s2 .= \Runtime\rtl::toStr($res[1]);
		}
		else
		{
			$s2 .= \Runtime\rtl::toStr(";");
		}
		$s1 = $t->s($ctx, $s1);
		/* Function comments */
		$res = $t->operator::AddComments($ctx, $t, $op_code->comments, $s1 . \Runtime\rtl::toStr($s2));
		$content .= \Runtime\rtl::toStr($res[1]);
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpFunctionAnnotations
	 */
	static function OpFunctionAnnotations($ctx, $t, $f)
	{
		$content = "";
		if ($f->flags->isFlag($ctx, "declare"))
		{
			return \Runtime\Collection::from([$t,$content]);
		}
		if ($f->annotations->count($ctx) == 0)
		{
			return \Runtime\Collection::from([$t,$content]);
		}
		$content .= \Runtime\rtl::toStr($t->s($ctx, "if ($field_name == " . \Runtime\rtl::toStr($t->expression::toString($ctx, $f->name)) . \Runtime\rtl::toStr(")")));
		$t = $t->levelInc($ctx);
		$content .= \Runtime\rtl::toStr($t->s($ctx, "return new \\Runtime\\Annotations\\IntrospectionInfo($ctx, ["));
		$t = $t->levelInc($ctx);
		$content .= \Runtime\rtl::toStr($t->s($ctx, "\"kind\"=>\\Runtime\\Annotations\\IntrospectionInfo::ITEM_METHOD,"));
		$content .= \Runtime\rtl::toStr($t->s($ctx, "\"class_name\"=>" . \Runtime\rtl::toStr($t->expression::toString($ctx, $t->current_class_full_name)) . \Runtime\rtl::toStr(",")));
		$content .= \Runtime\rtl::toStr($t->s($ctx, "\"name\"=>" . \Runtime\rtl::toStr($t->expression::toString($ctx, $f->name)) . \Runtime\rtl::toStr(",")));
		$content .= \Runtime\rtl::toStr($t->s($ctx, "\"annotations\"=>\\Runtime\\Collection::from(["));
		$t = $t->levelInc($ctx);
		for ($j = 0;$j < $f->annotations->count($ctx);$j++)
		{
			$annotation = $f->annotations->item($ctx, $j);
			$res = $t->expression::OpTypeIdentifier($ctx, $t, $annotation->name);
			$t = $res[0];
			$name = $res[1];
			$res = $t->expression::OpDict($ctx, $t, $annotation->params, true);
			$t = $res[0];
			$params = $res[1];
			$content .= \Runtime\rtl::toStr($t->s($ctx, "new " . \Runtime\rtl::toStr($name) . \Runtime\rtl::toStr("($ctx, ") . \Runtime\rtl::toStr($params) . \Runtime\rtl::toStr("),")));
		}
		$t = $t->levelDec($ctx);
		$content .= \Runtime\rtl::toStr($t->s($ctx, "]),"));
		$t = $t->levelDec($ctx);
		$content .= \Runtime\rtl::toStr($t->s($ctx, "]);"));
		$t = $t->levelDec($ctx);
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpClassBodyItemMethodsList
	 */
	static function OpClassBodyItemMethodsList($ctx, $t, $item)
	{
		$content = "";
		if ($item instanceof \Bayrell\Lang\OpCodes\OpPreprocessorIfDef)
		{
			if ($t->preprocessor_flags->has($ctx, $item->condition->value))
			{
				for ($i = 0;$i < $item->items->count($ctx);$i++)
				{
					$op_code = $item->items->item($ctx, $i);
					$res = static::OpClassBodyItemMethodsList($ctx, $t, $op_code);
					$t = $res[0];
					$content .= \Runtime\rtl::toStr($res[1]);
				}
			}
		}
		else if ($item instanceof \Bayrell\Lang\OpCodes\OpDeclareFunction)
		{
			$content .= \Runtime\rtl::toStr($t->s($ctx, $t->expression::toString($ctx, $item->name) . \Runtime\rtl::toStr(",")));
		}
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpClassBodyItemAnnotations
	 */
	static function OpClassBodyItemAnnotations($ctx, $t, $item)
	{
		$content = "";
		if ($item instanceof \Bayrell\Lang\OpCodes\OpPreprocessorIfDef)
		{
			if ($t->preprocessor_flags->has($ctx, $item->condition->value))
			{
				for ($i = 0;$i < $item->items->count($ctx);$i++)
				{
					$op_code = $item->items->item($ctx, $i);
					$res = static::OpClassBodyItemAnnotations($ctx, $t, $op_code);
					$t = $res[0];
					$content .= \Runtime\rtl::toStr($res[1]);
				}
			}
		}
		else if ($item instanceof \Bayrell\Lang\OpCodes\OpDeclareFunction)
		{
			$res = static::OpFunctionAnnotations($ctx, $t, $item);
			$t = $res[0];
			$content .= \Runtime\rtl::toStr($res[1]);
		}
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpDeclareClass
	 */
	static function OpDeclareClassConstructor($ctx, $t, $op_code)
	{
		if ($op_code->fn_create == null)
		{
			return \Runtime\Collection::from([$t,""]);
		}
		$open = "";
		$content = "";
		$save_t = $t;
		/* Set function name */
		$t = $t->copy($ctx, ["current_function"=>$op_code->fn_create]);
		/* Clear save op codes */
		$t = $t::clearSaveOpCode($ctx, $t);
		$open .= \Runtime\rtl::toStr($t->s($ctx, "function __construct("));
		$res = $t->operator::OpDeclareFunctionArgs($ctx, $t, $op_code->fn_create);
		$t = $res[0];
		$open .= \Runtime\rtl::toStr($res[1]);
		$open .= \Runtime\rtl::toStr(")");
		$open .= \Runtime\rtl::toStr($t->s($ctx, "{"));
		$t = $t->levelInc($ctx);
		/* Function body */
		$res = $t->operator::Operators($ctx, $t, ($op_code->fn_create->expression) ? $op_code->fn_create->expression : $op_code->fn_create->value);
		$t = $res[0];
		$content .= \Runtime\rtl::toStr($res[1]);
		/* Constructor end */
		$save = $t::outputSaveOpCode($ctx, $t);
		if ($save != "")
		{
			$content = $open . \Runtime\rtl::toStr($t->s($ctx, $save . \Runtime\rtl::toStr($content)));
		}
		else
		{
			$content = $open . \Runtime\rtl::toStr($content);
		}
		$t = $t->levelDec($ctx);
		$content .= \Runtime\rtl::toStr($t->s($ctx, "}"));
		return \Runtime\Collection::from([$save_t,$content]);
	}
	/**
	 * OpDeclareClass
	 */
	static function OpDeclareClassBody($ctx, $t, $op_code)
	{
		$content = "";
		$class_kind = $op_code->kind;
		$save_op_codes = $t->save_op_codes;
		$save_op_code_inc = $t->save_op_code_inc;
		$t = $t::clearSaveOpCode($ctx, $t);
		$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
		$t = $t->levelInc($ctx);
		/* Static variables */
		if ($class_kind != \Bayrell\Lang\OpCodes\OpDeclareClass::KIND_INTERFACE && $op_code->vars != null)
		{
			for ($i = 0;$i < $op_code->vars->count($ctx);$i++)
			{
				$variable = $op_code->vars->item($ctx, $i);
				if ($variable->kind != \Bayrell\Lang\OpCodes\OpAssign::KIND_DECLARE)
				{
					continue;
				}
				$is_static = $variable->flags->isFlag($ctx, "static");
				$is_const = $variable->flags->isFlag($ctx, "const");
				for ($j = 0;$j < $variable->values->count($ctx);$j++)
				{
					$value = $variable->values->item($ctx, $j);
					$res = $t->expression::Expression($ctx, $t, $value->expression);
					$s = ($value->expression != null) ? $res[1] : "null";
					if ($is_static && $is_const)
					{
						$content .= \Runtime\rtl::toStr($t->s($ctx, "const " . \Runtime\rtl::toStr($value->var_name) . \Runtime\rtl::toStr("=") . \Runtime\rtl::toStr($s) . \Runtime\rtl::toStr(";")));
					}
					else if ($is_static)
					{
						$content .= \Runtime\rtl::toStr($t->s($ctx, "static $" . \Runtime\rtl::toStr($value->var_name) . \Runtime\rtl::toStr("=") . \Runtime\rtl::toStr($s) . \Runtime\rtl::toStr(";")));
					}
					else if ($class_kind == \Bayrell\Lang\OpCodes\OpDeclareClass::KIND_STRUCT)
					{
						$content .= \Runtime\rtl::toStr($t->s($ctx, "public $__" . \Runtime\rtl::toStr($value->var_name) . \Runtime\rtl::toStr(";")));
					}
					else
					{
						$content .= \Runtime\rtl::toStr($t->s($ctx, "public $" . \Runtime\rtl::toStr($value->var_name) . \Runtime\rtl::toStr(";")));
					}
				}
			}
		}
		/* Constructor */
		if ($class_kind != \Bayrell\Lang\OpCodes\OpDeclareClass::KIND_INTERFACE)
		{
			$res = static::OpDeclareClassConstructor($ctx, $t, $op_code);
			$content .= \Runtime\rtl::toStr($res[1]);
		}
		/* Functions */
		if ($op_code->functions != null)
		{
			for ($i = 0;$i < $op_code->functions->count($ctx);$i++)
			{
				$f = $op_code->functions->item($ctx, $i);
				$res = static::OpDeclareFunction($ctx, $t, $f);
				$t = $res[0];
				$content .= \Runtime\rtl::toStr($res[1]);
			}
		}
		/* Class items */
		for ($i = 0;$i < $op_code->items->count($ctx);$i++)
		{
			$item = $op_code->items->item($ctx, $i);
			if ($item instanceof \Bayrell\Lang\OpCodes\OpPreprocessorIfCode)
			{
				$res = $t->operator::OpPreprocessorIfCode($ctx, $t, $item);
				$content .= \Runtime\rtl::toStr($res[1]);
			}
			else if ($item instanceof \Bayrell\Lang\OpCodes\OpPreprocessorIfDef)
			{
				$res = $t->operator::OpPreprocessorIfDef($ctx, $t, $item, \Bayrell\Lang\OpCodes\OpPreprocessorIfDef::KIND_CLASS_BODY);
				$content .= \Runtime\rtl::toStr($res[1]);
			}
			else if ($item instanceof \Bayrell\Lang\OpCodes\OpPreprocessorSwitch)
			{
				for ($j = 0;$j < $item->items->count($ctx);$j++)
				{
					$res = $t->operator::OpPreprocessorIfCode($ctx, $t, $item->items->item($ctx, $j));
					$s = $res[1];
					if ($s == "")
					{
						continue;
					}
					$content .= \Runtime\rtl::toStr($res[1]);
				}
			}
		}
		if ($class_kind != \Bayrell\Lang\OpCodes\OpDeclareClass::KIND_INTERFACE)
		{
			$content .= \Runtime\rtl::toStr($t->s($ctx, "/* ======================= Class Init Functions ======================= */"));
		}
		/* Init variables */
		if ($class_kind != \Bayrell\Lang\OpCodes\OpDeclareClass::KIND_INTERFACE && $op_code->vars != null)
		{
			$vars = $op_code->vars->filter($ctx, function ($ctx, $variable)
			{
				return !$variable->flags->isFlag($ctx, "static");
			});
			if ($t->current_class_full_name != "Runtime.CoreObject" && $vars->count($ctx) > 0)
			{
				$content .= \Runtime\rtl::toStr($t->s($ctx, "function _init($ctx)"));
				$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
				$t = $t->levelInc($ctx);
				if ($t->current_class_extends_name != "")
				{
					$content .= \Runtime\rtl::toStr($t->s($ctx, "parent::_init($ctx);"));
				}
				for ($i = 0;$i < $op_code->vars->count($ctx);$i++)
				{
					$variable = $op_code->vars->item($ctx, $i);
					$is_static = $variable->flags->isFlag($ctx, "static");
					if ($is_static)
					{
						continue;
					}
					if ($variable->kind != \Bayrell\Lang\OpCodes\OpAssign::KIND_DECLARE)
					{
						continue;
					}
					$prefix = "";
					if ($class_kind == \Bayrell\Lang\OpCodes\OpDeclareClass::KIND_STRUCT)
					{
						$prefix = "__";
					}
					else if ($class_kind == \Bayrell\Lang\OpCodes\OpDeclareClass::KIND_CLASS)
					{
						$prefix = "";
					}
					for ($j = 0;$j < $variable->values->count($ctx);$j++)
					{
						$value = $variable->values->item($ctx, $j);
						$res = $t->expression::Expression($ctx, $t, $value->expression);
						$s = ($value->expression != null) ? $res[1] : "null";
						$content .= \Runtime\rtl::toStr($t->s($ctx, "$this->" . \Runtime\rtl::toStr($prefix) . \Runtime\rtl::toStr($value->var_name) . \Runtime\rtl::toStr(" = ") . \Runtime\rtl::toStr($s) . \Runtime\rtl::toStr(";")));
					}
				}
				$t = $t->levelDec($ctx);
				$content .= \Runtime\rtl::toStr($t->s($ctx, "}"));
			}
			/* Struct */
			if ($class_kind == \Bayrell\Lang\OpCodes\OpDeclareClass::KIND_STRUCT)
			{
				/* Assign Object */
				$content .= \Runtime\rtl::toStr($t->s($ctx, "function assignObject($ctx,$o)"));
				$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
				$t = $t->levelInc($ctx);
				$content .= \Runtime\rtl::toStr($t->s($ctx, "if ($o instanceof \\" . \Runtime\rtl::toStr(\Runtime\rs::replace($ctx, "\\.", "\\", $t->current_class_full_name)) . \Runtime\rtl::toStr(")")));
				$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
				$t = $t->levelInc($ctx);
				for ($i = 0;$i < $op_code->vars->count($ctx);$i++)
				{
					$variable = $op_code->vars->item($ctx, $i);
					if ($variable->kind != \Bayrell\Lang\OpCodes\OpAssign::KIND_DECLARE)
					{
						continue;
					}
					$is_const = $variable->flags->isFlag($ctx, "const");
					$is_static = $variable->flags->isFlag($ctx, "static");
					if ($is_const || $is_static)
					{
						continue;
					}
					for ($j = 0;$j < $variable->values->count($ctx);$j++)
					{
						$value = $variable->values->item($ctx, $j);
						$content .= \Runtime\rtl::toStr($t->s($ctx, "$this->__" . \Runtime\rtl::toStr($value->var_name) . \Runtime\rtl::toStr(" = $o->__") . \Runtime\rtl::toStr($value->var_name) . \Runtime\rtl::toStr(";")));
					}
				}
				$t = $t->levelDec($ctx);
				$content .= \Runtime\rtl::toStr($t->s($ctx, "}"));
				$content .= \Runtime\rtl::toStr($t->s($ctx, "parent::assignObject($ctx,$o);"));
				$t = $t->levelDec($ctx);
				$content .= \Runtime\rtl::toStr($t->s($ctx, "}"));
				/* Assign Value */
				$content .= \Runtime\rtl::toStr($t->s($ctx, "function assignValue($ctx,$k,$v)"));
				$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
				$t = $t->levelInc($ctx);
				$flag = false;
				for ($i = 0;$i < $op_code->vars->count($ctx);$i++)
				{
					$variable = $op_code->vars->item($ctx, $i);
					if ($variable->kind != \Bayrell\Lang\OpCodes\OpAssign::KIND_DECLARE)
					{
						continue;
					}
					$is_const = $variable->flags->isFlag($ctx, "const");
					$is_static = $variable->flags->isFlag($ctx, "static");
					if ($is_const || $is_static)
					{
						continue;
					}
					for ($j = 0;$j < $variable->values->count($ctx);$j++)
					{
						$value = $variable->values->item($ctx, $j);
						if ($t->flag_struct_check_types)
						{
							$content .= \Runtime\rtl::toStr($t->s($ctx, (($flag) ? "else " : "") . \Runtime\rtl::toStr("if ($k == ") . \Runtime\rtl::toStr($t->expression::toString($ctx, $value->var_name)) . \Runtime\rtl::toStr(")") . \Runtime\rtl::toStr("$this->__") . \Runtime\rtl::toStr($value->var_name) . \Runtime\rtl::toStr(" = Runtime.rtl.to($v, null, ") . \Runtime\rtl::toStr(static::toPattern($ctx, $t, $variable->pattern)) . \Runtime\rtl::toStr(");")));
						}
						else
						{
							$content .= \Runtime\rtl::toStr($t->s($ctx, (($flag) ? "else " : "") . \Runtime\rtl::toStr("if ($k == ") . \Runtime\rtl::toStr($t->expression::toString($ctx, $value->var_name)) . \Runtime\rtl::toStr(")") . \Runtime\rtl::toStr("$this->__") . \Runtime\rtl::toStr($value->var_name) . \Runtime\rtl::toStr(" = $v;")));
						}
						$flag = true;
					}
				}
				$content .= \Runtime\rtl::toStr($t->s($ctx, (($flag) ? "else " : "") . \Runtime\rtl::toStr("parent::assignValue($ctx,$k,$v);")));
				$t = $t->levelDec($ctx);
				$content .= \Runtime\rtl::toStr($t->s($ctx, "}"));
				/* Take Value */
				$content .= \Runtime\rtl::toStr($t->s($ctx, "function takeValue($ctx,$k,$d=null)"));
				$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
				$t = $t->levelInc($ctx);
				$flag = false;
				for ($i = 0;$i < $op_code->vars->count($ctx);$i++)
				{
					$variable = $op_code->vars->item($ctx, $i);
					if ($variable->kind != \Bayrell\Lang\OpCodes\OpAssign::KIND_DECLARE)
					{
						continue;
					}
					$is_const = $variable->flags->isFlag($ctx, "const");
					$is_static = $variable->flags->isFlag($ctx, "static");
					if ($is_const || $is_static)
					{
						continue;
					}
					for ($j = 0;$j < $variable->values->count($ctx);$j++)
					{
						$value = $variable->values->item($ctx, $j);
						$content .= \Runtime\rtl::toStr($t->s($ctx, (($flag) ? "else " : "") . \Runtime\rtl::toStr("if ($k == ") . \Runtime\rtl::toStr($t->expression::toString($ctx, $value->var_name)) . \Runtime\rtl::toStr(")return $this->__") . \Runtime\rtl::toStr($value->var_name) . \Runtime\rtl::toStr(";")));
						$flag = true;
					}
				}
				$content .= \Runtime\rtl::toStr($t->s($ctx, "return parent::takeValue($ctx,$k,$d);"));
				$t = $t->levelDec($ctx);
				$content .= \Runtime\rtl::toStr($t->s($ctx, "}"));
			}
		}
		if ($class_kind != \Bayrell\Lang\OpCodes\OpDeclareClass::KIND_INTERFACE)
		{
			/* Get class name function */
			$content .= \Runtime\rtl::toStr($t->s($ctx, "function getClassName()"));
			$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
			$t = $t->levelInc($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "return " . \Runtime\rtl::toStr($t->expression::toString($ctx, $t->current_class_full_name)) . \Runtime\rtl::toStr(";")));
			$t = $t->levelDec($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "}"));
			/* Get current namespace function */
			$content .= \Runtime\rtl::toStr($t->s($ctx, "static function getCurrentNamespace()"));
			$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
			$t = $t->levelInc($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "return " . \Runtime\rtl::toStr($t->expression::toString($ctx, $t->current_namespace_name)) . \Runtime\rtl::toStr(";")));
			$t = $t->levelDec($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "}"));
			/* Get current class name function */
			$content .= \Runtime\rtl::toStr($t->s($ctx, "static function getCurrentClassName()"));
			$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
			$t = $t->levelInc($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "return " . \Runtime\rtl::toStr($t->expression::toString($ctx, $t->current_class_full_name)) . \Runtime\rtl::toStr(";")));
			$t = $t->levelDec($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "}"));
			/* Get parent class name function */
			$content .= \Runtime\rtl::toStr($t->s($ctx, "static function getParentClassName()"));
			$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
			$t = $t->levelInc($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "return " . \Runtime\rtl::toStr($t->expression::toString($ctx, $t->expression::findModuleName($ctx, $t, $t->current_class_extends_name))) . \Runtime\rtl::toStr(";")));
			$t = $t->levelDec($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "}"));
			/* Class info */
			$content .= \Runtime\rtl::toStr($t->s($ctx, "static function getClassInfo($ctx)"));
			$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
			$t = $t->levelInc($ctx);
			$t = $t::clearSaveOpCode($ctx, $t);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "return new \\Runtime\\Annotations\\IntrospectionInfo($ctx, ["));
			$t = $t->levelInc($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "\"kind\"=>\\Runtime\\Annotations\\IntrospectionInfo::ITEM_CLASS,"));
			$content .= \Runtime\rtl::toStr($t->s($ctx, "\"class_name\"=>" . \Runtime\rtl::toStr($t->expression::toString($ctx, $t->current_class_full_name)) . \Runtime\rtl::toStr(",")));
			$content .= \Runtime\rtl::toStr($t->s($ctx, "\"name\"=>" . \Runtime\rtl::toStr($t->expression::toString($ctx, $t->current_class_full_name)) . \Runtime\rtl::toStr(",")));
			$content .= \Runtime\rtl::toStr($t->s($ctx, "\"annotations\"=>\\Runtime\\Collection::from(["));
			$t = $t->levelInc($ctx);
			for ($j = 0;$j < $op_code->annotations->count($ctx);$j++)
			{
				$annotation = $op_code->annotations->item($ctx, $j);
				$res = $t->expression::OpTypeIdentifier($ctx, $t, $annotation->name);
				$t = $res[0];
				$name = $res[1];
				$res = $t->expression::OpDict($ctx, $t, $annotation->params, true);
				$t = $res[0];
				$params = $res[1];
				$content .= \Runtime\rtl::toStr($t->s($ctx, "new " . \Runtime\rtl::toStr($name) . \Runtime\rtl::toStr("($ctx, ") . \Runtime\rtl::toStr($params) . \Runtime\rtl::toStr("),")));
			}
			$t = $t->levelDec($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "]),"));
			$t = $t->levelDec($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "]);"));
			$t = $t->levelDec($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "}"));
			/* Get fields list of the function */
			$content .= \Runtime\rtl::toStr($t->s($ctx, "static function getFieldsList($ctx,$f)"));
			$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
			$t = $t->levelInc($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "$a = [];"));
			if ($op_code->vars != null)
			{
				$vars = new \Runtime\Map($ctx);
				for ($i = 0;$i < $op_code->vars->count($ctx);$i++)
				{
					$variable = $op_code->vars->item($ctx, $i);
					$is_static = $variable->flags->isFlag($ctx, "static");
					$is_serializable = $variable->flags->isFlag($ctx, "serializable");
					$is_assignable = $variable->flags->isFlag($ctx, "assignable");
					$has_annotation = $variable->annotations != null && $variable->annotations->count($ctx) > 0;
					if ($is_static)
					{
						continue;
					}
					if ($variable->kind != \Bayrell\Lang\OpCodes\OpAssign::KIND_DECLARE)
					{
						continue;
					}
					if ($class_kind == \Bayrell\Lang\OpCodes\OpDeclareClass::KIND_STRUCT)
					{
						$is_serializable = true;
						$is_assignable = true;
					}
					if ($is_serializable)
					{
						$is_assignable = true;
					}
					$flag = 0;
					if ($is_serializable)
					{
						$flag = $flag | 1;
					}
					if ($is_assignable)
					{
						$flag = $flag | 2;
					}
					if ($has_annotation)
					{
						$flag = $flag | 4;
					}
					if ($flag != 0)
					{
						if (!$vars->has($ctx, $flag))
						{
							$vars->set($ctx, $flag, new \Runtime\Vector($ctx));
						}
						$v = $vars->item($ctx, $flag);
						for ($j = 0;$j < $variable->values->count($ctx);$j++)
						{
							$value = $variable->values->item($ctx, $j);
							$v->push($ctx, $value->var_name);
						}
					}
				}
				$vars->each($ctx, function ($ctx, $v, $flag) use (&$t,&$content)
				{
					$content .= \Runtime\rtl::toStr($t->s($ctx, "if (($f|" . \Runtime\rtl::toStr($flag) . \Runtime\rtl::toStr(")==") . \Runtime\rtl::toStr($flag) . \Runtime\rtl::toStr(")")));
					$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
					$t = $t->levelInc($ctx);
					$v->each($ctx, function ($ctx, $varname) use (&$t,&$content)
					{
						$content .= \Runtime\rtl::toStr($t->s($ctx, "$a[] = " . \Runtime\rtl::toStr($t->expression::toString($ctx, $varname)) . \Runtime\rtl::toStr(";")));
					});
					$t = $t->levelDec($ctx);
					$content .= \Runtime\rtl::toStr($t->s($ctx, "}"));
				});
			}
			$content .= \Runtime\rtl::toStr($t->s($ctx, "return " . \Runtime\rtl::toStr($t->expression::getModuleName($ctx, $t, "Runtime.Collection")) . \Runtime\rtl::toStr("::from($a);")));
			$t = $t->levelDec($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "}"));
			/* Get field info by name */
			$content .= \Runtime\rtl::toStr($t->s($ctx, "static function getFieldInfoByName($ctx,$field_name)"));
			$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
			$t = $t->levelInc($ctx);
			if ($op_code->vars != null)
			{
				for ($i = 0;$i < $op_code->vars->count($ctx);$i++)
				{
					$variable = $op_code->vars->item($ctx, $i);
					$v = $variable->values->map($ctx, function ($ctx, $value)
					{
						return $value->var_name;
					});
					$v = $v->map($ctx, function ($ctx, $var_name) use (&$t)
					{
						return "$field_name == " . \Runtime\rtl::toStr($t->expression::toString($ctx, $var_name));
					});
					$t = $t::clearSaveOpCode($ctx, $t);
					$content .= \Runtime\rtl::toStr($t->s($ctx, "if (" . \Runtime\rtl::toStr(\Runtime\rs::join($ctx, " or ", $v)) . \Runtime\rtl::toStr(") ") . \Runtime\rtl::toStr("return new \\Runtime\\Annotations\\IntrospectionInfo($ctx, [")));
					$t = $t->levelInc($ctx);
					$content .= \Runtime\rtl::toStr($t->s($ctx, "\"kind\"=>\\Runtime\\Annotations\\IntrospectionInfo::ITEM_FIELD,"));
					$content .= \Runtime\rtl::toStr($t->s($ctx, "\"class_name\"=>" . \Runtime\rtl::toStr($t->expression::toString($ctx, $t->current_class_full_name)) . \Runtime\rtl::toStr(",")));
					$content .= \Runtime\rtl::toStr($t->s($ctx, "\"name\"=> $field_name,"));
					$content .= \Runtime\rtl::toStr($t->s($ctx, "\"annotations\"=>\\Runtime\\Collection::from(["));
					$t = $t->levelInc($ctx);
					for ($j = 0;$j < $variable->annotations->count($ctx);$j++)
					{
						$annotation = $variable->annotations->item($ctx, $j);
						$res = $t->expression::OpTypeIdentifier($ctx, $t, $annotation->name);
						$t = $res[0];
						$name = $res[1];
						$res = $t->expression::OpDict($ctx, $t, $annotation->params, true);
						$t = $res[0];
						$params = $res[1];
						$content .= \Runtime\rtl::toStr($t->s($ctx, "new " . \Runtime\rtl::toStr($name) . \Runtime\rtl::toStr("($ctx, ") . \Runtime\rtl::toStr($params) . \Runtime\rtl::toStr("),")));
					}
					$t = $t->levelDec($ctx);
					$content .= \Runtime\rtl::toStr($t->s($ctx, "]),"));
					$t = $t->levelDec($ctx);
					$content .= \Runtime\rtl::toStr($t->s($ctx, "]);"));
				}
			}
			$content .= \Runtime\rtl::toStr($t->s($ctx, "return null;"));
			$t = $t->levelDec($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "}"));
			/* Get methods list of the function */
			$content .= \Runtime\rtl::toStr($t->s($ctx, "static function getMethodsList($ctx)"));
			$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
			$t = $t->levelInc($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "$a = ["));
			$t = $t->levelInc($ctx);
			if ($op_code->functions != null)
			{
				for ($i = 0;$i < $op_code->functions->count($ctx);$i++)
				{
					$f = $op_code->functions->item($ctx, $i);
					if ($f->flags->isFlag($ctx, "declare"))
					{
						continue;
					}
					if ($f->annotations->count($ctx) == 0)
					{
						continue;
					}
					$content .= \Runtime\rtl::toStr($t->s($ctx, $t->expression::toString($ctx, $f->name) . \Runtime\rtl::toStr(",")));
				}
			}
			if ($op_code->items != null)
			{
				for ($i = 0;$i < $op_code->items->count($ctx);$i++)
				{
					$item = $op_code->items->item($ctx, $i);
					$res = static::OpClassBodyItemMethodsList($ctx, $t, $item);
					$t = $res[0];
					$content .= \Runtime\rtl::toStr($res[1]);
				}
			}
			$t = $t->levelDec($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "];"));
			$content .= \Runtime\rtl::toStr($t->s($ctx, "return " . \Runtime\rtl::toStr($t->expression::getModuleName($ctx, $t, "Runtime.Collection")) . \Runtime\rtl::toStr("::from($a);")));
			$t = $t->levelDec($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "}"));
			/* Get method info by name */
			$content .= \Runtime\rtl::toStr($t->s($ctx, "static function getMethodInfoByName($ctx,$field_name)"));
			$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
			$t = $t->levelInc($ctx);
			if ($op_code->functions != null)
			{
				for ($i = 0;$i < $op_code->functions->count($ctx);$i++)
				{
					$f = $op_code->functions->item($ctx, $i);
					$res = static::OpFunctionAnnotations($ctx, $t, $f);
					$t = $res[0];
					$content .= \Runtime\rtl::toStr($res[1]);
				}
			}
			if ($op_code->items != null)
			{
				for ($i = 0;$i < $op_code->items->count($ctx);$i++)
				{
					$item = $op_code->items->item($ctx, $i);
					$res = static::OpClassBodyItemAnnotations($ctx, $t, $item);
					$t = $res[0];
					$content .= \Runtime\rtl::toStr($res[1]);
				}
			}
			$content .= \Runtime\rtl::toStr($t->s($ctx, "return null;"));
			$t = $t->levelDec($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "}"));
		}
		$t = $t->levelDec($ctx);
		$content .= \Runtime\rtl::toStr($t->s($ctx, "}"));
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpDeclareClassFooter
	 */
	static function OpDeclareClassFooter($ctx, $t, $op_code)
	{
		$content = "";
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpDeclareClass
	 */
	static function OpDeclareClass($ctx, $t, $op_code)
	{
		if ($op_code->is_declare)
		{
			return \Runtime\Collection::from([$t,""]);
		}
		$content = "";
		$t = $t->copy($ctx, ["current_class"=>$op_code]);
		$t = $t->copy($ctx, ["current_class_name"=>$op_code->name]);
		$t = $t->copy($ctx, ["current_class_full_name"=>$t->current_namespace_name . \Runtime\rtl::toStr(".") . \Runtime\rtl::toStr($t->current_class_name)]);
		if ($op_code->class_extends != null)
		{
			$extends_name = \Runtime\rs::join($ctx, ".", $op_code->class_extends->entity_name->names);
			$t = $t->copy($ctx, ["current_class_extends_name"=>$extends_name]);
		}
		else if ($op_code->kind == \Bayrell\Lang\OpCodes\OpDeclareClass::KIND_STRUCT)
		{
			$t = $t->copy($ctx, ["current_class_extends_name"=>"Runtime.CoreStruct"]);
		}
		else if ($op_code->kind == \Bayrell\Lang\OpCodes\OpDeclareClass::KIND_STRUCT)
		{
			$t = $t->copy($ctx, ["current_class_extends_name"=>""]);
		}
		if ($op_code->kind != \Bayrell\Lang\OpCodes\OpDeclareClass::KIND_INTERFACE)
		{
			if ($op_code->class_extends != null)
			{
				$content = "class " . \Runtime\rtl::toStr($t->current_class_name) . \Runtime\rtl::toStr(" extends ") . \Runtime\rtl::toStr($t->expression::getModuleName($ctx, $t, $t->current_class_extends_name));
			}
			else
			{
				$content = "class " . \Runtime\rtl::toStr($t->current_class_name);
			}
		}
		else
		{
			$content = "interface " . \Runtime\rtl::toStr($t->current_class_name);
		}
		/* Add implements */
		if ($op_code->class_implements != null && $op_code->class_implements->count($ctx) > 0)
		{
			$arr = $op_code->class_implements->map($ctx, function ($ctx, $item) use (&$t)
			{
				return $t->expression::getModuleNames($ctx, $t, $item->entity_name->names);
			});
			$s1 = \Runtime\rs::join($ctx, ", ", $arr);
			$content .= \Runtime\rtl::toStr(" implements " . \Runtime\rtl::toStr($s1));
		}
		/* Class body */
		$res = static::OpDeclareClassBody($ctx, $t, $op_code);
		$content .= \Runtime\rtl::toStr($res[1]);
		/* Class comments */
		$res = $t->operator::AddComments($ctx, $t, $op_code->comments, $content);
		$content = $res[1];
		/* Class footer */
		$res = static::OpDeclareClassFooter($ctx, $t, $op_code);
		$content .= \Runtime\rtl::toStr($res[1]);
		return \Runtime\Collection::from([$t,$t->s($ctx, $content)]);
	}
	/**
	 * Translate item
	 */
	static function translateItem($ctx, $t, $op_code)
	{
		if ($op_code instanceof \Bayrell\Lang\OpCodes\OpNamespace)
		{
			return static::OpNamespace($ctx, $t, $op_code);
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpDeclareClass)
		{
			return static::OpDeclareClass($ctx, $t, $op_code);
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpComment)
		{
			return $t->operator::OpComment($ctx, $t, $op_code);
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpPreprocessorIfCode)
		{
			return $t->operator::OpPreprocessorIfCode($ctx, $t, $op_code);
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpPreprocessorSwitch)
		{
			$content = "";
			for ($i = 0;$i < $op_code->items->count($ctx);$i++)
			{
				$res = $t->operator::OpPreprocessorIfCode($ctx, $t, $op_code->items->item($ctx, $i));
				$s = $res[1];
				if ($s == "")
				{
					continue;
				}
				$content .= \Runtime\rtl::toStr($s);
			}
			return \Runtime\Collection::from([$t,$content]);
		}
		return \Runtime\Collection::from([$t,""]);
	}
	/**
	 * Translate program
	 */
	static function translateProgramHeader($ctx, $t, $op_code)
	{
		$content = "<?php";
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * Translate program
	 */
	static function translateProgram($ctx, $t, $op_code)
	{
		$content = "";
		if ($op_code == null)
		{
			return \Runtime\Collection::from([$t,$content]);
		}
		if ($op_code->uses != null)
		{
			$t = $t->copy($ctx, ["modules"=>$op_code->uses]);
		}
		if ($op_code->items != null)
		{
			$res = static::translateProgramHeader($ctx, $t, $op_code);
			$content .= \Runtime\rtl::toStr($res[1]);
			for ($i = 0;$i < $op_code->items->count($ctx);$i++)
			{
				$item = $op_code->items->item($ctx, $i);
				$res = static::translateItem($ctx, $t, $item);
				$t = $res[0];
				$s = $res[1];
				if ($s == "")
				{
					continue;
				}
				$content .= \Runtime\rtl::toStr($s);
			}
		}
		return \Runtime\Collection::from([$t,$content]);
	}
	/* ======================= Class Init Functions ======================= */
	function getClassName()
	{
		return "Bayrell.Lang.LangPHP.TranslatorPHPProgram";
	}
	static function getCurrentNamespace()
	{
		return "Bayrell.Lang.LangPHP";
	}
	static function getCurrentClassName()
	{
		return "Bayrell.Lang.LangPHP.TranslatorPHPProgram";
	}
	static function getParentClassName()
	{
		return "";
	}
	static function getClassInfo($ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.LangPHP.TranslatorPHPProgram",
			"name"=>"Bayrell.Lang.LangPHP.TranslatorPHPProgram",
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