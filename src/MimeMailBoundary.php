<?php

class MimeMailBoundary {

	protected $boundary;

	public function __construct () {
		$this->boundary = chr(rand(65, 90)) . chr(rand(65, 90)) . '-----' . md5(uniqid(rand()));
	}

	public function __toString () {
		return $this->boundary;
	}

}
