<?php
namespace Skriv\Markup;

interface SkrivInlineExtension {
	/**
	 *
	 * @return array ['name' => string, 'parameters-type' => 'listed'|'named'|'none']
	 */
	function getConf();
	/**
	 * @param $type string 'html', 'plain-text', 'docbook'
	 */
	function to($type);
}
