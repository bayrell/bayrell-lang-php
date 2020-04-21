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
class ParserBayOperator
{
	/**
	 * Read return
	 */
	static function readReturn($ctx, $parser)
	{
		$token = null;
		$op_code = null;
		$look = null;
		$res = $parser->parser_base::matchToken($ctx, $parser, "return");
		$parser = $res[0];
		$token = $res[1];
		$caret_start = $token->caret_start->clone($ctx);
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		if ($token->content != ";")
		{
			$res = $parser->parser_expression::readExpression($ctx, $parser);
			$parser = $res[0];
			$op_code = $res[1];
		}
		return \Runtime\Collection::from([$parser,new \Bayrell\Lang\OpCodes\OpReturn($ctx, \Runtime\Dict::from(["expression"=>$op_code,"caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($ctx)]))]);
	}
	/**
	 * Read delete
	 */
	static function readDelete($ctx, $parser)
	{
		$token = null;
		$op_code = null;
		$res = $parser->parser_base::matchToken($ctx, $parser, "delete");
		$parser = $res[0];
		$token = $res[1];
		$caret_start = $token->caret_start->clone($ctx);
		$res = $parser->parser_base::readDynamic($ctx, $parser);
		$parser = $res[0];
		$op_code = $res[1];
		return \Runtime\Collection::from([$parser,new \Bayrell\Lang\OpCodes\OpDelete($ctx, \Runtime\Dict::from(["op_code"=>$op_code,"caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($ctx)]))]);
	}
	/**
	 * Read throw
	 */
	static function readThrow($ctx, $parser)
	{
		$token = null;
		$op_code = null;
		$res = $parser->parser_base::matchToken($ctx, $parser, "throw");
		$parser = $res[0];
		$token = $res[1];
		$caret_start = $token->caret_start->clone($ctx);
		$res = $parser->parser_expression::readExpression($ctx, $parser);
		$parser = $res[0];
		$op_code = $res[1];
		return \Runtime\Collection::from([$parser,new \Bayrell\Lang\OpCodes\OpThrow($ctx, \Runtime\Dict::from(["expression"=>$op_code,"caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($ctx)]))]);
	}
	/**
	 * Read try
	 */
	static function readTry($ctx, $parser)
	{
		$look = null;
		$token = null;
		$op_try = null;
		$items = new \Runtime\Vector($ctx);
		$res = $parser->parser_base::matchToken($ctx, $parser, "try");
		$parser = $res[0];
		$token = $res[1];
		$caret_start = $token->caret_start->clone($ctx);
		/* Try */
		$res = static::readOperators($ctx, $parser);
		$parser = $res[0];
		$op_try = $res[1];
		/* Catch */
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		while (!$token->eof && $token->content == "catch")
		{
			$parser = $look->clone($ctx);
			$op_catch = null;
			$var_op_code = null;
			$pattern = null;
			$item_caret_start = $token->caret_start->clone($ctx);
			/* Read ident */
			$res = $parser->parser_base::matchToken($ctx, $parser, "(");
			$parser = $res[0];
			$res = $parser->parser_base::readTypeIdentifier($ctx, $parser);
			$parser = $res[0];
			$pattern = $res[1];
			$res = $parser->parser_base::readIdentifier($ctx, $parser);
			$parser = $res[0];
			$var_op_code = $res[1];
			$var_name = $var_op_code->value;
			$res = $parser->parser_base::matchToken($ctx, $parser, ")");
			$parser = $res[0];
			/* Save vars */
			$save_vars = $parser->vars;
			$parser = $parser->copy($ctx, ["vars"=>$parser->vars->setIm($ctx, $var_name, true)]);
			/* Catch operators */
			$res = static::readOperators($ctx, $parser);
			$parser = $res[0];
			$op_catch = $res[1];
			/* Restore vars */
			$parser = $parser->copy($ctx, ["vars"=>$save_vars]);
			$item = new \Bayrell\Lang\OpCodes\OpTryCatchItem($ctx, \Runtime\Dict::from(["name"=>$var_name,"pattern"=>$pattern,"value"=>$op_catch,"caret_start"=>$item_caret_start,"caret_end"=>$parser->caret->clone($ctx)]));
			$items->push($ctx, $item);
			$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
			$look = $res[0];
			$token = $res[1];
		}
		return \Runtime\Collection::from([$parser,new \Bayrell\Lang\OpCodes\OpTryCatch($ctx, \Runtime\Dict::from(["op_try"=>$op_try,"items"=>$items->toCollection($ctx),"caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($ctx)]))]);
	}
	/**
	 * Read then
	 */
	static function readThen($ctx, $parser)
	{
		$look = null;
		$token = null;
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		if ($token->content == "then")
		{
			return \Runtime\Collection::from([$look,$token]);
		}
		return \Runtime\Collection::from([$parser,$token]);
	}
	/**
	 * Read do
	 */
	static function readDo($ctx, $parser)
	{
		$look = null;
		$token = null;
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		if ($token->content == "do")
		{
			return \Runtime\Collection::from([$look,$token]);
		}
		return \Runtime\Collection::from([$parser,$token]);
	}
	/**
	 * Read if
	 */
	static function readIf($ctx, $parser)
	{
		$look = null;
		$look2 = null;
		$token = null;
		$token2 = null;
		$if_condition = null;
		$if_true = null;
		$if_false = null;
		$if_else = new \Runtime\Vector($ctx);
		$res = $parser->parser_base::matchToken($ctx, $parser, "if");
		$parser = $res[0];
		$token = $res[1];
		$caret_start = $token->caret_start->clone($ctx);
		/* Read expression */
		$res = $parser->parser_base::matchToken($ctx, $parser, "(");
		$parser = $res[0];
		$res = $parser->parser_expression::readExpression($ctx, $parser);
		$parser = $res[0];
		$if_condition = $res[1];
		$res = $parser->parser_base::matchToken($ctx, $parser, ")");
		$parser = $res[0];
		$res = static::readThen($ctx, $parser);
		$parser = $res[0];
		/* If true */
		$res = static::readOperators($ctx, $parser);
		$parser = $res[0];
		$if_true = $res[1];
		/* Else */
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		while (!$token->eof && ($token->content == "else" || $token->content == "elseif"))
		{
			$res = $parser->parser_base::readToken($ctx, $look->clone($ctx));
			$look2 = $res[0];
			$token2 = $res[1];
			if ($token2->content == "elseif" || $token2->content == "if")
			{
				$ifelse_condition = null;
				$ifelse_block = null;
				if ($token->content == "elseif")
				{
					$parser = $look->clone($ctx);
				}
				else if ($token2->content == "if")
				{
					$parser = $look2->clone($ctx);
				}
				/* Read expression */
				$res = $parser->parser_base::matchToken($ctx, $parser, "(");
				$parser = $res[0];
				$res = $parser->parser_expression::readExpression($ctx, $parser);
				$parser = $res[0];
				$ifelse_condition = $res[1];
				$res = $parser->parser_base::matchToken($ctx, $parser, ")");
				$parser = $res[0];
				$res = static::readThen($ctx, $parser);
				$parser = $res[0];
				$res = static::readOperators($ctx, $parser);
				$parser = $res[0];
				$ifelse_block = $res[1];
				$if_else->push($ctx, new \Bayrell\Lang\OpCodes\OpIfElse($ctx, \Runtime\Dict::from(["condition"=>$ifelse_condition,"if_true"=>$ifelse_block,"caret_start"=>$token2->caret_start->clone($ctx),"caret_end"=>$parser->caret->clone($ctx)])));
			}
			else
			{
				$res = static::readOperators($ctx, $look->clone($ctx));
				$parser = $res[0];
				$if_false = $res[1];
				break;
			}
			$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
			$look = $res[0];
			$token = $res[1];
		}
		return \Runtime\Collection::from([$parser,new \Bayrell\Lang\OpCodes\OpIf($ctx, \Runtime\Dict::from(["condition"=>$if_condition,"if_true"=>$if_true,"if_false"=>$if_false,"if_else"=>$if_else->toCollection($ctx),"caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($ctx)]))]);
	}
	/**
	 * Read For
	 */
	static function readFor($ctx, $parser)
	{
		$look = null;
		$token = null;
		$op_code = null;
		$expr1 = null;
		$expr2 = null;
		$expr3 = null;
		/* Save vars */
		$save_vars = $parser->vars;
		$res = $parser->parser_base::matchToken($ctx, $parser, "for");
		$parser = $res[0];
		$token = $res[1];
		$caret_start = $token->caret_start->clone($ctx);
		$res = $parser->parser_base::matchToken($ctx, $parser, "(");
		$parser = $res[0];
		$token = $res[1];
		$res = static::readAssign($ctx, $parser);
		$parser = $res[0];
		$expr1 = $res[1];
		$res = $parser->parser_base::matchToken($ctx, $parser, ";");
		$parser = $res[0];
		$token = $res[1];
		$res = $parser->parser_expression::readExpression($ctx, $parser);
		$parser = $res[0];
		$expr2 = $res[1];
		$res = $parser->parser_base::matchToken($ctx, $parser, ";");
		$parser = $res[0];
		$token = $res[1];
		$res = static::readOperator($ctx, $parser);
		$parser = $res[0];
		$expr3 = $res[1];
		$res = $parser->parser_base::matchToken($ctx, $parser, ")");
		$parser = $res[0];
		$token = $res[1];
		$res = static::readOperators($ctx, $parser);
		$parser = $res[0];
		$op_code = $res[1];
		/* Restore vars */
		$parser = $parser->copy($ctx, ["vars"=>$save_vars]);
		return \Runtime\Collection::from([$parser,new \Bayrell\Lang\OpCodes\OpFor($ctx, \Runtime\Dict::from(["expr1"=>$expr1,"expr2"=>$expr2,"expr3"=>$expr3,"value"=>$op_code,"caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($ctx)]))]);
	}
	/**
	 * Read While
	 */
	static function readWhile($ctx, $parser)
	{
		$look = null;
		$token = null;
		$condition = null;
		$op_code = null;
		$res = $parser->parser_base::matchToken($ctx, $parser, "while");
		$parser = $res[0];
		$token = $res[1];
		$caret_start = $token->caret_start->clone($ctx);
		$res = $parser->parser_base::matchToken($ctx, $parser, "(");
		$parser = $res[0];
		$res = $parser->parser_expression::readExpression($ctx, $parser);
		$parser = $res[0];
		$condition = $res[1];
		$res = $parser->parser_base::matchToken($ctx, $parser, ")");
		$parser = $res[0];
		$res = static::readDo($ctx, $parser);
		$parser = $res[0];
		$token = $res[1];
		$res = static::readOperators($ctx, $parser);
		$parser = $res[0];
		$op_code = $res[1];
		return \Runtime\Collection::from([$parser,new \Bayrell\Lang\OpCodes\OpWhile($ctx, \Runtime\Dict::from(["condition"=>$condition,"value"=>$op_code,"caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($ctx)]))]);
	}
	/**
	 * Read assign
	 */
	static function readAssign($ctx, $parser)
	{
		$start = $parser->clone($ctx);
		$save = null;
		$look = null;
		$token = null;
		$pattern = null;
		$op_code = null;
		$reg_name = null;
		$expression = null;
		$names = null;
		$values = null;
		$kind = \Bayrell\Lang\OpCodes\OpAssign::KIND_ASSIGN;
		$var_name = "";
		$res = $parser->parser_base::readIdentifier($ctx, $parser);
		$parser = $res[0];
		$op_code = $res[1];
		$caret_start = $op_code->caret_start->clone($ctx);
		$var_name = $op_code->value;
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		if ($token->content == "<=")
		{
			$arr = new \Runtime\Vector($ctx);
			while (!$token->eof && $token->content == "<=")
			{
				$parser = $look->clone($ctx);
				$save = $parser->clone($ctx);
				$res = $parser->parser_base::readToken($ctx, $parser);
				$parser = $res[0];
				$token = $res[1];
				$name = $token->content;
				$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
				$look = $res[0];
				$token = $res[1];
				if ($token->content != "<=")
				{
					$parser = $save->clone($ctx);
					break;
				}
				else
				{
					if (!$parser->parser_base::isIdentifier($ctx, $name))
					{
						throw new \Bayrell\Lang\Exceptions\ParserExpected($ctx, "Identifier", $save->caret->clone($ctx), $parser->file_name);
					}
					$arr->push($ctx, $name);
				}
			}
			$names = $arr->toCollection($ctx);
			$res = $parser->parser_expression::readExpression($ctx, $parser);
			$parser = $res[0];
			$expression = $res[1];
			return \Runtime\Collection::from([$parser,new \Bayrell\Lang\OpCodes\OpAssignStruct($ctx, \Runtime\Dict::from(["caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($ctx),"expression"=>$expression,"var_name"=>$var_name,"names"=>$names]))]);
		}
		if ($token->content != "=" && $token->content != "+=" && $token->content != "-=" && $token->content != "~=" && $token->content != "." && $token->content != "::" && $token->content != "[")
		{
			$var_op_code = null;
			$kind = \Bayrell\Lang\OpCodes\OpAssign::KIND_DECLARE;
			$values = new \Runtime\Vector($ctx);
			$parser = $start->clone($ctx);
			$res = $parser->parser_base::readTypeIdentifier($ctx, $parser);
			$parser = $res[0];
			$pattern = $res[1];
			$res = $parser->parser_base::readIdentifier($ctx, $parser);
			$parser = $res[0];
			$var_op_code = $res[1];
			$var_name = $var_op_code->value;
			/* Read expression */
			$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
			$look = $res[0];
			$token = $res[1];
			if ($token->content == "=")
			{
				$res = $parser->parser_expression::readExpression($ctx, $look->clone($ctx));
				$parser = $res[0];
				$expression = $res[1];
			}
			else
			{
				$expression = null;
			}
			$parser = $parser->copy($ctx, ["vars"=>$parser->vars->setIm($ctx, $var_name, true)]);
			$values->push($ctx, new \Bayrell\Lang\OpCodes\OpAssignValue($ctx, \Runtime\Dict::from(["var_name"=>$var_name,"expression"=>$expression,"caret_start"=>$var_op_code->caret_start->clone($ctx),"caret_end"=>$parser->caret->clone($ctx)])));
			/* Look next token */
			$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
			$look = $res[0];
			$token = $res[1];
			while (!$token->eof && $token->content == ",")
			{
				$res = $parser->parser_base::readIdentifier($ctx, $look->clone($ctx));
				$parser = $res[0];
				$var_op_code = $res[1];
				$var_name = $var_op_code->value;
				/* Read expression */
				$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
				$look = $res[0];
				$token = $res[1];
				if ($token->content == "=")
				{
					$res = $parser->parser_expression::readExpression($ctx, $look->clone($ctx));
					$parser = $res[0];
					$expression = $res[1];
				}
				else
				{
					$expression = null;
				}
				$parser = $parser->copy($ctx, ["vars"=>$parser->vars->setIm($ctx, $var_name, true)]);
				$values->push($ctx, new \Bayrell\Lang\OpCodes\OpAssignValue($ctx, \Runtime\Dict::from(["var_name"=>$var_name,"expression"=>$expression,"caret_start"=>$var_op_code->caret_start->clone($ctx),"caret_end"=>$parser->caret->clone($ctx)])));
				$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
				$look = $res[0];
				$token = $res[1];
			}
			$var_name = "";
			$expression = null;
		}
		else
		{
			$parser = $start->clone($ctx);
			$kind = \Bayrell\Lang\OpCodes\OpAssign::KIND_ASSIGN;
			$op = "";
			$res = $parser->parser_base::readDynamic($ctx, $parser);
			$parser = $res[0];
			$op_code = $res[1];
			$res = $parser->parser_base::readToken($ctx, $parser);
			$parser = $res[0];
			$token = $res[1];
			if ($token->content == "=" || $token->content == "+=" || $token->content == "-=" || $token->content == "~=")
			{
				$op = $token->content;
			}
			else
			{
				throw new \Bayrell\Lang\Exceptions\ParserError($ctx, "Unknown operator " . \Runtime\rtl::toStr($token->content), $token->caret_start->clone($ctx), $parser->file_name);
			}
			$res = $parser->parser_expression::readExpression($ctx, $parser);
			$parser = $res[0];
			$expression = $res[1];
			$values = \Runtime\Collection::from([new \Bayrell\Lang\OpCodes\OpAssignValue($ctx, \Runtime\Dict::from(["op_code"=>$op_code,"expression"=>$expression,"op"=>$op]))]);
			$var_name = "";
			$expression = null;
		}
		return \Runtime\Collection::from([$parser,new \Bayrell\Lang\OpCodes\OpAssign($ctx, \Runtime\Dict::from(["pattern"=>$pattern,"values"=>($values != null) ? $values->toCollection($ctx) : null,"caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($ctx),"expression"=>$expression,"var_name"=>$var_name,"names"=>$names,"kind"=>$kind]))]);
	}
	/**
	 * Read operator
	 */
	static function readInc($ctx, $parser)
	{
		$look = null;
		$look1 = null;
		$look2 = null;
		$token = null;
		$token1 = null;
		$token2 = null;
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look1 = $res[0];
		$token1 = $res[1];
		$caret_start = $token1->caret_start->clone($ctx);
		$res = $parser->parser_base::readToken($ctx, $look1->clone($ctx));
		$look2 = $res[0];
		$token2 = $res[1];
		$look1_content = $token1->content;
		$look2_content = $token2->content;
		if (($look1_content == "++" || $look1_content == "--") && $parser->parser_base::isIdentifier($ctx, $look2_content))
		{
			$parser = $look2->clone($ctx);
			$op_code = new \Bayrell\Lang\OpCodes\OpIdentifier($ctx, \Runtime\Dict::from(["value"=>$look2_content,"caret_start"=>$token2->caret_start->clone($ctx),"caret_end"=>$token2->caret_end->clone($ctx)]));
			$op_code = new \Bayrell\Lang\OpCodes\OpInc($ctx, \Runtime\Dict::from(["kind"=>($look1_content == "++") ? \Bayrell\Lang\OpCodes\OpInc::KIND_PRE_INC : \Bayrell\Lang\OpCodes\OpInc::KIND_PRE_DEC,"value"=>$op_code,"caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($ctx)]));
			return \Runtime\Collection::from([$parser,$op_code]);
		}
		if (($look2_content == "++" || $look2_content == "--") && $parser->parser_base::isIdentifier($ctx, $look1_content))
		{
			$parser = $look2->clone($ctx);
			$op_code = new \Bayrell\Lang\OpCodes\OpIdentifier($ctx, \Runtime\Dict::from(["value"=>$look1_content,"caret_start"=>$token1->caret_start->clone($ctx),"caret_end"=>$token1->caret_end->clone($ctx)]));
			$op_code = new \Bayrell\Lang\OpCodes\OpInc($ctx, \Runtime\Dict::from(["kind"=>($look2_content == "++") ? \Bayrell\Lang\OpCodes\OpInc::KIND_POST_INC : \Bayrell\Lang\OpCodes\OpInc::KIND_POST_DEC,"value"=>$op_code,"caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($ctx)]));
			return \Runtime\Collection::from([$parser,$op_code]);
		}
		return \Runtime\Collection::from([$parser,null]);
	}
	/**
	 * Read call function
	 */
	static function readCallFunction($ctx, $parser)
	{
		$op_code = null;
		$res = $parser->parser_base::readDynamic($ctx, $parser);
		$parser = $res[0];
		$op_code = $res[1];
		if ($op_code instanceof \Bayrell\Lang\OpCodes\OpCall || $op_code instanceof \Bayrell\Lang\OpCodes\OpPipe)
		{
			return \Runtime\Collection::from([$parser,$op_code]);
		}
		return \Runtime\Collection::from([$parser,null]);
	}
	/**
	 * Read operator
	 */
	static function readOperator($ctx, $parser)
	{
		$look = null;
		$token = null;
		$parser = $parser->copy($ctx, ["skip_comments"=>false]);
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		$caret_start = $token->caret_start->clone($ctx);
		$parser = $parser->copy($ctx, ["skip_comments"=>true]);
		if ($token->content == "/")
		{
			return $parser->parser_base::readComment($ctx, $parser);
		}
		else if ($token->content == "#switch" || $token->content == "#ifcode")
		{
			return $parser->parser_preprocessor::readPreprocessor($ctx, $parser);
		}
		else if ($token->content == "#ifdef")
		{
			return $parser->parser_preprocessor::readPreprocessorIfDef($ctx, $parser, \Bayrell\Lang\OpCodes\OpPreprocessorIfDef::KIND_OPERATOR);
		}
		else if ($token->content == "break")
		{
			return \Runtime\Collection::from([$look,new \Bayrell\Lang\OpCodes\OpBreak($ctx, \Runtime\Dict::from(["caret_start"=>$caret_start,"caret_end"=>$look->caret]))]);
		}
		else if ($token->content == "continue")
		{
			return \Runtime\Collection::from([$look,new \Bayrell\Lang\OpCodes\OpContinue($ctx, \Runtime\Dict::from(["caret_start"=>$caret_start,"caret_end"=>$look->caret->clone($ctx)]))]);
		}
		else if ($token->content == "delete")
		{
			return static::readDelete($ctx, $parser);
		}
		else if ($token->content == "return")
		{
			return static::readReturn($ctx, $parser);
		}
		else if ($token->content == "throw")
		{
			return static::readThrow($ctx, $parser);
		}
		else if ($token->content == "try")
		{
			return static::readTry($ctx, $parser);
		}
		else if ($token->content == "if")
		{
			return static::readIf($ctx, $parser);
		}
		else if ($token->content == "for")
		{
			return static::readFor($ctx, $parser);
		}
		else if ($token->content == "while")
		{
			return static::readWhile($ctx, $parser);
		}
		$op_code = null;
		/* Read op inc */
		$res = static::readInc($ctx, $parser->clone($ctx));
		$look = $res[0];
		$op_code = $res[1];
		if ($op_code != null)
		{
			return $res;
		}
		/* Read op call function */
		$res = static::readCallFunction($ctx, $parser->clone($ctx));
		$look = $res[0];
		$op_code = $res[1];
		if ($op_code != null)
		{
			return $res;
		}
		return static::readAssign($ctx, $parser);
	}
	/**
	 * Read operators
	 */
	static function readOpItems($ctx, $parser, $end_tag="}")
	{
		$look = null;
		$token = null;
		$op_code = null;
		$arr = new \Runtime\Vector($ctx);
		$caret_start = $parser->caret;
		$parser = $parser->copy($ctx, ["skip_comments"=>false]);
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		$parser = $parser->copy($ctx, ["skip_comments"=>true]);
		while (!$token->eof && $token->content != $end_tag)
		{
			$parser_value = null;
			$res = static::readOperator($ctx, $parser);
			$parser = $res[0];
			$parser_value = $res[1];
			if ($parser_value != null)
			{
				$arr->push($ctx, $parser_value);
			}
			$parser = $parser->copy($ctx, ["skip_comments"=>false]);
			$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
			$look = $res[0];
			$token = $res[1];
			$parser = $parser->copy($ctx, ["skip_comments"=>true]);
			if ($token->content == ";")
			{
				$parser = $look->clone($ctx);
				$parser = $parser->copy($ctx, ["skip_comments"=>false]);
				$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
				$look = $res[0];
				$token = $res[1];
				$parser = $parser->copy($ctx, ["skip_comments"=>true]);
			}
		}
		$op_code = new \Bayrell\Lang\OpCodes\OpItems($ctx, \Runtime\Dict::from(["items"=>$arr->toCollection($ctx),"caret_start"=>$caret_start,"caret_end"=>$parser->caret]));
		return \Runtime\Collection::from([$parser,$op_code]);
	}
	/**
	 * Read operators
	 */
	static function readOperators($ctx, $parser)
	{
		$look = null;
		$token = null;
		$op_code = null;
		/* Save vars */
		$save_vars = $parser->vars;
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		$caret_start = $token->caret_start->clone($ctx);
		if ($token->content == "{")
		{
			$res = $parser->parser_base::matchToken($ctx, $parser, "{");
			$parser = $res[0];
			$res = static::readOpItems($ctx, $parser, "}");
			$parser = $res[0];
			$op_code = $res[1];
			$res = $parser->parser_base::matchToken($ctx, $parser, "}");
			$parser = $res[0];
		}
		else
		{
			$res = static::readOperator($ctx, $parser);
			$parser = $res[0];
			$op_code = $res[1];
			$res = $parser->parser_base::matchToken($ctx, $parser, ";");
			$parser = $res[0];
		}
		/* Restore vars */
		$parser = $parser->copy($ctx, ["vars"=>$save_vars]);
		return \Runtime\Collection::from([$parser,$op_code]);
	}
	/**
	 * Read flags
	 */
	static function readFlags($ctx, $parser)
	{
		$look = null;
		$token = null;
		$values = new \Runtime\Map($ctx);
		$current_flags = \Bayrell\Lang\OpCodes\OpFlags::getFlags($ctx);
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		while (!$token->eof && $current_flags->indexOf($ctx, $token->content) >= 0)
		{
			$flag = $token->content;
			$values->set($ctx, "p_" . \Runtime\rtl::toStr($flag), true);
			$parser = $look->clone($ctx);
			$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
			$look = $res[0];
			$token = $res[1];
		}
		return \Runtime\Collection::from([$parser,new \Bayrell\Lang\OpCodes\OpFlags($ctx, $values)]);
	}
	/**
	 * Read function args
	 */
	static function readDeclareFunctionArgs($ctx, $parser, $find_ident=true)
	{
		$look = null;
		$token = null;
		$items = new \Runtime\Vector($ctx);
		$res = $parser->parser_base::matchToken($ctx, $parser, "(");
		$parser = $res[0];
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		while (!$token->eof && $token->content != ")")
		{
			$arg_value = null;
			$arg_pattern = null;
			$arg_expression = null;
			$arg_start = $parser;
			/* Arg type */
			$res = $parser->parser_base::readTypeIdentifier($ctx, $parser, $find_ident);
			$parser = $res[0];
			$arg_pattern = $res[1];
			/* Arg name */
			$res = $parser->parser_base::readIdentifier($ctx, $parser);
			$parser = $res[0];
			$arg_value = $res[1];
			$arg_name = $arg_value->value;
			/* Arg expression */
			$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
			$look = $res[0];
			$token = $res[1];
			if ($token->content == "=")
			{
				$parser = $look->clone($ctx);
				$save_vars = $parser->vars;
				$parser = $parser->copy($ctx, ["vars"=>new \Runtime\Dict($ctx)]);
				$res = $parser->parser_expression::readExpression($ctx, $parser);
				$parser = $res[0];
				$arg_expression = $res[1];
				$parser = $parser->copy($ctx, ["vars"=>$save_vars]);
			}
			/* Register variable in parser */
			$parser = $parser->copy($ctx, ["vars"=>$parser->vars->setIm($ctx, $arg_name, true)]);
			$items->push($ctx, new \Bayrell\Lang\OpCodes\OpDeclareFunctionArg($ctx, \Runtime\Dict::from(["pattern"=>$arg_pattern,"name"=>$arg_name,"expression"=>$arg_expression,"caret_start"=>$arg_pattern->caret_start->clone($ctx),"caret_end"=>$parser->caret->clone($ctx)])));
			$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
			$look = $res[0];
			$token = $res[1];
			if ($token->content == ",")
			{
				$parser = $look->clone($ctx);
				$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
				$look = $res[0];
				$token = $res[1];
			}
		}
		$res = $parser->parser_base::matchToken($ctx, $parser, ")");
		$parser = $res[0];
		return \Runtime\Collection::from([$parser,$items->toCollection($ctx)]);
	}
	/**
	 * Read function variables
	 */
	static function readDeclareFunctionUse($ctx, $parser, $vars=null, $find_ident=true)
	{
		$look = null;
		$token = null;
		$items = new \Runtime\Vector($ctx);
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		if ($token->content == "use")
		{
			$parser = $look->clone($ctx);
			$res = $parser->parser_base::matchToken($ctx, $parser, "(");
			$parser = $res[0];
			$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
			$look = $res[0];
			$token = $res[1];
			while (!$token->eof && $token->content != ")")
			{
				$ident = null;
				$res = $parser->parser_base::readIdentifier($ctx, $parser);
				$parser = $res[0];
				$ident = $res[1];
				$name = $ident->value;
				if ($vars != null && $find_ident)
				{
					if (!$vars->has($ctx, $name))
					{
						throw new \Bayrell\Lang\Exceptions\ParserError($ctx, "Unknown identifier '" . \Runtime\rtl::toStr($name) . \Runtime\rtl::toStr("'"), $ident->caret_start->clone($ctx), $parser->file_name);
					}
				}
				$items->push($ctx, $name);
				$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
				$look = $res[0];
				$token = $res[1];
				if ($token->content == ",")
				{
					$parser = $look->clone($ctx);
					$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
					$look = $res[0];
					$token = $res[1];
				}
			}
			$res = $parser->parser_base::matchToken($ctx, $parser, ")");
			$parser = $res[0];
		}
		return \Runtime\Collection::from([$parser,$items->toCollection($ctx)]);
	}
	/**
	 * Read function
	 */
	static function readDeclareFunction($ctx, $parser, $has_name=true)
	{
		$look = null;
		$parser_value = null;
		$op_code = null;
		$token = null;
		/* Clear vars */
		$save_vars = $parser->vars;
		$parser = $parser->copy($ctx, ["vars"=>new \Runtime\Dict($ctx)]);
		$res = $parser->parser_base::readTypeIdentifier($ctx, $parser);
		$parser = $res[0];
		$parser_value = $res[1];
		$caret_start = $parser_value->caret_start->clone($ctx);
		$result_type = $parser_value;
		$expression = null;
		$is_context = true;
		$name = "";
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		if ($token->content == "@")
		{
			$is_context = false;
			$parser = $look;
		}
		if ($has_name)
		{
			$res = $parser->parser_base::readIdentifier($ctx, $parser);
			$parser = $res[0];
			$parser_value = $res[1];
			$name = $parser_value->value;
		}
		/* Read function arguments */
		$args = null;
		$res = static::readDeclareFunctionArgs($ctx, $parser);
		$parser = $res[0];
		$args = $res[1];
		/* Read function variables */
		$vars = null;
		$res = static::readDeclareFunctionUse($ctx, $parser, $save_vars);
		$parser = $res[0];
		$vars = $res[1];
		/* Add variables */
		$vars->each($ctx, function ($ctx, $name) use (&$parser)
		{
			$parser = $parser->copy($ctx, ["vars"=>$parser->vars->setIm($ctx, $name, true)]);
		});
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		if ($token->content == "=>")
		{
			$res = $parser->parser_base::matchToken($ctx, $parser, "=>");
			$parser = $res[0];
			$res = $parser->parser_expression::readExpression($ctx, $parser);
			$parser = $res[0];
			$expression = $res[1];
			$op_code = null;
		}
		else if ($token->content == "{")
		{
			$save = $parser->clone($ctx);
			$res = $parser->parser_base::matchToken($ctx, $parser, "{");
			$parser = $res[0];
			$res = static::readOperators($ctx, $save);
			$parser = $res[0];
			$op_code = $res[1];
		}
		else if ($token->content == ";")
		{
			$res = $parser->parser_base::matchToken($ctx, $parser, ";");
			$parser = $res[0];
			$expression = null;
			$op_code = null;
		}
		/* Restore vars */
		$parser = $parser->copy($ctx, ["vars"=>$save_vars]);
		return \Runtime\Collection::from([$parser,new \Bayrell\Lang\OpCodes\OpDeclareFunction($ctx, \Runtime\Dict::from(["args"=>$args,"vars"=>$vars,"name"=>$name,"is_context"=>$is_context,"result_type"=>$result_type,"expression"=>$expression,"value"=>$op_code,"caret_start"=>$caret_start,"caret_end"=>$parser->caret->clone($ctx)]))]);
	}
	/**
	 * Returns true if next is function
	 */
	static function tryReadFunction($ctx, $parser, $has_name=true, $flags=null)
	{
		$look = null;
		$parser_value = null;
		$token = null;
		/* Clear vars */
		$save_vars = $parser->vars;
		$parser = $parser->copy($ctx, ["vars"=>new \Runtime\Dict($ctx)]);
		$parser = $parser->copy($ctx, ["find_ident"=>false]);
		$res = false;
		try
		{
			
			$res = $parser->parser_base::readTypeIdentifier($ctx, $parser, false);
			$parser = $res[0];
			$parser_value = $res[1];
			$caret_start = $parser_value->caret_start->clone($ctx);
			$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
			$look = $res[0];
			$token = $res[1];
			if ($token->content == "@")
			{
				$parser = $look;
			}
			if ($has_name)
			{
				$res = $parser->parser_base::readIdentifier($ctx, $parser);
				$parser = $res[0];
			}
			$res = static::readDeclareFunctionArgs($ctx, $parser, false);
			$parser = $res[0];
			$res = static::readDeclareFunctionUse($ctx, $parser, null, false);
			$parser = $res[0];
			$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
			$look = $res[0];
			$token = $res[1];
			if ($flags != null && $flags->p_declare || $parser->current_class_kind == "interface")
			{
				if ($token->content != ";")
				{
					throw new \Bayrell\Lang\Exceptions\ParserExpected($ctx, "Function", $caret_start, $parser->file_name);
				}
			}
			else if ($token->content != "=>" && $token->content != "{")
			{
				throw new \Bayrell\Lang\Exceptions\ParserExpected($ctx, "Function", $caret_start, $parser->file_name);
			}
			$res = true;
		}
		catch (\Exception $_ex)
		{
			if ($_ex instanceof \Bayrell\Lang\Exceptions\ParserExpected)
			{
				$e = $_ex;
				$res = false;
			}
			throw $_ex;
		}
		/* Restore vars */
		$parser = $parser->copy($ctx, ["vars"=>$save_vars]);
		$parser = $parser->copy($ctx, ["find_ident"=>true]);
		return $res;
	}
	/**
	 * Read annotation
	 */
	static function readAnnotation($ctx, $parser)
	{
		$look = null;
		$token = null;
		$name = null;
		$params = null;
		$res = $parser->parser_base::matchToken($ctx, $parser, "@");
		$parser = $res[0];
		$token = $res[1];
		$caret_start = $token->caret_start->clone($ctx);
		$res = $parser->parser_base::readTypeIdentifier($ctx, $parser);
		$parser = $res[0];
		$name = $res[1];
		$res = $parser->parser_base::readToken($ctx, $parser->clone($ctx));
		$look = $res[0];
		$token = $res[1];
		if ($token->content == "{")
		{
			$res = $parser->parser_base::readDict($ctx, $parser);
			$parser = $res[0];
			$params = $res[1];
		}
		return \Runtime\Collection::from([$parser,new \Bayrell\Lang\OpCodes\OpAnnotation($ctx, \Runtime\Dict::from(["name"=>$name,"params"=>$params]))]);
	}
	/* ======================= Class Init Functions ======================= */
	function getClassName()
	{
		return "Bayrell.Lang.LangBay.ParserBayOperator";
	}
	static function getCurrentNamespace()
	{
		return "Bayrell.Lang.LangBay";
	}
	static function getCurrentClassName()
	{
		return "Bayrell.Lang.LangBay.ParserBayOperator";
	}
	static function getParentClassName()
	{
		return "";
	}
	static function getClassInfo($ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.LangBay.ParserBayOperator",
			"name"=>"Bayrell.Lang.LangBay.ParserBayOperator",
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