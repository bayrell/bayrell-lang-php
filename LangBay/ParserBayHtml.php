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
class ParserBayHtml extends \Runtime\CoreObject
{
	/**
	 * Read css selector
	 */
	static function readCssSelector($ctx, $parser)
	{
		$content = $parser->content;
		$content_sz = $parser->content_sz;
		$pos = $parser->caret->pos;
		$x = $parser->caret->x;
		$y = $parser->caret->y;
		$class_name = $parser->current_namespace_name . \Runtime\rtl::toStr(".") . \Runtime\rtl::toStr($parser->current_class_name);
		$ch = \Runtime\rs::substr($ctx, $content->ref, $pos, 1);
		if ($ch == "(")
		{
			$pos = $pos + 1;
			$x = $parser->parser_base::nextX($ctx, $parser, $ch, $x);
			$y = $parser->parser_base::nextY($ctx, $parser, $ch, $y);
			$start_pos = $pos;
			while ($pos < $content_sz && $ch != ")")
			{
				$pos = $pos + 1;
				$x = $parser->parser_base::nextX($ctx, $parser, $ch, $x);
				$y = $parser->parser_base::nextY($ctx, $parser, $ch, $y);
				$ch = \Runtime\rs::substr($ctx, $content->ref, $pos, 1);
			}
			$class_name = \Runtime\rs::substr($ctx, $content->ref, $start_pos, $pos - $start_pos);
			if ($parser->uses->has($ctx, $class_name))
			{
				$class_name = $parser->uses->item($ctx, $class_name);
			}
			$pos = $pos + 1;
			$x = $parser->parser_base::nextX($ctx, $parser, $ch, $x);
			$y = $parser->parser_base::nextY($ctx, $parser, $ch, $y);
		}
		$start_pos = $pos;
		$ch = \Runtime\rs::substr($ctx, $content->ref, $pos, 1);
		while ($pos < $content_sz && $ch != " " && $ch != "," && $ch != "." && $ch != ":" && $ch != "[" && $ch != "{")
		{
			$pos = $pos + 1;
			$x = $parser->parser_base::nextX($ctx, $parser, $ch, $x);
			$y = $parser->parser_base::nextY($ctx, $parser, $ch, $y);
			$ch = \Runtime\rs::substr($ctx, $content->ref, $pos, 1);
		}
		$postfix = \Runtime\rs::substr($ctx, $content->ref, $start_pos, $pos - $start_pos);
		$selector = "." . \Runtime\rtl::toStr($postfix) . \Runtime\rtl::toStr("-") . \Runtime\rtl::toStr(\Runtime\RuntimeUtils::getCssHash($ctx, $class_name));
		$caret = new \Bayrell\Lang\Caret($ctx, \Runtime\Dict::from(["x"=>$x,"y"=>$y,"pos"=>$pos]));
		$parser = $parser->copy($ctx, ["caret"=>$caret]);
		return \Runtime\Collection::from([$parser,$selector]);
	}
	/**
	 * Read css body
	 */
	static function readCssBody($ctx, $parser)
	{
		$caret_start = $parser->caret;
		$css_str = "";
		$content = $parser->content;
		$content_sz = $parser->content_sz;
		$pos = $parser->caret->pos;
		$x = $parser->caret->x;
		$y = $parser->caret->y;
		$bracket_level = 0;
		$start_pos = $pos;
		$ch = \Runtime\rs::substr($ctx, $content->ref, $pos, 1);
		while ($pos < $content_sz && ($ch != "}" || $ch == "}" && $bracket_level > 0))
		{
			/* If html or  tag */
			if ($ch == "%")
			{
				$x = $parser->parser_base::nextX($ctx, $parser, $ch, $x);
				$y = $parser->parser_base::nextY($ctx, $parser, $ch, $y);
				$pos = $pos + 1;
				/* Add value */
				$value = \Runtime\rs::substr($ctx, $content->ref, $start_pos, $pos - $start_pos - 1);
				if ($value != "")
				{
					$css_str .= \Runtime\rtl::toStr($value);
				}
				/* Read CSS Selector */
				$caret = new \Bayrell\Lang\Caret($ctx, \Runtime\Dict::from(["x"=>$x,"y"=>$y,"pos"=>$pos]));
				$parser = $parser->copy($ctx, ["caret"=>$caret]);
				$res = static::readCssSelector($ctx, $parser);
				$parser = $res[0];
				$s = $res[1];
				$css_str .= \Runtime\rtl::toStr($s);
				/* Set pos, x, y */
				$caret_start = $parser->caret->clone($ctx);
				$pos = $parser->caret->pos;
				$x = $parser->caret->x;
				$y = $parser->caret->y;
				$start_pos = $pos;
			}
			else if ($ch == "@")
			{
				$x = $parser->parser_base::nextX($ctx, $parser, $ch, $x);
				$y = $parser->parser_base::nextY($ctx, $parser, $ch, $y);
				$pos = $pos + 1;
				$res = $parser->parser_base::readUntilStringArr($ctx, $parser, \Runtime\Collection::from(["{"]), false);
				$parser = $res[0];
				$s1 = $res[1];
				$res = $parser->parser_base::matchToken($ctx, $parser, "{");
				$parser = $res[0];
				$res = static::readCssBody($ctx, $parser);
				$parser = $res[0];
				$s2 = $res[1];
				$res = $parser->parser_base::matchToken($ctx, $parser, "}");
				$parser = $res[0];
				$css_str .= \Runtime\rtl::toStr($s1 . \Runtime\rtl::toStr("{") . \Runtime\rtl::toStr($s2) . \Runtime\rtl::toStr("}"));
				/* Set pos, x, y */
				$caret_start = $parser->caret->clone($ctx);
				$pos = $parser->caret->pos;
				$x = $parser->caret->x;
				$y = $parser->caret->y;
				$start_pos = $pos;
			}
			else if ($ch == "{")
			{
				/* Add value */
				$value = \Runtime\rs::substr($ctx, $content->ref, $start_pos, $pos - $start_pos);
				if ($value != "")
				{
					$css_str .= \Runtime\rtl::toStr($value);
				}
				/* Read CSS Block */
				$caret = new \Bayrell\Lang\Caret($ctx, \Runtime\Dict::from(["x"=>$x,"y"=>$y,"pos"=>$pos]));
				$parser = $parser->copy($ctx, ["caret"=>$caret]);
				$res = $parser->parser_base::matchToken($ctx, $parser, "{");
				$parser = $res[0];
				$res = $parser->parser_base::readUntilStringArr($ctx, $parser, \Runtime\Collection::from(["}"]), false);
				$parser = $res[0];
				$s = $res[1];
				$res = $parser->parser_base::matchToken($ctx, $parser, "}");
				$parser = $res[0];
				$css_str .= \Runtime\rtl::toStr("{" . \Runtime\rtl::toStr($s) . \Runtime\rtl::toStr("}"));
				/* Set pos, x, y */
				$caret_start = $parser->caret->clone($ctx);
				$pos = $parser->caret->pos;
				$x = $parser->caret->x;
				$y = $parser->caret->y;
				$start_pos = $pos;
			}
			else
			{
				$x = $parser->parser_base::nextX($ctx, $parser, $ch, $x);
				$y = $parser->parser_base::nextY($ctx, $parser, $ch, $y);
				$pos = $pos + 1;
			}
			$ch = \Runtime\rs::substr($ctx, $content->ref, $pos, 1);
		}
		/* Push item */
		$value = \Runtime\rs::substr($ctx, $content->ref, $start_pos, $pos - $start_pos);
		$caret = new \Bayrell\Lang\Caret($ctx, \Runtime\Dict::from(["x"=>$x,"y"=>$y,"pos"=>$pos]));
		if ($value != "")
		{
			$css_str .= \Runtime\rtl::toStr($value);
		}
		$parser = $parser->copy($ctx, ["caret"=>$caret]);
		return \Runtime\Collection::from([$parser,$css_str]);
	}
	/**
	 * Read css
	 */
	static function readCss($ctx, $parser)
	{
		$caret_start = $parser->caret->clone($ctx);
		$res = $parser->parser_base::matchToken($ctx, $parser, "@css");
		$parser = $res[0];
		$res = $parser->parser_base::matchToken($ctx, $parser, "{");
		$parser = $res[0];
		$res = static::readCssBody($ctx, $parser);
		$parser = $res[0];
		$css_str = $res[1];
		$caret = $parser->caret;
		$res = $parser->parser_base::matchToken($ctx, $parser, "}");
		$parser = $res[0];
		$css_str = \Runtime\rs::replace($ctx, "\t", "", $css_str);
		$css_str = \Runtime\rs::replace($ctx, "\n", "", $css_str);
		$op_code = new \Bayrell\Lang\OpCodes\OpString($ctx, \Runtime\Dict::from(["caret_start"=>$caret,"caret_end"=>$parser->caret->clone($ctx),"value"=>$css_str]));
		return \Runtime\Collection::from([$parser,$op_code]);
	}
	/**
	 * Read html value
	 */
	static function readHTMLValue($ctx, $parser)
	{
		$item = null;
		$caret = $parser->caret->clone($ctx);
		$content = $parser->content;
		$pos = $parser->caret->pos;
		$x = $parser->caret->x;
		$y = $parser->caret->y;
		$ch = \Runtime\rs::substr($ctx, $content->ref, $pos, 1);
		if ($ch == "<")
		{
			$res = static::readHTMLTag($ctx, $parser);
			$parser = $res[0];
			$item = $res[1];
		}
		else if ($ch == "{")
		{
			$res = $parser->parser_base::matchToken($ctx, $parser, "{");
			$parser = $res[0];
			$res = $parser->parser_expression::readExpression($ctx, $parser);
			$parser = $res[0];
			$item = $res[1];
			$res = $parser->parser_base::matchToken($ctx, $parser, "}");
			$parser = $res[0];
		}
		else if ($ch == "@")
		{
			$x = $parser->parser_base::nextX($ctx, $parser, $ch, $x);
			$y = $parser->parser_base::nextY($ctx, $parser, $ch, $y);
			$pos = $pos + 1;
			$ch3 = \Runtime\rs::substr($ctx, $content->ref, $pos, 3);
			$ch4 = \Runtime\rs::substr($ctx, $content->ref, $pos, 4);
			if ($ch3 == "raw" || $ch4 == "json" || $ch4 == "html")
			{
				if ($ch3 == "raw")
				{
					$res = $parser->parser_base::next($ctx, $parser, $ch3, $x, $y, $pos);
				}
				if ($ch4 == "json")
				{
					$res = $parser->parser_base::next($ctx, $parser, $ch4, $x, $y, $pos);
				}
				if ($ch4 == "html")
				{
					$res = $parser->parser_base::next($ctx, $parser, $ch4, $x, $y, $pos);
				}
				$x = $res[0];
				$y = $res[1];
				$pos = $res[2];
			}
			$caret = new \Bayrell\Lang\Caret($ctx, \Runtime\Dict::from(["x"=>$x,"y"=>$y,"pos"=>$pos]));
			$parser = $parser->copy($ctx, ["caret"=>$caret]);
			$res = $parser->parser_base::matchToken($ctx, $parser, "{");
			$parser = $res[0];
			$res = $parser->parser_expression::readExpression($ctx, $parser);
			$parser = $res[0];
			$item = $res[1];
			if ($ch3 == "raw")
			{
				$item = new \Bayrell\Lang\OpCodes\OpHtmlValue($ctx, \Runtime\Dict::from(["kind"=>\Bayrell\Lang\OpCodes\OpHtmlValue::KIND_RAW,"value"=>$item,"caret_start"=>$caret,"caret_end"=>$parser->caret->clone($ctx)]));
			}
			else if ($ch4 == "json")
			{
				$item = new \Bayrell\Lang\OpCodes\OpHtmlValue($ctx, \Runtime\Dict::from(["kind"=>\Bayrell\Lang\OpCodes\OpHtmlValue::KIND_JSON,"value"=>$item,"caret_start"=>$caret,"caret_end"=>$parser->caret->clone($ctx)]));
			}
			else if ($ch4 == "html")
			{
				$item = new \Bayrell\Lang\OpCodes\OpHtmlValue($ctx, \Runtime\Dict::from(["kind"=>\Bayrell\Lang\OpCodes\OpHtmlValue::KIND_HTML,"value"=>$item,"caret_start"=>$caret,"caret_end"=>$parser->caret->clone($ctx)]));
			}
			$res = $parser->parser_base::matchToken($ctx, $parser, "}");
			$parser = $res[0];
		}
		return \Runtime\Collection::from([$parser,$item]);
	}
	/**
	 * Read html attribute key
	 */
	static function readHTMLAttrKey($ctx, $parser)
	{
		$token = null;
		$look = null;
		$ident = null;
		$key = "";
		/* Look token */
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		if ($token->content == "@")
		{
			$parser = $look->clone($ctx);
			$key = "@";
		}
		$res = $parser->parser_base::readIdentifier($ctx, $parser);
		$parser = $res[0];
		$ident = $res[1];
		$key .= \Runtime\rtl::toStr($ident->value);
		/* Read attr */
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		while ($token->content == "-")
		{
			$res = $parser->parser_base::readIdentifier($ctx, $look->clone($ctx));
			$parser = $res[0];
			$ident = $res[1];
			$key .= \Runtime\rtl::toStr("-" . \Runtime\rtl::toStr($ident->value));
			/* Look next token */
			$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
			$look = $res[0];
			$token = $res[1];
		}
		/* Look token */
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		if ($token->content == ":")
		{
			$parser = $look->clone($ctx);
			$key .= \Runtime\rtl::toStr(":");
			$res = $parser->parser_base::readIdentifier($ctx, $parser);
			$parser = $res[0];
			$ident = $res[1];
			$key .= \Runtime\rtl::toStr($ident->value);
		}
		return \Runtime\Collection::from([$parser,$key]);
	}
	/**
	 * Read html attribute value
	 */
	static function readHTMLAttrValue($ctx, $parser)
	{
		$token = null;
		$look = null;
		$op_code = null;
		$ident = null;
		/* Look token */
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		if ($token->content == "{")
		{
			$res = $parser->parser_base::matchToken($ctx, $parser, "{");
			$parser = $res[0];
			$res = $parser->parser_expression::readExpression($ctx, $parser);
			$parser = $res[0];
			$op_code = $res[1];
			$res = $parser->parser_base::matchToken($ctx, $parser, "}");
			$parser = $res[0];
		}
		else if ($token->content == "@")
		{
			$res = static::readHTMLValue($ctx, $parser);
			$parser = $res[0];
			$op_code = $res[1];
		}
		else
		{
			$res = $parser->parser_base::readString($ctx, $parser);
			$parser = $res[0];
			$op_code = $res[1];
		}
		return \Runtime\Collection::from([$parser,$op_code]);
	}
	/**
	 * Read html attributes
	 */
	static function readHTMLAttrs($ctx, $parser)
	{
		$items = new \Runtime\Vector($ctx);
		$content = $parser->content;
		$content_sz = $parser->content_sz;
		$caret = $parser->parser_base::skipChar($ctx, $parser, $content, $parser->caret->clone($ctx));
		$ch = \Runtime\rs::substr($ctx, $content->ref, $caret->pos, 1);
		while ($ch != "/" && $ch != ">" && $caret->pos < $content_sz)
		{
			$caret_start = $caret;
			$parser = $parser->copy($ctx, ["caret"=>$caret->clone($ctx)]);
			$res = static::readHTMLAttrKey($ctx, $parser);
			$parser = $res[0];
			$key = $res[1];
			$res = $parser->parser_base::matchToken($ctx, $parser, "=");
			$parser = $res[0];
			$res = static::readHTMLAttrValue($ctx, $parser);
			$parser = $res[0];
			$value = $res[1];
			$items->push($ctx, new \Bayrell\Lang\OpCodes\OpHtmlAttribute($ctx, \Runtime\Dict::from(["key"=>$key,"value"=>$value,"caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($ctx)])));
			$caret = $parser->parser_base::skipChar($ctx, $parser, $content, $parser->caret->clone($ctx));
			$ch = \Runtime\rs::substr($ctx, $content->ref, $caret->pos, 1);
			$ch2 = \Runtime\rs::substr($ctx, $content->ref, $caret->pos, 2);
			if ($ch2 == "/>")
			{
				break;
			}
		}
		return \Runtime\Collection::from([$parser,$items->toCollection($ctx)]);
	}
	/**
	 * Read html template
	 */
	static function readHTMLContent($ctx, $parser, $end_tag)
	{
		$items = new \Runtime\Vector($ctx);
		$item = null;
		$token = null;
		$look = null;
		$caret = null;
		$caret_start = $parser->caret->clone($ctx);
		$content = $parser->content;
		$content_sz = $parser->content_sz;
		$pos = $parser->caret->pos;
		$x = $parser->caret->x;
		$y = $parser->caret->y;
		$start_pos = $pos;
		$end_tag_sz = \Runtime\rs::strlen($ctx, $end_tag);
		$ch2 = \Runtime\rs::substr($ctx, $content->ref, $pos, $end_tag_sz);
		while ($ch2 != $end_tag && $pos < $content_sz)
		{
			$ch = \Runtime\rs::substr($ctx, $content->ref, $pos, 1);
			/* If html or  tag */
			if ($ch == "<" || $ch == "{" || $ch == "@")
			{
				$value = \Runtime\rs::substr($ctx, $content->ref, $start_pos, $pos - $start_pos);
				$caret = new \Bayrell\Lang\Caret($ctx, \Runtime\Dict::from(["x"=>$x,"y"=>$y,"pos"=>$pos]));
				$value = \Runtime\rs::trim($ctx, $value, "\t\r\n");
				if ($value != "")
				{
					$item = new \Bayrell\Lang\OpCodes\OpHtmlContent($ctx, \Runtime\Dict::from(["value"=>$value,"caret_start"=>$caret_start,"caret_end"=>$caret]));
					$items->push($ctx, $item);
				}
				/* Read HTML Value */
				$parser = $parser->copy($ctx, ["caret"=>$caret]);
				$res = static::readHTMLValue($ctx, $parser);
				$parser = $res[0];
				$item = $res[1];
				$items->push($ctx, $item);
				/* Set pos, x, y */
				$caret_start = $parser->caret->clone($ctx);
				$pos = $parser->caret->pos;
				$x = $parser->caret->x;
				$y = $parser->caret->y;
				$start_pos = $pos;
			}
			else
			{
				$x = $parser->parser_base::nextX($ctx, $parser, $ch, $x);
				$y = $parser->parser_base::nextY($ctx, $parser, $ch, $y);
				$pos = $pos + 1;
			}
			$ch2 = \Runtime\rs::substr($ctx, $content->ref, $pos, $end_tag_sz);
		}
		/* Push item */
		$value = \Runtime\rs::substr($ctx, $content->ref, $start_pos, $pos - $start_pos);
		$value = \Runtime\rs::trim($ctx, $value, "\t\r\n");
		$caret = new \Bayrell\Lang\Caret($ctx, \Runtime\Dict::from(["x"=>$x,"y"=>$y,"pos"=>$pos]));
		if ($value != "")
		{
			$item = new \Bayrell\Lang\OpCodes\OpHtmlContent($ctx, \Runtime\Dict::from(["value"=>$value,"caret_start"=>$caret_start,"caret_end"=>$caret]));
			$items->push($ctx, $item);
		}
		return \Runtime\Collection::from([$parser->copy($ctx, \Runtime\Dict::from(["caret"=>$caret])),$items]);
	}
	/**
	 * Read html tag
	 */
	static function readHTMLTag($ctx, $parser)
	{
		$token = null;
		$look = null;
		$ident = null;
		$caret_items_start = null;
		$caret_items_end = null;
		$caret_start = $parser->caret->clone($ctx);
		$items = null;
		$op_code_name = null;
		$is_single_flag = false;
		$op_code_flag = false;
		$tag_name = "";
		/* Tag start */
		$res = $parser->parser_base::matchToken($ctx, $parser, "<");
		$parser = $res[0];
		/* Look token */
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		if ($token->content == "{")
		{
			$op_code_flag = true;
			$caret1 = $parser->caret->clone($ctx);
			$res = $parser->parser_base::matchToken($ctx, $parser, "{");
			$parser = $res[0];
			$res = $parser->parser_expression::readExpression($ctx, $parser);
			$parser = $res[0];
			$op_code_name = $res[1];
			$res = $parser->parser_base::matchToken($ctx, $parser, "}");
			$parser = $res[0];
			$caret2 = $parser->caret->clone($ctx);
			$tag_name = \Runtime\rs::substr($ctx, $parser->content->ref, $caret1->pos, $caret2->pos - $caret1->pos);
		}
		else if ($token->content == ">")
		{
			$op_code_flag = true;
			$tag_name = "";
		}
		else
		{
			$res = $parser->parser_base::readIdentifier($ctx, $parser, false);
			$parser = $res[0];
			$ident = $res[1];
			$tag_name = $ident->value;
		}
		$res = static::readHTMLAttrs($ctx, $parser);
		$parser = $res[0];
		$attrs = $res[1];
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		if ($token->content == "/")
		{
			$parser = $look->clone($ctx);
			$is_single_flag = true;
		}
		$res = $parser->parser_base::matchToken($ctx, $parser, ">");
		$parser = $res[0];
		if (!$is_single_flag)
		{
			/* Read items */
			$caret_items_start = $parser->caret->clone($ctx);
			$res = static::readHTMLContent($ctx, $parser, "</" . \Runtime\rtl::toStr($tag_name));
			$parser = $res[0];
			$items = $res[1];
			$caret_items_end = $parser->caret->clone($ctx);
			/* Tag end */
			if ($op_code_flag)
			{
				$res = $parser->parser_base::matchToken($ctx, $parser, "<");
				$parser = $res[0];
				$res = $parser->parser_base::matchToken($ctx, $parser, "/");
				$parser = $res[0];
				if ($tag_name)
				{
					$res = $parser->parser_base::matchString($ctx, $parser, $tag_name);
					$parser = $res[0];
				}
				$res = $parser->parser_base::matchToken($ctx, $parser, ">");
				$parser = $res[0];
			}
			else
			{
				$res = $parser->parser_base::matchToken($ctx, $parser, "<");
				$parser = $res[0];
				$res = $parser->parser_base::matchToken($ctx, $parser, "/");
				$parser = $res[0];
				if ($ident != null)
				{
					$res = $parser->parser_base::matchToken($ctx, $parser, $ident->value);
					$parser = $res[0];
				}
				$res = $parser->parser_base::matchToken($ctx, $parser, ">");
				$parser = $res[0];
			}
		}
		$op_code = new \Bayrell\Lang\OpCodes\OpHtmlTag($ctx, \Runtime\Dict::from(["attrs"=>$attrs,"tag_name"=>$tag_name,"op_code_name"=>$op_code_name,"caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($ctx),"items"=>($items != null) ? new \Bayrell\Lang\OpCodes\OpHtmlItems($ctx, \Runtime\Dict::from(["caret_start"=>$caret_items_start,"caret_end"=>$caret_items_end,"items"=>$items->toCollection($ctx)])) : null]));
		return \Runtime\Collection::from([$parser,$op_code]);
	}
	/**
	 * Read html template
	 */
	static function readHTML($ctx, $parser)
	{
		$look = null;
		$token = null;
		$items = new \Runtime\Vector($ctx);
		$caret_start = $parser->caret->clone($ctx);
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		$ch2 = \Runtime\rs::substr($ctx, $parser->content->ref, $parser->caret->pos, 2);
		while (!$token->eof && $token->content == "<" && $ch2 != "</")
		{
			$res = static::readHTMLTag($ctx, $parser);
			$parser = $res[0];
			$op_code = $res[1];
			$items->push($ctx, $op_code);
			$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
			$look = $res[0];
			$token = $res[1];
			$caret = $parser->parser_base::skipChar($ctx, $parser, $parser->content, $parser->caret->clone($ctx));
			$ch2 = \Runtime\rs::substr($ctx, $parser->content->ref, $caret->pos, 2);
		}
		$op_code = new \Bayrell\Lang\OpCodes\OpHtmlItems($ctx, \Runtime\Dict::from(["caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($ctx),"items"=>$items->toCollection($ctx)]));
		return \Runtime\Collection::from([$parser,$op_code]);
	}
	/* ======================= Class Init Functions ======================= */
	function getClassName()
	{
		return "Bayrell.Lang.LangBay.ParserBayHtml";
	}
	static function getCurrentNamespace()
	{
		return "Bayrell.Lang.LangBay";
	}
	static function getCurrentClassName()
	{
		return "Bayrell.Lang.LangBay.ParserBayHtml";
	}
	static function getParentClassName()
	{
		return "Runtime.CoreObject";
	}
	static function getClassInfo($ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.LangBay.ParserBayHtml",
			"name"=>"Bayrell.Lang.LangBay.ParserBayHtml",
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