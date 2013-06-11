<?php

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
