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
class ParserBayNameToken extends ParserToken{
	const TOKEN_NONE = "none";
	const TOKEN_BASE = "base";
	/**
	 * Return true if char is token char
	 * @param {char} ch
	 * @return {boolean}
	 */
	function isTokenChar($ch){
		return rs::strpos("qazwsxedcrfvtgbyhnujmikolp0123456789_.", rs::strtolower($ch)) !== -1;
	}
	/* ======================= Class Init Functions ======================= */
	public function getClassName(){return "BayrellLang.LangBay.ParserBayNameToken";}
	public static function getParentClassName(){return "BayrellParser.ParserToken";}
}