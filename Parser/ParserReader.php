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
use BayrellLang\Parser\Interfaces\ParserInterface;
use BayrellLang\Parser\ParserCursorPos;
class ParserReader extends ParserCursorPos{
	/**
	 * Returns new Instance
	 */
	function createNewInstance(){
		return new ParserReader($this->context(), $this->parser);
	}
	/**
	 * Throws expected error
	 */
	function expected($message){
		if ($message == "\n"){
			throw new ParserExpected("new line", $this->line, $this->col, $this->context());
		}
		else {
			throw new ParserExpected($message, $this->line, $this->col, $this->context());
		}
	}
	/**
	 * Return current char. Throws error if EOF.
	 * @return char
	 */
	function lookChar(){
		if ($this->isEOF()){
			return "";
		}
		return $this->parser->getContentPos($this->pos);
	}
	/**
	 * Look next N chars. Throws error if EOF.
	 * @param int len
	 * @return string
	 */
	function lookString($len){
		if ($this->pos + $len - 1 >= $this->parser->getContentSize()){
			return "";
		}
		return $this->parser->getContentString($this->pos, $len);
	}
	/**
	 * Find next string
	 * @param {string} s
	 * @return {bool} return True if next string is s
	 */
	function findString($s){
		$len = rs::strlen($s);
		if ($this->pos + $len - 1 >= $this->parser->getContentSize()){
			return false;
		}
		$next_s = $this->parser->getContentString($this->pos, $len);
		if ($next_s == $s){
			return true;
		}
		return false;
	}
	/**
	 * Check next string == look_str
	 * @param {string} look_str
	 */
	function match($look_str){
		if ($this->findString($look_str)){
			$this->moveString($look_str);
			return ;
		}
		throw $this->expected($look_str);
	}
	/**
	 * Find string from Vector
	 * @param {Vector<string>} vect
	 * @return {int} vector's index or -1 if not found
	 */
	function findVector($vect){
		for ($i = 0; $i < $vect->count(); $i++){
			$s = $vect->item($i);
			if ($this->findString($s)){
				return $i;
			}
		}
		return -1;
	}
	/**
	 * Read next char. Throws error if EOF.
	 * @param {int} len
	 * @return {string}
	 */
	function readChar(){
		$ch = $this->lookChar();
		$this->moveChar($ch);
		return $ch;
	}
	/**
	 * Read N chars from content
	 * @param {int} len
	 * @return {string}
	 */
	function readString($len){
		$s = $this->lookString($len);
		$this->moveString($s);
		return $s;
	}
	/**
	 * Read string until next string is not equal find_str. Throws error if EOF.
	 * @param {string} find_str - founded string
	 * @return {string} readed string
	 */
	function readUntilString($match_str, $flag_read_last = true){
		$len_match = rs::strlen($match_str);
		if ($len_match == 0){
			return "";
		}
		$s = "";
		$look = "";
		$look_str = $this->lookString($len_match);
		while ($look_str != "" && $look_str != $match_str && !$this->isEOF()){
			$look = $this->readChar();
			$s = rtl::toString($s) . rtl::toString($look);
			$look_str = $this->lookString($len_match);
		}
		if ($flag_read_last){
			if ($look_str == $match_str){
				$s = rtl::toString($s) . rtl::toString($look_str);
				$this->moveString($look_str);
			}
		}
		return $s;
	}
	/**
	 * Skip comments
	 */
	function readUntilVector($v){
		$look = "";
		$res_str = "";
		$pos = $this->findVector($v);
		while ($pos == -1 && !$this->isEOF()){
			$look = $this->readChar();
			$res_str .= $look;
			$pos = $this->findVector($v);
		}
		if ($pos != -1){
			$s = $v->item($pos);
			$sz = rs::strlen($s);
			$look_str = $this->lookString($sz);
			if ($look_str == $s){
				return $res_str;
			}
		}
		throw new ParserEOF($this->context());
	}
	/**
	 * Read string until end of line. Throws error if EOF.
	 * @return {string} readed string
	 */
	function readLine(){
		$s = $this->readUntilString("\n");
		return $s;
	}
	/* ======================= Class Init Functions ======================= */
	public function getClassName(){return "BayrellLang.Parser.ParserReader";}
	public static function getCurrentNamespace(){return "BayrellLang.Parser";}
	public static function getCurrentClassName(){return "BayrellLang.Parser.ParserReader";}
	public static function getParentClassName(){return "BayrellLang.Parser.ParserCursorPos";}
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