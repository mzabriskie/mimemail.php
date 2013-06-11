<?php

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
