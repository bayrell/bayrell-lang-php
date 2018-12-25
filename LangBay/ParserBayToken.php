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
use Runtime\rtl;
use Runtime\Map;
use Runtime\Vector;
use Runtime\IntrospectionInfo;
use Runtime\rs;
use BayrellParser\ParserToken;
use BayrellParser\Exceptions\ParserEOF;
use BayrellParser\Exceptions\ParserExpected;
use BayrellLang\Exceptions\EndOfStringExpected;
class ParserBayToken extends ParserToken{
	const TOKEN_NONE = "none";
	const TOKEN_BASE = "base";
	const TOKEN_STRING = "string";
	const TOKEN_COMMENT = "comment";
	protected $_special_tokens;
	/**
	 * Current content of the file
	 */
	public $parser;
	/**
	 * Returns new Instance
	 */
	function createNewInstance(){
		return new ParserBayToken($this->context(), $this->parser);
	}
	/**
	 * Constructor
	 */
	function __construct($context = null, $parser = null){
		parent::__construct($context, $parser);
		$this->_special_tokens = (new Vector())->push("!==")->push("===")->push("!=")->push("==")->push("<=")->push(">=")->push("=>")->push("::")->push("++")->push("--")->push("+=")->push("-=")->push("~=")->push("!=")->push("**")->push("<<")->push(">>")->push("#ifcode")->push("#switch")->push("#case")->push("#endswitch")->push("#endif");
	}
	/**
	 * Read next token as string
	 */
	function readTokenString(){
		/*
		\[0-7]{1,3}	- последовательность символов, соответствующая регулярному выражению символа в восьмеричной системе счисления, который молча переполняется, чтобы поместиться в байт (т.е. "\400" === "\000")
		\x[0-9A-Fa-f]{1,2} - последовательность символов, соответствующая регулярному выражению символа в шестнадцатеричной системе счисления
		\u{[0-9A-Fa-f]+} - последовательность символов, соответствующая регулярному выражению символа Unicode, которая отображается в строка в представлении UTF-8 (добавлено в PHP 7.0.0)
		*/
		$arr1 = (new Vector())->push("n")->push("r")->push("t")->push("\\")->push("'")->push("\"");
		$start_line = $this->start_line;
		$start_col = $this->start_col;
		$match_char = $this->lookChar();
		$this->moveChar($match_char);
		$res_str = "";
		$look = $this->lookChar();
		while ($look != $match_char && !$this->isEOF()){
			if ($look == "\\"){
				$this->moveChar($look);
				$look2 = $this->lookChar();
				if ($arr1->indexOf($look2) != -1){
					if ($look2 == "n"){
						$res_str .= "\n";
					}
					else if ($look2 == "r"){
						$res_str .= "\r";
					}
					else if ($look2 == "t"){
						$res_str .= "\t";
					}
					else if ($look2 == "\\"){
						$res_str .= "\\";
					}
					else if ($look2 == "\""){
						$res_str .= "\"";
					}
					else if ($look2 == "'"){
						$res_str .= "'";
					}
				}
				else {
					$res_str .= $look2;
				}
				$this->moveChar($look2);
			}
			else {
				$res_str .= $look;
				$this->moveChar($look);
			}
			$look = $this->lookChar();
		}
		if ($this->lookChar() == $match_char){
			$this->moveChar($match_char);
		}
		else {
			throw new EndOfStringExpected($start_line, $start_col, $this->context());
		}
		return $res_str;
	}
	/**
	 * Read comments
	 */
	function readComment($open_tag){
		$res = "";
		$ch = "";
		$look = "";
		$this->moveString($open_tag);
		while (!$this->isEOF()){
			$ch = $this->lookChar();
			$look = $this->lookString(2);
			if ($look == "*/"){
				break;
			}
			$res .= $ch;
			$this->moveChar($ch);
		}
		if ($look == "*/"){
			$this->moveString($look);
		}
		else {
			throw new ParserEOF($this->context(), $start_line, $start_col);
		}
		return $res;
	}
	/**
	 * Skip comments
	 */
	function skipComments(){
		$look = $this->lookString(2);
		while ($look == "/*" && !$this->isEOF()){
			/* */
			$this->readComment($look);
			$this->skipSystemChar();
			$look = $this->lookString(2);
		}
	}
	/**
	 * Get next token without move cursor pos. Throws error if EOF.
	 * @param {BayrellParserToken} token
	 */
	function readNextToken(){
		$look = "";
		/* Init next token function */
		$this->readNextTokenInit();
		$this->skipSystemChar();
		if ($this->parser->skip_comments){
			$this->skipComments();
		}
		$this->initStartPos();
		/* Try to read special tokens */
		$pos = $this->findVector($this->_special_tokens);
		if ($pos >= 0){
			$this->tp = static::TOKEN_BASE;
			$this->token = $this->_special_tokens->item($pos);
			$this->success = true;
			$this->readString(rs::strlen($this->token));
			return ;
		}
		$look = $this->lookChar();
		if ($look == "'" || $look == "\""){
			$this->tp = static::TOKEN_STRING;
			$this->token = $this->readTokenString();
			$this->success = true;
			return ;
		}
		$look = $this->lookString(2);
		if ($look == "/*"){
			$this->tp = static::TOKEN_COMMENT;
			$this->token = $this->readComment($look);
			$this->success = true;
			return ;
		}
		$this->readNextTokenBase();
	}
	/* ======================= Class Init Functions ======================= */
	public function getClassName(){return "BayrellLang.LangBay.ParserBayToken";}
	public static function getParentClassName(){return "BayrellParser.ParserToken";}
	protected function _init(){
		parent::_init();
		$this->_special_tokens = null;
		$this->parser = null;
	}
}