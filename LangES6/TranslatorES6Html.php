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
namespace Bayrell\Lang\LangES6;
class TranslatorES6Html
{
	/**
	 * Is component
	 */
	static function isComponent($__ctx, $tag_name)
	{
		$ch1 = \Runtime\rs::substr($__ctx, $tag_name, 0, 1);
		$ch2 = \Runtime\rs::strtoupper($__ctx, $ch1);
		return $ch1 == "{" || $ch1 == $ch2;
	}
	/**
	 * Translator html template
	 */
	static function OpHtmlAttrs($__ctx, $t, $attrs)
	{
		$attr_s = "null";
		$attrs = $attrs->map($__ctx, function ($__ctx, $attr) use (&$t)
		{
			$attr_key = $attr->key;
			$ch = \Runtime\rs::substr($__ctx, $attr_key, 0, 1);
			if ($attr_key == "@class" && $attr->value instanceof \Bayrell\Lang\OpCodes\OpString)
			{
				return "\"class\":" . \Runtime\rtl::toStr("this.getCssName(__ctx, ") . \Runtime\rtl::toStr($t->expression->staticMethod("toString")($__ctx, $attr->value->value)) . \Runtime\rtl::toStr(")");
			}
			if (\Runtime\rs::substr($__ctx, $attr_key, 0, 7) == "@event:")
			{
				$event_name = \Runtime\rs::substr($__ctx, $attr_key, 7);
				$event_name = $t->expression->staticMethod("findModuleName")($__ctx, $t, $event_name);
				$attr_key = "@event:" . \Runtime\rtl::toStr($event_name);
			}
			if ($attr->value instanceof \Bayrell\Lang\OpCodes\OpHtmlValue)
			{
				if ($attr->value->kind == \Bayrell\Lang\OpCodes\OpHtmlValue::KIND_RAW)
				{
					$res = $t->expression->staticMethod("Expression")($__ctx, $t, $attr->value->value);
					$t = $res[0];
					return $t->expression->staticMethod("toString")($__ctx, $attr_key) . \Runtime\rtl::toStr(":") . \Runtime\rtl::toStr($res[1]);
				}
				else if ($attr->value->kind == \Bayrell\Lang\OpCodes\OpHtmlValue::KIND_JSON)
				{
					$res = $t->expression->staticMethod("Expression")($__ctx, $t, $attr->value->value);
					$t = $res[0];
					$value = $res[1];
					$value = "static::json_encode(__ctx, " . \Runtime\rtl::toStr($value) . \Runtime\rtl::toStr(")");
					return $t->expression->staticMethod("toString")($__ctx, $attr_key) . \Runtime\rtl::toStr(":") . \Runtime\rtl::toStr($value);
				}
			}
			$res = $t->expression->staticMethod("Expression")($__ctx, $t, $attr->value);
			$t = $res[0];
			$value = $res[1];
			return $t->expression->staticMethod("toString")($__ctx, $attr_key) . \Runtime\rtl::toStr(":") . \Runtime\rtl::toStr($res[1]);
		});
		$attrs = $attrs->filter($__ctx, function ($__ctx, $s)
		{
			return $s != "";
		});
		if ($attrs->count($__ctx) > 0)
		{
			$attr_s = "{" . \Runtime\rtl::toStr(\Runtime\rs::join($__ctx, ",", $attrs)) . \Runtime\rtl::toStr("}");
		}
		return \Runtime\Collection::from([$t,$attr_s]);
	}
	/**
	 * Translator html template
	 */
	static function OpHtmlTag($__ctx, $t, $op_code, $item_pos)
	{
		$is_component = static::isComponent($__ctx, $op_code->tag_name);
		$content = "";
		if ($is_component)
		{
			$content = $t->s($__ctx, "/* Component '" . \Runtime\rtl::toStr($op_code->tag_name) . \Runtime\rtl::toStr("' */"));
		}
		else
		{
			$content = $t->s($__ctx, "/* Element '" . \Runtime\rtl::toStr($op_code->tag_name) . \Runtime\rtl::toStr("' */"));
		}
		$res = $t->staticMethod("incSaveOpCode")($__ctx, $t);
		$t = $res[0];
		$var_name = $res[1];
		$tag_name = $t->expression->staticMethod("toString")($__ctx, $op_code->tag_name);
		$res = static::OpHtmlAttrs($__ctx, $t, $op_code->attrs);
		$t = $res[0];
		$attrs = $res[1];
		$var_name_content = $var_name . \Runtime\rtl::toStr("_content");
		if ($op_code->items != null && $op_code->items->items->count($__ctx) > 0)
		{
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "var " . \Runtime\rtl::toStr($var_name_content) . \Runtime\rtl::toStr(" = (control) =>")));
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "{"));
			$t = $t->levelInc($__ctx);
			$f = \Runtime\rtl::method($__ctx, static::getCurrentClassName($__ctx), "OpHtmlItems");
			$res = $t->staticMethod("saveOpCodeCall")($__ctx, $t, $f, \Runtime\Collection::from([$op_code->items]));
			$t = $res[0];
			$content .= \Runtime\rtl::toStr($res[1]);
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "return " . \Runtime\rtl::toStr($res[2]) . \Runtime\rtl::toStr(";")));
			$t = $t->levelDec($__ctx);
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "};"));
		}
		else
		{
			$var_name_content = "null";
		}
		if ($is_component)
		{
			if ($op_code->op_code_name)
			{
				$res = $t->expression->staticMethod("Expression")($__ctx, $t, $op_code->op_code_name);
				$t = $res[0];
				$tag_name = $res[1];
			}
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "var " . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr("_elem = Runtime.UI.Drivers.RenderDriver.component(") . \Runtime\rtl::toStr("layout,") . \Runtime\rtl::toStr($tag_name) . \Runtime\rtl::toStr(",") . \Runtime\rtl::toStr($attrs) . \Runtime\rtl::toStr(",") . \Runtime\rtl::toStr($var_name_content) . \Runtime\rtl::toStr(",") . \Runtime\rtl::toStr("control,") . \Runtime\rtl::toStr($item_pos) . \Runtime\rtl::toStr(");")));
		}
		else
		{
			$content .= \Runtime\rtl::toStr($t->s($__ctx, "var " . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr("_elem = Runtime.UI.Drivers.RenderDriver.elem(") . \Runtime\rtl::toStr("layout,") . \Runtime\rtl::toStr($tag_name) . \Runtime\rtl::toStr(",") . \Runtime\rtl::toStr($attrs) . \Runtime\rtl::toStr(",") . \Runtime\rtl::toStr($var_name_content) . \Runtime\rtl::toStr(",") . \Runtime\rtl::toStr("control,") . \Runtime\rtl::toStr($item_pos) . \Runtime\rtl::toStr(");")));
		}
		$res = $t->staticMethod("addSaveOpCode")($__ctx, $t, \Runtime\Dict::from(["op_code"=>$op_code,"var_name"=>$var_name,"content"=>$content]));
		$t = $res[0];
		return \Runtime\Collection::from([$t,$var_name . \Runtime\rtl::toStr("_elem")]);
	}
	/**
	 * Translator html items
	 */
	static function OpHtmlItems($__ctx, $t, $op_code)
	{
		if ($op_code->items->count($__ctx) == 0)
		{
			return \Runtime\Collection::from([$t,""]);
		}
		$res = $t->staticMethod("incSaveOpCode")($__ctx, $t);
		$t = $res[0];
		$var_name = $res[1];
		$content = $t->s($__ctx, "/* Items */");
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "var " . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr(" = [];")));
		for ($i = 0;$i < $op_code->items->count($__ctx);$i++)
		{
			$item = $op_code->items->item($__ctx, $i);
			$item_value = "";
			$is_text = false;
			$is_raw = false;
			if ($item instanceof \Bayrell\Lang\OpCodes\OpHtmlContent)
			{
				$item_value = $t->expression->staticMethod("toString")($__ctx, $item->value);
				$is_text = true;
			}
			else if ($item instanceof \Bayrell\Lang\OpCodes\OpHtmlTag)
			{
				$res = static::OpHtmlTag($__ctx, $t, $item, $i);
				$t = $res[0];
				$item_value = $res[1];
			}
			else if ($item instanceof \Bayrell\Lang\OpCodes\OpHtmlValue)
			{
				if ($item->kind == \Bayrell\Lang\OpCodes\OpHtmlValue::KIND_RAW)
				{
					$res = $t->expression->staticMethod("Expression")($__ctx, $t, $item->value);
					$t = $res[0];
					$item_value = $res[1];
					$is_raw = true;
				}
				else if ($item->kind == \Bayrell\Lang\OpCodes\OpHtmlValue::KIND_JSON)
				{
					$res = $t->expression->staticMethod("Expression")($__ctx, $t, $item->value);
					$t = $res[0];
					$item_value = $res[1];
					$item_value = "this.json_encode(__ctx, " . \Runtime\rtl::toStr($item_value) . \Runtime\rtl::toStr(")");
					$is_text = true;
				}
			}
			else
			{
				$res = $t->expression->staticMethod("Expression")($__ctx, $t, $item);
				$t = $res[0];
				$item_value = $res[1];
				$is_text = true;
			}
			if ($item_value == "")
			{
				continue;
			}
			if ($is_text)
			{
				$item_value = "Runtime.UI.Drivers.RenderDriver.text(" . \Runtime\rtl::toStr("layout,") . \Runtime\rtl::toStr($item_value) . \Runtime\rtl::toStr(",") . \Runtime\rtl::toStr("control,") . \Runtime\rtl::toStr($i) . \Runtime\rtl::toStr(")");
			}
			else if ($is_raw)
			{
				$item_value = "Runtime.UI.Drivers.RenderDriver.raw(" . \Runtime\rtl::toStr("layout,") . \Runtime\rtl::toStr($item_value) . \Runtime\rtl::toStr(",") . \Runtime\rtl::toStr("control,") . \Runtime\rtl::toStr($i) . \Runtime\rtl::toStr(")");
			}
			$content .= \Runtime\rtl::toStr($t->s($__ctx, $var_name . \Runtime\rtl::toStr(".push(") . \Runtime\rtl::toStr($item_value) . \Runtime\rtl::toStr(");")));
		}
		$res = $t->staticMethod("addSaveOpCode")($__ctx, $t, \Runtime\Dict::from(["op_code"=>$op_code,"var_name"=>$var_name,"content"=>$content]));
		$t = $res[0];
		return \Runtime\Collection::from([$t,$var_name]);
	}
	/* ======================= Class Init Functions ======================= */
	function getClassName()
	{
		return "Bayrell.Lang.LangES6.TranslatorES6Html";
	}
	static function getCurrentNamespace()
	{
		return "Bayrell.Lang.LangES6";
	}
	static function getCurrentClassName()
	{
		return "Bayrell.Lang.LangES6.TranslatorES6Html";
	}
	static function getParentClassName()
	{
		return "";
	}
	static function getClassInfo($__ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($__ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.LangES6.TranslatorES6Html",
			"name"=>"Bayrell.Lang.LangES6.TranslatorES6Html",
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