<?php

namespace Skriv\Markup\DocBook;

/**
 * Process of titles.
 *
 * @author	Amaury Bouchard <amaury@amaury.net>
 * @copyright	© 2012-2013
 * @package	SkrivMarkup
 */
class Title extends \WikiRenderer\Block {
	public $type = 'title';
	protected $regexp = "/^(={1,6})(.*)\s*$/";
	protected $_closeNow = true;

	public function getRenderedLine() {
		$equals = $this->_detectMatch[1];
		$text = trim($this->_detectMatch[2]);
		$level = strlen($equals);
		$identifier = '';

		if (($offset = strrpos($text, $equals)) !== false && $offset > 0) {
			if ($text[$offset - 1] == '\\')
				$text = substr($text, 0, $offset - 1) . substr($text, $offset);
			else {
				$identifier = trim(substr($text, $offset + $level));
				$text = trim(substr($text, 0, $offset));
			}
		}
		$html = $this->_renderInlineTag($text);
		$identifier = empty($identifier) ? $text : $identifier;
		/** @var $config Config */
		$config = $this->engine->getConfig();
		$identifier = $config->renderContext->titleToIdentifier($level, $identifier);

		$config->renderContext->addTocEntry($level, $html, $identifier);
		$level += $config->getParam('firstTitleLevel') - 1;

		return ("<h$level id=\"" . $config->getParam('anchorsPrefix') . "$identifier\">$html</h$level>");
	}
}

