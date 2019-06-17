<?php
/*!
 *  Bayrell Language
 *
 *  (c) Copyright 2016-2019 "Ildar Bikmamatov" <support@bayrell.org>
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
namespace BayrellLang;
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
		return "BayrellLang";
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
		return (new Map())->set("Runtime", ">=0.2 <1.0")->set("BayrellLang.Parser", ">=0.1 <1.0");
	}
	/**
	 * Compatibility with older versions
	 */
	static function getRequiredModules(){
		return static::requiredModules();
	}
	/**
	 * Returns module files load order
	 * @return Collection<string>
	 */
	static function getModuleFiles(){
		return (new Vector())->push("BayrellLang.CommonParser")->push("BayrellLang.CommonTranslator")->push("BayrellLang.CoreTranslator")->push("BayrellLang.LangConstant")->push("BayrellLang.ModuleDescription")->push("BayrellLang.Utils")->push("BayrellLang.OpCodes.BaseOpCode")->push("BayrellLang.Exceptions.EndOfStringExpected")->push("BayrellLang.Exceptions.HexNumberExpected")->push("BayrellLang.Exceptions.TwiceDeclareElseError")->push("BayrellLang.OpCodes.OpValue1")->push("BayrellLang.OpCodes.OpValue2")->push("BayrellLang.OpCodes.OpValueString")->push("BayrellLang.OpCodes.OpChilds")->push("BayrellLang.LangBay.HtmlToken")->push("BayrellLang.LangBay.ParserBay")->push("BayrellLang.LangBay.ParserBayFactory")->push("BayrellLang.LangBay.ParserBayNameToken")->push("BayrellLang.LangBay.ParserBayToken")->push("BayrellLang.LangBay.TranslatorBay")->push("BayrellLang.LangBay.TranslatorBayFactory")->push("BayrellLang.LangES6.FunctionStack")->push("BayrellLang.LangES6.TranslatorES6")->push("BayrellLang.LangES6.TranslatorES6Factory")->push("BayrellLang.OpCodes.OpAdd")->push("BayrellLang.OpCodes.OpAnd")->push("BayrellLang.OpCodes.OpAnnotation")->push("BayrellLang.OpCodes.OpAssign")->push("BayrellLang.OpCodes.OpAssignDeclare")->push("BayrellLang.OpCodes.OpBitAnd")->push("BayrellLang.OpCodes.OpBitNot")->push("BayrellLang.OpCodes.OpBitOr")->push("BayrellLang.OpCodes.OpBitXor")->push("BayrellLang.OpCodes.OpBreak")->push("BayrellLang.OpCodes.OpCall")->push("BayrellLang.OpCodes.OpCallAwait")->push("BayrellLang.OpCodes.OpClassDeclare")->push("BayrellLang.OpCodes.OpClassName")->push("BayrellLang.OpCodes.OpClone")->push("BayrellLang.OpCodes.OpComponent")->push("BayrellLang.OpCodes.OpComment")->push("BayrellLang.OpCodes.OpCompare")->push("BayrellLang.OpCodes.OpConcat")->push("BayrellLang.OpCodes.OpContinue")->push("BayrellLang.OpCodes.OpCopyStruct")->push("BayrellLang.OpCodes.OpDelete")->push("BayrellLang.OpCodes.OpDiv")->push("BayrellLang.OpCodes.OpDynamic")->push("BayrellLang.OpCodes.OpFlags")->push("BayrellLang.OpCodes.OpFor")->push("BayrellLang.OpCodes.OpFunctionDeclare")->push("BayrellLang.OpCodes.OpHexNumber")->push("BayrellLang.OpCodes.OpHtmlAttribute")->push("BayrellLang.OpCodes.OpHtmlComment")->push("BayrellLang.OpCodes.OpHtmlEscape")->push("BayrellLang.OpCodes.OpHtmlJson")->push("BayrellLang.OpCodes.OpHtmlRaw")->push("BayrellLang.OpCodes.OpHtmlTag")->push("BayrellLang.OpCodes.OpHtmlText")->push("BayrellLang.OpCodes.OpHtmlView")->push("BayrellLang.OpCodes.OpIdentifier")->push("BayrellLang.OpCodes.OpIf")->push("BayrellLang.OpCodes.OpIfElse")->push("BayrellLang.OpCodes.OpMap")->push("BayrellLang.OpCodes.OpMethod")->push("BayrellLang.OpCodes.OpMod")->push("BayrellLang.OpCodes.OpMult")->push("BayrellLang.OpCodes.OpNamespace")->push("BayrellLang.OpCodes.OpNew")->push("BayrellLang.OpCodes.OpNope")->push("BayrellLang.OpCodes.OpNot")->push("BayrellLang.OpCodes.OpNumber")->push("BayrellLang.OpCodes.OpOr")->push("BayrellLang.OpCodes.OpPipe")->push("BayrellLang.OpCodes.OpPostDec")->push("BayrellLang.OpCodes.OpPostInc")->push("BayrellLang.OpCodes.OpPow")->push("BayrellLang.OpCodes.OpPreDec")->push("BayrellLang.OpCodes.OpPreInc")->push("BayrellLang.OpCodes.OpPreprocessorCase")->push("BayrellLang.OpCodes.OpPreprocessorSwitch")->push("BayrellLang.OpCodes.OpReturn")->push("BayrellLang.OpCodes.OpShiftLeft")->push("BayrellLang.OpCodes.OpShiftRight")->push("BayrellLang.OpCodes.OpStatic")->push("BayrellLang.OpCodes.OpString")->push("BayrellLang.OpCodes.OpStringItem")->push("BayrellLang.OpCodes.OpSub")->push("BayrellLang.OpCodes.OpTemplateIdentifier")->push("BayrellLang.OpCodes.OpTernary")->push("BayrellLang.OpCodes.OpThrow")->push("BayrellLang.OpCodes.OpTryCatch")->push("BayrellLang.OpCodes.OpTryCatchChilds")->push("BayrellLang.OpCodes.OpUse")->push("BayrellLang.OpCodes.OpVector")->push("BayrellLang.OpCodes.OpWhile")->push("BayrellLang.OpCodes.OpFunctionArrowDeclare")->push("BayrellLang.OpCodes.OpInterfaceDeclare")->push("BayrellLang.OpCodes.OpStructDeclare")->push("BayrellLang.LangNodeJS.TranslatorNodeJS")->push("BayrellLang.LangNodeJS.TranslatorNodeJSFactory")->push("BayrellLang.LangPHP.TranslatorPHP")->push("BayrellLang.LangPHP.TranslatorPHPFactory");
	}
	/**
	 * Returns enities
	 */
	static function entities(){
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
	public function getClassName(){return "BayrellLang.ModuleDescription";}
	public static function getCurrentNamespace(){return "BayrellLang";}
	public static function getCurrentClassName(){return "BayrellLang.ModuleDescription";}
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