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
namespace BayrellLang\LangPHP;
use Runtime\rs;
use Runtime\rtl;
use Runtime\Map;
use Runtime\Vector;
use Runtime\Dict;
use Runtime\Collection;
use Runtime\IntrospectionInfo;
use Runtime\UIStruct;
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
use BayrellLang\OpCodes\OpCopyStruct;
use BayrellLang\OpCodes\OpDelete;
use BayrellLang\OpCodes\OpDiv;
use BayrellLang\OpCodes\OpDynamic;
use BayrellLang\OpCodes\OpFlags;
use BayrellLang\OpCodes\OpFor;
use BayrellLang\OpCodes\OpFunctionArrowDeclare;
use BayrellLang\OpCodes\OpFunctionDeclare;
use BayrellLang\OpCodes\OpHexNumber;
use BayrellLang\OpCodes\OpHtmlAttribute;
use BayrellLang\OpCodes\OpHtmlComment;
use BayrellLang\OpCodes\OpHtmlEscape;
use BayrellLang\OpCodes\OpHtmlJson;
use BayrellLang\OpCodes\OpHtmlRaw;
use BayrellLang\OpCodes\OpHtmlTag;
use BayrellLang\OpCodes\OpHtmlText;
use BayrellLang\OpCodes\OpHtmlView;
use BayrellLang\OpCodes\OpIdentifier;
use BayrellLang\OpCodes\OpIf;
use BayrellLang\OpCodes\OpIfElse;
use BayrellLang\OpCodes\OpInterfaceDeclare;
use BayrellLang\OpCodes\OpMod;
use BayrellLang\OpCodes\OpMult;
use BayrellLang\OpCodes\OpNamespace;
use BayrellLang\OpCodes\OpNew;
use BayrellLang\OpCodes\OpNope;
use BayrellLang\OpCodes\OpNot;
use BayrellLang\OpCodes\OpNumber;
use BayrellLang\OpCodes\OpOr;
use BayrellLang\OpCodes\OpPipe;
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
use BayrellLang\OpCodes\OpStringItem;
use BayrellLang\OpCodes\OpStructDeclare;
use BayrellLang\OpCodes\OpSub;
use BayrellLang\OpCodes\OpTemplateIdentifier;
use BayrellLang\OpCodes\OpTernary;
use BayrellLang\OpCodes\OpThrow;
use BayrellLang\OpCodes\OpTryCatch;
use BayrellLang\OpCodes\OpTryCatchChilds;
use BayrellLang\OpCodes\OpUse;
use BayrellLang\OpCodes\OpWhile;
class TranslatorPHP extends CommonTranslator{
	public $ui_struct_class_name;
	public $modules;
	public $current_namespace;
	public $current_class_name;
	public $current_function_name;
	public $current_function_is_static;
	public $current_function_is_memorize;
	public $current_module_name;
	public $is_static;
	public $is_interface;
	public $is_struct;
	/**
	 * Returns full class name
	 * @return string
	 */
	function getCurrentClassName(){
		return rtl::toString($this->current_namespace) . "." . rtl::toString($this->current_class_name);
	}
	/**
	 * Returns full class name
	 * @return string
	 */
	function getCurrentFunctionName(){
		$c = $this->current_function_name->count();
		$last_function_name = $this->current_function_name->get($c - 1);
		return rtl::toString($this->current_namespace) . "." . rtl::toString($this->current_class_name) . "::" . rtl::toString($last_function_name);
	}
	/**
	 * Returns UI struct class name
	 * @return string
	 */
	function getUIStructClassName(){
		return $this->ui_struct_class_name->last();
	}
	/**
	 * Get name
	 */
	function getName($name){
		if ($name == "parent"){
			return "parent";
		}
		else if ($name == "self"){
			return "self";
		}
		else if ($name == "static"){
			return "static";
		}
		else if ($this->modules->has($name)){
			return $name;
		}
		else if ($this->is_static){
			return $name;
		}
		else if ($name == "null" || $name == "false" || $name == "true"){
			return $name;
		}
		return "\$" . rtl::toString($name);
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
		$s = re::replace("\\\$", "\\\$", $s);
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
	 * Array
	 */
	function OpStringItem($op_code){
		return "mb_substr(" . rtl::toString($this->translateRun($op_code->value1)) . ", " . rtl::toString($this->s($this->translateRun($op_code->value2))) . ", 1)";
	}
	/** ======================== Dynamic or static ======================== */
	/**
	 * Dynamic load
	 */
	function OpDynamic($op_code){
		$res = rtl::toString($this->o($this->translateRun($op_code->value), $this->max_opcode_level)) . "->" . rtl::toString($op_code->name);
		$this->current_opcode_level = $this->max_opcode_level;
		return $res;
	}
	/**
	 * Static load
	 */
	function OpStatic($op_code){
		$is_flag = false;
		$op_code_last = $this->op_code_stack->last(null, -2);
		if ($op_code->name == rs::strtoupper($op_code->name)){
			$is_flag = true;
		}
		if ($op_code_last instanceof OpCall){
			$is_flag = true;
		}
		if ($op_code->value instanceof OpIdentifier && $op_code_last instanceof OpCall){
			if ($op_code->value->value == "self"){
				return "(new \\Runtime\\Callback(" . "self::class, " . rtl::toString($this->convertString($op_code->name)) . "))";
			}
			else if ($op_code->value->value == "static"){
				return "static::" . rtl::toString($op_code->name);
			}
			else if ($op_code->value->value == "parent"){
				return "parent::" . rtl::toString($op_code->name);
			}
			else if (!$this->modules->has($op_code->value->value)){
				return "(new \\Runtime\\Callback(" . "\$" . rtl::toString($op_code->value->value) . "->getClassName(), " . rtl::toString($this->convertString($op_code->name)) . "))";
			}
		}
		if ($is_flag){
			return rtl::toString($this->translateRun($op_code->value)) . "::" . rtl::toString($op_code->name);
		}
		return rtl::toString($this->translateRun($op_code->value)) . "::\$" . rtl::toString($op_code->name);
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
			$res .= rtl::toString($this->getName("rtl")) . "::toString(" . rtl::toString($this->s($this->translateRun($op_code->value1))) . ")";
		}
		$res .= $this->s(" . ");
		if ($op_code->value2 instanceof OpConcat || $op_code->value2 instanceof OpString){
			$res .= $this->o($this->s($this->translateRun($op_code->value2)), 13);
		}
		else {
			$res .= rtl::toString($this->getName("rtl")) . "::toString(" . rtl::toString($this->s($this->translateRun($op_code->value2))) . ")";
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
		$res = rtl::toString($this->o($this->translateRun($op_code->value), 17)) . "--";
		$this->current_opcode_level = 17;
		if (!$this->is_operation){
			$res .= ";";
		}
		return $res;
	}
	/**
	 * Post increment
	 */
	function OpPostInc($op_code){
		$semicolon = ($this->is_operation) ? ("") : (";");
		$res = rtl::toString($this->o($this->translateRun($op_code->value), 17)) . "++";
		$this->current_opcode_level = 17;
		if (!$this->is_operation){
			$res .= ";";
		}
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
		$res = "--" . rtl::toString($this->o($this->translateRun($op_code->value), 16));
		$this->current_opcode_level = 16;
		if (!$this->is_operation){
			$res .= ";";
		}
		return $res;
	}
	/**
	 * Pre increment
	 */
	function OpPreInc($op_code){
		$semicolon = ($this->is_operation) ? ("") : (";");
		$res = "++" . rtl::toString($this->o($this->translateRun($op_code->value), 16));
		$this->current_opcode_level = 16;
		if (!$this->is_operation){
			$res .= ";";
		}
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
			if ($op_code->value->value == "parent"){
				if ($this->current_function_name->get(0) == "constructor"){
					$s .= "parent::__construct";
				}
				else if ($this->current_function_name->get(0) == "destructor"){
					$s .= "parent::__destruct";
				}
				else {
					$s .= "parent::" . rtl::toString($this->current_function_name->get(0));
				}
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
				$s .= $ch + $this->s($this->translateRun($op));
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
		return $s;
	}
	/**
	 * Operator call function
	 */
	function OpCompare($op_code){
		$this->current_opcode_level = 10;
		$condition = $op_code->condition;
		if ($condition == "implements"){
			$condition = "instanceof";
		}
		return $this->op($op_code, $condition, 10);
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
	/**
	 * Copy struct
	 */
	function copyStruct($op_code, $names){
		$old_is_operation = $this->beginOperation();
		$res = "";
		if ($op_code->item instanceof OpCopyStruct){
			$names->push($op_code->name);
			$name = "\$" . rtl::toString(rs::implode("->", $names));
			$res = rtl::toString($name) . "->copy( new Map([ " . rtl::toString($this->convertString($op_code->item->name)) . " => " . rtl::toString($this->copyStruct($op_code->item, $names)) . " ])  )";
		}
		else {
			$res = $this->translateRun($op_code->item);
		}
		$this->endOperation($old_is_operation);
		return $res;
	}
	/**
	 * Copy struct
	 */
	function OpCopyStruct($op_code){
		if ($this->is_operation){
			return $this->copyStruct($op_code, (new Vector()));
		}
		return "\$" . rtl::toString($op_code->name) . " = " . rtl::toString($this->copyStruct($op_code, (new Vector()))) . ";";
	}
	/**
	 * Pipe
	 */
	function OpPipe($op_code){
		$res = "";
		$res = "RuntimeMaybe::of(" . rtl::toString($this->translateItem($op_code->value)) . ")";
		if ($op_code->items != null){
			for ($i = 0; $i < $op_code->items->count(); $i++){
				$op_item = $op_code->items->item($i);
				$res .= $this->s("->map(" . rtl::toString($this->translateItem($op_item)) . ")");
			}
		}
		if ($op_code->is_return_value){
			$res .= $this->s("->value()");
		}
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
			$res .= $this->s("->push(" . rtl::toString($this->translateRun($item)) . ")");
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
			$res .= $this->s("->set(" . rtl::toString(rs::json_encode($key)) . ", " . rtl::toString($this->translateRun($item)) . ")");
		}
		$this->current_opcode_level = $this->max_opcode_level;
		return $res;
	}
	/**
	 * Method
	 */
	function OpMethod($op_code){
		if ($op_code->value instanceof OpDynamic){
			$name = $op_code->value->name;
			$obj = $this->translateRun($op_code->value->value);
			return "new \\Runtime\\Callback(" . rtl::toString($obj) . ", " . rtl::toString($this->convertString($name)) . ")";
		}
		else if ($op_code->value instanceof OpStatic){
			$name = $op_code->value->name;
			if ($op_code->value->value instanceof OpIdentifier){
				if ($op_code->value->value->value == "self"){
					return "(new \\Runtime\\Callback(" . "self::class, " . rtl::toString($this->convertString($name)) . "))";
				}
				else if ($op_code->value->value->value == "static"){
					return "(new \\Runtime\\Callback(" . "static::class, " . rtl::toString($this->convertString($name)) . "))";
				}
				else if ($op_code->value->value->value == "parent"){
					return $this->translateRun($op_code->value);
				}
				else if (!$this->modules->has($op_code->value->value->value)){
					return "(new \\Runtime\\Callback(" . "\$" . rtl::toString($op_code->value->value->value) . "->getClassName(), " . rtl::toString($this->convertString($name)) . "))";
				}
			}
			$obj = $this->translateRun($op_code->value->value);
			return "new \\Runtime\\Callback(" . rtl::toString($obj) . "::class, " . rtl::toString($this->convertString($name)) . ")";
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
	function OpAssignDeclare($op_code, $output_value = true){
		$res = "";
		$old_is_operation = $this->beginOperation();
		$ch_var = "\$";
		if ($op_code->isFlag("const")){
			$ch_var = "";
		}
		$var_prefix = "";
		if ($this->is_struct && $op_code->isFlag("public") && !$op_code->isFlag("static") && !$op_code->isFlag("const")){
			$var_prefix = "__";
		}
		if ($op_code->value == null || !$output_value && !$op_code->isFlag("static") && !$op_code->isFlag("const")){
			$this->pushOneLine(true);
			$res = rtl::toString($ch_var) . rtl::toString($var_prefix) . rtl::toString($op_code->name);
			$this->popOneLine();
		}
		else {
			$this->pushOneLine(true);
			$res = rtl::toString($ch_var) . rtl::toString($var_prefix) . rtl::toString($op_code->name) . " = ";
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
		return "break;";
	}
	/**
	 * Clone
	 */
	function OpClone($op_code){
		$old_is_operation = $this->beginOperation();
		/* result */
		$s = "rtl::_clone(";
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
		/* Header */
		$old_is_operation = $this->beginOperation();
		$s .= "for (" . rtl::toString($this->translateRun($op_code->loop_init)) . "; " . rtl::toString($this->translateRun($op_code->loop_condition)) . "; " . rtl::toString($this->translateRun($op_code->loop_inc)) . "){";
		$this->endOperation($old_is_operation);
		/* Childs */
		$this->levelInc();
		for ($i = 0; $i < $op_code->childs->count(); $i++){
			$s .= $this->s($this->translateRun($op_code->childs->item($i)));
		}
		$this->levelDec();
		$s .= $this->s("}");
		return $s;
	}
	/**
	 * If
	 */
	function OpIf($op_code){
		$s = "";
		/* Condition */
		$old_is_operation = $this->beginOperation();
		$s .= "if (" . rtl::toString($this->translateRun($op_code->condition)) . "){";
		$this->endOperation($old_is_operation);
		/* If true */
		$this->levelInc();
		for ($i = 0; $i < $op_code->if_true->count(); $i++){
			$s .= $this->s($this->translateRun($op_code->if_true->item($i)));
		}
		$this->levelDec();
		$s .= $this->s("}");
		/* If else */
		for ($i = 0; $i < $op_code->if_else->count(); $i++){
			$if_else = $op_code->if_else->item($i);
			$old_is_operation = $this->beginOperation();
			$res = "else if (" . rtl::toString($this->translateRun($if_else->condition)) . "){";
			$this->endOperation($old_is_operation);
			$s .= $this->s($res);
			$this->levelInc();
			for ($j = 0; $j < $if_else->if_true->count(); $j++){
				$s .= $this->s($this->translateRun($if_else->if_true->item($j)));
			}
			$this->levelDec();
			$s .= $this->s("}");
		}
		/* If false */
		if ($op_code->if_false != null){
			$s .= $this->s("else {");
			$this->levelInc();
			for ($i = 0; $i < $op_code->if_false->count(); $i++){
				$s .= $this->s($this->translateRun($op_code->if_false->item($i)));
			}
			$this->levelDec();
			$s .= $this->s("}");
		}
		return $s;
	}
	/**
	 * Return
	 */
	function OpReturn($op_code){
		$old_is_operation = $this->beginOperation();
		/* result */
		if ($this->current_function_is_memorize){
			$s = "\$__memorize_value = ";
		}
		else {
			$s = "return ";
		}
		$this->current_opcode_level = 0;
		$this->levelInc();
		$s .= $this->s($this->translateRun($op_code->value));
		$this->levelDec();
		$s .= $this->s(";");
		$this->endOperation($old_is_operation);
		if ($this->current_function_is_memorize){
			$s .= $this->s("rtl::_memorizeSave(" . rtl::toString($this->convertString($this->getCurrentFunctionName())) . ", func_get_args(), \$__memorize_value);");
			$s .= $this->s("return \$__memorize_value;");
			return $s;
		}
		else {
			return $s;
		}
	}
	/**
	 * Throw
	 */
	function OpThrow($op_code){
		$old_is_operation = $this->beginOperation();
		/* result */
		$s = "throw ";
		$this->current_opcode_level = 0;
		$s .= $this->s($this->translateRun($op_code->value));
		$s .= ";";
		$this->endOperation($old_is_operation);
		return $s;
	}
	/**
	 * Try Catch
	 */
	function OpTryCatch($op_code){
		$s = "";
		$s .= "try{";
		$this->levelInc();
		for ($i = 0; $i < $op_code->op_try->count(); $i++){
			$s .= $this->s($this->translateRun($op_code->op_try->item($i)));
		}
		$this->levelDec();
		$s .= $this->s("}");
		$try_catch_childs_sz = $op_code->childs->count();
		$is_else = "";
		$s .= "catch(\\Exception \$_the_exception){";
		for ($i = 0; $i < $try_catch_childs_sz; $i++){
			$try_catch = $op_code->childs->item($i);
			$old_is_operation = $this->beginOperation();
			$tp = $this->translateRun($try_catch->op_type);
			$name = $this->translateRun($try_catch->op_ident);
			$this->endOperation($old_is_operation);
			if ($tp == "\$var"){
				$tp = "\\Exception";
			}
			$this->levelInc();
			$s .= $this->s(rtl::toString($is_else) . "if (\$_the_exception instanceof " . rtl::toString($tp) . "){");
			$this->levelInc();
			$s .= $this->s(rtl::toString($name) . " = \$_the_exception;");
			for ($j = 0; $j < $try_catch->childs->count(); $j++){
				$s .= $this->s($this->translateRun($try_catch->childs->item($j)));
			}
			$this->levelDec();
			$s .= $this->s("}");
			$this->levelDec();
			$is_else = "else";
		}
		if ($try_catch_childs_sz > 0){
			$this->levelInc();
			$s .= $this->s("else { throw \$_the_exception; }");
			$this->levelDec();
		}
		$s .= $this->s("}");
		return $s;
	}
	/**
	 * While
	 */
	function OpWhile($op_code){
		$s = "";
		/* Condition */
		$old_is_operation = $this->beginOperation();
		$s .= "while (" . rtl::toString($this->translateRun($op_code->condition)) . "){";
		$this->endOperation($old_is_operation);
		/* Childs */
		$this->levelInc();
		for ($i = 0; $i < $op_code->childs->count(); $i++){
			$s .= $this->s($this->translateRun($op_code->childs->item($i)));
		}
		$this->levelDec();
		$s .= $this->s("}");
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
		$res = "namespace " . rtl::toString(rs::implode("\\", $arr)) . ";";
		if ($this->current_module_name != "Runtime"){
			$res .= $this->s("use Runtime\\rs;");
			$res .= $this->s("use Runtime\\rtl;");
			$res .= $this->s("use Runtime\\Map;");
			$res .= $this->s("use Runtime\\Vector;");
			$res .= $this->s("use Runtime\\Dict;");
			$res .= $this->s("use Runtime\\Collection;");
			$res .= $this->s("use Runtime\\IntrospectionInfo;");
			$res .= $this->s("use Runtime\\UIStruct;");
			$this->modules->set("rs", "Runtime.rs");
			$this->modules->set("rtl", "Runtime.rtl");
			$this->modules->set("Map", "Runtime.Map");
			$this->modules->set("Dict", "Runtime.Dict");
			$this->modules->set("Vector", "Runtime.Vector");
			$this->modules->set("Collection", "Runtime.Collection");
			$this->modules->set("IntrospectionInfo", "Runtime.IntrospectionInfo");
			$this->modules->set("UIStruct", "Runtime.UIStruct");
		}
		return $res;
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
		$res = rs::implode("\\", $arr);
		if ($op_code->alias_name != ""){
			return "use " . rtl::toString($res) . " as " . rtl::toString($op_code->alias_name) . ";";
		}
		return "use " . rtl::toString($res) . ";";
	}
	/** ============================= Classes ============================= */
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
		$old_current_function_is_memorize = $this->current_function_is_memorize;
		$this->current_function_is_memorize = false;
		if ($op_code->isFlag("memorize") && $op_code->isFlag("static") && !$op_code->isFlag("async") && $this->current_function_name->count() == 0){
			$this->current_function_is_memorize = true;
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
			$this->setOperation(false);
			$this->pushOneLine(false);
			$this->levelInc();
			if ($this->current_function_is_memorize){
				$res .= $this->s("\$__memorize_value = rtl::_memorizeValue(" . rtl::toString($this->convertString($this->getCurrentFunctionName())) . ", func_get_args());");
				$res .= $this->s("if (\$__memorize_value != rtl::\$_memorize_not_found) return \$__memorize_value;");
			}
			if ($op_code->childs != null){
				if ($op_code->is_lambda){
					if ($op_code->childs->count() > 0){
						$old_is_operation = $this->beginOperation(true);
						$lambda_res = $this->translateRun($op_code->childs->item(0));
						$this->endOperation($old_is_operation);
						if ($this->current_function_is_memorize){
							$res .= $this->s("\$__memorize_value = " . rtl::toString($lambda_res) . ";");
							$res .= $this->s("rtl::_memorizeSave(" . rtl::toString($this->convertString($this->getCurrentFunctionName())) . ", func_get_args(), \$__memorize_value);");
							$res .= $this->s("return \$__memorize_value;");
						}
						else {
							$res .= $this->s("return " . rtl::toString($lambda_res) . ";");
						}
					}
				}
				else {
					for ($i = 0; $i < $op_code->childs->count(); $i++){
						$res .= $this->s($this->translateRun($op_code->childs->item($i)));
					}
				}
			}
			else if ($op_code->return_function != null){
				$res .= $this->s("return " . rtl::toString($this->translateItem($op_code->return_function)));
			}
			$this->levelDec();
			$res .= $this->s("}" . rtl::toString(($end_semicolon) ? (";") : ("")));
			$this->popOneLine();
		}
		$this->current_function_name->pop();
		$this->current_function_is_memorize = $old_current_function_is_memorize;
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
		$this->levelInc();
		return $res;
	}
	/**
	 * Class declare variables
	 */
	function OpClassDeclareVariables($op_code){
		$res = "";
		for ($i = 0; $i < $op_code->childs->count(); $i++){
			$variable = $op_code->childs->item($i);
			if (!($variable instanceof OpAssignDeclare)){
				continue;
			}
			$s = $this->OpClassDeclareVariable($variable);
			if ($s != ""){
				$res .= $this->s($this->OpClassDeclareVariable($variable));
			}
		}
		return $res;
	}
	/**
	 * Class declare variable
	 */
	function OpClassDeclareVariable($op_code){
		if ($op_code->flags != null){
			$old_is_operation = $this->beginOperation();
			$s = "";
			if ($op_code->isFlag("const")){
				$s .= "const ";
			}
			else {
				if ($op_code->isFlag("static")){
					$s .= "static ";
				}
				if ($op_code->isFlag("protected")){
					$s .= "protected ";
				}
				else if ($this->is_struct && $op_code->isFlag("public") && !$op_code->isFlag("static")){
					$s .= "protected ";
				}
				else {
					$s .= "public ";
				}
			}
			$s .= $this->OpAssignDeclare($op_code, false);
			$s .= ";";
			$this->endOperation($old_is_operation);
			return $s;
		}
		return "";
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
			$name = $op_code->class_extends->value;
			if ($this->modules->has($name)){
				$class_extends = $this->modules->item($name);
			}
			else {
				$class_extends = $name;
			}
		}
		$res = "";
		$has_assignable = false;
		$has_variables = false;
		$has_serializable = false;
		$has_cloneable = false;
		$has_methods_annotations = false;
		$has_fields_annotations = false;
		$res .= $this->s("/* ======================= Class Init Functions ======================= */");
		if (!$this->is_interface){
			$res .= $this->s("public function getClassName(){" . "return " . rtl::toString($this->convertString(rtl::toString($this->current_namespace) . "." . rtl::toString($this->current_class_name))) . ";}");
			$res .= $this->s("public static function getCurrentClassName(){" . "return " . rtl::toString($this->convertString(rtl::toString($this->current_namespace) . "." . rtl::toString($this->current_class_name))) . ";}");
			$res .= $this->s("public static function getParentClassName(){" . "return " . rtl::toString($this->convertString($class_extends)) . ";}");
		}
		if ($this->is_struct){
			$has_serializable = true;
			$has_cloneable = true;
		}
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
					$has_assignable = true;
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
					$has_fields_annotations = true;
					$has_methods_annotations = true;
				}
			}
		}
		if ($this->current_module_name != "Runtime" || $this->current_class_name != "CoreObject"){
			if ($has_variables){
				$res .= $this->s("protected function _init(){");
				$this->levelInc();
				if ($class_extends != ""){
					$res .= $this->s("parent::_init();");
				}
				if ($childs != null){
					for ($i = 0; $i < $childs->count(); $i++){
						$variable = $childs->item($i);
						if (!($variable instanceof OpAssignDeclare)){
							continue;
						}
						if ($variable->value == null){
							continue;
						}
						$var_prefix = "";
						if ($this->is_struct && $variable->isFlag("public") && !$variable->isFlag("static")){
							$var_prefix = "__";
						}
						$is_struct = $this->is_struct && !$variable->isFlag("static") && !$variable->isFlag("const");
						if ($is_struct){
							$this->beginOperation();
							$s = "\$this->" . rtl::toString($var_prefix) . rtl::toString($variable->name) . " = " . rtl::toString($this->translateRun($variable->value)) . ";";
							$this->endOperation();
							$res .= $this->s($s);
						}
					}
				}
				$this->levelDec();
				$res .= $this->s("}");
			}
			if ($has_cloneable || $has_assignable){
				$s1 = "public";
				$res .= $this->s(rtl::toString($s1) . " function assignObject(\$obj){");
				$this->levelInc();
				$res .= $this->s("if (\$obj instanceof " . rtl::toString($this->getName($this->current_class_name)) . "){");
				$this->levelInc();
				for ($i = 0; $i < $childs->count(); $i++){
					$variable = $childs->item($i);
					if (!($variable instanceof OpAssignDeclare)){
						continue;
					}
					$var_prefix = "";
					if ($this->is_struct && $variable->isFlag("public") && !$variable->isFlag("static")){
						$var_prefix = "__";
					}
					$is_struct = $this->is_struct && !$variable->isFlag("static") && !$variable->isFlag("const");
					if ($variable->isFlag("public") && ($variable->isFlag("cloneable") || $variable->isFlag("serializable") || $is_struct)){
						if ($this->is_struct){
							$res .= $this->s("\$this->" . rtl::toString($var_prefix) . rtl::toString($variable->name) . " = " . "\$obj->" . rtl::toString($var_prefix) . rtl::toString($variable->name) . ";");
						}
						else {
							$res .= $this->s("\$this->" . rtl::toString($var_prefix) . rtl::toString($variable->name) . " = " . rtl::toString($this->getName("rtl")) . "::_clone(" . "\$obj->" . rtl::toString($var_prefix) . rtl::toString($variable->name) . ");");
						}
					}
				}
				$this->levelDec();
				$res .= $this->s("}");
				$res .= $this->s("parent::assignObject(\$obj);");
				$this->levelDec();
				$res .= $this->s("}");
			}
			if ($has_serializable || $has_assignable){
				$class_variables_serializable_count = 0;
				$s1 = "public";
				$res .= $this->s(rtl::toString($s1) . " function assignValue(\$variable_name, \$value, \$sender = null){");
				$this->levelInc();
				$class_variables_serializable_count = 0;
				for ($i = 0; $i < $childs->count(); $i++){
					$variable = $childs->item($i);
					if (!($variable instanceof OpAssignDeclare)){
						continue;
					}
					$var_prefix = "";
					if ($this->is_struct && $variable->isFlag("public") && !$variable->isFlag("static")){
						$var_prefix = "__";
					}
					$is_struct = $this->is_struct && !$variable->isFlag("static") && !$variable->isFlag("const");
					if ($variable->isFlag("public") && ($variable->isFlag("serializable") || $variable->isFlag("assignable") || $is_struct)){
						$type_value = $this->getAssignDeclareTypeValue($variable);
						$type_template = $this->getAssignDeclareTypeTemplate($variable);
						$def_val = "null";
						if ($variable->value != null){
							$def_val = $this->translateRun($variable->value);
						}
						$s = "if (\$variable_name == " . rtl::toString($this->convertString($variable->name)) . ")";
						$s .= "\$this->" . rtl::toString($var_prefix) . rtl::toString($variable->name) . " = ";
						$s .= "rtl::convert(\$value,\"" . rtl::toString($type_value) . "\"," . rtl::toString($def_val) . ",\"" . rtl::toString($type_template) . "\");";
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
					$res .= $this->s("parent::assignValue(\$variable_name, \$value, \$sender);");
				}
				else {
					$res .= $this->s("else parent::assignValue(\$variable_name, \$value, \$sender);");
				}
				$this->levelDec();
				$res .= $this->s("}");
				$res .= $this->s("public function takeValue(\$variable_name, \$default_value = null){");
				$this->levelInc();
				$class_variables_serializable_count = 0;
				for ($i = 0; $i < $childs->count(); $i++){
					$variable = $childs->item($i);
					if (!($variable instanceof OpAssignDeclare)){
						continue;
					}
					$var_prefix = "";
					if ($this->is_struct && $variable->isFlag("public") && !$variable->isFlag("static")){
						$var_prefix = "__";
					}
					$is_struct = $this->is_struct && !$variable->isFlag("static") && !$variable->isFlag("const");
					if ($variable->isFlag("public") && ($variable->isFlag("serializable") || $variable->isFlag("assignable") || $is_struct)){
						$take_value_s = "if (\$variable_name == " . rtl::toString($this->convertString($variable->name)) . ") " . "return \$this->" . rtl::toString($var_prefix) . rtl::toString($variable->name) . ";";
						if ($class_variables_serializable_count == 0){
							$res .= $this->s($take_value_s);
						}
						else {
							$res .= $this->s("else " . rtl::toString($take_value_s));
						}
						$class_variables_serializable_count++;
					}
				}
				$res .= $this->s("return parent::takeValue(\$variable_name, \$default_value);");
				$this->levelDec();
				$res .= $this->s("}");
			}
			if ($has_serializable || $has_assignable || $has_fields_annotations){
				$res .= $this->s("public static function getFieldsList(\$names, \$flag=0){");
				$this->levelInc();
				$vars = new Map();
				for ($i = 0; $i < $childs->count(); $i++){
					$variable = $childs->item($i);
					if (!($variable instanceof OpAssignDeclare)){
						continue;
					}
					if (!$variable->isFlag("public")){
						continue;
					}
					$is_struct = $this->is_struct && !$variable->isFlag("static") && !$variable->isFlag("const");
					$is_static = $variable->isFlag("static");
					$is_serializable = $variable->isFlag("serializable");
					$is_assignable = $variable->isFlag("assignable");
					$has_annotation = $variable->hasAnnotations();
					if ($is_struct){
						$is_serializable = true;
						$is_assignable = true;
					}
					if ($is_serializable){
						$is_assignable = true;
					}
					$flag = 0;
					if ($is_serializable){
						$flag = $flag | 1;
					}
					if ($is_assignable){
						$flag = $flag | 2;
					}
					if ($has_annotation){
						$flag = $flag | 4;
					}
					if ($flag != 0){
						if (!$vars->has($flag)){
							$vars->set($flag, new Vector());
						}
						$v = $vars->item($flag);
						$v->push($variable->name);
					}
				}
				$vars->each(function ($flag, $v) use (&$res){
					$res .= $this->s("if ((\$flag | " . rtl::toString($flag) . ")==" . rtl::toString($flag) . "){");
					$this->levelInc();
					$v->each(function ($varname) use (&$res){
						$res .= $this->s("\$names->push(" . rtl::toString($this->convertString($varname)) . ");");
					});
					$this->levelDec();
					$res .= $this->s("}");
				});
				$this->levelDec();
				$res .= $this->s("}");
				$res .= $this->s("public static function getFieldInfoByName(\$field_name){");
				$this->levelInc();
				for ($i = 0; $i < $childs->count(); $i++){
					$variable = $childs->item($i);
					if (!($variable instanceof OpAssignDeclare)){
						continue;
					}
					$is_struct = $this->is_struct && !$variable->isFlag("static") && !$variable->isFlag("const");
					if ($variable->isFlag("public") && $variable->hasAnnotations()){
						$res .= $this->s("if (\$field_name == " . rtl::toString($this->convertString($variable->name)) . "){");
						$this->levelInc();
						$res .= $this->s("return new " . rtl::toString($this->getName("IntrospectionInfo")) . "(");
						$this->levelInc();
						$res .= $this->s("(new " . rtl::toString($this->getName("Map")) . "())");
						$res .= $this->s("->set(\"kind\", \"field\")");
						$res .= $this->s("->set(\"class_name\", " . rtl::toString($this->convertString($this->getCurrentClassName())) . ")");
						$res .= $this->s("->set(\"name\", " . rtl::toString($this->convertString($variable->name)) . ")");
						$res .= $this->s("->set(\"annotations\", ");
						$this->levelInc();
						$res .= $this->s("(new " . rtl::toString($this->getName("Vector")) . "())");
						for ($j = 0; $j < $variable->annotations->count(); $j++){
							$annotation = $variable->annotations->item($j);
							$this->pushOneLine(true);
							$s_kind = $this->translateRun($annotation->kind);
							$s_options = $this->translateRun($annotation->options);
							$this->popOneLine();
							$res .= $this->s("->push(new " . rtl::toString($s_kind) . "(" . rtl::toString($s_options) . "))");
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
				$res .= $this->s("public static function getMethodsList(\$names){");
				$this->levelInc();
				for ($i = 0; $i < $childs->count(); $i++){
					$variable = $childs->item($i);
					if (!($variable instanceof OpFunctionDeclare)){
						continue;
					}
					if ($variable->isFlag("public") && $variable->hasAnnotations()){
						$res .= $this->s("\$names->push(" . rtl::toString($this->convertString($variable->name)) . ");");
					}
				}
				$this->levelDec();
				$res .= $this->s("}");
				$res .= $this->s("public static function getMethodInfoByName(\$method_name){");
				$this->levelInc();
				for ($i = 0; $i < $childs->count(); $i++){
					$variable = $childs->item($i);
					if (!($variable instanceof OpFunctionDeclare)){
						continue;
					}
					if ($variable->isFlag("public") && $variable->hasAnnotations()){
						$res .= $this->s("if (\$method_name == " . rtl::toString($this->convertString($variable->name)) . "){");
						$this->levelInc();
						$res .= $this->s("return new " . rtl::toString($this->getName("IntrospectionInfo")) . "(");
						$this->levelInc();
						$res .= $this->s("(new " . rtl::toString($this->getName("Map")) . "())");
						$res .= $this->s("->set(\"kind\", \"method\")");
						$res .= $this->s("->set(\"class_name\", " . rtl::toString($this->convertString($this->getCurrentClassName())) . ")");
						$res .= $this->s("->set(\"name\", " . rtl::toString($this->convertString($variable->name)) . ")");
						$res .= $this->s("->set(\"annotations\", ");
						$this->levelInc();
						$res .= $this->s("(new " . rtl::toString($this->getName("Vector")) . "())");
						for ($j = 0; $j < $variable->annotations->count(); $j++){
							$annotation = $variable->annotations->item($j);
							$this->pushOneLine(true);
							$s_kind = $this->translateRun($annotation->kind);
							$s_options = $this->translateRun($annotation->options);
							$this->popOneLine();
							$res .= $this->s("->push(new " . rtl::toString($s_kind) . "(" . rtl::toString($s_options) . "))");
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
			if ($this->is_struct){
				$res .= $this->s("public function __get(\$key){ return \$this->takeValue(\$key); }");
				$res .= $this->s("public function __set(\$key, \$value){" . "throw new \\Runtime\\Exceptions\\AssignStructValueError(\$key);" . "}");
			}
		}
		if ($op_code->hasAnnotations()){
			$res .= $this->s("public static function getClassInfo(){");
			$this->levelInc();
			$res .= $this->s("return new " . rtl::toString($this->getName("IntrospectionInfo")) . "(");
			$this->levelInc();
			$res .= $this->s("(new " . rtl::toString($this->getName("Map")) . "())");
			$res .= $this->s("->set(\"kind\", \"class\")");
			$res .= $this->s("->set(\"class_name\", " . rtl::toString($this->convertString($this->getCurrentClassName())) . ")");
			$res .= $this->s("->set(\"annotations\", ");
			$this->levelInc();
			$res .= $this->s("(new " . rtl::toString($this->getName("Vector")) . "())");
			for ($j = 0; $j < $op_code->annotations->count(); $j++){
				$annotation = $op_code->annotations->item($j);
				$this->pushOneLine(true);
				$s_kind = $this->translateRun($annotation->kind);
				$s_options = $this->translateRun($annotation->options);
				$this->popOneLine();
				$res .= $this->s("->push(new " . rtl::toString($s_kind) . "(" . rtl::toString($s_options) . "))");
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
	 * Class declare
	 */
	function OpClassDeclare($op_code){
		$res = "";
		$s = "";
		/* Set current class name */
		$this->current_class_name = $op_code->class_name;
		$this->modules->set($this->current_class_name, rtl::toString($this->current_namespace) . "." . rtl::toString($this->current_class_name));
		$this->ui_struct_class_name->push(rtl::toString($this->current_namespace) . "." . rtl::toString($this->current_class_name));
		/*this.is_interface = false;*/
		/* Skip if declare class */
		if ($op_code->isFlag("declare")){
			return "";
		}
		$res .= $this->OpClassDeclareHeader($op_code);
		/* Variables */
		/*res ~= this.OpClassDeclareVariables(op_code);*/
		/* Class body */
		for ($i = 0; $i < $op_code->childs->count(); $i++){
			$op_code2 = $op_code->childs->item($i);
			if ($op_code2 instanceof OpAssignDeclare){
				$s_assign_variable = $this->OpClassDeclareVariable($op_code2);
				if ($s_assign_variable){
					$res .= $this->s($s_assign_variable);
				}
			}
			else if ($op_code2 instanceof OpFunctionArrowDeclare){
				$res .= $this->s($this->OpFunctionArrowDeclare($op_code2));
			}
			else if ($op_code2 instanceof OpFunctionDeclare){
				$res .= $this->s($this->OpFunctionDeclare($op_code2));
			}
			else if ($op_code2 instanceof OpPreprocessorSwitch){
				$res .= $this->s($this->OpPreprocessorSwitch($op_code2));
			}
			else if ($op_code2 instanceof OpComment){
				$res .= $this->s($this->OpComment($op_code2));
			}
		}
		/* Class Init */
		$res .= $this->OpClassInit($op_code);
		/* Footer class */
		$this->levelDec();
		$res .= $this->s("}");
		$this->ui_struct_class_name->pop();
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
		$res = $this->OpClassDeclare($op_code);
		$this->is_struct = false;
		return $res;
	}
	/** ========================== HTML OP Codes ========================== */
	/**
	 * Check if name is component
	 * @param string name
	 * @return bool
	 */
	function isComponent($name){
		$ch = rs::charAt($name, 0);
		return rs::strtoupper($ch) == $ch && $ch != "";
	}
	/**
	 * Html escape
	 */
	function OpHtmlEscape($op_code){
		$value = $this->translateRun($op_code->value);
		return "rs::htmlEscape(" . rtl::toString($value) . ")";
	}
	/**
	 * OpHtmlJson
	 */
	function OpHtmlJson($op_code){
		return "rtl::json_encode(" . rtl::toString($this->translateRun($op_code->value)) . ")";
		$res = "";
		$res = "new UIStruct(new " . rtl::toString($this->getName("Map")) . "([";
		$res .= $this->s("\"name\"=>\"span\",");
		$res .= $this->s("\"props\"=>new " . rtl::toString($this->getName("Map")) . "(");
		$res .= $this->s("\"rawHTML\"=>" . rtl::toString($value));
		$res .= $this->s("])]))");
		return $res;
	}
	/**
	 * OpHtmlRaw
	 */
	function OpHtmlRaw($op_code){
		$value = $this->translateRun($op_code->value);
		return $this->translateRun($op_code->value);
		$res = "";
		$res = "new UIStruct(new " . rtl::toString($this->getName("Map")) . "([";
		$res .= $this->s("\"name\"=>\"span\",");
		$res .= $this->s("\"props\"=>new " . rtl::toString($this->getName("Map")) . "(");
		$res .= $this->s("\"rawHTML\"=>" . rtl::toString($value));
		$res .= $this->s("])]))");
		return $res;
	}
	/**
	 * Html Text
	 */
	function OpHtmlText($op_code){
		return $this->convertString($op_code->value);
	}
	/**
	 * Returns true if key is props
	 */
	function isOpHtmlTagProps($key){
		if ($key == "@key" || $key == "@control"){
			return false;
		}
		return true;
	}
	/**
	 * Retuns css hash 
	 * @param string component class name
	 * @return string hash
	 */
	function getCssHash($s){
		$arr = "1234567890abcdef";
		$arr_sz = 16;
		$arr_mod = 65536;
		$sz = rs::strlen($s);
		$hash = 0;
		for ($i = 0; $i < $sz; $i++){
			$ch = rs::ord(mb_substr($s, $i, 1));
			$hash = ($hash << 2) + ($hash >> 14) + $ch & 65535;
		}
		$res = "";
		$pos = 0;
		$c = 0;
		while ($hash != 0 || $pos < 4){
			$c = $hash & 15;
			$hash = $hash >> 4;
			$res .= mb_substr($arr, $c, 1);
			$pos++;
		}
		return $res;
	}
	/**
	 * Html tag
	 */
	function OpHtmlTag($op_code){
		$is_component = false;
		$res = "";
		$this->pushOneLine(false);
		/* isComponent */
		if ($this->modules->has($op_code->tag_name)){
			$res = "new UIStruct(new " . rtl::toString($this->getName("Map")) . "([";
			$res .= $this->s("\"kind\"=>\"component\",");
			$res .= $this->s("\"name\"=>" . rtl::toString($this->convertString($this->modules->item($op_code->tag_name))) . ",");
			$is_component = true;
		}
		else {
			$res = "new UIStruct(new " . rtl::toString($this->getName("Map")) . "([";
			$res .= $this->s("\"space\"=>" . rtl::toString($this->convertString($this->getCssHash($this->getUIStructClassName()))) . ",");
			$res .= $this->s("\"class_name\"=>static::getCurrentClassName(),");
			$res .= $this->s("\"name\"=>" . rtl::toString($this->convertString($op_code->tag_name)) . ",");
		}
		$is_props = false;
		$is_spreads = $op_code->spreads != null && $op_code->spreads->count() > 0;
		if ($op_code->attributes != null && $op_code->attributes->count() > 0){
			$op_code->attributes->each(function ($item) use (&$res, &$is_props, &$is_events){
				$key = $item->key;
				if ($this->isOpHtmlTagProps($key)){
					$is_props = true;
				}
				else if ($key == "@key"){
					$value = $this->translateRun($item->value);
					$res .= $this->s("\"key\"=>" . rtl::toString($value) . ",");
				}
				else if ($key == "@control"){
					$value = $this->translateRun($item->value);
					$res .= $this->s("\"controller\"=>" . rtl::toString($value) . ",");
				}
			});
		}
		if ($is_props || $is_spreads){
			$res .= $this->s("\"props\"=>(new " . rtl::toString($this->getName("Map")) . "())");
			$this->levelInc();
			if ($is_props){
				$op_code->attributes->each(function ($item) use (&$res){
					if ($this->isOpHtmlTagProps($item->key)){
						$old_operation = $this->beginOperation(true);
						$this->pushOneLine(true);
						$key = $item->key;
						$value = $this->translateRun($item->value);
						if ($key == "@lambda"){
							$key = "callback";
						}
						$this->popOneLine();
						$this->endOperation($old_operation);
						$res .= $this->s("->set(" . rtl::toString($this->convertString($key)) . ", " . rtl::toString($value) . ")");
					}
				});
			}
			if ($is_spreads){
				$op_code->spreads->each(function ($item) use (&$res){
					$res .= $this->s("->addMap(\$" . rtl::toString($item) . ")");
				});
			}
			$this->levelDec();
			$res .= $this->s(",");
		}
		if ($op_code->is_plain){
			if ($op_code->childs != null){
				$value = $op_code->childs->reduce(function ($res, $item){
					$value = "";
					if ($item instanceof OpHtmlJson){
						$value = "rtl::json_encode(" . rtl::toString($this->translateRun($item->value)) . ")";
						$value = "rtl::toString(" . rtl::toString($value) . ")";
					}
					else if ($item instanceof OpHtmlRaw){
						$value = $this->translateRun($item->value);
						$value = "rtl::toString(" . rtl::toString($value) . ")";
					}
					else if ($item instanceof OpConcat || $item instanceof OpString){
						$value = $this->translateRun($item);
					}
					else if ($item instanceof OpHtmlEscape){
						$value = $this->translateRun($item);
						$value = "rs::htmlEscape(" . rtl::toString($value) . ")";
					}
					else if ($item instanceof OpHtmlText){
						$value = $this->convertString($item->value);
					}
					else {
						$value = $this->translateRun($item);
						$value = "rtl::toString(" . rtl::toString($value) . ")";
					}
					if ($res == ""){
						return $value;
					}
					return rtl::toString($res) . "." . rtl::toString($value);
				}, "");
				$old_operation = $this->beginOperation(true);
				$this->pushOneLine(true);
				$res .= $this->s("\"children\" => new " . rtl::toString($this->getName("Vector")) . "([");
				$this->levelInc();
				$res .= $this->s("rtl::normalizeUI(" . rtl::toString($value) . ")");
				$this->levelDec();
				$res .= $this->s("])");
				$this->popOneLine();
				$this->endOperation($old_operation);
			}
		}
		else {
			if ($op_code->childs != null && $op_code->childs->count() > 0){
				$res .= $this->s("\"children\" => rtl::normalizeUIVector(new " . rtl::toString($this->getName("Vector")) . "([");
				$this->levelInc();
				$childs_sz = $op_code->childs->count();
				for ($i = 0; $i < $childs_sz; $i++){
					$item = $op_code->childs->item($i);
					if ($item instanceof OpComment){
						continue;
					}
					$res .= $this->s(rtl::toString($this->translateRun($item)) . rtl::toString(($i + 1 == $childs_sz) ? ("") : (",")));
				}
				$this->levelDec();
				$res .= $this->s("]))");
			}
		}
		$res .= $this->s("]))");
		$this->popOneLine();
		if ($is_component){
		}
		return $res;
	}
	/**
	 * Html tag
	 */
	function OpHtmlView($op_code){
		$res = "rtl::normalizeUIVector(new Vector([";
		$this->pushOneLine(false);
		$childs_sz = $op_code->childs->count();
		for ($i = 0; $i < $childs_sz; $i++){
			if ($item instanceof OpComment){
				continue;
			}
			$item = $op_code->childs->item($i);
			$res .= $this->s(rtl::toString($this->translateRun($item)) . rtl::toString(($i + 1 == $childs_sz) ? ("") : (",")));
		}
		$this->popOneLine();
		$res .= $this->s("]))");
		return $res;
	}
	/** =========================== Preprocessor ========================== */
	function calcPreprocessorCondition($op_case){
		if ($op_case->condition instanceof OpIdentifier){
			if ($op_case->condition->value == "PHP"){
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
		$this->current_function_name = new Vector();
		$this->ui_struct_class_name = new Vector();
	}
	/**
	 * Translate to language
	 * @param BaseOpCode op_code - Abstract syntax tree
	 * @returns string - The result
	 */
	function translate($op_code){
		$this->resetTranslator();
		$s = "<?php" . rtl::toString($this->crlf);
		$s .= $this->translateRun($op_code);
		return $s;
	}
	/* ======================= Class Init Functions ======================= */
	public function getClassName(){return "BayrellLang.LangPHP.TranslatorPHP";}
	public static function getCurrentClassName(){return "BayrellLang.LangPHP.TranslatorPHP";}
	public static function getParentClassName(){return "BayrellLang.CommonTranslator";}
	protected function _init(){
		parent::_init();
	}
}