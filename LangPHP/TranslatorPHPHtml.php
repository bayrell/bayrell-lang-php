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
namespace Bayrell\Lang\LangPHP;
class TranslatorPHPHtml
{
	/**
	 * Is component
	 */
	static function isComponent($ctx, $tag_name)
	{
		$ch1 = \Runtime\rs::substr($ctx, $tag_name, 0, 1);
		$ch2 = \Runtime\rs::strtoupper($ctx, $ch1);
		return $ch1 == "{" || $ch1 == $ch2;
	}
	/**
	 * Is single tag
	 */
	static function isSingleTag($ctx, $tag_name)
	{
		$tokens = \Runtime\Collection::from(["img","meta","input","link","br"]);
		if ($tokens->indexOf($ctx, $tag_name) == -1)
		{
			return false;
		}
		return true;
	}
	/**
	 * Translator html component
	 */
	static function OpHtmlComponent($ctx, $t, $op_code)
	{
		$res = $t::incSaveOpCode($ctx, $t);
		$t = $res[0];
		$var_name = $res[1];
		$content = "";
		$v_model = "null";
		$tag_name = $op_code->tag_name;
		$module_name = "";
		if ($op_code->op_code_name)
		{
			$res = $t->expression::Expression($ctx, $t, $op_code->op_code_name);
			$t = $res[0];
			$module_name = $res[1];
		}
		else
		{
			$module_name = $t->expression::toString($ctx, $t->expression::findModuleName($ctx, $t, $op_code->tag_name));
		}
		$model = $op_code->attrs->findItem($ctx, \Runtime\lib::equalAttr($ctx, "key", "@model"));
		if ($model)
		{
			$res = $t->expression::Expression($ctx, $t, $model->value);
			$t = $res[0];
			$v_model = $res[1];
		}
		else
		{
			$bind = $op_code->attrs->findItem($ctx, \Runtime\lib::equalAttr($ctx, "key", "@bind"));
			if ($bind)
			{
				$res = $t->expression::Expression($ctx, $t, $bind->value);
				$t = $res[0];
				$v_model = "$model[" . \Runtime\rtl::toStr($res[1]) . \Runtime\rtl::toStr("]");
			}
		}
		$content .= \Runtime\rtl::toStr($t->s($ctx, "/* Component '" . \Runtime\rtl::toStr($tag_name) . \Runtime\rtl::toStr("' */")));
		$content .= \Runtime\rtl::toStr($t->s($ctx, $var_name . \Runtime\rtl::toStr("_params = [];")));
		for ($i = 0;$i < $op_code->attrs->count($ctx);$i++)
		{
			$attr = $op_code->attrs->item($ctx, $i);
			if ($attr->key == "@bind")
			{
				continue;
			}
			if ($attr->key == "@model")
			{
				continue;
			}
			$res = static::OpHtmlAttr($ctx, $t, $attr);
			$t = $res[0];
			$attr_value = $res[1];
			$content .= \Runtime\rtl::toStr($t->s($ctx, $var_name . \Runtime\rtl::toStr("_params[") . \Runtime\rtl::toStr($t->expression::toString($ctx, $attr->key)) . \Runtime\rtl::toStr("] = ") . \Runtime\rtl::toStr($attr_value) . \Runtime\rtl::toStr(";")));
		}
		$content .= \Runtime\rtl::toStr($t->s($ctx, $var_name . \Runtime\rtl::toStr("_content = \"\";")));
		$f = \Runtime\rtl::method($ctx, static::getCurrentClassName($ctx), "OpHtmlItems");
		$res = $t::saveOpCodeCall($ctx, $t, $f, \Runtime\Collection::from([$op_code->items,$var_name . \Runtime\rtl::toStr("_content")]));
		$t = $res[0];
		$content .= \Runtime\rtl::toStr($res[1]);
		/*content ~= t.s(var_name~"_content .= " ~ res[2] ~ ";");*/
		if ($op_code->op_code_name)
		{
			$content .= \Runtime\rtl::toStr($t->s($ctx, $var_name . \Runtime\rtl::toStr("_name = \\Runtime\\rtl::find_class(") . \Runtime\rtl::toStr($module_name) . \Runtime\rtl::toStr(");")));
			$content .= \Runtime\rtl::toStr($t->s($ctx, $var_name . \Runtime\rtl::toStr(" = ") . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr("_name::render($ctx, $layout,") . \Runtime\rtl::toStr($v_model) . \Runtime\rtl::toStr(",\\Runtime\\Dict::from(") . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr("_params),") . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr("_content);")));
		}
		else
		{
			$content .= \Runtime\rtl::toStr($t->s($ctx, $var_name . \Runtime\rtl::toStr("_name = \\Runtime\\rtl::find_class(") . \Runtime\rtl::toStr($module_name) . \Runtime\rtl::toStr(");")));
			$content .= \Runtime\rtl::toStr($t->s($ctx, $var_name . \Runtime\rtl::toStr(" = ") . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr("_name::render($ctx, $layout,") . \Runtime\rtl::toStr($v_model) . \Runtime\rtl::toStr(",\\Runtime\\Dict::from(") . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr("_params),") . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr("_content);")));
		}
		$res = $t::addSaveOpCode($ctx, $t, \Runtime\Dict::from(["op_code"=>$op_code,"var_name"=>$var_name,"content"=>$content]));
		$t = $res[0];
		return \Runtime\Collection::from([$t,$var_name]);
	}
	/**
	 * Translator html attr
	 */
	static function OpHtmlAttr($ctx, $t, $attr)
	{
		if ($attr->value instanceof \Bayrell\Lang\OpCodes\OpString)
		{
			return \Runtime\Collection::from([$t,$t->expression::toString($ctx, $attr->value->value)]);
		}
		if ($attr->value instanceof \Bayrell\Lang\OpCodes\OpHtmlValue)
		{
			if ($attr->value->kind == \Bayrell\Lang\OpCodes\OpHtmlValue::KIND_RAW)
			{
				$res = $t->expression::Expression($ctx, $t, $attr->value->value);
				$t = $res[0];
				return \Runtime\Collection::from([$t,$res[1]]);
			}
			else if ($attr->value->kind == \Bayrell\Lang\OpCodes\OpHtmlValue::KIND_JSON)
			{
				$res = $t->expression::Expression($ctx, $t, $attr->value->value);
				$t = $res[0];
				$value = $res[1];
				$value = "static::json_encode($ctx, " . \Runtime\rtl::toStr($value) . \Runtime\rtl::toStr(")");
				return \Runtime\Collection::from([$t,$value]);
			}
		}
		$res = $t->expression::Expression($ctx, $t, $attr->value);
		$t = $res[0];
		$value = $res[1];
		$value = $t->o($ctx, $value, $res[0]->opcode_level, 13);
		return \Runtime\Collection::from([$t,$value]);
	}
	/**
	 * Translator html template
	 */
	static function OpHtmlTag($ctx, $t, $op_code)
	{
		if (static::isComponent($ctx, $op_code->tag_name))
		{
			return static::OpHtmlComponent($ctx, $t, $op_code);
		}
		$attr_class = new \Runtime\Vector($ctx);
		$res = $t::incSaveOpCode($ctx, $t);
		$t = $res[0];
		$var_name = $res[1];
		$attr_s = "";
		$attr_key_value = "";
		$has_attr_key = false;
		$attrs = $op_code->attrs->map($ctx, function ($ctx, $attr) use (&$t,&$op_code,&$attr_class,&$attr_key_value,&$has_attr_key)
		{
			$attr_key = $attr->key;
			$attr_value = "";
			if ($attr_key == "@class")
			{
				$res = static::OpHtmlAttr($ctx, $t, $attr);
				$t = $res[0];
				$attr_value = $res[1];
				$attr_class->push($ctx, "static::getCssName($ctx, " . \Runtime\rtl::toStr($attr_value) . \Runtime\rtl::toStr(")"));
				if (!$has_attr_key && $attr->value instanceof \Bayrell\Lang\OpCodes\OpString)
				{
					$arr = \Runtime\rs::split($ctx, " ", $attr->value->value);
					$attr_key_value = $t->expression::toString($ctx, $arr[0]);
					$has_attr_key = true;
				}
				return "";
			}
			else if ($attr_key == "class")
			{
				$t = $t->copy($ctx, ["opcode_level"=>1000]);
				$res = static::OpHtmlAttr($ctx, $t, $attr);
				$t = $res[0];
				$attr_value = $res[1];
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
			if ($attr_key == "@model" && $op_code->tag_name == "input")
			{
				$attr_key = "value";
			}
			if ($attr_key == "@bind" && $op_code->tag_name == "input")
			{
				$res = $t->expression::Expression($ctx, $t, $attr->value);
				$t = $res[0];
				$attr_value = "$model[" . \Runtime\rtl::toStr($res[1]) . \Runtime\rtl::toStr("]");
				$attr_key = "value";
			}
			$ch = \Runtime\rs::substr($ctx, $attr_key, 0, 1);
			if ($ch == "@")
			{
				return "";
			}
			if ($attr_value == "")
			{
				$res = static::OpHtmlAttr($ctx, $t, $attr);
				$t = $res[0];
				$attr_value = $res[1];
			}
			return $attr_key . \Runtime\rtl::toStr("=\"'.static::escapeAttr($ctx, ") . \Runtime\rtl::toStr($attr_value) . \Runtime\rtl::toStr(").'\"");
		});
		$attrs = $attrs->filter($ctx, function ($ctx, $s)
		{
			return $s != "";
		});
		if ($attr_class->count($ctx) > 0)
		{
			$attrs = $attrs->pushIm($ctx, "class=" . \Runtime\rtl::toStr("\"'.") . \Runtime\rtl::toStr(\Runtime\rs::join($ctx, ".\" \".", $attr_class)) . \Runtime\rtl::toStr(".'\""));
		}
		if ($attrs->count($ctx) > 0)
		{
			$attr_s = " " . \Runtime\rtl::toStr(\Runtime\rs::join($ctx, " ", $attrs));
		}
		$content = "/* Element '" . \Runtime\rtl::toStr($op_code->tag_name) . \Runtime\rtl::toStr("' */");
		if (static::isSingleTag($ctx, $op_code->tag_name))
		{
			$content .= \Runtime\rtl::toStr($t->s($ctx, $var_name . \Runtime\rtl::toStr(" = '<") . \Runtime\rtl::toStr($op_code->tag_name) . \Runtime\rtl::toStr($attr_s) . \Runtime\rtl::toStr(" />';")));
		}
		else
		{
			$content .= \Runtime\rtl::toStr($t->s($ctx, $var_name . \Runtime\rtl::toStr(" = '<") . \Runtime\rtl::toStr($op_code->tag_name) . \Runtime\rtl::toStr($attr_s) . \Runtime\rtl::toStr(">';")));
			$flag_value = false;
			if ($op_code->tag_name == "textarea")
			{
				$model_attr = $op_code->attrs->findItem($ctx, \Runtime\lib::equalAttr($ctx, "key", "@model"));
				if ($model_attr != null)
				{
					$res = static::OpHtmlAttr($ctx, $t, $model_attr);
					$t = $res[0];
					$attr_value = $res[1];
					if ($model_attr instanceof \Bayrell\Lang\OpCodes\OpHtmlValue)
					{
						$content .= \Runtime\rtl::toStr($t->s($ctx, $var_name . \Runtime\rtl::toStr(" .= ") . \Runtime\rtl::toStr($attr_value) . \Runtime\rtl::toStr(";")));
					}
					else
					{
						$content .= \Runtime\rtl::toStr($t->s($ctx, $var_name . \Runtime\rtl::toStr(" .= static::escapeHtml($ctx, ") . \Runtime\rtl::toStr($attr_value) . \Runtime\rtl::toStr(");")));
					}
					$flag_value = true;
				}
			}
			if (!$flag_value)
			{
				$f = \Runtime\rtl::method($ctx, static::getCurrentClassName($ctx), "OpHtmlItems");
				$res = $t::saveOpCodeCall($ctx, $t, $f, \Runtime\Collection::from([$op_code->items,$var_name]));
				$t = $res[0];
				$content .= \Runtime\rtl::toStr($res[1]);
			}
			$content .= \Runtime\rtl::toStr($t->s($ctx, $var_name . \Runtime\rtl::toStr(" .= '</") . \Runtime\rtl::toStr($op_code->tag_name) . \Runtime\rtl::toStr(">';")));
		}
		$res = $t::addSaveOpCode($ctx, $t, \Runtime\Dict::from(["op_code"=>$op_code,"var_name"=>$var_name,"content"=>$content]));
		$t = $res[0];
		return \Runtime\Collection::from([$t,$var_name]);
	}
	/**
	 * Translator html items
	 */
	static function OpHtmlItems($ctx, $t, $op_code, $var_name="")
	{
		if ($op_code == null || $op_code->items->count($ctx) == 0)
		{
			return \Runtime\Collection::from([$t,""]);
		}
		$content = "";
		if ($var_name == "")
		{
			$res = $t::incSaveOpCode($ctx, $t);
			$t = $res[0];
			$var_name = $res[1];
			$content .= \Runtime\rtl::toStr($t->s($ctx, $var_name . \Runtime\rtl::toStr(" = \"\";")));
		}
		for ($i = 0;$i < $op_code->items->count($ctx);$i++)
		{
			$item = $op_code->items->item($ctx, $i);
			$item_value = "";
			if ($item instanceof \Bayrell\Lang\OpCodes\OpHtmlContent)
			{
				$item_value = $t->expression::toString($ctx, $item->value);
			}
			else if ($item instanceof \Bayrell\Lang\OpCodes\OpHtmlTag)
			{
				$res = static::OpHtmlTag($ctx, $t, $item);
				$t = $res[0];
				$item_value = $res[1];
			}
			else if ($item instanceof \Bayrell\Lang\OpCodes\OpHtmlValue)
			{
				if ($item->kind == \Bayrell\Lang\OpCodes\OpHtmlValue::KIND_RAW)
				{
					$res = $t->expression::Expression($ctx, $t, $item->value);
					$t = $res[0];
					$item_value = $res[1];
				}
				else if ($item->kind == \Bayrell\Lang\OpCodes\OpHtmlValue::KIND_HTML)
				{
					$res = $t->expression::Expression($ctx, $t, $item->value);
					$t = $res[0];
					$item_value = $res[1];
					$item_value = "static::toHtml($ctx, " . \Runtime\rtl::toStr($item_value) . \Runtime\rtl::toStr(")");
				}
				else if ($item->kind == \Bayrell\Lang\OpCodes\OpHtmlValue::KIND_JSON)
				{
					$res = $t->expression::Expression($ctx, $t, $item->value);
					$t = $res[0];
					$item_value = $res[1];
					$item_value = "static::json_encode($ctx, " . \Runtime\rtl::toStr($item_value) . \Runtime\rtl::toStr(")");
				}
			}
			else
			{
				$res = $t->expression::Expression($ctx, $t, $item);
				$t = $res[0];
				$item_value = $res[1];
				$item_value = "static::escapeHtml($ctx, " . \Runtime\rtl::toStr($item_value) . \Runtime\rtl::toStr(")");
			}
			$content .= \Runtime\rtl::toStr($t->s($ctx, $var_name . \Runtime\rtl::toStr(" .= ") . \Runtime\rtl::toStr($item_value) . \Runtime\rtl::toStr(";")));
		}
		$res = $t::addSaveOpCode($ctx, $t, \Runtime\Dict::from(["op_code"=>$op_code,"var_name"=>$var_name,"content"=>$content]));
		$t = $res[0];
		return \Runtime\Collection::from([$t,"new \\Runtime\\RawString(" . \Runtime\rtl::toStr($var_name) . \Runtime\rtl::toStr(")")]);
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
	static function getClassInfo($ctx)
	{
		return new \Runtime\Annotations\IntrospectionInfo($ctx, [
			"kind"=>\Runtime\Annotations\IntrospectionInfo::ITEM_CLASS,
			"class_name"=>"Bayrell.Lang.LangPHP.TranslatorPHPHtml",
			"name"=>"Bayrell.Lang.LangPHP.TranslatorPHPHtml",
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