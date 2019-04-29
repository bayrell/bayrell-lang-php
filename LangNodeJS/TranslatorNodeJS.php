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
namespace BayrellLang\LangNodeJS;
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
use BayrellLang\OpCodes\OpAssignDeclare;
use BayrellLang\OpCodes\OpIdentifier;
use BayrellLang\OpCodes\OpPreprocessorCase;
use BayrellLang\LangES6\TranslatorES6;
use BayrellLang\CommonTranslator;
class TranslatorNodeJS extends TranslatorES6{
	/**
	 * Get name
	 */
	function getName($name){
		if ($name == "parent"){
			return "super";
		}
		else if ($name == "self"){
			return $this->current_class_name;
		}
		else if ($name == "static"){
			return "this";
		}
		return $name;
	}
	/**
	 * Namespace
	 */
	function OpNamespace($op_code){
		$this->current_namespace = $op_code->value;
		$arr = rs::explode(".", $this->current_namespace);
		$this->current_module_name = $arr->item(0);
		$this->modules->clear();
		if ($this->current_module_name != "Runtime"){
			return "var rtl = require('bayrell-runtime-nodejs').rtl;" . rtl::toString($this->s("var Map = require('bayrell-runtime-nodejs').Map;")) . rtl::toString($this->s("var Dict = require('bayrell-runtime-nodejs').Dict;")) . rtl::toString($this->s("var Vector = require('bayrell-runtime-nodejs').Vector;")) . rtl::toString($this->s("var Collection = require('bayrell-runtime-nodejs').Collection;")) . rtl::toString($this->s("var IntrospectionInfo = require('bayrell-runtime-nodejs').IntrospectionInfo;"));
		}
		return "";
	}
	/**
	 * Use
	 */
	function OpUse($op_code){
		$res = "";
		$lib_name = $op_code->value;
		$var_name = "m_" . rtl::toString(re::replace("\\.", "_", $lib_name));
		$arr1 = rs::explode(".", $lib_name);
		$arr2 = rs::explode(".", $this->current_namespace);
		$sz_arr1 = $arr1->count();
		$sz_arr2 = $arr2->count();
		if ($sz_arr1 < 2){
			return "";
		}
		$class_name = $arr1->getLastItem();
		if ($op_code->alias_name != ""){
			$class_name = $op_code->alias_name;
		}
		$this->modules->set($class_name, $lib_name);
		/* If same modules */
		if ($arr1->item(0) == $arr2->item(0)){
			$pos = 0;
			while ($pos < $sz_arr1 && $pos < $sz_arr2 && $arr1->item($pos) == $arr2->item($pos)){
				$pos++;
			}
			$js_path = "";
			if ($pos == $arr2->count()){
				$js_path = "./";
			}
			else {
				for ($j = $pos; $j < $sz_arr2; $j++){
					$js_path = rtl::toString($js_path) . "../";
				}
			}
			$ch = "";
			for ($j = $pos; $j < $sz_arr1; $j++){
				$js_path = rtl::toString($js_path) . rtl::toString($ch) . rtl::toString($arr1->item($j));
				$ch = "/";
			}
			$module_name = $arr1->shift();
			$module_path = rs::implode(".", $arr1);
			$js_path = rtl::toString($js_path) . ".js";
			$res = "var " . rtl::toString($class_name) . " = require('" . rtl::toString($js_path) . "');";
		}
		else {
			$module_name = $arr1->shift();
			$module_path = rs::implode(".", $arr1);
			if ($module_name == "Runtime"){
				$module_name = "BayrellRuntime";
			}
			if ($module_name == "Core"){
				$module_name = "BayrellCore";
			}
			$module_name = rtl::convertNodeJSModuleName($module_name);
			$res = "var " . rtl::toString($class_name) . " = require('" . rtl::toString($module_name) . "')." . rtl::toString($module_path) . ";";
		}
		return $res;
	}
	/**
	 * Class declare header
	 */
	function OpClassDeclareHeader($op_code){
		$res = "";
		$this->ui_struct_class_name->push(rtl::toString($this->current_namespace) . "." . rtl::toString($this->current_class_name));
		$this->beginOperation();
		$res .= "class " . rtl::toString($op_code->class_name);
		if ($op_code->class_extends != ""){
			$res .= " extends " . rtl::toString($this->translateRun($op_code->class_extends));
		}
		$res .= "{";
		$this->endOperation();
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
				$s = rtl::toString($op_code->class_name) . "." . rtl::toString($variable->name) . " = " . rtl::toString($this->translateRun($variable->value)) . ";";
				$this->endOperation();
				$res .= $this->s($s);
			}
		}
		/* Static implements */
		$class_implements = $op_code->class_implements;
		if ($class_implements != null && $class_implements->count() > 0){
			$name = $op_code->class_name;
			$res .= $this->s(rtl::toString($name) . ".__static_implements__ = [];");
			for ($i = 0; $i < $class_implements->count(); $i++){
				$value = $class_implements->item($i);
				$res .= $this->s(rtl::toString($name) . ".__static_implements__.push(" . rtl::toString($this->getName($value)) . ")");
			}
		}
		$res .= $this->s("module.exports = " . rtl::toString($op_code->class_name) . ";");
		return $res;
	}
	/**
	 * Class declare footer
	 */
	function OpClassDeclareFooterNew($op_code){
		$ch = "";
		$res = "";
		$current_namespace = "";
		$v = rs::explode(".", $this->current_namespace);
		$res .= $this->s("module.exports = {};");
		for ($i = 0; $i < $v->count(); $i++){
			if ($i == 0){
				continue;
			}
			$current_namespace .= rtl::toString($ch) . rtl::toString($v->item($i));
			$s = "if (typeof module.exports." . rtl::toString($current_namespace) . " == 'undefined') " . "module.exports." . rtl::toString($current_namespace) . " = {};";
			$res .= $this->s($s);
			$ch = ".";
		}
		if ($current_namespace == ""){
			$current_namespace = "module.exports";
		}
		else {
			$current_namespace = "module.exports." . rtl::toString($current_namespace);
		}
		$res .= $this->s(rtl::toString($current_namespace) . "." . rtl::toString($op_code->class_name) . " = " . rtl::toString($op_code->class_name));
		for ($i = 0; $i < $op_code->class_variables->count(); $i++){
			$variable = $op_code->class_variables->item($i);
			if ($variable->flags != null && $variable->flags->p_static == true){
				$this->beginOperation();
				$s = rtl::toString($current_namespace) . "." . rtl::toString($op_code->class_name) . "." . rtl::toString($variable->name) . " = " . rtl::toString($this->translateRun($variable->value)) . ";";
				$this->endOperation();
				$res .= $this->s($s);
			}
		}
		/* Static implements */
		$class_implements = $op_code->class_implements;
		if ($class_implements != null && $class_implements->count() > 0){
			$name = $op_code->class_name;
			$res .= $this->s(rtl::toString($current_namespace) . "." . rtl::toString($name) . ".__static_implements__ = [];");
			for ($i = 0; $i < $class_implements->count(); $i++){
				$value = $class_implements->item($i);
				$res .= $this->s(rtl::toString($current_namespace) . "." . rtl::toString($name) . ".__static_implements__.push(" . rtl::toString($this->getName($value)) . ")");
			}
		}
		return $res;
	}
	/**
	 * Calc preprocessor condition
	 */
	function calcPreprocessorCondition($op_case){
		if ($op_case->condition instanceof OpIdentifier){
			if ($op_case->condition->value == "JAVASCRIPT" || $op_case->condition->value == "NODEJS"){
				return true;
			}
		}
		return false;
	}
	/* ======================= Class Init Functions ======================= */
	public function getClassName(){return "BayrellLang.LangNodeJS.TranslatorNodeJS";}
	public static function getCurrentClassName(){return "BayrellLang.LangNodeJS.TranslatorNodeJS";}
	public static function getParentClassName(){return "BayrellLang.LangES6.TranslatorES6";}
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