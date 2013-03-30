<?php

namespace Skriv\Markup\PlainText;

/** Gestion du texte souligné. */
class Underline extends PlainTextTag {
	protected $name = 'u';
	public $beginTag = '__';
	public $endTag = '__';
}

