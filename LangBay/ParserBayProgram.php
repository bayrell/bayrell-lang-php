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
class ParserBayProgram
{
	/**
	 * Read namespace
	 */
	static function readNamespace($__ctx, $parser)
	{
		$token = null;
		$name = null;
		$res = $parser->parser_base->staticMethod("matchToken")($__ctx, $parser, "namespace");
		$parser = $res[0];
		$token = $res[1];
		$caret_start = $token->caret_start->clone($__ctx);
		$res = $parser->parser_base->staticMethod("readEntityName")($__ctx, $parser, false);
		$parser = $res[0];
		$name = $res[1];
		$current_namespace_name = \Runtime\rs::join($__ctx, ".", $name->names);
		$current_namespace = new \Bayrell\Lang\OpCodes\OpNamespace($__ctx, \Runtime\Dict::from(["name"=>$current_namespace_name,"caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($__ctx)]));
		$parser = $parser->copy($__ctx, ["current_namespace"=>$current_namespace]);
		$parser = $parser->copy($__ctx, ["current_namespace_name"=>$current_namespace_name]);
		return \Runtime\Collection::from([$parser,$current_namespace]);
	}
	/**
	 * Read use
	 */
	static function readUse($__ctx, $parser)
	{
		$look = null;
		$token = null;
		$name = null;
		$alias = "";
		$res = $parser->parser_base->staticMethod("matchToken")($__ctx, $parser, "use");
		$parser = $res[0];
		$token = $res[1];
		$caret_start = $token->caret_start->clone($__ctx);
		$res = $parser->parser_base->staticMethod("readEntityName")($__ctx, $parser, false);
		$parser = $res[0];
		$name = $res[1];
		$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
		$look = $res[0];
		$token = $res[1];
		if ($token->content == "as")
		{
			$parser_value = null;
			$parser = $look->clone($__ctx);
			$res = $parser->parser_base->staticMethod("readIdentifier")($__ctx, $parser);
			$parser = $res[0];
			$parser_value = $res[1];
			$alias = $parser_value->value;
		}
		return \Runtime\Collection::from([$parser,new \Bayrell\Lang\OpCodes\OpUse($__ctx, \Runtime\Dict::from(["name"=>\Runtime\rs::join($__ctx, ".", $name->names),"alias"=>$alias,"caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($__ctx)]))]);
	}
	/**
	 * Read class body
	 */
	static function readClassBody($__ctx, $parser)
	{
		$look = null;
		$token = null;
		$items = new \Runtime\Vector($__ctx);
		$parser = $parser->copy($__ctx, ["skip_comments"=>false]);
		$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
		$look = $res[0];
		$token = $res[1];
		$parser = $parser->copy($__ctx, ["skip_comments"=>true]);
		while (!$token->eof && $token->content != "}")
		{
			$item = null;
			if ($token->content == "/")
			{
				$res = $parser->parser_base->staticMethod("readComment")($__ctx, $parser);
				$parser = $res[0];
				$item = $res[1];
				if ($item != null)
				{
					$items->push($__ctx, $item);
				}
			}
			else if ($token->content == "@")
			{
				$res = $parser->parser_operator->staticMethod("readAnnotation")($__ctx, $parser);
				$parser = $res[0];
				$item = $res[1];
				$items->push($__ctx, $item);
			}
			else if ($token->content == "#switch" || $token->content == "#ifcode")
			{
				$res = $parser->parser_preprocessor->staticMethod("readPreprocessor")($__ctx, $parser);
				$parser = $res[0];
				$item = $res[1];
				if ($item != null)
				{
					$items->push($__ctx, $item);
				}
			}
			else
			{
				$flags = null;
				$res = $parser->parser_operator->staticMethod("readFlags")($__ctx, $parser);
				$parser = $res[0];
				$flags = $res[1];
				if ($parser->parser_operator->staticMethod("tryReadFunction")($__ctx, $parser->clone($__ctx), true, $flags))
				{
					$res = $parser->parser_operator->staticMethod("readDeclareFunction")($__ctx, $parser, true);
					$parser = $res[0];
					$item = $res[1];
					if ($item->expression != null)
					{
						$res = $parser->parser_base->staticMethod("matchToken")($__ctx, $parser, ";");
						$parser = $res[0];
					}
				}
				else
				{
					$res = $parser->parser_operator->staticMethod("readAssign")($__ctx, $parser);
					$parser = $res[0];
					$item = $res[1];
					$res = $parser->parser_base->staticMethod("matchToken")($__ctx, $parser, ";");
					$parser = $res[0];
				}
				$item = $item->copy($__ctx, ["flags"=>$flags]);
				if ($item != null)
				{
					$items->push($__ctx, $item);
				}
			}
			$parser = $parser->copy($__ctx, ["skip_comments"=>false]);
			$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
			$look = $res[0];
			$token = $res[1];
			$parser = $parser->copy($__ctx, ["skip_comments"=>true]);
		}
		return \Runtime\Collection::from([$parser,$items->toCollection($__ctx)]);
	}
	/**
	 * Read class
	 */
	static function readClass($__ctx, $parser)
	{
		$look = null;
		$token = null;
		$op_code = null;
		$template = null;
		$is_declare = false;
		$is_static = false;
		$is_struct = false;
		$class_kind = "";
		$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
		$look = $res[0];
		$token = $res[1];
		$caret_start = $token->caret_start->clone($__ctx);
		if ($token->content == "static")
		{
			$parser = $look->clone($__ctx);
			$is_static = true;
		}
		$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
		$look = $res[0];
		$token = $res[1];
		if ($token->content == "declare")
		{
			$parser = $look->clone($__ctx);
			$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
			$look = $res[0];
			$token = $res[1];
			$is_declare = true;
		}
		if ($token->content == "class")
		{
			$res = $parser->parser_base->staticMethod("matchToken")($__ctx, $parser, "class");
			$parser = $res[0];
			$class_kind = \Bayrell\Lang\OpCodes\OpDeclareClass::KIND_CLASS;
		}
		else if ($token->content == "struct")
		{
			$res = $parser->parser_base->staticMethod("matchToken")($__ctx, $parser, "struct");
			$parser = $res[0];
			$class_kind = \Bayrell\Lang\OpCodes\OpDeclareClass::KIND_STRUCT;
		}
		else if ($token->content == "interface")
		{
			$res = $parser->parser_base->staticMethod("matchToken")($__ctx, $parser, "interface");
			$parser = $res[0];
			$class_kind = \Bayrell\Lang\OpCodes\OpDeclareClass::KIND_INTERFACE;
		}
		else
		{
			$res = $parser->parser_base->staticMethod("matchToken")($__ctx, $parser, "class");
		}
		$res = $parser->parser_base->staticMethod("readIdentifier")($__ctx, $parser);
		$parser = $res[0];
		$op_code = $res[1];
		$class_name = $op_code->value;
		/* Set class name */
		$parser = $parser->copy($__ctx, ["current_class_name"=>$class_name]);
		$parser = $parser->copy($__ctx, ["current_class_kind"=>$class_kind]);
		/* Register module in parser */
		$parser = $parser->copy($__ctx, ["uses"=>$parser->uses->setIm($__ctx, $class_name, $parser->current_namespace_name . \Runtime\rtl::toStr(".") . \Runtime\rtl::toStr($class_name))]);
		$save_uses = $parser->uses;
		$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
		$look = $res[0];
		$token = $res[1];
		if ($token->content == "<")
		{
			$template = new \Runtime\Vector($__ctx);
			$res = $parser->parser_base->staticMethod("matchToken")($__ctx, $parser, "<");
			$parser = $res[0];
			$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
			$look = $res[0];
			$token = $res[1];
			while (!$token->eof && $token->content != ">")
			{
				$parser_value = null;
				$res = $parser->parser_base->staticMethod("readIdentifier")($__ctx, $parser);
				$parser = $res[0];
				$parser_value = $res[1];
				$template->push($__ctx, $parser_value);
				$parser = $parser->copy($__ctx, ["uses"=>$parser->uses->setIm($__ctx, $parser_value->value, $parser_value->value)]);
				$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
				$look = $res[0];
				$token = $res[1];
				if ($token->content != ">")
				{
					$res = $parser->parser_base->staticMethod("matchToken")($__ctx, $parser, ",");
					$parser = $res[0];
					$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
					$look = $res[0];
					$token = $res[1];
				}
			}
			$res = $parser->parser_base->staticMethod("matchToken")($__ctx, $parser, ">");
			$parser = $res[0];
		}
		$class_extends = null;
		$class_implements = null;
		$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
		$look = $res[0];
		$token = $res[1];
		if ($token->content == "extends")
		{
			$res = $parser->parser_base->staticMethod("readTypeIdentifier")($__ctx, $look->clone($__ctx));
			$parser = $res[0];
			$class_extends = $res[1];
		}
		$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
		$look = $res[0];
		$token = $res[1];
		if ($token->content == "implements")
		{
			$class_implements = new \Runtime\Vector($__ctx);
			$res = $parser->parser_base->staticMethod("readTypeIdentifier")($__ctx, $look->clone($__ctx));
			$parser = $res[0];
			$op_code = $res[1];
			$class_implements->push($__ctx, $op_code);
			$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
			$look = $res[0];
			$token = $res[1];
			while (!$token->eof && $token->content == ",")
			{
				$parser = $look->clone($__ctx);
				$res = $parser->parser_base->staticMethod("readTypeIdentifier")($__ctx, $look->clone($__ctx));
				$parser = $res[0];
				$op_code = $res[1];
				$class_implements->push($__ctx, $op_code);
				$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
				$look = $res[0];
				$token = $res[1];
			}
		}
		$arr = null;
		$res = $parser->parser_base->staticMethod("matchToken")($__ctx, $parser, "{");
		$parser = $res[0];
		$res = static::readClassBody($__ctx, $parser);
		$parser = $res[0];
		$arr = $res[1];
		$names = new \Runtime\Map($__ctx);
		$vars = new \Runtime\Vector($__ctx);
		$functions = new \Runtime\Vector($__ctx);
		$items = new \Runtime\Vector($__ctx);
		$annotations = new \Runtime\Vector($__ctx);
		$comments = new \Runtime\Vector($__ctx);
		$fn_create = null;
		$fn_destroy = null;
		for ($i = 0;$i < $arr->count($__ctx);$i++)
		{
			$item = $arr->item($__ctx, $i);
			if ($item instanceof \Bayrell\Lang\OpCodes\OpAnnotation)
			{
				$annotations->push($__ctx, $item);
			}
			else if ($item instanceof \Bayrell\Lang\OpCodes\OpComment)
			{
				$comments->push($__ctx, $item);
			}
			else if ($item instanceof \Bayrell\Lang\OpCodes\OpAssign)
			{
				for ($j = 0;$j < $item->values->count($__ctx);$j++)
				{
					$assign_value = $item->values->item($__ctx, $j);
					$value_name = $assign_value->var_name;
					if ($names->has($__ctx, $value_name))
					{
						throw new \Bayrell\Lang\Exceptions\ParserError($__ctx, "Dublicate identifier " . \Runtime\rtl::toStr($value_name), $assign_value->caret_start->clone($__ctx), $parser->file_name);
					}
					$names->set($__ctx, $value_name, true);
				}
				$item = $item->copy($__ctx, \Runtime\Dict::from(["annotations"=>$annotations->toCollection($__ctx),"comments"=>$comments->toCollection($__ctx)]));
				$vars->push($__ctx, $item);
				$annotations->clear($__ctx);
				$comments->clear($__ctx);
			}
			else if ($item instanceof \Bayrell\Lang\OpCodes\OpDeclareFunction)
			{
				$item = $item->copy($__ctx, \Runtime\Dict::from(["annotations"=>$annotations->toCollection($__ctx),"comments"=>$comments->toCollection($__ctx)]));
				if ($names->has($__ctx, $item->name))
				{
					throw new \Bayrell\Lang\Exceptions\ParserError($__ctx, "Dublicate identifier " . \Runtime\rtl::toStr($item->name), $item->caret_start->clone($__ctx), $parser->file_name);
				}
				$names->set($__ctx, $item->name, true);
				if ($item->name == "constructor")
				{
					$fn_create = $item;
				}
				else if ($item->name == "destructor")
				{
					$fn_destroy = $item;
				}
				else
				{
					$functions->push($__ctx, $item);
				}
				$annotations->clear($__ctx);
				$comments->clear($__ctx);
			}
			else
			{
				$items->push($__ctx, $item);
			}
		}
		$items->appendVector($__ctx, $comments);
		$res = $parser->parser_base->staticMethod("matchToken")($__ctx, $parser, "}");
		$parser = $res[0];
		$current_class = new \Bayrell\Lang\OpCodes\OpDeclareClass($__ctx, \Runtime\Dict::from(["kind"=>$class_kind,"name"=>$class_name,"is_static"=>$is_static,"is_declare"=>$is_declare,"class_extends"=>$class_extends,"class_implements"=>($class_implements != null) ? $class_implements->toCollection($__ctx) : null,"template"=>($template != null) ? $template->toCollection($__ctx) : null,"vars"=>$vars->toCollection($__ctx),"functions"=>$functions->toCollection($__ctx),"fn_create"=>$fn_create,"fn_destroy"=>$fn_destroy,"items"=>$items->toCollection($__ctx),"caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($__ctx)]));
		/* Restore uses */
		$parser = $parser->copy($__ctx, ["uses"=>$save_uses]);
		return \Runtime\Collection::from([$parser->copy($__ctx, \Runtime\Dict::from(["current_class"=>$current_class])),$current_class]);
	}
	/**
	 * Read program
	 */
	static function readProgram($__ctx, $parser)
	{
		$look = null;
		$token = null;
		$op_code = null;
		$annotations = new \Runtime\Vector($__ctx);
		$comments = new \Runtime\Vector($__ctx);
		$items = new \Runtime\Vector($__ctx);
		$parser = $parser->copy($__ctx, ["skip_comments"=>false]);
		$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
		$look = $res[0];
		$token = $res[1];
		$caret_start = $token->caret_start->clone($__ctx);
		$parser = $parser->copy($__ctx, ["skip_comments"=>true]);
		if ($token->eof)
		{
			return \Runtime\Collection::from([$parser,null]);
		}
		while (!$token->eof)
		{
			if ($token->content == "/")
			{
				$res = $parser->parser_base->staticMethod("readComment")($__ctx, $parser);
				$parser = $res[0];
				$op_code = $res[1];
				if ($op_code != null)
				{
					$comments->push($__ctx, $op_code);
				}
			}
			else if ($token->content == "@")
			{
				$res = $parser->parser_operator->staticMethod("readAnnotation")($__ctx, $parser);
				$parser = $res[0];
				$op_code = $res[1];
				$annotations->push($__ctx, $op_code);
			}
			else if ($token->content == "#switch" || $token->content == "#ifcode")
			{
				/* Append comments */
				$items->appendVector($__ctx, $comments);
				$comments->clear($__ctx);
				$res = $parser->parser_preprocessor->staticMethod("readPreprocessor")($__ctx, $parser);
				$parser = $res[0];
				$op_code = $res[1];
				if ($op_code != null)
				{
					$items->appendVector($__ctx, $comments);
					$items->push($__ctx, $op_code);
				}
			}
			else if ($token->content == "namespace")
			{
				/* Append comments */
				$items->appendVector($__ctx, $comments);
				$comments->clear($__ctx);
				$res = static::readNamespace($__ctx, $parser);
				$parser = $res[0];
				$op_code = $res[1];
				$items->push($__ctx, $op_code);
				$res = $parser->parser_base->staticMethod("matchToken")($__ctx, $parser, ";");
				$parser = $res[0];
			}
			else if ($token->content == "use")
			{
				/* Append comments */
				$items->appendVector($__ctx, $comments);
				$comments->clear($__ctx);
				$res = static::readUse($__ctx, $parser);
				$parser = $res[0];
				$op_code = $res[1];
				$full_name = $op_code->name;
				$short_name = "";
				if ($op_code->alias == "")
				{
					$short_name = \Runtime\rs::explode($__ctx, ".", $full_name)->last($__ctx);
				}
				else
				{
					$short_name = $op_code->alias;
				}
				/* Register module in parser */
				$parser = $parser->copy($__ctx, ["uses"=>$parser->uses->setIm($__ctx, $short_name, $full_name)]);
				$res = $parser->parser_base->staticMethod("matchToken")($__ctx, $parser, ";");
				$parser = $res[0];
			}
			else if ($token->content == "class" || $token->content == "struct" || $token->content == "static" || $token->content == "declare" || $token->content == "interface")
			{
				$item = null;
				$res = static::readClass($__ctx, $parser);
				$parser = $res[0];
				$item = $res[1];
				$item = $item->copy($__ctx, \Runtime\Dict::from(["annotations"=>$annotations->toCollection($__ctx),"comments"=>$comments->toCollection($__ctx)]));
				$items->push($__ctx, $item);
				$annotations->clear($__ctx);
				$comments->clear($__ctx);
			}
			else
			{
				break;
			}
			$parser = $parser->copy($__ctx, ["skip_comments"=>false]);
			$res = $parser->parser_base->staticMethod("readToken")($__ctx, $parser->clone($__ctx));
			$look = $res[0];
			$token = $res[1];
			$parser = $parser->copy($__ctx, ["skip_comments"=>true]);
		}
		$items->appendVector($__ctx, $comments);
		return \Runtime\Collection::from([$parser,new \Bayrell\Lang\OpCodes\OpModule($__ctx, \Runtime\Dict::from(["uses"=>$parser->uses->toDict($__ctx),"items"=>$items->toCollection($__ctx),"caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($__ctx)]))]);
	}
	/* ======================= Class Init Functions ======================= */
	function getClassName()
	{
		return "Bayrell.Lang.LangBay.ParserBayProgram";
	}
	static function getCurrentNamespace()
	{
		return "Bayrell.Lang.LangBay";
	}
	static function getCurrentClassName()
	{
		return "Bayrell.Lang.LangBay.ParserBayProgram";
	}
	static function getParentClassName()
	{
		return "";
	}
	static function getClassInfo($__ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($__ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.LangBay.ParserBayProgram",
			"name"=>"Bayrell.Lang.LangBay.ParserBayProgram",
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