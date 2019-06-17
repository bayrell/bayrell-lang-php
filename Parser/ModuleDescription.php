<?php
/*!
 *  Bayrell Json Serializer
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
use Runtime\Interfaces\ContextInterface;
use Runtime\Interfaces\ModuleDescriptionInterface;
class ModuleDescription implements ModuleDescriptionInterface{
	/**
	 * Returns module name
	 * @return string
	 */
	static function getModuleName(){
		return "BayrellLang.Parser";
	}
	/**
	 * Returns module name
	 * @return string
	 */
	static function getModuleVersion(){
		return "0.7.3";
	}
	/**
	 * Returns required modules
	 * @return Map<string>
	 */
	static function requiredModules(){
		return (new Map())->set("Runtime", ">=0.2 <1.0");
	}
	/**
	 * Returns module files load order
	 * @return Collection<string>
	 */
	static function getModuleFiles(){
		return (new Vector())->push("BayrellLang.Parser.ModuleDescription")->push("BayrellLang.Parser.ParserConstant")->push("BayrellLang.Parser.ParserCursorPos")->push("BayrellLang.Parser.Exceptions.ParserError")->push("BayrellLang.Parser.Interfaces.ParserInterface")->push("BayrellLang.Parser.CoreParser")->push("BayrellLang.Parser.ParserReader")->push("BayrellLang.Parser.Exceptions.ParserEOF")->push("BayrellLang.Parser.Exceptions.ParserLinePosError")->push("BayrellLang.Parser.ParserToken")->push("BayrellLang.Parser.TokenPair")->push("BayrellLang.Parser.Exceptions.ParserExpected");
	}
	/**
	 * Returns enities
	 */
	static function entities(){
		return null;
	}
	/**
	 * Register lambda filters
	 */
	static function lambdaFilters(){
		return null;
	}
	/**
	 * Called then module registed in context
	 * @param ContextInterface context
	 */
	static function onRegister($context){
	}
	/**
	 * Called then context read config
	 * @param Map<mixed> config
	 */
	static function onReadConfig($context, $config){
	}
	/**
	 * Init context
	 * @param ContextInterface context
	 */
	static function onInitContext($context){
	}
	/* ======================= Class Init Functions ======================= */
	public function getClassName(){return "BayrellLang.Parser.ModuleDescription";}
	public static function getCurrentNamespace(){return "BayrellLang.Parser";}
	public static function getCurrentClassName(){return "BayrellLang.Parser.ModuleDescription";}
	public static function getParentClassName(){return "";}
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