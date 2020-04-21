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
namespace Bayrell\Lang\LangNode;
class TranslatorNodeExpression extends \Bayrell\Lang\LangES6\TranslatorES6Expression
{
	/**
	 * Returns string
	 */
	static function rtlToStr($ctx, $t, $s)
	{
		return "use(\"Runtime.rtl\").toStr(" . \Runtime\rtl::toStr($s) . \Runtime\rtl::toStr(")");
	}
	/**
	 * Use module name
	 */
	static function useModuleName($ctx, $t, $module_name)
	{
		$module_name = static::findModuleName($ctx, $t, $module_name);
		return "use(" . \Runtime\rtl::toStr(static::toString($ctx, $module_name)) . \Runtime\rtl::toStr(")");
	}
	/**
	 * OpIdentifier
	 */
	static function OpIdentifier($ctx, $t, $op_code)
	{
		if ($op_code->kind == \Bayrell\Lang\OpCodes\OpIdentifier::KIND_CONTEXT && $op_code->value == "@")
		{
			return \Runtime\Collection::from([$t,"ctx"]);
		}
		else if ($op_code->kind == \Bayrell\Lang\OpCodes\OpIdentifier::KIND_CONTEXT && $op_code->value == "_")
		{
			return \Runtime\Collection::from([$t,"ctx.translate"]);
		}
		else if ($t->modules->has($ctx, $op_code->value) || $op_code->kind == \Bayrell\Lang\OpCodes\OpIdentifier::KIND_SYS_TYPE)
		{
			$module_name = $op_code->value;
			$new_module_name = static::findModuleName($ctx, $t, $module_name);
			if ($module_name != $new_module_name)
			{
				$res = $t::addSaveOpCode($ctx, $t, \Runtime\Dict::from(["op_code"=>$op_code,"var_content"=>static::useModuleName($ctx, $t, $module_name)]));
				$t = $res[0];
				$var_name = $res[1];
				return \Runtime\Collection::from([$t,$var_name]);
			}
		}
		return \Runtime\Collection::from([$t,$op_code->value]);
	}
	/**
	 * OpTypeIdentifier
	 */
	static function OpTypeIdentifier($ctx, $t, $op_code)
	{
		$var_name = "";
		if ($op_code->entity_name->names->count($ctx) > 0)
		{
			$module_name = $op_code->entity_name->names->first($ctx);
			$new_module_name = static::findModuleName($ctx, $t, $module_name);
			if ($module_name != $new_module_name)
			{
				$res = $t::addSaveOpCode($ctx, $t, \Runtime\Dict::from(["var_content"=>static::useModuleName($ctx, $t, $module_name)]));
				$t = $res[0];
				$var_name = $res[1];
			}
		}
		if ($var_name == "")
		{
			$var_name = \Runtime\rs::join($ctx, ".", $op_code->entity_name->names);
		}
		return \Runtime\Collection::from([$t,$var_name]);
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
		$content = static::useModuleName($ctx, $t, "Runtime.Collection") . \Runtime\rtl::toStr(".from([") . \Runtime\rtl::toStr(\Runtime\rs::join($ctx, ",", $values)) . \Runtime\rtl::toStr("])");
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
		$content = static::useModuleName($ctx, $t, "Runtime.Dict") . \Runtime\rtl::toStr(".from({") . \Runtime\rtl::toStr(\Runtime\rs::join($ctx, ",", $values)) . \Runtime\rtl::toStr("})");
		return \Runtime\Collection::from([$t,$content]);
	}
	/* ======================= Class Init Functions ======================= */
	function getClassName()
	{
		return "Bayrell.Lang.LangNode.TranslatorNodeExpression";
	}
	static function getCurrentNamespace()
	{
		return "Bayrell.Lang.LangNode";
	}
	static function getCurrentClassName()
	{
		return "Bayrell.Lang.LangNode.TranslatorNodeExpression";
	}
	static function getParentClassName()
	{
		return "Bayrell.Lang.LangES6.TranslatorES6Expression";
	}
	static function getClassInfo($ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.LangNode.TranslatorNodeExpression",
			"name"=>"Bayrell.Lang.LangNode.TranslatorNodeExpression",
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