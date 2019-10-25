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
namespace Bayrell\Lang;
class ModuleDescription implements \Runtime\Interfaces\ModuleDescriptionInterface, \Runtime\Interfaces\AssetsInterface
{
	/**
	 * Returns module name
	 * @return string
	 */
	static function getModuleName($__ctx)
	{
		return "Bayrell.Lang";
	}
	/**
	 * Returns module name
	 * @return string
	 */
	static function getModuleVersion($__ctx)
	{
		return "0.8.0-alpha.9";
	}
	/**
	 * Returns required modules
	 * @return Map<string>
	 */
	static function requiredModules($__ctx)
	{
		return \Runtime\Dict::from(["Runtime"=>">=0.2 <1.0"]);
	}
	/**
	 * Returns module files load order
	 * @return Collection<string>
	 */
	static function assets($__ctx)
	{
		return \Runtime\Collection::from(["Bayrell.Lang/Caret","Bayrell.Lang/CoreParser","Bayrell.Lang/CoreToken","Bayrell.Lang/CoreTranslator","Bayrell.Lang/LangConstant","Bayrell.Lang/LangUtils","Bayrell.Lang/SaveOpCode","Bayrell.Lang/ModuleDescription","Bayrell.Lang/Exceptions/ParserUnknownError","Bayrell.Lang/Exceptions/ParserError","Bayrell.Lang/Exceptions/ParserEOF","Bayrell.Lang/Exceptions/ParserExpected","Bayrell.Lang/LangBay/ParserBay","Bayrell.Lang/LangBay/ParserBayBase","Bayrell.Lang/LangBay/ParserBayExpression","Bayrell.Lang/LangBay/ParserBayOperator","Bayrell.Lang/LangBay/ParserBayPreprocessor","Bayrell.Lang/LangBay/ParserBayProgram","Bayrell.Lang/LangES6/TranslatorES6","Bayrell.Lang/LangES6/TranslatorES6Expression","Bayrell.Lang/LangES6/TranslatorES6Operator","Bayrell.Lang/LangES6/TranslatorES6Program","Bayrell.Lang/OpCodes/BaseOpCode","Bayrell.Lang/OpCodes/OpAnnotation","Bayrell.Lang/OpCodes/OpAssign","Bayrell.Lang/OpCodes/OpAssignValue","Bayrell.Lang/OpCodes/OpAttr","Bayrell.Lang/OpCodes/OpBreak","Bayrell.Lang/OpCodes/OpCall","Bayrell.Lang/OpCodes/OpClassOf","Bayrell.Lang/OpCodes/OpClassRef","Bayrell.Lang/OpCodes/OpCollection","Bayrell.Lang/OpCodes/OpComment","Bayrell.Lang/OpCodes/OpContinue","Bayrell.Lang/OpCodes/OpDeclareClass","Bayrell.Lang/OpCodes/OpDeclareFunction","Bayrell.Lang/OpCodes/OpDeclareFunctionArg","Bayrell.Lang/OpCodes/OpDict","Bayrell.Lang/OpCodes/OpEntityName","Bayrell.Lang/OpCodes/OpFlags","Bayrell.Lang/OpCodes/OpFor","Bayrell.Lang/OpCodes/OpIdentifier","Bayrell.Lang/OpCodes/OpIf","Bayrell.Lang/OpCodes/OpIfElse","Bayrell.Lang/OpCodes/OpInc","Bayrell.Lang/OpCodes/OpItems","Bayrell.Lang/OpCodes/OpMath","Bayrell.Lang/OpCodes/OpMethod","Bayrell.Lang/OpCodes/OpModule","Bayrell.Lang/OpCodes/OpNamespace","Bayrell.Lang/OpCodes/OpNew","Bayrell.Lang/OpCodes/OpNumber","Bayrell.Lang/OpCodes/OpPreprocessorIfCode","Bayrell.Lang/OpCodes/OpPreprocessorSwitch","Bayrell.Lang/OpCodes/OpReturn","Bayrell.Lang/OpCodes/OpString","Bayrell.Lang/OpCodes/OpTernary","Bayrell.Lang/OpCodes/OpThrow","Bayrell.Lang/OpCodes/OpTypeConvert","Bayrell.Lang/OpCodes/OpTypeIdentifier","Bayrell.Lang/OpCodes/OpUse","Bayrell.Lang/OpCodes/OpWhile"]);
	}
	static function getAssetsFiles($__ctx)
	{
		return static::assets($__ctx);
	}
	/**
	 * Returns enities
	 */
	static function entities($__ctx)
	{
		return null;
	}
	/**
	 * Returns enities
	 */
	static function resources($__ctx)
	{
		return null;
	}
	/* ======================= Class Init Functions ======================= */
	function getClassName()
	{
		return "Bayrell.Lang.ModuleDescription";
	}
	static function getCurrentNamespace()
	{
		return "Bayrell.Lang";
	}
	static function getCurrentClassName()
	{
		return "Bayrell.Lang.ModuleDescription";
	}
	static function getParentClassName()
	{
		return "";
	}
	static function getClassInfo($__ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($__ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.ModuleDescription",
			"name"=>"Bayrell.Lang.ModuleDescription",
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
	}
	static function getFieldsList($__ctx,$f)
	{
		$a = [];
		return \Runtime\Collection::from($a);
	}
	static function getFieldInfoByName($__ctx,$field_name)
	{
		return null;
	}
	static function getMethodsList($__ctx)
	{
		$a = [
		];
		return \Runtime\Collection::from($a);
	}
	static function getMethodInfoByName($__ctx,$field_name)
	{
		return null;
	}
}