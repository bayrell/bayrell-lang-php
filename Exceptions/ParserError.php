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
namespace Bayrell\Lang\Exceptions;
class ParserError extends \Bayrell\Lang\Exceptions\ParserUnknownError
{
	function __construct($ctx, $s, $caret, $file="", $code, $context, $prev=null)
	{
		parent::__construct($ctx, $s, $code, $context, $prev);
		$this->error_line = $caret->y + 1;
		$this->error_pos = $caret->x + 1;
		$this->error_file = $file;
		$this->updateError($ctx);
	}
	function buildMessage($ctx)
	{
		$error_str = $this->error_str;
		$file = $this->getFileName($ctx);
		$line = $this->getErrorLine($ctx);
		$pos = $this->getErrorPos($ctx);
		if ($line != -1)
		{
			$error_str .= \Runtime\rtl::toStr(" at Ln:" . \Runtime\rtl::toStr($line) . \Runtime\rtl::toStr((($pos != "") ? ", Pos:" . \Runtime\rtl::toStr($pos) : "")));
		}
		if ($file != "")
		{
			$error_str .= \Runtime\rtl::toStr(" in file:'" . \Runtime\rtl::toStr($file) . \Runtime\rtl::toStr("'"));
		}
		return $error_str;
	}
	/* ======================= Class Init Functions ======================= */
	function getClassName()
	{
		return "Bayrell.Lang.Exceptions.ParserError";
	}
	static function getCurrentNamespace()
	{
		return "Bayrell.Lang.Exceptions";
	}
	static function getCurrentClassName()
	{
		return "Bayrell.Lang.Exceptions.ParserError";
	}
	static function getParentClassName()
	{
		return "Bayrell.Lang.Exceptions.ParserUnknownError";
	}
	static function getClassInfo($ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.Exceptions.ParserError",
			"name"=>"Bayrell.Lang.Exceptions.ParserError",
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
	}
	static function getFieldsList($ctx,$f)
	{
		$a = [];
		return \Runtime\Collection::from($a);
	}
	static function getFieldInfoByName($ctx,$field_name)
	{
		return null;
	}
	static function getMethodsList($ctx)
	{
		$a = [
		];
		return \Runtime\Collection::from($a);
	}
	static function getMethodInfoByName($ctx,$field_name)
	{
		return null;
	}
}