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
class ParserBayPreprocessor
{
	/**
	 * Read namespace
	 */
	static function readPreprocessor($ctx, $parser)
	{
		$start = $parser->clone($ctx);
		$look = null;
		$token = null;
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		if ($token->content == "#switch")
		{
			return static::readPreprocessorSwitch($ctx, $start);
		}
		if ($token->content == "#ifcode")
		{
			return static::readPreprocessorIfCode($ctx, $start);
		}
		return null;
	}
	/**
	 * Read preprocessor switch
	 */
	static function readPreprocessorSwitch($ctx, $parser)
	{
		$look = null;
		$token = null;
		$op_code = null;
		$items = new \Runtime\Vector($ctx);
		/* Save vars */
		$save_vars = $parser->vars;
		$parser = $parser->copy($ctx, ["vars"=>$parser->vars->concat($ctx, \Runtime\Dict::from(["ES6"=>true,"NODEJS"=>true,"JAVASCRIPT"=>true,"PHP"=>true,"PYTHON3"=>true]))]);
		$res = $parser->parser_base::matchToken($ctx, $parser, "#switch");
		$parser = $res[0];
		$token = $res[1];
		$caret_start = $token->caret_start->clone($ctx);
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		while ($token->content == "#case")
		{
			$parser = $look->clone($ctx);
			/* Skip ifcode */
			$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
			$look = $res[0];
			$token = $res[1];
			if ($token->content == "ifcode")
			{
				$parser = $look->clone($ctx);
			}
			/* Read condition */
			$condition = null;
			$parser = $parser->copy($ctx, ["find_ident"=>false]);
			$res = $parser->parser_expression::readExpression($ctx, $parser);
			$parser = $res[0];
			$condition = $res[1];
			$parser = $parser->copy($ctx, ["find_ident"=>true]);
			/* Read then */
			$res = $parser->parser_base::matchToken($ctx, $parser, "then");
			$parser = $res[0];
			$token = $res[1];
			/* Read content */
			$content = "";
			$caret_content = $parser->caret->clone($ctx);
			$res = $parser->parser_base::readUntilStringArr($ctx, $parser, \Runtime\Collection::from(["#case","#endswitch"]), false);
			$parser = $res[0];
			$content = $res[1];
			/* Look content */
			$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
			$look = $res[0];
			$token = $res[1];
			$ifcode = new \Bayrell\Lang\OpCodes\OpPreprocessorIfCode($ctx, \Runtime\Dict::from(["condition"=>$condition,"content"=>$content,"caret_start"=>$caret_content,"caret_end"=>$parser->caret->clone($ctx)]));
			$items->push($ctx, $ifcode);
		}
		/* Restore vars */
		$parser = $parser->copy($ctx, ["vars"=>$save_vars]);
		/* read endswitch */
		$res = $parser->parser_base::matchToken($ctx, $parser, "#endswitch");
		$parser = $res[0];
		return \Runtime\Collection::from([$parser,new \Bayrell\Lang\OpCodes\OpPreprocessorSwitch($ctx, \Runtime\Dict::from(["items"=>$items->toCollection($ctx),"caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($ctx)]))]);
	}
	/**
	 * Read preprocessor ifcode
	 */
	static function readPreprocessorIfCode($ctx, $parser)
	{
		$look = null;
		$token = null;
		$caret_start = $parser->caret;
		$res = $parser->parser_base::matchToken($ctx, $parser, "#ifcode");
		$parser = $res[0];
		$token = $res[1];
		/* Read condition */
		$condition = null;
		$parser = $parser->copy($ctx, ["find_ident"=>false]);
		$res = $parser->parser_expression::readExpression($ctx, $parser);
		$parser = $res[0];
		$condition = $res[1];
		$parser = $parser->copy($ctx, ["find_ident"=>true]);
		/* Read then */
		$res = $parser->parser_base::matchToken($ctx, $parser, "then");
		$parser = $res[0];
		$token = $res[1];
		/* Read content */
		$content = "";
		$caret_content = $parser->caret->clone($ctx);
		$res = $parser->parser_base::readUntilStringArr($ctx, $parser, \Runtime\Collection::from(["#endif"]), false);
		$parser = $res[0];
		$content = $res[1];
		/* Match endif */
		$res = $parser->parser_base::matchToken($ctx, $parser, "#endif");
		$parser = $res[0];
		$token = $res[1];
		$ifcode = new \Bayrell\Lang\OpCodes\OpPreprocessorIfCode($ctx, \Runtime\Dict::from(["condition"=>$condition,"content"=>$content,"caret_start"=>$caret_content,"caret_end"=>$parser->caret]));
		return \Runtime\Collection::from([$parser,$ifcode]);
	}
	/**
	 * Read preprocessor ifdef
	 */
	static function readPreprocessorIfDef($ctx, $parser, $kind="")
	{
		$items = null;
		$token = null;
		$caret_start = $parser->caret;
		$res = $parser->parser_base::matchToken($ctx, $parser, "#ifdef");
		$parser = $res[0];
		$token = $res[1];
		/* Read condition */
		$condition = null;
		$parser = $parser->copy($ctx, ["find_ident"=>false]);
		$res = $parser->parser_expression::readExpression($ctx, $parser);
		$parser = $res[0];
		$condition = $res[1];
		$parser = $parser->copy($ctx, ["find_ident"=>true]);
		/* Read then */
		$res = $parser->parser_base::matchToken($ctx, $parser, "then");
		$parser = $res[0];
		$token = $res[1];
		if ($kind == \Bayrell\Lang\OpCodes\OpPreprocessorIfDef::KIND_PROGRAM)
		{
			$res = $parser->parser_program::readProgram($ctx, $parser, "#endif");
			$parser = $res[0];
			$items = $res[1];
			$res = $parser->parser_base::matchToken($ctx, $parser, "#endif");
			$parser = $res[0];
		}
		else if ($kind == \Bayrell\Lang\OpCodes\OpPreprocessorIfDef::KIND_CLASS_BODY)
		{
			$res = $parser->parser_program::readClassBody($ctx, $parser, "#endif");
			$parser = $res[0];
			$items = $res[1];
			$res = $parser->parser_base::matchToken($ctx, $parser, "#endif");
			$parser = $res[0];
			$d = $parser->parser_program::classBodyAnalyze($ctx, $parser, $items);
			$items = $d->item($ctx, "functions");
		}
		else if ($kind == \Bayrell\Lang\OpCodes\OpPreprocessorIfDef::KIND_OPERATOR)
		{
			$res = $parser->parser_operator::readOpItems($ctx, $parser, "#endif");
			$parser = $res[0];
			$items = $res[1];
			$res = $parser->parser_base::matchToken($ctx, $parser, "#endif");
			$parser = $res[0];
		}
		else if ($kind == \Bayrell\Lang\OpCodes\OpPreprocessorIfDef::KIND_EXPRESSION)
		{
			$res = $parser->parser_expression::readExpression($ctx, $parser);
			$parser = $res[0];
			$items = $res[1];
			$res = $parser->parser_base::matchToken($ctx, $parser, "#endif");
			$parser = $res[0];
		}
		return \Runtime\Collection::from([$parser,new \Bayrell\Lang\OpCodes\OpPreprocessorIfDef($ctx, \Runtime\Dict::from(["items"=>$items,"condition"=>$condition,"caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($ctx)]))]);
	}
	/* ======================= Class Init Functions ======================= */
	function getClassName()
	{
		return "Bayrell.Lang.LangBay.ParserBayPreprocessor";
	}
	static function getCurrentNamespace()
	{
		return "Bayrell.Lang.LangBay";
	}
	static function getCurrentClassName()
	{
		return "Bayrell.Lang.LangBay.ParserBayPreprocessor";
	}
	static function getParentClassName()
	{
		return "";
	}
	static function getClassInfo($ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.LangBay.ParserBayPreprocessor",
			"name"=>"Bayrell.Lang.LangBay.ParserBayPreprocessor",
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