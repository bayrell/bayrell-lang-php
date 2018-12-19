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
namespace BayrellLang\LangES6;
use Runtime\rtl;
use Runtime\Map;
use Runtime\Vector;
use Runtime\IntrospectionInfo;
use Runtime\re;
use Runtime\rs;
use BayrellLang\CommonTranslator;
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
use BayrellLang\OpCodes\OpPreprocessorCase;
use BayrellLang\OpCodes\OpPreprocessorSwitch;
use BayrellLang\OpCodes\OpReturn;
use BayrellLang\OpCodes\OpShiftLeft;
use BayrellLang\OpCodes\OpShiftRight;
use BayrellLang\OpCodes\OpStatic;
use BayrellLang\OpCodes\OpString;
use BayrellLang\OpCodes\OpStructDeclare;
use BayrellLang\OpCodes\OpSub;
use BayrellLang\OpCodes\OpTemplateIdentifier;
use BayrellLang\OpCodes\OpTernary;
use BayrellLang\OpCodes\OpThrow;
use BayrellLang\OpCodes\OpTryCatch;
use BayrellLang\OpCodes\OpTryCatchChilds;
use BayrellLang\OpCodes\OpUse;
use BayrellLang\OpCodes\OpVector;
use BayrellLang\OpCodes\OpWhile;
use BayrellLang\LangES6\FunctionStack;
class TranslatorES6 extends CommonTranslator{
	/**
	 * Returns full class name
	 * @return string
	 */
	function getCurrentClassName(){
		return rtl::toString($this->current_namespace) . "." . rtl::toString($this->current_class_name);
	}
	/**
	 * Returns true if function is async
	 * @return bool
	 */
	function checkAwaitOpCode($op_code){
		if ($this->detectIsAwait($op_code)){
			$this->is_async_opcode = true;
		}
		else {
			$this->is_async_opcode = false;
		}
	}
	/**
	 * Returns true if function is async
	 * @return bool
	 */
	function isAsyncF(){
		if ($this->function_stack->count() == 0){
			return false;
		}
		return $this->function_stack->last()->is_async;
	}
	/**
	 * Returns true if function is async
	 * @return bool
	 */
	function isAsync(){
		if (!$this->isAsyncF()){
			return false;
		}
		return $this->is_async_opcode;
	}
	/**
	 * Returns async ctx name
	 * @return string
	 */
	function asyncContextName(){
		if (!$this->isAsyncF()){
			return "";
		}
		return $this->function_stack->last()->async_ctx;
	}
	/**
	 * Returns async jump name
	 * @return string
	 */
	function asyncJumpName(){
		if (!$this->isAsyncF()){
			return "";
		}
		return $this->function_stack->last()->async_jump;
	}
	/**
	 * Returns async jump position
	 * @return string
	 */
	function asyncJumpCurrent(){
		if (!$this->isAsyncF()){
			return "";
		}
		$obj = $this->function_stack->last();
		return $obj->getJumpPos();
	}
	/**
	 * Returns async jump position
	 * @return string
	 */
	function asyncJumpAdd($force = false){
		if (!$this->isAsync() && !$force){
			return "";
		}
		$obj = $this->function_stack->last();
		$obj->jumpAdd();
		return $obj->getJumpPos();
	}
	/**
	 * Returns next jump position
	 * @return string
	 */
	function asyncJumpNext($force = false){
		if (!$this->isAsync() && !$force){
			return "";
		}
		$obj = $this->function_stack->last();
		return $this->function_stack->last()->getJumpNext();
	}
	/**
	 * Increment jump position's level 
	 */
	function asyncJumpPush($force = false){
		if (!$this->isAsync() && !$force){
			return ;
		}
		$obj = $this->function_stack->last();
		$this->function_stack->last()->jumpPush();
	}
	/**
	 * Decrement jump position's level
	 */
	function asyncJumpPop($force = false){
		if (!$this->isAsync() && !$force){
			return ;
		}
		$obj = $this->function_stack->last();
		$this->function_stack->last()->jumpPop();
	}
	/**
	 * Push stop jump positions for break and continue
	 */
	function asyncPushStop($start_pos, $end_pos){
		if (!$this->isAsync()){
			return ;
		}
		$obj = $this->function_stack->last();
		$obj->stopPush($start_pos, $end_pos);
	}
	/**
	 * Pop stop jump positions
	 */
	function asyncPopStop(){
		if (!$this->isAsync()){
			return ;
		}
		$obj = $this->function_stack->last();
		$obj->stopPop();
	}
	/**
	 * Returns begin async position
	 * @return string
	 */
	function asyncBeginPos(){
		if (!$this->isAsync()){
			return ;
		}
		$obj = $this->function_stack->last();
		return $obj->getAsyncBeginPos();
	}
	/**
	 * Returns end async position
	 * @return string
	 */
	function asyncEndPos(){
		if (!$this->isAsync()){
			return ;
		}
		$obj = $this->function_stack->last();
		return $obj->getAsyncEndPos();
	}
	/**
	 * Push function
	 * @string name - Function name
	 */
	function functionPush($name, $is_async){
		$obj = new FunctionStack();
		$obj->name = $name;
		$obj->is_async = $is_async;
		if ($is_async){
			$obj->async_ctx = "async_ctx_" . rtl::toString($this->function_stack->count());
			$obj->async_jump = "async_jump_" . rtl::toString($this->function_stack->count());
			$obj->async_jump_pos->push(0);
		}
		$this->function_stack->push($obj);
	}
	/**
	 * Pop function
	 */
	function functionPop(){
		$this->function_stack->pop();
	}
	/**
	 * Get name
	 */
	function getName($name){
		if ($name == "parent"){
			return "super";
		}
		else if ($name == "self"){
			return rtl::toString($this->current_namespace) . "." . rtl::toString($this->current_class_name);
		}
		else if ($this->modules->has($name)){
			return $this->modules->item($name);
		}
		return $name;
	}
	/**
	 * Get module name
	 * @param string name
	 * @return string
	 */
	function getModuleName($name){
		if ($this->modules->has($name)){
			return $this->modules->item($name);
		}
		return $name;
	}
	/**
	 * Constructor
	 */
	function __construct($context = null){
		parent::__construct($context);
		$this->modules = new Map();
	}
	/**
	 * Escape string
	 */
	function escapeString($s){
		$s = re::replace("\\\\", "\\\\", $s);
		$s = re::replace("\"", "\\\"", $s);
		$s = re::replace("\n", "\\n", $s);
		$s = re::replace("\r", "\\r", $s);
		$s = re::replace("\t", "\\t", $s);
		return $s;
	}
	/**
	 * Escape string
	 */
	function convertString($s){
		return "\"" . rtl::toString($this->escapeString($s)) . "\"";
	}
	/**
	 * Comment
	 */
	function OpComment($op_code){
		return "/*" . rtl::toString($op_code->value) . "*/";
	}
	/** =========================== Identifier ============================ */
	/**
	 * HexNumber
	 */
	function OpHexNumber($op_code){
		$this->current_opcode_level = $this->max_opcode_level;
		return $op_code->value;
	}
	/**
	 * Identifier
	 */
	function OpIdentifier($op_code){
		$this->current_opcode_level = $this->max_opcode_level;
		return $this->getName($op_code->value);
	}
	/**
	 * Number
	 */
	function OpNumber($op_code){
		$this->current_opcode_level = $this->max_opcode_level;
		return $op_code->value;
	}
	/**
	 * String
	 */
	function OpString($op_code){
		$this->current_opcode_level = $this->max_opcode_level;
		return $this->convertString($op_code->value);
	}
	/**
	 * OpStringItem
	 */
	function OpStringItem($op_code){
		return rtl::toString($this->translateRun($op_code->value1)) . "[" . rtl::toString($this->s($this->translateRun($op_code->value2))) . rtl::toString($this->s("]"));
	}
	/** ======================== Dynamic or static ======================== */
	/**
	 * Dynamic load
	 */
	function OpDynamic($op_code){
		$res = rtl::toString($this->o($this->translateRun($op_code->value), $this->max_opcode_level)) . "." . rtl::toString($op_code->name);
		$this->current_opcode_level = $this->max_opcode_level;
		return $res;
	}
	/**
	 * Static load
	 */
	function OpStatic($op_code){
		return rtl::toString($this->translateRun($op_code->value)) . "." . rtl::toString($op_code->name);
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
		$this->current_opcode_level = 16;
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
		$res = "";
		if ($op_code->value1 instanceof OpConcat || $op_code->value1 instanceof OpString){
			$res .= $this->o($this->s($this->translateRun($op_code->value1)), 13);
		}
		else {
			$res .= rtl::toString($this->getName("rtl")) . ".toString(" . rtl::toString($this->s($this->translateRun($op_code->value1))) . ")";
		}
		$res .= $this->s("+");
		if ($op_code->value2 instanceof OpConcat || $op_code->value2 instanceof OpString){
			$res .= $this->o($this->s($this->translateRun($op_code->value2)), 13);
		}
		else {
			$res .= rtl::toString($this->getName("rtl")) . ".toString(" . rtl::toString($this->s($this->translateRun($op_code->value2))) . ")";
		}
		$this->current_opcode_level = 13;
		return $res;
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
		$s = "";
		/* Function name */
		$s .= "new " . rtl::toString($this->translateRun($op_code->value));
		/* Call arguments */
		$this->current_opcode_level = $this->max_opcode_level;
		$old_is_operation = $this->is_operation;
		$this->is_operation = true;
		$s .= "(";
		if ($op_code->args != null){
			$ch = "";
			for ($i = 0; $i < $op_code->args->count(); $i++){
				$op = $op_code->args->item($i);
				$s .= rtl::toString($ch) . rtl::toString($this->s($this->translateRun($op)));
				$ch = ", ";
			}
		}
		$s .= ")";
		$this->is_operation = $old_is_operation;
		$this->current_opcode_level = 19;
		return $s;
	}
	/**
	 * Not
	 */
	function OpNot($op_code){
		$res = "!" . rtl::toString($this->o($this->translateRun($op_code->value), 16));
		$this->current_opcode_level = 16;
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
		$semicolon = ($this->is_operation) ? ("") : (";");
		$res = rtl::toString($this->o($this->translateRun($op_code->value), 17)) . "--" . rtl::toString($semicolon);
		$this->current_opcode_level = 17;
		return $res;
	}
	/**
	 * Post increment
	 */
	function OpPostInc($op_code){
		$semicolon = ($this->is_operation) ? ("") : (";");
		$res = rtl::toString($this->o($this->translateRun($op_code->value), 17)) . "++" . rtl::toString($semicolon);
		$this->current_opcode_level = 17;
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
		$semicolon = ($this->is_operation) ? ("") : (";");
		$res = "--" . rtl::toString($this->o($this->translateRun($op_code->value), 16)) . rtl::toString($semicolon);
		$this->current_opcode_level = 16;
		return $res;
	}
	/**
	 * Pre increment
	 */
	function OpPreInc($op_code){
		$semicolon = ($this->is_operation) ? ("") : (";");
		$res = "++" . rtl::toString($this->o($this->translateRun($op_code->value), 16)) . rtl::toString($semicolon);
		$this->current_opcode_level = 16;
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
		$this->pushOneLine(true);
		/* Function name */
		$f = true;
		if ($op_code->value instanceof OpIdentifier){
			if ($op_code->value->value == "parent" && $this->function_stack->get(0)->name != "constructor"){
				$s .= "super." . rtl::toString($this->function_stack->get(0)->name);
				$f = false;
			}
		}
		if ($f){
			$old_is_operation = $this->is_operation;
			$this->is_operation = true;
			$s .= $this->translateRun($op_code->value);
			$this->is_operation = $old_is_operation;
		}
		$this->current_opcode_level = $this->max_opcode_level;
		$old_is_operation = $this->is_operation;
		$this->is_operation = true;
		$s .= "(";
		if ($op_code->args != null){
			$ch = "";
			for ($i = 0; $i < $op_code->args->count(); $i++){
				$op = $op_code->args->item($i);
				$s .= rtl::toString($ch) . rtl::toString($this->s($this->translateRun($op)));
				$ch = ", ";
			}
		}
		$s .= ")";
		$this->is_operation = $old_is_operation;
		/* semicolon */
		$this->popOneLine();
		if (!$this->is_operation){
			$s .= ";";
		}
		$this->current_opcode_level = $this->max_opcode_level;
		if ($this->isAsyncF() && !$this->is_async_await_op_call && $op_code->is_await){
			$old_async_opcode = $this->is_async_opcode;
			$this->is_async_opcode = true;
			$res = "";
			$jump_pos_next = $this->asyncJumpNext();
			$res .= rtl::toString($this->asyncContextName()) . ".jump(" . rtl::toString($this->convertString($jump_pos_next)) . ");";
			$res .= $this->s("return " . rtl::toString($s));
			$this->levelDec();
			$res .= $this->s("}");
			$res .= $this->s("else if (" . rtl::toString($this->asyncJumpName()) . " == " . rtl::toString($this->convertString($jump_pos_next)) . "){");
			$this->levelInc();
			$this->asyncJumpAdd();
			$this->is_async_opcode = $old_async_opcode;
			return $res;
		}
		return $s;
	}
	/**
	 * Operator call function
	 */
	function OpCompare($op_code){
		if ($op_code->condition == "implements"){
			return rtl::toString($this->getName("rtl")) . ".implements(" . rtl::toString($this->translateRun($op_code->value1)) . ", " . rtl::toString($this->s($this->translateRun($op_code->value2))) . ")";
		}
		$this->current_opcode_level = 10;
		return $this->op($op_code, $op_code->condition, 10);
	}
	/**
	 * Operator call function
	 */
	function OpTernary($op_code){
		$semicolon = ($this->is_operation) ? ("") : (";");
		$res = "(" . rtl::toString($this->translateRun($op_code->condition)) . ") ? " . "(" . rtl::toString($this->s($this->translateRun($op_code->if_true))) . ") : " . "(" . rtl::toString($this->s($this->translateRun($op_code->if_false))) . ")";
		$this->current_opcode_level = 4;
		return $res;
	}
	/** ========================== Vector and Map ========================= */
	/**
	 * Vector
	 */
	function OpVector($op_code){
		$res = "";
		$res .= "(new " . rtl::toString($this->getName("Vector")) . "())";
		for ($i = 0; $i < $op_code->values->count(); $i++){
			$item = $op_code->values->item($i);
			$this->current_opcode_level = $this->max_opcode_level;
			$res .= $this->s(".push(" . rtl::toString($this->translateRun($item)) . ")");
		}
		$this->current_opcode_level = $this->max_opcode_level;
		return $res;
	}
	/**
	 * Map
	 */
	function OpMap($op_code){
		$res = "";
		$keys = $op_code->values->keys();
		$res .= "(new " . rtl::toString($this->getName("Map")) . "())";
		for ($i = 0; $i < $keys->count(); $i++){
			$key = $keys->item($i);
			$item = $op_code->values->item($key);
			$this->current_opcode_level = $this->max_opcode_level;
			$res .= $this->s(".set(" . rtl::toString(rs::json_encode($key)) . ", " . rtl::toString($this->translateRun($item)) . ")");
		}
		$this->current_opcode_level = $this->max_opcode_level;
		return $res;
	}
	/**
	 * Clone
	 */
	function OpMethod($op_code){
		if ($op_code->value instanceof OpDynamic){
			$name = $op_code->value->name;
			$obj = $this->translateRun($op_code->value->value);
			if ($obj == "this"){
				return rtl::toString($obj) . "." . rtl::toString($name) . ".bind(this)";
			}
			return rtl::toString($obj) . "." . rtl::toString($name);
		}
		return $this->translateRun($op_code->value);
	}
	/**
	 * Class name
	 */
	function OpClassName($op_code){
		return $this->convertString($this->modules->get($op_code->value, ""));
	}
	/** ============================ Operators ============================ */
	/**
	 * Assign
	 */
	function OpAssignAwait($op_code){
		$res = "";
		$s = "";
		$this->is_async_await_op_call = true;
		$old_is_operation = $this->beginOperation();
		$this->current_opcode_level = 0;
		$this->levelInc();
		$s = $this->s($this->translateRun($op_code->value));
		$this->levelDec();
		$this->endOperation($old_is_operation);
		$jump_pos_next = $this->asyncJumpNext(true);
		$res .= rtl::toString($this->asyncContextName()) . ".jump(" . rtl::toString($this->convertString($jump_pos_next)) . ");";
		$res .= $this->s("return " . rtl::toString($s) . ";");
		$this->levelDec();
		$res .= $this->s("}");
		$res .= $this->s("else if (" . rtl::toString($this->asyncJumpName()) . " == " . rtl::toString($this->convertString($jump_pos_next)) . "){");
		$this->levelInc();
		$old_is_operation = $this->beginOperation();
		$this->pushOneLine(true);
		if ($op_code instanceof OpAssign){
			$s = $this->translateRun($op_code->ident);
			if ($op_code->op_name == "="){
				$s .= " = ";
			}
			else if ($op_code->op_name == "~="){
				$s .= " += ";
			}
			else if ($op_code->op_name == "+="){
				$s .= " += ";
			}
			else if ($op_code->op_name == "-="){
				$s .= " -= ";
			}
		}
		else if ($op_code instanceof OpAssignDeclare){
			$s = rtl::toString($op_code->name) . " = ";
		}
		$s .= rtl::toString($this->asyncContextName()) . ".result();";
		$this->popOneLine();
		$this->endOperation($old_is_operation);
		$this->asyncJumpAdd(true);
		$res .= $this->s($s);
		$this->is_async_await_op_call = false;
		return $res;
	}
	/**
	 * Assign
	 */
	function OpAssign($op_code){
		$is_async_f = $this->isAsyncF();
		$is_async = false;
		if ($is_async_f){
			if ($op_code->value instanceof OpCall){
				if ($op_code->value->is_await){
					$is_async = true;
				}
			}
		}
		if ($is_async){
			return $this->OpAssignAwait($op_code);
		}
		$old_is_operation = $this->beginOperation();
		/* one line */
		$this->pushOneLine(true);
		$res = $this->translateRun($op_code->ident);
		$this->popOneLine();
		if ($op_code->op_name == "="){
			$res .= " = ";
		}
		else if ($op_code->op_name == "~="){
			$res .= " += ";
		}
		else if ($op_code->op_name == "+="){
			$res .= " += ";
		}
		else if ($op_code->op_name == "-="){
			$res .= " -= ";
		}
		$this->current_opcode_level = 0;
		$this->levelInc();
		$res .= $this->s($this->translateRun($op_code->value));
		$this->levelDec();
		if (!$old_is_operation){
			$res .= $this->s(";");
		}
		$this->endOperation($old_is_operation);
		return $res;
	}
	/**
	 * Assign declare
	 */
	function OpAssignDeclare($op_code){
		$is_async_f = $this->isAsyncF();
		$is_async = false;
		if ($is_async_f){
			if ($op_code->value != null && $op_code->value instanceof OpCall){
				if ($op_code->value->is_await){
					$is_async = true;
				}
			}
		}
		if ($is_async){
			return $this->OpAssignAwait($op_code);
		}
		$res = "";
		$old_is_operation = $this->beginOperation();
		if ($op_code->value == null){
			if ($is_async_f){
				$this->endOperation($old_is_operation);
				return "";
			}
			$this->pushOneLine(true);
			$res = "var " . rtl::toString($op_code->name);
			$this->popOneLine();
		}
		else {
			$this->pushOneLine(true);
			$res = rtl::toString((!$is_async_f) ? ("var ") : ("")) . rtl::toString($op_code->name) . " = ";
			$this->popOneLine();
			$this->current_opcode_level = 0;
			$this->levelInc();
			$res .= $this->s($this->translateRun($op_code->value));
			$this->levelDec();
		}
		if (!$old_is_operation){
			$res .= $this->s(";");
		}
		$this->endOperation($old_is_operation);
		return $res;
	}
	/**
	 * Break
	 */
	function OpBreak($op_code){
		if ($this->isAsync()){
			$this->is_return = true;
			$pos = $this->asyncEndPos();
			return "return " . rtl::toString($this->asyncContextName()) . ".jump(" . rtl::toString($this->convertString($pos)) . ");";
		}
		return "break;";
	}
	/**
	 * Clone
	 */
	function OpClone($op_code){
		$old_is_operation = $this->beginOperation();
		/* result */
		$s = rtl::toString($this->getName("rtl")) . "._clone(";
		$this->current_opcode_level = 0;
		$s .= $this->s($this->translateRun($op_code->value));
		$s .= $this->s(")");
		if (!$old_is_operation){
			$res .= $this->s(";");
		}
		$this->endOperation($old_is_operation);
		return $s;
	}
	/**
	 * Continue
	 */
	function OpContinue($op_code){
		if ($this->isAsync()){
			$this->is_return = true;
			$pos = $this->asyncBeginPos();
			return "return " . rtl::toString($this->asyncContextName()) . ".jump(" . rtl::toString($this->convertString($pos)) . ");";
		}
		return "continue;";
	}
	/**
	 * Delete
	 */
	function OpDelete($op_code){
		return "";
	}
	/**
	 * For
	 */
	function OpFor($op_code){
		$s = "";
		$jump_pos_begin = "";
		$jump_pos_end = "";
		$jump_pos_childs = "";
		/* Check await op_code*/
		$this->checkAwaitOpCode($op_code);
		/* Async start */
		$jump_pos_begin = $this->asyncJumpCurrent();
		$jump_pos_end = $this->asyncJumpNext();
		$this->asyncJumpPush();
		$jump_pos_childs = $this->asyncJumpCurrent();
		/* Push stop jump positions for break and continue */
		$this->asyncPushStop($jump_pos_begin, $jump_pos_end);
		/* Header */
		if ($this->isAsync()){
			$s .= $this->translateRun($op_code->loop_init);
			$this->asyncJumpAdd();
			$jump_pos_begin = $this->asyncJumpCurrent();
			$s .= $this->s("return " . rtl::toString($this->asyncContextName()) . ".jump(" . rtl::toString($this->convertString($jump_pos_begin)) . ");");
			$this->levelDec();
			$s .= $this->s("}");
			$s .= $this->s("else if (" . rtl::toString($this->asyncJumpName()) . " == " . rtl::toString($this->convertString($jump_pos_begin)) . "){");
			$this->levelInc();
			$this->beginOperation();
			$s1 = "if (" . rtl::toString($this->translateRun($op_code->loop_condition)) . "){";
			$this->endOperation();
			$s .= $this->s($s1);
		}
		else {
			$this->beginOperation();
			$s .= "for (" . rtl::toString($this->translateRun($op_code->loop_init)) . "; " . rtl::toString($this->translateRun($op_code->loop_condition)) . "; " . rtl::toString($this->translateRun($op_code->loop_inc)) . "){";
			$this->endOperation();
		}
		$op_code_childs = "";
		if (!$this->isAsync()){
			$this->levelInc();
		}
		for ($i = 0; $i < $op_code->childs->count(); $i++){
			$op_code_childs .= $this->s($this->translateRun($op_code->childs->item($i)));
		}
		if ($this->isAsync()){
			$op_code_childs .= $this->s($this->translateRun($op_code->loop_inc));
		}
		if (!$this->isAsync()){
			$this->levelDec();
		}
		if ($this->isAsync()){
			$this->levelInc();
			$s .= $this->s("return " . rtl::toString($this->asyncContextName()) . ".jump(" . rtl::toString($this->convertString($jump_pos_childs)) . ");");
			$this->levelDec();
		}
		else {
			$s .= $op_code_childs;
		}
		$s .= $this->s("}");
		/* Async jump code */
		if ($this->isAsync()){
			$s .= $this->s("return " . rtl::toString($this->asyncContextName()) . ".jump(" . rtl::toString($this->convertString($jump_pos_end)) . ");");
			if ($jump_pos_childs != ""){
				$this->levelDec();
				$s .= $this->s("}");
				$s .= $this->s("else if (" . rtl::toString($this->asyncJumpName()) . " == " . rtl::toString($this->convertString($jump_pos_childs)) . "){");
				$this->levelInc();
				$s .= $op_code_childs;
				$s .= $this->s("return " . rtl::toString($this->asyncContextName()) . ".jump(" . rtl::toString($this->convertString($jump_pos_begin)) . ");");
			}
			$this->levelDec();
			$s .= $this->s("}");
			$s .= $this->s("else if (" . rtl::toString($this->asyncJumpName()) . " == " . rtl::toString($this->convertString($jump_pos_end)) . "){");
			$this->levelInc();
		}
		$this->asyncPopStop();
		$this->asyncJumpPop();
		$this->asyncJumpAdd();
		return $s;
	}
	/**
	 * If
	 */
	function OpIf($op_code){
		$s = "";
		$old_is_return = $this->is_return;
		$jump_pos_true = "";
		$jump_pos_false = "";
		$jump_pos_begin = "";
		$jump_pos_end = "";
		$is_return_true = false;
		$is_return_false = false;
		$op_code_true = "";
		$op_code_false = "";
		$op_code_else = new Vector();
		/* Check await op_code*/
		$this->checkAwaitOpCode($op_code);
		/* Condition */
		$this->beginOperation();
		$s .= "if (" . rtl::toString($this->translateRun($op_code->condition)) . "){";
		$this->endOperation();
		/* Increase level */
		$jump_pos_end = $this->asyncJumpNext();
		$this->asyncJumpPush();
		$jump_pos_begin = $this->asyncJumpCurrent();
		/* If true */
		$this->is_return = false;
		$jump_pos_true = $this->asyncJumpAdd();
		if (!$this->isAsync()){
			$this->levelInc();
		}
		for ($i = 0; $i < $op_code->if_true->count(); $i++){
			$op_code_true .= $this->s($this->translateRun($op_code->if_true->item($i)));
		}
		if (!$this->isAsync()){
			$this->levelDec();
		}
		if ($this->isAsync()){
			$is_return_true = $this->is_return;
			$this->levelInc();
			$s .= $this->s("return " . rtl::toString($this->asyncContextName()) . ".jump(" . rtl::toString($this->convertString($jump_pos_true)) . ");");
			$this->levelDec();
		}
		else {
			$s .= $op_code_true;
		}
		$s .= $this->s("}");
		/* If else */
		for ($i = 0; $i < $op_code->if_else->count(); $i++){
			$if_else = $op_code->if_else->item($i);
			$this->beginOperation();
			$res = "else if (" . rtl::toString($this->translateRun($if_else->condition)) . "){";
			$this->endOperation();
			$s .= $this->s($res);
			$this->is_return = false;
			$jump_pos = $this->asyncJumpAdd();
			$code_else = "";
			if (!$this->isAsync()){
				$this->levelInc();
			}
			for ($j = 0; $j < $if_else->if_true->count(); $j++){
				$code_else .= $this->s($this->translateRun($if_else->if_true->item($j)));
			}
			if (!$this->isAsync()){
				$this->levelDec();
			}
			if ($this->isAsync()){
				$is_return = $this->is_return;
				$op_code_else->push((new Map())->set("jump_pos", $jump_pos)->set("is_return", $is_return)->set("op_code", $code_else));
				$this->levelInc();
				$s .= $this->s("return " . rtl::toString($this->asyncContextName()) . ".jump(" . rtl::toString($this->convertString($jump_pos)) . ");");
				$this->levelDec();
			}
			else {
				$s .= $code_else;
			}
			$s .= $this->s("}");
		}
		/* If false */
		if ($op_code->if_false != null){
			$this->is_return = false;
			$jump_pos_false = $this->asyncJumpAdd();
			$s .= $this->s("else {");
			if (!$this->isAsync()){
				$this->levelInc();
			}
			for ($i = 0; $i < $op_code->if_false->count(); $i++){
				$op_code_false .= $this->s($this->translateRun($op_code->if_false->item($i)));
			}
			if (!$this->isAsync()){
				$this->levelDec();
			}
			if ($this->isAsync()){
				$is_return_false = $this->is_return;
				$this->levelInc();
				$s .= $this->s("return " . rtl::toString($this->asyncContextName()) . ".jump(" . rtl::toString($this->convertString($jump_pos_false)) . ");");
				$this->levelDec();
			}
			else {
				$s .= $op_code_false;
			}
			$s .= $this->s("}");
		}
		if ($this->isAsync()){
			$s .= $this->s("return " . rtl::toString($this->asyncContextName()) . ".jump(" . rtl::toString($this->convertString($jump_pos_end)) . ");");
			if ($jump_pos_true != ""){
				$this->levelDec();
				$s .= $this->s("}");
				$s .= $this->s("else if (" . rtl::toString($this->asyncJumpName()) . " == " . rtl::toString($this->convertString($jump_pos_true)) . "){");
				$this->levelInc();
				$s .= $op_code_true;
				if (!$is_return_true){
					$s .= $this->s("return " . rtl::toString($this->asyncContextName()) . ".jump(" . rtl::toString($this->convertString($jump_pos_end)) . ");");
				}
			}
			if ($op_code_else->count() > 0){
				$op_code_else_sz = $op_code_else->count();
				for ($i = 0; $i < $op_code_else_sz; $i++){
					$item = $op_code_else->item($i);
					$jump_pos = $item->get("jump_pos", -1, "int");
					$is_return = $item->get("is_return", false, "bool");
					$op_code = $item->get("op_code", "", "string");
					$this->levelDec();
					$s .= $this->s("}");
					$s .= $this->s("else if (" . rtl::toString($this->asyncJumpName()) . " == " . rtl::toString($this->convertString($jump_pos)) . "){");
					$this->levelInc();
					$s .= $op_code;
					if (!$is_return){
						$s .= $this->s("return " . rtl::toString($this->asyncContextName()) . ".jump(" . rtl::toString($this->convertString($jump_pos_end)) . ");");
					}
				}
			}
			if ($jump_pos_false != ""){
				$this->levelDec();
				$s .= $this->s("}");
				$s .= $this->s("else if (" . rtl::toString($this->asyncJumpName()) . " == " . rtl::toString($this->convertString($jump_pos_false)) . "){");
				$this->levelInc();
				$s .= $op_code_false;
				if (!$is_return_false){
					$s .= $this->s("return " . rtl::toString($this->asyncContextName()) . ".jump(" . rtl::toString($this->convertString($jump_pos_end)) . ");");
				}
			}
			$this->levelDec();
			$s .= $this->s("}");
			$s .= $this->s("else if (" . rtl::toString($this->asyncJumpName()) . " == " . rtl::toString($this->convertString($jump_pos_end)) . "){");
			$this->levelInc();
		}
		$this->asyncJumpPop();
		$this->asyncJumpAdd();
		$this->is_return = $old_is_return;
		return $s;
	}
	/**
	 * Return
	 */
	function OpReturn($op_code){
		$this->beginOperation();
		/* result */
		$this->current_opcode_level = 0;
		$this->levelInc();
		$s = $this->s($this->translateRun($op_code->value));
		$this->levelDec();
		if ($this->isAsyncF()){
			$s = "return " . rtl::toString($this->asyncContextName()) . ".resolve(" . rtl::toString($s) . ");";
			$this->is_return = true;
		}
		else {
			$s = "return " . rtl::toString($s) . ";";
		}
		$this->endOperation();
		return $s;
	}
	/**
	 * Throw
	 */
	function OpThrow($op_code){
		$this->beginOperation();
		/* result */
		$s = "throw ";
		$this->current_opcode_level = 0;
		$s .= $this->s($this->translateRun($op_code->value));
		$s .= ";";
		$this->endOperation();
		return $s;
	}
	/**
	 * Try Catch
	 */
	function OpTryCatch($op_code){
		$s = "";
		/* Check await op_code*/
		$this->checkAwaitOpCode($op_code);
		$jump_pos_catch = "";
		$jump_pos_end = "";
		if ($this->isAsync()){
		}
		else {
			$s .= "try{";
			$this->levelInc();
		}
		$op_code_try = "";
		for ($i = 0; $i < $op_code->op_try->count(); $i++){
			$op_code_try .= $this->s($this->translateRun($op_code->op_try->item($i)));
		}
		$jump_pos_catch = $this->asyncJumpNext();
		$this->asyncJumpAdd();
		$jump_pos_end = $this->asyncJumpNext();
		if ($this->isAsync()){
			$s .= rtl::toString($this->asyncContextName()) . ".catchPush(" . rtl::toString($this->convertString($jump_pos_catch)) . ");";
			$s .= $op_code_try;
			$s .= $this->s("return " . rtl::toString($this->asyncContextName()) . ".jump(" . rtl::toString($this->convertString($jump_pos_end)) . ");");
			$this->levelDec();
			$s .= $this->s("}");
			$s .= $this->s("else if (" . rtl::toString($this->asyncJumpName()) . " == " . rtl::toString($this->convertString($jump_pos_catch)) . "){");
			$this->levelInc();
			$this->asyncJumpAdd();
		}
		else {
			$s .= $op_code_try;
			$this->levelDec();
			$s .= $this->s("}");
		}
		$is_else = "";
		$try_catch_childs_sz = $op_code->childs->count();
		if (!$this->isAsync()){
			$s .= "catch(_the_exception){";
		}
		else {
			$s .= $this->s(rtl::toString($this->asyncContextName()) . ".catchPop();");
			$s .= $this->s("var _the_exception = " . rtl::toString($this->asyncContextName()) . ".getError();");
		}
		for ($i = 0; $i < $try_catch_childs_sz; $i++){
			$try_catch = $op_code->childs->item($i);
			$this->beginOperation();
			$tp = $this->translateRun($try_catch->op_type);
			$name = $this->translateRun($try_catch->op_ident);
			$this->endOperation();
			if ($tp == "var"){
				$tp = "Error";
			}
			if (!$this->isAsync()){
				$this->levelInc();
			}
			$s .= $this->s(rtl::toString($is_else) . "if (_the_exception instanceof " . rtl::toString($tp) . "){");
			$this->levelInc();
			$s .= $this->s("var " . rtl::toString($name) . " = _the_exception;");
			for ($j = 0; $j < $try_catch->childs->count(); $j++){
				$s .= $this->s($this->translateRun($try_catch->childs->item($j)));
			}
			$this->levelDec();
			$s .= $this->s("}");
			if (!$this->isAsync()){
				$this->levelDec();
			}
			$is_else = "else";
		}
		if ($try_catch_childs_sz > 0){
			if ($this->isAsync()){
				$s .= $this->s("else {  return " . rtl::toString($this->asyncContextName()) . ".error( _the_exception );  }");
			}
			else {
				$this->levelInc();
				$s .= $this->s("else { throw _the_exception; }");
				$this->levelDec();
			}
		}
		if ($this->isAsync()){
			$s .= $this->s(rtl::toString($this->asyncContextName()) . ".clearError();");
			$s .= $this->s("return " . rtl::toString($this->asyncContextName()) . ".jump(" . rtl::toString($this->convertString($jump_pos_end)) . ");");
			$this->levelDec();
			$s .= $this->s("}");
			$s .= $this->s("else if (" . rtl::toString($this->asyncJumpName()) . " == " . rtl::toString($this->convertString($jump_pos_end)) . "){");
			$this->levelInc();
			$this->asyncJumpAdd();
		}
		else {
			$s .= $this->s("}");
		}
		return $s;
	}
	/**
	 * While
	 */
	function OpWhile($op_code){
		$s = "";
		$jump_pos_begin = "";
		$jump_pos_end = "";
		$jump_pos_childs = "";
		/* Check await op_code*/
		$this->checkAwaitOpCode($op_code);
		$jump_pos_begin = $this->asyncJumpCurrent();
		$jump_pos_end = $this->asyncJumpNext();
		$this->asyncJumpPush();
		$jump_pos_childs = $this->asyncJumpCurrent();
		/* Push stop jump positions for break and continue */
		$this->asyncPushStop($jump_pos_begin, $jump_pos_end);
		/* Condition */
		$this->beginOperation();
		if ($this->isAsync()){
			$s .= "if (" . rtl::toString($this->translateRun($op_code->condition)) . "){";
		}
		else {
			$s .= "while (" . rtl::toString($this->translateRun($op_code->condition)) . "){";
		}
		$this->endOperation();
		/* Childs */
		$op_code_childs = "";
		if (!$this->isAsync()){
			$this->levelInc();
		}
		for ($i = 0; $i < $op_code->childs->count(); $i++){
			$op_code_childs .= $this->s($this->translateRun($op_code->childs->item($i)));
		}
		if (!$this->isAsync()){
			$this->levelDec();
		}
		if ($this->isAsync()){
			$this->levelInc();
			$s .= $this->s("return " . rtl::toString($this->asyncContextName()) . ".jump(" . rtl::toString($this->convertString($jump_pos_childs)) . ");");
			$this->levelDec();
		}
		else {
			$s .= $op_code_childs;
		}
		$s .= $this->s("}");
		/* Async jump code */
		if ($this->isAsync()){
			$s .= $this->s("return " . rtl::toString($this->asyncContextName()) . ".jump(" . rtl::toString($this->convertString($jump_pos_end)) . ");");
			if ($jump_pos_childs != ""){
				$this->levelDec();
				$s .= $this->s("}");
				$s .= $this->s("else if (" . rtl::toString($this->asyncJumpName()) . " == " . rtl::toString($this->convertString($jump_pos_childs)) . "){");
				$this->levelInc();
				$s .= $op_code_childs;
				$s .= $this->s("return " . rtl::toString($this->asyncContextName()) . ".jump(" . rtl::toString($this->convertString($jump_pos_begin)) . ");");
			}
			$this->levelDec();
			$s .= $this->s("}");
			$s .= $this->s("else if (" . rtl::toString($this->asyncJumpName()) . " == " . rtl::toString($this->convertString($jump_pos_end)) . "){");
			$this->levelInc();
		}
		$this->asyncPopStop();
		$this->asyncJumpPop();
		$this->asyncJumpAdd();
		return $s;
	}
	/** ======================== Namespace and use ======================== */
	/**
	 * Namespace
	 */
	function OpNamespace($op_code){
		$this->current_namespace = $op_code->value;
		$arr = rs::explode(".", $this->current_namespace);
		$this->current_module_name = $arr->item(0);
		$this->modules->clear();
		if ($this->current_module_name != "Runtime"){
			$this->modules->set("rtl", "Runtime.rtl");
			$this->modules->set("Map", "Runtime.Map");
			$this->modules->set("Vector", "Runtime.Vector");
			$this->modules->set("IntrospectionInfo", "Runtime.IntrospectionInfo");
		}
		return "";
	}
	/**
	 * Use
	 */
	function OpUse($op_code){
		$lib_name = $op_code->value;
		$arr = rs::explode(".", $lib_name);
		$class_name = $arr->getLastItem("");
		if ($op_code->alias_name != ""){
			$this->modules->set($op_code->alias_name, $lib_name);
		}
		else if ($class_name != ""){
			$this->modules->set($class_name, $lib_name);
		}
		return "";
	}
	/** ============================= Classes ============================= */
	/**
	 * Returns all declare variables for async function
	 * @param BaseOpCode op_code
	 * @param Vector<string> variables
	 */
	function detectAsyncDeclareVariables($op_code, $variables){
		if ($op_code instanceof OpAssignDeclare){
			$variables->push($op_code->name);
		}
		else if ($op_code instanceof OpFunctionDeclare){
			for ($i = 0; $i < $op_code->childs->count(); $i++){
				$this->detectAsyncDeclareVariables($op_code->childs->item($i), $variables);
			}
		}
		else if ($op_code instanceof OpFor){
			for ($i = 0; $i < $op_code->childs->count(); $i++){
				$this->detectAsyncDeclareVariables($op_code->childs->item($i), $variables);
			}
		}
		else if ($op_code instanceof OpIf){
			for ($i = 0; $i < $op_code->if_true->count(); $i++){
				$this->detectAsyncDeclareVariables($op_code->if_true->item($i), $variables);
			}
			for ($i = 0; $i < $op_code->if_else->count(); $i++){
				$item = $op_code->if_else->item($i);
				for ($j = 0; $j < $item->if_true->count(); $j++){
					$this->detectAsyncDeclareVariables($item->if_true->item($j), $variables);
				}
			}
			if ($op_code->if_false != null){
				for ($i = 0; $i < $op_code->if_false->count(); $i++){
					$this->detectAsyncDeclareVariables($op_code->if_false->item($i), $variables);
				}
			}
		}
		else if ($op_code instanceof OpTryCatch){
			for ($i = 0; $i < $op_code->op_try->count(); $i++){
				$this->detectAsyncDeclareVariables($op_code->op_try->item($i), $variables);
			}
		}
		else if ($op_code instanceof OpWhile){
			for ($i = 0; $i < $op_code->childs->count(); $i++){
				$this->detectAsyncDeclareVariables($op_code->childs->item($i), $variables);
			}
		}
	}
	/**
	 * Returns true if op_code contains await opCall
	 * @param BaseOpCode op_code
	 * @return bool
	 */
	function detectIsAwait($op_code){
		if ($op_code instanceof OpAssign){
			if ($op_code->value != null && $op_code->value instanceof OpCall){
				if ($op_code->value->is_await){
					return true;
				}
			}
			return false;
		}
		else if ($op_code instanceof OpAssignDeclare){
			if ($op_code->value != null && $op_code->value instanceof OpCall){
				if ($op_code->value->is_await){
					return true;
				}
			}
			return false;
		}
		else if ($op_code instanceof OpCall){
			if ($op_code->is_await){
				return true;
			}
			return false;
		}
		else if ($op_code instanceof OpFunctionDeclare){
			for ($i = 0; $i < $op_code->childs->count(); $i++){
				if ($this->detectIsAwait($op_code->childs->item($i))){
					return true;
				}
			}
		}
		else if ($op_code instanceof OpFor){
			for ($i = 0; $i < $op_code->childs->count(); $i++){
				if ($this->detectIsAwait($op_code->childs->item($i))){
					return true;
				}
			}
		}
		else if ($op_code instanceof OpIf){
			for ($i = 0; $i < $op_code->if_true->count(); $i++){
				if ($this->detectIsAwait($op_code->if_true->item($i))){
					return true;
				}
			}
			for ($i = 0; $i < $op_code->if_else->count(); $i++){
				$item = $op_code->if_else->item($i);
				for ($j = 0; $j < $item->if_true->count(); $j++){
					if ($this->detectIsAwait($item->if_true->item($j))){
						return true;
					}
				}
			}
			if ($op_code->if_false != null){
				for ($i = 0; $i < $op_code->if_false->count(); $i++){
					if ($this->detectIsAwait($op_code->if_false->item($i))){
						return true;
					}
				}
			}
		}
		else if ($op_code instanceof OpTryCatch){
			for ($i = 0; $i < $op_code->op_try->count(); $i++){
				if ($this->detectIsAwait($op_code->op_try->item($i))){
					return true;
				}
			}
		}
		else if ($op_code instanceof OpWhile){
			for ($i = 0; $i < $op_code->childs->count(); $i++){
				if ($this->detectIsAwait($op_code->childs->item($i))){
					return true;
				}
			}
		}
		return false;
	}
	/**
	 * Function header
	 */
	function OpFunctionDeclareHeader($op_code){
		$res = "";
		$ch = "";
		/* Static function */
		if ($op_code->isFlag("static")){
			$res .= "static ";
		}
		$res .= $op_code->name;
		$res .= "(";
		for ($i = 0; $i < $op_code->args->count(); $i++){
			$variable = $op_code->args->item($i);
			$this->pushOneLine(true);
			$res .= rtl::toString($ch) . rtl::toString($variable->name);
			$this->popOneLine();
			$ch = ", ";
		}
		$res .= ")";
		if ($this->function_stack->count() > 1){
			$res .= " => ";
		}
		return $res;
	}
	/**
	 * Function arrow declare
	 */
	function OpFunctionArrowDeclare($op_code){
		$res = "";
		/* Skip if declare function */
		if ($op_code->isFlag("declare")){
			return "";
		}
		$this->functionPush($op_code->name, false);
		$this->beginOperation();
		$res .= $this->OpFunctionDeclareHeader($op_code);
		$res .= "{";
		$this->endOperation();
		$this->levelInc();
		$this->pushOneLine(false);
		$res .= $this->s("return ");
		$res .= $this->OpFunctionDeclare($op_code->return_function);
		$this->popOneLine(false);
		$this->levelDec();
		$res .= $this->s("}");
		$this->functionPop();
		return $res;
	}
	/**
	 * Function declare
	 */
	function OpFunctionDeclare($op_code){
		$res = "";
		$s = "";
		/* Skip if declare function */
		if ($op_code->isFlag("declare")){
			return "";
		}
		$this->functionPush($op_code->name, $op_code->isFlag("async"));
		$res .= $this->OpFunctionDeclareHeader($op_code);
		$res .= "{";
		$this->setOperation(false);
		$this->pushOneLine(false);
		$this->levelInc();
		/* Async function */
		if ($op_code->isFlag("async")){
			$variables = new Vector();
			$this->detectAsyncDeclareVariables($op_code, $variables);
			if ($variables->count() > 0){
				$res .= $this->s("var " . rtl::toString(rs::implode(", ", $variables)) . ";");
			}
			$res .= $this->s("return (" . rtl::toString($this->asyncContextName()) . ") => {");
			$this->levelInc();
			$res .= $this->s("var " . rtl::toString($this->asyncJumpName()) . " = " . rtl::toString($this->asyncContextName()) . ".current();");
			$res .= $this->s("if (" . rtl::toString($this->asyncJumpName()) . " == " . rtl::toString($this->convertString($this->asyncJumpCurrent())) . "){");
			$this->levelInc();
		}
		for ($i = 0; $i < $op_code->args->count(); $i++){
			$variable = $op_code->args->item($i);
			if ($variable->value == null){
				continue;
			}
			$this->pushOneLine(true);
			$s = "if (" . rtl::toString($variable->name) . " == undefined) " . rtl::toString($variable->name) . "=" . rtl::toString($this->translateRun($variable->value)) . ";";
			$this->popOneLine();
			$res .= $this->s($s);
		}
		/* Childs */
		if ($op_code->childs != null){
			for ($i = 0; $i < $op_code->childs->count(); $i++){
				$res .= $this->s($this->translateRun($op_code->childs->item($i)));
			}
		}
		if ($op_code->isFlag("async")){
			$this->levelDec();
			$res .= $this->s("}");
			$res .= $this->s("else if (" . rtl::toString($this->asyncJumpName()) . " == " . rtl::toString($this->convertString("-1")) . "){");
			$this->levelInc();
			$res .= $this->s("return " . rtl::toString($this->asyncContextName()) . ".error( " . rtl::toString($this->asyncContextName()) . ".getError() )");
			$this->levelDec();
			$res .= $this->s("}");
			$res .= $this->s("else{");
			$this->levelInc();
			$res .= $this->s("return " . rtl::toString($this->asyncContextName()) . ".next();");
			$this->levelDec();
			$res .= $this->s("}");
			$res .= $this->s("return " . rtl::toString($this->asyncContextName()) . ".end();");
			$this->levelDec();
			$res .= $this->s("}");
		}
		$this->levelDec();
		$res .= $this->s("}");
		$this->popOneLine();
		$this->functionPop();
		return $res;
	}
	/**
	 * Class declare header
	 */
	function OpClassDeclareHeader($op_code){
		$s = "";
		$res = "";
		$name = "";
		$ch = "";
		$v = rs::explode(".", $this->current_namespace);
		for ($i = 0; $i < $v->count(); $i++){
			$name .= rtl::toString($ch) . rtl::toString($v->item($i));
			$s = "if (typeof " . rtl::toString($name) . " == 'undefined') " . rtl::toString($name) . " = {};";
			if ($i == 0){
				$res .= $s;
			}
			else {
				$res .= $this->s($s);
			}
			$ch = ".";
		}
		$this->beginOperation();
		$s = rtl::toString($this->current_namespace) . "." . rtl::toString($op_code->class_name) . " = class";
		if ($op_code->class_extends != ""){
			$s .= " extends " . rtl::toString($this->translateRun($op_code->class_extends));
		}
		$s .= "{";
		$this->endOperation();
		$res .= $this->s($s);
		$this->levelInc();
		return $res;
	}
	/**
	 * Class declare footer
	 */
	function OpClassDeclareFooter($op_code){
		$res = "";
		/* Static variables */
		for ($i = 0; $i < $op_code->childs->count(); $i++){
			$variable = $op_code->childs->item($i);
			if (!($variable instanceof OpAssignDeclare)){
				continue;
			}
			if ($variable->flags != null && ($variable->isFlag("static") || $variable->isFlag("const"))){
				$this->beginOperation();
				$s = rtl::toString($this->current_namespace) . "." . rtl::toString($op_code->class_name) . "." . rtl::toString($variable->name) . " = " . rtl::toString($this->translateRun($variable->value)) . ";";
				$this->endOperation();
				$res .= $this->s($s);
			}
		}
		/* Static implements */
		$class_implements = $op_code->class_implements;
		if ($class_implements != null && $class_implements->count() > 0){
			$name = rtl::toString($this->current_namespace) . "." . rtl::toString($op_code->class_name);
			$res .= $this->s(rtl::toString($name) . ".__static_implements__ = [];");
			for ($i = 0; $i < $class_implements->count(); $i++){
				$value = $class_implements->item($i);
				$res .= $this->s(rtl::toString($name) . ".__static_implements__.push(" . rtl::toString($this->getName($value)) . ")");
			}
		}
		return $res;
	}
	/**
	 * Returns declare type
	 * @return string
	 */
	function getTypeValue($tp){
		$res = "";
		while ($tp != null){
			if ($tp instanceof OpIdentifier){
				if ($res != ""){
					$res = "." . rtl::toString($res);
				}
				$res = rtl::toString($this->getModuleName($tp->value)) . rtl::toString($res);
				$tp = null;
			}
			else if ($tp instanceof OpDynamic){
				if ($res != ""){
					$res = "." . rtl::toString($res);
				}
				$res = rtl::toString($tp->name) . rtl::toString($res);
				$tp = $tp->value;
			}
			else if ($tp instanceof OpTemplateIdentifier){
				$tp = $tp->t;
			}
			else {
				$tp = null;
			}
		}
		return $res;
	}
	/**
	 * Returns declare type
	 * @return string
	 */
	function getAssignDeclareTypeValue($variable){
		return $this->getTypeValue($variable->tp);
	}
	/**
	 * Returns declare type
	 * @return string
	 */
	function getAssignDeclareTypeTemplate($variable){
		if ($variable->tp instanceof OpTemplateIdentifier){
			if ($variable->tp->childs != null){
				$code = $variable->tp->childs->get(0);
				return $this->getTypeValue($code);
			}
		}
		return "";
	}
	/**
	 * Class init functions
	 */
	function OpClassInit($op_code){
		$childs = $op_code->childs;
		$class_implements = $op_code->class_implements;
		$class_extends = "";
		if ($op_code->class_extends){
			$class_extends = $this->getName($op_code->class_extends->value);
		}
		$s = "";
		$res = "";
		$has_serializable = false;
		$has_cloneable = false;
		$has_variables = false;
		$has_implements = $class_implements != null && $class_implements->count() > 0;
		$has_methods_annotations = false;
		$has_fields_annotations = false;
		for ($i = 0; $i < $childs->count(); $i++){
			$variable = $childs->item($i);
			if ($variable instanceof OpAssignDeclare){
				if ($variable->isFlag("serializable")){
					$has_serializable = true;
					$has_cloneable = true;
				}
				if ($variable->isFlag("cloneable")){
					$has_cloneable = true;
				}
				if ($variable->isFlag("assignable")){
					$has_serializable = true;
				}
				if (!$variable->isFlag("static") && !$variable->isFlag("const")){
					$has_variables = true;
				}
				if ($variable->hasAnnotations()){
					$has_fields_annotations = true;
				}
			}
			if ($variable instanceof OpFunctionDeclare){
				if ($variable->hasAnnotations()){
					$has_methods_annotations = true;
				}
			}
		}
		if ($this->is_struct){
			$has_serializable = true;
			$has_cloneable = true;
		}
		$res .= $this->s("/* ======================= Class Init Functions ======================= */");
		if (!$this->is_interface){
			$res .= $this->s("getClassName(){" . "return " . rtl::toString($this->convertString(rtl::toString($this->current_namespace) . "." . rtl::toString($this->current_class_name))) . ";}");
			$res .= $this->s("static getParentClassName(){" . "return " . rtl::toString($this->convertString($class_extends)) . ";}");
		}
		if ($this->current_module_name != "Runtime" || $this->current_class_name != "CoreObject"){
			if ($has_variables || $has_implements){
				$res .= $this->s("_init(){");
				$this->levelInc();
				if ($class_extends != ""){
					$res .= $this->s("super._init();");
				}
				if ($childs != null){
					for ($i = 0; $i < $childs->count(); $i++){
						$variable = $childs->item($i);
						if (!($variable instanceof OpAssignDeclare)){
							continue;
						}
						if (!$variable->isFlag("static") && !$variable->isFlag("const")){
							$this->beginOperation();
							$s = "this." . rtl::toString($variable->name) . " = " . rtl::toString($this->translateRun($variable->value)) . ";";
							$this->endOperation();
							$res .= $this->s($s);
						}
					}
				}
				if ($class_implements != null && $class_implements->count() > 0){
					$res .= $this->s("if (this.__implements__ == undefined){this.__implements__ = [];}");
					for ($i = 0; $i < $class_implements->count(); $i++){
						$name = $class_implements->item($i);
						$this->beginOperation();
						$s = "this.__implements__.push(" . rtl::toString($this->getName($name)) . ");";
						$this->endOperation();
						$res .= $this->s($s);
					}
				}
				$this->levelDec();
				$res .= $this->s("}");
			}
			if ($has_cloneable){
				$res .= $this->s("assignObject(obj){");
				$this->levelInc();
				$res .= $this->s("if (obj instanceof " . rtl::toString($this->getName($this->current_class_name)) . "){");
				$this->levelInc();
				for ($i = 0; $i < $childs->count(); $i++){
					$variable = $childs->item($i);
					if (!($variable instanceof OpAssignDeclare)){
						continue;
					}
					$is_struct = $this->is_struct && !$variable->isFlag("static") && !$variable->isFlag("const");
					if ($variable->isFlag("public") && ($variable->isFlag("cloneable") || $variable->isFlag("serializable") || $is_struct)){
						$res .= $this->s("this." . rtl::toString($variable->name) . " = " . rtl::toString($this->getName("rtl")) . "._clone(" . "obj." . rtl::toString($variable->name) . ");");
					}
				}
				$this->levelDec();
				$res .= $this->s("}");
				$res .= $this->s("super.assignObject(obj);");
				$this->levelDec();
				$res .= $this->s("}");
			}
			if ($has_serializable){
				$class_variables_serializable_count = 0;
				$res .= $this->s("assignValue(variable_name, value){");
				$this->levelInc();
				$class_variables_serializable_count = 0;
				for ($i = 0; $i < $childs->count(); $i++){
					$variable = $childs->item($i);
					if (!($variable instanceof OpAssignDeclare)){
						continue;
					}
					$is_struct = $this->is_struct && !$variable->isFlag("static") && !$variable->isFlag("const");
					if ($variable->isFlag("public") && ($variable->isFlag("serializable") || $variable->isFlag("assignable") || $is_struct)){
						$type_value = $this->getAssignDeclareTypeValue($variable);
						$type_template = $this->getAssignDeclareTypeTemplate($variable);
						$def_val = "null";
						if ($variable->value != null){
							$def_val = $this->translateRun($variable->value);
						}
						$s = "if (variable_name == " . rtl::toString($this->convertString($variable->name)) . ") ";
						$s .= "this." . rtl::toString($variable->name) . " = ";
						$s .= rtl::toString($this->getName("rtl")) . ".correct(value, \"" . rtl::toString($type_value) . "\", " . rtl::toString($def_val) . ", \"" . rtl::toString($type_template) . "\");";
						if ($class_variables_serializable_count == 0){
							$res .= $this->s($s);
						}
						else {
							$res .= $this->s("else " . rtl::toString($s));
						}
						$class_variables_serializable_count++;
					}
				}
				if ($class_variables_serializable_count == 0){
					$res .= $this->s("super.assignValue(variable_name, value);");
				}
				else {
					$res .= $this->s("else super.assignValue(variable_name, value);");
				}
				$this->levelDec();
				$res .= $this->s("}");
				$res .= $this->s("takeValue(variable_name, default_value){");
				$this->levelInc();
				$res .= $this->s("if (default_value == undefined) default_value = null;");
				$class_variables_serializable_count = 0;
				for ($i = 0; $i < $childs->count(); $i++){
					$variable = $childs->item($i);
					if (!($variable instanceof OpAssignDeclare)){
						continue;
					}
					$is_struct = $this->is_struct && !$variable->isFlag("static") && !$variable->isFlag("const");
					if ($variable->isFlag("public") && ($variable->isFlag("serializable") || $variable->isFlag("assignable") || $is_struct)){
						$take_value_s = "if (variable_name == " . rtl::toString($this->convertString($variable->name)) . ") " . "return this." . rtl::toString($variable->name) . ";";
						if ($class_variables_serializable_count == 0){
							$res .= $this->s($take_value_s);
						}
						else {
							$res .= $this->s("else " . rtl::toString($take_value_s));
						}
						$class_variables_serializable_count++;
					}
				}
				$res .= $this->s("return super.takeValue(variable_name, default_value);");
				$this->levelDec();
				$res .= $this->s("}");
			}
			if ($has_serializable || $has_fields_annotations){
				$res .= $this->s("static getFieldsList(names){");
				$this->levelInc();
				for ($i = 0; $i < $childs->count(); $i++){
					$variable = $childs->item($i);
					if (!($variable instanceof OpAssignDeclare)){
						continue;
					}
					$is_struct = $this->is_struct && !$variable->isFlag("static") && !$variable->isFlag("const");
					if ($variable->isFlag("public") && ($variable->isFlag("serializable") || $is_struct || $variable->hasAnnotations())){
						$res .= $this->s("names.push(" . rtl::toString($this->convertString($variable->name)) . ");");
					}
				}
				$this->levelDec();
				$res .= $this->s("}");
				$res .= $this->s("static getFieldInfoByName(field_name){");
				$this->levelInc();
				for ($i = 0; $i < $childs->count(); $i++){
					$variable = $childs->item($i);
					if (!($variable instanceof OpAssignDeclare)){
						continue;
					}
					if ($variable->isFlag("public") && $variable->hasAnnotations()){
						$res .= $this->s("if (field_name == " . rtl::toString($this->convertString($variable->name)) . "){");
						$this->levelInc();
						$res .= $this->s("return new " . rtl::toString($this->getName("IntrospectionInfo")) . "(");
						$this->levelInc();
						$res .= $this->s("(new " . rtl::toString($this->getName("Map")) . "())");
						$res .= $this->s(".set(\"kind\", \"field\")");
						$res .= $this->s(".set(\"class_name\", " . rtl::toString($this->convertString($this->getCurrentClassName())) . ")");
						$res .= $this->s(".set(\"name\", " . rtl::toString($this->convertString($variable->name)) . ")");
						$res .= $this->s(".set(\"annotations\", ");
						$this->levelInc();
						$res .= $this->s("(new " . rtl::toString($this->getName("Vector")) . "())");
						for ($j = 0; $j < $variable->annotations->count(); $j++){
							$annotation = $variable->annotations->item($j);
							$this->pushOneLine(true);
							$s_kind = $this->translateRun($annotation->kind);
							$s_options = $this->translateRun($annotation->options);
							$this->popOneLine();
							$res .= $this->s(".push(new " . rtl::toString($s_kind) . "(" . rtl::toString($s_options) . "))");
						}
						$this->levelDec();
						$res .= $this->s(")");
						$this->levelDec();
						$res .= $this->s(");");
						$this->levelDec();
						$res .= $this->s("}");
					}
				}
				$res .= $this->s("return null;");
				$this->levelDec();
				$res .= $this->s("}");
			}
			if ($has_methods_annotations){
				$res .= $this->s("static getMethodsList(names){");
				$this->levelInc();
				for ($i = 0; $i < $childs->count(); $i++){
					$variable = $childs->item($i);
					if (!($variable instanceof OpFunctionDeclare)){
						continue;
					}
					if ($variable->isFlag("public") && $variable->hasAnnotations()){
						$res .= $this->s("names.push(" . rtl::toString($this->convertString($variable->name)) . ");");
					}
				}
				$this->levelDec();
				$res .= $this->s("}");
				$res .= $this->s("static getMethodInfoByName(method_name){");
				$this->levelInc();
				for ($i = 0; $i < $childs->count(); $i++){
					$variable = $childs->item($i);
					if (!($variable instanceof OpFunctionDeclare)){
						continue;
					}
					if ($variable->isFlag("public") && $variable->hasAnnotations()){
						$res .= $this->s("if (method_name == " . rtl::toString($this->convertString($variable->name)) . "){");
						$this->levelInc();
						$res .= $this->s("return new " . rtl::toString($this->getName("IntrospectionInfo")) . "(");
						$this->levelInc();
						$res .= $this->s("(new " . rtl::toString($this->getName("Map")) . "())");
						$res .= $this->s(".set(\"kind\", \"method\")");
						$res .= $this->s(".set(\"class_name\", " . rtl::toString($this->convertString($this->getCurrentClassName())) . ")");
						$res .= $this->s(".set(\"name\", " . rtl::toString($this->convertString($variable->name)) . ")");
						$res .= $this->s(".set(\"annotations\", ");
						$this->levelInc();
						$res .= $this->s("(new " . rtl::toString($this->getName("Vector")) . "())");
						for ($j = 0; $j < $variable->annotations->count(); $j++){
							$annotation = $variable->annotations->item($j);
							$this->pushOneLine(true);
							$s_kind = $this->translateRun($annotation->kind);
							$s_options = $this->translateRun($annotation->options);
							$this->popOneLine();
							$res .= $this->s(".push(new " . rtl::toString($s_kind) . "(" . rtl::toString($s_options) . "))");
						}
						$this->levelDec();
						$res .= $this->s(")");
						$this->levelDec();
						$res .= $this->s(");");
						$this->levelDec();
						$res .= $this->s("}");
					}
				}
				$res .= $this->s("return null;");
				$this->levelDec();
				$res .= $this->s("}");
			}
		}
		if ($op_code->hasAnnotations()){
			$res .= $this->s("static getClassInfo(){");
			$this->levelInc();
			$res .= $this->s("return new " . rtl::toString($this->getName("IntrospectionInfo")) . "(");
			$this->levelInc();
			$res .= $this->s("(new " . rtl::toString($this->getName("Map")) . "())");
			$res .= $this->s(".set(\"kind\", \"class\")");
			$res .= $this->s(".set(\"class_name\", " . rtl::toString($this->convertString($this->getCurrentClassName())) . ")");
			$res .= $this->s(".set(\"annotations\", ");
			$this->levelInc();
			$res .= $this->s("(new " . rtl::toString($this->getName("Vector")) . "())");
			for ($j = 0; $j < $op_code->annotations->count(); $j++){
				$annotation = $op_code->annotations->item($j);
				$this->pushOneLine(true);
				$s_kind = $this->translateRun($annotation->kind);
				$s_options = $this->translateRun($annotation->options);
				$this->popOneLine();
				$res .= $this->s(".push(new " . rtl::toString($s_kind) . "(" . rtl::toString($s_options) . "))");
			}
			$this->levelDec();
			$res .= $this->s(")");
			$this->levelDec();
			$res .= $this->s(");");
			$this->levelDec();
			$res .= $this->s("}");
		}
		return $res;
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
		$this->modules->set($this->current_class_name, rtl::toString($this->current_namespace) . "." . rtl::toString($this->current_class_name));
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
		$this->levelDec();
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
	/** =========================== Preprocessor ========================== */
	function calcPreprocessorCondition($op_case){
		if ($op_case->condition instanceof OpIdentifier){
			if ($op_case->condition->value == "JAVASCRIPT" || $op_case->condition->value == "ES6"){
				return true;
			}
		}
		return false;
	}
	/**
	 * Interface declare
	 */
	function OpPreprocessorSwitch($op_code){
		if ($op_code->childs == null){
			return "";
		}
		$res = "";
		for ($i = 0; $i < $op_code->childs->count(); $i++){
			$op_case = $op_code->childs->item($i);
			if ($this->calcPreprocessorCondition($op_case)){
				$res .= $this->s($op_case->value);
			}
		}
		return $res;
	}
	/**
	 * Reset translator to default settings
	 */
	function resetTranslator(){
		parent::resetTranslator();
		$this->function_stack = new Vector();
	}
	/**
	 * Translate to language
	 * @param BaseOpCode op_code - Abstract syntax tree
	 * @returns string - The result
	 */
	function translate($op_code){
		$this->resetTranslator();
		$s = "\"use strict;\"" . rtl::toString($this->crlf);
		$s .= $this->translateRun($op_code);
		return $s;
	}
	/* ======================= Class Init Functions ======================= */
	public function getClassName(){return "BayrellLang.LangES6.TranslatorES6";}
	public static function getParentClassName(){return "BayrellLang.CommonTranslator";}
	protected function _init(){
		parent::_init();
		$this->modules = null;
		$this->current_namespace = "";
		$this->current_class_name = "";
		$this->function_stack = null;
		$this->current_module_name = "";
		$this->is_interface = false;
		$this->is_struct = false;
		$this->is_return = false;
		$this->is_async_opcode = false;
		$this->is_async_await_op_call = false;
		$this->struct_read_only = false;
	}
}