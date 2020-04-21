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
class ParserBayProgram
{
	/**
	 * Read namespace
	 */
	static function readNamespace($ctx, $parser)
	{
		$token = null;
		$name = null;
		$res = $parser->parser_base::matchToken($ctx, $parser, "namespace");
		$parser = $res[0];
		$token = $res[1];
		$caret_start = $token->caret_start->clone($ctx);
		$res = $parser->parser_base::readEntityName($ctx, $parser, false);
		$parser = $res[0];
		$name = $res[1];
		$current_namespace_name = \Runtime\rs::join($ctx, ".", $name->names);
		$current_namespace = new \Bayrell\Lang\OpCodes\OpNamespace($ctx, \Runtime\Dict::from(["name"=>$current_namespace_name,"caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($ctx)]));
		$parser = $parser->copy($ctx, ["current_namespace"=>$current_namespace]);
		$parser = $parser->copy($ctx, ["current_namespace_name"=>$current_namespace_name]);
		return \Runtime\Collection::from([$parser,$current_namespace]);
	}
	/**
	 * Read use
	 */
	static function readUse($ctx, $parser)
	{
		$look = null;
		$token = null;
		$name = null;
		$alias = "";
		$res = $parser->parser_base::matchToken($ctx, $parser, "use");
		$parser = $res[0];
		$token = $res[1];
		$caret_start = $token->caret_start->clone($ctx);
		$res = $parser->parser_base::readEntityName($ctx, $parser, false);
		$parser = $res[0];
		$name = $res[1];
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		if ($token->content == "as")
		{
			$parser_value = null;
			$parser = $look->clone($ctx);
			$res = $parser->parser_base::readIdentifier($ctx, $parser);
			$parser = $res[0];
			$parser_value = $res[1];
			$alias = $parser_value->value;
		}
		return \Runtime\Collection::from([$parser,new \Bayrell\Lang\OpCodes\OpUse($ctx, \Runtime\Dict::from(["name"=>\Runtime\rs::join($ctx, ".", $name->names),"alias"=>$alias,"caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($ctx)]))]);
	}
	/**
	 * Read class body
	 */
	static function readClassBody($ctx, $parser, $end_tag="}")
	{
		$look = null;
		$token = null;
		$items = new \Runtime\Vector($ctx);
		$parser = $parser->copy($ctx, ["skip_comments"=>false]);
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		$parser = $parser->copy($ctx, ["skip_comments"=>true]);
		while (!$token->eof && $token->content != $end_tag)
		{
			$item = null;
			if ($token->content == "/")
			{
				$res = $parser->parser_base::readComment($ctx, $parser);
				$parser = $res[0];
				$item = $res[1];
				if ($item != null)
				{
					$items->push($ctx, $item);
				}
			}
			else if ($token->content == "@")
			{
				$res = $parser->parser_operator::readAnnotation($ctx, $parser);
				$parser = $res[0];
				$item = $res[1];
				$items->push($ctx, $item);
			}
			else if ($token->content == "#switch" || $token->content == "#ifcode")
			{
				$res = $parser->parser_preprocessor::readPreprocessor($ctx, $parser);
				$parser = $res[0];
				$item = $res[1];
				if ($item != null)
				{
					$items->push($ctx, $item);
				}
			}
			else if ($token->content == "#ifdef")
			{
				$res = $parser->parser_preprocessor::readPreprocessorIfDef($ctx, $parser, \Bayrell\Lang\OpCodes\OpPreprocessorIfDef::KIND_CLASS_BODY);
				$parser = $res[0];
				$item = $res[1];
				if ($item != null)
				{
					$items->push($ctx, $item);
				}
			}
			else
			{
				$flags = null;
				$res = $parser->parser_operator::readFlags($ctx, $parser);
				$parser = $res[0];
				$flags = $res[1];
				if ($parser->parser_operator::tryReadFunction($ctx, $parser->clone($ctx), true, $flags))
				{
					$res = $parser->parser_operator::readDeclareFunction($ctx, $parser, true);
					$parser = $res[0];
					$item = $res[1];
					if ($item->expression != null)
					{
						$res = $parser->parser_base::matchToken($ctx, $parser, ";");
						$parser = $res[0];
					}
				}
				else
				{
					$res = $parser->parser_operator::readAssign($ctx, $parser);
					$parser = $res[0];
					$item = $res[1];
					$res = $parser->parser_base::matchToken($ctx, $parser, ";");
					$parser = $res[0];
				}
				$item = $item->copy($ctx, ["flags"=>$flags]);
				if ($item != null)
				{
					$items->push($ctx, $item);
				}
			}
			$parser = $parser->copy($ctx, ["skip_comments"=>false]);
			$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
			$look = $res[0];
			$token = $res[1];
			$parser = $parser->copy($ctx, ["skip_comments"=>true]);
		}
		return \Runtime\Collection::from([$parser,$items->toCollection($ctx)]);
	}
	/**
	 * Class body analyze
	 */
	static function classBodyAnalyze($ctx, $parser, $arr)
	{
		$names = new \Runtime\Map($ctx);
		$vars = new \Runtime\Vector($ctx);
		$functions = new \Runtime\Vector($ctx);
		$items = new \Runtime\Vector($ctx);
		$annotations = new \Runtime\Vector($ctx);
		$comments = new \Runtime\Vector($ctx);
		$fn_create = null;
		$fn_destroy = null;
		for ($i = 0;$i < $arr->count($ctx);$i++)
		{
			$item = $arr->item($ctx, $i);
			if ($item instanceof \Bayrell\Lang\OpCodes\OpAnnotation)
			{
				$annotations->push($ctx, $item);
			}
			else if ($item instanceof \Bayrell\Lang\OpCodes\OpComment)
			{
				$comments->push($ctx, $item);
			}
			else if ($item instanceof \Bayrell\Lang\OpCodes\OpAssign)
			{
				for ($j = 0;$j < $item->values->count($ctx);$j++)
				{
					$assign_value = $item->values->item($ctx, $j);
					$value_name = $assign_value->var_name;
					if ($names->has($ctx, $value_name))
					{
						throw new \Bayrell\Lang\Exceptions\ParserError($ctx, "Dublicate identifier " . \Runtime\rtl::toStr($value_name), $assign_value->caret_start->clone($ctx), $parser->file_name);
					}
					$names->set($ctx, $value_name, true);
				}
				$item = $item->copy($ctx, \Runtime\Dict::from(["annotations"=>$annotations->toCollection($ctx),"comments"=>$comments->toCollection($ctx)]));
				$vars->push($ctx, $item);
				$annotations->clear($ctx);
				$comments->clear($ctx);
			}
			else if ($item instanceof \Bayrell\Lang\OpCodes\OpDeclareFunction)
			{
				$item = $item->copy($ctx, \Runtime\Dict::from(["annotations"=>$annotations->toCollection($ctx),"comments"=>$comments->toCollection($ctx)]));
				if ($names->has($ctx, $item->name))
				{
					throw new \Bayrell\Lang\Exceptions\ParserError($ctx, "Dublicate identifier " . \Runtime\rtl::toStr($item->name), $item->caret_start->clone($ctx), $parser->file_name);
				}
				$names->set($ctx, $item->name, true);
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
					$functions->push($ctx, $item);
				}
				$annotations->clear($ctx);
				$comments->clear($ctx);
			}
			else
			{
				$items->push($ctx, $item);
			}
		}
		$items->appendVector($ctx, $comments);
		return \Runtime\Dict::from(["annotations"=>$annotations->toCollection($ctx),"comments"=>$comments->toCollection($ctx),"functions"=>$functions->toCollection($ctx),"items"=>$items->toCollection($ctx),"vars"=>$vars->toCollection($ctx),"fn_create"=>$fn_create,"fn_destroy"=>$fn_destroy]);
	}
	/**
	 * Read class
	 */
	static function readClass($ctx, $parser)
	{
		$look = null;
		$token = null;
		$op_code = null;
		$template = null;
		$is_declare = false;
		$is_static = false;
		$is_struct = false;
		$class_kind = "";
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		$caret_start = $token->caret_start->clone($ctx);
		if ($token->content == "static")
		{
			$parser = $look->clone($ctx);
			$is_static = true;
		}
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		if ($token->content == "declare")
		{
			$parser = $look->clone($ctx);
			$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
			$look = $res[0];
			$token = $res[1];
			$is_declare = true;
		}
		if ($token->content == "class")
		{
			$res = $parser->parser_base::matchToken($ctx, $parser, "class");
			$parser = $res[0];
			$class_kind = \Bayrell\Lang\OpCodes\OpDeclareClass::KIND_CLASS;
		}
		else if ($token->content == "struct")
		{
			$res = $parser->parser_base::matchToken($ctx, $parser, "struct");
			$parser = $res[0];
			$class_kind = \Bayrell\Lang\OpCodes\OpDeclareClass::KIND_STRUCT;
		}
		else if ($token->content == "interface")
		{
			$res = $parser->parser_base::matchToken($ctx, $parser, "interface");
			$parser = $res[0];
			$class_kind = \Bayrell\Lang\OpCodes\OpDeclareClass::KIND_INTERFACE;
		}
		else
		{
			$res = $parser->parser_base::matchToken($ctx, $parser, "class");
		}
		$res = $parser->parser_base::readIdentifier($ctx, $parser);
		$parser = $res[0];
		$op_code = $res[1];
		$class_name = $op_code->value;
		/* Set class name */
		$parser = $parser->copy($ctx, ["current_class_name"=>$class_name]);
		$parser = $parser->copy($ctx, ["current_class_kind"=>$class_kind]);
		/* Register module in parser */
		$parser = $parser->copy($ctx, ["uses"=>$parser->uses->setIm($ctx, $class_name, $parser->current_namespace_name . \Runtime\rtl::toStr(".") . \Runtime\rtl::toStr($class_name))]);
		$save_uses = $parser->uses;
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		if ($token->content == "<")
		{
			$template = new \Runtime\Vector($ctx);
			$res = $parser->parser_base::matchToken($ctx, $parser, "<");
			$parser = $res[0];
			$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
			$look = $res[0];
			$token = $res[1];
			while (!$token->eof && $token->content != ">")
			{
				$parser_value = null;
				$res = $parser->parser_base::readIdentifier($ctx, $parser);
				$parser = $res[0];
				$parser_value = $res[1];
				$template->push($ctx, $parser_value);
				$parser = $parser->copy($ctx, ["uses"=>$parser->uses->setIm($ctx, $parser_value->value, $parser_value->value)]);
				$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
				$look = $res[0];
				$token = $res[1];
				if ($token->content != ">")
				{
					$res = $parser->parser_base::matchToken($ctx, $parser, ",");
					$parser = $res[0];
					$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
					$look = $res[0];
					$token = $res[1];
				}
			}
			$res = $parser->parser_base::matchToken($ctx, $parser, ">");
			$parser = $res[0];
		}
		$class_extends = null;
		$class_implements = null;
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		if ($token->content == "extends")
		{
			$res = $parser->parser_base::readTypeIdentifier($ctx, $look->clone($ctx));
			$parser = $res[0];
			$class_extends = $res[1];
		}
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		if ($token->content == "implements")
		{
			$class_implements = new \Runtime\Vector($ctx);
			$res = $parser->parser_base::readTypeIdentifier($ctx, $look->clone($ctx));
			$parser = $res[0];
			$op_code = $res[1];
			$class_implements->push($ctx, $op_code);
			$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
			$look = $res[0];
			$token = $res[1];
			while (!$token->eof && $token->content == ",")
			{
				$parser = $look->clone($ctx);
				$res = $parser->parser_base::readTypeIdentifier($ctx, $look->clone($ctx));
				$parser = $res[0];
				$op_code = $res[1];
				$class_implements->push($ctx, $op_code);
				$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
				$look = $res[0];
				$token = $res[1];
			}
		}
		$arr = null;
		$res = $parser->parser_base::matchToken($ctx, $parser, "{");
		$parser = $res[0];
		$res = static::readClassBody($ctx, $parser);
		$parser = $res[0];
		$arr = $res[1];
		$d = static::classBodyAnalyze($ctx, $parser, $arr);
		$res = $parser->parser_base::matchToken($ctx, $parser, "}");
		$parser = $res[0];
		$current_class = new \Bayrell\Lang\OpCodes\OpDeclareClass($ctx, \Runtime\Dict::from(["kind"=>$class_kind,"name"=>$class_name,"is_static"=>$is_static,"is_declare"=>$is_declare,"class_extends"=>$class_extends,"class_implements"=>($class_implements != null) ? $class_implements->toCollection($ctx) : null,"template"=>($template != null) ? $template->toCollection($ctx) : null,"vars"=>$d->item($ctx, "vars"),"functions"=>$d->item($ctx, "functions"),"fn_create"=>$d->item($ctx, "fn_create"),"fn_destroy"=>$d->item($ctx, "fn_destroy"),"items"=>$d->item($ctx, "items"),"caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($ctx)]));
		/* Restore uses */
		$parser = $parser->copy($ctx, ["uses"=>$save_uses]);
		return \Runtime\Collection::from([$parser->copy($ctx, \Runtime\Dict::from(["current_class"=>$current_class])),$current_class]);
	}
	/**
	 * Read program
	 */
	static function readProgram($ctx, $parser, $end_tag="")
	{
		$look = null;
		$token = null;
		$op_code = null;
		$annotations = new \Runtime\Vector($ctx);
		$comments = new \Runtime\Vector($ctx);
		$items = new \Runtime\Vector($ctx);
		$parser = $parser->copy($ctx, ["skip_comments"=>false]);
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		$caret_start = $token->caret_start->clone($ctx);
		$parser = $parser->copy($ctx, ["skip_comments"=>true]);
		if ($token->eof)
		{
			return \Runtime\Collection::from([$parser,null]);
		}
		while (!$token->eof && ($end_tag == "" || $end_tag != "" && $token->content == $end_tag))
		{
			if ($token->content == "/")
			{
				$res = $parser->parser_base::readComment($ctx, $parser);
				$parser = $res[0];
				$op_code = $res[1];
				if ($op_code != null)
				{
					$comments->push($ctx, $op_code);
				}
			}
			else if ($token->content == "@")
			{
				$res = $parser->parser_operator::readAnnotation($ctx, $parser);
				$parser = $res[0];
				$op_code = $res[1];
				$annotations->push($ctx, $op_code);
			}
			else if ($token->content == "#switch" || $token->content == "#ifcode")
			{
				/* Append comments */
				$items->appendVector($ctx, $comments);
				$comments->clear($ctx);
				$res = $parser->parser_preprocessor::readPreprocessor($ctx, $parser);
				$parser = $res[0];
				$op_code = $res[1];
				if ($op_code != null)
				{
					$items->appendVector($ctx, $comments);
					$items->push($ctx, $op_code);
				}
			}
			else if ($token->content == "#ifdef")
			{
				/* Append comments */
				$items->appendVector($ctx, $comments);
				$comments->clear($ctx);
				$res = $parser->parser_preprocessor::readPreprocessorIfDef($ctx, $parser, \Bayrell\Lang\OpCodes\OpPreprocessorIfDef::KIND_PROGRAM);
				$parser = $res[0];
				$op_code = $res[1];
				if ($op_code != null)
				{
					$items->appendVector($ctx, $comments);
					$items->push($ctx, $op_code);
				}
			}
			else if ($token->content == "namespace")
			{
				/* Append comments */
				$items->appendVector($ctx, $comments);
				$comments->clear($ctx);
				$res = static::readNamespace($ctx, $parser);
				$parser = $res[0];
				$op_code = $res[1];
				$items->push($ctx, $op_code);
				$res = $parser->parser_base::matchToken($ctx, $parser, ";");
				$parser = $res[0];
			}
			else if ($token->content == "use")
			{
				/* Append comments */
				$items->appendVector($ctx, $comments);
				$comments->clear($ctx);
				$res = static::readUse($ctx, $parser);
				$parser = $res[0];
				$op_code = $res[1];
				$full_name = $op_code->name;
				$short_name = "";
				if ($op_code->alias == "")
				{
					$short_name = \Runtime\rs::explode($ctx, ".", $full_name)->last($ctx);
				}
				else
				{
					$short_name = $op_code->alias;
				}
				/* Register module in parser */
				$parser = $parser->copy($ctx, ["uses"=>$parser->uses->setIm($ctx, $short_name, $full_name)]);
				$res = $parser->parser_base::matchToken($ctx, $parser, ";");
				$parser = $res[0];
			}
			else if ($token->content == "class" || $token->content == "struct" || $token->content == "static" || $token->content == "declare" || $token->content == "interface")
			{
				$item = null;
				$res = static::readClass($ctx, $parser);
				$parser = $res[0];
				$item = $res[1];
				$item = $item->copy($ctx, \Runtime\Dict::from(["annotations"=>$annotations->toCollection($ctx),"comments"=>$comments->toCollection($ctx)]));
				$items->push($ctx, $item);
				$annotations->clear($ctx);
				$comments->clear($ctx);
			}
			else
			{
				break;
			}
			$parser = $parser->copy($ctx, ["skip_comments"=>false]);
			$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
			$look = $res[0];
			$token = $res[1];
			$parser = $parser->copy($ctx, ["skip_comments"=>true]);
		}
		$items->appendVector($ctx, $comments);
		return \Runtime\Collection::from([$parser,new \Bayrell\Lang\OpCodes\OpModule($ctx, \Runtime\Dict::from(["uses"=>$parser->uses->toDict($ctx),"items"=>$items->toCollection($ctx),"caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($ctx)]))]);
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
	static function getClassInfo($ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.LangBay.ParserBayProgram",
			"name"=>"Bayrell.Lang.LangBay.ParserBayProgram",
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