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
use Runtime\rs;
use Runtime\rtl;
use Runtime\Map;
use Runtime\Vector;
use Runtime\IntrospectionInfo;
use Runtime\UIStruct;
use Runtime\rs;
use Runtime\ContextObject;
use BayrellLang\CoreTranslator;
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
class TranslatorBay extends CoreTranslator{
	public $is_interface;
	public $is_struct;
	public $struct_read_only;
	public $current_function_name;
	public $current_class_name;
	public $current_namespace;
	/**
	 * Get name
	 */
	function getName($name){
		return $name;
	}
	/**
	 * Escape string
	 */
	function convertString($s){
		$s = $re::replace("\\\\", "\\\\", $s);
		$s = $re::replace("\"", "\\\"", $s);
		$s = $re::replace("\n", "\\n", $s);
		$s = $re::replace("\r", "\\r", $s);
		$s = $re::replace("\t", "\\t", $s);
		return $s;
	}
	/**
	 * Convert string
	 */
	function convertString($s){
		return "\"" . rtl::toString($this->escapeString($s)) . "\"";
	}
	/** =========================== Identifier ============================ */
	/**
	 * HexNumber
	 */
	function OpHexNumber($op_code){
		$this->setMaxOpCodeLevel();
		return $op_code->value;
	}
	/**
	 * Identifier
	 */
	function OpIdentifier($op_code){
		$this->setMaxOpCodeLevel();
		return $this->getName($op_code->value);
	}
	/**
	 * Number
	 */
	function OpNumber($op_code){
		$this->setMaxOpCodeLevel();
		return $op_code->value;
	}
	/**
	 * String
	 */
	function OpString($op_code){
		$this->setMaxOpCodeLevel();
		return $this->convertString($op_code->value);
	}
	/**
	 * OpStringItem
	 */
	function OpStringItem($op_code){
		return rtl::toString($this->translateRun($op_code->value1)) . rtl::toString($this->n("[")) . rtl::toString($this->s($this->translateRun($op_code->value2))) . "]";
	}
	/** ======================== Dynamic or static ======================== */
	/**
	 * Dynamic load
	 */
	function OpDynamic($op_code){
		$res = rtl::toString($this->o($this->translateRun($op_code->value), $this->max_opcode_level)) . "." . rtl::toString($op_code->name);
		$this->setMaxOpCodeLevel();
		return $res;
	}
	/**
	 * Static load
	 */
	function OpStatic($op_code){
		return rtl::toString($this->translateRun($op_code->value)) . "::" . rtl::toString($op_code->name);
	}
	/**
	 * Template Identifier
	 */
	function OpTemplateIdentifier($op_code){
		return $this->translateRun($op_code->t);
	}
	/** ============================ Operations ============================ */
	/**
	 * ADD
	 */
	function OpAdd($op_code){
		return $this->op($op_code, "+", 13);
	}
	/**
	 * AND
	 */
	function OpAnd($op_code){
		return $this->op($op_code, "&&", 6);
	}
	/**
	 * Bit AND
	 */
	function OpBitAnd($op_code){
		return $this->op($op_code, "&", 9);
	}
	/**
	 * Bit NOT
	 */
	function OpBitNot($op_code){
		$res = "!" . rtl::toString($this->o($this->translateRun($op_code->value), 16));
		$this->setOpCodeLevel(16);
		return $res;
	}
	/**
	 * Bit OR
	 */
	function OpBitOr($op_code){
		return $this->op($op_code, "|", 7);
	}
	/**
	 * Bit XOR
	 */
	function OpBitXor($op_code){
		return $this->op($op_code, "^", 8);
	}
	/**
	 * Concat strings
	 */
	function OpConcat($op_code){
		return $this->op($op_code, "~", 13);
	}
	/**
	 * Divide
	 */
	function OpDiv($op_code){
		return $this->op($op_code, "/", 14);
	}
	/**
	 * Module
	 */
	function OpMod($op_code){
		return $this->op($op_code, "%", 14);
	}
	/**
	 * Multiply
	 */
	function OpMult($op_code){
		return $this->op($op_code, "*", 14);
	}
	/**
	 * New
	 */
	function OpNew($op_code){
		/* Function name */
		$s = "new " . rtl::toString($this->translateRun($op_code->value));
		/* Call arguments */
		$this->setMaxOpCodeLevel();
		$this->beginOperation();
		$s .= "(";
		if ($op_code->args != null){
			$ch = "";
			for ($i = 0; $i < $op_code->args->count(); $i++){
				$op = $op_code->args->item($i);
				$s .= rtl::toString($ch) . rtl::toString($this->s($this->translateRun($op)));
				$ch = ", ";
			}
		}
		$s .= $this->s(")");
		$this->endOperation();
		$this->setOpCodeLevel(19);
		return $s;
	}
	/**
	 * Not
	 */
	function OpNot($op_code){
		$res = "!" . rtl::toString($this->o($this->translateRun($op_code->value), 16));
		$this->setOpCodeLevel(16);
		return $res;
	}
	/**
	 * Or
	 */
	function OpOr($op_code){
		return $this->op($op_code, "||", 5);
	}
	/**
	 * Post decrement
	 */
	function OpPostDec($op_code){
		$semicolon = ($this->isOperation()) ? ("") : (";");
		$res = rtl::toString($this->o($this->translateRun($op_code->value), 17)) . "--" . rtl::toString($semicolon);
		$this->setOpCodeLevel(17);
		return $res;
	}
	/**
	 * Post increment
	 */
	function OpPostInc($op_code){
		$semicolon = ($this->isOperation()) ? ("") : (";");
		$res = rtl::toString($this->o($this->translateRun($op_code->value), 17)) . "++" . rtl::toString($semicolon);
		$this->setOpCodeLevel(17);
		return $res;
	}
	/**
	 * Pow
	 */
	function OpPow($op_code){
		return $this->op($op_code, "**", 15);
	}
	/**
	 * Pre decrement
	 */
	function OpPreDec($op_code){
		$semicolon = ($this->isOperation()) ? ("") : (";");
		$res = "--" . rtl::toString($this->o($this->translateRun($op_code->value), 16)) . rtl::toString($semicolon);
		$this->setOpCodeLevel(16);
		return $res;
	}
	/**
	 * Pre increment
	 */
	function OpPreInc($op_code){
		$semicolon = ($this->isOperation()) ? ("") : (";");
		$res = "++" . rtl::toString($this->o($this->translateRun($op_code->value), 16)) . rtl::toString($semicolon);
		$this->setOpCodeLevel(16);
		return $res;
	}
	/**
	 * Bit shift left
	 */
	function OpShiftLeft($op_code){
		return $this->op($op_code, "<<", 12);
	}
	/**
	 * Bit shift right
	 */
	function OpShiftRight($op_code){
		return $this->op($op_code, ">>", 12);
	}
	/**
	 * Sub
	 */
	function OpSub($op_code){
		return $this->op($op_code, "-", 13);
	}
	/**
	 * Operator call function
	 */
	function OpCall($op_code){
		$s = "";
		$this->pushOneLine();
		$this->beginOperation();
		$s .= $this->translateRun($op_code->value);
		$this->endOperation();
		/* Call arguments */
		$s_args = "";
		$s .= $this->s("(");
		if ($op_code->args != null){
			$ch = "";
			for ($i = 0; $i < $op_code->args->count(); $i++){
				$op = $op_code->args->item($i);
				$s_args .= rtl::toString($ch) . rtl::toString($this->s($this->translateRun($op)));
				$ch = ", ";
			}
		}
		$s .= $this->s($s_args);
		$s .= $this->s(")");
		if (!$this->isOperation()){
			$s .= ";";
		}
		$this->popOneLine();
		return $s;
	}
	/**
	 * Operator call function
	 */
	function OpCompare($op_code){
		$this->setOpCodeLevel(10);
		return $this->op($op_code, $op_code->condition, 10);
	}
	/**
	 * Operator call function
	 */
	function OpTernary($op_code){
		$semicolon = ($this->isOperation()) ? ("") : (";");
		$res = "(" . rtl::toString($this->translateRun($op_code->condition)) . ") ? " . "(" . rtl::toString($this->s($this->translateRun($op_code->if_true))) . ") : " . "(" . rtl::toString($this->s($this->translateRun($op_code->if_false))) . ")";
		$this->setOpCodeLevel(4);
		return $res;
	}
	/** ========================== Vector and Map ========================= */
	/**
	 * Vector
	 */
	function OpVector($op_code){
		$res = "";
		$ch = "";
		for ($i = 0; $i < $op_code->values->count(); $i++){
			$item = $op_code->values->item($i);
			$this->setMaxOpCodeLevel();
			$res .= rtl::toString($ch) . rtl::toString($this->s($this->translateRun($item)));
			$ch = ",";
		}
		$this->setMaxOpCodeLevel();
		return rtl::toString($this->n("[")) . rtl::toString($res) . "]";
	}
	/**
	 * Map
	 */
	function OpMap($op_code){
		$res = "";
		$keys = $op_code->values->keys();
		for ($i = 0; $i < $op_code->values->count(); $i++){
			$key = $keys->item($i);
			$item = $op_code->values->item($key);
			$this->setMaxOpCodeLevel();
			$res .= rtl::toString($ch) . rtl::toString($this->s(rtl::toString($this->convertString($key)) . ": " . rtl::toString($this->translateRun($item))));
			$ch = ",";
		}
		$this->setMaxOpCodeLevel();
		return rtl::toString($this->n("{")) . rtl::toString($res) . "}";
	}
	/**
	 * Method
	 */
	function OpMethod($op_code){
		return "method " . rtl::toString($this->translateRun($op_code->value));
	}
	/**
	 * Class name
	 */
	function OpClassName($op_code){
		return $this->convertString($op_code->value);
	}
	/** ============================ Operators ============================ */
	/**
	 * Assign
	 */
	function OpAssign($op_code){
		$old_is_operation = $this->beginOperation();
		/* one line */
		$this->pushOneLine(true);
		$res = $this->translateRun($op_code->ident);
		$this->popOneLine();
		if ($op_code->op_name == "="){
			$res .= " = ";
		}
		else if ($op_code->op_name == "~="){
			$res .= " .= ";
		}
		else if ($op_code->op_name == "+="){
			$res .= " += ";
		}
		else if ($op_code->op_name == "-="){
			$res .= " -= ";
		}
		$this->setOpCodeLevel(0);
		$res .= $this->s($this->translateRun($op_code->value));
		if (!$old_is_operation){
			$res .= $this->n(";");
		}
		$this->endOperation();
		return rs::trim($res);
	}
	/**
	 * Assign declare
	 */
	function OpAssignDeclare($op_code){
		$old_is_operation = $this->beginOperation();
		$res = "";
		/* one line */
		$this->pushOneLine(true);
		$res .= rtl::toString($this->translateRun($op_code->tp)) . " ";
		$res .= $op_code->name;
		$this->popOneLine();
		if ($op_code->value == null){
			$this->setOpCodeLevel(0);
			$res .= " = " . rtl::toString($this->s($this->translateRun($op_code->value)));
		}
		if (!$old_is_operation){
			$res .= $this->n(";");
		}
		$this->endOperation();
		return rs::trim($res);
	}
	/**
	 * Break
	 */
	function OpBreak($op_code){
		return "break;";
	}
	/**
	 * Clone
	 */
	function OpClone($op_code){
		$this->beginOperation();
		$this->setOpCodeLevel(0);
		$res = "clone " . rtl::toString($this->translateRun($op_code->value));
		$this->endOperation();
		return rs::trim($res);
	}
	/**
	 * Continue
	 */
	function OpContinue($op_code){
		return "continue;";
	}
	/**
	 * Delete
	 */
	function OpDelete($op_code){
		$this->beginOperation();
		$this->setOpCodeLevel(0);
		$res = "delete " . rtl::toString($this->translateRun($op_code->value));
		$this->endOperation();
		return rs::trim($res);
	}
	/**
	 * For
	 */
	function OpFor($op_code){
		$s = "";
		/* Header */
		$this->beginOperation();
		$s .= $this->n("for (" . rtl::toString($this->translateRun($op_code->loop_init)) . "; " . rtl::toString($this->translateRun($op_code->loop_condition)) . "; " . rtl::toString($this->translateRun($op_code->loop_inc)) . ")");
		$s .= $this->n("{");
		$this->endOperation();
		/* Childs */
		for ($i = 0; $i < $op_code->childs->count(); $i++){
			$item = $op_code->childs->item($i);
			$s .= $this->s($this->translateRun($item));
		}
		$s .= $this->n("}");
		return rs::trim($s);
	}
	/**
	 * If
	 */
	function OpIf($op_code){
		$s = "";
		/* Condition */
		$this->beginOperation();
		$s .= $this->n("if (" . rtl::toString($this->translateRun($op_code->condition)) . ")");
		$s .= $this->n("{");
		$this->endOperation();
		/* If true */
		for ($i = 0; $i < $op_code->if_true->count(); $i++){
			$item = $op_code->if_true->item($i);
			$s .= $this->s($this->translateRun($item));
		}
		$s .= $this->n("}");
		/* If else */
		for ($i = 0; $i < $op_code->if_else->count(); $i++){
			$if_else = $op_code->if_else->item($i);
			$this->beginOperation();
			$res = "else if (" . rtl::toString($this->translateRun($if_else->condition)) . ")";
			$res .= $this->n("{");
			$this->endOperation();
			$s .= $this->n($res);
			for ($j = 0; $j < $if_else->if_true->count(); $j++){
				$item = $if_else->if_true->item($j);
				$s .= $this->s($this->translateRun($item));
			}
			$s .= $this->n("}");
		}
		/* If false */
		if ($op_code->if_false != null){
			$s .= $this->n("else");
			$s .= $this->n("{");
			for ($i = 0; $i < $op_code->if_false->count(); $i++){
				$item = $op_code->if_false->item($i);
				$s .= $this->s($this->translateRun($item));
			}
			$s .= $this->n("}");
		}
		return rs::trim($s);
	}
	/**
	 * Return
	 */
	function OpReturn($op_code){
		$this->beginOperation();
		$this->setOpCodeLevel(0);
		$res = "return " . rtl::toString($this->translateRun($op_code->value));
		$this->endOperation();
		return rs::trim($res);
	}
	/**
	 * Throw
	 */
	function OpThrow($op_code){
		$this->beginOperation();
		$this->setOpCodeLevel(0);
		$res = "throw " . rtl::toString($this->translateRun($op_code->value));
		$this->endOperation();
		return rs::trim($res);
	}
	/**
	 * Try Catch
	 */
	function OpTryCatch($op_code){
		$s = "";
		$s .= "try";
		$s .= $this->n("{");
		for ($i = 0; $i < $op_code->op_try->count(); $i++){
			$item = $op_code->op_try->item($i);
			$s .= $this->s($this->translateRun($item));
		}
		$s .= $this->n("}");
		$try_catch_childs_sz = $op_code->childs->count();
		$is_else = "";
		for ($i = 0; $i < $try_catch_childs_sz; $i++){
			$try_catch = $op_code->childs->item($i);
			$this->beginOperation();
			$tp = $this->translateRun($try_catch->op_type);
			$name = $this->translateRun($try_catch->op_ident);
			$this->endOperation();
			/* catch childs */
			$catch_s = "";
			$s .= $this->n("catch (" . rtl::toString($tp) . " " . rtl::toString($name) . ")");
			$s .= $this->n("{");
			for ($j = 0; $j < $try_catch->childs->count(); $j++){
				$item = $op_code->op_try->item($i);
				$catch_s .= $this->s($this->translateRun($item));
			}
			$s .= $this->s($catch_s);
			$s .= $this->n("}");
		}
		return rs::trim($s);
	}
	/**
	 * While
	 */
	function OpWhile($op_code){
		$s = "";
		/* Condition */
		$this->beginOperation();
		$s .= $this->n("while (" . rtl::toString($this->translateRun($op_code->condition)) . ")");
		$s .= $this->n("{");
		$this->endOperation();
		/* Childs */
		for ($i = 0; $i < $op_code->childs->count(); $i++){
			$item = $op_code->childs->item($i);
			$s .= $this->s($this->translateRun($item));
		}
		$s .= "}";
		return $s;
	}
	/** ======================== Namespace and use ======================== */
	/**
	 * Namespace
	 */
	function OpNamespace($op_code){
		$this->current_namespace = $op_code->value;
		return "namespace " . rtl::toString($op_code->value) . ";";
	}
	/**
	 * Use
	 */
	function OpUse($op_code){
		$lib_name = $op_code->value;
		return "use " . rtl::toString($lib_name) . ";";
	}
	/** ============================= Classes ============================= */
	/**
	 * Function arrow declare
	 */
	function OpFunctionArrowDeclare($op_code){
		$res = "";
		$ch = "";
		$use_vars = new Vector();
		/* Skip if declare function */
		if ($op_code->isFlag("declare")){
			return "";
		}
		if ($op_code->isFlag("static")){
			$res .= "static function ";
			if ($this->current_function_name->count() == 0){
				$this->current_function_is_static = true;
			}
		}
		else {
			$res .= "function ";
			if ($this->current_function_name->count() == 0){
				$this->current_function_is_static = false;
			}
		}
		$this->current_function_name->push($op_code->name);
		$res .= $op_code->name;
		$res .= "(";
		for ($i = 0; $i < $op_code->args->count(); $i++){
			$variable = $op_code->args->item($i);
			$this->pushOneLine(true);
			$res .= rtl::toString($ch) . "\$" . rtl::toString($variable->name);
			if ($variable->value != null){
				$res .= " = " . rtl::toString($this->translateRun($variable->value));
			}
			$this->popOneLine();
			$use_vars->push($variable->name);
			$ch = ", ";
		}
		$res .= ")";
		$res .= "{";
		$this->pushOneLine();
		$res .= $this->s("return ");
		$res .= $this->OpFunctionDeclare($op_code->return_function, true, $use_vars);
		$res .= $this->s("}");
		$this->popOneLine();
		$this->current_function_name->pop();
		return $res;
	}
	/**
	 * Function declare
	 */
	function OpFunctionDeclare($op_code, $end_semicolon = false, $use_vars = null){
		$res = "";
		$ch = "";
		$s = "";
		/* Skip if declare function */
		if ($op_code->isFlag("declare")){
			return "";
		}
		if ($op_code->isFlag("static")){
			$res .= "static function ";
			if ($this->current_function_name->count() == 0){
				$this->current_function_is_static = true;
			}
		}
		else {
			$res .= "function ";
			if ($this->current_function_name->count() == 0){
				$this->current_function_is_static = false;
			}
		}
		if ($op_code->name == "constructor"){
			$res .= "__construct";
		}
		else if ($op_code->name == "destructor"){
			$res .= "__destruct";
		}
		else {
			$res .= $op_code->name;
		}
		$this->current_function_name->push($op_code->name);
		$this->pushOneLine(true);
		$res .= "(";
		for ($i = 0; $i < $op_code->args->count(); $i++){
			$variable = $op_code->args->item($i);
			$res .= rtl::toString($ch) . "\$" . rtl::toString($variable->name);
			if ($variable->value != null){
				$res .= " = " . rtl::toString($this->translateRun($variable->value));
			}
			$ch = ", ";
		}
		$res .= ")";
		$flag_use = false;
		if ($this->current_function_name->count() == 2 && !$this->current_function_is_static){
			if ($use_vars == null){
				$use_vars = new Vector();
			}
		}
		if ($use_vars != null){
			if ($use_vars->count() > 0){
				$flag_use = true;
			}
		}
		if ($op_code->use_variables != null){
			if ($op_code->use_variables->count() > 0){
				$flag_use = true;
			}
		}
		if ($flag_use){
			$ch = "";
			$res .= " use (";
			if ($use_vars != null){
				for ($i = 0; $i < $use_vars->count(); $i++){
					$res .= rtl::toString($ch) . "&\$" . rtl::toString($use_vars->item($i));
					$ch = ", ";
				}
			}
			if ($op_code->use_variables != null){
				for ($i = 0; $i < $op_code->use_variables->count(); $i++){
					$res .= rtl::toString($ch) . "&\$" . rtl::toString($op_code->use_variables->item($i));
					$ch = ", ";
				}
			}
			$res .= ")";
		}
		$this->popOneLine();
		if ($this->is_interface){
			$res .= ";";
		}
		else {
			$res .= "{";
			$this->pushOneLine(false);
			if ($op_code->childs != null){
				for ($i = 0; $i < $op_code->childs->count(); $i++){
					$res .= $this->s($this->translateRun($op_code->childs->item($i)));
				}
			}
			$res .= $this->s("}" . rtl::toString(($end_semicolon) ? (";") : ("")));
			$this->popOneLine();
		}
		$this->current_function_name->pop();
		return $res;
	}
	/**
	 * Class declare header
	 */
	function OpClassDeclareHeader($op_code){
		$res = "";
		$old_is_operation = $this->beginOperation();
		if ($this->is_interface){
			$res .= "interface ";
		}
		else {
			$res .= "class ";
		}
		$res .= $op_code->class_name;
		if ($op_code->class_extends != ""){
			$res .= " extends " . rtl::toString($this->translateRun($op_code->class_extends));
		}
		if ($op_code->class_implements != null && $op_code->class_implements->count() > 0){
			$res .= " implements ";
			$ch = "";
			for ($i = 0; $i < $op_code->class_implements->count(); $i++){
				$name = $op_code->class_implements->item($i);
				$res .= rtl::toString($ch) . rtl::toString($this->getName($name));
				$ch = ", ";
			}
		}
		$res .= "{";
		$this->endOperation($old_is_operation);
		return $res;
	}
	/**
	 * Class declare footer
	 */
	function OpClassDeclareFooter($op_code){
	}
	/**
	 * Class init functions
	 */
	function OpClassInit($op_code){
	}
	/**
	 * Class declare body
	 */
	function OpClassBody($op_code){
		$res = "";
		for ($i = 0; $i < $op_code->childs->count(); $i++){
			$item = $op_code->childs->item($i);
			$res .= $this->s($this->OpClassBodyItem($item));
		}
		return $res;
	}
	/**
	 * Class declare body item
	 */
	function OpClassBodyItem($op_code){
		if ($op_code instanceof OpFunctionArrowDeclare){
			return $this->OpFunctionArrowDeclare($op_code);
		}
		else if ($op_code instanceof OpFunctionDeclare){
			return $this->OpFunctionDeclare($op_code);
		}
		else if ($op_code instanceof OpPreprocessorSwitch){
			return $this->OpPreprocessorSwitch($op_code);
		}
		else if ($op_code instanceof OpComment){
			return $this->OpComment($op_code);
		}
		return "";
	}
	/**
	 * Class declare
	 */
	function OpClassDeclare($op_code){
		$res = "";
		$s = "";
		/* Set current class name */
		$this->current_class_name = $op_code->class_name;
		/* Skip if declare class */
		if ($op_code->isFlag("declare")){
			return "";
		}
		$res .= $this->OpClassDeclareHeader($op_code);
		/* Body */
		$res .= $this->OpClassBody($op_code);
		/* Class Init */
		$res .= $this->OpClassInit($op_code);
		/* Footer class */
		$res .= $this->s("}");
		/* Footer */
		$res .= $this->OpClassDeclareFooter($op_code);
		return $res;
	}
	/**
	 * Interface declare
	 */
	function OpInterfaceDeclare($op_code){
		$this->is_interface = true;
		$res = $this->OpClassDeclare($op_code);
		$this->is_interface = false;
		return $res;
	}
	/**
	 * Struct declare
	 */
	function OpStructDeclare($op_code){
		$this->is_struct = true;
		$this->struct_read_only = $op_code->is_readonly;
		$res = $this->OpClassDeclare($op_code);
		$this->is_struct = false;
		return $res;
	}
	/**
	 * Reset translator to default settings
	 */
	function resetTranslator(){
		parent::resetTranslator();
		$this->current_function_name = new Vector();
	}
	/* ======================= Class Init Functions ======================= */
	public function getClassName(){return "BayrellLang.LangBay.TranslatorBay";}
	public static function getParentClassName(){return "BayrellLang.CoreTranslator";}
	protected function _init(){
		parent::_init();
	}
}