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
namespace BayrellLang;
use Runtime\rtl;
use Runtime\Map;
use Runtime\Vector;
use BayrellLang\CommonTranslator;
use BayrellLang\Output\OutputAbstract;
use BayrellLang\Output\OutputChilds;
use BayrellLang\Output\OutputNope;
use BayrellLang\Output\OutputOneLine;
use BayrellLang\Output\OutputString;
class TranslatorBay extends CommonTranslator{
	public function getClassName(){return "BayrellLang.TranslatorBay";}
	public static function getParentClassName(){return "BayrellLang.CommonTranslator";}
	/**
	 * Operator ADD
	 */
	function OpAdd($code_tree){
		$tag = new OutputNope();
		$tag->addChild($this->translateRun($code_tree->value1));
		$tag->addChild((new OutputString())->setValueAtSameLine("+"));
		$tag->addChild($this->translateRun($code_tree->value2));
		return $tag;
	}
	/**
	 * Operator AND
	 */
	function OpAnd($code_tree){
		return (new OutputOneLine())->addChild($this->translateRun($code_tree->value1))->addChild((new OutputString())->setValueAtSameLine("and"))->addChild($this->translateRun($code_tree->value2));
	}
	function OpArray($code_tree){
		return null;
	}
	/**
	 * Assign
	 */
	function OpAssign($code_tree){
		return (new OutputOneLine())->addChild($this->translateRun($code_tree->ident))->addChild((new OutputString())->setValueAtSameLine("="))->addChild($this->translateRun($code_tree->value));
	}
	function OpAssignDeclare($code_tree){
		return null;
	}
	function OpBitAnd($code_tree){
		return null;
	}
	function OpBitNot($code_tree){
		return null;
	}
	function OpBitOr($code_tree){
		return null;
	}
	function OpBitXor($code_tree){
		return null;
	}
	function OpBreak($code_tree){
		return null;
	}
	function OpCall($code_tree){
		return null;
	}
	function OpChilds($code_tree){
		return null;
	}
	function OpClone($code_tree){
		return null;
	}
	function OpCompare($code_tree){
		return null;
	}
	function OpConcat($code_tree){
		return null;
	}
	function OpContinue($code_tree){
		return null;
	}
	function OpDelete($code_tree){
		return null;
	}
	function OpDiv($code_tree){
		return null;
	}
	function OpDynamic($code_tree){
		return null;
	}
	function OpFlags($code_tree){
		return null;
	}
	function OpFor($code_tree){
		return null;
	}
	function OpHexNumber($code_tree){
		return null;
	}
	/**
	 * Identifier
	 */
	function OpIdentifier($code_tree){
		return (new OutputString())->setValueAtSameLine($code_tree->value);
	}
	function OpIf($code_tree){
		return null;
	}
	function OpIfElse($code_tree){
		return null;
	}
	function OpMod($code_tree){
		return null;
	}
	function OpMult($code_tree){
		return null;
	}
	function OpNamespace($code_tree){
		return null;
	}
	function OpNew($code_tree){
		return null;
	}
	function OpNope($code_tree){
		return null;
	}
	function OpNot($code_tree){
		return null;
	}
	/**
	 * Number
	 */
	function OpNumber($code_tree){
		return (new OutputString())->setValueAtSameLine($code_tree->value);
	}
	function OpOr($code_tree){
		return null;
	}
	function OpPostDec($code_tree){
		return null;
	}
	function OpPostInc($code_tree){
		return null;
	}
	function OpPow($code_tree){
		return null;
	}
	function OpPreDec($code_tree){
		return null;
	}
	function OpPreInc($code_tree){
		return null;
	}
	function OpReturn($code_tree){
		return null;
	}
	function OpShiftLeft($code_tree){
		return null;
	}
	function OpShiftRight($code_tree){
		return null;
	}
	function OpStatic($code_tree){
		return null;
	}
	function OpString($code_tree){
		return null;
	}
	function OpSub($code_tree){
		return null;
	}
	function OpTemplateIdentifier($code_tree){
		return null;
	}
	function OpTernary($code_tree){
		return null;
	}
	function OpThrow($code_tree){
		return null;
	}
	function OpTryCatch($code_tree){
		return null;
	}
	function OpUse($code_tree){
		return null;
	}
	function OpWhile($code_tree){
		return null;
	}
}