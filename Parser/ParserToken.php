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
use BayrellLang\Parser\Exceptions\ParserEOF;
use BayrellLang\Parser\Exceptions\ParserExpected;
use BayrellLang\Parser\ParserReader;
class ParserToken extends ParserReader{
	const TOKEN_NONE = "none";
	const TOKEN_BASE = "base";
	/**
	 * Token content
	 */
	public $token;
	/**
	 * Token type
	 */
	public $tp;
	/**
	 * Start pos of the current token
	 */
	public $start_pos;
	/**
	 * Start current token line
	 */
	public $start_line;
	/**
	 * Start current token column
	 */
	public $start_col;
	/**
	 * The token is success readed
	 */
	public $success;
	/**
	 * Returns new Instance
	 */
	function createNewInstance(){
		return new ParserToken($this->context(), $this->parser);
	}
	/**
	 * Assign all data from other object
	 * @param CoreObject obj
	 */
	function assign($obj){
		if ($obj instanceof ParserToken){
			$this->tp = $obj->tp;
			$this->token = $obj->token;
			$this->success = $obj->success;
			$this->start_line = $obj->start_line;
			$this->start_col = $obj->start_col;
		}
		parent::assign($obj);
	}
	/**
	 * Assign all data from other object
	 * @param CoreObject obj
	 */
	function assignObject($obj){
		$this->assign($obj);
	}
	/**
	 * Reset cursor
	 */
	function reset(){
		parent::reset();
		$this->token = "";
		$this->tp = "";
		$this->success = false;
	}
	/**
	 * Return true if char is token char
	 * @param {char} ch
	 * @return {boolean}
	 */
	function isTokenChar($ch){
		return rs::strpos("qazwsxedcrfvtgbyhnujmikolp0123456789_", rs::strtolower($ch)) !== -1;
	}
	/**
	 * Return true if char is system or space. ASCII code <= 32.
	 * @param char ch
	 * @return boolean
	 */
	function isSkipChar($ch){
		if (rs::ord($ch) <= 32){
			return true;
		}
		return false;
	}
	/**
	 * Skip system char. Throws error if EOF.
	 */
	function skipSystemChar(){
		$look = $this->lookChar();
		while ($this->isSkipChar($look) && !$this->isEOF()){
			$this->moveChar($look);
			$look = $this->lookChar();
			if ($look == ""){
				break;
			}
		}
	}
	/**
	 * Assign new value of the start position
	 */
	function initStartPos(){
		$this->start_pos = $this->pos;
		$this->start_line = $this->line;
		$this->start_col = $this->col;
	}
	/**
	 * Init read next token
	 */
	function readNextTokenInit(){
		$this->tp = self::TOKEN_NONE;
		$this->token = "";
		$this->success = false;
		$this->start_line = $this->line;
		$this->start_col = $this->col;
		$this->start_pos = $this->pos;
		if ($this->isEOF()){
			throw new ParserEOF($this->context());
		}
	}
	/**
	 * Read base next token
	 */
	function readNextTokenBase(){
		$look = $this->lookChar();
		$this->tp = self::TOKEN_BASE;
		$this->success = true;
		$this->token = $look;
		$this->moveChar($look);
		if ($this->isTokenChar($look)){
			try{
				$look = $this->lookChar();
				while ($this->isTokenChar($look) && !$this->isEOF()){
					$this->token = rtl::toString($this->token) . rtl::toString($look);
					$this->moveChar($look);
					$look = $this->lookChar();
				}
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
		}
	}
	/**
	 * Get next token without move cursor pos. Throws error if EOF.
	 * @param {BayrellLang.ParserToken} token
	 */
	function readNextToken(){
		/* Init next token function */
		$this->readNextTokenInit();
		$this->skipSystemChar();
		$this->initStartPos();
		/* Read base token */
		$this->readNextTokenBase();
	}
	/* ======================= Class Init Functions ======================= */
	public function getClassName(){return "BayrellLang.Parser.ParserToken";}
	public static function getCurrentNamespace(){return "BayrellLang.Parser";}
	public static function getCurrentClassName(){return "BayrellLang.Parser.ParserToken";}
	public static function getParentClassName(){return "BayrellLang.Parser.ParserReader";}
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