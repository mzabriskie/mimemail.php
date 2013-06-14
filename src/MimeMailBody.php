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

require_once('MimeMailBoundary.php');
require_once('MimeMailEntity.php');

class MimeMailBody extends MimeMailEntity {

	protected
		$boundary,
		$parts = array();

	/**
	 * Gets the boundary for the body
     *
     * @return  string
	 */
    public function getBoundary () {
		if (is_null($this->boundary)) {
			$this->boundary = new MimeMailBoundary();
		}
		return $this->boundary;
	}

	/**
     * Add a part to the body
     *
     * @param   MimeMailEntity
     * @param   string
     * @return  void
     */
    public function addPart (MimeMailEntity $part, $key = null) {
		if (is_null($key)) {
			$this->parts[] = $part;
		}
		else {
			$this->parts[$key] = $part;
		}
	}

	/**
	 * Get a part of the body
     *
     * @param   string
	 * @return  MimeMailEntity
	 */
	public function getPart ($key) {
		return $this->parts[$key];
	}

	/**
     * Get all the body parts
     *
     * @return  array
     */
    public function getParts () {
		return $this->parts;
	}

	public function build () {
		$output = 'Content-Type: ' . $this->getContentType() . '; boundary="' . $this->getBoundary() . '"' . "\r\n\r\n";

		if ($this->getContentType() == 'multipart/mixed') {
			$output .= 'This is a multi-part message in MIME format.' . "\r\n";
		}

		foreach ($this->getParts() as $part) {
			$output .= '--' . $this->getBoundary() . "\r\n";
			$output .= $part->build();
		}

		$output .= '--' . $this->getBoundary() . '--' . "\r\n\r\n";

		return $this->formatCrLf($output);
	}

}
