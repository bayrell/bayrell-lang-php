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
class TranslatorES6Program extends \Runtime\CoreStruct
{
	/**
	 * To pattern
	 */
	static function toPattern($ctx, $t, $pattern)
	{
		$names = $t->expression::findModuleNames($ctx, $t, $pattern->entity_name->names);
		$e = \Runtime\rs::join($ctx, ".", $names);
		$a = ($pattern->template != null) ? $pattern->template->map($ctx, function ($ctx, $pattern) use (&$t)
		{
			return static::toPattern($ctx, $t, $pattern);
		}) : null;
		$b = ($a != null) ? ",\"t\":[" . \Runtime\rtl::toStr(\Runtime\rs::join($ctx, ",", $a)) . \Runtime\rtl::toStr("]") : "";
		return "{\"e\":" . \Runtime\rtl::toStr($t->expression::toString($ctx, $e)) . \Runtime\rtl::toStr($b) . \Runtime\rtl::toStr("}");
	}
	/**
	 * OpNamespace
	 */
	static function OpNamespace($ctx, $t, $op_code)
	{
		$content = "";
		$name = "";
		$s = "";
		$arr = \Runtime\rs::split($ctx, "\\.", $op_code->name);
		for ($i = 0;$i < $arr->count($ctx);$i++)
		{
			$name = $name . \Runtime\rtl::toStr((($i == 0) ? "" : ".")) . \Runtime\rtl::toStr($arr->item($ctx, $i));
			$s = "if (typeof " . \Runtime\rtl::toStr($name) . \Runtime\rtl::toStr(" == 'undefined') ") . \Runtime\rtl::toStr($name) . \Runtime\rtl::toStr(" = {};");
			$content .= \Runtime\rtl::toStr($t->s($ctx, $s));
		}
		$t = $t->copy($ctx, ["current_namespace_name"=>$op_code->name]);
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpDeclareFunction
	 */
	static function OpDeclareFunction($ctx, $t, $op_code)
	{
		$is_static_function = $t->is_static_function;
		$is_static = $op_code->isStatic($ctx);
		$content = "";
		if ($op_code->isFlag($ctx, "declare"))
		{
			return \Runtime\Collection::from([$t,""]);
		}
		if (!$is_static && $is_static_function || $is_static && !$is_static_function)
		{
			return \Runtime\Collection::from([$t,""]);
		}
		/* Set current function */
		$t = $t->copy($ctx, ["current_function"=>$op_code]);
		$s = "";
		$res = $t->operator::OpDeclareFunctionArgs($ctx, $t, $op_code);
		$args = $res[1];
		$s .= \Runtime\rtl::toStr($op_code->name . \Runtime\rtl::toStr(": function(") . \Runtime\rtl::toStr($args) . \Runtime\rtl::toStr(")"));
		$res = $t->operator::OpDeclareFunctionBody($ctx, $t, $op_code);
		$s .= \Runtime\rtl::toStr($res[1]);
		$s .= \Runtime\rtl::toStr(",");
		/* Function comments */
		$res = $t->operator::AddComments($ctx, $t, $op_code->comments, $t->s($ctx, $s));
		$content .= \Runtime\rtl::toStr($res[1]);
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpDeclareClass
	 */
	static function OpDeclareClassConstructor($ctx, $t, $op_code)
	{
		$open = "";
		$content = "";
		$save_t = $t;
		/* Set function name */
		$t = $t->copy($ctx, ["current_function"=>$op_code->fn_create]);
		/* Clear save op codes */
		$t = $t::clearSaveOpCode($ctx, $t);
		if ($op_code->fn_create == null)
		{
			$open .= \Runtime\rtl::toStr($t->current_class_full_name . \Runtime\rtl::toStr(" = "));
			$open .= \Runtime\rtl::toStr("function(ctx)");
			$open = $t->s($ctx, $open) . \Runtime\rtl::toStr($t->s($ctx, "{"));
			$t = $t->levelInc($ctx);
			/* Call parent */
			if ($t->current_class_extends_name != "")
			{
				$content .= \Runtime\rtl::toStr($t->s($ctx, $t->expression::useModuleName($ctx, $t, $t->current_class_extends_name) . \Runtime\rtl::toStr(".apply(this, arguments);")));
			}
		}
		else
		{
			$open .= \Runtime\rtl::toStr($t->current_class_full_name . \Runtime\rtl::toStr(" = function("));
			$res = $t->operator::OpDeclareFunctionArgs($ctx, $t, $op_code->fn_create);
			$t = $res[0];
			$open .= \Runtime\rtl::toStr($res[1]);
			$open .= \Runtime\rtl::toStr(")");
			$open = $t->s($ctx, $open) . \Runtime\rtl::toStr($t->s($ctx, "{"));
			$t = $t->levelInc($ctx);
		}
		/* Function body */
		if ($op_code->fn_create != null)
		{
			$res = $t->operator::Operators($ctx, $t, ($op_code->fn_create->expression) ? $op_code->fn_create->expression : $op_code->fn_create->value);
			$t = $res[0];
			$content .= \Runtime\rtl::toStr($res[1]);
		}
		/* Constructor end */
		$content = $open . \Runtime\rtl::toStr($content);
		$t = $t->levelDec($ctx);
		$content .= \Runtime\rtl::toStr($t->s($ctx, "};"));
		return \Runtime\Collection::from([$save_t,$content]);
	}
	/**
	 * OpDeclareClassBodyItem
	 */
	static function OpDeclareClassBodyItem($ctx, $t, $item)
	{
		$content = "";
		if ($item instanceof \Bayrell\Lang\OpCodes\OpPreprocessorIfDef)
		{
			$res = $t->operator::OpPreprocessorIfDef($ctx, $t, $item, \Bayrell\Lang\OpCodes\OpPreprocessorIfDef::KIND_CLASS_BODY);
			$content .= \Runtime\rtl::toStr($res[1]);
		}
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
		$content .= \Runtime\rtl::toStr($t->s($ctx, "if (field_name == " . \Runtime\rtl::toStr($t->expression::toString($ctx, $f->name)) . \Runtime\rtl::toStr(")")));
		$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
		$s1 = "";
		$t = $t->levelInc($ctx);
		$s1 .= \Runtime\rtl::toStr($t->s($ctx, "var Collection = " . \Runtime\rtl::toStr($t->expression::useModuleName($ctx, $t, "Runtime.Collection")) . \Runtime\rtl::toStr(";")));
		$s1 .= \Runtime\rtl::toStr($t->s($ctx, "var Dict = " . \Runtime\rtl::toStr($t->expression::useModuleName($ctx, $t, "Runtime.Dict")) . \Runtime\rtl::toStr(";")));
		$s1 .= \Runtime\rtl::toStr($t->s($ctx, "var IntrospectionInfo = " . \Runtime\rtl::toStr($t->expression::useModuleName($ctx, $t, "Runtime.Annotations.IntrospectionInfo")) . \Runtime\rtl::toStr(";")));
		$s1 .= \Runtime\rtl::toStr($t->s($ctx, "return new IntrospectionInfo(ctx, {"));
		$t = $t->levelInc($ctx);
		$s1 .= \Runtime\rtl::toStr($t->s($ctx, "\"kind\": IntrospectionInfo.ITEM_METHOD,"));
		$s1 .= \Runtime\rtl::toStr($t->s($ctx, "\"class_name\": " . \Runtime\rtl::toStr($t->expression::toString($ctx, $t->current_class_full_name)) . \Runtime\rtl::toStr(",")));
		$s1 .= \Runtime\rtl::toStr($t->s($ctx, "\"name\": " . \Runtime\rtl::toStr($t->expression::toString($ctx, $f->name)) . \Runtime\rtl::toStr(",")));
		$s1 .= \Runtime\rtl::toStr($t->s($ctx, "\"annotations\": Collection.from(["));
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
			$s1 .= \Runtime\rtl::toStr($t->s($ctx, "new " . \Runtime\rtl::toStr($name) . \Runtime\rtl::toStr("(ctx, ") . \Runtime\rtl::toStr($params) . \Runtime\rtl::toStr("),")));
		}
		$t = $t->levelDec($ctx);
		$s1 .= \Runtime\rtl::toStr($t->s($ctx, "]),"));
		$t = $t->levelDec($ctx);
		$s1 .= \Runtime\rtl::toStr($t->s($ctx, "});"));
		$save = $t::outputSaveOpCode($ctx, $t);
		if ($save != "")
		{
			$content .= \Runtime\rtl::toStr($t->s($ctx, $save));
		}
		$content .= \Runtime\rtl::toStr($s1);
		$t = $t->levelDec($ctx);
		$content .= \Runtime\rtl::toStr($t->s($ctx, "}"));
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
	static function OpDeclareClassBodyStatic($ctx, $t, $op_code)
	{
		$content = "";
		$class_kind = $op_code->kind;
		$current_class_extends_name = $t->expression::findModuleName($ctx, $t, $t->current_class_extends_name);
		$save_op_codes = $t->save_op_codes;
		$save_op_code_inc = $t->save_op_code_inc;
		$t = $t::clearSaveOpCode($ctx, $t);
		/* Returns parent class name */
		$parent_class_name = "";
		if ($op_code->class_extends != null)
		{
			$res = $t->expression::OpTypeIdentifier($ctx, $t, $op_code->class_extends);
			$parent_class_name = $res[1];
		}
		if ($current_class_extends_name != "")
		{
			$content .= \Runtime\rtl::toStr($t->s($ctx, "Object.assign(" . \Runtime\rtl::toStr($t->current_class_full_name) . \Runtime\rtl::toStr(", ") . \Runtime\rtl::toStr($t->expression::useModuleName($ctx, $t, $current_class_extends_name)) . \Runtime\rtl::toStr(");")));
		}
		$content .= \Runtime\rtl::toStr($t->s($ctx, "Object.assign(" . \Runtime\rtl::toStr($t->current_class_full_name) . \Runtime\rtl::toStr(",")));
		$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
		$t = $t->levelInc($ctx);
		/* Static variables */
		if ($op_code->vars != null)
		{
			for ($i = 0;$i < $op_code->vars->count($ctx);$i++)
			{
				$variable = $op_code->vars->item($ctx, $i);
				if ($variable->kind != \Bayrell\Lang\OpCodes\OpAssign::KIND_DECLARE)
				{
					continue;
				}
				$is_static = $variable->flags->isFlag($ctx, "static");
				if (!$is_static)
				{
					continue;
				}
				for ($j = 0;$j < $variable->values->count($ctx);$j++)
				{
					$value = $variable->values->item($ctx, $j);
					$res = $t->expression::Expression($ctx, $t, $value->expression);
					$s = ($value->expression != null) ? $res[1] : "null";
					$content .= \Runtime\rtl::toStr($t->s($ctx, $value->var_name . \Runtime\rtl::toStr(": ") . \Runtime\rtl::toStr($s) . \Runtime\rtl::toStr(",")));
				}
			}
		}
		if ($class_kind != \Bayrell\Lang\OpCodes\OpDeclareClass::KIND_INTERFACE)
		{
			/* Static Functions */
			if ($op_code->functions != null)
			{
				$t = $t->copy($ctx, ["is_static_function"=>true]);
				for ($i = 0;$i < $op_code->functions->count($ctx);$i++)
				{
					$f = $op_code->functions->item($ctx, $i);
					if ($f->flags->isFlag($ctx, "declare"))
					{
						continue;
					}
					if (!$f->isStatic($ctx))
					{
						continue;
					}
					/* Set function name */
					$t = $t->copy($ctx, ["current_function"=>$f]);
					$s = "";
					$res = $t->operator::OpDeclareFunctionArgs($ctx, $t, $f);
					$args = $res[1];
					$s .= \Runtime\rtl::toStr($f->name . \Runtime\rtl::toStr(": function(") . \Runtime\rtl::toStr($args) . \Runtime\rtl::toStr(")"));
					$res = $t->operator::OpDeclareFunctionBody($ctx, $t, $f);
					$s .= \Runtime\rtl::toStr($res[1]);
					$s .= \Runtime\rtl::toStr(",");
					/* Function comments */
					$res = $t->operator::AddComments($ctx, $t, $f->comments, $t->s($ctx, $s));
					$content .= \Runtime\rtl::toStr($res[1]);
				}
			}
			/* Items */
			if ($op_code->items != null)
			{
				$t = $t->copy($ctx, ["is_static_function"=>true]);
				for ($i = 0;$i < $op_code->items->count($ctx);$i++)
				{
					$item = $op_code->items->item($ctx, $i);
					$res = static::OpDeclareClassBodyItem($ctx, $t, $item);
					$t = $res[0];
					$content .= \Runtime\rtl::toStr($res[1]);
				}
			}
			$content .= \Runtime\rtl::toStr($t->s($ctx, "/* ======================= Class Init Functions ======================= */"));
			/* Get current namespace function */
			$content .= \Runtime\rtl::toStr($t->s($ctx, "getCurrentNamespace: function()"));
			$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
			$t = $t->levelInc($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "return " . \Runtime\rtl::toStr($t->expression::toString($ctx, $t->current_namespace_name)) . \Runtime\rtl::toStr(";")));
			$t = $t->levelDec($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "},"));
			/* Get current class name function */
			$content .= \Runtime\rtl::toStr($t->s($ctx, "getCurrentClassName: function()"));
			$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
			$t = $t->levelInc($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "return " . \Runtime\rtl::toStr($t->expression::toString($ctx, $t->current_class_full_name)) . \Runtime\rtl::toStr(";")));
			$t = $t->levelDec($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "},"));
			/* Get parent class name function */
			$content .= \Runtime\rtl::toStr($t->s($ctx, "getParentClassName: function()"));
			$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
			$t = $t->levelInc($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "return " . \Runtime\rtl::toStr($t->expression::toString($ctx, $current_class_extends_name)) . \Runtime\rtl::toStr(";")));
			$t = $t->levelDec($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "},"));
			/* Class info */
			$content .= \Runtime\rtl::toStr($t->s($ctx, "getClassInfo: function(ctx)"));
			$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
			$t = $t->levelInc($ctx);
			$t = $t::clearSaveOpCode($ctx, $t);
			$s1 = "";
			$s1 .= \Runtime\rtl::toStr($t->s($ctx, "var Collection = " . \Runtime\rtl::toStr($t->expression::useModuleName($ctx, $t, "Runtime.Collection")) . \Runtime\rtl::toStr(";")));
			$s1 .= \Runtime\rtl::toStr($t->s($ctx, "var Dict = " . \Runtime\rtl::toStr($t->expression::useModuleName($ctx, $t, "Runtime.Dict")) . \Runtime\rtl::toStr(";")));
			$s1 .= \Runtime\rtl::toStr($t->s($ctx, "var IntrospectionInfo = " . \Runtime\rtl::toStr($t->expression::useModuleName($ctx, $t, "Runtime.Annotations.IntrospectionInfo")) . \Runtime\rtl::toStr(";")));
			$s1 .= \Runtime\rtl::toStr($t->s($ctx, "return new IntrospectionInfo(ctx, {"));
			$t = $t->levelInc($ctx);
			$s1 .= \Runtime\rtl::toStr($t->s($ctx, "\"kind\": IntrospectionInfo.ITEM_CLASS,"));
			$s1 .= \Runtime\rtl::toStr($t->s($ctx, "\"class_name\": " . \Runtime\rtl::toStr($t->expression::toString($ctx, $t->current_class_full_name)) . \Runtime\rtl::toStr(",")));
			$s1 .= \Runtime\rtl::toStr($t->s($ctx, "\"name\": " . \Runtime\rtl::toStr($t->expression::toString($ctx, $t->current_class_full_name)) . \Runtime\rtl::toStr(",")));
			$s1 .= \Runtime\rtl::toStr($t->s($ctx, "\"annotations\": Collection.from(["));
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
				$s1 .= \Runtime\rtl::toStr($t->s($ctx, "new " . \Runtime\rtl::toStr($name) . \Runtime\rtl::toStr("(ctx, ") . \Runtime\rtl::toStr($params) . \Runtime\rtl::toStr("),")));
			}
			$t = $t->levelDec($ctx);
			$s1 .= \Runtime\rtl::toStr($t->s($ctx, "]),"));
			$t = $t->levelDec($ctx);
			$s1 .= \Runtime\rtl::toStr($t->s($ctx, "});"));
			$save = $t::outputSaveOpCode($ctx, $t);
			if ($save != "")
			{
				$content .= \Runtime\rtl::toStr($save);
			}
			$content .= \Runtime\rtl::toStr($s1);
			$t = $t->levelDec($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "},"));
			/* Get fields list of the function */
			$t = $t::clearSaveOpCode($ctx, $t);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "getFieldsList: function(ctx, f)"));
			$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
			$t = $t->levelInc($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "var a = [];"));
			$content .= \Runtime\rtl::toStr($t->s($ctx, "if (f==undefined) f=0;"));
			if ($op_code->vars != null)
			{
				$vars = new \Runtime\Map($ctx);
				for ($i = 0;$i < $op_code->vars->count($ctx);$i++)
				{
					$variable = $op_code->vars->item($ctx, $i);
					$is_static = $variable->flags->isFlag($ctx, "static");
					$is_serializable = $variable->flags->isFlag($ctx, "serializable");
					$is_assignable = true;
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
					$content .= \Runtime\rtl::toStr($t->s($ctx, "if ((f|" . \Runtime\rtl::toStr($flag) . \Runtime\rtl::toStr(")==") . \Runtime\rtl::toStr($flag) . \Runtime\rtl::toStr(")")));
					$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
					$t = $t->levelInc($ctx);
					$v->each($ctx, function ($ctx, $varname) use (&$t,&$content)
					{
						$content .= \Runtime\rtl::toStr($t->s($ctx, "a.push(" . \Runtime\rtl::toStr($t->expression::toString($ctx, $varname)) . \Runtime\rtl::toStr(");")));
					});
					$t = $t->levelDec($ctx);
					$content .= \Runtime\rtl::toStr($t->s($ctx, "}"));
				});
			}
			$content .= \Runtime\rtl::toStr($t->s($ctx, "return " . \Runtime\rtl::toStr($t->expression::useModuleName($ctx, $t, "Runtime.Collection")) . \Runtime\rtl::toStr(".from(a);")));
			$t = $t->levelDec($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "},"));
			/* Get field info by name */
			$content .= \Runtime\rtl::toStr($t->s($ctx, "getFieldInfoByName: function(ctx,field_name)"));
			$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
			$t = $t->levelInc($ctx);
			if ($op_code->vars != null)
			{
				$content .= \Runtime\rtl::toStr($t->s($ctx, "var Collection = " . \Runtime\rtl::toStr($t->expression::useModuleName($ctx, $t, "Runtime.Collection")) . \Runtime\rtl::toStr(";")));
				$content .= \Runtime\rtl::toStr($t->s($ctx, "var Dict = " . \Runtime\rtl::toStr($t->expression::useModuleName($ctx, $t, "Runtime.Dict")) . \Runtime\rtl::toStr(";")));
				$content .= \Runtime\rtl::toStr($t->s($ctx, "var IntrospectionInfo = " . \Runtime\rtl::toStr($t->expression::useModuleName($ctx, $t, "Runtime.Annotations.IntrospectionInfo")) . \Runtime\rtl::toStr(";")));
				for ($i = 0;$i < $op_code->vars->count($ctx);$i++)
				{
					$variable = $op_code->vars->item($ctx, $i);
					$v = $variable->values->map($ctx, function ($ctx, $value)
					{
						return $value->var_name;
					});
					$v = $v->map($ctx, function ($ctx, $var_name) use (&$t)
					{
						return "field_name == " . \Runtime\rtl::toStr($t->expression::toString($ctx, $var_name));
					});
					$t = $t::clearSaveOpCode($ctx, $t);
					$s1 = "";
					$s1 .= \Runtime\rtl::toStr($t->s($ctx, "if (" . \Runtime\rtl::toStr(\Runtime\rs::join($ctx, " or ", $v)) . \Runtime\rtl::toStr(") return new IntrospectionInfo(ctx, {")));
					$t = $t->levelInc($ctx);
					$s1 .= \Runtime\rtl::toStr($t->s($ctx, "\"kind\": IntrospectionInfo.ITEM_FIELD,"));
					$s1 .= \Runtime\rtl::toStr($t->s($ctx, "\"class_name\": " . \Runtime\rtl::toStr($t->expression::toString($ctx, $t->current_class_full_name)) . \Runtime\rtl::toStr(",")));
					$s1 .= \Runtime\rtl::toStr($t->s($ctx, "\"name\": field_name,"));
					$s1 .= \Runtime\rtl::toStr($t->s($ctx, "\"annotations\": Collection.from(["));
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
						$s1 .= \Runtime\rtl::toStr($t->s($ctx, "new " . \Runtime\rtl::toStr($name) . \Runtime\rtl::toStr("(ctx, ") . \Runtime\rtl::toStr($params) . \Runtime\rtl::toStr("),")));
					}
					$t = $t->levelDec($ctx);
					$s1 .= \Runtime\rtl::toStr($t->s($ctx, "]),"));
					$t = $t->levelDec($ctx);
					$s1 .= \Runtime\rtl::toStr($t->s($ctx, "});"));
					$save = $t::outputSaveOpCode($ctx, $t);
					if ($save != "")
					{
						$content .= \Runtime\rtl::toStr($save);
					}
					$content .= \Runtime\rtl::toStr($s1);
				}
			}
			$content .= \Runtime\rtl::toStr($t->s($ctx, "return null;"));
			$t = $t->levelDec($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "},"));
			/* Get methods list of the function */
			$t = $t::clearSaveOpCode($ctx, $t);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "getMethodsList: function(ctx)"));
			$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
			$t = $t->levelInc($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "var a = ["));
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
			$content .= \Runtime\rtl::toStr($t->s($ctx, "return " . \Runtime\rtl::toStr($t->expression::useModuleName($ctx, $t, "Runtime.Collection")) . \Runtime\rtl::toStr(".from(a);")));
			$t = $t->levelDec($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "},"));
			/* Get method info by name */
			$t = $t::clearSaveOpCode($ctx, $t);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "getMethodInfoByName: function(ctx,field_name)"));
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
			$content .= \Runtime\rtl::toStr($t->s($ctx, "},"));
			/* Add implements */
			if ($op_code->class_implements != null && $op_code->class_implements->count($ctx) > 0)
			{
				$content .= \Runtime\rtl::toStr($t->s($ctx, "__implements__:"));
				$content .= \Runtime\rtl::toStr($t->s($ctx, "["));
				$t = $t->levelInc($ctx);
				for ($i = 0;$i < $op_code->class_implements->count($ctx);$i++)
				{
					$item = $op_code->class_implements->item($ctx, $i);
					$module_name = $item->entity_name->names->first($ctx);
					$s = $t->expression::useModuleName($ctx, $t, $module_name);
					if ($s == "")
					{
						continue;
					}
					$content .= \Runtime\rtl::toStr($t->s($ctx, $s . \Runtime\rtl::toStr(",")));
				}
				$t = $t->levelDec($ctx);
				$content .= \Runtime\rtl::toStr($t->s($ctx, "],"));
			}
		}
		else
		{
			/* Get current namespace function */
			$content .= \Runtime\rtl::toStr($t->s($ctx, "getCurrentNamespace: function()"));
			$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
			$t = $t->levelInc($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "return " . \Runtime\rtl::toStr($t->expression::toString($ctx, $t->current_namespace_name)) . \Runtime\rtl::toStr(";")));
			$t = $t->levelDec($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "},"));
			/* Get current class name function */
			$content .= \Runtime\rtl::toStr($t->s($ctx, "getCurrentClassName: function()"));
			$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
			$t = $t->levelInc($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "return " . \Runtime\rtl::toStr($t->expression::toString($ctx, $t->current_class_full_name)) . \Runtime\rtl::toStr(";")));
			$t = $t->levelDec($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "},"));
		}
		$t = $t->levelDec($ctx);
		$content .= \Runtime\rtl::toStr($t->s($ctx, "});"));
		/* Restore save op codes */
		$t = $t->copy($ctx, ["save_op_codes"=>$save_op_codes]);
		$t = $t->copy($ctx, ["save_op_code_inc"=>$save_op_code_inc]);
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpDeclareClass
	 */
	static function OpDeclareClassBody($ctx, $t, $op_code)
	{
		$content = "";
		$class_kind = $op_code->kind;
		$content .= \Runtime\rtl::toStr($t->s($ctx, "Object.assign(" . \Runtime\rtl::toStr($t->current_class_full_name) . \Runtime\rtl::toStr(".prototype,")));
		$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
		$t = $t->levelInc($ctx);
		/* Functions */
		if ($op_code->functions != null)
		{
			$t = $t->copy($ctx, ["is_static_function"=>false]);
			for ($i = 0;$i < $op_code->functions->count($ctx);$i++)
			{
				$f = $op_code->functions->item($ctx, $i);
				if ($f->flags->isFlag($ctx, "declare"))
				{
					continue;
				}
				if ($f->isStatic($ctx))
				{
					continue;
				}
				/* Set function name */
				$t = $t->copy($ctx, ["current_function"=>$f]);
				$s = "";
				$res = $t->operator::OpDeclareFunctionArgs($ctx, $t, $f);
				$args = $res[1];
				$s .= \Runtime\rtl::toStr($f->name . \Runtime\rtl::toStr(": function(") . \Runtime\rtl::toStr($args) . \Runtime\rtl::toStr(")"));
				$res = $t->operator::OpDeclareFunctionBody($ctx, $t, $f);
				$s .= \Runtime\rtl::toStr($res[1]);
				$s .= \Runtime\rtl::toStr(",");
				/* Function comments */
				$res = $t->operator::AddComments($ctx, $t, $f->comments, $t->s($ctx, $s));
				$content .= \Runtime\rtl::toStr($res[1]);
			}
		}
		/* Items */
		if ($op_code->items != null)
		{
			$t = $t->copy($ctx, ["is_static_function"=>false]);
			for ($i = 0;$i < $op_code->items->count($ctx);$i++)
			{
				$item = $op_code->items->item($ctx, $i);
				$res = static::OpDeclareClassBodyItem($ctx, $t, $item);
				$t = $res[0];
				$content .= \Runtime\rtl::toStr($res[1]);
			}
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
				$content .= \Runtime\rtl::toStr($t->s($ctx, "_init: function(ctx)"));
				$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
				$t = $t->levelInc($ctx);
				/* Clear save op codes */
				$save_op_codes = $t->save_op_codes;
				$save_op_code_inc = $t->save_op_code_inc;
				if ($class_kind == \Bayrell\Lang\OpCodes\OpDeclareClass::KIND_STRUCT)
				{
					$content .= \Runtime\rtl::toStr($t->s($ctx, "var defProp = use('Runtime.rtl').defProp;"));
					$content .= \Runtime\rtl::toStr($t->s($ctx, "var a = Object.getOwnPropertyNames(this);"));
				}
				$s1 = "";
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
						/* prefix = "__"; */
						$prefix = "";
					}
					else if ($class_kind == \Bayrell\Lang\OpCodes\OpDeclareClass::KIND_CLASS)
					{
						$prefix = "";
					}
					for ($j = 0;$j < $variable->values->count($ctx);$j++)
					{
						$value = $variable->values->item($ctx, $j);
						$res = $t->expression::Expression($ctx, $t, $value->expression);
						$t = $res[0];
						$s = ($value->expression != null) ? $res[1] : "null";
						$s1 .= \Runtime\rtl::toStr($t->s($ctx, "this." . \Runtime\rtl::toStr($prefix) . \Runtime\rtl::toStr($value->var_name) . \Runtime\rtl::toStr(" = ") . \Runtime\rtl::toStr($s) . \Runtime\rtl::toStr(";")));
						if ($class_kind == \Bayrell\Lang\OpCodes\OpDeclareClass::KIND_STRUCT)
						{
							$var_name = $t->expression::toString($ctx, $value->var_name);
							/*s1 ~= t.s
							(
								"if (a.indexOf(" ~ var_name ~ ") == -1) defProp(this, " ~ var_name ~ ");"
							);*/
							/*
							s1 ~= t.s
							(
								"if (a.indexOf(" ~ t.expression::toString(value.var_name) ~ ") == -1)"~
								"Object.defineProperty(this, " ~ t.expression::toString(value.var_name) ~ ",{"~
								"get:function(){return this." ~ prefix ~ value.var_name ~ ";},"~
								"set:function(value){"~
									"throw new Runtime.Exceptions.AssignStructValueError(" ~
										t.expression::toString(value.var_name) ~
									");}"~
								"});"
							);
							*/
						}
					}
				}
				if ($t->current_class_extends_name != "")
				{
					$s1 .= \Runtime\rtl::toStr($t->s($ctx, $t->expression::useModuleName($ctx, $t, $t->current_class_extends_name) . \Runtime\rtl::toStr(".prototype._init.call(this,ctx);")));
				}
				/* Output save op code */
				$save = $t::outputSaveOpCode($ctx, $t, $save_op_codes->count($ctx));
				if ($save != "")
				{
					$content .= \Runtime\rtl::toStr($save);
				}
				/* Restore save op codes */
				$t = $t->copy($ctx, ["save_op_codes"=>$save_op_codes]);
				$t = $t->copy($ctx, ["save_op_code_inc"=>$save_op_code_inc]);
				/* Add content */
				$content .= \Runtime\rtl::toStr($s1);
				$t = $t->levelDec($ctx);
				$content .= \Runtime\rtl::toStr($t->s($ctx, "},"));
			}
			$is_struct = $class_kind == \Bayrell\Lang\OpCodes\OpDeclareClass::KIND_STRUCT;
			/* string var_prefix = is_struct ? "__" : ""; */
			$var_prefix = "";
			/* Assign Object */
			$content .= \Runtime\rtl::toStr($t->s($ctx, "assignObject: function(ctx,o)"));
			$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
			$t = $t->levelInc($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "if (o instanceof " . \Runtime\rtl::toStr($t->expression::useModuleName($ctx, $t, $t->current_class_full_name)) . \Runtime\rtl::toStr(")")));
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
					$content .= \Runtime\rtl::toStr($t->s($ctx, "this." . \Runtime\rtl::toStr($var_prefix) . \Runtime\rtl::toStr($value->var_name) . \Runtime\rtl::toStr(" = o.") . \Runtime\rtl::toStr($var_prefix) . \Runtime\rtl::toStr($value->var_name) . \Runtime\rtl::toStr(";")));
				}
			}
			$t = $t->levelDec($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "}"));
			if ($t->current_class_extends_name != "")
			{
				$content .= \Runtime\rtl::toStr($t->s($ctx, $t->expression::useModuleName($ctx, $t, $t->current_class_extends_name) . \Runtime\rtl::toStr(".prototype.assignObject.call(this,ctx,o);")));
			}
			$t = $t->levelDec($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "},"));
			/* Assign Value */
			$content .= \Runtime\rtl::toStr($t->s($ctx, "assignValue: function(ctx,k,v)"));
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
						$content .= \Runtime\rtl::toStr($t->s($ctx, (($flag) ? "else " : "") . \Runtime\rtl::toStr("if (k == ") . \Runtime\rtl::toStr($t->expression::toString($ctx, $value->var_name)) . \Runtime\rtl::toStr(")") . \Runtime\rtl::toStr("this.") . \Runtime\rtl::toStr($var_prefix) . \Runtime\rtl::toStr($value->var_name) . \Runtime\rtl::toStr(" = Runtime.rtl.to(v, null, ") . \Runtime\rtl::toStr(static::toPattern($ctx, $t, $variable->pattern)) . \Runtime\rtl::toStr(");")));
					}
					else
					{
						$content .= \Runtime\rtl::toStr($t->s($ctx, (($flag) ? "else " : "") . \Runtime\rtl::toStr("if (k == ") . \Runtime\rtl::toStr($t->expression::toString($ctx, $value->var_name)) . \Runtime\rtl::toStr(")") . \Runtime\rtl::toStr("this.") . \Runtime\rtl::toStr($var_prefix) . \Runtime\rtl::toStr($value->var_name) . \Runtime\rtl::toStr(" = v;")));
					}
					$flag = true;
				}
			}
			if ($t->current_class_extends_name != "")
			{
				$content .= \Runtime\rtl::toStr($t->s($ctx, (($flag) ? "else " : "") . \Runtime\rtl::toStr($t->expression::useModuleName($ctx, $t, $t->current_class_extends_name)) . \Runtime\rtl::toStr(".prototype.assignValue.call(this,ctx,k,v);")));
			}
			$t = $t->levelDec($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "},"));
			/* Take Value */
			$content .= \Runtime\rtl::toStr($t->s($ctx, "takeValue: function(ctx,k,d)"));
			$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
			$t = $t->levelInc($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "if (d == undefined) d = null;"));
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
					$content .= \Runtime\rtl::toStr($t->s($ctx, (($flag) ? "else " : "") . \Runtime\rtl::toStr("if (k == ") . \Runtime\rtl::toStr($t->expression::toString($ctx, $value->var_name)) . \Runtime\rtl::toStr(")return this.") . \Runtime\rtl::toStr($var_prefix) . \Runtime\rtl::toStr($value->var_name) . \Runtime\rtl::toStr(";")));
					$flag = true;
				}
			}
			if ($t->current_class_extends_name != "")
			{
				$content .= \Runtime\rtl::toStr($t->s($ctx, "return " . \Runtime\rtl::toStr($t->expression::useModuleName($ctx, $t, $t->current_class_extends_name)) . \Runtime\rtl::toStr(".prototype.takeValue.call(this,ctx,k,d);")));
			}
			$t = $t->levelDec($ctx);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "},"));
		}
		/* Get class name function */
		$content .= \Runtime\rtl::toStr($t->s($ctx, "getClassName: function(ctx)"));
		$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
		$t = $t->levelInc($ctx);
		$content .= \Runtime\rtl::toStr($t->s($ctx, "return " . \Runtime\rtl::toStr($t->expression::toString($ctx, $t->current_class_full_name)) . \Runtime\rtl::toStr(";")));
		$t = $t->levelDec($ctx);
		$content .= \Runtime\rtl::toStr($t->s($ctx, "},"));
		$t = $t->levelDec($ctx);
		$content .= \Runtime\rtl::toStr($t->s($ctx, "});"));
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpDeclareClassFooter
	 */
	static function OpDeclareClassFooter($ctx, $t, $op_code)
	{
		$content = $t->s($ctx, "Runtime.rtl.defClass(" . \Runtime\rtl::toStr($t->current_class_full_name) . \Runtime\rtl::toStr(");"));
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
		/* Constructor */
		$res = static::OpDeclareClassConstructor($ctx, $t, $op_code);
		$content .= \Runtime\rtl::toStr($res[1]);
		/* Extends */
		if ($op_code->class_extends != null)
		{
			$content .= \Runtime\rtl::toStr($t->s($ctx, $t->current_class_full_name . \Runtime\rtl::toStr(".prototype = Object.create(") . \Runtime\rtl::toStr($t->expression::useModuleName($ctx, $t, $t->current_class_extends_name)) . \Runtime\rtl::toStr(".prototype);")));
			$content .= \Runtime\rtl::toStr($t->s($ctx, $t->current_class_full_name . \Runtime\rtl::toStr(".prototype.constructor = ") . \Runtime\rtl::toStr($t->current_class_full_name) . \Runtime\rtl::toStr(";")));
		}
		/* Class body */
		$res = static::OpDeclareClassBody($ctx, $t, $op_code);
		$content .= \Runtime\rtl::toStr($res[1]);
		/* Class static functions */
		$res = static::OpDeclareClassBodyStatic($ctx, $t, $op_code);
		$content .= \Runtime\rtl::toStr($res[1]);
		/* Class comments */
		$res = $t->operator::AddComments($ctx, $t, $op_code->comments, $content);
		$content = $res[1];
		/* Class footer */
		$res = static::OpDeclareClassFooter($ctx, $t, $op_code);
		$content .= \Runtime\rtl::toStr($res[1]);
		return \Runtime\Collection::from([$t,$content]);
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
		$content = "\"use strict;\"";
		$content .= \Runtime\rtl::toStr($t->s($ctx, "var use = (typeof Runtime != 'undefined' && typeof Runtime.rtl != 'undefined')" . \Runtime\rtl::toStr(" ? Runtime.rtl.find_class : null;")));
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
	function assignObject($ctx,$o)
	{
		if ($o instanceof \Bayrell\Lang\LangES6\TranslatorES6Program)
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
		return "Bayrell.Lang.LangES6.TranslatorES6Program";
	}
	static function getCurrentNamespace()
	{
		return "Bayrell.Lang.LangES6";
	}
	static function getCurrentClassName()
	{
		return "Bayrell.Lang.LangES6.TranslatorES6Program";
	}
	static function getParentClassName()
	{
		return "Runtime.CoreStruct";
	}
	static function getClassInfo($ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.LangES6.TranslatorES6Program",
			"name"=>"Bayrell.Lang.LangES6.TranslatorES6Program",
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