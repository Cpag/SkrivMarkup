<?php

namespace Skriv\Markup\PlainText;

class Link extends PlainTextTag {
	protected $name = 'a';
	public $beginTag = '[[';
	public $endTag = ']]';
	protected $attribute = array('$$', 'href');
	public $separators = array('|');
}

