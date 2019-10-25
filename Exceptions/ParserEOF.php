<?php
/*!
 *  Bayrell Parser Library.
 *
 *  (c) Copyright 2016-2018 "Ildar Bikmamatov" <support@bayrell.org>
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
namespace Bayrell\Lang\Exceptions;
class ParserEOF extends \Bayrell\Lang\Exceptions\ParserUnknownError
{
	function __construct($__ctx, $context, $prev=null)
	{
		parent::__construct($__ctx, "ERROR_PARSER_EOF", \Bayrell\Lang\LangConstant::ERROR_PARSER_EOF, $context, $prev);
	}
	/* ======================= Class Init Functions ======================= */
	function getClassName()
	{
		return "Bayrell.Lang.Exceptions.ParserEOF";
	}
	static function getCurrentNamespace()
	{
		return "Bayrell.Lang.Exceptions";
	}
	static function getCurrentClassName()
	{
		return "Bayrell.Lang.Exceptions.ParserEOF";
	}
	static function getParentClassName()
	{
		return "Bayrell.Lang.Exceptions.ParserUnknownError";
	}
	static function getClassInfo($__ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($__ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.Exceptions.ParserEOF",
			"name"=>"Bayrell.Lang.Exceptions.ParserEOF",
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