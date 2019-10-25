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
namespace Bayrell\Lang\LangBay;
class ParserBay extends \Bayrell\Lang\CoreParser
{
	public $vars;
	public $uses;
	public $current_namespace;
	public $current_class;
	public $current_namespace_name;
	public $current_class_name;
	public $current_class_kind;
	public $find_identifier;
	public $skip_comments;
	public $parser_base;
	public $parser_expression;
	public $parser_html;
	public $parser_operator;
	public $parser_preprocessor;
	public $parser_program;
	/**
	 * Reset parser
	 */
	static function reset($__ctx, $parser)
	{
		return $parser->copy($__ctx, \Runtime\Dict::from(["vars"=>new \Runtime\Dict($__ctx),"uses"=>new \Runtime\Dict($__ctx),"caret"=>new \Bayrell\Lang\Caret($__ctx, \Runtime\Dict::from([])),"token"=>null,"parser_base"=>new \Bayrell\Lang\LangBay\ParserBayBase($__ctx),"parser_expression"=>new \Bayrell\Lang\LangBay\ParserBayExpression($__ctx),"parser_html"=>new \Bayrell\Lang\LangBay\ParserBayHtml($__ctx),"parser_operator"=>new \Bayrell\Lang\LangBay\ParserBayOperator($__ctx),"parser_preprocessor"=>new \Bayrell\Lang\LangBay\ParserBayPreprocessor($__ctx),"parser_program"=>new \Bayrell\Lang\LangBay\ParserBayProgram($__ctx)]));
	}
	/**
	 * Parse file and convert to BaseOpCode
	 */
	static function parse($__ctx, $parser, $content)
	{
		$parser = static::reset($__ctx, $parser);
		$parser = static::setContent($__ctx, $parser, $content);
		return $parser->parser_program->staticMethod("readProgram")($__ctx, $parser);
	}
	/* ======================= Class Init Functions ======================= */
	function _init($__ctx)
	{
		parent::_init($__ctx);
		$this->vars = null;
		$this->uses = null;
		$this->current_namespace = null;
		$this->current_class = null;
		$this->current_namespace_name = "";
		$this->current_class_name = "";
		$this->current_class_kind = "";
		$this->find_identifier = true;
		$this->skip_comments = true;
		$this->parser_base = null;
		$this->parser_expression = null;
		$this->parser_html = null;
		$this->parser_operator = null;
		$this->parser_preprocessor = null;
		$this->parser_program = null;
	}
	function getClassName()
	{
		return "Bayrell.Lang.LangBay.ParserBay";
	}
	static function getCurrentNamespace()
	{
		return "Bayrell.Lang.LangBay";
	}
	static function getCurrentClassName()
	{
		return "Bayrell.Lang.LangBay.ParserBay";
	}
	static function getParentClassName()
	{
		return "Bayrell.Lang.CoreParser";
	}
	static function getClassInfo($__ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($__ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.LangBay.ParserBay",
			"name"=>"Bayrell.Lang.LangBay.ParserBay",
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