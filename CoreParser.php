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
class CoreParser extends \Runtime\FakeStruct
{
	public $tab_size;
	public $file_name;
	public $content;
	public $content_sz;
	public $caret;
	public $find_ident;
	/**
	 * Returns true if eof
	 */
	function isEof($__ctx)
	{
		return $this->caret->pos >= $this->content_sz;
	}
	/**
	 * Reset parser
	 */
	static function reset($__ctx, $parser)
	{
		return $parser->copy($__ctx, \Runtime\Dict::from(["caret"=>new \Bayrell\Lang\Caret($__ctx, \Runtime\Dict::from([])),"token"=>null]));
	}
	/**
	 * Set content
	 */
	static function setContent($__ctx, $parser, $content)
	{
		return $parser->copy($__ctx, \Runtime\Dict::from(["content"=>new \Runtime\Reference($__ctx, $content),"content_sz"=>\Runtime\rs::strlen($__ctx, $content)]));
	}
	/**
	 * Parse file and convert to BaseOpCode
	 */
	static function parse($__ctx, $parser, $content)
	{
		$parser = static::reset($__ctx, $parser);
		$parser = static::setContent($__ctx, $parser, $content);
		while ($parser->caret->pos < $parser->content_sz)
		{
			$parser = $parser->staticMethod("nextToken")($__ctx, $parser);
		}
		return $parser;
	}
	/* ======================= Class Init Functions ======================= */
	function _init($__ctx)
	{
		parent::_init($__ctx);
		$this->tab_size = 4;
		$this->file_name = "";
		$this->content = null;
		$this->content_sz = 0;
		$this->caret = null;
		$this->find_ident = true;
	}
	function getClassName()
	{
		return "Bayrell.Lang.CoreParser";
	}
	static function getCurrentNamespace()
	{
		return "Bayrell.Lang";
	}
	static function getCurrentClassName()
	{
		return "Bayrell.Lang.CoreParser";
	}
	static function getParentClassName()
	{
		return "Runtime.FakeStruct";
	}
	static function getClassInfo($__ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($__ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.CoreParser",
			"name"=>"Bayrell.Lang.CoreParser",
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