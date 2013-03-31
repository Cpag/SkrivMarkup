<?php

namespace Skriv\Markup\PlainText;

/**
 * Gestion du texte en indice.
 */
class Subscript extends PlainTextTag {
	protected $name = 'sub';
	public $beginTag = ',,';
	public $endTag = ',,';
}

