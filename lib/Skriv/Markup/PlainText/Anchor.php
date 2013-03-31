<?php

namespace Skriv\Markup\PlainText;

class Anchor extends PlainTextTag {
	protected $name = 'anchor';
	public $beginTag = '~~';
	public $endTag = '~~';
	protected $attribute = array('name');
	public $separators = array('|');
}

