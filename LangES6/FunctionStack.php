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
namespace BayrellLang\LangES6;
use Runtime\rs;
use Runtime\rtl;
use Runtime\Map;
use Runtime\Vector;
use Runtime\Dict;
use Runtime\Collection;
use Runtime\IntrospectionInfo;
use Runtime\UIStruct;
use Runtime\rs;
use Runtime\CoreObject;
class FunctionStack extends CoreObject{
	public $name;
	public $is_async;
	public $async_ctx;
	public $async_jump;
	public $async_jump_pos;
	public $async_stop_pos;
	/**
	 * Returns jump string from arr
	 * @param Vector<int> arr
	 * @return string
	 */
	static function getJumpString($arr1){
		$arr2 = $arr1->map(function ($item){
			return rtl::toString($item);
		});
		return rs::implode(".", $arr2);
	}
	/**
	 * Returns jump position
	 * @return string
	 */
	function getJumpPos(){
		return (new \Runtime\Callback(self::class, "getJumpString"))($this->async_jump_pos);
	}
	/**
	 * Returns next jump position
	 * @return string
	 */
	function getJumpNext(){
		$arr = $this->async_jump_pos->copy();
		$sz = $arr->count();
		$item = $arr->item($sz - 1);
		$arr->set($sz - 1, $item + 1);
		return (new \Runtime\Callback(self::class, "getJumpString"))($arr);
	}
	/**
	 * Increments jump position
	 */
	function jumpAdd(){
		$sz = $this->async_jump_pos->count();
		if ($sz == 0){
			return ;
		}
		$item = $this->async_jump_pos->item($sz - 1);
		$this->async_jump_pos->set($sz - 1, $item + 1);
	}
	/**
	 * Increment jump position's level
	 */
	function jumpPush(){
		$this->async_jump_pos->push(0);
	}
	/**
	 * Decrement jump position's level
	 */
	function jumpPop(){
		$this->async_jump_pos->pop();
	}
	/**
	 * Push stop
	 */
	function stopPush($begin_pos, $end_pos){
		$this->async_stop_pos->push((new Map())->set("begin", $begin_pos)->set("end", $end_pos));
	}
	/**
	 * Pop stop
	 */
	function stopPop(){
		$this->async_stop_pos->pop();
	}
	/**
	 * Returns begin async position
	 * @return string
	 */
	function getAsyncBeginPos(){
		$sz = $this->async_stop_pos->count();
		if ($sz == 0){
			return "";
		}
		$obj = $this->async_stop_pos->item($sz - 1);
		return $obj->get("begin", "", "string");
	}
	/**
	 * Returns end async position
	 * @return string
	 */
	function getAsyncEndPos(){
		$sz = $this->async_stop_pos->count();
		if ($sz == 0){
			return "";
		}
		$obj = $this->async_stop_pos->item($sz - 1);
		return $obj->get("end", "", "string");
	}
	/* ======================= Class Init Functions ======================= */
	public function getClassName(){return "BayrellLang.LangES6.FunctionStack";}
	public static function getCurrentClassName(){return "BayrellLang.LangES6.FunctionStack";}
	public static function getParentClassName(){return "Runtime.CoreObject";}
	protected function _init(){
		parent::_init();
	}
}