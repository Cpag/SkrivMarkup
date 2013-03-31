<?php

namespace Skriv\Markup\PlainText;

class Image extends PlainTextTag {
	protected $name = 'image';
	public $beginTag = '{{';
	public $endTag = '}}';
	protected $attribute = array('alt', 'src');
	public $separators = array('|');
}

