<?php
namespace Skriv\Markup\PlainText;

class Abbr extends PlainTextTag {
	protected $name = 'abbr';
	public $beginTag = '??';
	public $endTag = '??';
	protected $attribute = array('$$', 'title');
	public $separators = array('|');
}

