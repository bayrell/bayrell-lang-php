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
namespace Bayrell\Lang\LangBay;
class ParserBayExpression
{
	/**
	 * Read bit not
	 */
	static function readBitNot($ctx, $parser)
	{
		$look = null;
		$token = null;
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		$caret_start = $token->caret_start->clone($ctx);
		if ($token->content == "!")
		{
			$op_code = null;
			$res = $parser->parser_base::readDynamic($ctx, $look->clone($ctx));
			$parser = $res[0];
			$op_code = $res[1];
			return \Runtime\Collection::from([$parser,new \Bayrell\Lang\OpCodes\OpMath($ctx, \Runtime\Dict::from(["value1"=>$op_code,"math"=>"!","caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($ctx)]))]);
		}
		return $parser->parser_base::readDynamic($ctx, $parser);
	}
	/**
	 * Read bit shift
	 */
	static function readBitShift($ctx, $parser)
	{
		$look = null;
		$token = null;
		$op_code = null;
		$look_value = null;
		$res = static::readBitNot($ctx, $parser);
		$parser = $res[0];
		$op_code = $res[1];
		$caret_start = $op_code->caret_start->clone($ctx);
		$math = "";
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		while (!$token->eof && ($token->content == ">>" || $token->content == "<<"))
		{
			$math = $token->content;
			$res = static::readBitNot($ctx, $look);
			$look = $res[0];
			$look_value = $res[1];
			$op_code = new \Bayrell\Lang\OpCodes\OpMath($ctx, \Runtime\Dict::from(["value1"=>$op_code,"value2"=>$look_value,"math"=>$math,"caret_start"=>$caret_start,"caret_end"=>$look->caret->clone($ctx)]));
			$parser = $look->clone($ctx);
			$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
			$look = $res[0];
			$token = $res[1];
		}
		return \Runtime\Collection::from([$parser,$op_code]);
	}
	/**
	 * Read bit and
	 */
	static function readBitAnd($ctx, $parser)
	{
		$look = null;
		$token = null;
		$op_code = null;
		$look_value = null;
		$res = static::readBitShift($ctx, $parser);
		$parser = $res[0];
		$op_code = $res[1];
		$caret_start = $op_code->caret_start->clone($ctx);
		$math = "";
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		while (!$token->eof && $token->content == "&")
		{
			$math = $token->content;
			$res = static::readBitShift($ctx, $look);
			$look = $res[0];
			$look_value = $res[1];
			$op_code = new \Bayrell\Lang\OpCodes\OpMath($ctx, \Runtime\Dict::from(["value1"=>$op_code,"value2"=>$look_value,"math"=>$math,"caret_start"=>$caret_start,"caret_end"=>$look->caret->clone($ctx)]));
			$parser = $look->clone($ctx);
			$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
			$look = $res[0];
			$token = $res[1];
		}
		return \Runtime\Collection::from([$parser,$op_code]);
	}
	/**
	 * Read bit or
	 */
	static function readBitOr($ctx, $parser)
	{
		$look = null;
		$token = null;
		$op_code = null;
		$look_value = null;
		$res = static::readBitAnd($ctx, $parser);
		$parser = $res[0];
		$op_code = $res[1];
		$caret_start = $op_code->caret_start->clone($ctx);
		$math = "";
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		while (!$token->eof && ($token->content == "|" || $token->content == "xor"))
		{
			$math = $token->content;
			$res = static::readBitAnd($ctx, $look);
			$look = $res[0];
			$look_value = $res[1];
			$op_code = new \Bayrell\Lang\OpCodes\OpMath($ctx, \Runtime\Dict::from(["value1"=>$op_code,"value2"=>$look_value,"math"=>$math,"caret_start"=>$caret_start,"caret_end"=>$look->caret->clone($ctx)]));
			$parser = $look->clone($ctx);
			$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
			$look = $res[0];
			$token = $res[1];
		}
		return \Runtime\Collection::from([$parser,$op_code]);
	}
	/**
	 * Read factor
	 */
	static function readFactor($ctx, $parser)
	{
		$look = null;
		$token = null;
		$op_code = null;
		$look_value = null;
		$res = static::readBitOr($ctx, $parser);
		$parser = $res[0];
		$op_code = $res[1];
		$caret_start = $op_code->caret_start->clone($ctx);
		$math = "";
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		while (!$token->eof && ($token->content == "*" || $token->content == "/" || $token->content == "%" || $token->content == "div" || $token->content == "mod"))
		{
			$math = $token->content;
			$res = static::readBitOr($ctx, $look);
			$look = $res[0];
			$look_value = $res[1];
			$op_code = new \Bayrell\Lang\OpCodes\OpMath($ctx, \Runtime\Dict::from(["value1"=>$op_code,"value2"=>$look_value,"math"=>$math,"caret_start"=>$caret_start,"caret_end"=>$look->caret->clone($ctx)]));
			$parser = $look->clone($ctx);
			$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
			$look = $res[0];
			$token = $res[1];
		}
		return \Runtime\Collection::from([$parser,$op_code]);
	}
	/**
	 * Read arithmetic
	 */
	static function readArithmetic($ctx, $parser)
	{
		$look = null;
		$token = null;
		$op_code = null;
		$look_value = null;
		$res = static::readFactor($ctx, $parser);
		$parser = $res[0];
		$op_code = $res[1];
		$caret_start = $op_code->caret_start->clone($ctx);
		$math = "";
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		while (!$token->eof && ($token->content == "+" || $token->content == "-"))
		{
			$math = $token->content;
			$res = static::readFactor($ctx, $look);
			$look = $res[0];
			$look_value = $res[1];
			$op_code = new \Bayrell\Lang\OpCodes\OpMath($ctx, \Runtime\Dict::from(["value1"=>$op_code,"value2"=>$look_value,"math"=>$math,"caret_start"=>$caret_start,"caret_end"=>$look->caret->clone($ctx)]));
			$parser = $look->clone($ctx);
			$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
			$look = $res[0];
			$token = $res[1];
		}
		return \Runtime\Collection::from([$parser,$op_code]);
	}
	/**
	 * Read concat
	 */
	static function readConcat($ctx, $parser)
	{
		$look = null;
		$token = null;
		$op_code = null;
		$look_value = null;
		$res = static::readArithmetic($ctx, $parser);
		$parser = $res[0];
		$op_code = $res[1];
		$caret_start = $op_code->caret_start->clone($ctx);
		$math = "";
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		while (!$token->eof && $token->content == "~")
		{
			$math = $token->content;
			$res = static::readArithmetic($ctx, $look);
			$look = $res[0];
			$look_value = $res[1];
			$op_code = new \Bayrell\Lang\OpCodes\OpMath($ctx, \Runtime\Dict::from(["value1"=>$op_code,"value2"=>$look_value,"math"=>$math,"caret_start"=>$caret_start,"caret_end"=>$look->caret->clone($ctx)]));
			$parser = $look->clone($ctx);
			$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
			$look = $res[0];
			$token = $res[1];
		}
		return \Runtime\Collection::from([$parser,$op_code]);
	}
	/**
	 * Read compare
	 */
	static function readCompare($ctx, $parser)
	{
		$look = null;
		$token = null;
		$op_code = null;
		$look_value = null;
		$res = static::readConcat($ctx, $parser);
		$parser = $res[0];
		$op_code = $res[1];
		$caret_start = $op_code->caret_start->clone($ctx);
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		$content = $token->content;
		if ($content == "===" || $content == "!==" || $content == "==" || $content == "!=" || $content == ">=" || $content == "<=" || $content == ">" || $content == "<")
		{
			$math = $token->content;
			$res = static::readConcat($ctx, $look);
			$look = $res[0];
			$look_value = $res[1];
			$op_code = new \Bayrell\Lang\OpCodes\OpMath($ctx, \Runtime\Dict::from(["value1"=>$op_code,"value2"=>$look_value,"math"=>$math,"caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($ctx)]));
			$parser = $look->clone($ctx);
		}
		else if ($content == "is" || $content == "implements" || $content == "instanceof")
		{
			$math = $token->content;
			$res = $parser->parser_base::readTypeIdentifier($ctx, $look);
			$look = $res[0];
			$look_value = $res[1];
			$op_code = new \Bayrell\Lang\OpCodes\OpMath($ctx, \Runtime\Dict::from(["value1"=>$op_code,"value2"=>$look_value,"math"=>$math,"caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($ctx)]));
			$parser = $look->clone($ctx);
		}
		return \Runtime\Collection::from([$parser,$op_code]);
	}
	/**
	 * Read not
	 */
	static function readNot($ctx, $parser)
	{
		$look = null;
		$token = null;
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		$caret_start = $token->caret_start->clone($ctx);
		if ($token->content == "not")
		{
			$op_code = null;
			$start = $parser->clone($ctx);
			$res = static::readCompare($ctx, $look->clone($ctx));
			$parser = $res[0];
			$op_code = $res[1];
			return \Runtime\Collection::from([$parser,new \Bayrell\Lang\OpCodes\OpMath($ctx, \Runtime\Dict::from(["value1"=>$op_code,"math"=>"not","caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($ctx)]))]);
		}
		return static::readCompare($ctx, $parser);
	}
	/**
	 * Read and
	 */
	static function readAnd($ctx, $parser)
	{
		$look = null;
		$token = null;
		$op_code = null;
		$look_value = null;
		$res = static::readNot($ctx, $parser);
		$parser = $res[0];
		$op_code = $res[1];
		$caret_start = $op_code->caret_start->clone($ctx);
		$math = "";
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		while (!$token->eof && ($token->content == "and" || $token->content == "&&"))
		{
			$math = $token->content;
			$res = static::readNot($ctx, $look);
			$look = $res[0];
			$look_value = $res[1];
			$op_code = new \Bayrell\Lang\OpCodes\OpMath($ctx, \Runtime\Dict::from(["value1"=>$op_code,"value2"=>$look_value,"math"=>"and","caret_start"=>$caret_start,"caret_end"=>$look->caret->clone($ctx)]));
			$parser = $look->clone($ctx);
			$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
			$look = $res[0];
			$token = $res[1];
		}
		return \Runtime\Collection::from([$parser,$op_code]);
	}
	/**
	 * Read or
	 */
	static function readOr($ctx, $parser)
	{
		$look = null;
		$token = null;
		$op_code = null;
		$look_value = null;
		$res = static::readAnd($ctx, $parser);
		$parser = $res[0];
		$op_code = $res[1];
		$caret_start = $op_code->caret_start->clone($ctx);
		$math = "";
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		while (!$token->eof && ($token->content == "or" || $token->content == "||"))
		{
			$math = $token->content;
			$res = static::readAnd($ctx, $look);
			$look = $res[0];
			$look_value = $res[1];
			$op_code = new \Bayrell\Lang\OpCodes\OpMath($ctx, \Runtime\Dict::from(["value1"=>$op_code,"value2"=>$look_value,"math"=>"or","caret_start"=>$caret_start,"caret_end"=>$look->caret->clone($ctx)]));
			$parser = $look->clone($ctx);
			$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
			$look = $res[0];
			$token = $res[1];
		}
		return \Runtime\Collection::from([$parser,$op_code]);
	}
	/**
	 * Read element
	 */
	static function readElement($ctx, $parser)
	{
		/* Try to read function */
		if ($parser->parser_operator::tryReadFunction($ctx, $parser->clone($ctx), false))
		{
			return $parser->parser_operator::readDeclareFunction($ctx, $parser, false);
		}
		return static::readOr($ctx, $parser);
	}
	/**
	 * Read ternary operation
	 */
	static function readTernary($ctx, $parser)
	{
		$look = null;
		$token = null;
		$op_code = null;
		$condition = null;
		$if_true = null;
		$if_false = null;
		$res = static::readElement($ctx, $parser);
		$parser = $res[0];
		$op_code = $res[1];
		$caret_start = $op_code->caret_start->clone($ctx);
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		if ($token->content == "?")
		{
			$condition = $op_code;
			$res = static::readOr($ctx, $look);
			$parser = $res[0];
			$if_true = $res[1];
			$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
			$look = $res[0];
			$token = $res[1];
			if ($token->content == ":")
			{
				$res = static::readOr($ctx, $look);
				$parser = $res[0];
				$if_false = $res[1];
			}
			$op_code = new \Bayrell\Lang\OpCodes\OpTernary($ctx, \Runtime\Dict::from(["condition"=>$condition,"if_true"=>$if_true,"if_false"=>$if_false,"caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($ctx)]));
		}
		return \Runtime\Collection::from([$parser,$op_code]);
	}
	/**
	 * Read pipe call
	 */
	static function readPipeCall($ctx, $parser)
	{
		$look = null;
		$token = null;
		$op_code = null;
		$is_context_call = true;
		$caret_start = $parser->caret;
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		if ($token->content == "@")
		{
			$is_context_call = false;
			$parser = $look;
		}
		$args = null;
		$res = $parser->parser_base::readIdentifier($ctx, $parser);
		$parser = $res[0];
		$op_code = $res[1];
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$token = $res[1];
		if ($token->content == "(" || $token->content == "{")
		{
			$res = $parser->parser_base::readCallArgs($ctx, $parser);
			$parser = $res[0];
			$args = $res[1];
		}
		return \Runtime\Collection::from([$parser,new \Bayrell\Lang\OpCodes\OpCall($ctx, \Runtime\Dict::from(["obj"=>$op_code,"args"=>$args,"caret_start"=>$caret_start,"caret_end"=>$parser->caret,"is_context"=>$is_context_call]))]);
	}
	/**
	 * Read pipe
	 */
	static function ExpressionPipe($ctx, $parser)
	{
		$look = null;
		$look_token = null;
		$op_code = null;
		$res = static::readTernary($ctx, $parser);
		$parser = $res[0];
		$op_code = $res[1];
		$caret_start = $op_code->caret_start;
		$res = $parser->parser_base::readToken($ctx, $parser);
		$look = $res[0];
		$look_token = $res[1];
		while ($look_token->content == "->")
		{
			$parser = $look;
			$value = null;
			$kind = "";
			$is_async = false;
			$res = $parser->parser_base::readToken($ctx, $parser);
			$look = $res[0];
			$look_token = $res[1];
			if ($look_token->content == "await")
			{
				$is_async = true;
				$parser = $look;
				$res = $parser->parser_base::readToken($ctx, $parser);
				$look = $res[0];
				$look_token = $res[1];
			}
			if ($look_token->content == "attr")
			{
				$parser = $look;
				$res = static::readTernary($ctx, $parser);
				$parser = $res[0];
				$value = $res[1];
				$kind = \Bayrell\Lang\OpCodes\OpPipe::KIND_ATTR;
			}
			else if ($look_token->content == "monad")
			{
				$parser = $look;
				$res = $parser->parser_base::readDynamic($ctx, $parser);
				$parser = $res[0];
				$value = $res[1];
				$kind = \Bayrell\Lang\OpCodes\OpPipe::KIND_MONAD;
			}
			else if ($look_token->content == "method")
			{
				$parser = $look;
				$kind = \Bayrell\Lang\OpCodes\OpPipe::KIND_METHOD;
				$res = static::readPipeCall($ctx, $parser);
				$parser = $res[0];
				$value = $res[1];
			}
			else
			{
				$kind = \Bayrell\Lang\OpCodes\OpPipe::KIND_CALL;
				$res = $parser->parser_base::readDynamic($ctx, $parser);
				$parser = $res[0];
				$value = $res[1];
			}
			$op_code = new \Bayrell\Lang\OpCodes\OpPipe($ctx, \Runtime\Dict::from(["obj"=>$op_code,"kind"=>$kind,"value"=>$value,"is_async"=>$is_async]));
			$res = $parser->parser_base::readToken($ctx, $parser);
			$look = $res[0];
			$look_token = $res[1];
		}
		return \Runtime\Collection::from([$parser,$op_code]);
	}
	/**
	 * Read expression
	 */
	static function readExpression($ctx, $parser)
	{
		$look = null;
		$token = null;
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		if ($token->content == "<")
		{
			return $parser->parser_html::readHTML($ctx, $parser);
		}
		else if ($token->content == "@css")
		{
			return $parser->parser_html::readCss($ctx, $parser);
		}
		else if ($token->content == "#ifdef")
		{
			return $parser->parser_preprocessor::readPreprocessorIfDef($ctx, $parser, \Bayrell\Lang\OpCodes\OpPreprocessorIfDef::KIND_EXPRESSION);
		}
		return static::ExpressionPipe($ctx, $parser);
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
	static function getClassInfo($ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.LangBay.ParserBayExpression",
			"name"=>"Bayrell.Lang.LangBay.ParserBayExpression",
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