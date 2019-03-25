<?php
/*!
 *  Bayrell Common Languages Transcompiler
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
namespace BayrellLang\LangBay;
use Runtime\rs;
use Runtime\rtl;
use Runtime\Map;
use Runtime\Vector;
use Runtime\Dict;
use Runtime\Collection;
use Runtime\IntrospectionInfo;
use Runtime\UIStruct;
use Runtime\rs;
use BayrellLang\CommonParser;
use BayrellLang\OpCodes\BaseOpCode;
use BayrellLang\OpCodes\OpAdd;
use BayrellLang\OpCodes\OpAnd;
use BayrellLang\OpCodes\OpAnnotation;
use BayrellLang\OpCodes\OpAssign;
use BayrellLang\OpCodes\OpAssignDeclare;
use BayrellLang\OpCodes\OpBitAnd;
use BayrellLang\OpCodes\OpBitNot;
use BayrellLang\OpCodes\OpBitOr;
use BayrellLang\OpCodes\OpBitXor;
use BayrellLang\OpCodes\OpBreak;
use BayrellLang\OpCodes\OpCall;
use BayrellLang\OpCodes\OpCallAwait;
use BayrellLang\OpCodes\OpChilds;
use BayrellLang\OpCodes\OpClassDeclare;
use BayrellLang\OpCodes\OpClassName;
use BayrellLang\OpCodes\OpClone;
use BayrellLang\OpCodes\OpComponent;
use BayrellLang\OpCodes\OpComment;
use BayrellLang\OpCodes\OpCompare;
use BayrellLang\OpCodes\OpConcat;
use BayrellLang\OpCodes\OpContinue;
use BayrellLang\OpCodes\OpCopyStruct;
use BayrellLang\OpCodes\OpDelete;
use BayrellLang\OpCodes\OpDiv;
use BayrellLang\OpCodes\OpDynamic;
use BayrellLang\OpCodes\OpFlags;
use BayrellLang\OpCodes\OpFor;
use BayrellLang\OpCodes\OpFunctionArrowDeclare;
use BayrellLang\OpCodes\OpFunctionDeclare;
use BayrellLang\OpCodes\OpHexNumber;
use BayrellLang\OpCodes\OpHtmlAttribute;
use BayrellLang\OpCodes\OpHtmlComment;
use BayrellLang\OpCodes\OpHtmlEscape;
use BayrellLang\OpCodes\OpHtmlJson;
use BayrellLang\OpCodes\OpHtmlRaw;
use BayrellLang\OpCodes\OpHtmlTag;
use BayrellLang\OpCodes\OpHtmlText;
use BayrellLang\OpCodes\OpHtmlView;
use BayrellLang\OpCodes\OpIdentifier;
use BayrellLang\OpCodes\OpIf;
use BayrellLang\OpCodes\OpIfElse;
use BayrellLang\OpCodes\OpInterfaceDeclare;
use BayrellLang\OpCodes\OpMap;
use BayrellLang\OpCodes\OpMethod;
use BayrellLang\OpCodes\OpMod;
use BayrellLang\OpCodes\OpMult;
use BayrellLang\OpCodes\OpNamespace;
use BayrellLang\OpCodes\OpNew;
use BayrellLang\OpCodes\OpNope;
use BayrellLang\OpCodes\OpNot;
use BayrellLang\OpCodes\OpNumber;
use BayrellLang\OpCodes\OpOr;
use BayrellLang\OpCodes\OpPipe;
use BayrellLang\OpCodes\OpPostDec;
use BayrellLang\OpCodes\OpPostInc;
use BayrellLang\OpCodes\OpPow;
use BayrellLang\OpCodes\OpPreDec;
use BayrellLang\OpCodes\OpPreInc;
use BayrellLang\OpCodes\OpPreprocessorCase;
use BayrellLang\OpCodes\OpPreprocessorSwitch;
use BayrellLang\OpCodes\OpReturn;
use BayrellLang\OpCodes\OpShiftLeft;
use BayrellLang\OpCodes\OpShiftRight;
use BayrellLang\OpCodes\OpStatic;
use BayrellLang\OpCodes\OpString;
use BayrellLang\OpCodes\OpStringItem;
use BayrellLang\OpCodes\OpStructDeclare;
use BayrellLang\OpCodes\OpSub;
use BayrellLang\OpCodes\OpTemplateIdentifier;
use BayrellLang\OpCodes\OpTernary;
use BayrellLang\OpCodes\OpThrow;
use BayrellLang\OpCodes\OpTryCatch;
use BayrellLang\OpCodes\OpTryCatchChilds;
use BayrellLang\OpCodes\OpUse;
use BayrellLang\OpCodes\OpVector;
use BayrellLang\OpCodes\OpWhile;
use BayrellLang\LangBay\HtmlToken;
use BayrellLang\LangBay\ParserBayToken;
use BayrellLang\LangBay\ParserBayNameToken;
use BayrellLang\Exceptions\HexNumberExpected;
use BayrellLang\Exceptions\TwiceDeclareElseError;
use BayrellParser\Exceptions\ParserError;
class ParserBay extends CommonParser{
	public $current_namespace;
	public $current_class_name;
	public $is_interface;
	public $modules;
	public $annotations;
	/**
	 * Tokens Fabric
	 * @return BayrellParserToken
	 */
	function createToken(){
		return new ParserBayToken($this->context(), $this);
	}
	/**
	 * Get module name
	 */
	function getModuleName($name){
		if ($this->modules->has($name)){
			return $this->modules->item($name);
		}
		return $name;
	}
	/**
	 * Read double value and
	 * @return BaseOpCode
	 */
	function readFixed(){
		$res = "";
		/* Try to read HEX Number */
		$this->pushToken();
		try{
			$res = $this->matchHexNumber();
		}catch(\Exception $_the_exception){
			if ($_the_exception instanceof \Exception){
				$ex = $_the_exception;
				if ($ex instanceof HexNumberExpected){
					throw $ex;
				}
				else if ($ex instanceof ParserError){
					$res = null;
				}
				else {
					throw $ex;
				}
			}
			else { throw $_the_exception; }
		}
		if ($res != null){
			$this->popToken();
			return new OpHexNumber($res);
		}
		else {
			$this->popRollbackToken();
		}
		$this->pushToken();
		try{
			$res = $this->matchDouble();
		}catch(\Exception $_the_exception){
			if ($_the_exception instanceof \Exception){
				$ex = $_the_exception;
				if ($ex instanceof ParserError){
					$res = null;
				}
				else {
					throw $ex;
				}
			}
			else { throw $_the_exception; }
		}
		if ($res != null){
			$this->popToken();
			return new OpNumber($res);
		}
		else {
			$this->popRollbackToken();
		}
		if ($this->lookNextTokenType() == ParserBayToken::TOKEN_STRING){
			return new OpString($this->readAnyNextToken()->token);
		}
		return null;
	}
	/**
	 * Read name
	 */
	function readIdentifierName(){
		$res = $this->lookNextToken();
		$s = rs::charAt($res, 0);
		if (!$this->isLetterChar($s) && $s != "_"){
			throw $this->parserError($this->translate("ERROR_PARSER_FIRST_CHAR_MUST_BE_LETTER"));
		}
		$this->readNextToken();
		return $res;
	}
	/**
	 * Read name
	 */
	function readDynamicName(){
		/* Create new token */
		$next_token = new ParserBayNameToken($this->context(), $this);
		$this->pushToken($next_token);
		/* Get name */
		$name = $next_token->token;
		/* Assign next token */
		$this->popRollbackToken();
		$this->assignCurrentToken($next_token);
		return $name;
	}
	/**
	 * Read Identifier
	 * @return OpIdentifier
	 */
	function readIdentifier(){
		$res = $this->readIdentifierName();
		return new OpIdentifier($res);
	}
	/**
	 * Read call args
	 * @return BaseOpCode
	 */
	function readCallArgs(){
		$v = new Vector();
		$op_code = $this->readExpression();
		$v->push($op_code);
		while ($this->findNextToken(",")){
			$this->matchNextToken(",");
			$op_code = $this->readExpression();
			$v->push($op_code);
		}
		return $v;
	}
	/**
	 * Read call body
	 * @return BaseOpCode
	 */
	function readCallBody(){
		$v = null;
		$this->matchNextToken("(");
		if (!$this->findNextToken(")")){
			$v = $this->readCallArgs();
		}
		$this->matchNextToken(")");
		return $v;
	}
	/**
	 * Read new or await function
	 */
	function readGroupExpression(){
		if ($this->findNextToken("(")){
			$this->matchNextToken("(");
			$op_code = $this->readExpression();
			$this->matchNextToken(")");
			return $op_code;
		}
		return $this->readIdentifier();
	}
	/**
	 * Read new instance
	 * @return BaseOpCode
	 */
	function readNewInstance(){
		$this->matchNextToken("new");
		$ident = $this->readTemplateIdentifier();
		if ($this->findNextToken("(")){
			$v = $this->readCallBody();
			return new OpNew($ident, $v);
		}
		else if ($this->findNextToken("{")){
			$v = $this->readMap();
			return new OpNew($ident, (new Vector())->push($v));
		}
		return new OpNew($ident);
	}
	/**
	 * Read call await
	 * @return BaseOpCode
	 */
	function readCallAwait(){
		$this->matchNextToken("await");
		$obj = $this->readCallDynamic(true, true, true, false);
		$v = $this->readCallBody();
		$obj = new OpCall($obj, $v);
		$obj->is_await = true;
		return $obj;
	}
	/**
	 * Read clone
	 * @return BaseOpCode
	 */
	function readClone(){
		$this->matchNextToken("clone");
		$value = $this->readExpression();
		return new OpClone($value);
	}
	/**
	 * Read method
	 * @return BaseOpCode
	 */
	function readMethod(){
		$this->matchNextToken("method");
		if ($this->findNextToken("(")){
			$this->matchNextToken("(");
			$class_name = $this->readExpression();
			$this->matchNextToken(",");
			$method_name = $this->readExpression();
			$this->matchNextToken(")");
			return new OpCall(new OpStatic(new OpIdentifier("rtl"), "method"), (new Vector())->push($class_name)->push($method_name));
		}
		$value = $this->readCallDynamic(true, false, true, false);
		return new OpMethod($value);
	}
	/**
	 * Read pipe
	 * @return BaseOpCode
	 */
	function readPipe(){
		$item = null;
		$items = new Vector();
		$is_return_value = false;
		$value = null;
		$this->matchNextToken("pipe");
		$this->matchNextToken("(");
		if (!$this->findNextToken(")")){
			$value = $this->readExpression();
		}
		$this->matchNextToken(")");
		while ($this->findNextToken(">>")){
			$this->matchNextToken(">>");
			if ($this->findNextToken("method")){
				$item = $this->readMethod();
			}
			else if ($this->findNextToken("value")){
				break;
			}
			else {
				$item = $this->readCallDynamic(true, true, true, true);
			}
			$items->push($item);
		}
		if ($this->findNextToken("value")){
			$this->matchNextToken("value");
			$is_return_value = true;
		}
		return new OpPipe((new Map())->set("value", $value)->set("items", $items)->set("is_return_value", $is_return_value));
	}
	/**
	 * Read get class name
	 */
	function readClassName(){
		if ($this->findNextToken("class")){
			$this->matchNextToken("class");
			$this->matchNextToken("of");
			$value = $this->readIdentifierName();
			return new OpClassName($value);
		}
		if ($this->findNextToken("classof")){
			$this->matchNextToken("classof");
			$value = $this->readIdentifierName();
			return new OpClassName($value);
		}
		return null;
	}
	/**
	 * Read call dynamic
	 * @return BaseOpCode
	 */
	function readCallDynamic($allow_dynamic = true, $allow_bracket = true, $allow_static = true, $allow_call = true){
		$name = "";
		$can_static = true;
		$obj = $this->readGroupExpression();
		$ident = null;
		while ($this->findNextToken(".") && $allow_dynamic || $this->findNextToken("[") && $allow_bracket || $this->findNextToken("::") && $allow_static || $this->findNextToken("(") && $allow_call){
			if ($this->findNextToken(".") && $allow_dynamic){
				$this->matchNextToken(".");
				$name = $this->readIdentifierName();
				$obj = new OpDynamic($obj, $name);
			}
			else if ($this->findNextToken("[") && $allow_bracket){
				$this->matchNextToken("[");
				$ident = $this->readExpression();
				$this->matchNextToken("]");
				$obj = new OpStringItem($obj, $ident);
			}
			else if ($this->findNextToken("::") && $allow_static){
				if (!$can_static){
					throw $this->parserError($this->translate("ERROR_PARSER_STATIC_METHOD_IS_NOT_ALOWED_HERE"));
				}
				$this->matchNextToken("::");
				$name = $this->readIdentifierName();
				$obj = new OpStatic($obj, $name);
				$can_static = false;
			}
			else if ($this->findNextToken("(") && $allow_call){
				$v = $this->readCallBody();
				$obj = new OpCall($obj, $v);
				$can_static = false;
			}
		}
		return $obj;
	}
	/**
	 * Read type identifier
	 * @return BaseOpCode
	 */
	function readTemplateIdentifier(){
		$op_code1 = $this->readCallDynamic(true, false, false, false);
		if (!$this->findNextToken("<")){
			return $op_code1;
		}
		$v = new Vector();
		$this->matchNextToken("<");
		while (true){
			$op_code2 = $this->readCallDynamic(true, false, false, false);
			$v->push($op_code2);
			if ($this->findNextToken(",")){
				$this->matchNextToken(",");
				continue;
			}
			break;
		}
		$this->matchNextToken(">");
		return new OpTemplateIdentifier($op_code1, $v);
	}
	/**
	 * Read element
	 * @return BaseOpCode
	 */
	function readVector(){
		$res = new OpVector();
		$this->matchNextToken("[");
		while (!$this->findNextToken("]")){
			$res->values->push($this->readExpression());
			if ($this->findNextToken(",")){
				$this->matchNextToken(",");
			}
		}
		$this->matchNextToken("]");
		return $res;
	}
	/**
	 * Read element
	 * @return BaseOpCode
	 */
	function readMap(){
		$res = new OpMap();
		$this->matchNextToken("{");
		while (!$this->findNextToken("}")){
			if ($this->lookNextTokenType() != ParserBayToken::TOKEN_STRING){
				throw $this->parserExpected("string");
			}
			$key = $this->readAnyNextToken()->token;
			$this->matchNextToken(":");
			$value = $this->readExpression();
			$res->values->set($key, $value);
			if ($this->findNextToken(",")){
				$this->matchNextToken(",");
			}
		}
		$this->matchNextToken("}");
		return $res;
	}
	/* ============== HTML Template Parser ============== */
	/**
	 * Read Html Comment
	 */
	function readHtmlComment(){
		$this->matchNextToken("<!--");
		$res_str = $this->current_token->readUntilString("-->", false);
		$this->assignCurrentToken($this->current_token);
		$this->matchNextToken("-->");
		return new OpHtmlText("<!--" . rtl::toString($res_str) . "-->");
	}
	/**
	 * Read Html Doctype
	 */
	function readHtmlDOCTYPE(){
		$s = $this->current_token->readUntilString(">", false);
		$this->assignCurrentToken($this->current_token);
		$res = new OpHtmlText("<!" . rtl::toString(rs::trim($s)) . ">");
		$this->matchNextToken(">");
		return $res;
	}
	/**
	 * Add attribute
	 */
	function addAttribute($attributes, $attr){
		$pos = -1;
		for ($i = 0; $i < $attributes->count(); $i++){
			if ($attributes->item($i)->key == $attr->key){
				$pos = $i;
				break;
			}
		}
		if ($pos == -1){
			$attributes->push($attr);
		}
		else {
			mb_substr($attributes, $i, 1)->value = new OpConcat(mb_substr($attributes, $i, 1)->value, new OpString(" "));
			mb_substr($attributes, $i, 1)->value = new OpConcat(mb_substr($attributes, $i, 1)->value, $attr->value);
		}
	}
	/**
	 * Read Html Attributes
	 */
	function readHtmlAttributes($op_code){
		if ($this->findNextToken(">")){
			return null;
		}
		$spreads = new Vector();
		$attributes = new Vector();
		while (!$this->findNextToken(">") && !$this->findNextToken("/>")){
			if ($this->findNextToken("...")){
				$this->matchNextToken("...");
				$spread_name = $this->readIdentifierName();
				$spreads->push($spread_name);
				continue;
			}
			$spec_attr = false;
			$attr = new OpHtmlAttribute();
			if ($this->findNextToken("@")){
				$spec_attr = true;
				$this->matchNextToken("@");
				$attr->key = "@" . rtl::toString($this->readNextToken()->token);
			}
			else {
				$attr->key = $this->readNextToken()->token;
			}
			if ($attr->key == "@lambda"){
				$attr->value = $this->readDeclareFunctionHeader(new OpFunctionDeclare());
			}
			else if ($this->findNextToken("=")){
				$this->matchNextToken("=");
				if ($this->findNextToken("'") || $this->findNextToken("\"")){
					$this->pushToken(new ParserBayToken($this->context(), $this));
					$attr->value = new OpString($this->readAnyNextToken()->token);
					$this->popRestoreToken();
				}
				else if ($this->findNextToken("@{") || $this->findNextToken("{")){
					if ($this->findNextToken("@{")){
						$this->matchNextToken("@{");
					}
					else if ($this->findNextToken("{")){
						$this->matchNextToken("{");
					}
					$this->pushToken(new ParserBayToken($this->context(), $this));
					$attr->value = $this->readExpression();
					$this->popRestoreToken();
					$this->matchNextToken("}");
				}
				else {
					throw $this->parserError("Unknown token " . rtl::toString($this->next_token->token));
				}
			}
			else {
				$attr->bracket = "\"";
				$attr->value = new OpString($attr->key);
			}
			$this->addAttribute($attributes, $attr);
		}
		$op_code->spreads = $spreads;
		$op_code->attributes = $attributes;
	}
	/**
	 * Read Html Expression
	 */
	function readHtmlBlock($match_str, $is_plain = false){
		$len_match = rs::strlen($match_str);
		if ($len_match == 0){
			return null;
		}
		$look_str = $this->current_token->lookString($len_match);
		$childs = new Vector();
		$special_tokens = HtmlToken::getSpecialTokens();
		$special_tokens->removeValue("{");
		$special_tokens->removeValue("@{");
		$special_tokens->removeValue("@raw{");
		$special_tokens->removeValue("@json{");
		/*special_tokens.removeValue("@code{");*/
		$special_tokens->removeValue("<!--");
		$bracket_level = 0;
		$s = "";
		$is_next_html_token = false;
		$is_next_special_token = false;
		$is_prev_special_token = false;
		/* Main loop */
		while ($look_str != "" && !$this->current_token->isEOF() && ($look_str != $match_str || $look_str == "}" && $bracket_level > 0)){
			$is_next_html_token = $this->current_token->findString("<");
			$is_next_special_token = $this->current_token->findVector($special_tokens) != -1;
			$res = null;
			if (!$is_plain){
				if ($is_next_special_token || $is_next_html_token){
					if ($is_next_special_token || $is_prev_special_token){
						$s = rs::trim($s, "\\t\\r\\n");
					}
					else {
						$s = rs::trim($s, "\\t\\r\\n");
					}
					if ($s != ""){
						$childs->push(new OpHtmlText($s));
					}
					$s = "";
					$this->assignCurrentToken($this->current_token);
					$res = $this->readHtmlTag();
					$is_prev_special_token = false;
				}
			}
			if ($res == null){
				if ($this->current_token->findString("{") || $this->current_token->findString("@{") || $this->current_token->findString("@raw{") || $this->current_token->findString("@json{")){
					if (!$is_plain){
						$s = rs::trim($s, "\\t\\r\\n");
					}
					if ($s != ""){
						$childs->push(new OpHtmlText($s));
					}
					$s = "";
					$is_raw = false;
					$is_json = false;
					$is_code = false;
					if ($this->current_token->findString("@raw{")){
						$is_raw = true;
						$this->current_token->match("@raw{");
					}
					else if ($this->current_token->findString("@json{")){
						$is_json = true;
						$this->current_token->match("@json{");
					}
					else if ($this->current_token->findString("@code{")){
						$is_code = true;
						$this->current_token->match("@code{");
					}
					else if ($this->current_token->findString("@{")){
						$this->current_token->match("@{");
					}
					else if ($this->current_token->findString("{")){
						$this->current_token->match("{");
					}
					$this->assignCurrentToken($this->current_token);
					$this->pushToken(new ParserBayToken($this->context(), $this));
					$res = $this->readExpression();
					if ($is_raw){
						$res = new OpHtmlRaw($res);
					}
					else if ($is_json){
						$res = new OpHtmlJson($res);
					}
					else if ($is_code){
						$res = new OpHtmlRaw($res);
					}
					else {
						$res = new OpHtmlEscape($res);
					}
					$this->popRestoreToken();
					$this->matchNextToken("}");
					$is_prev_special_token = true;
				}
			}
			if ($res != null){
				$childs->push($res);
			}
			else {
				$look = $this->current_token->readChar();
				$s = rtl::toString($s) . rtl::toString($look);
				if ($look == "{"){
					$bracket_level++;
				}
				else if ($look == "}"){
					$bracket_level--;
				}
			}
			$look_str = $this->current_token->lookString($len_match);
		}
		if (!$is_plain){
			if ($is_prev_special_token){
				$s = rs::trim($s, "\\t\\r\\n");
			}
			else {
				$s = rs::trim($s, "\\t\\r\\n");
			}
		}
		if ($s != ""){
			$childs->push(new OpHtmlText($s));
		}
		$this->assignCurrentToken($this->current_token);
		return $childs;
	}
	/**
	 * Read Html tag
	 * @return BaseOpCode
	 */
	function readHtmlTag(){
		if ($this->lookNextTokenType() == ParserBayToken::TOKEN_COMMENT){
			return new OpComment($this->readAnyNextToken()->token);
		}
		else if ($this->findNextToken("<!--")){
			return $this->readHtmlComment();
		}
		else if ($this->findNextToken("<!")){
			$this->matchNextToken("<!");
			if ($this->findNextString("DOCTYPE")){
				return $this->readHtmlDOCTYPE();
			}
		}
		else if ($this->findNextToken("<")){
			$this->matchNextToken("<");
			if ($this->findNextToken(">")){
				$this->matchNextToken(">");
				$res = new OpHtmlView($this->readHtmlBlock("</>"));
				if ($res->childs != null && $res->childs->count() == 1){
					$res = $res->childs->item(0);
				}
				$this->matchNextToken("</");
				$this->matchNextToken(">");
				return $res;
			}
			$res = new OpHtmlTag();
			$res->tag_name = $this->readNextToken()->token;
			$this->readHtmlAttributes($res);
			$childs_function = null;
			if ($this->findNextToken("/>")){
				$this->matchNextToken("/>");
			}
			else {
				$this->matchNextToken(">");
				$close_tag = "</" . rtl::toString($res->tag_name) . ">";
				if ($res->tag_name == "script" || $res->tag_name == "pre" || $res->tag_name == "textarea"){
					$res->is_plain = true;
					$res->childs = $this->readHtmlBlock("</" . rtl::toString($res->tag_name) . ">", true);
				}
				else if ($this->findNextToken("lambda") || $this->findNextToken("pure")){
					$this->assignCurrentToken($this->current_token);
					$this->pushToken(new ParserBayToken($this->context(), $this));
					$childs_function = $this->readDeclareFunction(false);
					$this->popRestoreToken();
				}
				else {
					$res->childs = $this->readHtmlBlock("</");
				}
				$this->matchNextToken("</");
				$this->matchNextToken($res->tag_name);
				$this->matchNextToken(">");
			}
			if ($childs_function != null){
				$res->setAttribute("@lambda", $childs_function);
				if ($res->childs){
					$res->childs->clear();
				}
			}
			else {
				$op_code = $res->findAttribute("@lambda");
				if ($op_code != null && $op_code->value instanceof OpFunctionDeclare){
					$op_code->value->is_lambda = true;
					if ($res->childs != null){
						$op_code->value->childs = $res->childs->copy();
						$res->childs->clear();
					}
				}
			}
			return $res;
		}
		return null;
	}
	/**
	 * Read Html
	 * @return BaseOpCode
	 */
	function readHtml(){
		/* Skip comments */
		$old_skip_comments = $this->skip_comments;
		$this->skip_comments = false;
		/* Push new token */
		$this->pushToken(new HtmlToken($this->context(), $this));
		/* Read Html tag */
		$this->current_token->skipSystemChar();
		/* Read Html View */
		$res = new OpHtmlView();
		$res->childs = new Vector();
		while ($this->findNextToken("<") || $this->findNextToken("<!")){
			$res->childs->push($this->readHtmlTag());
		}
		if ($res->childs == null){
			$this->popRestoreToken();
			return null;
		}
		if ($res->childs->count() == 0){
			$this->popRestoreToken();
			return null;
		}
		$this->popRestoreToken();
		$this->skip_comments = $old_skip_comments;
		return $res;
	}
	/**
	 * Retuns css hash 
	 * @param string component class name
	 * @return string hash
	 */
	function getCssHash($s){
		$arr = "1234567890abcdef";
		$arr_sz = 16;
		$arr_mod = 65536;
		$sz = rs::strlen($s);
		$hash = 0;
		for ($i = 0; $i < $sz; $i++){
			$ch = rs::ord(mb_substr($s, $i, 1));
			$hash = ($hash << 2) + ($hash >> 14) + $ch & 65535;
		}
		$res = "";
		$pos = 0;
		$c = 0;
		while ($hash != 0 || $pos < 4){
			$c = $hash & 15;
			$hash = $hash >> 4;
			$res .= mb_substr($arr, $c, 1);
			$pos++;
		}
		return $res;
	}
	/**
	 * Read CSS Selector
	 */
	function readCssSelector($look){
		$s = "";
		$s = $this->current_token->readUntilVector((new Vector())->push(",")->push("%")->push("{"));
		if ($s == ""){
			return "";
		}
		if ($look != "%"){
			return $s;
		}
		$pos = 0;
		$sz = rs::strlen($s);
		$class_name = rtl::toString($this->current_namespace) . "." . rtl::toString($this->current_class_name);
		/* Read class name */
		if (mb_substr($s, 0, 1) == "("){
			while ($pos < $sz && mb_substr($s, $pos, 1) != ")"){
				$pos++;
			}
			$class_name = rs::substr($s, 1, $pos - 1);
			$class_name = $this->getModuleName($class_name);
			$s = rs::substr($s, $pos + 1);
		}
		$pos = 0;
		$sz = rs::strlen($s);
		while ($pos < $sz && mb_substr($s, $pos, 1) != " " && mb_substr($s, $pos, 1) != "." && mb_substr($s, $pos, 1) != ":" && mb_substr($s, $pos, 1) != "["){
			$pos++;
		}
		/* Get component name and postfix */
		$name = "";
		$postfix = "";
		if ($pos == $sz){
			$name = $s;
		}
		else {
			$name = rs::substr($s, 0, $pos);
			$postfix = rs::substr($s, $pos, $sz - $pos);
		}
		$hash = "-" . rtl::toString($this->getCssHash($class_name));
		return rtl::toString($name) . rtl::toString($hash) . rtl::toString($postfix);
	}
	/**
	 * Add OpCode
	 * @return BaseOpCode
	 */
	function readCssAddOpCode($current_op_code, $s, $new_op_code){
		if ($s != ""){
			if ($current_op_code == null){
				$current_op_code = new OpString($s);
			}
			else {
				$current_op_code = new OpConcat($current_op_code, new OpString($s));
			}
		}
		if ($new_op_code != null){
			$current_op_code = new OpConcat($current_op_code, $new_op_code);
		}
		return $current_op_code;
	}
	/**
	 * Read CSS
	 * @return BaseOpCode
	 */
	function readCss(){
		$this->matchNextToken("{");
		$op_code = null;
		$s = "";
		$look_str = $this->current_token->lookString(1);
		$match_str = "}";
		$bracket_level = 0;
		$flag_is_media = false;
		$flag_is_css_body = false;
		/* Main loop */
		while ($look_str != "" && !$this->current_token->isEOF() && ($look_str != $match_str || $look_str == "}" && $bracket_level > 0)){
			$look = $this->current_token->readChar();
			if ($look == "\$"){
				$this->assignCurrentToken($this->current_token);
				$this->pushToken(new ParserBayToken($this->context(), $this));
				if ($this->findNextToken("{")){
					$this->matchNextToken("{");
					$op_code = $this->readCssAddOpCode($op_code, $s, $this->readExpressionElement());
					$s = "";
					$this->matchNextToken("}");
				}
				else {
					$name = $this->readIdentifierName();
					$op_code = $this->readCssAddOpCode($op_code, $s, new OpIdentifier($name));
					$s = "";
				}
				$this->popRestoreToken();
			}
			else if (($look == "." || $look == "%") && !$flag_is_media && !$flag_is_css_body){
				$s = rtl::toString($s) . "." . rtl::toString($this->readCssSelector($look));
			}
			else if ($look != "\t" && $look != "\n" && $look != "\r"){
				$s = rtl::toString($s) . rtl::toString($look);
			}
			if ($look == "@" && $this->current_token->lookString(5) == "media"){
				$flag_is_media = true;
			}
			if ($look == "{"){
				if (!$flag_is_media){
					$flag_is_css_body = true;
				}
				$flag_is_media = false;
				$bracket_level++;
			}
			else if ($look == "}"){
				$flag_is_css_body = false;
				$bracket_level--;
			}
			$look_str = $this->current_token->lookString(1);
		}
		$this->assignCurrentToken($this->current_token);
		$op_code = $this->readCssAddOpCode($op_code, $s, null);
		$this->matchNextToken("}");
		return $op_code;
	}
	/* ============== Read Expression ============== */
	/**
	 * Read element
	 * @return BaseOpCode
	 */
	function readExpressionElement(){
		if ($this->findNextToken("@") && $this->next_token->lookString(3) == "css"){
			$this->matchNextToken("@");
			$this->matchNextToken("css");
			return $this->readCss();
		}
		else if ($this->findNextToken("new")){
			return $this->readNewInstance();
		}
		else if ($this->findNextToken("clone")){
			return $this->readClone();
		}
		else if ($this->findNextToken("class")){
			return $this->readClassName();
		}
		else if ($this->findNextToken("classof")){
			return $this->readClassName();
		}
		else if ($this->findNextToken("method")){
			return $this->readMethod();
		}
		else if ($this->findNextToken("pipe")){
			return $this->readPipe();
		}
		else if ($this->findNextToken("[")){
			return $this->readVector();
		}
		else if ($this->findNextToken("{")){
			return $this->readMap();
		}
		$op_code = $this->readFixed();
		if ($op_code != null){
			return $op_code;
		}
		return $this->readCallDynamic(true, true, true, true);
	}
	/**
	 * Read postfix
	 * @return BaseOpCode
	 */
	function readExpressionPostfix(){
		$op_code = $this->readExpressionElement();
		if ($this->findNextToken("++")){
			$this->matchNextToken("++");
			return new OpPostInc($op_code);
		}
		else if ($this->findNextToken("--")){
			$this->matchNextToken("--");
			return new OpPostDec($op_code);
		}
		return $op_code;
	}
	/**
	 * Read prefix
	 * @return BaseOpCode
	 */
	function readExpressionPrefix(){
		if ($this->findNextToken("++")){
			$this->matchNextToken("++");
			return new OpPreInc($this->readExpressionPostfix());
		}
		else if ($this->findNextToken("--")){
			$this->matchNextToken("--");
			return new OpPreDec($this->readExpressionPostfix());
		}
		return $this->readExpressionPostfix();
	}
	/**
	 * Read bit NOT
	 * @return BaseOpCode
	 */
	function readExpressionBitNot(){
		if ($this->findNextToken("!")){
			$this->matchNextToken("!");
			return new OpBitNot($this->readExpressionPrefix());
		}
		return $this->readExpressionPrefix();
	}
	/**
	 * Read pow
	 * @return BaseOpCode
	 */
	function readExpressionPow(){
		$op_code = $this->readExpressionBitNot();
		while ($this->findNextToken("**")){
			$this->matchNextToken("**");
			$op_code = new OpPow($op_code, $this->readExpressionBitNot());
		}
		return $op_code;
	}
	/**
	 * Read arithmetic multiply and divide
	 * @return BaseOpCode
	 */
	function readExpressionFactor(){
		/* Read first opcode */
		$op_code = $this->readExpressionPow();
		while ($this->findNextToken("*") || $this->findNextToken("/") || $this->findNextToken("%")){
			if ($this->findNextToken("*")){
				$this->matchNextToken("*");
				$op_code = new OpMult($op_code, $this->readExpressionPow());
			}
			else if ($this->findNextToken("/")){
				$this->matchNextToken("/");
				$op_code = new OpDiv($op_code, $this->readExpressionPow());
			}
			else if ($this->findNextToken("%")){
				$this->matchNextToken("%");
				$op_code = new OpMod($op_code, $this->readExpressionPow());
			}
			else {
				throw $this->nextTokenExpected("\"*\", \"/\" or \"%\"");
			}
		}
		return $op_code;
	}
	/**
	 * Read arithmetic expression
	 * @return BaseOpCode
	 */
	function readExpressionArithmetic(){
		/* Read first opcode */
		$op_code = $this->readExpressionFactor();
		while ($this->findNextToken("+") || $this->findNextToken("-")){
			if ($this->findNextToken("+")){
				$this->matchNextToken("+");
				$op_code = new OpAdd($op_code, $this->readExpressionFactor());
			}
			else if ($this->findNextToken("-")){
				$this->matchNextToken("-");
				$op_code = new OpSub($op_code, $this->readExpressionFactor());
			}
			else {
				throw $this->nextTokenExpected("\"+\" or \"-\"");
			}
		}
		return $op_code;
	}
	/**
	 * Read shift
	 * @return BaseOpCode
	 */
	function readExpressionShift(){
		/* Read first opcode */
		$op_code = $this->readExpressionArithmetic();
		while ($this->findNextToken("<<") || $this->findNextToken(">>")){
			if ($this->findNextToken("<<")){
				$this->matchNextToken("<<");
				$op_code = new OpShiftLeft($op_code, $this->readExpressionArithmetic());
			}
			else if ($this->findNextToken(">>")){
				$this->matchNextToken(">>");
				$op_code = new OpShiftRight($op_code, $this->readExpressionArithmetic());
			}
			else {
				throw $this->nextTokenExpected("\"<<\" or \">>\"");
			}
		}
		return $op_code;
	}
	/**
	 * Read concat string
	 * @return BaseOpCode
	 */
	function readExpressionConcat(){
		/* Read first opcode */
		$op_code = $this->readExpressionShift();
		while ($this->findNextToken("~")){
			$this->matchNextToken("~");
			$op_code = new OpConcat($op_code, $this->readExpressionShift());
		}
		return $op_code;
	}
	/**
	 * Read compare
	 * @return BaseOpCode
	 */
	function readExpressionCompare1(){
		/* Read first opcode */
		$op_code = $this->readExpressionConcat();
		while ($this->findNextToken("<") || $this->findNextToken("<=") || $this->findNextToken(">") || $this->findNextToken(">=") || $this->findNextToken("in") || $this->findNextToken("instanceof") || $this->findNextToken("implements")){
			$cond = $this->readNextToken()->token;
			$op_code = new OpCompare($cond, $op_code, $this->readExpressionConcat());
		}
		return $op_code;
	}
	/**
	 * Read compare
	 * @return BaseOpCode
	 */
	function readExpressionCompare2(){
		/* Read first opcode */
		$op_code = $this->readExpressionCompare1();
		while ($this->findNextToken("==") || $this->findNextToken("===") || $this->findNextToken("!=") || $this->findNextToken("!==")){
			$cond = $this->readNextToken()->token;
			$op_code = new OpCompare($cond, $op_code, $this->readExpressionCompare1());
		}
		return $op_code;
	}
	/**
	 * Read bit AND
	 * @return BaseOpCode
	 */
	function readExpressionBitAnd(){
		/* Read first opcode */
		$op_code = $this->readExpressionCompare2();
		while ($this->findNextToken("&")){
			$this->matchNextToken("&");
			$op_code = new OpBitAnd($op_code, $this->readExpressionCompare2());
		}
		return $op_code;
	}
	/**
	 * Read bit XOR
	 * @return BaseOpCode
	 */
	function readExpressionBitXor(){
		/* Read first opcode */
		$op_code = $this->readExpressionBitAnd();
		while ($this->findNextToken("^")){
			$this->matchNextToken("^");
			$op_code = new OpBitXor($op_code, $this->readExpressionBitAnd());
		}
		return $op_code;
	}
	/**
	 * Read bit OR
	 * @return BaseOpCode
	 */
	function readExpressionBitOr(){
		/* Read first opcode */
		$op_code = $this->readExpressionBitXor();
		while ($this->findNextToken("|")){
			$this->matchNextToken("|");
			$op_code = new OpBitOr($op_code, $this->readExpressionBitXor());
		}
		return $op_code;
	}
	/**
	 * Read NOT
	 * @return BaseOpCode
	 */
	function readExpressionNot(){
		if ($this->findNextToken("not")){
			$this->matchNextToken("not");
			return new OpNot($this->readExpressionBitOr());
		}
		return $this->readExpressionBitOr();
	}
	/**
	 * Read AND
	 * @return BaseOpCode
	 */
	function readExpressionAnd(){
		/* Read first opcode */
		$op_code = $this->readExpressionNot();
		while ($this->findNextToken("and")){
			$this->matchNextToken("and");
			$op_code = new OpAnd($op_code, $this->readExpressionNot());
		}
		return $op_code;
	}
	/**
	 * Read OR
	 * @return BaseOpCode
	 */
	function readExpressionOr(){
		/* Read first opcode */
		$op_code = $this->readExpressionAnd();
		while ($this->findNextToken("or")){
			$this->matchNextToken("or");
			$op_code = new OpOr($op_code, $this->readExpressionAnd());
		}
		return $op_code;
	}
	/**
	 * Read ternary operator
	 * @return BaseOpCode
	 */
	function readExpressionTernary(){
		/* Read first opcode */
		$op_code = $this->readExpressionOr();
		if ($this->findNextToken("?")){
			$this->matchNextToken("?");
			$if_true = $this->readExpressionOr();
			$this->matchNextToken(":");
			$if_false = $this->readExpressionOr();
			return new OpTernary($op_code, $if_true, $if_false);
		}
		return $op_code;
	}
	/**
	 * Read expression
	 * @return BaseOpCode
	 */
	function readExpression(){
		if ($this->findNextToken("<")){
			return $this->readHtml();
		}
		$this->pushToken();
		$is_lambda = false;
		if ($this->findNextToken("lambda")){
			$is_lambda = true;
			$this->matchNextToken("lambda");
		}
		else if ($this->findNextToken("pure")){
			$this->matchNextToken("pure");
		}
		$res = null;
		$res = $this->readDeclareFunction(false, false, $is_lambda);
		if ($res != null){
			$this->popToken();
			return $res;
		}
		try{
			$res = $this->readOpCopyStruct();
		}catch(\Exception $_the_exception){
			if ($_the_exception instanceof ParserError){
				$ex = $_the_exception;
				$res = null;
			}
			else { throw $_the_exception; }
		}
		if ($res != null){
			$this->popToken();
			return $res;
		}
		$this->popRollbackToken();
		$old_skip_comments = $this->skip_comments;
		$this->skip_comments = true;
		$res = $this->readExpressionTernary();
		$this->skip_comments = $old_skip_comments;
		return $res;
	}
	/**
	 * Read copy struct
	 * @return BaseOpCode
	 */
	function readOpCopyStruct(){
		$name = $this->readIdentifierName();
		$this->matchNextToken("<=");
		$item = $this->readExpression();
		return new OpCopyStruct((new Map())->set("name", $name)->set("item", $item));
	}
	/* ============== Read Operators ============== */
	/**
	 * Read operator assign
	 * @return BaseOpCode
	 */
	function readOperatorAssign(){
		$op_type = null;
		$op_ident = null;
		$op_ident_name = "";
		$op_exp = null;
		$success = false;
		$v = (new Vector())->push("=")->push("~=")->push("+=")->push("-=")->push("<=");
		/* Read assign */
		$success = false;
		$this->pushToken();
		try{
			$op_ident = $this->readCallDynamic(true, true, true, false);
			if ($this->findNextTokenVector($v) != -1){
				$success = true;
			}
		}catch(\Exception $_the_exception){
			if ($_the_exception instanceof \Exception){
				$ex = $_the_exception;
				if ($ex instanceof ParserError){
					$success = false;
				}
				else {
					throw $ex;
				}
			}
			else { throw $_the_exception; }
		}
		if ($success){
			$pos = $this->findNextTokenVector($v);
			$op_name = $v->item($pos);
			$this->matchNextToken($op_name);
			if ($op_name == "<="){
				$this->popRollbackToken();
				return $this->readOpCopyStruct();
			}
			$this->popToken();
			if ($this->findNextToken("await")){
				$op_exp = $this->readCallAwait();
			}
			else {
				$op_exp = $this->readExpression();
			}
			return new OpAssign($op_ident, $op_exp, $op_name);
		}
		$this->popRollbackToken();
		/* Read declare */
		$this->pushToken();
		try{
			$op_type = $this->readTemplateIdentifier();
			$op_ident_name = $this->readIdentifierName();
			$success = true;
		}catch(\Exception $_the_exception){
			if ($_the_exception instanceof \Exception){
				$ex = $_the_exception;
				if ($ex instanceof ParserError){
					$success = false;
				}
				else {
					throw $ex;
				}
			}
			else { throw $_the_exception; }
		}
		if ($success){
			$this->popToken();
			if ($this->findNextToken("=")){
				$this->matchNextToken("=");
				if ($this->findNextToken("await")){
					$op_exp = $this->readCallAwait();
				}
				else {
					$op_exp = $this->readExpression();
				}
			}
			return new OpAssignDeclare($op_type, $op_ident_name, $op_exp);
		}
		$this->popRollbackToken();
		return null;
	}
	/**
	 * Read operator if
	 * @return BaseOpCode
	 */
	function readOperatorIf(){
		$old_skip_comments = $this->skip_comments;
		$this->skip_comments = true;
		$condition = null;
		$if_true = null;
		$if_false = null;
		$if_else = new Vector();
		/* Read condition */
		$this->matchNextToken("if");
		$this->matchNextToken("(");
		$condition = $this->readExpression();
		$this->matchNextToken(")");
		/* Read if true operators block */
		if ($this->findNextToken("{")){
			$this->matchNextToken("{");
			$if_true = $this->readOperatorsBlock();
			$this->matchNextToken("}");
		}
		else {
			$if_true = new Vector();
			$if_true->push($this->readOperator());
		}
		while ($this->findNextToken("elseif") || $this->findNextToken("else")){
			if ($this->findNextToken("else")){
				$this->matchNextToken("else");
				if ($this->findNextToken("if")){
					$op_if_else = new OpIfElse();
					$this->matchNextToken("if");
					$this->matchNextToken("(");
					$op_if_else->condition = $this->readExpression();
					$this->matchNextToken(")");
					if ($this->findNextToken("{")){
						$this->matchNextToken("{");
						$op_if_else->if_true = $this->readOperatorsBlock();
						$this->matchNextToken("}");
					}
					else {
						$op_if_else->if_true = new Vector();
						$op_if_else->if_true->push($this->readOperator());
					}
					$if_else->push($op_if_else);
				}
				else {
					if ($this->findNextToken("{")){
						$this->matchNextToken("{");
						$if_false = $this->readOperatorsBlock();
						$this->matchNextToken("}");
					}
					else {
						$if_false = new Vector();
						$if_false->push($this->readOperator());
					}
					break;
				}
			}
			else if ($this->findNextToken("elseif")){
				$op_if_else = new OpIfElse();
				$this->matchNextToken("elseif");
				$this->matchNextToken("(");
				$op_if_else->condition = $this->readExpression();
				$this->matchNextToken(")");
				if ($this->findNextToken("{")){
					$this->matchNextToken("{");
					$op_if_else->if_true = $this->readOperatorsBlock();
					$this->matchNextToken("}");
					$if_else->push($op_if_else);
				}
				else {
					$op_if_else->if_true = new Vector();
					$op_if_else->if_true->push($this->readOperator());
				}
			}
		}
		$this->skip_comments = $old_skip_comments;
		return new OpIf($condition, $if_true, $if_false, $if_else);
	}
	/**
	 * Read operator while
	 * @return BaseOpCode
	 */
	function readOperatorWhile(){
		$condition = null;
		$childs = null;
		/* Read condition */
		$this->matchNextToken("while");
		$this->matchNextToken("(");
		$condition = $this->readExpression();
		$this->matchNextToken(")");
		/* Read operators block */
		$this->matchNextToken("{");
		$childs = $this->readOperatorsBlock();
		$this->matchNextToken("}");
		return new OpWhile($condition, $childs);
	}
	/**
	 * Read operator for
	 * @return BaseOpCode
	 */
	function readOperatorFor(){
		$loop_condition = null;
		$loop_init = null;
		$loop_inc = null;
		$childs = null;
		/* Read loop header */
		$this->matchNextToken("for");
		$this->matchNextToken("(");
		$loop_init = $this->readOperatorAssign();
		$this->matchNextToken(";");
		$loop_condition = $this->readExpression();
		$this->matchNextToken(";");
		$loop_inc = $this->readExpression();
		$this->matchNextToken(")");
		/* Read operators block */
		$this->matchNextToken("{");
		$childs = $this->readOperatorsBlock();
		$this->matchNextToken("}");
		return new OpFor($loop_condition, $loop_init, $loop_inc, $childs);
	}
	/**
	 * Read operator try
	 * @return BaseOpCode
	 */
	function readOperatorTry(){
		$op_try = null;
		$op_catch = new Vector();
		/* Read try block */
		$this->matchNextToken("try");
		$this->matchNextToken("{");
		$op_try = $this->readOperatorsBlock();
		$this->matchNextToken("}");
		/* Read catch */
		while ($this->findNextToken("catch")){
			$try_catch_child = new OpTryCatchChilds();
			$this->matchNextToken("catch");
			$this->matchNextToken("(");
			$try_catch_child->op_type = $this->readTemplateIdentifier();
			$try_catch_child->op_ident = $this->readIdentifier();
			$this->matchNextToken(")");
			$this->matchNextToken("{");
			$try_catch_child->childs = $this->readOperatorsBlock();
			$this->matchNextToken("}");
			$op_catch->push($try_catch_child);
		}
		return new OpTryCatch($op_try, $op_catch);
	}
	/**
	 * Read operator return
	 * @return BaseOpCode
	 */
	function readOperatorReturn(){
		$this->matchNextToken("return");
		$value = null;
		if (!$this->findNextToken(";")){
			$value = $this->readExpression();
		}
		$this->matchNextToken(";");
		return new OpReturn($value);
	}
	/**
	 * Read operator throw
	 * @return BaseOpCode
	 */
	function readOperatorThrow(){
		$this->matchNextToken("throw");
		$value = $this->readExpression();
		$this->matchNextToken(";");
		return new OpThrow($value);
	}
	/**
	 * Read operator delete
	 * @return BaseOpCode
	 */
	function readOperatorDelete(){
		$this->matchNextToken("delete");
		$value = $this->readCallDynamic(true, true, false, false);
		$this->matchNextToken(";");
		return new OpDelete($value);
	}
	/**
	 * Read postfix
	 * @return BaseOpCode
	 */
	function readOperatorPostfix(){
		$this->pushToken();
		try{
			$op_code = $this->readExpressionElement();
		}catch(\Exception $_the_exception){
			if ($_the_exception instanceof ParserError){
				$ex = $_the_exception;
				$this->popRollbackToken();
				return null;
			}
			else { throw $_the_exception; }
		}
		if ($this->findNextToken("++")){
			$this->matchNextToken("++");
			$this->popToken();
			return new OpPostInc($op_code);
		}
		else if ($this->findNextToken("--")){
			$this->matchNextToken("--");
			$this->popToken();
			return new OpPostDec($op_code);
		}
		$this->popRollbackToken();
		return null;
	}
	/**
	 * Read prefix
	 * @return BaseOpCode
	 */
	function readOperatorPrefix(){
		if ($this->findNextToken("++")){
			$this->matchNextToken("++");
			return new OpPreInc($this->readExpressionPostfix());
		}
		else if ($this->findNextToken("--")){
			$this->matchNextToken("--");
			return new OpPreDec($this->readExpressionPostfix());
		}
		return null;
	}
	/**
	 * Read operator 
	 * @return BaseOpCode
	 */
	function readOperator(){
		$res = null;
		if ($this->findNextToken(";")){
			$this->matchNextToken(";");
			return null;
		}
		else if ($this->lookNextTokenType() == ParserBayToken::TOKEN_COMMENT){
			return new OpComment($this->readAnyNextToken()->token);
		}
		else if ($this->findNextToken("await")){
			$res = $this->readCallAwait();
			$this->matchNextToken(";");
			return $res;
		}
		else if ($this->findNextToken("if")){
			return $this->readOperatorIf();
		}
		else if ($this->findNextToken("while")){
			return $this->readOperatorWhile();
		}
		else if ($this->findNextToken("for")){
			return $this->readOperatorFor();
		}
		else if ($this->findNextToken("try")){
			return $this->readOperatorTry();
		}
		else if ($this->findNextToken("return")){
			return $this->readOperatorReturn();
		}
		else if ($this->findNextToken("throw")){
			return $this->readOperatorThrow();
		}
		else if ($this->findNextToken("delete")){
			return $this->readOperatorDelete();
		}
		else if ($this->findNextToken("break")){
			$this->matchNextToken("break");
			$this->matchNextToken(";");
			return new OpBreak();
		}
		else if ($this->findNextToken("continue")){
			$this->matchNextToken("continue");
			$this->matchNextToken(";");
			return new OpContinue();
		}
		else if ($this->findNextTokenPreprocessor()){
			return $this->readPreprocessor();
		}
		$res = $this->readOperatorAssign();
		if ($res){
			$this->matchNextToken(";");
			return $res;
		}
		$res = $this->readOperatorPrefix();
		if ($res){
			$this->matchNextToken(";");
			return $res;
		}
		$res = $this->readOperatorPostfix();
		if ($res){
			$this->matchNextToken(";");
			return $res;
		}
		$res = $this->readExpressionElement();
		$this->matchNextToken(";");
		return $res;
		/*return this.readCallDynamic(true, true, true, true);*/
	}
	/**
	 * Read operator block
	 * @return BaseOpCode
	 */
	function readOperatorsBlock(){
		$res = new Vector();
		$match_bracket = false;
		if ($this->findNextToken("{")){
			$this->matchNextToken("{");
			$match_bracket = true;
		}
		$op_code = null;
		while (!$this->findNextToken("}") && !$this->isEOF()){
			$op_code = $this->readOperator();
			if ($op_code != null){
				$res->push($op_code);
			}
		}
		if ($match_bracket){
			$this->matchNextToken("}");
		}
		return $res;
	}
	/* ============== Read Class ============== */
	/**
	 * Read operator namespace
	 * @return BaseOpCode
	 */
	function readOperatorNamespace(){
		$this->matchNextToken("namespace");
		$name = $this->readDynamicName();
		$this->current_namespace = $name;
		$this->matchNextToken(";");
		return new OpNamespace($name);
	}
	/**
	 * Read operator namespace
	 * @return BaseOpCode
	 */
	function readOperatorUse(){
		$this->matchNextToken("use");
		$name = $this->readDynamicName();
		$alias_name = "";
		if ($this->findNextToken("as")){
			$this->matchNextToken("as");
			$alias_name = $this->readIdentifierName();
		}
		$this->matchNextToken(";");
		if ($alias_name != ""){
			$this->modules->set($alias_name, $name);
		}
		else {
			$arr = rs::explode(".", $name);
			$last_name = $arr->pop();
			$this->modules->set($last_name, $name);
		}
		return new OpUse($name, $alias_name);
	}
	/**
	 * Read flags
	 * @return OpFlags
	 */
	function readFlags(){
		$flags = null;
		$flags_vector = OpFlags::getFlags();
		if ($this->findNextTokenVector($flags_vector) != -1){
			$flags = new OpFlags();
			while ($this->findNextTokenVector($flags_vector) != -1){
				if (!$flags->assignFlag($this->lookNextToken())){
					throw $this->parserError("Unknown flag '" . rtl::toString($this->lookNextToken()) . "'");
				}
				$this->readNextToken();
			}
		}
		if ($flags_vector != null){
		}
		return $flags;
	}
	/**
	 * Read annotation
	 */
	function readAnnotation(){
		$this->matchNextToken("@");
		$op_annotation = new OpAnnotation();
		$op_annotation->kind = $this->readTemplateIdentifier();
		$op_annotation->options = $this->readMap();
		if ($this->annotations == null){
			$this->annotations = new Vector();
		}
		$this->annotations->push($op_annotation);
	}
	/**
	 * Read declare class arguments
	 * @return BaseOpCode
	 */
	function readFunctionsArguments(){
		$args = new Vector();
		$this->matchNextToken("(");
		while (!$this->findNextToken(")") && !$this->isEOF()){
			$op_code = $this->readOperatorAssign();
			if ($op_code instanceof OpAssign){
				throw $this->parserError("Assign are not alowed here");
			}
			else if ($op_code instanceof OpAssignDeclare){
				$args->push($op_code);
			}
			if ($this->findNextToken(",")){
				$this->matchNextToken(",");
				continue;
			}
			break;
		}
		$this->matchNextToken(")");
		return $args;
	}
	/**
	 * Read declare class function
	 * @return BaseOpCode
	 */
	function readDeclareFunctionHeader($res){
		$res->args = $this->readFunctionsArguments();
		/* Read use variables*/
		if ($this->findNextToken("use")){
			$this->matchNextToken("use");
			$this->matchNextToken("(");
			while (!$this->findNextToken(")") && !$this->isEOF()){
				$name = $this->readIdentifierName();
				$res->use_variables->push($name);
				if ($this->findNextToken(",")){
					$this->matchNextToken(",");
				}
				else {
					break;
				}
			}
			$this->matchNextToken(")");
		}
		return $res;
	}
	/**
	 * Read declare class function
	 * @return BaseOpCode
	 */
	function readDeclareFunction($read_name = true, $is_declare_function = false, $is_lambda = false){
		$res = new OpFunctionDeclare();
		$this->pushToken();
		$res->is_lambda = $is_lambda;
		try{
			$res->result_type = $this->readTemplateIdentifier();
		}catch(\Exception $_the_exception){
			if ($_the_exception instanceof ParserError){
				$ex = $_the_exception;
				$this->popRollbackToken();
				return null;
			}
			else { throw $_the_exception; }
		}
		if ($read_name){
			$res->name = $this->readIdentifierName();
		}
		if (!$this->findNextToken("(")){
			$this->popRollbackToken();
			return null;
		}
		try{
			$res->args = $this->readFunctionsArguments();
		}catch(\Exception $_the_exception){
			if ($_the_exception instanceof ParserError){
				$ex = $_the_exception;
				$this->popRollbackToken();
				return null;
			}
			else { throw $_the_exception; }
		}
		/* Read use variables*/
		if ($this->findNextToken("use")){
			$this->matchNextToken("use");
			$this->matchNextToken("(");
			while (!$this->findNextToken(")") && !$this->isEOF()){
				$name = $this->readIdentifierName();
				$res->use_variables->push($name);
				if ($this->findNextToken(",")){
					$this->matchNextToken(",");
				}
				else {
					break;
				}
			}
			$this->matchNextToken(")");
		}
		if ($this->findNextToken("=>")){
			$this->matchNextToken("=>");
			if ($this->findNextToken("return")){
				$this->matchNextToken("return");
			}
			$this->popToken();
			if ($is_lambda){
				$this->pushToken();
				try{
					$flags = null;
					$flags = $this->readFlags();
					$res->return_function = $this->readDeclareFunction(false, $is_declare_function, $is_lambda);
					if ($res->return_function != null){
						$res->return_function->flags = $flags;
					}
				}catch(\Exception $_the_exception){
					if ($_the_exception instanceof ParserError){
						$ex = $_the_exception;
						$res->return_function = null;
					}
					else { throw $_the_exception; }
				}
				if ($res->return_function == null){
					$this->popRollbackToken();
				}
				else {
					$this->popToken();
				}
				if ($res->return_function == null){
					$op_item;
					try{
						$op_item = $this->readExpression();
					}catch(\Exception $_the_exception){
						if ($_the_exception instanceof ParserError){
							$ex = $_the_exception;
							$op_item = null;
						}
						else { throw $_the_exception; }
					}
					if ($op_item != null){
						$res->childs = (new Vector())->push($op_item);
					}
				}
				if ($res->return_function == null && $res->childs == null){
					$this->matchNextToken(";");
				}
			}
			else {
				$flags = null;
				$flags = $this->readFlags();
				$res->return_function = $this->readDeclareFunction(false, $is_declare_function, $is_lambda);
				if ($res->return_function != null){
					$res->return_function->flags = $flags;
				}
			}
			return $res;
		}
		else if ($is_declare_function){
			$this->matchNextToken(";");
		}
		else if ($this->findNextToken("{")){
			$this->matchNextToken("{");
			$res->childs = $this->readOperatorsBlock();
			$this->matchNextToken("}");
		}
		else {
			$this->popRollbackToken();
			return null;
		}
		$this->popToken();
		return $res;
	}
	/**
	 * Read class body
	 */
	function readClassBody($res){
		if ($this->findNextToken(";")){
			$this->matchNextToken(";");
			return ;
		}
		$flags = null;
		$op_code = null;
		if ($this->findNextTokenPreprocessor()){
			$op_code = $this->readPreprocessor();
			$res->childs->push($op_code);
			return ;
		}
		else if ($this->lookNextTokenType() == ParserBayToken::TOKEN_COMMENT){
			$op_code = new OpComment($this->readAnyNextToken()->token);
			$res->childs->push($op_code);
			return ;
		}
		$flags = $this->readFlags();
		$this->readClassBodyContent($res, $flags);
	}
	/**
	 * Read class body content
	 */
	function readClassBodyContent($res, $flags){
		$op_code = null;
		$is_declare_function = false;
		if ($flags != null && $flags->p_declare || $this->is_interface){
			$is_declare_function = true;
		}
		if ($this->findNextToken("@")){
			$this->readAnnotation();
			return ;
		}
		if ($flags == null){
			$flags = new OpFlags();
			$flags->assignValue("public", true);
		}
		else {
			if (!$flags->isFlag("protected") && !$flags->isFlag("private")){
				$flags->assignValue("public", true);
			}
		}
		$is_lambda = false;
		if ($flags != null){
			if ($flags->isFlag("lambda")){
				$is_lambda = true;
			}
		}
		$op_code = $this->readDeclareFunction(true, $is_declare_function, $is_lambda);
		if ($op_code && $op_code instanceof OpFunctionDeclare){
			$op_code->annotations = $this->annotations;
			$op_code->flags = $flags;
			if ($op_code->is_lambda){
				$flags->assignValue("static", true);
			}
			if ($op_code->isFlag("pure")){
				$flags->assignValue("static", true);
			}
			$res->childs->push($op_code);
			$this->annotations = null;
			return ;
		}
		$op_code = $this->readOperatorAssign();
		if ($op_code instanceof OpAssign){
			throw $this->parserError("Assign are not alowed here");
		}
		else if ($op_code instanceof OpAssignDeclare){
			$op_code->annotations = $this->annotations;
			$op_code->flags = $flags;
			$res->childs->push($op_code);
			$this->matchNextToken(";");
			$this->annotations = null;
			return ;
		}
		throw $this->parserError("Unknown operator");
	}
	/**
	 * Read class header
	 * @return BaseOpCode
	 */
	function readClassHead($res){
		$res->class_name = $this->readIdentifierName();
		$this->current_class_name = $res->class_name;
		if ($this->findNextToken("<")){
			$this->matchNextToken("<");
			while (true){
				$op_code2 = $this->readCallDynamic(true, false, false, false);
				$res->class_template->push($op_code2);
				if ($this->findNextToken(",")){
					$this->matchNextToken(",");
					continue;
				}
				break;
			}
			$this->matchNextToken(">");
		}
		if ($this->findNextToken("extends")){
			$this->matchNextToken("extends");
			$res->class_extends = $this->readIdentifier();
			if ($this->findNextToken("<")){
				$this->matchNextToken("<");
				while (true){
					$this->readCallDynamic(true, false, false, false);
					if ($this->findNextToken(",")){
						$this->matchNextToken(",");
						continue;
					}
					break;
				}
				$this->matchNextToken(">");
			}
		}
		if ($this->findNextToken("implements")){
			$this->matchNextToken("implements");
			while (!$this->findNextToken("{") && !$this->isEOF()){
				$res->class_implements->push($this->readDynamicName());
				if ($this->findNextToken(",")){
					$this->matchNextToken(",");
					continue;
				}
				break;
			}
		}
		$this->matchNextToken("{");
		while (!$this->findNextToken("}") && !$this->isEOF()){
			$this->readClassBody($res);
		}
		$this->matchNextToken("}");
	}
	/**
	 * Read class
	 * @return BaseOpCode
	 */
	function readDeclareClass($class_flags){
		$res = new OpClassDeclare();
		$this->matchNextToken("class");
		$this->readClassHead($res);
		$res->flags = $class_flags;
		return $res;
	}
	/**
	 * Read interface
	 * @return BaseOpCode
	 */
	function readDeclareInterface($class_flags){
		$res = new OpInterfaceDeclare();
		$this->matchNextToken("interface");
		$this->is_interface = true;
		$this->readClassHead($res);
		$this->is_interface = false;
		$res->flags = $class_flags;
		return $res;
	}
	/**
	 * Read struct
	 * @return BaseOpCode
	 */
	function readDeclareStruct($class_flags){
		$res = new OpStructDeclare();
		$this->matchNextToken("struct");
		$this->readClassHead($res);
		$res->flags = $class_flags;
		return $res;
	}
	/**
	 * Prepocessor
	 */
	function findNextTokenPreprocessor(){
		$token = $this->lookNextToken();
		if ($this->lookNextTokenType() == ParserBayToken::TOKEN_BASE && ($token == "#switch" || $token == "#ifcode")){
			return true;
		}
		return false;
	}
	/**
	 * Read prepocessors block
	 */
	function readPreprocessor(){
		if ($this->findNextToken("#switch")){
			$childs = new Vector();
			$comment;
			$pos;
			$this->matchNextToken("#switch");
			$v = (new Vector())->push("#case")->push("#endswitch");
			while ($this->findNextToken("#case")){
				$this->matchNextToken("#case");
				$this->matchNextToken("ifcode");
				$op_case = new OpPreprocessorCase();
				$op_case->condition = $this->readExpression();
				if (!$this->findNextToken("then")){
					throw $this->nextTokenExpected("then");
				}
				$op_case->value = rs::trim($this->next_token->readUntilVector($v));
				$childs->push($op_case);
				$this->readAnyNextToken();
				$pos = $this->findNextTokenVector($v);
				if ($pos == -1){
					throw $this->parserError("Unknown preprocessor token " . rtl::toString($this->lookNextToken()));
				}
			}
			$this->matchNextToken("#endswitch");
			return new OpPreprocessorSwitch($childs);
		}
		else if ($this->findNextToken("#ifcode")){
			$this->matchNextToken("#endifcode");
		}
		else {
			throw $this->parserError("Unknown preprocessor token " . rtl::toString($this->lookNextToken()));
		}
	}
	/**
	 * Read program
	 * @return BaseOpCode
	 */
	function readProgram(){
		$op_code = null;
		$res = new Vector();
		while (!$this->isEOF()){
			if ($this->lookNextTokenType() == ParserBayToken::TOKEN_COMMENT){
				$res->push(new OpComment($this->readAnyNextToken()->token));
				continue;
			}
			else if ($this->findNextToken("namespace")){
				$res->push($this->readOperatorNamespace());
				continue;
			}
			else if ($this->findNextToken("use")){
				$res->push($this->readOperatorUse());
				continue;
			}
			else if ($this->findNextTokenPreprocessor()){
				$res->push($this->readPreprocessor());
				continue;
			}
			if ($this->findNextToken("@")){
				$this->readAnnotation();
				continue;
			}
			$flags = $this->readFlags();
			$is_class = $this->findNextToken("class");
			$is_interface = $this->findNextToken("interface");
			$is_struct = $this->findNextToken("struct");
			if ($is_class || $is_interface || $is_struct){
				$annotations = $this->annotations;
				$this->annotations = null;
				$op_code = null;
				if ($is_class){
					$op_code = $this->readDeclareClass($flags);
				}
				else if ($is_interface){
					$op_code = $this->readDeclareInterface($flags);
				}
				else if ($is_struct){
					$op_code = $this->readDeclareStruct($flags);
				}
				$op_code->annotations = $annotations;
				$res->push($op_code);
			}
			else {
				throw $this->parserError("Unknown token " . rtl::toString($this->lookNextToken()));
			}
		}
		return $res;
	}
	/**
	 * Reset parser to default settings
	 */
	function resetParser(){
		parent::resetParser();
		$this->modules = new Map();
	}
	/**
	 * Parser function
	 */
	function runParser(){
		$this->_result = new OpNope($this->readProgram());
	}
	/* ======================= Class Init Functions ======================= */
	public function getClassName(){return "BayrellLang.LangBay.ParserBay";}
	public static function getCurrentClassName(){return "BayrellLang.LangBay.ParserBay";}
	public static function getParentClassName(){return "BayrellLang.CommonParser";}
	protected function _init(){
		parent::_init();
	}
}