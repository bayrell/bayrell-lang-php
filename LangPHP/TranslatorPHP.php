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
class TranslatorPHP extends \Bayrell\Lang\CoreTranslator
{
	public $__expression;
	public $__html;
	public $__operator;
	public $__program;
	/**
	 * Reset translator
	 */
	static function reset($ctx, $t)
	{
		return $t->copy($ctx, \Runtime\Dict::from(["value"=>"","current_namespace_name"=>"","modules"=>new \Runtime\Dict($ctx),"expression"=>new \Bayrell\Lang\LangPHP\TranslatorPHPExpression($ctx),"html"=>new \Bayrell\Lang\LangPHP\TranslatorPHPHtml($ctx),"operator"=>new \Bayrell\Lang\LangPHP\TranslatorPHPOperator($ctx),"program"=>new \Bayrell\Lang\LangPHP\TranslatorPHPProgram($ctx),"save_vars"=>new \Runtime\Collection($ctx),"save_op_codes"=>new \Runtime\Collection($ctx),"save_op_code_inc"=>0,"preprocessor_flags"=>\Runtime\Dict::from(["BACKEND"=>true,"PHP"=>true])]));
	}
	/**
	 * Translate BaseOpCode
	 */
	static function translate($ctx, $t, $op_code)
	{
		$t = static::reset($ctx, $t);
		return $t->program::translateProgram($ctx, $t, $op_code);
	}
	/**
	 * Inc save op code
	 */
	static function nextSaveOpCode($ctx, $t)
	{
		return "$__v" . \Runtime\rtl::toStr($t->save_op_code_inc);
	}
	/**
	 * Output save op code content
	 */
	static function outputSaveOpCode($ctx, $t, $save_op_code_value=0)
	{
		$content = "";
		for ($i = 0;$i < $t->save_op_codes->count($ctx);$i++)
		{
			if ($i < $save_op_code_value)
			{
				continue;
			}
			$save = $t->save_op_codes->item($ctx, $i);
			$s = ($save->content == "") ? $t->s($ctx, $save->var_name . \Runtime\rtl::toStr(" = ") . \Runtime\rtl::toStr($save->var_content) . \Runtime\rtl::toStr(";")) : $save->content;
			$content .= \Runtime\rtl::toStr($s);
		}
		return $content;
	}
	/* ======================= Class Init Functions ======================= */
	function _init($ctx)
	{
		parent::_init($ctx);
		$this->__expression = null;
		$this->__html = null;
		$this->__operator = null;
		$this->__program = null;
	}
	function assignObject($ctx,$o)
	{
		if ($o instanceof \Bayrell\Lang\LangPHP\TranslatorPHP)
		{
			$this->__expression = $o->__expression;
			$this->__html = $o->__html;
			$this->__operator = $o->__operator;
			$this->__program = $o->__program;
		}
		parent::assignObject($ctx,$o);
	}
	function assignValue($ctx,$k,$v)
	{
		if ($k == "expression")$this->__expression = $v;
		else if ($k == "html")$this->__html = $v;
		else if ($k == "operator")$this->__operator = $v;
		else if ($k == "program")$this->__program = $v;
		else parent::assignValue($ctx,$k,$v);
	}
	function takeValue($ctx,$k,$d=null)
	{
		if ($k == "expression")return $this->__expression;
		else if ($k == "html")return $this->__html;
		else if ($k == "operator")return $this->__operator;
		else if ($k == "program")return $this->__program;
		return parent::takeValue($ctx,$k,$d);
	}
	function getClassName()
	{
		return "Bayrell.Lang.LangPHP.TranslatorPHP";
	}
	static function getCurrentNamespace()
	{
		return "Bayrell.Lang.LangPHP";
	}
	static function getCurrentClassName()
	{
		return "Bayrell.Lang.LangPHP.TranslatorPHP";
	}
	static function getParentClassName()
	{
		return "Bayrell.Lang.CoreTranslator";
	}
	static function getClassInfo($ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.LangPHP.TranslatorPHP",
			"name"=>"Bayrell.Lang.LangPHP.TranslatorPHP",
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
	}
	static function getFieldsList($ctx,$f)
	{
		$a = [];
		if (($f|3)==3)
		{
			$a[] = "expression";
			$a[] = "html";
			$a[] = "operator";
			$a[] = "program";
		}
		return \Runtime\Collection::from($a);
	}
	static function getFieldInfoByName($ctx,$field_name)
	{
		if ($field_name == "expression") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.LangPHP.TranslatorPHP",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "html") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.LangPHP.TranslatorPHP",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "operator") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.LangPHP.TranslatorPHP",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
		if ($field_name == "program") return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_FIELD,
			"class_name"=>"Bayrell.Lang.LangPHP.TranslatorPHP",
			"name"=> $field_name,
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
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