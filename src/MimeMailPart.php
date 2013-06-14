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

require_once('MimeMailEntity.php');

class MimeMailPart extends MimeMailEntity {

	protected
		$charset,
		$content,
		$contentEncoding;

	public function __construct ($content, $contentType, $contentEncoding = null, $charset = MimeMail::CHARSET_WESTERN) {
		$this->setCharset($charset);
		$this->setContent($content);
		$this->setContentType($contentType);
		$this->setContentEncoding($contentEncoding);
	}

	/**
     * Sets the charset of the part
     *
     * @param   string
     * @return  void
     */
    public function setCharset ($charset) {
		$this->charset = $charset;
	}

	/**
     * Gets the charset of the part
     *
     * @return  string
     */
    public function getCharset () {
		return $this->charset;
	}

	/**
	 * Set the content of the part
	 *
	 * @param	string
	 * @return	void
	 */
	public function setContent ($content) {
		$this->content = $content;
	}

	/**
	 * Get the content of the part
	 *
	 * @return	string
	 */
	public function getContent () {
		if (is_null($this->content)) {
			$this->content = '';
		}
		return $this->content;
	}

	/**
	 * Set the content encoding
	 *
	 * @param   string
	 * @return  void
	 */
	public function setContentEncoding ($contentEncoding) {
		$this->contentEncoding = $contentEncoding;
	}

	/**
	 * Get the content encoding
	 *
	 * @return  string
	 */
	public function getContentEncoding () {
		if (is_null($this->contentEncoding)) {
			$this->contentEncoding = MimeMail::ENCODING_7BIT;
		}
		return $this->contentEncoding;
	}

	public function build () {
		$output  = 'Content-Type: ' . $this->getContentType() . '; charset=' . $this->getCharset() . "\r\n";
		$output .= 'Content-Transfer-Encoding: ' . $this->getContentEncoding() . "\r\n\r\n";
		$output .= $this->getContent() . "\r\n\r\n";
		return $this->formatCrLf($output);
	}

}
