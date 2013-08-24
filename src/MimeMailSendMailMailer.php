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

require_once('MimeMailMailer.php');

class MimeMailSendMailMailer implements MimeMailMailer {

    /**
     * Send email message
     *
     * @param   MimeMailMessage $message Message to be sent
     * @return	void
     * @throws  MimeMailException
     */
    public function send(MimeMailMessage $message) {
        // send the mail
        $result = @mail($message->formatTo(),
            $message->getSubject(),
            '', // No message parameter is sent, since the message is going to be sent as a header
            $message->formatHeaders() . PHP_EOL . $message->formatMessage(),
            $message->formatParameters());

        // check for error
        if (!$result) {
            throw new MimeMailException('Unable to send message');
        }
    }

}