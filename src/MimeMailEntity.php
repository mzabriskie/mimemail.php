<?php

require_once('MimeMail.php');

abstract class MimeMailEntity {

	protected
		$contentType;

	public function __construct ($contentType) {
		$this->setContentType($contentType);
	}

	/**
	 * Set the content type
	 *
	 * @param	string
	 * @return	void
	 * @throws  MimeMailException if content type is invalid
	 */
	public function setContentType ($contentType) {
        MimeMail::validateUserInput($contentType);
		$this->contentType = $contentType;
	}

	/**
	 * Get the content type
	 *
	 * @return	string
	 */
	public function getContentType () {
		if (is_null($this->contentType)) {
			$this->contentType = '';
		}
		return $this->contentType;
	}

	/**
	 * Formats CR/LF within a string
	 *
	 * @param   string The string to format CR/LF on
	 * @return  string A string with CR/LF properly formatted
	 */
	protected function formatCrLf ($subject) {
		if ("\r\n" != PHP_EOL) { // Non-Windows
			$search = "\r\n";
			$replace = PHP_EOL;
		}
		else { // Windows
			$search = "\r\n.";
			$replace = "\r\n..";
		}
		return str_replace($search, $replace, $subject);
	}

	/**
	 * Formats an email address.
	 *
	 * @param	string Email address
	 * @param	string Recepient's name (optional)
	 * @return	string Formatted email (e.g., "John Smith <john.smith@example.com>")
	 * @throws  MimeMailException if email address is invalid
	 */
	protected function formatEmail ($address, $name = null) {
		MimeMail::validateEmailAddress($address);
		return (is_null($name)) ? $address : sprintf('%s <%s>', $name, $address);
	}

	abstract public function build ();

}
