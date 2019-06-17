<?php
/*!
 *  Bayrell Parser Library.  
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
namespace BayrellLang\Parser\Interfaces;
use Runtime\rs;
use Runtime\rtl;
use Runtime\Map;
use Runtime\Vector;
use Runtime\Dict;
use Runtime\Collection;
use Runtime\IntrospectionInfo;
use Runtime\UIStruct;
interface ParserInterface{
	/**
	 * Set content of the file
	 * @param {string} content - content
	 */
	function setContent($content);
	/**
	 * Get content of the file
	 * @return {string}
	 */
	function getContent();
	/**
	 * Get char of the content
	 * @return {char}
	 */
	function getContentPos($pos);
	/**
	 * Get content size
	 * @return {int}
	 */
	function getContentSize();
	/**
	 * Get tab size
	 * @return {int}
	 */
	function getTabSize();
	/**
	 * Get content string
	 * @return {int} pos
	 * @return {int} len 
	 * @return {string}
	 */
	function getContentString($pos, $len);
	/**
	 * Return file name
	 * @return {string}
	 */
	function getFileName($file_name);
	/**
	 * Reset parser to default settings
	 */
	function resetParser();
	/**
	 * Parser function
	 */
	function runParser();
}