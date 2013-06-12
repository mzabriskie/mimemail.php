mimemail
========

Mime Mail for PHP

    <?php

    require_once('MimeMail.php');

    $message = new MimeMailMessage();
    $message->setSubject('Pictures from my vacation');

    /*
    The second parameter of methods taking an email address is the person's real name, and is optional
    	These methods are:
    		- setTo, addTo (Recipients)
    		- setCc, addCc (Carbon Copies)
    		- setBcc, addBcc (Blind Carbon Copies)
    		- setFrom, setReplyTo

    Methods setTo, setCc and setBcc accept either a single address, or an array of addresses.
    Calling setTo, setCc and setBcc will directly assign the value.
    Calling addTo, addCc and addBcc will add an address to the respective value.

    Using setReplyTo is optional, and only useful when replies to the message should be sent
    somewhere other than to the from address.
     */
    $message->setTo(array('fred@example.com', 'barney@example.com', 'wilma@example.com', 'betty@example.com'));
    $message->addTo('john.smith@example.com', 'John Smith');
    $message->addTo('jane.smith@example.com', 'Jane Smith');

    $message->setFrom('joe.somebody@example.com', 'Joe Somebody');

    /*
    It is possible to send either a plain text message, a rich html message, or both.
    You may either set the value of the message directly, or provide the path to a file.
     */
    $message->setTextMessage('We had a great vacation! Check out our photos!');
    $message->setHTMLMessage('We had a <b>great</b> vacation! Check out our photos!');
    // optionally:
    // $message->setTextMessage('email.txt');
    // $message->setHTMLMessage('email.html');

    /*
    You can either add a file directly by specifying the file path, or you can set the
    content of the file for dynamically created files.
     */
    $message->addAttachment('cool.jpg', null, MimeMail::TYPE_IMAGE_JPEG, MimeMail::DISPOSITION_INLINE);
    $message->addAttachment('Blah, blah, blah', 'MyTextFile.txt', MimeMail::TYPE_TEXT_PLAIN);

    MimeMail::send($message);
