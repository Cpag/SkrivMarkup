<?php

namespace Skriv\Markup\PlainText;

/**
 * Gestion du texte en gras.
 */
class Strong extends PlainTextTag {
	protected $name = 'strong';
	public $beginTag = '**';
	public $endTag = '**';
}

