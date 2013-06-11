<?php

require_once(dirname(__FILE__) . '/../src/MimeMail.php');

class MimeMailTest extends TestCase {
	const
		TO_ADDRESS      = 'mzabriskie@gmail.com',
		TO_NAME         = 'Matt Zabriskie',
		FROM_ADDRESS    = 'mattz@example.com',
		FROM_NAME       = __CLASS__,
        TEST_ADDRESS    = 'someone@example.com';

	private $message;

	function setUp() {
		parent::setUp();

		$this->message = new MimeMailMessage();
		$this->message->setTo(self::TO_ADDRESS, self::TO_NAME);
		$this->message->setFrom(self::FROM_ADDRESS, self::FROM_NAME);
		$this->message->setSubject(__CLASS__);
        $this->message->setTextMessage(dirname(__FILE__) . '/email.txt');
        $this->message->setHTMLMessage(dirname(__FILE__) . '/email.html');
        $this->message->addAttachment('Blah, blah, blah', 'MyTextFile.txt', MimeMail::TYPE_TEXT_PLAIN);
        $this->message->addAttachment(dirname(__FILE__) . '/kungfu.jpg', null, MimeMail::TYPE_IMAGE_JPEG, MimeMail::DISPOSITION_INLINE);
	}

	function tearDown() {
        if (file_exists($this->message->getSubject() . '.eml')) {
            unlink($this->message->getSubject() . '.eml');
        }

        $this->message = null;
		
		parent::tearDown();
	}

//	function testSimpleMail() {
//
//		MimeMail::send($this->message);
//
//	}

	function testSaveMessage() {
		file_put_contents($this->message->getSubject() . '.eml', $this->message);
	}

	function testMessageContent() {
        //echo $this->message;
	}

	function testValidEmail() {
		// These should pass
		$this->assertTrue(MimeMail::isEmailAddressValid("dclo@us.ibm.com"));
		$this->assertTrue(MimeMail::isEmailAddressValid("abc\\@def@example.com"));
		$this->assertTrue(MimeMail::isEmailAddressValid("abc\\\\@example.com"));
		$this->assertTrue(MimeMail::isEmailAddressValid("Fred\\ Bloggs@example.com"));
		$this->assertTrue(MimeMail::isEmailAddressValid("Joe.\\\\Blow@example.com"));
		$this->assertTrue(MimeMail::isEmailAddressValid("\"Abc@def\"@example.com"));
		$this->assertTrue(MimeMail::isEmailAddressValid("\"Fred Bloggs\"@example.com"));
		$this->assertTrue(MimeMail::isEmailAddressValid("customer/department=shipping@example.com"));
		$this->assertTrue(MimeMail::isEmailAddressValid("\$A12345@example.com"));
		$this->assertTrue(MimeMail::isEmailAddressValid("!def!xyz%abc@example.com"));
		$this->assertTrue(MimeMail::isEmailAddressValid("_somename@example.com"));
		$this->assertTrue(MimeMail::isEmailAddressValid("user+mailbox@example.com"));
		$this->assertTrue(MimeMail::isEmailAddressValid("peter.piper@example.com"));
		$this->assertTrue(MimeMail::isEmailAddressValid("Doug\\ \\\"Ace\\\"\\ Lovell@example.com"));
		$this->assertTrue(MimeMail::isEmailAddressValid("\"Doug \\\"Ace\\\" L.\"@example.com"));

		// These should fail
		$this->assertFalse(MimeMail::isEmailAddressValid("abc@def@example.com"));
		$this->assertFalse(MimeMail::isEmailAddressValid("abc\\\\@def@example.com"));
		$this->assertFalse(MimeMail::isEmailAddressValid("abc\\@example.com"));
		$this->assertFalse(MimeMail::isEmailAddressValid("@example.com"));
		$this->assertFalse(MimeMail::isEmailAddressValid("doug@"));
		$this->assertFalse(MimeMail::isEmailAddressValid("\"qu@example.com"));
		$this->assertFalse(MimeMail::isEmailAddressValid("ote\"@example.com"));
		$this->assertFalse(MimeMail::isEmailAddressValid(".dot@example.com"));
		$this->assertFalse(MimeMail::isEmailAddressValid("dot.@example.com"));
		$this->assertFalse(MimeMail::isEmailAddressValid("two..dot@example.com"));
		$this->assertFalse(MimeMail::isEmailAddressValid("\"Doug \"Ace\" L.\"@example.com"));
		$this->assertFalse(MimeMail::isEmailAddressValid("Doug\\ \\\"Ace\\\"\\ L\\.@example.com"));
		$this->assertFalse(MimeMail::isEmailAddressValid("hello world@example.com"));
		$this->assertFalse(MimeMail::isEmailAddressValid("gatsby@f.sc.ot.t.f.i.tzg.era.l.d."));
	}

	function testHeaderInjection() {

        $m = new MimeMailMessage();

        $error = false;
        try {
            $m->setBcc(self::TEST_ADDRESS . "\r\nTo:" . self::TEST_ADDRESS);
        }
        catch (MimeMailException $e) {
            $error = true;
        }
        $this->assertTrue($error);

        $error = false;
        try {
            $m->setCc(self::TEST_ADDRESS . "\r\nBcc:" . self::TEST_ADDRESS);
        }
        catch (MimeMailException $e) {
            $error = true;
        }
        $this->assertTrue($error);

        $error = false;
        try {
            $m->setCharset(MimeMail::CHARSET_WESTERN . "\r\nTo:" . self::TEST_ADDRESS);
        }
        catch (MimeMailException $e) {
            $error = true;
        }
        $this->assertTrue($error);

        $error = false;
        try {
            $m->setContentType('text/html' . "\r\nBcc:" . self::TEST_ADDRESS);
        }
        catch (MimeMailException $e) {
            $error = true;
        }
        $this->assertTrue($error);
        
        $error = false;
        try {
            $m->setFrom(self::TEST_ADDRESS . "\r\nBcc:" . self::TEST_ADDRESS);
        }
        catch (MimeMailException $e) {
            $error = true;
        }
        $this->assertTrue($error);

        $error = false;
        try {
            $m->setReplyTo(self::TEST_ADDRESS . "\r\nBcc:" . self::TEST_ADDRESS);
        }
        catch (MimeMailException $e) {
            $error = true;
        }
        $this->assertTrue($error);

        $error = false;
        try {
            $m->setSubject("Testing Subject Line\r\nBcc:" . self::TEST_ADDRESS);
        }
        catch (MimeMailException $e) {
            $error = true;
        }
        $this->assertTrue($error);

        $error = false;
        try {
            $m->setTo(self::TEST_ADDRESS . "\r\nBcc:" . self::TEST_ADDRESS);
        }
        catch (MimeMailException $e) {
            $error = true;
        }
        $this->assertTrue($error);

	}

}
