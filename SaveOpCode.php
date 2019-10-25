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
class SaveOpCode extends \Runtime\CoreStruct
{
	public $__var_name;
	public $__var_content;
	public $__content;
	public $__op_code;
	/* ======================= Class Init Functions ======================= */
	function _init($__ctx)
	{
		parent::_init($__ctx);
		$this->__var_name = "";
		$this->__var_content = "";
		$this->__content = "";
		$this->__op_code = null;
	}
	function assignObject($__ctx,$o)
	{
		if ($o instanceof \Bayrell\Lang\SaveOpCode)
		{
			$this->__var_name = $o->__var_name;
			$this->__var_content = $o->__var_content;
			$this->__content = $o->__content;
			$this->__op_code = $o->__op_code;
		}
		parent::assignObject($__ctx,$o);
	}
	function assignValue($__ctx,$k,$v)
	{
		if ($k == "var_name")$this->__var_name = $v;
		else if ($k == "var_content")$this->__var_content = $v;
		else if ($k == "content")$this->__content = $v;
		else if ($k == "op_code")$this->__op_code = $v;
		else parent::assignValue($__ctx,$k,$v);
	}
	function takeValue($__ctx,$k,$d=null)
	{
		if ($k == "var_name")return $this->__var_name;
		else if ($k == "var_content")return $this->__var_content;
		else if ($k == "content")return $this->__content;
		else if ($k == "op_code")return $this->__op_code;
		return parent::takeValue($__ctx,$k,$d);
	}
	function getClassName()
	{
		return "Bayrell.Lang.SaveOpCode";
	}
	static function getCurrentNamespace()
	{
		return "Bayrell.Lang";
	}
	static function getCurrentClassName()
	{
		return "Bayrell.Lang.SaveOpCode";
	}
	static function getParentClassName()
	{
		return "Runtime.CoreStruct";
	}
	static function getClassInfo($__ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($__ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.SaveOpCode",
			"name"=>"Bayrell.Lang.SaveOpCode",
			"annotations"=>\Runtime\Collection::from([
			]),
		]);
	}
	static function getFieldsList($__ctx,$f)
	{
		$a = [];
		if (($f|3)==3)
		{
			$a[] = "var_name";
			$a[] = "var_content";
			$a[] = "content";
			$a[] = "op_code";
		}
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