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
class ParserBayPreprocessor
{
	/**
	 * Read namespace
	 */
	static function readPreprocessor($__ctx, $parser)
	{
		$start = $parser->clone($__ctx);
		$look = null;
		$token = null;
		$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
		$look = $res[0];
		$token = $res[1];
		if ($token->content == "#switch")
		{
			return static::readPreprocessorSwitch($__ctx, $start);
		}
		return null;
	}
	/**
	 * Read preprocessor switch
	 */
	static function readPreprocessorSwitch($__ctx, $parser)
	{
		$look = null;
		$token = null;
		$op_code = null;
		$items = new \Runtime\Vector($__ctx);
		/* Save vars */
		$save_vars = $parser->vars;
		$parser = $parser->copy($__ctx, ["vars"=>$parser->vars->concat($__ctx, \Runtime\Dict::from(["ES6"=>true,"NODEJS"=>true,"JAVASCRIPT"=>true,"PHP"=>true,"PYTHON3"=>true]))]);
		$res = $parser->parser_base->staticMethod("matchToken")($__ctx, $parser, "#switch");
		$parser = $res[0];
		$token = $res[1];
		$caret_start = $token->caret_start->clone($__ctx);
		$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
		$look = $res[0];
		$token = $res[1];
		while ($token->content == "#case")
		{
			$parser = $look->clone($__ctx);
			/* Skip ifcode */
			$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
			$look = $res[0];
			$token = $res[1];
			if ($token->content == "ifcode")
			{
				$parser = $look->clone($__ctx);
			}
			/* Read condition */
			$condition = null;
			$parser = $parser->copy($__ctx, ["find_ident"=>false]);
			$res = $parser->parser_expression->staticMethod("readExpression")($__ctx, $parser);
			$parser = $res[0];
			$condition = $res[1];
			$parser = $parser->copy($__ctx, ["find_ident"=>true]);
			/* Read then */
			$res = $parser->parser_base->staticMethod("matchToken")($__ctx, $parser, "then");
			$parser = $res[0];
			$token = $res[1];
			/* Read content */
			$content = "";
			$caret_content = $parser->caret->clone($__ctx);
			$res = $parser->parser_base->staticMethod("readUntilStringArr")($__ctx, $parser, \Runtime\Collection::from(["#case","#endswitch"]), false);
			$parser = $res[0];
			$content = $res[1];
			/* Look content */
			$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
			$look = $res[0];
			$token = $res[1];
			$ifcode = new \Bayrell\Lang\OpCodes\OpPreprocessorIfCode($__ctx, \Runtime\Dict::from(["condition"=>$condition,"content"=>$content,"caret_start"=>$caret_content,"caret_end"=>$parser->caret->clone($__ctx)]));
			$items->push($__ctx, $ifcode);
		}
		/* Restore vars */
		$parser = $parser->copy($__ctx, ["vars"=>$save_vars]);
		/* read endswitch */
		$res = $parser->parser_base->staticMethod("matchToken")($__ctx, $parser, "#endswitch");
		$parser = $res[0];
		return \Runtime\Collection::from([$parser,new \Bayrell\Lang\OpCodes\OpPreprocessorSwitch($__ctx, \Runtime\Dict::from(["items"=>$items->toCollection($__ctx),"caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($__ctx)]))]);
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
	static function getClassInfo($__ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($__ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.LangBay.ParserBayPreprocessor",
			"name"=>"Bayrell.Lang.LangBay.ParserBayPreprocessor",
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