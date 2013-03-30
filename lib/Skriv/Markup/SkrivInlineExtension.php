<?php

namespace Skriv\Markup;

interface SkrivInlineExtension {
	/**
	 *
	 * @return array ['name' => string, 'parameters-type' => 'listed'|'named'|'none']
	 */
	function getConf();
	/**
	 * @param $type string 'html' or 'plain-text'
	 */
	function to($type);
}
