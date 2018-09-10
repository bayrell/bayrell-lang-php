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
namespace BayrellLang\Exceptions;
use Runtime\rtl;
use Runtime\Map;
use Runtime\Vector;
use Runtime\Utils;
use BayrellParser\Exceptions\ParserError;
use BayrellLang\LangConstant;
class HexNumberExpected extends ParserError{
	public function getClassName(){return "BayrellLang.Exceptions.HexNumberExpected";}
	public static function getParentClassName(){return "BayrellParser.Exceptions.ParserError";}
	function __construct($context, $line, $col, $prev = null){
		if ($context == null){
			$context = Utils::globalContext();
		}
		parent::__construct($context, $context->translate("ERROR_PARSER_HEX_NUMBER_EXPECTED"), LangConstant::ERROR_PARSER_HEX_NUMBER_EXPECTED, $prev);
		$this->line = $line;
		$this->pos = $col;
		$this->buildMessage();
	}
}