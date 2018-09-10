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
use Runtime\CoreObject;
use Runtime\ContextObject;
use Runtime\Interfaces\FactoryInterface;
use BayrellCommon\Utils as BayrellCommonUtils;
use BayrellLang\LangBay\ParserBay;
use BayrellLang\LangES6\TranslatorES6;
use BayrellLang\CommonParser;
use BayrellLang\CommonTranslator;
class Utils extends ContextObject{
	public function getClassName(){return "BayrellLang.Utils";}
	public static function getParentClassName(){return "Runtime.ContextObject";}
	/**
	 * Transcompile one language to other
	 * @string string parser_factory_name
	 * @string string translator_factory_name
	 * @string string source
	 * @return string
	 */
	static function getAST($context, $parser_factory, $source){
		$parser = $parser_factory->newInstance($context);
		$parser->parseString($source);
		$code_tree = $parser->getAST();
		return $code_tree;
	}
	/**
	 * Transcompile one language to other
	 * @string string parser_factory_name
	 * @string string translator_factory_name
	 * @string string source
	 * @return string
	 */
	static function translateAST($context, $translator_factory, $code_tree){
		$translator = $translator_factory->newInstance($context);
		$res = $translator->translate($code_tree);
		return $res;
	}
	/**
	 * Transcompile one language to other
	 * @string string parser_factory_name
	 * @string string translator_factory_name
	 * @string string source
	 * @return string
	 */
	static function translate($context, $parser_factory, $translator_factory, $source){
		$parser = $parser_factory->newInstance($context);
		$translator = $translator_factory->newInstance($context);
		$parser->parseString($source);
		$code_tree = $parser->getAST();
		$res = $translator->translate($code_tree);
		return $res;
	}
	/**
	 * Transcompile Bayrell language to other
	 * @string string translator_factory_name
	 * @string string source
	 * @return string
	 */
	static function translateBay($context, $translator_factory, $source){
		$translator = $translator_factory->newInstance($context);
		$parser = new ParserBay($context);
		$parser->parseString($source);
		$code_tree = $parser->getAST();
		$res = $translator->translate($code_tree);
		return $res;
	}
	/**
	 * Transcompile Bayrell language to other
	 * @string FactoryInterface parser_factory
	 * @string FactoryInterface translator_factory
	 * @string string src_file_name
	 * @string string dest_file_name
	 */
	static function translateFile($context, $parser_factory, $translator_factory, $src_file_name, $dest_file_name){
		/*
		#switch
		#case ifcode NODEJS then
		var fsModule = require('fs');
		var shellModule = require('shelljs');
		var content = fsModule.readFileSync(src_file_name, {encoding : 'utf8'}).toString();
		#endswitch
		*/
		$file_system = $context->createProvider("default:fs");
		$content = $file_system->readFile($src_file_name);
		$res = static::translate($context, $parser_factory, $translator_factory, $content);
		$dir = BayrellCommonUtils::dirname($dest_file_name);
		$file_system->makeDir($dir);
		$file_system->saveFile($dest_file_name, $res);
		/*
		#switch
		#case ifcode NODEJS then
		if (!fsModule.existsSync(dir)){
			shellModule.mkdir('-p', dirpath);
		}
		fsModule.writeFileSync(dest_file_name, res, {encoding : 'utf8'});
		#endswitch
		*/
	}
}