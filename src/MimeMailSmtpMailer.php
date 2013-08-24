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

class MimeMailSmtpMailer implements MimeMailMailer {

    private $resource;
    private $host,
            $port,
            $user,
            $pass;

    /**
     * Construct a MimeMailMailer instance configured for using SMTP to mail message
     *
     * @param   string $user The username for authenticating with the SMTP server
     * @param   string $pass The password for authenticating with the SMTP server
     * @param   string $host The host of the SMTP server
     * @param   int $port The port of the SMTP server
     */
    public function __construct($user, $pass, $host, $port = 25) {
        $this->user = base64_encode($user);
        $this->pass = base64_encode($pass);
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * Send email message
     *
     * @param   MimeMailMessage $message Message to be sent
     * @return	void
     * @throws  MimeMailException if mail was successfully accepted for delivery.
     */
    public function send(MimeMailMessage $message) {
        // send the mail
        if ($this->resource = fsockopen('ssl://' . $this->host, $this->port)) {
            // Authenticate with server
            $this->transmit('EHLO ' . $this->host);
            $this->transmit('AUTH LOGIN');
            $this->transmit($this->user);
            $this->transmit($this->pass);

            // Send message
            $this->transmit('MAIL FROM: ' . $message->getFrom());
            foreach ($message->getTo() as $to) {
                $this->transmit('RCPT TO: ' . $to);
            }
            $this->transmit('DATA', 354);
            $this->transmit($message->toString());

            // Quit and close connection
            $this->transmit('QUIT', 221);
            fclose($this->resource);
            $this->resource = null;
        }
    }

    /**
     * @param   string $message Message to be sent to the server
     * @param   int $expect Expected status code from response
     * @throws  MimeMailException
     */
    private function transmit($message, $expect = 250) {
        fputs($this->resource, $message . "\r\n");
        $result = fgets($this->resource, 1024);

        if (strpos($result, $expect) < 0) {
            fclose($this->resource);
            $this->resource = null;
            throw new MimeMailException('Unable to send message');
        }
    }

}