<?php

namespace Skriv\Markup\PlainText;

/**
 * Gestion du texte en exposant.
 */
class Superscript extends PlainTextTag {
	protected $name = 'sup';
	public $beginTag = '^^';
	public $endTag = '^^';
}

