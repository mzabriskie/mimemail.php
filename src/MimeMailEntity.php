<?php

/*

Copyright (c) 2013 by Matt Zabriskie

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

*/

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
	 * @param   string $subject The string to format CR/LF on
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
	 * @param	string $address Email address
	 * @param	string $name Recipient's name (optional)
	 * @return	string Formatted email (e.g., "John Smith <john.smith@example.com>")
	 * @throws  MimeMailException if email address is invalid
	 */
	protected function formatEmail ($address, $name = null) {
		MimeMail::validateEmailAddress($address);
		return (is_null($name)) ? $address : sprintf('%s <%s>', $name, $address);
	}

	abstract public function build ();

}
