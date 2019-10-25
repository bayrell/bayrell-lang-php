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
class ParserBayExpression
{
	/**
	 * Read bit not
	 */
	static function readBitNot($__ctx, $parser)
	{
		$look = null;
		$token = null;
		$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
		$look = $res[0];
		$token = $res[1];
		$caret_start = $token->caret_start->clone($__ctx);
		if ($token->content == "!")
		{
			$op_code = null;
			$res = $parser->parser_base->staticMethod("readBaseItem")($__ctx, $look->clone($__ctx));
			$parser = $res[0];
			$op_code = $res[1];
			return \Runtime\Collection::from([$parser,new \Bayrell\Lang\OpCodes\OpMath($__ctx, \Runtime\Dict::from(["value1"=>$op_code,"math"=>"!","caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($__ctx)]))]);
		}
		return $parser->parser_base->staticMethod("readBaseItem")($__ctx, $parser);
	}
	/**
	 * Read bit shift
	 */
	static function readBitShift($__ctx, $parser)
	{
		$look = null;
		$token = null;
		$op_code = null;
		$look_value = null;
		$res = static::readBitNot($__ctx, $parser);
		$parser = $res[0];
		$op_code = $res[1];
		$caret_start = $op_code->caret_start->clone($__ctx);
		$math = "";
		$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
		$look = $res[0];
		$token = $res[1];
		while (!$token->eof && ($token->content == ">>" || $token->content == "<<"))
		{
			$math = $token->content;
			$res = static::readBitNot($__ctx, $look);
			$look = $res[0];
			$look_value = $res[1];
			$op_code = new \Bayrell\Lang\OpCodes\OpMath($__ctx, \Runtime\Dict::from(["value1"=>$op_code,"value2"=>$look_value,"math"=>$math,"caret_start"=>$caret_start,"caret_end"=>$look->caret->clone($__ctx)]));
			$parser = $look->clone($__ctx);
			$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
			$look = $res[0];
			$token = $res[1];
		}
		return \Runtime\Collection::from([$parser,$op_code]);
	}
	/**
	 * Read bit and
	 */
	static function readBitAnd($__ctx, $parser)
	{
		$look = null;
		$token = null;
		$op_code = null;
		$look_value = null;
		$res = static::readBitShift($__ctx, $parser);
		$parser = $res[0];
		$op_code = $res[1];
		$caret_start = $op_code->caret_start->clone($__ctx);
		$math = "";
		$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
		$look = $res[0];
		$token = $res[1];
		while (!$token->eof && $token->content == "&")
		{
			$math = $token->content;
			$res = static::readBitShift($__ctx, $look);
			$look = $res[0];
			$look_value = $res[1];
			$op_code = new \Bayrell\Lang\OpCodes\OpMath($__ctx, \Runtime\Dict::from(["value1"=>$op_code,"value2"=>$look_value,"math"=>$math,"caret_start"=>$caret_start,"caret_end"=>$look->caret->clone($__ctx)]));
			$parser = $look->clone($__ctx);
			$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
			$look = $res[0];
			$token = $res[1];
		}
		return \Runtime\Collection::from([$parser,$op_code]);
	}
	/**
	 * Read bit or
	 */
	static function readBitOr($__ctx, $parser)
	{
		$look = null;
		$token = null;
		$op_code = null;
		$look_value = null;
		$res = static::readBitAnd($__ctx, $parser);
		$parser = $res[0];
		$op_code = $res[1];
		$caret_start = $op_code->caret_start->clone($__ctx);
		$math = "";
		$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
		$look = $res[0];
		$token = $res[1];
		while (!$token->eof && ($token->content == "|" || $token->content == "xor"))
		{
			$math = $token->content;
			$res = static::readBitAnd($__ctx, $look);
			$look = $res[0];
			$look_value = $res[1];
			$op_code = new \Bayrell\Lang\OpCodes\OpMath($__ctx, \Runtime\Dict::from(["value1"=>$op_code,"value2"=>$look_value,"math"=>$math,"caret_start"=>$caret_start,"caret_end"=>$look->caret->clone($__ctx)]));
			$parser = $look->clone($__ctx);
			$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
			$look = $res[0];
			$token = $res[1];
		}
		return \Runtime\Collection::from([$parser,$op_code]);
	}
	/**
	 * Read factor
	 */
	static function readFactor($__ctx, $parser)
	{
		$look = null;
		$token = null;
		$op_code = null;
		$look_value = null;
		$res = static::readBitOr($__ctx, $parser);
		$parser = $res[0];
		$op_code = $res[1];
		$caret_start = $op_code->caret_start->clone($__ctx);
		$math = "";
		$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
		$look = $res[0];
		$token = $res[1];
		while (!$token->eof && ($token->content == "*" || $token->content == "/" || $token->content == "%" || $token->content == "div" || $token->content == "mod"))
		{
			$math = $token->content;
			$res = static::readBitOr($__ctx, $look);
			$look = $res[0];
			$look_value = $res[1];
			$op_code = new \Bayrell\Lang\OpCodes\OpMath($__ctx, \Runtime\Dict::from(["value1"=>$op_code,"value2"=>$look_value,"math"=>$math,"caret_start"=>$caret_start,"caret_end"=>$look->caret->clone($__ctx)]));
			$parser = $look->clone($__ctx);
			$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
			$look = $res[0];
			$token = $res[1];
		}
		return \Runtime\Collection::from([$parser,$op_code]);
	}
	/**
	 * Read arithmetic
	 */
	static function readArithmetic($__ctx, $parser)
	{
		$look = null;
		$token = null;
		$op_code = null;
		$look_value = null;
		$res = static::readFactor($__ctx, $parser);
		$parser = $res[0];
		$op_code = $res[1];
		$caret_start = $op_code->caret_start->clone($__ctx);
		$math = "";
		$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
		$look = $res[0];
		$token = $res[1];
		while (!$token->eof && ($token->content == "+" || $token->content == "-"))
		{
			$math = $token->content;
			$res = static::readFactor($__ctx, $look);
			$look = $res[0];
			$look_value = $res[1];
			$op_code = new \Bayrell\Lang\OpCodes\OpMath($__ctx, \Runtime\Dict::from(["value1"=>$op_code,"value2"=>$look_value,"math"=>$math,"caret_start"=>$caret_start,"caret_end"=>$look->caret->clone($__ctx)]));
			$parser = $look->clone($__ctx);
			$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
			$look = $res[0];
			$token = $res[1];
		}
		return \Runtime\Collection::from([$parser,$op_code]);
	}
	/**
	 * Read concat
	 */
	static function readConcat($__ctx, $parser)
	{
		$look = null;
		$token = null;
		$op_code = null;
		$look_value = null;
		$res = static::readArithmetic($__ctx, $parser);
		$parser = $res[0];
		$op_code = $res[1];
		$caret_start = $op_code->caret_start->clone($__ctx);
		$math = "";
		$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
		$look = $res[0];
		$token = $res[1];
		while (!$token->eof && $token->content == "~")
		{
			$math = $token->content;
			$res = static::readArithmetic($__ctx, $look);
			$look = $res[0];
			$look_value = $res[1];
			$op_code = new \Bayrell\Lang\OpCodes\OpMath($__ctx, \Runtime\Dict::from(["value1"=>$op_code,"value2"=>$look_value,"math"=>$math,"caret_start"=>$caret_start,"caret_end"=>$look->caret->clone($__ctx)]));
			$parser = $look->clone($__ctx);
			$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
			$look = $res[0];
			$token = $res[1];
		}
		return \Runtime\Collection::from([$parser,$op_code]);
	}
	/**
	 * Read compare
	 */
	static function readCompare($__ctx, $parser)
	{
		$look = null;
		$token = null;
		$op_code = null;
		$look_value = null;
		$res = static::readConcat($__ctx, $parser);
		$parser = $res[0];
		$op_code = $res[1];
		$caret_start = $op_code->caret_start->clone($__ctx);
		$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
		$look = $res[0];
		$token = $res[1];
		$content = $token->content;
		if ($content == "===" || $content == "!==" || $content == "==" || $content == "!=" || $content == ">=" || $content == "<=" || $content == ">" || $content == "<")
		{
			$math = $token->content;
			$res = static::readConcat($__ctx, $look);
			$look = $res[0];
			$look_value = $res[1];
			$op_code = new \Bayrell\Lang\OpCodes\OpMath($__ctx, \Runtime\Dict::from(["value1"=>$op_code,"value2"=>$look_value,"math"=>$math,"caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($__ctx)]));
			$parser = $look->clone($__ctx);
		}
		else if ($content == "is" || $content == "implements" || $content == "instanceof")
		{
			$math = $token->content;
			$res = $parser->parser_base->staticMethod("readTypeIdentifier")($__ctx, $look);
			$look = $res[0];
			$look_value = $res[1];
			$op_code = new \Bayrell\Lang\OpCodes\OpMath($__ctx, \Runtime\Dict::from(["value1"=>$op_code,"value2"=>$look_value,"math"=>$math,"caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($__ctx)]));
			$parser = $look->clone($__ctx);
		}
		return \Runtime\Collection::from([$parser,$op_code]);
	}
	/**
	 * Read not
	 */
	static function readNot($__ctx, $parser)
	{
		$look = null;
		$token = null;
		$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
		$look = $res[0];
		$token = $res[1];
		$caret_start = $token->caret_start->clone($__ctx);
		if ($token->content == "not")
		{
			$op_code = null;
			$start = $parser->clone($__ctx);
			$res = static::readCompare($__ctx, $look->clone($__ctx));
			$parser = $res[0];
			$op_code = $res[1];
			return \Runtime\Collection::from([$parser,new \Bayrell\Lang\OpCodes\OpMath($__ctx, \Runtime\Dict::from(["value1"=>$op_code,"math"=>"not","caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($__ctx)]))]);
		}
		return static::readCompare($__ctx, $parser);
	}
	/**
	 * Read and
	 */
	static function readAnd($__ctx, $parser)
	{
		$look = null;
		$token = null;
		$op_code = null;
		$look_value = null;
		$res = static::readNot($__ctx, $parser);
		$parser = $res[0];
		$op_code = $res[1];
		$caret_start = $op_code->caret_start->clone($__ctx);
		$math = "";
		$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
		$look = $res[0];
		$token = $res[1];
		while (!$token->eof && ($token->content == "and" || $token->content == "&&"))
		{
			$math = $token->content;
			$res = static::readNot($__ctx, $look);
			$look = $res[0];
			$look_value = $res[1];
			$op_code = new \Bayrell\Lang\OpCodes\OpMath($__ctx, \Runtime\Dict::from(["value1"=>$op_code,"value2"=>$look_value,"math"=>"and","caret_start"=>$caret_start,"caret_end"=>$look->caret->clone($__ctx)]));
			$parser = $look->clone($__ctx);
			$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
			$look = $res[0];
			$token = $res[1];
		}
		return \Runtime\Collection::from([$parser,$op_code]);
	}
	/**
	 * Read or
	 */
	static function readOr($__ctx, $parser)
	{
		$look = null;
		$token = null;
		$op_code = null;
		$look_value = null;
		$res = static::readAnd($__ctx, $parser);
		$parser = $res[0];
		$op_code = $res[1];
		$caret_start = $op_code->caret_start->clone($__ctx);
		$math = "";
		$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
		$look = $res[0];
		$token = $res[1];
		while (!$token->eof && ($token->content == "or" || $token->content == "||"))
		{
			$math = $token->content;
			$res = static::readAnd($__ctx, $look);
			$look = $res[0];
			$look_value = $res[1];
			$op_code = new \Bayrell\Lang\OpCodes\OpMath($__ctx, \Runtime\Dict::from(["value1"=>$op_code,"value2"=>$look_value,"math"=>"or","caret_start"=>$caret_start,"caret_end"=>$look->caret->clone($__ctx)]));
			$parser = $look->clone($__ctx);
			$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
			$look = $res[0];
			$token = $res[1];
		}
		return \Runtime\Collection::from([$parser,$op_code]);
	}
	/**
	 * Read element
	 */
	static function readElement($__ctx, $parser)
	{
		/* Try to read function */
		if ($parser->parser_operator->staticMethod("tryReadFunction")($__ctx, $parser->clone($__ctx), false))
		{
			return $parser->parser_operator->staticMethod("readDeclareFunction")($__ctx, $parser, false);
		}
		return static::readOr($__ctx, $parser);
	}
	/**
	 * Read ternary operation
	 */
	static function readTernary($__ctx, $parser)
	{
		$look = null;
		$token = null;
		$op_code = null;
		$condition = null;
		$if_true = null;
		$if_false = null;
		$res = static::readElement($__ctx, $parser);
		$parser = $res[0];
		$op_code = $res[1];
		$caret_start = $op_code->caret_start->clone($__ctx);
		$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
		$look = $res[0];
		$token = $res[1];
		if ($token->content == "?")
		{
			$condition = $op_code;
			$res = static::readOr($__ctx, $look);
			$parser = $res[0];
			$if_true = $res[1];
			$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
			$look = $res[0];
			$token = $res[1];
			if ($token->content == ":")
			{
				$res = static::readOr($__ctx, $look);
				$parser = $res[0];
				$if_false = $res[1];
			}
			$op_code = new \Bayrell\Lang\OpCodes\OpTernary($__ctx, \Runtime\Dict::from(["condition"=>$condition,"if_true"=>$if_true,"if_false"=>$if_false,"caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($__ctx)]));
		}
		return \Runtime\Collection::from([$parser,$op_code]);
	}
	/**
	 * Read expression
	 */
	static function readExpression($__ctx, $parser)
	{
		$look = null;
		$token = null;
		$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
		$look = $res[0];
		$token = $res[1];
		if ($token->content == "<")
		{
			return $parser->parser_html->staticMethod("readHTML")($__ctx, $parser);
		}
		if ($token->content == "@css")
		{
			return $parser->parser_html->staticMethod("readCss")($__ctx, $parser);
		}
		return static::readTernary($__ctx, $parser);
	}
	/* ======================= Class Init Functions ======================= */
	function getClassName()
	{
		return "Bayrell.Lang.LangBay.ParserBayExpression";
	}
	static function getCurrentNamespace()
	{
		return "Bayrell.Lang.LangBay";
	}
	static function getCurrentClassName()
	{
		return "Bayrell.Lang.LangBay.ParserBayExpression";
	}
	static function getParentClassName()
	{
		return "";
	}
	static function getClassInfo($__ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($__ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.LangBay.ParserBayExpression",
			"name"=>"Bayrell.Lang.LangBay.ParserBayExpression",
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