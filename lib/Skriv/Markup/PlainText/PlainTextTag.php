<?php
namespace Skriv\Markup\PlainText;
use WikiRenderer\Tag;

abstract class PlainTextTag extends Tag {
	protected $additionnalAttributes = array();
	/** Sometimes, an attribute could not correspond to something in the target format so we could indicate it. */
	protected $ignoreAttribute = array();

	public function getContent() {
		if (!isset($this->contents[0]))
			return '';
		$content = trim(array_shift($this->contents));
		if (!empty($this->contents))
			$content .= ' (' . trim(implode(', ', $this->contents)) . ')';
		return $content;
	}
}

