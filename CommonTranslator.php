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
use Runtime\IntrospectionInfo;
use Runtime\rs;
use Runtime\ContextObject;
use BayrellLang\OpCodes\BaseOpCode;
use BayrellLang\OpCodes\OpAdd;
use BayrellLang\OpCodes\OpAnd;
use BayrellLang\OpCodes\OpAssign;
use BayrellLang\OpCodes\OpAssignDeclare;
use BayrellLang\OpCodes\OpBitAnd;
use BayrellLang\OpCodes\OpBitNot;
use BayrellLang\OpCodes\OpBitOr;
use BayrellLang\OpCodes\OpBitXor;
use BayrellLang\OpCodes\OpBreak;
use BayrellLang\OpCodes\OpCall;
use BayrellLang\OpCodes\OpCallAwait;
use BayrellLang\OpCodes\OpChilds;
use BayrellLang\OpCodes\OpClassDeclare;
use BayrellLang\OpCodes\OpClassName;
use BayrellLang\OpCodes\OpClone;
use BayrellLang\OpCodes\OpComment;
use BayrellLang\OpCodes\OpCompare;
use BayrellLang\OpCodes\OpConcat;
use BayrellLang\OpCodes\OpContinue;
use BayrellLang\OpCodes\OpDelete;
use BayrellLang\OpCodes\OpDiv;
use BayrellLang\OpCodes\OpDynamic;
use BayrellLang\OpCodes\OpFlags;
use BayrellLang\OpCodes\OpFor;
use BayrellLang\OpCodes\OpFunctionArrowDeclare;
use BayrellLang\OpCodes\OpFunctionDeclare;
use BayrellLang\OpCodes\OpHexNumber;
use BayrellLang\OpCodes\OpIdentifier;
use BayrellLang\OpCodes\OpIf;
use BayrellLang\OpCodes\OpIfElse;
use BayrellLang\OpCodes\OpInterfaceDeclare;
use BayrellLang\OpCodes\OpMap;
use BayrellLang\OpCodes\OpMethod;
use BayrellLang\OpCodes\OpMod;
use BayrellLang\OpCodes\OpMult;
use BayrellLang\OpCodes\OpNamespace;
use BayrellLang\OpCodes\OpNew;
use BayrellLang\OpCodes\OpNope;
use BayrellLang\OpCodes\OpNot;
use BayrellLang\OpCodes\OpNumber;
use BayrellLang\OpCodes\OpOr;
use BayrellLang\OpCodes\OpPostDec;
use BayrellLang\OpCodes\OpPostInc;
use BayrellLang\OpCodes\OpPow;
use BayrellLang\OpCodes\OpPreDec;
use BayrellLang\OpCodes\OpPreInc;
use BayrellLang\OpCodes\OpPreprocessorSwitch;
use BayrellLang\OpCodes\OpReturn;
use BayrellLang\OpCodes\OpShiftLeft;
use BayrellLang\OpCodes\OpShiftRight;
use BayrellLang\OpCodes\OpStatic;
use BayrellLang\OpCodes\OpString;
use BayrellLang\OpCodes\OpStringItem;
use BayrellLang\OpCodes\OpStructDeclare;
use BayrellLang\OpCodes\OpSub;
use BayrellLang\OpCodes\OpTemplateIdentifier;
use BayrellLang\OpCodes\OpTernary;
use BayrellLang\OpCodes\OpThrow;
use BayrellLang\OpCodes\OpTryCatch;
use BayrellLang\OpCodes\OpUse;
use BayrellLang\OpCodes\OpVector;
use BayrellLang\OpCodes\OpWhile;
class CommonTranslator extends ContextObject{
	public $one_lines;
	public $is_operation;
	public $current_opcode_level;
	public $max_opcode_level;
	public $indent_level;
	public $indent;
	public $space;
	public $crlf;
	/**
	 * Constructor
	 */
	function __construct($context = null){
		parent::__construct($context);
	}
	/**
	 * Push new level
	 */
	function pushOneLine($level){
		$this->one_lines->push($level);
	}
	/**
	 * Pop level
	 */
	function popOneLine(){
		return $this->one_lines->pop();
	}
	/**
	 * Returns if is one line
	 */
	function isOneLine(){
		return $this->one_lines->get($this->one_lines->count() - 1, false);
	}
	/**
	 * Increment indent level
	 */
	function levelInc(){
		if (!$this->isOneLine()){
			$this->indent_level++;
		}
	}
	/**
	 * Decrease indent level
	 */
	function levelDec(){
		if (!$this->isOneLine()){
			$this->indent_level--;
		}
	}
	/**
	 * Begin operation
	 */
	function beginOperation($push_one_line = true){
		$old_is_operation = $this->is_operation;
		$this->is_operation = true;
		$this->current_opcode_level = 0;
		$this->pushOneLine($push_one_line);
		return $old_is_operation;
	}
	/**
	 * End operation
	 */
	function endOperation($old_is_operation = false){
		$this->popOneLine();
		$this->is_operation = $old_is_operation;
	}
	/**
	 * Set operation
	 */
	function setOperation($is_operation = false){
		$this->is_operation = $is_operation;
	}
	/**
	 * Output operation
	 */
	function op($op_code, $op, $opcode_level){
		$res = "";
		$res .= $this->o($this->translateRun($op_code->value1), $opcode_level);
		$res .= " " . rtl::toString($op) . " ";
		$res .= $this->o($this->translateRun($op_code->value2), $opcode_level);
		$this->current_opcode_level = $opcode_level;
		return $res;
	}
	/**
	 * Output string
	 */
	function s($s, $force = false){
		if ($s == "" && !$force){
			return "";
		}
		if ($this->isOneLine()){
			return $s;
		}
		return rtl::toString($this->crlf) . rtl::toString(rs::str_repeat($this->indent, $this->indent_level)) . rtl::toString($s);
	}
	/**
	 * Output string witch brackets
	 */
	function o($s, $current_opcode_level){
		if ($this->is_operation == false){
			return $s;
		}
		if ($current_opcode_level > $this->current_opcode_level){
			return "(" . rtl::toString($s) . ")";
		}
		return $s;
	}
	function OpAdd($op_code){
		return "";
	}
	function OpAnd($op_code){
		return "";
	}
	function OpAssign($op_code){
		return "";
	}
	function OpAssignDeclare($op_code){
		return "";
	}
	function OpBitAnd($op_code){
		return "";
	}
	function OpBitNot($op_code){
		return "";
	}
	function OpBitOr($op_code){
		return "";
	}
	function OpBitXor($op_code){
		return "";
	}
	function OpBreak($op_code){
		return "";
	}
	function OpCall($op_code){
		return "";
	}
	function OpClassDeclare($op_code){
		return "";
	}
	function OpClassName($op_code){
		return "";
	}
	function OpClone($op_code){
		return "";
	}
	function OpComment($op_code){
		return "";
	}
	function OpCompare($op_code){
		return "";
	}
	function OpConcat($op_code){
		return "";
	}
	function OpContinue($op_code){
		return "";
	}
	function OpDelete($op_code){
		return "";
	}
	function OpDiv($op_code){
		return "";
	}
	function OpDynamic($op_code){
		return "";
	}
	function OpFlags($op_code){
		return "";
	}
	function OpFor($op_code){
		return "";
	}
	function OpFunctionArrowDeclare($op_code){
		return "";
	}
	function OpFunctionDeclare($op_code){
		return "";
	}
	function OpHexNumber($op_code){
		return "";
	}
	function OpIdentifier($op_code){
		return "";
	}
	function OpIf($op_code){
		return "";
	}
	function OpInterfaceDeclare($op_code){
		return "";
	}
	function OpMethod($op_code){
		return "";
	}
	function OpMod($op_code){
		return "";
	}
	function OpMult($op_code){
		return "";
	}
	function OpNamespace($op_code){
		return "";
	}
	function OpNew($op_code){
		return "";
	}
	function OpNope($op_code){
		return "";
	}
	function OpNot($op_code){
		return "";
	}
	function OpNumber($op_code){
		return "";
	}
	function OpOr($op_code){
		return "";
	}
	function OpPostDec($op_code){
		return "";
	}
	function OpPostInc($op_code){
		return "";
	}
	function OpPow($op_code){
		return "";
	}
	function OpPreDec($op_code){
		return "";
	}
	function OpPreInc($op_code){
		return "";
	}
	function OpPreprocessorSwitch($op_code){
		return "";
	}
	function OpReturn($op_code){
		return "";
	}
	function OpShiftLeft($op_code){
		return "";
	}
	function OpShiftRight($op_code){
		return "";
	}
	function OpStatic($op_code){
		return "";
	}
	function OpString($op_code){
		return "";
	}
	function OpStringItem($op_code){
		return "";
	}
	function OpStructDeclare($op_code){
		return "";
	}
	function OpSub($op_code){
		return "";
	}
	function OpTemplateIdentifier($op_code){
		return "";
	}
	function OpTernary($op_code){
		return "";
	}
	function OpThrow($op_code){
		return "";
	}
	function OpTryCatch($op_code){
		return "";
	}
	function OpUse($op_code){
		return "";
	}
	function OpWhile($op_code){
		return "";
	}
	/**
	 * Translate to language
	 * @param BaseOpCode op_code - Abstract syntax tree
	 * @returns string - The result
	 */
	function translateChilds($childs){
		if ($childs == null){
			return "";
		}
		$res = "";
		$code_str = "";
		$flag = true;
		for ($i = 0; $i < $childs->count(); $i++){
			$this->current_opcode_level = 0;
			$code_str = $this->translateRun($childs->item($i));
			if ($code_str == ""){
				continue;
			}
			if ($flag){
				$res .= $code_str;
				$flag = false;
			}
			else {
				$res .= $this->s($code_str);
			}
		}
		return $res;
	}
	/**
	 * Translate to language
	 * @param BaseOpCode op_code - Abstract syntax tree
	 * @returns string - The result
	 */
	function translateRun($op_code){
		if ($op_code instanceof OpNope){
			return $this->translateChilds($op_code->childs);
		}
		else if ($op_code instanceof OpInterfaceDeclare){
			return $this->OpInterfaceDeclare($op_code);
		}
		else if ($op_code instanceof OpStructDeclare){
			return $this->OpStructDeclare($op_code);
		}
		else if ($op_code instanceof OpAdd){
			return $this->OpAdd($op_code);
		}
		else if ($op_code instanceof OpAnd){
			return $this->OpAnd($op_code);
		}
		else if ($op_code instanceof OpAssign){
			return $this->OpAssign($op_code);
		}
		else if ($op_code instanceof OpAssignDeclare){
			return $this->OpAssignDeclare($op_code);
		}
		else if ($op_code instanceof OpBitAnd){
			return $this->OpBitAnd($op_code);
		}
		else if ($op_code instanceof OpBitNot){
			return $this->OpBitNot($op_code);
		}
		else if ($op_code instanceof OpBitOr){
			return $this->OpBitOr($op_code);
		}
		else if ($op_code instanceof OpBitXor){
			return $this->OpBitXor($op_code);
		}
		else if ($op_code instanceof OpBreak){
			return $this->OpBreak($op_code);
		}
		else if ($op_code instanceof OpCall){
			return $this->OpCall($op_code);
		}
		else if ($op_code instanceof OpClassDeclare){
			return $this->OpClassDeclare($op_code);
		}
		else if ($op_code instanceof OpClassName){
			return $this->OpClassName($op_code);
		}
		else if ($op_code instanceof OpClone){
			return $this->OpClone($op_code);
		}
		else if ($op_code instanceof OpComment){
			return $this->OpComment($op_code);
		}
		else if ($op_code instanceof OpCompare){
			return $this->OpCompare($op_code);
		}
		else if ($op_code instanceof OpConcat){
			return $this->OpConcat($op_code);
		}
		else if ($op_code instanceof OpContinue){
			return $this->OpContinue($op_code);
		}
		else if ($op_code instanceof OpDelete){
			return $this->OpDelete($op_code);
		}
		else if ($op_code instanceof OpDiv){
			return $this->OpDiv($op_code);
		}
		else if ($op_code instanceof OpDynamic){
			return $this->OpDynamic($op_code);
		}
		else if ($op_code instanceof OpFlags){
			return $this->OpFlags($op_code);
		}
		else if ($op_code instanceof OpFor){
			return $this->OpFor($op_code);
		}
		else if ($op_code instanceof OpFunctionArrowDeclare){
			return $this->OpFunctionArrowDeclare($op_code);
		}
		else if ($op_code instanceof OpFunctionDeclare){
			return $this->OpFunctionDeclare($op_code);
		}
		else if ($op_code instanceof OpHexNumber){
			return $this->OpHexNumber($op_code);
		}
		else if ($op_code instanceof OpIdentifier){
			return $this->OpIdentifier($op_code);
		}
		else if ($op_code instanceof OpMap){
			return $this->OpMap($op_code);
		}
		else if ($op_code instanceof OpMethod){
			return $this->OpMethod($op_code);
		}
		else if ($op_code instanceof OpIf){
			return $this->OpIf($op_code);
		}
		else if ($op_code instanceof OpMod){
			return $this->OpMod($op_code);
		}
		else if ($op_code instanceof OpMult){
			return $this->OpMult($op_code);
		}
		else if ($op_code instanceof OpNamespace){
			return $this->OpNamespace($op_code);
		}
		else if ($op_code instanceof OpNew){
			return $this->OpNew($op_code);
		}
		else if ($op_code instanceof OpNope){
			return $this->OpNope($op_code);
		}
		else if ($op_code instanceof OpNot){
			return $this->OpNot($op_code);
		}
		else if ($op_code instanceof OpNumber){
			return $this->OpNumber($op_code);
		}
		else if ($op_code instanceof OpOr){
			return $this->OpOr($op_code);
		}
		else if ($op_code instanceof OpPostDec){
			return $this->OpPostDec($op_code);
		}
		else if ($op_code instanceof OpPostInc){
			return $this->OpPostInc($op_code);
		}
		else if ($op_code instanceof OpPow){
			return $this->OpPow($op_code);
		}
		else if ($op_code instanceof OpPreDec){
			return $this->OpPreDec($op_code);
		}
		else if ($op_code instanceof OpPreInc){
			return $this->OpPreInc($op_code);
		}
		else if ($op_code instanceof OpPreprocessorSwitch){
			return $this->OpPreprocessorSwitch($op_code);
		}
		else if ($op_code instanceof OpReturn){
			return $this->OpReturn($op_code);
		}
		else if ($op_code instanceof OpShiftLeft){
			return $this->OpShiftLeft($op_code);
		}
		else if ($op_code instanceof OpShiftRight){
			return $this->OpShiftRight($op_code);
		}
		else if ($op_code instanceof OpStatic){
			return $this->OpStatic($op_code);
		}
		else if ($op_code instanceof OpString){
			return $this->OpString($op_code);
		}
		else if ($op_code instanceof OpStringItem){
			return $this->OpStringItem($op_code);
		}
		else if ($op_code instanceof OpSub){
			return $this->OpSub($op_code);
		}
		else if ($op_code instanceof OpTemplateIdentifier){
			return $this->OpTemplateIdentifier($op_code);
		}
		else if ($op_code instanceof OpTernary){
			return $this->OpTernary($op_code);
		}
		else if ($op_code instanceof OpThrow){
			return $this->OpThrow($op_code);
		}
		else if ($op_code instanceof OpTryCatch){
			return $this->OpTryCatch($op_code);
		}
		else if ($op_code instanceof OpUse){
			return $this->OpUse($op_code);
		}
		else if ($op_code instanceof OpVector){
			return $this->OpVector($op_code);
		}
		else if ($op_code instanceof OpWhile){
			return $this->OpWhile($op_code);
		}
		return "";
	}
	/**
	 * Reset translator to default settings
	 */
	function resetTranslator(){
		$this->one_lines = new Vector();
		$this->is_operation = false;
		$this->current_opcode_level = 0;
		$this->max_opcode_level = 100;
		$this->indent_level = 0;
	}
	/**
	 * Translate to language
	 * @param BaseOpCode op_code - Abstract syntax tree
	 * @returns string - The result
	 */
	function translate($op_code){
		$this->resetTranslator();
		return $this->translateRun($op_code);
	}
	/* ======================= Class Init Functions ======================= */
	public function getClassName(){return "BayrellLang.CommonTranslator";}
	public static function getParentClassName(){return "Runtime.ContextObject";}
	protected function _init(){
		parent::_init();
		$this->one_lines = null;
		$this->is_operation = false;
		$this->current_opcode_level = 0;
		$this->max_opcode_level = 100;
		$this->indent_level = 0;
		$this->indent = "\t";
		$this->space = " ";
		$this->crlf = "\n";
	}
}