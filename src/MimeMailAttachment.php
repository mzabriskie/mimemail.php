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

require_once('MimeMailPart.php');

class MimeMailAttachment extends MimeMailPart {

	protected
		$fileName,
		$disposition;

	/**
	 * @param	string 		Value may be either a file path, (e.g., "/tmp/email_attachment"),
	 * 									or a string that will be converted into a file.
	 * @param	string 		Name of the file to be attached
	 * @param	string		Content type of the file
	 * @param	string		Disposition of the attachment in the email
	 * @return	void
	 */
	public function __construct ($content, $fileName = null, $contentType = null, $disposition = null) {
		parent::__construct($content, $contentType);
		$this->setFileName($fileName);
		$this->setDisposition($disposition);

		if (is_null($this->getFileName()) && file_exists($this->getContent())) {
			$this->setFileName(basename($this->getContent()));
		}
	}

	/**
	 * Get the content type
	 *
	 * @return	string
	 */
	public function getContentType () {
		if (is_null($this->contentType)) {
			$this->contentType = MimeMail::TYPE_APPLICATION_OCTET_STREAM;
		}
		return $this->contentType;
	}

	/**
	 * Get the content encoding
	 *
	 * @return  string
	 */
	public function getContentEncoding () {
		return MimeMail::ENCODING_BASE64;
	}

	/**
	 * Get the file name
	 *
	 * @return	string
	 */
	public function getFileName () {
		if (strlen(trim($this->fileName)) == 0) {
			$this->fileName = null;
		}
		return $this->fileName;
	}

	/**
	 * Set the file name
	 *
	 * @param	string
	 * @return	void
	 */
	public function setFileName ($fileName) {
		$this->fileName = $fileName;
	}

	/**
	 * Get the disposition
	 *
	 * @return	string
	 */
	public function getDisposition () {
		if (is_null($this->disposition)) {
			$this->disposition = MimeMail::DISPOSITION_ATTACHMENT;
		}
		return $this->disposition;
	}

	/**
	 * Set the disposition
	 *
	 * @param	string
	 * @return	void
	 */
	public function setDisposition ($disposition) {
		$this->disposition = $disposition;
	}

	public function build () {
		$fileName = (is_null($this->getFileName())) ? 'File Attachment' : $this->getFileName();
		$baseName = basename($fileName);

		$output  = 'Content-Type: ' . $this->getContentType() . '; name="' . $baseName . '"' . "\r\n";
		$output .= 'Content-Disposition: ' . $this->getDisposition() . '; filename="' . $baseName . '"' . "\r\n";
		$output .= 'Content-Transfer-Encoding: ' . $this->getContentEncoding() . "\r\n\r\n";

		// if attachment is a file, encode the file
		$content = $this->getContent();
		if (file_exists($content)) {
			$content = file_get_contents($content);
		}

		$output .= chunk_split(base64_encode($content)) . "\r\n";

		return $this->formatCrLf($output);
	}

}
