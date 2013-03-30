<?php

namespace Skriv\Markup\PlainText;

/** Gestion de texte monospace. */
class Monospace extends PlainTextTag {
	protected $name = 'tt';
	public $beginTag = '##';
	public $endTag = '##';
}

