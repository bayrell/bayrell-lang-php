<?php
/*!
 *  Bayrell Language
 *
 *  (c) Copyright 2016-2020 "Ildar Bikmamatov" <support@bayrell.org>
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
	static function isComponent($ctx, $tag_name)
	{
		if ($tag_name == "")
		{
			return false;
		}
		$ch1 = \Runtime\rs::substr($ctx, $tag_name, 0, 1);
		$ch2 = \Runtime\rs::strtoupper($ctx, $ch1);
		return $ch1 == "{" || $ch1 == $ch2;
	}
	/**
	 * Translator html value
	 */
	static function OpHtmlAttr($ctx, $t, $attr, $item_pos)
	{
		$op_code = $attr->value;
		if ($attr instanceof \Bayrell\Lang\OpCodes\OpString)
		{
			return \Runtime\Collection::from([$t,$t->expression::toString($ctx, $op_code->value)]);
		}
		if ($op_code instanceof \Bayrell\Lang\OpCodes\OpHtmlValue)
		{
			if ($op_code->kind == \Bayrell\Lang\OpCodes\OpHtmlValue::KIND_RAW)
			{
				$res = $t->expression::Expression($ctx, $t, $op_code->value);
				$t = $res[0];
				$value = $res[1];
				return \Runtime\Collection::from([$t,$value]);
			}
			else if ($op_code->kind == \Bayrell\Lang\OpCodes\OpHtmlValue::KIND_JSON)
			{
				$res = $t->expression::Expression($ctx, $t, $op_code->value);
				$t = $res[0];
				$value = $res[1];
				$value = $t->expression::useModuleName($ctx, $t, "RenderHelper") . \Runtime\rtl::toStr(".json_encode(ctx, ") . \Runtime\rtl::toStr($value) . \Runtime\rtl::toStr(")");
				return \Runtime\Collection::from([$t,$value]);
			}
		}
		$res = $t->expression::Expression($ctx, $t, $op_code);
		$t = $res[0];
		$value = $res[1];
		$value = $t->o($ctx, $value, $res[0]->opcode_level, 13);
		return \Runtime\Collection::from([$t,$value]);
	}
	/**
	 * Translator html template
	 */
	static function OpHtmlAttrs($ctx, $t, $attrs, $item_pos)
	{
		$attr_class = new \Runtime\Vector($ctx);
		$attr_s = "null";
		$attr_key_value = "";
		$has_attr_key = false;
		$v_model = "";
		$model = $attrs->findItem($ctx, \Runtime\lib::equalAttr($ctx, "key", "@model"));
		if (!$model)
		{
			$bind = $attrs->findItem($ctx, \Runtime\lib::equalAttr($ctx, "key", "@bind"));
			if ($bind)
			{
				$res = $t->expression::Expression($ctx, $t, $bind->value);
				$t = $res[0];
				$v_model = "model[" . \Runtime\rtl::toStr($res[1]) . \Runtime\rtl::toStr("]");
			}
		}
		$attrs = $attrs->map($ctx, function ($ctx, $attr) use (&$t,&$v_model,&$attr_class,&$attr_key_value,&$has_attr_key,&$item_pos)
		{
			$res = static::OpHtmlAttr($ctx, $t, $attr);
			$t = $res[0];
			$attr_value = $res[1];
			$attr_key = $attr->key;
			$ch = \Runtime\rs::substr($ctx, $attr_key, 0, 1);
			if ($attr_key == "@class")
			{
				$attr_class->push($ctx, "this.getCssName(ctx, " . \Runtime\rtl::toStr($attr_value) . \Runtime\rtl::toStr(")"));
				if (!$has_attr_key && $attr->value instanceof \Bayrell\Lang\OpCodes\OpString)
				{
					$arr = \Runtime\rs::split($ctx, " ", $attr->value->value);
					$attr_key_value = $t->expression::toString($ctx, $arr[0] . \Runtime\rtl::toStr("-") . \Runtime\rtl::toStr($item_pos));
					$has_attr_key = true;
				}
				return "";
			}
			else if ($attr_key == "class")
			{
				$attr_class->push($ctx, $attr_value);
				return "";
			}
			else if ($attr_key == "@key")
			{
				$has_attr_key = true;
				$res = static::OpHtmlAttr($ctx, $t, $attr);
				$t = $res[0];
				$attr_value = $res[1];
				$attr_key_value = $attr_value;
				return "";
			}
			if (\Runtime\rs::substr($ctx, $attr_key, 0, 7) == "@event:")
			{
				$event_name = \Runtime\rs::substr($ctx, $attr_key, 7);
				$event_name = $t->expression::findModuleName($ctx, $t, $event_name);
				$attr_key = "@event:" . \Runtime\rtl::toStr($event_name);
			}
			if (\Runtime\rs::substr($ctx, $attr_key, 0, 12) == "@eventAsync:")
			{
				$event_name = \Runtime\rs::substr($ctx, $attr_key, 12);
				$event_name = $t->expression::findModuleName($ctx, $t, $event_name);
				$attr_key = "@eventAsync:" . \Runtime\rtl::toStr($event_name);
			}
			if ($attr_key == "@bind" && $v_model != "")
			{
				$s = "";
				$s = $t->expression::toString($ctx, $attr_key) . \Runtime\rtl::toStr(":") . \Runtime\rtl::toStr($attr_value) . \Runtime\rtl::toStr(",");
				$s .= \Runtime\rtl::toStr($t->expression::toString($ctx, "@model") . \Runtime\rtl::toStr(":") . \Runtime\rtl::toStr($v_model));
				return $s;
			}
			return $t->expression::toString($ctx, $attr_key) . \Runtime\rtl::toStr(":") . \Runtime\rtl::toStr($attr_value);
		});
		$attrs = $attrs->filter($ctx, function ($ctx, $s)
		{
			return $s != "";
		});
		if ($attr_class->count($ctx) > 0)
		{
			$attrs = $attrs->pushIm($ctx, "\"class\":" . \Runtime\rtl::toStr(\Runtime\rs::join($ctx, " + \" \" + ", $attr_class)));
		}
		if ($attr_key_value != "")
		{
			$attrs = $attrs->pushIm($ctx, "\"@key\":" . \Runtime\rtl::toStr($attr_key_value));
		}
		if ($attrs->count($ctx) > 0)
		{
			$attr_s = "{" . \Runtime\rtl::toStr(\Runtime\rs::join($ctx, ",", $attrs)) . \Runtime\rtl::toStr("}");
		}
		return \Runtime\Collection::from([$t,$attr_s]);
	}
	/**
	 * Translator html template
	 */
	static function OpHtmlTag($ctx, $t, $op_code, $item_pos, $var_name)
	{
		$content = "";
		$content2 = "";
		$str_var_name = $t->expression::toString($ctx, $var_name);
		if ($op_code instanceof \Bayrell\Lang\OpCodes\OpHtmlContent)
		{
			$item_value = $t->expression::toString($ctx, $op_code->value);
			$content .= \Runtime\rtl::toStr($t->s($ctx, "[__vnull, " . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr("_childs] = ") . \Runtime\rtl::toStr("RenderDriver.insert(") . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr(", ") . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr("_childs") . \Runtime\rtl::toStr(", \"text\", {\"content\": ") . \Runtime\rtl::toStr($item_value) . \Runtime\rtl::toStr("}, ") . \Runtime\rtl::toStr($item_pos) . \Runtime\rtl::toStr(");")));
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpHtmlValue)
		{
			if ($op_code->kind == \Bayrell\Lang\OpCodes\OpHtmlValue::KIND_RAW)
			{
				$res = $t->expression::Expression($ctx, $t, $op_code->value);
				$t = $res[0];
				$item_value = $res[1];
				$content .= \Runtime\rtl::toStr($t->s($ctx, "[__vnull, " . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr("_childs] = ") . \Runtime\rtl::toStr("RenderDriver.insert(") . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr(", ") . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr("_childs") . \Runtime\rtl::toStr(", \"raw\", {\"content\": ") . \Runtime\rtl::toStr($item_value) . \Runtime\rtl::toStr("}, ") . \Runtime\rtl::toStr($item_pos) . \Runtime\rtl::toStr(");")));
			}
			else if ($op_code->kind == \Bayrell\Lang\OpCodes\OpHtmlValue::KIND_HTML)
			{
				$res = $t->expression::Expression($ctx, $t, $op_code->value);
				$t = $res[0];
				$item_value = $res[1];
				$content .= \Runtime\rtl::toStr($t->s($ctx, "[__vnull, " . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr("_childs] = ") . \Runtime\rtl::toStr("RenderDriver.insert(") . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr(", ") . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr("_childs") . \Runtime\rtl::toStr(", \"html\", {\"content\": ") . \Runtime\rtl::toStr($item_value) . \Runtime\rtl::toStr("}, ") . \Runtime\rtl::toStr($item_pos) . \Runtime\rtl::toStr(");")));
			}
			else if ($op_code->kind == \Bayrell\Lang\OpCodes\OpHtmlValue::KIND_JSON)
			{
				$res = $t->expression::Expression($ctx, $t, $op_code->value);
				$t = $res[0];
				$item_value = $res[1];
				$item_value = "this.json_encode(ctx, " . \Runtime\rtl::toStr($item_value) . \Runtime\rtl::toStr(")");
				$content .= \Runtime\rtl::toStr($t->s($ctx, "[__vnull, " . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr("_childs] = ") . \Runtime\rtl::toStr("RenderDriver.insert(") . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr(", ") . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr("_childs") . \Runtime\rtl::toStr(", \"text\", {\"content\": ") . \Runtime\rtl::toStr($item_value) . \Runtime\rtl::toStr("}, ") . \Runtime\rtl::toStr($item_pos) . \Runtime\rtl::toStr(");")));
			}
		}
		else if ($op_code instanceof \Bayrell\Lang\OpCodes\OpHtmlTag)
		{
			$new_var_name = "";
			$has_childs = $op_code->items != null && $op_code->items->items != null && $op_code->items->items->count($ctx) > 0;
			$is_component = static::isComponent($ctx, $op_code->tag_name);
			$res = static::OpHtmlAttrs($ctx, $t, $op_code->attrs, $item_pos);
			$t = $res[0];
			$attrs = $res[1];
			if ($op_code->tag_name == "")
			{
				if ($has_childs)
				{
					$res = $t::incSaveOpCode($ctx, $t);
					$t = $res[0];
					$new_var_name = $res[1];
					$content .= \Runtime\rtl::toStr($t->s2($ctx, ""));
					$content .= \Runtime\rtl::toStr($t->s($ctx, "/* Items */"));
					$content .= \Runtime\rtl::toStr($t->s($ctx, "var " . \Runtime\rtl::toStr($new_var_name) . \Runtime\rtl::toStr("; var ") . \Runtime\rtl::toStr($new_var_name) . \Runtime\rtl::toStr("_childs = [];")));
					$content .= \Runtime\rtl::toStr($t->s($ctx, "[" . \Runtime\rtl::toStr($new_var_name) . \Runtime\rtl::toStr(", ") . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr("_childs] = ") . \Runtime\rtl::toStr("RenderDriver.insert(") . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr(", ") . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr("_childs") . \Runtime\rtl::toStr(", \"empty\", null, ") . \Runtime\rtl::toStr($item_pos) . \Runtime\rtl::toStr(");")));
				}
				else
				{
					$content .= \Runtime\rtl::toStr($t->s($ctx, "[__vnull, " . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr("_childs] = ") . \Runtime\rtl::toStr("RenderDriver.insert(") . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr(", ") . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr("_childs") . \Runtime\rtl::toStr(", \"empty\", null, ") . \Runtime\rtl::toStr($item_pos) . \Runtime\rtl::toStr(");")));
				}
			}
			else if ($is_component)
			{
				$tag_name = "";
				if ($op_code->op_code_name)
				{
					$res = $t->expression::Expression($ctx, $t, $op_code->op_code_name);
					$t = $res[0];
					$tag_name = $res[1];
				}
				else
				{
					$tag_name = $t->expression::toString($ctx, $t->expression::findModuleName($ctx, $t, $op_code->tag_name));
				}
				if ($has_childs)
				{
					$res = static::OpHtmlItems($ctx, $t, $op_code->items);
					$t = $res[0];
					$f = $res[1];
					$content .= \Runtime\rtl::toStr($t->s2($ctx, ""));
					$content .= \Runtime\rtl::toStr($t->s($ctx, "/* Component '" . \Runtime\rtl::toStr($op_code->tag_name) . \Runtime\rtl::toStr("' */")));
					$content .= \Runtime\rtl::toStr($t->s($ctx, "[__vnull, " . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr("_childs] = ") . \Runtime\rtl::toStr("RenderDriver.insert(") . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr(", ") . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr("_childs") . \Runtime\rtl::toStr(", \"component\", {\"name\": ") . \Runtime\rtl::toStr($tag_name) . \Runtime\rtl::toStr(",\"attrs\": ") . \Runtime\rtl::toStr($attrs) . \Runtime\rtl::toStr(", \"layout\": layout, \"content\": ") . \Runtime\rtl::toStr($f) . \Runtime\rtl::toStr("}, ") . \Runtime\rtl::toStr($item_pos) . \Runtime\rtl::toStr(");")));
					$has_childs = false;
				}
				else
				{
					$content .= \Runtime\rtl::toStr($t->s($ctx, "[__vnull, " . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr("_childs] = ") . \Runtime\rtl::toStr("RenderDriver.insert(") . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr(", ") . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr("_childs") . \Runtime\rtl::toStr(", \"component\", {\"name\": ") . \Runtime\rtl::toStr($tag_name) . \Runtime\rtl::toStr(",\"attrs\": ") . \Runtime\rtl::toStr($attrs) . \Runtime\rtl::toStr(", \"layout\": layout}, ") . \Runtime\rtl::toStr($item_pos) . \Runtime\rtl::toStr(");")));
				}
			}
			else
			{
				$tag_name = $t->expression::toString($ctx, $op_code->tag_name);
				if ($has_childs)
				{
					$res = $t::incSaveOpCode($ctx, $t);
					$t = $res[0];
					$new_var_name = $res[1];
					$content .= \Runtime\rtl::toStr($t->s2($ctx, ""));
					$content .= \Runtime\rtl::toStr($t->s($ctx, "/* Element '" . \Runtime\rtl::toStr($op_code->tag_name) . \Runtime\rtl::toStr("' */")));
					$content .= \Runtime\rtl::toStr($t->s($ctx, "var " . \Runtime\rtl::toStr($new_var_name) . \Runtime\rtl::toStr("; var ") . \Runtime\rtl::toStr($new_var_name) . \Runtime\rtl::toStr("_childs = [];")));
					$content .= \Runtime\rtl::toStr($t->s($ctx, "[" . \Runtime\rtl::toStr($new_var_name) . \Runtime\rtl::toStr(", ") . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr("_childs] = ") . \Runtime\rtl::toStr("RenderDriver.insert(") . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr(", ") . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr("_childs") . \Runtime\rtl::toStr(", \"element\", {\"name\": ") . \Runtime\rtl::toStr($tag_name) . \Runtime\rtl::toStr(",\"attrs\": ") . \Runtime\rtl::toStr($attrs) . \Runtime\rtl::toStr("}, ") . \Runtime\rtl::toStr($item_pos) . \Runtime\rtl::toStr(");")));
				}
				else
				{
					$content .= \Runtime\rtl::toStr($t->s($ctx, "[__vnull, " . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr("_childs] = ") . \Runtime\rtl::toStr("RenderDriver.insert(") . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr(", ") . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr("_childs") . \Runtime\rtl::toStr(", \"element\", {\"name\": ") . \Runtime\rtl::toStr($tag_name) . \Runtime\rtl::toStr(",\"attrs\": ") . \Runtime\rtl::toStr($attrs) . \Runtime\rtl::toStr("}, ") . \Runtime\rtl::toStr($item_pos) . \Runtime\rtl::toStr(");")));
				}
			}
			if ($has_childs)
			{
				$res = static::OpHtmlChilds($ctx, $t, $op_code->items, $new_var_name);
				$t = $res[0];
				$content .= \Runtime\rtl::toStr($res[1]);
			}
		}
		else
		{
			$res = $t->expression::Expression($ctx, $t, $op_code);
			$t = $res[0];
			$item_value = $res[1];
			$content .= \Runtime\rtl::toStr($t->s($ctx, "[__vnull, " . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr("_childs] = ") . \Runtime\rtl::toStr("RenderDriver.insert(") . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr(", ") . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr("_childs") . \Runtime\rtl::toStr(", \"text\", {\"content\": ") . \Runtime\rtl::toStr($item_value) . \Runtime\rtl::toStr("}, ") . \Runtime\rtl::toStr($item_pos) . \Runtime\rtl::toStr(");")));
		}
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * Translator html items
	 */
	static function OpHtmlChilds($ctx, $t, $op_code, $control_name)
	{
		if ($op_code == null || $op_code->items->count($ctx) == 0)
		{
			return \Runtime\Collection::from([$t,""]);
		}
		$content = "";
		for ($i = 0;$i < $op_code->items->count($ctx);$i++)
		{
			$item = $op_code->items->item($ctx, $i);
			$res = static::OpHtmlTag($ctx, $t, $item, $i, $control_name);
			$t = $res[0];
			$content .= \Runtime\rtl::toStr($res[1]);
		}
		if ($control_name != "control")
		{
			$content .= \Runtime\rtl::toStr($t->s($ctx, "RenderDriver.patch(" . \Runtime\rtl::toStr($control_name) . \Runtime\rtl::toStr(", ") . \Runtime\rtl::toStr($control_name) . \Runtime\rtl::toStr("_childs);")));
		}
		return \Runtime\Collection::from([$t,$content]);
	}
	/**
	 * Translator html items
	 */
	static function OpHtmlItems($ctx, $t, $op_code)
	{
		if ($op_code == null || $op_code->items->count($ctx) == 0)
		{
			return \Runtime\Collection::from([$t,""]);
		}
		/* Save op codes */
		$save_t = $t;
		$save_op_codes = $t->save_op_codes;
		$save_op_code_inc = $t->save_op_code_inc;
		$t = $t::clearSaveOpCode($ctx, $t);
		$content = "";
		$content .= \Runtime\rtl::toStr("(control) =>");
		$content .= \Runtime\rtl::toStr($t->s($ctx, "{"));
		$t = $t->levelInc($ctx);
		$content .= \Runtime\rtl::toStr($t->s($ctx, "var __vnull = null;"));
		$content .= \Runtime\rtl::toStr($t->s($ctx, "var control_childs = [];"));
		$res = static::OpHtmlChilds($ctx, $t, $op_code, "control");
		$t = $res[0];
		$content .= \Runtime\rtl::toStr($res[1]);
		$content .= \Runtime\rtl::toStr($t->s2($ctx, ""));
		$content .= \Runtime\rtl::toStr($t->s($ctx, "return control_childs;"));
		$t = $t->levelDec($ctx);
		$content .= \Runtime\rtl::toStr($t->s($ctx, "}"));
		/* Restore save op codes */
		$t = $t->copy($ctx, ["save_op_codes"=>$save_op_codes]);
		$t = $t->copy($ctx, ["save_op_code_inc"=>$save_op_code_inc]);
		return \Runtime\Collection::from([$t,$content]);
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
	static function getClassInfo($ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.LangES6.TranslatorES6Html",
			"name"=>"Bayrell.Lang.LangES6.TranslatorES6Html",
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