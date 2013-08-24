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
require_once('MimeMailAttachment.php');
require_once('MimeMailBody.php');
require_once('MimeMailPart.php');

class MimeMailMessage extends MimeMailBody {

	protected
		$to				= array(),
		$cc				= array(),
		$bcc 			= array(),
		$headers	    = array(),
		$params			= array(),
		$priority       = MimeMail::PRIORITY_NORMAL,
		$charset        = MimeMail::CHARSET_WESTERN,
		$from,
		$replyTo,
		$subject;

	public function __construct () {
		parent::__construct('multipart/mixed');
		$this->addPart(new MimeMailBody('multipart/alternative'), 'body');
	}

	/**
	 * Set the to address array
	 *
	 * @param	mixed $address Array of addresses, or string of single address
	 * @param	string $name To recipient's name (only if first parameter is string)
	 * @return	void
	 * @throws  MimeMailException if email address is invalid
	 */
	public function setTo ($address, $name = null) {
		$this->to = array();

		if (is_array($address)) {
			foreach ($address as $tmp) {
				$this->addTo($tmp);
			}
		}
		else {
			$this->addTo($address, $name);
		}
	}

	/**
	 * Add an address to the to array
	 *
	 * @param	string $address Email address
	 * @param	string $name To recipient's name (optional)
	 * @return	void
	 * @throws  MimeMailException if email address is invalid
	 */
	public function addTo ($address, $name = null) {
        if (empty($address)) return;
        MimeMail::validateUserInput($address);
        MimeMail::validateUserInput($name);
		$this->to[] = $this->formatEmail($address, $name);
	}

	/**
	 * Get the to address array
	 *
	 * @return	array
	 */
	public function getTo () {
		return $this->to;
	}

	/**
	 * Set the CC address array
	 *
	 * @param	mixed $address Array of addresses, or string of single address
	 * @param	string $name CC recipient's name (only if first parameter is string)
	 * @return	void
	 * @throws  MimeMailException if email address is invalid
	 */
	public function setCc ($address, $name = null) {
		$this->cc = array();

		if (is_array($address)) {
			foreach ($address as $tmp) {
				$this->addCc($tmp);
			}
		}
		else {
			$this->addCc($address, $name);
		}
	}

	/**
	 * Add an address to the CC array
	 *
	 * @param	string $address Email address
	 * @param	string $name CC recipient's name (optional)
	 * @return	void
	 * @throws  MimeMailException if email address is invalid
	 */
	public function addCc ($address, $name = null) {
        if (empty($address)) return;
        MimeMail::validateUserInput($address);
        MimeMail::validateUserInput($name);
		$this->cc[] = $this->formatEmail($address, $name);
	}

	/**
	 * Get the carbon copy array
	 *
	 * @return	array
	 */
	public function getCc () {
		return $this->cc;
	}

	/**
	 * Set the BCC address array
	 *
	 * @param	mixed $address Array of addresses, or string of single address
	 * @param	string $name BCC recipient's name (only if first parameter is string)
	 * @return	void
	 * @throws  MimeMailException if email address is invalid
	 */
	public function setBcc ($address, $name = null) {
		$this->bcc = array();

		if (is_array($address)) {
			foreach ($address as $tmp) {
				$this->addBcc($tmp);
			}
		}
		else {
			$this->addBcc($address, $name);
		}
	}

	/**
	 * Add an address to the BCC array
	 *
	 * @param	mixed $address Email address
	 * @param	string $name BCC recipient's name (optional)
	 * @return	void
	 * @throws  MimeMailException if email address is invalid
	 */
	public function addBcc ($address, $name = null) {
        if (empty($address)) return;
        MimeMail::validateUserInput($address);
        MimeMail::validateUserInput($name);
		$this->bcc[] = $this->formatEmail($address, $name);
	}

	/**
	 * Get the blind carbon copy array
	 *
	 * @return	array
	 */
	public function getBcc () {
		return $this->bcc;
	}

	/**
	 * Set the from address
	 *
	 * @param	string $address Email address
	 * @param	string $name From's name (optional)
	 * @return	void
	 * @throws  MimeMailException if email address is invalid
	 */
	public function setFrom ($address, $name = null) {
        MimeMail::validateUserInput($address);
        MimeMail::validateUserInput($name);
		$this->from = $this->formatEmail($address, $name);
	}

	/**
	 * Get the from address
	 *
	 * @return	string
	 */
	public function getFrom () {
		return $this->from;
	}

	/**
	 * Set the reply to address
	 *
	 * @param	string $address Email address
	 * @param   string $name Reply to's name (optional)
	 * @return	void
	 * @throws  MimeMailException if email address is invalid
	 */
	public function setReplyTo ($address, $name = null) {
        MimeMail::validateUserInput($address);
        MimeMail::validateUserInput($name);
		$this->replyTo = $this->formatEmail($address, $name);
	}

	/**
	 * Get the reply to address
	 *
	 * @return	string
	 */
	public function getReplyTo () {
		return $this->replyTo;
	}

	/**
	 * Set the subject
	 *
	 * @param	string $subject Subject
	 * @return	void
	 */
	public function setSubject ($subject) {
		MimeMail::validateUserInput($subject);
        $this->subject = $subject;
	}

	/**
	 * Get the subject
	 *
	 * @return	string
	 */
	public function getSubject () {
		return $this->subject;
	}

	/**
	 * Set the HTML message
	 *
	 * @param	string $html Value may be either a file path, ie "/tmp/email.html",
	 * 						or a string that will be converted into a file.
	 * @return	void
	 */
	public function setHTMLMessage ($html) {
        if (file_exists($html)) {
            $html = $this->formatCrLf(implode("\r\n", file($html)));
        }
		$this->getPart('body')->addPart(new MimeMailPart(preg_replace("/\\\\'/", '\'', $html), MimeMail::TYPE_TEXT_HTML), 'html');
	}

	/**
	 * Set the Text message
	 *
     * @param	string $text Value may be either a file path, ie "/tmp/email.txt",
	 * 						or a string that will be converted into a file.
	 * @return	void
	 */
	public function setTextMessage ($text) {
        if (file_exists($text)) {
            $text = $this->formatCrLf(implode("\r\n", file($text)));
        }
		$this->getPart('body')->addPart(new MimeMailPart( preg_replace("/\\\\'/", '\'', $text), MimeMail::TYPE_TEXT_PLAIN), 'text');
	}

	/**
	 * Set the priority
	 *
	 * @param	integer (value between 1 and 5, see PRIORITY constants on this class)
	 * @return	void
	 */
	public function setPriority ($priority) {
		if (intval($priority) && isset(MimeMail::$PRIORITIES[$priority - 1])) {
			$this->priority = $priority - 1;
		}
	}

	/**
	 * Get the priority
	 *
	 * @return	string
	 */
	public function getPriority () {
		return $this->priority;
	}

	/**
	 * Set the character encoding
	 *
	 * @param   string
	 * @return  void
	 */
	public function setCharset($charset) {
		MimeMail::validateUserInput($charset);
        $this->charset = $charset;
	}

	/**
	 * Get the character encoding
	 *
	 * @return  string
	 */
	public function getCharset() {
		return $this->charset;
	}

	/**
	 * Add a file for attachment to the attachment array
	 *
	 * @param	string $file Value may be either a file path, ie "/tmp/email_attachment",
	 * 									or a string that will be converted into a file.
	 * @param	string $fileName Name of the file to be attached
	 * @param	string $contentType Content type of the file
	 * @param	string $disposition Disposition of the attachment in the email
	 * @return	void
	 */
	public function addAttachment ($file, $fileName = null, $contentType = MimeMail::TYPE_APPLICATION_OCTET_STREAM, $disposition = MimeMail::DISPOSITION_ATTACHMENT) {
		$this->addPart(new MimeMailAttachment($file, $fileName, $contentType, $disposition));
	}

	/**
	 * Add additional parameters to the mail function
	 *
	 * @param	string
	 * @return	void
	 */
	public function addParameter ($parameter) {
		$this->params[] = $parameter;
	}

	/**
	 * Set all additional parameters
	 *
	 * @param	array
	 * @return	void
	 */
	public function setParameters ($parameters) {
		$this->params = $parameters;
	}

	/**
	 * Get all additional parameters
	 *
	 * @return	array
	 */
	public function getParameters () {
		if (is_null($this->params)) {
			$this->params = array();
		}
		return $this->params;
	}

	/**
	 * Add additional headers to the message
	 *
	 * @param	string
	 * @return	void
	 */
	public function addHeader ($header) {
		$this->headers[] = $header;
	}

	/**
	 * Set all additional headers
	 *
	 * @param	array
	 * @return	void
	 */
	public function setHeaders ($headers) {
		$this->headers = $headers;
	}

	/**
	 * Get all additional parameters
	 *
	 * @return	array
	 */
    public function getHeaders () {
        if (is_null($this->headers)) {
			$this->headers = array();
		}
        return $this->headers;
    }

    public function formatTo () {
        return implode(', ', $this->getTo());
    }

    public function formatParameters () {
        return implode(' ', $this->getParameters());
    }

	public function formatMessage () {
		return $this->build();
	}

	public function formatHeaders () {
		$headers = array();

		// add sender
		$headers[] = 'From: ' . $this->getFrom();

		if (strlen(trim($this->getReplyTo())) > 0) {
			$headers[] = 'Reply-To: ' . $this->getReplyTo();
		}

		// add carbon copy
		if (count($this->getCc()) > 0) {
			$headers[] = 'CC: ' . implode(', ', $this->getCc());
		}

		// add blind carbon copy
		if (count($this->getBcc()) > 0) {
			$headers[] = 'BCC: ' . implode(', ', $this->getBcc());
		}

		// add priority
		if (intval($this->getPriority()) && $this->getPriority() != MimeMail::PRIORITY_NORMAL) {
			$headers[] = 'X-Priority: ' . MimeMail::$PRIORITIES[$this->getPriority()];
		}

		// add mailer version
		$headers[] = 'X-Mailer: MimeMail v' . MimeMail::VERSION;

		// add additional headers
		foreach ($this->getHeaders() as $header) {
			$headers[] = $header;
		}

		$headers[] = 'MIME-Version: 1.0';

		return $this->formatCrLf(implode("\r\n", $headers));
	}

	public function __toString () {
		return $this->toString();
	}

	public function toString () {
        $output  = 'To: ' . $this->formatTo() . PHP_EOL;
        $output .= 'Subject: ' . $this->getSubject() . PHP_EOL;
        $output .= $this->formatHeaders() . PHP_EOL;
		$output .= $this->formatMessage();

		return $output;
	}

}
