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
namespace BayrellLang\Parser\Exceptions;
use Runtime\rs;
use Runtime\rtl;
use Runtime\Map;
use Runtime\Vector;
use Runtime\Dict;
use Runtime\Collection;
use Runtime\IntrospectionInfo;
use Runtime\UIStruct;
use Runtime\RuntimeUtils;
use BayrellLang\Parser\Exceptions\ParserLinePosError;
use BayrellLang\Parser\ParserConstant;
class ParserExpected extends ParserLinePosError{
	function __construct($s, $line, $col, $file = "", $context, $prev = null){
		if ($context == null){
			$context = RuntimeUtils::globalContext();
		}
		parent::__construct(rtl::toString($s) . " expected", $line, $col, $file, ParserConstant::ERROR_PARSER_EXPECTED, $context, $prev);
	}
	/* ======================= Class Init Functions ======================= */
	public function getClassName(){return "BayrellLang.Parser.Exceptions.ParserExpected";}
	public static function getCurrentNamespace(){return "BayrellLang.Parser.Exceptions";}
	public static function getCurrentClassName(){return "BayrellLang.Parser.Exceptions.ParserExpected";}
	public static function getParentClassName(){return "BayrellLang.Parser.Exceptions.ParserLinePosError";}
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