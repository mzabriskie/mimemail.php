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

require_once('MimeMailConstants.php');
require_once('MimeMailException.php');
require_once('MimeMailMessage.php');

/**
 * Create MIME email capable of having multiple parts including attachments
 *
 * @author	Matt Zabriskie (mzabriskie@gmail.com)
 * @version	2.2.0
 */
class MimeMail extends MimeMailConstants {

    /**
     * Create a MimeMailMailer instance configured for using sendmail command to mail message
     *
     * @return  MimeMailMailer
     */
    public static function createSendMailInstance () {
        require_once('MimeMailSendMailMailer.php');
        return new MimeMailSendMailMailer();
    }

    /**
     * Create a MimeMailMailer instance configured for using SMTP to mail message
     *
     * @param   string $user The username for authenticating with the SMTP server
     * @param   string $pass The password for authenticating with the SMTP server
     * @param   string $host The host of the SMTP server
     * @param   int $port The port of the SMTP server
     * @return  MimeMailMailer
     */
    public static function createSmtpInstance ($user, $pass, $host, $port = 25) {
        require_once('MimeMailSmtpMailer.php');
        return new MimeMailSmtpMailer($user, $pass, $host, $port);
    }

    /**
     * Checks input to ensure no headers were injected
     *
     * @param   string $input User input
     * @return  bool True if no headers where injected in input, otherwise false
     */
    public static function isUserInputValid ($input) {
        if (!empty($input)) {
            if (preg_match(self::INJECTED, $input)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Validate input to ensure no headers were injected
     *
     * @param   string $input User input
     * @return  void
     * @throws  MimeMailException if the user input contains unsafe input
     */
    public static function validateUserInput ($input) {
        if (!self::isUserInputValid($input)) {
            throw new MimeMailException('Invalid input \'' . $input . '\'');
        }
    }

    /**
	 * Checks an email address to make sure it's valid
	 *
	 * @author  Douglas Lovell (http://www.linuxjournal.com/article/9585)
	 * @param   string $address Email address
	 * @return  bool True if email address is valid, otherwise false
	 */
    public static function isEmailAddressValid ($address) {
        $isValid = true;
		$atIndex = strrpos($address, '@');

		if (is_bool($atIndex) && !$atIndex) {
			$isValid = false;
		}
		else {
			$domain = substr($address, $atIndex + 1);
			$local = substr($address, 0, $atIndex);
			$localLen = strlen($local);
			$domainLen = strlen($domain);

			if ($localLen < 1 || $localLen > 64) {
				// local part length exceeded
				$isValid = false;
			}
			else if ($domainLen < 1 || $domainLen > 255) {
				// domain part length exceeded
				$isValid = false;
			}
			else if ($local[0] == '.' || $local[$localLen - 1] == '.') {
				// local part starts or ends with '.'
				$isValid = false;
			}
			else if (preg_match('/\\.\\./', $local)) {
				// local part has two consecutive dots
				$isValid = false;
			}
			else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
				// character not valid in domain part
				$isValid = false;
			}
			else if (preg_match('/\\.\\./', $domain)) {
				// domain part has two consecutive dots
				$isValid = false;
			}
			else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\", "", $local))) {
				// character not valid in local part unless local part is quoted
				if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\", "", $local))) {
					$isValid = false;
				}
			}
			if ($isValid && !(checkdnsrr($domain, "MX") || checkdnsrr($domain, "A"))) {
				// domain not found in DNS
				$isValid = false;
			}
		}

		return $isValid;
    }

	/**
	 * Validate an email address.
	 *
	 * @author  Douglas Lovell (http://www.linuxjournal.com/article/9585)
	 * @param   string $address Email address
	 * @return  void
	 * @throws  MimeMailException if the email address doesn't follow correct format or the domain doesn't exist
	 */
	public static function validateEmailAddress ($address) {
		if (!self::isEmailAddressValid($address)) {
            throw new MimeMailException('Invalid email address \'' . $address . '\'');
        }
	}

}
