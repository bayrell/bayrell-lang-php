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
use Runtime\Interfaces\CloneableInterface;
use Runtime\Interfaces\ContextInterface;
use BayrellLang\Parser\Interfaces\ParserInterface;
class ParserCursorPos extends ContextObject implements CloneableInterface{
	/**
	 * Current content of the file
	 */
	public $parser;
	/**
	 * Current parser pos
	 */
	public $pos;
	/**
	 * Current file line
	 */
	public $line;
	/**
	 * Current file column
	 */
	public $col;
	/**
	 * Returns new Instance
	 */
	function createNewInstance(){
		return new ParserCursorPos($this->context(), $this->parser);
	}
	/**
	 * Assign all data from other object
	 * @param CoreObject obj
	 */
	function assign($obj){
		if ($obj instanceof ParserCursorPos){
			$this->pos = $obj->pos;
			$this->line = $obj->line;
			$this->col = $obj->col;
			$this->parser = $obj->parser;
		}
	}
	/**
	 * Assign all data from other object
	 * @param CoreObject obj
	 */
	function assignObject($obj){
		$this->assign($obj);
	}
	/**
	 * Return true if eof
	 * @param {bool}
	 */
	function isEOF(){
		return $this->pos >= $this->parser->getContentSize();
	}
	/**
	 * Reset cursor
	 */
	function reset(){
		$this->pos = 0;
		$this->line = 1;
		$this->col = 1;
	}
	/**
	 * Constructor
	 */
	function __construct($context = null, $parser = null){
		parent::__construct($context);
		$this->parser = $parser;
	}
	/**
	 * Destructor
	 */
	/*
	void destructor (){
		this.parser = null;
		parent::destructor();
	}
	*/
	/**
	 * Move cursor pos by char
	 * @param char ch
	 * @param int len
	 * @param int invert
	 */
	function moveChar($ch, $len = 1, $invert = 1){
		if ($ch == ""){
			return ;
		}
		if ($ch == "\n"){
			$this->line = $this->line + $invert * $len;
			$this->col = 1;
		}
		else if ($ch == "\t"){
			$this->col = $this->col + $this->parser->tab_space_count * $invert * $len;
		}
		else {
			$this->col = $this->col + $invert * $len;
		}
		$this->pos = $this->pos + $invert * $len;
	}
	/**
	 * Move pos by string
	 * @param {string} s
	 * @param {int} invert
	 */
	function moveString($s, $invert = 1){
		$sz = rs::strlen($s);
		for ($i = 0; $i < $sz; $i++){
			$this->moveChar(mb_substr($s, $i, 1), 1, $invert);
		}
	}
	/* ======================= Class Init Functions ======================= */
	public function getClassName(){return "BayrellLang.Parser.ParserCursorPos";}
	public static function getCurrentNamespace(){return "BayrellLang.Parser";}
	public static function getCurrentClassName(){return "BayrellLang.Parser.ParserCursorPos";}
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