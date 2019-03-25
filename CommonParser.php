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
namespace BayrellLang;
use Runtime\rs;
use Runtime\rtl;
use Runtime\Map;
use Runtime\Vector;
use Runtime\Dict;
use Runtime\Collection;
use Runtime\IntrospectionInfo;
use Runtime\UIStruct;
use Runtime\rs;
use BayrellParser\ParserToken;
use BayrellParser\CoreParser;
use BayrellLang\Exceptions\HexNumberExpected;
use BayrellLang\OpCodes\BaseOpCode;
class CommonParser extends CoreParser{
	protected $_result;
	public $skip_comments;
	/**
	 * Return true if char is alfa symbol
	 * @param char ch
	 * @return boolean
	 */
	function isLetterChar($ch){
		return rs::strpos("qazwsxedcrfvtgbyhnujmikolp", rs::strtolower($ch)) !== -1;
	}
	/**
	 * Return true if char is number
	 * @param char ch
	 * @return boolean
	 */
	function isNumChar($ch){
		return rs::strpos("0123456789", $ch) !== -1;
	}
	/**
	 * Return true if char is number
	 * @param char ch
	 * @return boolean
	 */
	function isHexChar($ch){
		return rs::strpos("0123456789abcdef", rs::strtolower($ch)) !== -1;
	}
	/**
	 * Return true if string is alfa string
	 * @param string ch
	 * @return boolean
	 */
	function isLetterString($s){
		$sz = rs::strlen($s);
		for ($i = 0; $i < $sz; $i++){
			if (!$this->isLetterChar(mb_substr($s, $i, 1))){
				return false;
			}
		}
		return true;
	}
	/**
	 * Return true if string is number
	 * @param string ch
	 * @return boolean
	 */
	function isNumString($s){
		$sz = rs::strlen($s);
		for ($i = 0; $i < $sz; $i++){
			if (!$this->isNumChar(mb_substr($s, $i, 1))){
				return false;
			}
		}
		return true;
	}
	/**
	 * Return true if string is number
	 * @param string ch
	 * @return boolean
	 */
	function isHexStringBegin($s){
		$sz = rs::strlen($s);
		if ($sz < 2){
			return false;
		}
		if (mb_substr($s, 0, 1) == "0" && (mb_substr($s, 1, 1) == "x" || mb_substr($s, 1, 1) == "X")){
			return true;
		}
		return false;
	}
	/**
	 * Return true if string is number
	 * @param string ch
	 * @return boolean
	 */
	function isHexString($s){
		$sz = rs::strlen($s);
		if ($sz < 2){
			return false;
		}
		if (mb_substr($s, 0, 1) == "0" && (mb_substr($s, 1, 1) == "x" || mb_substr($s, 1, 1) == "X")){
			for ($i = 2; $i < $sz; $i++){
				if (!$this->isHexChar(mb_substr($s, $i, 1))){
					return false;
				}
			}
			return true;
		}
		return false;
	}
	/**
	 * Return true if string is alfa string
	 * @param string ch
	 * @return boolean
	 */
	function isSymbolOrNumString($s){
		$sz = rs::strlen($s);
		for ($i = 0; $i < $sz; $i++){
			if (!$this->isAlphaChar(mb_substr($s, $i, 1)) && !$this->isNumChar(mb_substr($s, $i, 1))){
				return false;
			}
		}
		return true;
	}
	/**
	 * Return if next token is number
	 * @return boolean
	 */
	function isNextTokenNumber(){
		return $this->isNumString($this->next_token->token) && $this->next_token->tp == ParserToken::TOKEN_BASE;
	}
	/**
	 * Return if next token is number
	 * @return boolean
	 */
	function isNextTokenHexNumber(){
		return $this->isHexString($this->next_token->token) && $this->next_token->tp == ParserToken::TOKEN_BASE;
	}
	/**
	 * Return if next token is alfa string
	 * @return boolean
	 */
	function isNextTokenLetters(){
		return $this->isLetterString($this->next_token->token) && $this->next_token->tp == ParserToken::TOKEN_BASE;
	}
	/**
	 * Check next string is number
	 * @return {string} number
	 */
	function matchDouble(){
		$sign = "";
		if ($this->findNextToken("+")){
			$this->matchNextToken("+");
		}
		else if ($this->findNextToken("-")){
			$this->matchNextToken("-");
			$sign = "-";
		}
		if (!$this->isNextTokenNumber()){
			throw $this->nextTokenExpected("number");
		}
		$value = $this->readNextToken()->token;
		if ($this->findNextToken(".")){
			$this->matchNextToken(".");
			if (!$this->isNextTokenNumber()){
				throw $this->nextTokenExpected("double");
			}
			$value .= "." . rtl::toString($this->readNextToken()->token);
		}
		if ($sign == "-"){
			return "-" . rtl::toString($value);
		}
		return $value;
	}
	/**
	 * Check next string is number
	 * @return {string} number
	 */
	function matchHexNumber(){
		$sign = "";
		if ($this->findNextToken("+")){
			$this->matchNextToken("+");
		}
		else if ($this->findNextToken("-")){
			$this->matchNextToken("-");
			$sign = "-";
		}
		if (!$this->isNextTokenHexNumber()){
			if ($this->lookNextTokenType() == ParserToken::TOKEN_BASE && $this->isHexStringBegin($this->lookNextToken())){
				$start_line = $this->next_token->start_line;
				$start_col = $this->next_token->start_col;
				throw new HexNumberExpected($start_line, $start_col, $this->context());
			}
			else {
				throw $this->nextTokenExpected($this->translate("ERROR_PARSER_HEX_NUMBER_EXPECTED"));
			}
		}
		return rtl::toString($sign) . rtl::toString($this->readNextToken()->token);
	}
	/**
	 * Returns abstract syntax tree
	 */
	function getAST(){
		return $this->_result;
	}
	/**
	 * Parser function
	 */
	function runParser(){
		$this->_result = null;
	}
	/* ======================= Class Init Functions ======================= */
	public function getClassName(){return "BayrellLang.CommonParser";}
	public static function getCurrentClassName(){return "BayrellLang.CommonParser";}
	public static function getParentClassName(){return "BayrellParser.CoreParser";}
	protected function _init(){
		parent::_init();
	}
}