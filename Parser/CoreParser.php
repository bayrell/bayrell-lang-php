<?php
/*!
 *  Bayrell Parser Library.  
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
namespace BayrellLang\Parser;
use Runtime\rs;
use Runtime\rtl;
use Runtime\Map;
use Runtime\Vector;
use Runtime\Dict;
use Runtime\Collection;
use Runtime\IntrospectionInfo;
use Runtime\UIStruct;
use Runtime\re;
use Runtime\ContextObject;
use Runtime\Exceptions\RuntimeException;
use BayrellLang\Parser\Exceptions\ParserEOF;
use BayrellLang\Parser\Exceptions\ParserError;
use BayrellLang\Parser\Exceptions\ParserExpected;
use BayrellLang\Parser\Exceptions\ParserLinePosError;
use BayrellLang\Parser\Interfaces\ParserInterface;
use BayrellLang\Parser\ParserCursorPos;
use BayrellLang\Parser\ParserToken;
use BayrellLang\Parser\TokenPair;
class CoreParser extends ContextObject implements ParserInterface{
	/**
	 * Parser result
	 */
	public $the_result;
	/**
	 * Tab space count
	 */
	public $tab_space_count;
	/**
	 * Current content of the file
	 */
	protected $_content;
	/**
	 * Size of the content
	 */
	protected $_content_size;
	/**
	 * Current file name
	 */
	protected $_file_name;
	/**
	 * Current token
	 */
	public $current_token;
	/**
	 * Next token
	 */
	public $next_token;
	/**
	 * Token stack
	 */
	protected $_token_stack;
	/**
	 * Constructor
	 */
	function __construct($context = null){
		parent::__construct($context);
		$this->current_token = $this->createToken();
		$this->next_token = $this->createToken();
		$this->_token_stack = new Vector();
	}
	/**
	 * Destructor
	 */
	function __destruct(){
	}
	/**
	 * Get tab size
	 * @return {int}
	 */
	function getTabSize(){
		return $this->tab_space_count;
	}
	/**
	 * Set content of the file
	 * @param {string} content - content
	 */
	function setContent($content){
		$this->_content = $content;
		$this->_content_size = rs::strlen($this->_content);
	}
	/**
	 * Get content of the file
	 * @return {string}
	 */
	function getContent(){
		return $this->_content;
	}
	/**
	 * Get char of the content
	 * @return {char}
	 */
	function getContentPos($pos){
		return mb_substr($this->_content, $pos, 1);
	}
	/**
	 * Get content size
	 * @return {int}
	 */
	function getContentSize(){
		return $this->_content_size;
	}
	/**
	 * Get content string
	 * @return {int} pos
	 * @return {int} len 
	 * @return {string}
	 */
	function getContentString($pos, $len){
		return rs::substr($this->_content, $pos, $len);
	}
	/**
	 * Set new file name
	 * @param {string} file_name
	 */
	function setFileName($file_name){
		$this->_file_name = $file_name;
	}
	/**
	 * Get file name
	 * @return {string}
	 */
	function getFileName($file_name){
		return $this->_file_name;
	}
	/**
	 * Read token
	 * @param {BayrellLang.ParserToken} token
	 */
	function readAnyNextToken(){
		$this->current_token->assign($this->next_token);
		try{
			$this->next_token->readNextToken();
		}catch(\Exception $_the_exception){
			if ($_the_exception instanceof \Exception){
				$e = $_the_exception;
				if ($e instanceof ParserEOF){
				}
				else {
					throw $e;
				}
			}
			else { throw $_the_exception; }
		}
		return $this->current_token;
	}
	/**
	 * Read token
	 * @param {BayrellLang.ParserToken} token
	 */
	function readNextToken($tp = "base", $error_message = "Base token"){
		if ($this->lookNextTokenType() != $tp){
			throw $this->nextTokenExpected($error_message);
		}
		return $this->readAnyNextToken();
	}
	/**
	 * Return true if EOF
	 * @param boolean
	 */
	function isEOF(){
		return $this->next_token->isEOF();
	}
	/**
	 * Throws expected error
	 */
	function nextTokenExpected($message){
		$start_line = $this->next_token->start_line;
		$start_col = $this->next_token->start_col;
		if ($message == "\n"){
			return new ParserExpected("new line", $start_line, $start_col, "", $this->context());
		}
		else {
			return new ParserExpected($message, $start_line, $start_col, "", $this->context());
		}
	}
	/**
	 * Throws expected error
	 */
	function parserExpected($message){
		$start_line = $this->next_token->start_line;
		$start_col = $this->next_token->start_col;
		return new ParserExpected($message, $start_line, $start_col, "", $this->context());
	}
	/**
	 * Throws expected error
	 */
	function parserError($message){
		$start_line = $this->next_token->start_line;
		$start_col = $this->next_token->start_col;
		return new ParserLinePosError($message, $start_line, $start_col, "", -1, $this->context());
	}
	/**
	 * Return next token
	 * @return {string} - Content of the next token
	 */
	function lookNextToken(){
		return $this->next_token->token;
	}
	/**
	 * Return next token type
	 * @return {string} - Token type
	 */
	function lookNextTokenType(){
		return $this->next_token->tp;
	}
	/**
	 * Find next token
	 * @param {string} token - Content of next token
	 * @param {string} tp - Type of the token
	 * @return {bool} - Return true if next token is equal token and tp
	 */
	function findNextToken($token, $tp = "base"){
		return $this->next_token->token == $token && $this->next_token->tp == $tp;
	}
	/**
	 * Find next token
	 * @param {Vector<string>} tokens - Vector of next token
	 * @param {string} tp - Type of the token
	 * @return int - Return -1 if token does not match
	 */
	function findNextTokenVector($tokens, $tp = "base"){
		if ($this->next_token->tp != $tp){
			return -1;
		}
		return $tokens->indexOf($this->next_token->token);
	}
	/**
	 * Find next string
	 * @param {string} str - Find string
	 * @return {bool} - Return true if string found
	 */
	function findNextString($str){
		return $this->current_token->findString($str);
	}
	/**
	 * Read string until next string is not equal find_str. Throws error if EOF.
	 * @param {string} str - Find string
	 * @return {bool} - Return true if string found
	 */
	function readUntilString($str, $flag_read_last = true){
		$s = $this->current_token->readUntilString($str, $flag_read_last);
		$this->assignCurrentToken($this->current_token);
		return $s;
	}
	/**
	 * Check next string == look_str
	 * @param {string} look_str
	 */
	function matchNextToken($look_str, $tp = "base"){
		if ($this->findNextToken($look_str, $tp)){
			$this->readAnyNextToken();
			return ;
		}
		throw $this->nextTokenExpected($look_str);
	}
	/**
	 * Check next string == look_str
	 * @param {string} look_str
	 */
	function matchNextString($look_str){
		if ($this->current_token->findString($look_str)){
			$s = $this->current_token->moveString($look_str);
			$this->assignCurrentToken($this->current_token);
			return ;
		}
		throw $this->nextTokenExpected($look_str);
	}
	/**
	 * Tokens Fabric
	 * @return BayrellLang.ParserToken
	 */
	function createToken(){
		return new ParserToken($this->context(), $this);
	}
	/**
	 * Push token
	 */
	function pushToken($new_token = null){
		$res = new TokenPair($this->current_token, $this->next_token);
		$this->_token_stack->push($res);
		if ($new_token == null){
			$this->current_token = rtl::_clone($this->current_token);
			$this->next_token = rtl::_clone($this->next_token);
		}
		else {
			$this->current_token = rtl::_clone($this->current_token);
			$new_token->assign($this->current_token);
			$new_token->readNextToken();
			$this->next_token = $new_token;
		}
	}
	/**
	 * Pop token
	 */
	function popToken(){
		return $this->_token_stack->pop();
	}
	/**
	 * Pop token and assign
	 */
	function popRollbackToken(){
		$res = $this->popToken();
		$this->current_token = $res->current_token;
		$this->next_token = $res->next_token;
	}
	/**
	 * Assign current token
	 */
	function assignCurrentToken($new_token = null){
		$this->current_token->assign($new_token);
		$this->next_token->assign($new_token);
		$this->next_token->readNextToken();
	}
	/**
	 * Assign next token
	 */
	function assignNextToken($new_token = null){
		$this->next_token->assign($new_token);
	}
	/**
	 * Pop and assign next token
	 */
	function popRestoreToken(){
		$old_current_token = $this->current_token;
		$old_next_token = $this->next_token;
		$res = $this->popToken();
		$this->current_token = $res->current_token;
		$this->next_token = $res->next_token;
		$this->current_token->assign($old_current_token);
		$this->next_token->assign($old_current_token);
		$this->next_token->readNextToken();
	}
	/**
	 * Reset parser to default settings
	 */
	function resetParser(){
		$this->the_result = null;
		$this->current_token->reset();
		$this->next_token->reset();
		$this->readAnyNextToken();
	}
	/**
	 * Parser function
	 */
	function runParser(){
	}
	/**
	 * Parse content
	 */
	function parseContent(){
		$this->resetParser();
		$this->runParser();
	}
	/**
	 * Parse string
	 * @param {string} s
	 */
	function parseString($s){
		$this->setContent($s);
		$this->parseContent();
	}
	/* ======================= Class Init Functions ======================= */
	public function getClassName(){return "BayrellLang.Parser.CoreParser";}
	public static function getCurrentNamespace(){return "BayrellLang.Parser";}
	public static function getCurrentClassName(){return "BayrellLang.Parser.CoreParser";}
	public static function getParentClassName(){return "Runtime.ContextObject";}
	protected function _init(){
		parent::_init();
	}
	public static function getFieldsList($names, $flag=0){
	}
	public static function getFieldInfoByName($field_name){
		return null;
	}
	public static function getMethodsList($names){
	}
	public static function getMethodInfoByName($method_name){
		return null;
	}
}