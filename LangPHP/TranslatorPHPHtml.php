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
namespace Bayrell\Lang\LangPHP;
class TranslatorPHPHtml
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
	 * Is single tag
	 */
	static function isSingleTag($__ctx, $tag_name)
	{
		$tokens = \Runtime\Collection::from(["img","meta","input","link","br"]);
		if ($tokens->indexOf($__ctx, $tag_name) == -1)
		{
			return false;
		}
		return true;
	}
	/**
	 * Translator html attr
	 */
	static function OpHtmlAttr($__ctx, $t, $attr)
	{
		if ($attr->value instanceof \Bayrell\Lang\OpCodes\OpHtmlValue)
		{
			if ($attr->value->kind == \Bayrell\Lang\OpCodes\OpHtmlValue::KIND_RAW)
			{
				$res = $t->expression->staticMethod("Expression")($__ctx, $t, $attr->value->value);
				$t = $res[0];
				return \Runtime\Collection::from([$t,$res[1]]);
			}
			else if ($attr->value->kind == \Bayrell\Lang\OpCodes\OpHtmlValue::KIND_JSON)
			{
				$res = $t->expression->staticMethod("Expression")($__ctx, $t, $attr->value->value);
				$t = $res[0];
				$value = $res[1];
				$value = "static::json_encode($__ctx, " . \Runtime\rtl::toStr($value) . \Runtime\rtl::toStr(")");
				return \Runtime\Collection::from([$t,$value]);
			}
		}
		$res = $t->expression->staticMethod("Expression")($__ctx, $t, $attr->value);
		$t = $res[0];
		$value = $res[1];
		return \Runtime\Collection::from([$t,$value]);
	}
	/**
	 * Translator html component
	 */
	static function OpHtmlComponent($__ctx, $t, $op_code)
	{
		$res = $t->staticMethod("incSaveOpCode")($__ctx, $t);
		$t = $res[0];
		$var_name = $res[1];
		$content = "";
		$v_model = "null";
		$tag_name = $op_code->tag_name;
		$module_name = "";
		if ($op_code->op_code_name)
		{
			$res = $t->expression->staticMethod("Expression")($__ctx, $t, $op_code->op_code_name);
			$t = $res[0];
			$module_name = $res[1];
		}
		else
		{
			$module_name = $t->expression->staticMethod("toString")($__ctx, $t->expression->staticMethod("findModuleName")($__ctx, $t, $op_code->tag_name));
		}
		$model = $op_code->attrs->findItem($__ctx, \Runtime\lib::equalAttr($__ctx, "key", "@model"));
		if ($model)
		{
			$res = $t->expression->staticMethod("Expression")($__ctx, $t, $model->value);
			$t = $res[0];
			$v_model = $res[1];
		}
		else
		{
			$bind = $op_code->attrs->findItem($__ctx, \Runtime\lib::equalAttr($__ctx, "key", "@bind"));
			if ($bind)
			{
				$res = $t->expression->staticMethod("Expression")($__ctx, $t, $bind->value);
				$t = $res[0];
				$v_model = "$model[" . \Runtime\rtl::toStr($res[1]) . \Runtime\rtl::toStr("]");
			}
		}
		$content .= \Runtime\rtl::toStr($t->s($__ctx, "/* Component '" . \Runtime\rtl::toStr($tag_name) . \Runtime\rtl::toStr("' */")));
		$content .= \Runtime\rtl::toStr($t->s($__ctx, $var_name . \Runtime\rtl::toStr("_params = [];")));
		for ($i = 0;$i < $op_code->attrs->count($__ctx);$i++)
		{
			$attr = $op_code->attrs->item($__ctx, $i);
			if ($attr->key == "@bind")
			{
				continue;
			}
			if ($attr->key == "@model")
			{
				continue;
			}
			$res = static::OpHtmlAttr($__ctx, $t, $attr);
			$t = $res[0];
			$attr_value = $res[1];
			$content .= \Runtime\rtl::toStr($t->s($__ctx, $var_name . \Runtime\rtl::toStr("_params[") . \Runtime\rtl::toStr($t->expression->staticMethod("toString")($__ctx, $attr->key)) . \Runtime\rtl::toStr("] = ") . \Runtime\rtl::toStr($attr_value) . \Runtime\rtl::toStr(";")));
		}
		$content .= \Runtime\rtl::toStr($t->s($__ctx, $var_name . \Runtime\rtl::toStr("_content = \"\";")));
		$f = \Runtime\rtl::method($__ctx, static::getCurrentClassName($__ctx), "OpHtmlItems");
		$res = $t->staticMethod("saveOpCodeCall")($__ctx, $t, $f, \Runtime\Collection::from([$op_code->items,$var_name . \Runtime\rtl::toStr("_content")]));
		$t = $res[0];
		$content .= \Runtime\rtl::toStr($res[1]);
		/*content ~= t.s(var_name~"_content .= " ~ res[2] ~ ";");*/
		if ($op_code->op_code_name)
		{
			$content .= \Runtime\rtl::toStr($t->s($__ctx, $var_name . \Runtime\rtl::toStr("_name = \\Runtime\\rtl::find_class(") . \Runtime\rtl::toStr($module_name) . \Runtime\rtl::toStr(");")));
			$content .= \Runtime\rtl::toStr($t->s($__ctx, $var_name . \Runtime\rtl::toStr(" = ") . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr("_name::render($__ctx, $layout,") . \Runtime\rtl::toStr($v_model) . \Runtime\rtl::toStr(",\\Runtime\\Dict::from(") . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr("_params),") . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr("_content,$control);")));
		}
		else
		{
			$content .= \Runtime\rtl::toStr($t->s($__ctx, $var_name . \Runtime\rtl::toStr("_name = \\Runtime\\rtl::find_class(") . \Runtime\rtl::toStr($module_name) . \Runtime\rtl::toStr(");")));
			$content .= \Runtime\rtl::toStr($t->s($__ctx, $var_name . \Runtime\rtl::toStr(" = ") . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr("_name::render($__ctx, $layout,") . \Runtime\rtl::toStr($v_model) . \Runtime\rtl::toStr(",\\Runtime\\Dict::from(") . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr("_params),") . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr("_content,$control);")));
		}
		$res = $t->staticMethod("addSaveOpCode")($__ctx, $t, \Runtime\Dict::from(["op_code"=>$op_code,"var_name"=>$var_name,"content"=>$content]));
		$t = $res[0];
		return \Runtime\Collection::from([$t,$var_name]);
	}
	/**
	 * Translator html template
	 */
	static function OpHtmlTag($__ctx, $t, $op_code)
	{
		if (static::isComponent($__ctx, $op_code->tag_name))
		{
			return static::OpHtmlComponent($__ctx, $t, $op_code);
		}
		$res = $t->staticMethod("incSaveOpCode")($__ctx, $t);
		$t = $res[0];
		$var_name = $res[1];
		$attr_s = "";
		$attrs = $op_code->attrs->map($__ctx, function ($__ctx, $attr) use (&$t,&$op_code)
		{
			$attr_value = "";
			$key = $attr->key;
			if ($key == "@class" && $attr->value instanceof \Bayrell\Lang\OpCodes\OpString)
			{
				return "class=" . \Runtime\rtl::toStr("\"'.static::getCssName($__ctx, ") . \Runtime\rtl::toStr($t->expression->staticMethod("toString")($__ctx, $attr->value->value)) . \Runtime\rtl::toStr(").'\"");
			}
			if ($key == "@model" && $op_code->tag_name == "input")
			{
				$key = "value";
			}
			if ($key == "@bind" && $op_code->tag_name == "input")
			{
				$res = $t->expression->staticMethod("Expression")($__ctx, $t, $attr->value);
				$t = $res[0];
				$attr_value = "$model[" . \Runtime\rtl::toStr($res[1]) . \Runtime\rtl::toStr("]");
				$key = "value";
			}
			$ch = \Runtime\rs::substr($__ctx, $key, 0, 1);
			if ($ch == "@")
			{
				return "";
			}
			if ($attr_value == "")
			{
				$res = static::OpHtmlAttr($__ctx, $t, $attr);
				$t = $res[0];
				$attr_value = $res[1];
			}
			return $key . \Runtime\rtl::toStr("=\"'.static::escapeAttr($__ctx, ") . \Runtime\rtl::toStr($attr_value) . \Runtime\rtl::toStr(").'\"");
		});
		$attrs = $attrs->filter($__ctx, function ($__ctx, $s)
		{
			return $s != "";
		});
		if ($attrs->count($__ctx) > 0)
		{
			$attr_s = " " . \Runtime\rtl::toStr(\Runtime\rs::join($__ctx, " ", $attrs));
		}
		$content = "/* Element '" . \Runtime\rtl::toStr($op_code->tag_name) . \Runtime\rtl::toStr("' */");
		if (static::isSingleTag($__ctx, $op_code->tag_name))
		{
			$content .= \Runtime\rtl::toStr($t->s($__ctx, $var_name . \Runtime\rtl::toStr(" = '<") . \Runtime\rtl::toStr($op_code->tag_name) . \Runtime\rtl::toStr($attr_s) . \Runtime\rtl::toStr(" />';")));
		}
		else
		{
			$content .= \Runtime\rtl::toStr($t->s($__ctx, $var_name . \Runtime\rtl::toStr(" = '<") . \Runtime\rtl::toStr($op_code->tag_name) . \Runtime\rtl::toStr($attr_s) . \Runtime\rtl::toStr(">';")));
			$flag_value = false;
			if ($op_code->tag_name == "textarea")
			{
				$model_attr = $op_code->attrs->findItem($__ctx, \Runtime\lib::equalAttr($__ctx, "key", "@model"));
				if ($model_attr != null)
				{
					$res = static::OpHtmlAttr($__ctx, $t, $model_attr);
					$t = $res[0];
					$attr_value = $res[1];
					if ($model_attr instanceof \Bayrell\Lang\OpCodes\OpHtmlValue)
					{
						$content .= \Runtime\rtl::toStr($t->s($__ctx, $var_name . \Runtime\rtl::toStr(" .= ") . \Runtime\rtl::toStr($attr_value) . \Runtime\rtl::toStr(";")));
					}
					else
					{
						$content .= \Runtime\rtl::toStr($t->s($__ctx, $var_name . \Runtime\rtl::toStr(" .= static::escapeHtml($__ctx, ") . \Runtime\rtl::toStr($attr_value) . \Runtime\rtl::toStr(");")));
					}
					$flag_value = true;
				}
			}
			if (!$flag_value)
			{
				$f = \Runtime\rtl::method($__ctx, static::getCurrentClassName($__ctx), "OpHtmlItems");
				$res = $t->staticMethod("saveOpCodeCall")($__ctx, $t, $f, \Runtime\Collection::from([$op_code->items,$var_name]));
				$t = $res[0];
				$content .= \Runtime\rtl::toStr($res[1]);
			}
			$content .= \Runtime\rtl::toStr($t->s($__ctx, $var_name . \Runtime\rtl::toStr(" .= '</") . \Runtime\rtl::toStr($op_code->tag_name) . \Runtime\rtl::toStr(">';")));
		}
		$res = $t->staticMethod("addSaveOpCode")($__ctx, $t, \Runtime\Dict::from(["op_code"=>$op_code,"var_name"=>$var_name,"content"=>$content]));
		$t = $res[0];
		return \Runtime\Collection::from([$t,$var_name]);
	}
	/**
	 * Translator html items
	 */
	static function OpHtmlItems($__ctx, $t, $op_code, $var_name="")
	{
		if ($op_code->items->count($__ctx) == 0)
		{
			return \Runtime\Collection::from([$t,""]);
		}
		$content = "";
		if ($var_name == "")
		{
			$res = $t->staticMethod("incSaveOpCode")($__ctx, $t);
			$t = $res[0];
			$var_name = $res[1];
			$content .= \Runtime\rtl::toStr($t->s($__ctx, $var_name . \Runtime\rtl::toStr(" = \"\";")));
		}
		for ($i = 0;$i < $op_code->items->count($__ctx);$i++)
		{
			$item = $op_code->items->item($__ctx, $i);
			$item_value = "";
			if ($item instanceof \Bayrell\Lang\OpCodes\OpHtmlContent)
			{
				$item_value = $t->expression->staticMethod("toString")($__ctx, $item->value);
			}
			else if ($item instanceof \Bayrell\Lang\OpCodes\OpHtmlTag)
			{
				$res = static::OpHtmlTag($__ctx, $t, $item);
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
				}
				else if ($item->kind == \Bayrell\Lang\OpCodes\OpHtmlValue::KIND_JSON)
				{
					$res = $t->expression->staticMethod("Expression")($__ctx, $t, $item->value);
					$t = $res[0];
					$item_value = $res[1];
					$item_value = "static::json_encode($__ctx, " . \Runtime\rtl::toStr($item_value) . \Runtime\rtl::toStr(")");
				}
			}
			else
			{
				$res = $t->expression->staticMethod("Expression")($__ctx, $t, $item);
				$t = $res[0];
				$item_value = $res[1];
				$item_value = "static::escapeHtml($__ctx, " . \Runtime\rtl::toStr($item_value) . \Runtime\rtl::toStr(")");
			}
			$content .= \Runtime\rtl::toStr($t->s($__ctx, $var_name . \Runtime\rtl::toStr(" .= ") . \Runtime\rtl::toStr($item_value) . \Runtime\rtl::toStr(";")));
		}
		$res = $t->staticMethod("addSaveOpCode")($__ctx, $t, \Runtime\Dict::from(["op_code"=>$op_code,"var_name"=>$var_name,"content"=>$content]));
		$t = $res[0];
		return \Runtime\Collection::from([$t,$var_name]);
	}
	/* ======================= Class Init Functions ======================= */
	function getClassName()
	{
		return "Bayrell.Lang.LangPHP.TranslatorPHPHtml";
	}
	static function getCurrentNamespace()
	{
		return "Bayrell.Lang.LangPHP";
	}
	static function getCurrentClassName()
	{
		return "Bayrell.Lang.LangPHP.TranslatorPHPHtml";
	}
	static function getParentClassName()
	{
		return "";
	}
	static function getClassInfo($__ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($__ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.LangPHP.TranslatorPHPHtml",
			"name"=>"Bayrell.Lang.LangPHP.TranslatorPHPHtml",
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