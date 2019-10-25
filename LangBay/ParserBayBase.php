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
class ParserBayBase
{
	/**
	 * Return true if is char
	 * @param char ch
	 * @return boolean
	 */
	static function isChar($__ctx, $ch)
	{
		$__memorize_value = \Runtime\rtl::_memorizeValue("Bayrell.Lang.LangBay.ParserBayBase.isChar", func_get_args());
		if ($__memorize_value != \Runtime\rtl::$_memorize_not_found) return $__memorize_value;$__memorize_value = \Runtime\rs::strpos($__ctx, "qazwsxedcrfvtgbyhnujmikolp", \Runtime\rs::strtolower($__ctx, $ch)) !== -1;
		\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isChar", func_get_args(), $__memorize_value);
		return $__memorize_value;
	}
	/**
	 * Return true if is number
	 * @param char ch
	 * @return boolean
	 */
	static function isNumber($__ctx, $ch)
	{
		$__memorize_value = \Runtime\rtl::_memorizeValue("Bayrell.Lang.LangBay.ParserBayBase.isNumber", func_get_args());
		if ($__memorize_value != \Runtime\rtl::$_memorize_not_found) return $__memorize_value;$__memorize_value = \Runtime\rs::strpos($__ctx, "0123456789", $ch) !== -1;
		\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isNumber", func_get_args(), $__memorize_value);
		return $__memorize_value;
	}
	/**
	 * Return true if char is number
	 * @param char ch
	 * @return boolean
	 */
	static function isHexChar($__ctx, $ch)
	{
		$__memorize_value = \Runtime\rtl::_memorizeValue("Bayrell.Lang.LangBay.ParserBayBase.isHexChar", func_get_args());
		if ($__memorize_value != \Runtime\rtl::$_memorize_not_found) return $__memorize_value;$__memorize_value = \Runtime\rs::strpos($__ctx, "0123456789abcdef", \Runtime\rs::strtolower($__ctx, $ch)) !== -1;
		\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isHexChar", func_get_args(), $__memorize_value);
		return $__memorize_value;
	}
	/**
	 * Return true if is string of numbers
	 * @param string s
	 * @return boolean
	 */
	static function isStringOfNumbers($__ctx, $s)
	{
		$__memorize_value = \Runtime\rtl::_memorizeValue("Bayrell.Lang.LangBay.ParserBayBase.isStringOfNumbers", func_get_args());
		if ($__memorize_value != \Runtime\rtl::$_memorize_not_found) return $__memorize_value;
		$sz = \Runtime\rs::strlen($__ctx, $s);
		for ($i = 0;$i < $sz;$i++)
		{
			if (!static::isNumber($__ctx, \Runtime\rs::charAt($__ctx, $s, $i)))
			{$__memorize_value = false;
				\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isStringOfNumbers", func_get_args(), $__memorize_value);
				return $__memorize_value;
			}
		}$__memorize_value = true;
		\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isStringOfNumbers", func_get_args(), $__memorize_value);
		return $__memorize_value;
	}
	/**
	 * Is system type
	 */
	static function isSystemType($__ctx, $name)
	{
		$__memorize_value = \Runtime\rtl::_memorizeValue("Bayrell.Lang.LangBay.ParserBayBase.isSystemType", func_get_args());
		if ($__memorize_value != \Runtime\rtl::$_memorize_not_found) return $__memorize_value;
		if ($name == "var")
		{$__memorize_value = true;
			\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isSystemType", func_get_args(), $__memorize_value);
			return $__memorize_value;
		}
		if ($name == "void")
		{$__memorize_value = true;
			\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isSystemType", func_get_args(), $__memorize_value);
			return $__memorize_value;
		}
		if ($name == "bool")
		{$__memorize_value = true;
			\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isSystemType", func_get_args(), $__memorize_value);
			return $__memorize_value;
		}
		if ($name == "byte")
		{$__memorize_value = true;
			\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isSystemType", func_get_args(), $__memorize_value);
			return $__memorize_value;
		}
		if ($name == "int")
		{$__memorize_value = true;
			\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isSystemType", func_get_args(), $__memorize_value);
			return $__memorize_value;
		}
		if ($name == "double")
		{$__memorize_value = true;
			\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isSystemType", func_get_args(), $__memorize_value);
			return $__memorize_value;
		}
		if ($name == "float")
		{$__memorize_value = true;
			\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isSystemType", func_get_args(), $__memorize_value);
			return $__memorize_value;
		}
		if ($name == "char")
		{$__memorize_value = true;
			\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isSystemType", func_get_args(), $__memorize_value);
			return $__memorize_value;
		}
		if ($name == "string")
		{$__memorize_value = true;
			\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isSystemType", func_get_args(), $__memorize_value);
			return $__memorize_value;
		}
		if ($name == "list")
		{$__memorize_value = true;
			\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isSystemType", func_get_args(), $__memorize_value);
			return $__memorize_value;
		}
		if ($name == "scalar")
		{$__memorize_value = true;
			\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isSystemType", func_get_args(), $__memorize_value);
			return $__memorize_value;
		}
		if ($name == "primitive")
		{$__memorize_value = true;
			\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isSystemType", func_get_args(), $__memorize_value);
			return $__memorize_value;
		}
		if ($name == "html")
		{$__memorize_value = true;
			\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isSystemType", func_get_args(), $__memorize_value);
			return $__memorize_value;
		}
		if ($name == "Error")
		{$__memorize_value = true;
			\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isSystemType", func_get_args(), $__memorize_value);
			return $__memorize_value;
		}
		if ($name == "Object")
		{$__memorize_value = true;
			\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isSystemType", func_get_args(), $__memorize_value);
			return $__memorize_value;
		}
		if ($name == "DateTime")
		{$__memorize_value = true;
			\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isSystemType", func_get_args(), $__memorize_value);
			return $__memorize_value;
		}
		if ($name == "Collection")
		{$__memorize_value = true;
			\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isSystemType", func_get_args(), $__memorize_value);
			return $__memorize_value;
		}
		if ($name == "Dict")
		{$__memorize_value = true;
			\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isSystemType", func_get_args(), $__memorize_value);
			return $__memorize_value;
		}
		if ($name == "Vector")
		{$__memorize_value = true;
			\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isSystemType", func_get_args(), $__memorize_value);
			return $__memorize_value;
		}
		if ($name == "Map")
		{$__memorize_value = true;
			\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isSystemType", func_get_args(), $__memorize_value);
			return $__memorize_value;
		}
		if ($name == "rs")
		{$__memorize_value = true;
			\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isSystemType", func_get_args(), $__memorize_value);
			return $__memorize_value;
		}
		if ($name == "rtl")
		{$__memorize_value = true;
			\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isSystemType", func_get_args(), $__memorize_value);
			return $__memorize_value;
		}
		if ($name == "ArrayInterface")
		{$__memorize_value = true;
			\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isSystemType", func_get_args(), $__memorize_value);
			return $__memorize_value;
		}$__memorize_value = false;
		\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isSystemType", func_get_args(), $__memorize_value);
		return $__memorize_value;
	}
	/**
	 * Returns true if name is identifier
	 */
	static function isIdentifier($__ctx, $name)
	{
		$__memorize_value = \Runtime\rtl::_memorizeValue("Bayrell.Lang.LangBay.ParserBayBase.isIdentifier", func_get_args());
		if ($__memorize_value != \Runtime\rtl::$_memorize_not_found) return $__memorize_value;
		if ($name == "")
		{$__memorize_value = false;
			\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isIdentifier", func_get_args(), $__memorize_value);
			return $__memorize_value;
		}
		if ($name == "@")
		{$__memorize_value = true;
			\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isIdentifier", func_get_args(), $__memorize_value);
			return $__memorize_value;
		}
		if (static::isNumber($__ctx, \Runtime\rs::charAt($__ctx, $name, 0)))
		{$__memorize_value = false;
			\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isIdentifier", func_get_args(), $__memorize_value);
			return $__memorize_value;
		}
		$sz = \Runtime\rs::strlen($__ctx, $name);
		for ($i = 0;$i < $sz;$i++)
		{
			$ch = \Runtime\rs::charAt($__ctx, $name, $i);
			if (static::isChar($__ctx, $ch) || static::isNumber($__ctx, $ch) || $ch == "_")
			{
				continue;
			}$__memorize_value = false;
			\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isIdentifier", func_get_args(), $__memorize_value);
			return $__memorize_value;
		}$__memorize_value = true;
		\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isIdentifier", func_get_args(), $__memorize_value);
		return $__memorize_value;
	}
	/**
	 * Returns true if reserved words
	 */
	static function isReserved($__ctx, $name)
	{
		$__memorize_value = \Runtime\rtl::_memorizeValue("Bayrell.Lang.LangBay.ParserBayBase.isReserved", func_get_args());
		if ($__memorize_value != \Runtime\rtl::$_memorize_not_found) return $__memorize_value;
		if ($name == "__async_t")
		{$__memorize_value = true;
			\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isReserved", func_get_args(), $__memorize_value);
			return $__memorize_value;
		}
		if ($name == "__async_var")
		{$__memorize_value = true;
			\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isReserved", func_get_args(), $__memorize_value);
			return $__memorize_value;
		}
		if ($name == "__ctx")
		{$__memorize_value = true;
			\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isReserved", func_get_args(), $__memorize_value);
			return $__memorize_value;
		}
		if (\Runtime\rs::substr($__ctx, $name, 0, 3) == "__v")
		{$__memorize_value = true;
			\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isReserved", func_get_args(), $__memorize_value);
			return $__memorize_value;
		}$__memorize_value = false;
		\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isReserved", func_get_args(), $__memorize_value);
		return $__memorize_value;
	}
	/**
	 * Returns kind of identifier or thrown Error
	 */
	static function findIdentifier($__ctx, $parser, $name, $caret)
	{
		$kind = "";
		if ($parser->vars->has($__ctx, $name))
		{
			$kind = \Bayrell\Lang\OpCodes\OpIdentifier::KIND_VARIABLE;
		}
		else if ($parser->uses->has($__ctx, $name))
		{
			$kind = \Bayrell\Lang\OpCodes\OpIdentifier::KIND_MODULE;
		}
		else if (static::isSystemType($__ctx, $name))
		{
			$kind = \Bayrell\Lang\OpCodes\OpIdentifier::KIND_SYS_TYPE;
		}
		else if ($name == "null" || $name == "true" || $name == "false")
		{
			$kind = \Bayrell\Lang\OpCodes\OpIdentifier::KIND_CONSTANT;
		}
		else if ($name == "fn")
		{
			$kind = \Bayrell\Lang\OpCodes\OpIdentifier::KIND_FUNCTION;
		}
		else if ($name == "@" || $name == "_")
		{
			$kind = \Bayrell\Lang\OpCodes\OpIdentifier::KIND_CONTEXT;
		}
		else if ($name == "static" || $name == "self" || $name == "this" || $name == "parent")
		{
			$kind = \Bayrell\Lang\OpCodes\OpIdentifier::KIND_CLASSREF;
		}
		else if ($parser->find_ident)
		{
			throw new \Bayrell\Lang\Exceptions\ParserError($__ctx, "Unknown identifier '" . \Runtime\rtl::toStr($name) . \Runtime\rtl::toStr("'"), $caret, $parser->file_name);
		}
		return $kind;
	}
	/**
	 * Return true if char is token char
	 * @param {char} ch
	 * @return {boolean}
	 */
	static function isTokenChar($__ctx, $ch)
	{
		$__memorize_value = \Runtime\rtl::_memorizeValue("Bayrell.Lang.LangBay.ParserBayBase.isTokenChar", func_get_args());
		if ($__memorize_value != \Runtime\rtl::$_memorize_not_found) return $__memorize_value;$__memorize_value = \Runtime\rs::strpos($__ctx, "qazwsxedcrfvtgbyhnujmikolp0123456789_", \Runtime\rs::strtolower($__ctx, $ch)) !== -1;
		\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isTokenChar", func_get_args(), $__memorize_value);
		return $__memorize_value;
	}
	/**
	 * Return true if char is system or space. ASCII code <= 32.
	 * @param char ch
	 * @return boolean
	 */
	static function isSkipChar($__ctx, $ch)
	{
		$__memorize_value = \Runtime\rtl::_memorizeValue("Bayrell.Lang.LangBay.ParserBayBase.isSkipChar", func_get_args());
		if ($__memorize_value != \Runtime\rtl::$_memorize_not_found) return $__memorize_value;
		if (\Runtime\rs::ord($__ctx, $ch) <= 32)
		{$__memorize_value = true;
			\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isSkipChar", func_get_args(), $__memorize_value);
			return $__memorize_value;
		}$__memorize_value = false;
		\Runtime\rtl::_memorizeSave("Bayrell.Lang.LangBay.ParserBayBase.isSkipChar", func_get_args(), $__memorize_value);
		return $__memorize_value;
	}
	/**
	 * Returns next X
	 */
	static function nextX($__ctx, $parser, $ch, $pos)
	{
		if ($ch == "\t")
		{
			return $pos + $parser->tab_size;
		}
		if ($ch == "\n")
		{
			return 0;
		}
		return $pos + 1;
	}
	/**
	 * Returns next Y
	 */
	static function nextY($__ctx, $parser, $ch, $pos)
	{
		if ($ch == "\n")
		{
			return $pos + 1;
		}
		return $pos;
	}
	/**
	 * Returns next X
	 */
	static function next($__ctx, $parser, $s, $x, $y, $pos)
	{
		$sz = \Runtime\rs::strlen($__ctx, $s);
		for ($i = 0;$i < $sz;$i++)
		{
			$ch = \Runtime\rs::substr($__ctx, $s, $i, 1);
			$x = static::nextX($__ctx, $parser, $ch, $x);
			$y = static::nextY($__ctx, $parser, $ch, $y);
			$pos = $pos + 1;
		}
		return \Runtime\Collection::from([$x,$y,$pos]);
	}
	/**
	 * Open comment
	 */
	static function isCommentOpen($__ctx, $str, $skip_comments)
	{
		return $skip_comments && $str == "/*";
	}
	/**
	 * Close comment
	 */
	static function isCommentClose($__ctx, $str)
	{
		return $str == "*/";
	}
	/**
	 * Skip char
	 */
	static function skipChar($__ctx, $parser, $content, $start_pos)
	{
		$x = $start_pos->x;
		$y = $start_pos->y;
		$pos = $start_pos->pos;
		$skip_comments = $parser->skip_comments;
		/* Check boundaries */
		if ($pos >= $parser->content_sz)
		{
			throw new \Bayrell\Lang\Exceptions\ParserEOF($__ctx);
		}
		$ch = \Runtime\rs::charAt($__ctx, $content->ref, $pos);
		$ch2 = \Runtime\rs::substr($__ctx, $content->ref, $pos, 2);
		while ((static::isSkipChar($__ctx, $ch) || static::isCommentOpen($__ctx, $ch2, $skip_comments)) && $pos < $parser->content_sz)
		{
			if (static::isCommentOpen($__ctx, $ch2, $skip_comments))
			{
				$ch2 = \Runtime\rs::substr($__ctx, $content->ref, $pos, 2);
				while (!static::isCommentClose($__ctx, $ch2) && $pos < $parser->content_sz)
				{
					$x = static::nextX($__ctx, $parser, $ch, $x);
					$y = static::nextY($__ctx, $parser, $ch, $y);
					$pos = $pos + 1;
					if ($pos >= $parser->content_sz)
					{
						break;
					}
					$ch = \Runtime\rs::charAt($__ctx, $content->ref, $pos);
					$ch2 = \Runtime\rs::substr($__ctx, $content->ref, $pos, 2);
				}
				if (static::isCommentClose($__ctx, $ch2))
				{
					$x = $x + 2;
					$pos = $pos + 2;
				}
			}
			else
			{
				$x = static::nextX($__ctx, $parser, $ch, $x);
				$y = static::nextY($__ctx, $parser, $ch, $y);
				$pos = $pos + 1;
			}
			if ($pos >= $parser->content_sz)
			{
				break;
			}
			$ch = \Runtime\rs::charAt($__ctx, $content->ref, $pos);
			$ch2 = \Runtime\rs::substr($__ctx, $content->ref, $pos, 2);
		}
		return new \Bayrell\Lang\Caret($__ctx, \Runtime\Dict::from(["pos"=>$pos,"x"=>$x,"y"=>$y]));
	}
	/**
	 * Read special token
	 */
	static function readSpecialToken($__ctx, $parser, $content, $start_pos)
	{
		$pos = $start_pos->pos;
		$s = "";
		$s = \Runtime\rs::substr($__ctx, $content->ref, $pos, 10);
		if ($s == "#endswitch")
		{
			return $s;
		}
		$s = \Runtime\rs::substr($__ctx, $content->ref, $pos, 7);
		if ($s == "#ifcode" || $s == "#switch")
		{
			return $s;
		}
		$s = \Runtime\rs::substr($__ctx, $content->ref, $pos, 6);
		if ($s == "#endif")
		{
			return $s;
		}
		$s = \Runtime\rs::substr($__ctx, $content->ref, $pos, 5);
		if ($s == "#case")
		{
			return $s;
		}
		$s = \Runtime\rs::substr($__ctx, $content->ref, $pos, 4);
		if ($s == "@css")
		{
			return $s;
		}
		$s = \Runtime\rs::substr($__ctx, $content->ref, $pos, 3);
		if ($s == "!==" || $s == "===" || $s == "#if")
		{
			return $s;
		}
		$s = \Runtime\rs::substr($__ctx, $content->ref, $pos, 2);
		if ($s == "==" || $s == "!=" || $s == "<=" || $s == ">=" || $s == "=>" || $s == "|>" || $s == "->" || $s == "::" || $s == "+=" || $s == "-=" || $s == "~=" || $s == "**" || $s == "<<" || $s == ">>" || $s == "++" || $s == "--")
		{
			return $s;
		}
		return "";
	}
	/**
	 * Read next token and return caret end
	 */
	static function nextToken($__ctx, $parser, $content, $start_pos)
	{
		$is_first = true;
		$x = $start_pos->x;
		$y = $start_pos->y;
		$pos = $start_pos->pos;
		/* Check boundaries */
		if ($pos >= $parser->content_sz)
		{
			throw new \Bayrell\Lang\Exceptions\ParserEOF($__ctx);
		}
		$s = static::readSpecialToken($__ctx, $parser, $content, $start_pos);
		if ($s != "")
		{
			$sz = \Runtime\rs::strlen($__ctx, $s);
			for ($i = 0;$i < $sz;$i++)
			{
				$ch = \Runtime\rs::charAt($__ctx, $s, $i);
				$x = static::nextX($__ctx, $parser, $ch, $x);
				$y = static::nextY($__ctx, $parser, $ch, $y);
				$pos = $pos + 1;
			}
			return new \Bayrell\Lang\Caret($__ctx, \Runtime\Dict::from(["pos"=>$pos,"x"=>$x,"y"=>$y]));
		}
		$ch = \Runtime\rs::charAt($__ctx, $content->ref, $pos);
		if (!static::isTokenChar($__ctx, $ch))
		{
			$x = static::nextX($__ctx, $parser, $ch, $x);
			$y = static::nextY($__ctx, $parser, $ch, $y);
			$pos = $pos + 1;
		}
		else
		{
			while (static::isTokenChar($__ctx, $ch))
			{
				$x = static::nextX($__ctx, $parser, $ch, $x);
				$y = static::nextY($__ctx, $parser, $ch, $y);
				$pos = $pos + 1;
				if ($pos >= $parser->content_sz)
				{
					break;
				}
				$ch = \Runtime\rs::charAt($__ctx, $content->ref, $pos);
			}
		}
		return new \Bayrell\Lang\Caret($__ctx, \Runtime\Dict::from(["pos"=>$pos,"x"=>$x,"y"=>$y]));
	}
	/**
	 * Read next token
	 */
	static function readToken($__ctx, $parser)
	{
		$caret_start = null;
		$caret_end = null;
		$eof = false;
		try
		{
			
			$caret_start = static::skipChar($__ctx, $parser, $parser->content, $parser->caret->clone($__ctx));
			$caret_end = static::nextToken($__ctx, $parser, $parser->content, $caret_start);
		}
		catch (\Exception $_ex)
		{
			if ($_ex instanceof \Bayrell\Lang\Exceptions\ParserEOF)
			{
				$e = $_ex;
				if ($caret_start == null)
				{
					$caret_start = $parser->caret->clone($__ctx);
				}
				if ($caret_end == null)
				{
					$caret_end = $caret_start;
				}
				$eof = true;
			}
			else 
			{
				$e = $_ex;
				throw $e;
			}
		}
		return \Runtime\Collection::from([$parser->copy($__ctx, \Runtime\Dict::from(["caret"=>$caret_end])),new \Bayrell\Lang\CoreToken($__ctx, \Runtime\Dict::from(["content"=>\Runtime\rs::substr($__ctx, $parser->content->ref, $caret_start->pos, $caret_end->pos - $caret_start->pos),"caret_start"=>$caret_start,"caret_end"=>$caret_end,"eof"=>$eof]))]);
	}
	/**
	 * Match next token
	 */
	static function matchToken($__ctx, $parser, $next_token)
	{
		$token = null;
		/* Read token */
		$res = static::readToken($__ctx, $parser);
		$parser = $res[0];
		$token = $res[1];
		if ($next_token != $token->content)
		{
			throw new \Bayrell\Lang\Exceptions\ParserExpected($__ctx, $next_token, $token->caret_start->clone($__ctx), $parser->file_name);
		}
		return \Runtime\Collection::from([$parser,$token]);
	}
	/**
	 * Match next string
	 */
	static function matchString($__ctx, $parser, $str1)
	{
		$caret = $parser->caret->clone($__ctx);
		$sz = \Runtime\rs::strlen($__ctx, $str1);
		$str2 = \Runtime\rs::substr($__ctx, $parser->content->ref, $caret->pos, $sz);
		if ($str1 != $str2)
		{
			throw new \Bayrell\Lang\Exceptions\ParserExpected($__ctx, $str1, $caret, $parser->file_name);
		}
		$res = static::next($__ctx, $parser, $str1, $caret->x, $caret->y, $caret->pos);
		$caret = new \Bayrell\Lang\Caret($__ctx, \Runtime\Dict::from(["x"=>$res[0],"y"=>$res[1],"pos"=>$res[2]]));
		$parser = $parser->copy($__ctx, ["caret"=>$caret]);
		return \Runtime\Collection::from([$parser,null]);
	}
	/**
	 * Read number
	 */
	static function readNumber($__ctx, $parser)
	{
		$token = null;
		$start = $parser->clone($__ctx);
		/* Read token */
		$res = static::readToken($__ctx, $parser);
		$parser = $res[0];
		$token = $res[1];
		$caret_start = $token->caret_start->clone($__ctx);
		if ($token->content == "")
		{
			throw new \Bayrell\Lang\Exceptions\ParserExpected($__ctx, "Number", $caret_start, $parser->file_name);
		}
		if (!static::isStringOfNumbers($__ctx, $token->content))
		{
			throw new \Bayrell\Lang\Exceptions\ParserExpected($__ctx, "Number", $caret_start, $parser->file_name);
		}
		return \Runtime\Collection::from([$parser,new \Bayrell\Lang\OpCodes\OpNumber($__ctx, \Runtime\Dict::from(["value"=>$token->content,"caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($__ctx)]))]);
	}
	/**
	 * Read string
	 */
	static function readUntilStringArr($__ctx, $parser, $arr, $flag_include=true)
	{
		$token = null;
		$look = null;
		$content = $parser->content;
		$content_sz = $parser->content_sz;
		$pos = $parser->caret->pos;
		$x = $parser->caret->x;
		$y = $parser->caret->y;
		/* Search next string in arr */
		$search = function ($__ctx, $pos) use (&$content,&$arr)
		{
			for ($i = 0;$i < $arr->count($__ctx);$i++)
			{
				$item = $arr->item($__ctx, $i);
				$sz = \Runtime\rs::strlen($__ctx, $item);
				$str = \Runtime\rs::substr($__ctx, $content->ref, $pos, $sz);
				if ($str == $item)
				{
					return $i;
				}
			}
			return -1;
		};
		/* Start and end positionss */
		$start_pos = $pos;
		$end_pos = $pos;
		/* Read string value */
		$ch = "";
		$arr_pos = $search($__ctx, $pos);
		while ($pos < $content_sz && $arr_pos == -1)
		{
			$ch = \Runtime\rs::charAt($__ctx, $content->ref, $pos);
			$x = static::nextX($__ctx, $parser, $ch, $x);
			$y = static::nextY($__ctx, $parser, $ch, $y);
			$pos = $pos + 1;
			if ($pos >= $content_sz)
			{
				throw new \Bayrell\Lang\Exceptions\ParserExpected($__ctx, \Runtime\rs::join($__ctx, ",", $arr), new \Bayrell\Lang\Caret($__ctx, \Runtime\Dict::from(["x"=>$x,"y"=>$y,"pos"=>$pos])), $parser->file_name);
			}
			$arr_pos = $search($__ctx, $pos);
		}
		if ($arr_pos == -1)
		{
			throw new \Bayrell\Lang\Exceptions\ParserExpected($__ctx, "End of string", new \Bayrell\Lang\Caret($__ctx, \Runtime\Dict::from(["x"=>$x,"y"=>$y,"pos"=>$pos])), $parser->file_name);
		}
		if (!$flag_include)
		{
			$end_pos = $pos;
		}
		else
		{
			$item = $arr->item($__ctx, $arr_pos);
			$sz = \Runtime\rs::strlen($__ctx, $item);
			for ($i = 0;$i < $sz;$i++)
			{
				$ch = \Runtime\rs::charAt($__ctx, $content->ref, $pos);
				$x = static::nextX($__ctx, $parser, $ch, $x);
				$y = static::nextY($__ctx, $parser, $ch, $y);
				$pos = $pos + 1;
			}
			$end_pos = $pos;
		}
		/* Return result */
		$caret_end = new \Bayrell\Lang\Caret($__ctx, \Runtime\Dict::from(["x"=>$x,"y"=>$y,"pos"=>$end_pos]));
		return \Runtime\Collection::from([$parser->copy($__ctx, \Runtime\Dict::from(["caret"=>$caret_end])),\Runtime\rs::substr($__ctx, $content->ref, $start_pos, $end_pos - $start_pos)]);
	}
	/**
	 * Read string
	 */
	static function readString($__ctx, $parser)
	{
		$token = null;
		$look = null;
		/* Read token */
		$res = static::readToken($__ctx, $parser->clone($__ctx));
		$look = $res[0];
		$token = $res[1];
		$caret_start = $token->caret_start->clone($__ctx);
		$str_char = $token->content;
		/* Read begin string char */
		if ($str_char != "'" && $str_char != "\"")
		{
			throw new \Bayrell\Lang\Exceptions\ParserExpected($__ctx, "String", $caret_start, $parser->file_name);
		}
		$content = $look->content;
		$content_sz = $look->content_sz;
		$pos = $look->caret->pos;
		$x = $look->caret->x;
		$y = $look->caret->y;
		/* Read string value */
		$value_str = "";
		$ch = \Runtime\rs::charAt($__ctx, $content->ref, $pos);
		while ($pos < $content_sz && $ch != $str_char)
		{
			if ($ch == "\\")
			{
				$x = static::nextX($__ctx, $parser, $ch, $x);
				$y = static::nextY($__ctx, $parser, $ch, $y);
				$pos = $pos + 1;
				if ($pos >= $content_sz)
				{
					throw new \Bayrell\Lang\Exceptions\ParserExpected($__ctx, "End of string", new \Bayrell\Lang\Caret($__ctx, \Runtime\Dict::from(["x"=>$x,"y"=>$y,"pos"=>$pos])), $parser->file_name);
				}
				$ch2 = \Runtime\rs::charAt($__ctx, $content->ref, $pos);
				if ($ch2 == "n")
				{
					$value_str .= \Runtime\rtl::toStr("\n");
				}
				else if ($ch2 == "r")
				{
					$value_str .= \Runtime\rtl::toStr("\r");
				}
				else if ($ch2 == "t")
				{
					$value_str .= \Runtime\rtl::toStr("\t");
				}
				else if ($ch2 == "\\")
				{
					$value_str .= \Runtime\rtl::toStr("\\");
				}
				else if ($ch2 == "'")
				{
					$value_str .= \Runtime\rtl::toStr("'");
				}
				else if ($ch2 == "\"")
				{
					$value_str .= \Runtime\rtl::toStr("\"");
				}
				$x = static::nextX($__ctx, $parser, $ch2, $x);
				$y = static::nextY($__ctx, $parser, $ch2, $y);
				$pos = $pos + 1;
			}
			else
			{
				$value_str .= \Runtime\rtl::toStr($ch);
				$x = static::nextX($__ctx, $parser, $ch, $x);
				$y = static::nextY($__ctx, $parser, $ch, $y);
				$pos = $pos + 1;
			}
			if ($pos >= $content_sz)
			{
				throw new \Bayrell\Lang\Exceptions\ParserExpected($__ctx, "End of string", new \Bayrell\Lang\Caret($__ctx, \Runtime\Dict::from(["x"=>$x,"y"=>$y,"pos"=>$pos])), $parser->file_name);
			}
			$ch = \Runtime\rs::charAt($__ctx, $content->ref, $pos);
		}
		/* Read end string char */
		if ($ch != "'" && $ch != "\"")
		{
			throw new \Bayrell\Lang\Exceptions\ParserExpected($__ctx, "End of string", new \Bayrell\Lang\Caret($__ctx, \Runtime\Dict::from(["x"=>$x,"y"=>$y,"pos"=>$pos])), $parser->file_name);
		}
		$x = static::nextX($__ctx, $parser, $ch, $x);
		$y = static::nextY($__ctx, $parser, $ch, $y);
		$pos = $pos + 1;
		/* Return result */
		$caret_end = new \Bayrell\Lang\Caret($__ctx, \Runtime\Dict::from(["x"=>$x,"y"=>$y,"pos"=>$pos]));
		return \Runtime\Collection::from([$parser->copy($__ctx, \Runtime\Dict::from(["caret"=>$caret_end])),new \Bayrell\Lang\OpCodes\OpString($__ctx, \Runtime\Dict::from(["value"=>$value_str,"caret_start"=>$caret_start,"caret_end"=>$caret_end]))]);
	}
	/**
	 * Read comment
	 */
	static function readComment($__ctx, $parser)
	{
		$start = $parser->clone($__ctx);
		$token = null;
		$look = null;
		$parser = $parser->copy($__ctx, ["skip_comments"=>false]);
		$res = \Bayrell\Lang\LangBay\ParserBayBase::readToken($__ctx, $parser->clone($__ctx));
		$look = $res[0];
		$token = $res[1];
		$caret_start = $token->caret_start->clone($__ctx);
		$parser = $parser->copy($__ctx, ["skip_comments"=>true]);
		if ($token->content == "/")
		{
			$parser = $look->clone($__ctx);
			$content = $look->content;
			$content_sz = $look->content_sz;
			$pos = $look->caret->pos;
			$x = $look->caret->x;
			$y = $look->caret->y;
			$pos_start = $pos;
			$ch = \Runtime\rs::charAt($__ctx, $content->ref, $pos);
			$ch2 = \Runtime\rs::substr($__ctx, $content->ref, $pos, 2);
			while (!static::isCommentClose($__ctx, $ch2) && $pos < $content_sz)
			{
				$x = static::nextX($__ctx, $parser, $ch, $x);
				$y = static::nextY($__ctx, $parser, $ch, $y);
				$pos = $pos + 1;
				if ($pos >= $parser->content_sz)
				{
					break;
				}
				$ch = \Runtime\rs::charAt($__ctx, $content->ref, $pos);
				$ch2 = \Runtime\rs::substr($__ctx, $content->ref, $pos, 2);
			}
			$pos_end = $pos;
			if (static::isCommentClose($__ctx, $ch2))
			{
				$x = $x + 2;
				$pos = $pos + 2;
			}
			else
			{
				throw new \Bayrell\Lang\Exceptions\ParserExpected($__ctx, "End of comment", new \Bayrell\Lang\Caret($__ctx, \Runtime\Dict::from(["x"=>$x,"y"=>$y,"pos"=>$pos])), $start->file_name);
			}
			/* Return result */
			$value_str = \Runtime\rs::substr($__ctx, $content->ref, $pos_start + 1, $pos_end - $pos_start - 1);
			$caret_end = new \Bayrell\Lang\Caret($__ctx, \Runtime\Dict::from(["x"=>$x,"y"=>$y,"pos"=>$pos]));
			return \Runtime\Collection::from([$start->copy($__ctx, \Runtime\Dict::from(["caret"=>$caret_end])),new \Bayrell\Lang\OpCodes\OpComment($__ctx, \Runtime\Dict::from(["value"=>$value_str,"caret_start"=>$caret_start,"caret_end"=>$caret_end]))]);
		}
		return \Runtime\Collection::from([$parser,null]);
	}
	/**
	 * Read identifier
	 */
	static function readIdentifier($__ctx, $parser, $find_ident=false)
	{
		$start = $parser->clone($__ctx);
		$token = null;
		$look = null;
		$name = "";
		$res = \Bayrell\Lang\LangBay\ParserBayBase::readToken($__ctx, $parser);
		$parser = $res[0];
		$token = $res[1];
		if ($token->content == "")
		{
			throw new \Bayrell\Lang\Exceptions\ParserExpected($__ctx, "Identifier", $token->caret_start->clone($__ctx), $parser->file_name);
		}
		if (!static::isIdentifier($__ctx, $token->content))
		{
			throw new \Bayrell\Lang\Exceptions\ParserExpected($__ctx, "Identifier", $token->caret_start->clone($__ctx), $parser->file_name);
		}
		if (static::isReserved($__ctx, $token->content))
		{
			throw new \Bayrell\Lang\Exceptions\ParserExpected($__ctx, "Identifier " . \Runtime\rtl::toStr($token->content) . \Runtime\rtl::toStr(" is reserverd"), $token->caret_start->clone($__ctx), $parser->file_name);
		}
		$name = $token->content;
		$kind = "";
		if ($find_ident)
		{
			$kind = static::findIdentifier($__ctx, $parser, $name, $token->caret_start->clone($__ctx));
		}
		return \Runtime\Collection::from([$parser,new \Bayrell\Lang\OpCodes\OpIdentifier($__ctx, \Runtime\Dict::from(["kind"=>$kind,"value"=>$name,"caret_start"=>$token->caret_start->clone($__ctx),"caret_end"=>$token->caret_end->clone($__ctx)]))]);
	}
	/**
	 * Read entity name
	 */
	static function readEntityName($__ctx, $parser, $find_ident=true)
	{
		$look = null;
		$token = null;
		$ident = null;
		$names = new \Runtime\Vector($__ctx);
		$res = $parser->parser_base->staticMethod("readIdentifier")($__ctx, $parser, $find_ident);
		$parser = $res[0];
		$ident = $res[1];
		$caret_start = $ident->caret_start->clone($__ctx);
		$name = $ident->value;
		$names->push($__ctx, $name);
		$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
		$look = $res[0];
		$token = $res[1];
		while (!$token->eof && $token->content == ".")
		{
			$res = $parser->parser_base->staticMethod("matchToken")($__ctx, $parser, ".");
			$parser = $res[0];
			$res = $parser->parser_base->staticMethod("readIdentifier")($__ctx, $parser);
			$parser = $res[0];
			$ident = $res[1];
			$name = $ident->value;
			$names->push($__ctx, $name);
			$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
			$look = $res[0];
			$token = $res[1];
		}
		return \Runtime\Collection::from([$parser,new \Bayrell\Lang\OpCodes\OpEntityName($__ctx, \Runtime\Dict::from(["caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($__ctx),"names"=>$names->toCollection($__ctx)]))]);
	}
	/**
	 * Read type identifier
	 */
	static function readTypeIdentifier($__ctx, $parser, $find_ident=true)
	{
		$start = $parser->clone($__ctx);
		$look = null;
		$token = null;
		$op_code = null;
		$entity_name = null;
		$template = null;
		$res = static::readEntityName($__ctx, $parser, $find_ident);
		$parser = $res[0];
		$entity_name = $res[1];
		$caret_start = $entity_name->caret_start->clone($__ctx);
		$res = static::readToken($__ctx, $parser->clone($__ctx));
		$look = $res[0];
		$token = $res[1];
		if ($token->content == "<")
		{
			$template = new \Runtime\Vector($__ctx);
			$res = static::matchToken($__ctx, $parser, "<");
			$parser = $res[0];
			$res = static::readToken($__ctx, $parser->clone($__ctx));
			$look = $res[0];
			$token = $res[1];
			while (!$token->eof && $token->content != ">")
			{
				$parser_value = null;
				$res = static::readTypeIdentifier($__ctx, $parser);
				$parser = $res[0];
				$parser_value = $res[1];
				$template->push($__ctx, $parser_value);
				$res = static::readToken($__ctx, $parser->clone($__ctx));
				$look = $res[0];
				$token = $res[1];
				if ($token->content != ">")
				{
					$res = static::matchToken($__ctx, $parser, ",");
					$parser = $res[0];
					$res = static::readToken($__ctx, $parser->clone($__ctx));
					$look = $res[0];
					$token = $res[1];
				}
			}
			$res = static::matchToken($__ctx, $parser, ">");
			$parser = $res[0];
		}
		return \Runtime\Collection::from([$parser,new \Bayrell\Lang\OpCodes\OpTypeIdentifier($__ctx, \Runtime\Dict::from(["entity_name"=>$entity_name,"template"=>($template) ? $template->toCollection($__ctx) : null,"caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($__ctx)]))]);
	}
	/**
	 * Read collection
	 */
	static function readCollection($__ctx, $parser)
	{
		$start = $parser->clone($__ctx);
		$look = null;
		$token = null;
		$values = new \Runtime\Vector($__ctx);
		$res = static::matchToken($__ctx, $parser, "[");
		$parser = $res[0];
		$token = $res[1];
		$caret_start = $token->caret_start->clone($__ctx);
		$res = static::readToken($__ctx, $parser->clone($__ctx));
		$look = $res[0];
		$token = $res[1];
		while (!$token->eof && $token->content != "]")
		{
			$parser_value = null;
			$res = $parser->parser_expression->staticMethod("readExpression")($__ctx, $parser);
			$parser = $res[0];
			$parser_value = $res[1];
			$values->push($__ctx, $parser_value);
			$res = static::readToken($__ctx, $parser->clone($__ctx));
			$look = $res[0];
			$token = $res[1];
			if ($token->content == ",")
			{
				$parser = $look->clone($__ctx);
				$res = static::readToken($__ctx, $parser->clone($__ctx));
				$look = $res[0];
				$token = $res[1];
			}
		}
		$res = static::matchToken($__ctx, $parser, "]");
		$parser = $res[0];
		$token = $res[1];
		return \Runtime\Collection::from([$parser,new \Bayrell\Lang\OpCodes\OpCollection($__ctx, \Runtime\Dict::from(["values"=>$values->toCollection($__ctx),"caret_start"=>$caret_start,"caret_end"=>$token->caret_end->clone($__ctx)]))]);
	}
	/**
	 * Read collection
	 */
	static function readDict($__ctx, $parser)
	{
		$look = null;
		$token = null;
		$values = new \Runtime\Map($__ctx);
		$res = static::matchToken($__ctx, $parser, "{");
		$parser = $res[0];
		$token = $res[1];
		$caret_start = $token->caret_start->clone($__ctx);
		$res = static::readToken($__ctx, $parser->clone($__ctx));
		$look = $res[0];
		$token = $res[1];
		while (!$token->eof && $token->content != "}")
		{
			$parser_value = null;
			$res = static::readString($__ctx, $parser);
			$parser = $res[0];
			$parser_value = $res[1];
			$key = $parser_value->value;
			$res = static::matchToken($__ctx, $parser, ":");
			$parser = $res[0];
			$res = $parser->parser_expression->staticMethod("readExpression")($__ctx, $parser);
			$parser = $res[0];
			$parser_value = $res[1];
			$values->set($__ctx, $key, $parser_value);
			$res = static::readToken($__ctx, $parser->clone($__ctx));
			$look = $res[0];
			$token = $res[1];
			if ($token->content == ",")
			{
				$parser = $look->clone($__ctx);
				$res = static::readToken($__ctx, $parser->clone($__ctx));
				$look = $res[0];
				$token = $res[1];
			}
		}
		$res = static::matchToken($__ctx, $parser, "}");
		$parser = $res[0];
		$token = $res[1];
		return \Runtime\Collection::from([$parser,new \Bayrell\Lang\OpCodes\OpDict($__ctx, \Runtime\Dict::from(["values"=>$values->toDict($__ctx),"caret_start"=>$caret_start,"caret_end"=>$token->caret_end->clone($__ctx)]))]);
	}
	/**
	 * Read fixed
	 */
	static function readFixed($__ctx, $parser, $find_ident=false)
	{
		$look = null;
		$token = null;
		$start = $parser->clone($__ctx);
		$flag_negative = false;
		$res = static::readToken($__ctx, $parser->clone($__ctx));
		$look = $res[0];
		$token = $res[1];
		if ($token->content == "")
		{
			throw new \Bayrell\Lang\Exceptions\ParserExpected($__ctx, "Identifier", $token->caret_start->clone($__ctx), $look->file_name);
		}
		/* Negative number */
		if ($token->content == "-")
		{
			$flag_negative = true;
			$res = static::readToken($__ctx, $look);
			$look = $res[0];
			$token = $res[1];
		}
		/* Read string */
		if (!$flag_negative && ($token->content == "'" || $token->content == "\""))
		{
			return static::readString($__ctx, $parser);
		}
		/* Read Collection */
		if (!$flag_negative && $token->content == "[")
		{
			return static::readCollection($__ctx, $parser);
		}
		/* Read Dict */
		if (!$flag_negative && $token->content == "{")
		{
			return static::readDict($__ctx, $parser);
		}
		/* Read Number */
		if (static::isStringOfNumbers($__ctx, $token->content))
		{
			return \Runtime\Collection::from([$look,new \Bayrell\Lang\OpCodes\OpNumber($__ctx, \Runtime\Dict::from(["value"=>$token->content,"caret_start"=>$token->caret_start->clone($__ctx),"caret_end"=>$look->caret->clone($__ctx),"negative"=>$flag_negative]))]);
		}
		/* Read Identifier */
		if (!static::isIdentifier($__ctx, $token->content))
		{
			throw new \Bayrell\Lang\Exceptions\ParserExpected($__ctx, "Identifier", $token->caret_start->clone($__ctx), $look->file_name);
		}
		$kind = "";
		if ($find_ident)
		{
			$kind = static::findIdentifier($__ctx, $parser, $token->content, $token->caret_start->clone($__ctx));
		}
		return \Runtime\Collection::from([$look,new \Bayrell\Lang\OpCodes\OpIdentifier($__ctx, \Runtime\Dict::from(["kind"=>$kind,"value"=>$token->content,"caret_start"=>$token->caret_start->clone($__ctx),"caret_end"=>$look->caret->clone($__ctx)]))]);
	}
	/**
	 * Read call args
	 */
	static function readCallArgs($__ctx, $parser)
	{
		$look = null;
		$token = null;
		$items = new \Runtime\Vector($__ctx);
		$res = static::readToken($__ctx, $parser->clone($__ctx));
		$look = $res[0];
		$token = $res[1];
		if ($token->content == "{")
		{
			$res = static::readDict($__ctx, $parser);
			$parser = $res[0];
			$d = $res[1];
			$items = \Runtime\Collection::from([$d]);
		}
		else if ($token->content == "(")
		{
			$res = static::matchToken($__ctx, $parser, "(");
			$parser = $res[0];
			$res = static::readToken($__ctx, $parser->clone($__ctx));
			$look = $res[0];
			$token = $res[1];
			while (!$token->eof && $token->content != ")")
			{
				$parser_value = null;
				$res = $parser->parser_expression->staticMethod("readExpression")($__ctx, $parser);
				$parser = $res[0];
				$parser_value = $res[1]->clone($__ctx);
				$items->push($__ctx, $parser_value);
				$res = static::readToken($__ctx, $parser->clone($__ctx));
				$look = $res[0];
				$token = $res[1];
				if ($token->content == ",")
				{
					$parser = $look->clone($__ctx);
					$res = static::readToken($__ctx, $parser->clone($__ctx));
					$look = $res[0];
					$token = $res[1];
				}
			}
			$res = static::matchToken($__ctx, $parser, ")");
			$parser = $res[0];
		}
		return \Runtime\Collection::from([$parser,$items->toCollection($__ctx)]);
	}
	/**
	 * Read new instance
	 */
	static function readNew($__ctx, $parser)
	{
		$look = null;
		$token = null;
		$op_code = null;
		$args = \Runtime\Collection::from([]);
		$res = static::matchToken($__ctx, $parser, "new");
		$parser = $res[0];
		$token = $res[1];
		$caret_start = $token->caret_start->clone($__ctx);
		$res = static::readTypeIdentifier($__ctx, $parser);
		$parser = $res[0];
		$op_code = $res[1];
		$res = static::readToken($__ctx, $parser->clone($__ctx));
		$token = $res[1];
		if ($token->content == "(" || $token->content == "{")
		{
			$res = static::readCallArgs($__ctx, $parser);
			$parser = $res[0];
			$args = $res[1];
		}
		else
		{
			static::matchToken($__ctx, $parser->clone($__ctx), "(");
		}
		return \Runtime\Collection::from([$parser,new \Bayrell\Lang\OpCodes\OpNew($__ctx, \Runtime\Dict::from(["args"=>$args,"value"=>$op_code,"caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($__ctx)]))]);
	}
	/**
	 * Read method
	 */
	static function readMethod($__ctx, $parser)
	{
		$look = null;
		$token = null;
		$parser_value = null;
		$value1 = null;
		$value2 = null;
		$res = static::matchToken($__ctx, $parser, "method");
		$parser = $res[0];
		$token = $res[1];
		$caret_start = $token->caret_start->clone($__ctx);
		$res = static::readTypeIdentifier($__ctx, $parser);
		$parser = $res[0];
		$value2 = $res[1];
		$res = static::readToken($__ctx, $parser->clone($__ctx));
		$look = $res[0];
		$token = $res[1];
		$look_token = $token->content;
		if ($look_token != "." && $look_token != "::")
		{
			throw new \Bayrell\Lang\Exceptions\ParserExpected($__ctx, "'.' or '::'", $token->caret_start->clone($__ctx), $look->file_name);
		}
		$res = static::readIdentifier($__ctx, $look->clone($__ctx));
		$parser = $res[0];
		$value2 = $res[1];
		return \Runtime\Collection::from([$parser,new \Bayrell\Lang\OpCodes\OpMethod($__ctx, \Runtime\Dict::from(["value1"=>$value1,"value2"=>$value2,"caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($__ctx)]))]);
	}
	/**
	 * Read dynamic
	 */
	static function readDynamic($__ctx, $parser, $op_code=null)
	{
		$look = null;
		$token = null;
		$parser_items = null;
		$op_code_first = null;
		$is_await = false;
		$is_context_call = true;
		$caret_start = null;
		$f_next = function ($__ctx, $s)
		{
			return $s == "." || $s == "::" || $s == "->" || $s == "|>" || $s == "{" || $s == "[" || $s == "(" || $s == "@";
		};
		if ($op_code == null)
		{
			$res = static::readToken($__ctx, $parser->clone($__ctx));
			$look = $res[0];
			$token = $res[1];
			if ($token->content == "await")
			{
				$caret_start = $token->caret_start->clone($__ctx);
				$is_await = true;
				$parser = $look->clone($__ctx);
			}
			$res = static::readToken($__ctx, $parser->clone($__ctx));
			$look = $res[0];
			$token = $res[1];
			if ($token->content == "@")
			{
				$res = static::readToken($__ctx, $look->clone($__ctx));
				$look2 = $res[0];
				$token2 = $res[1];
				if (!$f_next($__ctx, $token2->content))
				{
					if (static::isIdentifier($__ctx, $token2->content))
					{
						$parser = $look->clone($__ctx);
						$is_context_call = false;
					}
				}
			}
			$res = static::readFixed($__ctx, $parser, true);
			$parser = $res[0];
			$op_code = $res[1];
		}
		$op_code_first = $op_code;
		if ($caret_start == null)
		{
			$caret_start = $op_code->caret_start->clone($__ctx);
		}
		if ($op_code->kind == \Bayrell\Lang\OpCodes\OpIdentifier::KIND_CONTEXT)
		{
			$is_context_call = false;
		}
		$res = static::readToken($__ctx, $parser->clone($__ctx));
		$look = $res[0];
		$token = $res[1];
		if ($f_next($__ctx, $token->content))
		{
			if ($op_code instanceof \Bayrell\Lang\OpCodes\OpIdentifier)
			{
				if ($op_code->kind != \Bayrell\Lang\OpCodes\OpIdentifier::KIND_SYS_TYPE && $op_code->kind != \Bayrell\Lang\OpCodes\OpIdentifier::KIND_VARIABLE && $op_code->kind != \Bayrell\Lang\OpCodes\OpIdentifier::KIND_MODULE && $op_code->kind != \Bayrell\Lang\OpCodes\OpIdentifier::KIND_CLASSREF && $op_code->kind != \Bayrell\Lang\OpCodes\OpIdentifier::KIND_CONTEXT)
				{
					throw new \Bayrell\Lang\Exceptions\ParserExpected($__ctx, "Module or variable '" . \Runtime\rtl::toStr($op_code->value) . \Runtime\rtl::toStr("'"), $op_code->caret_start->clone($__ctx), $parser->file_name);
				}
			}
			else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpNew)
			{
			}
			else
			{
				throw new \Bayrell\Lang\Exceptions\ParserExpected($__ctx, "Module or variable", $op_code->caret_start->clone($__ctx), $parser->file_name);
			}
		}
		while (!$token->eof && $f_next($__ctx, $token->content))
		{
			$token_content = $token->content;
			/* Static call */
			if ($token_content == "(" || $token_content == "{" || $token_content == "@")
			{
				if ($token_content == "@")
				{
					$parser = $look->clone($__ctx);
					$is_context_call = false;
				}
				$res = static::readCallArgs($__ctx, $parser);
				$parser = $res[0];
				$parser_items = $res[1];
				$op_code = new \Bayrell\Lang\OpCodes\OpCall($__ctx, \Runtime\Dict::from(["obj"=>$op_code,"args"=>$parser_items,"caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($__ctx),"is_await"=>$is_await,"is_context"=>$is_context_call]));
				$is_context_call = true;
			}
			else if ($token_content == "->" || $token_content == "|>")
			{
				$kind = "";
				$class_name = null;
				$method_name = null;
				$parser = $look->clone($__ctx);
				if ($token_content == "->")
				{
					$res = static::readToken($__ctx, $parser->clone($__ctx));
					$look = $res[0];
					$token = $res[1];
					if ($token->content == "@")
					{
						$parser = $look->clone($__ctx);
						$is_context_call = false;
					}
					$res = static::readIdentifier($__ctx, $parser);
					$parser = $res[0];
					$method_name = $res[1];
					$kind = \Bayrell\Lang\OpCodes\OpPipe::KIND_METHOD;
				}
				else if ($token_content == "|>")
				{
					$res = static::readTypeIdentifier($__ctx, $parser);
					$parser = $res[0];
					$class_name = $res[1];
					$res = static::matchToken($__ctx, $parser, "::");
					$parser = $res[0];
					$res = static::readToken($__ctx, $parser->clone($__ctx));
					$look = $res[0];
					$token = $res[1];
					if ($token->content == "@")
					{
						$parser = $look->clone($__ctx);
						$is_context_call = false;
					}
					$res = static::readIdentifier($__ctx, $parser);
					$parser = $res[0];
					$method_name = $res[1];
					$kind = \Bayrell\Lang\OpCodes\OpPipe::KIND_LAMBDA;
				}
				$res = static::readCallArgs($__ctx, $parser);
				$parser = $res[0];
				$args = $res[1];
				$op_code = new \Bayrell\Lang\OpCodes\OpPipe($__ctx, \Runtime\Dict::from(["kind"=>$kind,"obj"=>$op_code,"args"=>$args,"class_name"=>$class_name,"method_name"=>$method_name,"caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($__ctx),"is_await"=>$is_await,"is_context"=>$is_context_call]));
				$is_context_call = true;
			}
			else if ($token_content == "." || $token_content == "::" || $token_content == "[")
			{
				$kind = "";
				$look_value = null;
				$parser = $look->clone($__ctx);
				if ($token_content == ".")
				{
					$kind = \Bayrell\Lang\OpCodes\OpAttr::KIND_ATTR;
				}
				else if ($token_content == "::")
				{
					$kind = \Bayrell\Lang\OpCodes\OpAttr::KIND_STATIC;
				}
				else if ($token_content == "[")
				{
					$kind = \Bayrell\Lang\OpCodes\OpAttr::KIND_DYNAMIC;
				}
				if ($token_content == "[")
				{
					$res = $parser->parser_expression->staticMethod("readExpression")($__ctx, $parser);
					$parser = $res[0];
					$look_value = $res[1];
					$res = static::matchToken($__ctx, $parser, "]");
					$parser = $res[0];
				}
				else
				{
					$res = static::readToken($__ctx, $parser->clone($__ctx));
					$look = $res[0];
					$token = $res[1];
					if ($token->content == "@")
					{
						$parser = $look->clone($__ctx);
						$is_context_call = false;
					}
					$res = static::readIdentifier($__ctx, $parser);
					$parser = $res[0];
					$look_value = $res[1];
				}
				$op_code = new \Bayrell\Lang\OpCodes\OpAttr($__ctx, \Runtime\Dict::from(["kind"=>$kind,"obj"=>$op_code,"value"=>$look_value,"caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($__ctx)]));
			}
			else
			{
				throw new \Bayrell\Lang\Exceptions\ParserExpected($__ctx, "Next attr", $token->caret_start->clone($__ctx), $parser->file_name);
			}
			$res = static::readToken($__ctx, $parser->clone($__ctx));
			$look = $res[0];
			$token = $res[1];
			if ($op_code instanceof \Bayrell\Lang\OpCodes\OpAttr && $op_code->kind == \Bayrell\Lang\OpCodes\OpAttr::KIND_PIPE && $token->content != "(" && $token->content != "{")
			{
				throw new \Bayrell\Lang\Exceptions\ParserExpected($__ctx, "Call", $token->caret_start->clone($__ctx), $parser->file_name);
			}
		}
		return \Runtime\Collection::from([$parser,$op_code]);
	}
	/**
	 * Read base item
	 */
	static function readBaseItem($__ctx, $parser)
	{
		$look = null;
		$token = null;
		$op_code = null;
		$flag_dynamic = false;
		$res = static::readToken($__ctx, $parser->clone($__ctx));
		$look = $res[0];
		$token = $res[1];
		$caret_start = $look->caret->clone($__ctx);
		if ($token->content == "new")
		{
			$res = static::readNew($__ctx, $parser);
			$parser = $res[0];
			$op_code = $res[1];
			$flag_dynamic = true;
		}
		else if ($token->content == "method")
		{
			$res = static::readMethod($__ctx, $parser);
			$parser = $res[0];
			$op_code = $res[1];
		}
		else if ($token->content == "classof")
		{
			$res = static::readClassOf($__ctx, $parser);
			$parser = $res[0];
			$op_code = $res[1];
		}
		else if ($token->content == "classref")
		{
			$res = static::readClassRef($__ctx, $parser);
			$parser = $res[0];
			$op_code = $res[1];
		}
		else if ($token->content == "(")
		{
			$save_parser = $look->clone($__ctx);
			$parser = $look->clone($__ctx);
			/* Try to read OpTypeConvert */
			try
			{
				
				$res = static::readTypeIdentifier($__ctx, $parser);
				$parser = $res[0];
				$op_type = $res[1];
				$res = static::readToken($__ctx, $parser);
				$parser = $res[0];
				$token = $res[1];
				if ($token->content == ")")
				{
					$res = static::readBaseItem($__ctx, $parser);
					$parser = $res[0];
					$op_code = $res[1];
					return \Runtime\Collection::from([$parser,new \Bayrell\Lang\OpCodes\OpTypeConvert($__ctx, \Runtime\Dict::from(["pattern"=>$op_type,"value"=>$op_code,"caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($__ctx)]))]);
				}
			}
			catch (\Exception $_ex)
			{
				if ($_ex instanceof \Bayrell\Lang\Exceptions\ParserError)
				{
					$e = $_ex;
				}
			}
			/* Read Expression */
			$parser = $save_parser->clone($__ctx);
			$res = $parser->parser_expression->staticMethod("readExpression")($__ctx, $parser);
			$parser = $res[0];
			$op_code = $res[1];
			$res = static::matchToken($__ctx, $parser, ")");
			$parser = $res[0];
			$flag_dynamic = true;
		}
		else
		{
			$flag_dynamic = true;
		}
		if ($flag_dynamic)
		{
			$res = static::readDynamic($__ctx, $parser, $op_code);
			$parser = $res[0];
			$op_code = $res[1];
		}
		return \Runtime\Collection::from([$parser,$op_code]);
	}
	/**
	 * Read classof
	 */
	static function readClassOf($__ctx, $parser)
	{
		$look = null;
		$token = null;
		$op_code = null;
		$res = static::matchToken($__ctx, $parser, "classof");
		$parser = $res[0];
		$token = $res[1];
		$caret_start = $token->caret_start->clone($__ctx);
		$res = static::readEntityName($__ctx, $parser);
		$parser = $res[0];
		$op_code = $res[1];
		return \Runtime\Collection::from([$parser,new \Bayrell\Lang\OpCodes\OpClassOf($__ctx, \Runtime\Dict::from(["entity_name"=>$op_code,"caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($__ctx)]))]);
	}
	/**
	 * Read classref
	 */
	static function readClassRef($__ctx, $parser)
	{
		$look = null;
		$token = null;
		$op_code = null;
		$res = static::matchToken($__ctx, $parser, "classref");
		$parser = $res[0];
		$token = $res[1];
		$caret_start = $token->caret_start->clone($__ctx);
		$res = $parser->parser_expression->staticMethod("readExpression")($__ctx, $parser);
		$parser = $res[0];
		$op_code = $res[1];
		return \Runtime\Collection::from([$parser,new \Bayrell\Lang\OpCodes\OpClassRef($__ctx, \Runtime\Dict::from(["value"=>$op_code,"caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($__ctx)]))]);
	}
	/* ======================= Class Init Functions ======================= */
	function getClassName()
	{
		return "Bayrell.Lang.LangBay.ParserBayBase";
	}
	static function getCurrentNamespace()
	{
		return "Bayrell.Lang.LangBay";
	}
	static function getCurrentClassName()
	{
		return "Bayrell.Lang.LangBay.ParserBayBase";
	}
	static function getParentClassName()
	{
		return "";
	}
	static function getClassInfo($__ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($__ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.LangBay.ParserBayBase",
			"name"=>"Bayrell.Lang.LangBay.ParserBayBase",
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