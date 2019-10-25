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
class TranslatorPHPProgram
{
	/**
	 * OpNamespace
	 */
	static function OpNamespace($__ctx, $t, $op_code)
	{
		$arr = \Runtime\rs::split($__ctx, "\\.", $op_code->name);
		$t = $t->copy($__ctx, ["current_namespace_name"=>$op_code->name]);
		return \Runtime\Collection::from([$t,$t->s($__ctx, "namespace " . \Runtime\rtl::toStr(\Runtime\rs::join($__ctx, "\\", $arr)) . \Runtime\rtl::toStr(";"))]);
	}
	/**
	 * OpDeclareClass
	 */
	static function OpDeclareClassConstructor($__ctx, $t, $op_code)
	{
		if ($op_code->fn_create == null)
		{
			return \Runtime\Collection::from([$t,""]);
		}
		$open = "";
		$content = "";
		$save_t = $t;
		/* Set function name */
		$t = $t->copy($__ctx, ["current_function"=>$op_code->fn_create]);
		/* Clear save op codes */
		$t = $t->staticMethod("clearSaveOpCode")($__ctx, $t);
		$open .= \Runtime\rtl::toStr($t->s($__ctx, "function __construct("));
		$res = $t->operator->staticMethod("OpDeclareFunctionArgs")($__ctx, $t, $op_code->fn_create);
		$t = $res[0];
		$open .= \Runtime\rtl::toStr($res[1]);
		$open .= \Runtime\rtl::toStr(")");
		$open .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
		$t = $t->levelInc($__ctx);
		/* Function body */
		$res = $t->operator->staticMethod("Operators")($__ctx, $t, ($op_code->fn_create->expression) ? $op_code->fn_create->expression : $op_code->fn_create->value);
		$t = $res[0];
		$content .= \Runtime\rtl::toStr($res[1]);
		/* Constructor end */
		$save = $t->staticMethod("outputSaveOpCode")($__ctx, $t);
		if ($save != "")
		{
			$content = $open . \Runtime\rtl::toStr($t->s($__ctx, $save . \Runtime\rtl::toStr($content)));
		}
		else
		{
			$content = $open . \Runtime\rtl::toStr($content);
		}
		$t = $t->levelDec($__ctx);
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
		return \Runtime\Collection::from([$save_t,$content]);
	}
	/**
	 * OpDeclareClass
	 */
	static function OpDeclareClassBody($__ctx, $t, $op_code)
	{
		$content = "";
		$class_kind = $op_code->kind;
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
		$t = $t->levelInc($__ctx);
		/* Static variables */
		if ($class_kind != \Bayrell\Lang\OpCodes\OpDeclareClass::KIND_INTERFACE && $op_code->vars != null)
		{
			for ($i = 0;$i < $op_code->vars->count($__ctx);$i++)
			{
				$variable = $op_code->vars->item($__ctx, $i);
				if ($variable->kind != \Bayrell\Lang\OpCodes\OpAssign::KIND_DECLARE)
				{
					continue;
				}
				$is_static = $variable->flags->isFlag($__ctx, "static");
				$is_const = $variable->flags->isFlag($__ctx, "const");
				for ($j = 0;$j < $variable->values->count($__ctx);$j++)
				{
					$value = $variable->values->item($__ctx, $j);
					$res = $t->expression->staticMethod("Expression")($__ctx, $t, $value->expression);
					$s = ($value->expression != null) ? $res[1] : "null";
					if ($is_static && $is_const)
					{
						$content .= \Runtime\rtl::toStr($t->s($__ctx, "const " . \Runtime\rtl::toStr($value->var_name) . \Runtime\rtl::toStr("=") . \Runtime\rtl::toStr($s) . \Runtime\rtl::toStr(";")));
					}
					else if ($is_static)
					{
						$content .= \Runtime\rtl::toStr($t->s($__ctx, "static $" . \Runtime\rtl::toStr($value->var_name) . \Runtime\rtl::toStr("=") . \Runtime\rtl::toStr($s) . \Runtime\rtl::toStr(";")));
					}
					else if ($class_kind == \Bayrell\Lang\OpCodes\OpDeclareClass::KIND_STRUCT)
					{
						$content .= \Runtime\rtl::toStr($t->s($__ctx, "public $__" . \Runtime\rtl::toStr($value->var_name) . \Runtime\rtl::toStr(";")));
					}
					else
					{
						$content .= \Runtime\rtl::toStr($t->s($__ctx, "public $" . \Runtime\rtl::toStr($value->var_name) . \Runtime\rtl::toStr(";")));
					}
				}
			}
		}
		/* Constructor */
		if ($class_kind != \Bayrell\Lang\OpCodes\OpDeclareClass::KIND_INTERFACE)
		{
			$res = static::OpDeclareClassConstructor($__ctx, $t, $op_code);
			$content .= \Runtime\rtl::toStr($res[1]);
		}
		/* Functions */
		if ($op_code->functions != null)
		{
			for ($i = 0;$i < $op_code->functions->count($__ctx);$i++)
			{
				$f = $op_code->functions->item($__ctx, $i);
				if ($f->flags->isFlag($__ctx, "declare"))
				{
					continue;
				}
				/* Set function name */
				$t = $t->copy($__ctx, ["current_function"=>$f]);
				$s1 = "";
				$s2 = "";
				if ($f->isStatic($__ctx))
				{
					$s1 .= \Runtime\rtl::toStr("static ");
					$t = $t->copy($__ctx, ["is_static_function"=>true]);
				}
				else
				{
					$t = $t->copy($__ctx, ["is_static_function"=>false]);
				}
				$res = $t->operator->staticMethod("OpDeclareFunctionArgs")($__ctx, $t, $f);
				$args = $res[1];
				$s1 .= \Runtime\rtl::toStr("function " . \Runtime\rtl::toStr($f->name) . \Runtime\rtl::toStr("(") . \Runtime\rtl::toStr($args) . \Runtime\rtl::toStr(")"));
				if ($class_kind != \Bayrell\Lang\OpCodes\OpDeclareClass::KIND_INTERFACE)
				{
					$res = $t->operator->staticMethod("OpDeclareFunctionBody")($__ctx, $t, $f);
					$s2 .= \Runtime\rtl::toStr($res[1]);
				}
				else
				{
					$s2 .= \Runtime\rtl::toStr(";");
				}
				$s1 = $t->s($__ctx, $s1);
				/* Function comments */
				$res = $t->operator->staticMethod("AddComments")($__ctx, $t, $f->comments, $s1 . \Runtime\rtl::toStr($s2));
				$content .= \Runtime\rtl::toStr($res[1]);
			}
		}
		if ($class_kind != \Bayrell\Lang\OpCodes\OpDeclareClass::KIND_INTERFACE)
		{
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "/* ======================= Class Init Functions ======================= */"));
		}
		/* Init variables */
		if ($class_kind != \Bayrell\Lang\OpCodes\OpDeclareClass::KIND_INTERFACE && $op_code->vars != null)
		{
			$vars = $op_code->vars->filter($__ctx, function ($__ctx, $variable)
			{
				return !$variable->flags->isFlag($__ctx, "static");
			});
			if ($t->current_class_full_name != "Runtime.CoreObject" && $vars->count($__ctx) > 0)
			{
				$content .= \Runtime\rtl::toStr($t->s($__ctx, "function _init($__ctx)"));
				$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
				$t = $t->levelInc($__ctx);
				if ($t->current_class_extends_name != "")
				{
					$content .= \Runtime\rtl::toStr($t->s($__ctx, "parent::_init($__ctx);"));
				}
				for ($i = 0;$i < $op_code->vars->count($__ctx);$i++)
				{
					$variable = $op_code->vars->item($__ctx, $i);
					$is_static = $variable->flags->isFlag($__ctx, "static");
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
					for ($j = 0;$j < $variable->values->count($__ctx);$j++)
					{
						$value = $variable->values->item($__ctx, $j);
						$res = $t->expression->staticMethod("Expression")($__ctx, $t, $value->expression);
						$s = ($value->expression != null) ? $res[1] : "null";
						$content .= \Runtime\rtl::toStr($t->s($__ctx, "$this->" . \Runtime\rtl::toStr($prefix) . \Runtime\rtl::toStr($value->var_name) . \Runtime\rtl::toStr(" = ") . \Runtime\rtl::toStr($s) . \Runtime\rtl::toStr(";")));
					}
				}
				$t = $t->levelDec($__ctx);
				$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
			}
			/* Struct */
			if ($class_kind == \Bayrell\Lang\OpCodes\OpDeclareClass::KIND_STRUCT)
			{
				/* Assign Object */
				$content .= \Runtime\rtl::toStr($t->s($__ctx, "function assignObject($__ctx,$o)"));
				$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
				$t = $t->levelInc($__ctx);
				$content .= \Runtime\rtl::toStr($t->s($__ctx, "if ($o instanceof \\" . \Runtime\rtl::toStr(\Runtime\rs::replace($__ctx, "\\.", "\\", $t->current_class_full_name)) . \Runtime\rtl::toStr(")")));
				$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
				$t = $t->levelInc($__ctx);
				for ($i = 0;$i < $op_code->vars->count($__ctx);$i++)
				{
					$variable = $op_code->vars->item($__ctx, $i);
					if ($variable->kind != \Bayrell\Lang\OpCodes\OpAssign::KIND_DECLARE)
					{
						continue;
					}
					$is_const = $variable->flags->isFlag($__ctx, "const");
					$is_static = $variable->flags->isFlag($__ctx, "static");
					if ($is_const || $is_static)
					{
						continue;
					}
					for ($j = 0;$j < $variable->values->count($__ctx);$j++)
					{
						$value = $variable->values->item($__ctx, $j);
						$content .= \Runtime\rtl::toStr($t->s($__ctx, "$this->__" . \Runtime\rtl::toStr($value->var_name) . \Runtime\rtl::toStr(" = $o->__") . \Runtime\rtl::toStr($value->var_name) . \Runtime\rtl::toStr(";")));
					}
				}
				$t = $t->levelDec($__ctx);
				$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
				$content .= \Runtime\rtl::toStr($t->s($__ctx, "parent::assignObject($__ctx,$o);"));
				$t = $t->levelDec($__ctx);
				$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
				/* Assign Value */
				$content .= \Runtime\rtl::toStr($t->s($__ctx, "function assignValue($__ctx,$k,$v)"));
				$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
				$t = $t->levelInc($__ctx);
				$flag = false;
				for ($i = 0;$i < $op_code->vars->count($__ctx);$i++)
				{
					$variable = $op_code->vars->item($__ctx, $i);
					if ($variable->kind != \Bayrell\Lang\OpCodes\OpAssign::KIND_DECLARE)
					{
						continue;
					}
					$is_const = $variable->flags->isFlag($__ctx, "const");
					$is_static = $variable->flags->isFlag($__ctx, "static");
					if ($is_const || $is_static)
					{
						continue;
					}
					for ($j = 0;$j < $variable->values->count($__ctx);$j++)
					{
						$value = $variable->values->item($__ctx, $j);
						if ($t->flag_struct_check_types)
						{
							$content .= \Runtime\rtl::toStr($t->s($__ctx, (($flag) ? "else " : "") . \Runtime\rtl::toStr("if ($k == ") . \Runtime\rtl::toStr($t->expression->staticMethod("toString")($__ctx, $value->var_name)) . \Runtime\rtl::toStr(")") . \Runtime\rtl::toStr("$this->__") . \Runtime\rtl::toStr($value->var_name) . \Runtime\rtl::toStr(" = Runtime.rtl.to($v, null, ") . \Runtime\rtl::toStr(static::toPattern($__ctx, $t, $variable->pattern)) . \Runtime\rtl::toStr(");")));
						}
						else
						{
							$content .= \Runtime\rtl::toStr($t->s($__ctx, (($flag) ? "else " : "") . \Runtime\rtl::toStr("if ($k == ") . \Runtime\rtl::toStr($t->expression->staticMethod("toString")($__ctx, $value->var_name)) . \Runtime\rtl::toStr(")") . \Runtime\rtl::toStr("$this->__") . \Runtime\rtl::toStr($value->var_name) . \Runtime\rtl::toStr(" = $v;")));
						}
						$flag = true;
					}
				}
				$content .= \Runtime\rtl::toStr($t->s($__ctx, (($flag) ? "else " : "") . \Runtime\rtl::toStr("parent::assignValue($__ctx,$k,$v);")));
				$t = $t->levelDec($__ctx);
				$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
				/* Take Value */
				$content .= \Runtime\rtl::toStr($t->s($__ctx, "function takeValue($__ctx,$k,$d=null)"));
				$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
				$t = $t->levelInc($__ctx);
				$flag = false;
				for ($i = 0;$i < $op_code->vars->count($__ctx);$i++)
				{
					$variable = $op_code->vars->item($__ctx, $i);
					if ($variable->kind != \Bayrell\Lang\OpCodes\OpAssign::KIND_DECLARE)
					{
						continue;
					}
					$is_const = $variable->flags->isFlag($__ctx, "const");
					$is_static = $variable->flags->isFlag($__ctx, "static");
					if ($is_const || $is_static)
					{
						continue;
					}
					for ($j = 0;$j < $variable->values->count($__ctx);$j++)
					{
						$value = $variable->values->item($__ctx, $j);
						$content .= \Runtime\rtl::toStr($t->s($__ctx, (($flag) ? "else " : "") . \Runtime\rtl::toStr("if ($k == ") . \Runtime\rtl::toStr($t->expression->staticMethod("toString")($__ctx, $value->var_name)) . \Runtime\rtl::toStr(")return $this->__") . \Runtime\rtl::toStr($value->var_name) . \Runtime\rtl::toStr(";")));
						$flag = true;
					}
				}
				$content .= \Runtime\rtl::toStr($t->s($__ctx, "return parent::takeValue($__ctx,$k,$d);"));
				$t = $t->levelDec($__ctx);
				$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
			}
		}
		if ($class_kind != \Bayrell\Lang\OpCodes\OpDeclareClass::KIND_INTERFACE)
		{
			/* Get class name function */
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "function getClassName()"));
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
			$t = $t->levelInc($__ctx);
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "return " . \Runtime\rtl::toStr($t->expression->staticMethod("toString")($__ctx, $t->current_class_full_name)) . \Runtime\rtl::toStr(";")));
			$t = $t->levelDec($__ctx);
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
			/* Get current namespace function */
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "static function getCurrentNamespace()"));
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
			$t = $t->levelInc($__ctx);
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "return " . \Runtime\rtl::toStr($t->expression->staticMethod("toString")($__ctx, $t->current_namespace_name)) . \Runtime\rtl::toStr(";")));
			$t = $t->levelDec($__ctx);
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
			/* Get current class name function */
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "static function getCurrentClassName()"));
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
			$t = $t->levelInc($__ctx);
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "return " . \Runtime\rtl::toStr($t->expression->staticMethod("toString")($__ctx, $t->current_class_full_name)) . \Runtime\rtl::toStr(";")));
			$t = $t->levelDec($__ctx);
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
			/* Get parent class name function */
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "static function getParentClassName()"));
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
			$t = $t->levelInc($__ctx);
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "return " . \Runtime\rtl::toStr($t->expression->staticMethod("toString")($__ctx, $t->expression->staticMethod("findModuleName")($__ctx, $t, $t->current_class_extends_name))) . \Runtime\rtl::toStr(";")));
			$t = $t->levelDec($__ctx);
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
			/* Class info */
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "static function getClassInfo($__ctx)"));
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
			$t = $t->levelInc($__ctx);
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "return new \\Runtime\\Annotations\\IntrospectionInfo($__ctx, ["));
			$t = $t->levelInc($__ctx);
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "\"kind\"=>\\Runtime\\Annotations\\IntrospectionInfo::ITEM_CLASS,"));
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "\"class_name\"=>" . \Runtime\rtl::toStr($t->expression->staticMethod("toString")($__ctx, $t->current_class_full_name)) . \Runtime\rtl::toStr(",")));
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "\"name\"=>" . \Runtime\rtl::toStr($t->expression->staticMethod("toString")($__ctx, $t->current_class_full_name)) . \Runtime\rtl::toStr(",")));
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "\"annotations\"=>\\Runtime\\Collection::from(["));
			$t = $t->levelInc($__ctx);
			for ($j = 0;$j < $op_code->annotations->count($__ctx);$j++)
			{
				$annotation = $op_code->annotations->item($__ctx, $j);
				$res = $t->expression->staticMethod("OpTypeIdentifier")($__ctx, $t, $annotation->name);
				$t = $res[0];
				$name = $res[1];
				$res = $t->expression->staticMethod("OpDict")($__ctx, $t, $annotation->params, true);
				$t = $res[0];
				$params = $res[1];
				$content .= \Runtime\rtl::toStr($t->s($__ctx, "new " . \Runtime\rtl::toStr($name) . \Runtime\rtl::toStr("($__ctx, ") . \Runtime\rtl::toStr($params) . \Runtime\rtl::toStr("),")));
			}
			$t = $t->levelDec($__ctx);
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "]),"));
			$t = $t->levelDec($__ctx);
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "]);"));
			$t = $t->levelDec($__ctx);
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
			/* Get fields list of the function */
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "static function getFieldsList($__ctx,$f)"));
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
			$t = $t->levelInc($__ctx);
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "$a = [];"));
			if ($op_code->vars != null)
			{
				$vars = new \Runtime\Map($__ctx);
				for ($i = 0;$i < $op_code->vars->count($__ctx);$i++)
				{
					$variable = $op_code->vars->item($__ctx, $i);
					$is_static = $variable->flags->isFlag($__ctx, "static");
					$is_serializable = $variable->flags->isFlag($__ctx, "serializable");
					$is_assignable = $variable->flags->isFlag($__ctx, "assignable");
					$has_annotation = $variable->annotations != null && $variable->annotations->count($__ctx) > 0;
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
						if (!$vars->has($__ctx, $flag))
						{
							$vars->set($__ctx, $flag, new \Runtime\Vector($__ctx));
						}
						$v = $vars->item($__ctx, $flag);
						for ($j = 0;$j < $variable->values->count($__ctx);$j++)
						{
							$value = $variable->values->item($__ctx, $j);
							$v->push($__ctx, $value->var_name);
						}
					}
				}
				$vars->each($__ctx, function ($__ctx, $v, $flag) use (&$t,&$content)
				{
					$content .= \Runtime\rtl::toStr($t->s($__ctx, "if (($f|" . \Runtime\rtl::toStr($flag) . \Runtime\rtl::toStr(")==") . \Runtime\rtl::toStr($flag) . \Runtime\rtl::toStr(")")));
					$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
					$t = $t->levelInc($__ctx);
					$v->each($__ctx, function ($__ctx, $varname) use (&$t,&$content)
					{
						$content .= \Runtime\rtl::toStr($t->s($__ctx, "$a[] = " . \Runtime\rtl::toStr($t->expression->staticMethod("toString")($__ctx, $varname)) . \Runtime\rtl::toStr(";")));
					});
					$t = $t->levelDec($__ctx);
					$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
				});
			}
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "return " . \Runtime\rtl::toStr($t->expression->staticMethod("getModuleName")($__ctx, $t, "Runtime.Collection")) . \Runtime\rtl::toStr("::from($a);")));
			$t = $t->levelDec($__ctx);
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
			/* Get field info by name */
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "static function getFieldInfoByName($__ctx,$field_name)"));
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
			$t = $t->levelInc($__ctx);
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "return null;"));
			$t = $t->levelDec($__ctx);
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
			/* Get methods list of the function */
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "static function getMethodsList($__ctx)"));
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
			$t = $t->levelInc($__ctx);
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "$a = ["));
			$t = $t->levelInc($__ctx);
			if ($op_code->functions != null)
			{
				for ($i = 0;$i < $op_code->functions->count($__ctx);$i++)
				{
					$f = $op_code->functions->item($__ctx, $i);
					if ($f->flags->isFlag($__ctx, "declare"))
					{
						continue;
					}
					if ($f->annotations->count($__ctx) == 0)
					{
						continue;
					}
					$content .= \Runtime\rtl::toStr($t->s($__ctx, $t->expression->staticMethod("toString")($__ctx, $f->name) . \Runtime\rtl::toStr(",")));
				}
			}
			$t = $t->levelDec($__ctx);
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "];"));
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "return " . \Runtime\rtl::toStr($t->expression->staticMethod("getModuleName")($__ctx, $t, "Runtime.Collection")) . \Runtime\rtl::toStr("::from($a);")));
			$t = $t->levelDec($__ctx);
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
			/* Get method info by name */
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "static function getMethodInfoByName($__ctx,$field_name)"));
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
			$t = $t->levelInc($__ctx);
			if ($op_code->functions != null)
			{
				for ($i = 0;$i < $op_code->functions->count($__ctx);$i++)
				{
					$f = $op_code->functions->item($__ctx, $i);
					if ($f->flags->isFlag($__ctx, "declare"))
					{
						continue;
					}
					if ($f->annotations->count($__ctx) == 0)
					{
						continue;
					}
					$content .= \Runtime\rtl::toStr($t->s($__ctx, "if ($field_name == " . \Runtime\rtl::toStr($t->expression->staticMethod("toString")($__ctx, $f->name)) . \Runtime\rtl::toStr(")")));
					$t = $t->levelInc($__ctx);
					$content .= \Runtime\rtl::toStr($t->s($__ctx, "return new \\Runtime\\Annotations\\IntrospectionInfo($__ctx, ["));
					$t = $t->levelInc($__ctx);
					$content .= \Runtime\rtl::toStr($t->s($__ctx, "\"kind\"=>\\Runtime\\Annotations\\IntrospectionInfo::ITEM_METHOD,"));
					$content .= \Runtime\rtl::toStr($t->s($__ctx, "\"class_name\"=>" . \Runtime\rtl::toStr($t->expression->staticMethod("toString")($__ctx, $t->current_class_full_name)) . \Runtime\rtl::toStr(",")));
					$content .= \Runtime\rtl::toStr($t->s($__ctx, "\"name\"=>" . \Runtime\rtl::toStr($t->expression->staticMethod("toString")($__ctx, $f->name)) . \Runtime\rtl::toStr(",")));
					$content .= \Runtime\rtl::toStr($t->s($__ctx, "\"annotations\"=>\\Runtime\\Collection::from(["));
					$t = $t->levelInc($__ctx);
					for ($j = 0;$j < $f->annotations->count($__ctx);$j++)
					{
						$annotation = $f->annotations->item($__ctx, $j);
						$res = $t->expression->staticMethod("OpTypeIdentifier")($__ctx, $t, $annotation->name);
						$t = $res[0];
						$name = $res[1];
						$res = $t->expression->staticMethod("OpDict")($__ctx, $t, $annotation->params, true);
						$t = $res[0];
						$params = $res[1];
						$content .= \Runtime\rtl::toStr($t->s($__ctx, "new " . \Runtime\rtl::toStr($name) . \Runtime\rtl::toStr("($__ctx, ") . \Runtime\rtl::toStr($params) . \Runtime\rtl::toStr("),")));
					}
					$t = $t->levelDec($__ctx);
					$content .= \Runtime\rtl::toStr($t->s($__ctx, "]),"));
					$t = $t->levelDec($__ctx);
					$content .= \Runtime\rtl::toStr($t->s($__ctx, "]);"));
					$t = $t->levelDec($__ctx);
				}
			}
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "return null;"));
			$t = $t->levelDec($__ctx);
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
		}
		/* Class items */
		for ($i = 0;$i < $op_code->items->count($__ctx);$i++)
		{
			$item = $op_code->items->item($__ctx, $i);
			if ($item instanceof \Bayrell\Lang\OpCodes\OpPreprocessorIfCode)
			{
				$res = $t->operator->staticMethod("OpPreprocessorIfCode")($__ctx, $t, $item);
				$content .= \Runtime\rtl::toStr($t->s($__ctx, $res[1]));
			}
			else if ($item instanceof \Bayrell\Lang\OpCodes\OpPreprocessorSwitch)
			{
				for ($j = 0;$i < $item->items->count($__ctx);$i++)
				{
					$res = $t->operator->staticMethod("OpPreprocessorIfCode")($__ctx, $t, $item->items->item($__ctx, $i));
					$s = $res[1];
					if ($s == "")
					{
						continue;
					}
					$content .= \Runtime\rtl::toStr($t->s($__ctx, $res[1]));
				}
			}
		}
		$t = $t->levelDec($__ctx);
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "}"));
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpDeclareClassFooter
	 */
	static function OpDeclareClassFooter($__ctx, $t, $op_code)
	{
		$content = "";
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpDeclareClass
	 */
	static function OpDeclareClass($__ctx, $t, $op_code)
	{
		if ($op_code->is_declare)
		{
			return \Runtime\Collection::from([$t,""]);
		}
		$content = "";
		$t = $t->copy($__ctx, ["current_class_name"=>$op_code->name]);
		$t = $t->copy($__ctx, ["current_class_full_name"=>$t->current_namespace_name . \Runtime\rtl::toStr(".") . \Runtime\rtl::toStr($t->current_class_name)]);
		if ($op_code->class_extends != null)
		{
			$extends_name = \Runtime\rs::join($__ctx, ".", $op_code->class_extends->entity_name->names);
			$t = $t->copy($__ctx, ["current_class_extends_name"=>$extends_name]);
		}
		else if ($op_code->kind == \Bayrell\Lang\OpCodes\OpDeclareClass::KIND_STRUCT)
		{
			$t = $t->copy($__ctx, ["current_class_extends_name"=>"Runtime.CoreStruct"]);
		}
		else if ($op_code->kind == \Bayrell\Lang\OpCodes\OpDeclareClass::KIND_STRUCT)
		{
			$t = $t->copy($__ctx, ["current_class_extends_name"=>""]);
		}
		if ($op_code->kind != \Bayrell\Lang\OpCodes\OpDeclareClass::KIND_INTERFACE)
		{
			if ($op_code->class_extends != null)
			{
				$content = "class " . \Runtime\rtl::toStr($t->current_class_name) . \Runtime\rtl::toStr(" extends ") . \Runtime\rtl::toStr($t->expression->staticMethod("getModuleName")($__ctx, $t, $t->current_class_extends_name));
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
		if ($op_code->class_implements != null && $op_code->class_implements->count($__ctx) > 0)
		{
			$arr = $op_code->class_implements->map($__ctx, function ($__ctx, $item) use (&$t)
			{
				return $t->expression->staticMethod("getModuleNames")($__ctx, $t, $item->entity_name->names);
			});
			$s1 = \Runtime\rs::join($__ctx, ", ", $arr);
			$content .= \Runtime\rtl::toStr(" implements " . \Runtime\rtl::toStr($s1));
		}
		/* Class body */
		$res = static::OpDeclareClassBody($__ctx, $t, $op_code);
		$content .= \Runtime\rtl::toStr($res[1]);
		/* Class comments */
		$res = $t->operator->staticMethod("AddComments")($__ctx, $t, $op_code->comments, $content);
		$content = $res[1];
		/* Class footer */
		$res = static::OpDeclareClassFooter($__ctx, $t, $op_code);
		$content .= \Runtime\rtl::toStr($res[1]);
		return \Runtime\Collection::from([$t,$t->s($__ctx, $content)]);
	}
	/**
	 * Translate item
	 */
	static function translateItem($__ctx, $t, $op_code)
	{
		if ($op_code instanceof \Bayrell\Lang\OpCodes\OpNamespace)
		{
			return static::OpNamespace($__ctx, $t, $op_code);
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpDeclareClass)
		{
			return static::OpDeclareClass($__ctx, $t, $op_code);
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpComment)
		{
			return $t->operator->staticMethod("OpComment")($__ctx, $t, $op_code);
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpPreprocessorIfCode)
		{
			return $t->operator->staticMethod("OpPreprocessorIfCode")($__ctx, $t, $op_code);
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpPreprocessorSwitch)
		{
			$content = "";
			for ($i = 0;$i < $op_code->items->count($__ctx);$i++)
			{
				$res = $t->operator->staticMethod("OpPreprocessorIfCode")($__ctx, $t, $op_code->items->item($__ctx, $i));
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
	static function translateProgramHeader($__ctx, $t, $op_code)
	{
		$content = "<?php";
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * Translate program
	 */
	static function translateProgram($__ctx, $t, $op_code)
	{
		$content = "";
		if ($op_code->uses != null)
		{
			$t = $t->copy($__ctx, ["modules"=>$op_code->uses]);
		}
		if ($op_code->items != null)
		{
			$res = static::translateProgramHeader($__ctx, $t, $op_code);
			$content .= \Runtime\rtl::toStr($res[1]);
			for ($i = 0;$i < $op_code->items->count($__ctx);$i++)
			{
				$item = $op_code->items->item($__ctx, $i);
				$res = static::translateItem($__ctx, $t, $item);
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
	static function getClassInfo($__ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($__ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.LangPHP.TranslatorPHPProgram",
			"name"=>"Bayrell.Lang.LangPHP.TranslatorPHPProgram",
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