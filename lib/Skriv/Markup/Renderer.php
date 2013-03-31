<?php

namespace Skriv\Markup;

/**
 * Main renderer object for processing of SkrivMarkup-enabled text.
 *
 * @author	Amaury Bouchard <amaury@amaury.net>
 * @copyright	© 2013, Amaury Bouchard
 * @package	SkrivMarkup
 * @see		WikiRenderer
 */
use WikiRenderer\Config;

class Renderer {
	/** @var RenderingContext */
	private $_context;
	/** @var Config */
	private $_config;
	/** The WikiRenderer object. */
	private $_wikiRenderer;

	/**
	 * Factory method. Creates a renderer object of the given type.
	 * @param	string	$type	(optional). Available values: 'html' (by default) and 'docbook' (warning, docbook is still in alpha version).
	 * @param	array	$params	(optional) Hash of parameters. The accepted parameters depends of the chosen rendering type.
	 * @return	\Skriv\Markup\Renderer	A rendering object.
	 * @throws	\Exception	If something goes wrong.
	 */
	static public function factory($type = 'html', array $params = null) {
		if (!isset($type))
			$type = 'html';
		switch ($type) {
			case 'html':
				$context = new Html\HtmlRenderingContext($params);
				return new Renderer($context, new Html\Config($context));
			case 'plain-text':
				$context = new PlainText\PlainTextRenderingContext($params);
				return new Renderer($context, new PlainText\Config($context));
			case 'docbook':
				$context = new DocBook\DocBookRenderingContext($params);
				return new Renderer($context, new DocBook\Config($context));
			default:
				throw new \Exception("Unknown Skriv rendering type '$type'.");
		}
	}
	/**
	 * Parses a Skriv text and generates a converted text.
	 * @param	string	$text	The text to parse.
	 * @return	string	The generated string.
	 */
	public function __construct(RenderingContext $context, Config $config) {
		$this->_context = $context;
		$this->_config = $config;
		$this->_wikiRenderer = new \WikiRenderer\Renderer($config);
	}

	public function registerInlineExtension(SkrivInlineExtension $ext) {
		$this->_context->registerInlineExtension($ext);
	}

	/**
	 * Parses a Skriv text and generates a converted text.
	 * @param	string	$text	The text to parse.
	 * @return	string	The generated string.
	 */
	public function render($text) {
		return $this->_wikiRenderer->render($text);
	}
	/**
	 * Returns the TOC content. By default, the rendered string is returned, but the
	 * raw TOC tree is available.
	 * @param	bool	$raw	(optional) Set to True to get the raw TOC tree. False by default.
	 * @return	string|array	The TOC rendered string or the TOC tree.
	 */
	public function getToc($raw = false) {
		return ($this->_context->getToc($raw));
	}
	/**
	 * Returns the footnotes content. By default, the rendered string is returned, but the
	 * raw list of footnotes is available.
	 * @param	bool	$raw	(optional) Set to True to get the raw list of footnotes.
	 *				False by default.
	 * @return	string|array	The footnotes' rendered string or the list of footnotes.
	 */
	public function getFootnotes($raw = false) {
		return ($this->_context->getFootnotes($raw));
	}
	/**
	 * Returns the lines which contain an error.
	 * @return	array	List of lines.
	 * @deprecated Please use getErrorsAsMessages()
	 */
	public function getErrors() {
		return ($this->_wikiRenderer->errors);
	}
	/**
	 * Returns the lines which contain an error.
	 * @return  array  List of lines.
	 */
	public function getErrorsAsMessages() {
		$messages = array();
		$err = $this->_wikiRenderer->errors;
		if ($err) {
			if (count($err) > 1)
				$messages[] = 'Errors at lines: ' . implode(', ', array_keys($err));
			else {
				$messages[] = 'Error at line: ' . implode(', ', array_keys($err));
			}
		}
		$messages = array_merge($messages, $this->_context->Errors);
		return empty($messages) ? null : $messages;
	}
}
