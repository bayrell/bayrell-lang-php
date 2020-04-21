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
class TranslatorNodeProgram extends \Bayrell\Lang\LangES6\TranslatorES6Program
{
	/**
	 * Translate program
	 */
	static function translateProgramHeader($ctx, $t, $op_code)
	{
		$content = "\"use strict;\"";
		$content .= \Runtime\rtl::toStr($t->s($ctx, "var use = require('bayrell').use;"));
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * OpDeclareClassFooter
	 */
	static function OpDeclareClassFooter($ctx, $t, $op_code)
	{
		$content = "";
		$name = "";
		$content .= \Runtime\rtl::toStr("use.add(" . \Runtime\rtl::toStr($t->current_class_full_name) . \Runtime\rtl::toStr(");"));
		$content .= \Runtime\rtl::toStr($t->s($ctx, "if (module.exports == undefined) module.exports = {};"));
		$arr = \Runtime\rs::split($ctx, "\\.", $t->current_namespace_name);
		for ($i = 0;$i < $arr->count($ctx);$i++)
		{
			$name = $name . \Runtime\rtl::toStr((($i == 0) ? "" : ".")) . \Runtime\rtl::toStr($arr->item($ctx, $i));
			$s = "if (module.exports." . \Runtime\rtl::toStr($name) . \Runtime\rtl::toStr(" == undefined) module.exports.") . \Runtime\rtl::toStr($name) . \Runtime\rtl::toStr(" = {};");
			$content .= \Runtime\rtl::toStr(($content == 0) ? $s : $t->s($ctx, $s));
		}
		$content .= \Runtime\rtl::toStr($t->s($ctx, "module.exports." . \Runtime\rtl::toStr($t->current_class_full_name) . \Runtime\rtl::toStr(" = ") . \Runtime\rtl::toStr($t->current_class_full_name) . \Runtime\rtl::toStr(";")));
		return \Runtime\Collection::from([$t,$content]);
	}
	/* ======================= Class Init Functions ======================= */
	function getClassName()
	{
		return "Bayrell.Lang.LangNode.TranslatorNodeProgram";
	}
	static function getCurrentNamespace()
	{
		return "Bayrell.Lang.LangNode";
	}
	static function getCurrentClassName()
	{
		return "Bayrell.Lang.LangNode.TranslatorNodeProgram";
	}
	static function getParentClassName()
	{
		return "Bayrell.Lang.LangES6.TranslatorES6Program";
	}
	static function getClassInfo($ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.LangNode.TranslatorNodeProgram",
			"name"=>"Bayrell.Lang.LangNode.TranslatorNodeProgram",
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