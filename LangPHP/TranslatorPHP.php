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
class TranslatorPHP extends \Bayrell\Lang\CoreTranslator
{
	public $__expression;
	public $__html;
	public $__operator;
	public $__program;
	/**
	 * Reset translator
	 */
	static function reset($__ctx, $t)
	{
		return $t->copy($__ctx, \Runtime\Dict::from(["value"=>"","current_namespace_name"=>"","modules"=>new \Runtime\Dict($__ctx),"expression"=>new \Bayrell\Lang\LangPHP\TranslatorPHPExpression($__ctx),"html"=>new \Bayrell\Lang\LangPHP\TranslatorPHPHtml($__ctx),"operator"=>new \Bayrell\Lang\LangPHP\TranslatorPHPOperator($__ctx),"program"=>new \Bayrell\Lang\LangPHP\TranslatorPHPProgram($__ctx),"save_vars"=>new \Runtime\Collection($__ctx),"save_op_codes"=>new \Runtime\Collection($__ctx),"save_op_code_inc"=>0,"preprocessor_flags"=>\Runtime\Dict::from(["PHP"=>true])]));
	}
	/**
	 * Translate BaseOpCode
	 */
	static function translate($__ctx, $t, $op_code)
	{
		$t = static::reset($__ctx, $t);
		return $t->program->staticMethod("translateProgram")($__ctx, $t, $op_code);
	}
	/**
	 * Inc save op code
	 */
	static function nextSaveOpCode($__ctx, $t)
	{
		return "$__v" . \Runtime\rtl::toStr($t->save_op_code_inc);
	}
	/**
	 * Output save op code content
	 */
	static function outputSaveOpCode($__ctx, $t, $save_op_code_value=0)
	{
		$content = "";
		for ($i = 0;$i < $t->save_op_codes->count($__ctx);$i++)
		{
			if ($i < $save_op_code_value)
			{
				continue;
			}
			$save = $t->save_op_codes->item($__ctx, $i);
			$s = ($save->content == "") ? $t->s($__ctx, $save->var_name . \Runtime\rtl::toStr(" = ") . \Runtime\rtl::toStr($save->var_content) . \Runtime\rtl::toStr(";")) : $save->content;
			$content .= \Runtime\rtl::toStr($s);
		}
		return $content;
	}
	/* ======================= Class Init Functions ======================= */
	function _init($__ctx)
	{
		parent::_init($__ctx);
		$this->__expression = null;
		$this->__html = null;
		$this->__operator = null;
		$this->__program = null;
	}
	function assignObject($__ctx,$o)
	{
		if ($o instanceof \Bayrell\Lang\LangPHP\TranslatorPHP)
		{
			$this->__expression = $o->__expression;
			$this->__html = $o->__html;
			$this->__operator = $o->__operator;
			$this->__program = $o->__program;
		}
		parent::assignObject($__ctx,$o);
	}
	function assignValue($__ctx,$k,$v)
	{
		if ($k == "expression")$this->__expression = $v;
		else if ($k == "html")$this->__html = $v;
		else if ($k == "operator")$this->__operator = $v;
		else if ($k == "program")$this->__program = $v;
		else parent::assignValue($__ctx,$k,$v);
	}
	function takeValue($__ctx,$k,$d=null)
	{
		if ($k == "expression")return $this->__expression;
		else if ($k == "html")return $this->__html;
		else if ($k == "operator")return $this->__operator;
		else if ($k == "program")return $this->__program;
		return parent::takeValue($__ctx,$k,$d);
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
	static function getClassInfo($__ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($__ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.LangPHP.TranslatorPHP",
			"name"=>"Bayrell.Lang.LangPHP.TranslatorPHP",
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
	}
	static function getFieldsList($__ctx,$f)
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