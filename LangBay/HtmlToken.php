<?php
/*!
 *  Bayrell Template Engine
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
use BayrellParser\ParserToken;
use BayrellParser\Exceptions\ParserEOF;
use BayrellParser\Exceptions\ParserExpected;
use BayrellLang\Exceptions\EndOfStringExpected;
class HtmlToken extends ParserToken{
	const TOKEN_NONE = "none";
	const TOKEN_BASE = "base";
	const TOKEN_HTML = "html";
	const TOKEN_STRING = "string";
	const TOKEN_COMMENT = "comment";
	protected $_special_tokens;
	/**
	 * Returns new Instance
	 */
	function createNewInstance(){
		return new HtmlToken($this->context(), $this->parser);
	}
	/**
	 * Returns special tokens
	 */
	static function getSpecialTokens(){
		return (new Vector())->push("...")->push("@code{")->push("@json{")->push("@raw{")->push("@{")->push("<!--")->push("-->")->push("<!")->push("</")->push("/>")->push("/*")->push("*/");
	}
	/**
	 * Constructor
	 */
	function __construct($context = null, $parser = null){
		parent::__construct($context, $parser);
		$this->_special_tokens = (new \Runtime\Callback(self::class, "getSpecialTokens"))();
	}
	/**
	 * Return true if char is token char
	 * @param {char} ch
	 * @return {boolean}
	 */
	function isTokenChar($ch){
		return rs::strpos("qazwsxedcrfvtgbyhnujmikolp0123456789_-", rs::strtolower($ch)) !== -1;
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
	 * Get next token without move cursor pos. Throws error if EOF.
	 * @param {BayrellParserToken} token
	 */
	function readNextToken(){
		/* Init next token function */
		$this->readNextTokenInit();
		$this->skipSystemChar();
		$this->initStartPos();
		/* Read comment */
		$look = $this->lookString(2);
		if ($look == "/*"){
			$this->tp = self::TOKEN_COMMENT;
			$this->token = $this->readComment($look);
			$this->success = true;
			return ;
		}
		$pos = $this->findVector($this->_special_tokens);
		if ($pos >= 0){
			$this->tp = self::TOKEN_BASE;
			$this->token = $this->_special_tokens->item($pos);
			$this->success = true;
			$this->readString(rs::strlen($this->token));
			return ;
		}
		$this->readNextTokenBase();
	}
	/* ======================= Class Init Functions ======================= */
	public function getClassName(){return "BayrellLang.LangBay.HtmlToken";}
	public static function getCurrentClassName(){return "BayrellLang.LangBay.HtmlToken";}
	public static function getParentClassName(){return "BayrellParser.ParserToken";}
	protected function _init(){
		parent::_init();
	}
}