<?php

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
	 * Send email message
	 *
     * @param   MimeMailMessage Message to be sent
	 * @return	void
     * @throws  MimeMailException if mail was successfully accepted for delivery.
	 */
	public static function send (MimeMailMessage $message) {
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

    /**
     * Checks input to ensure no headers were injected
     *
     * @param   string User input
     * @return  bool True if no headers where injected in input, otherwise false
     */
    public static function isUserInputValid ($input) {
        if (!empty($input)) {
            if (preg_match(self::$INJECTED, $input)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Validate input to ensure no headers were injected
     *
     * @param   string User input
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
	 * @param   string Email address
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
	 * @param   string Email address
	 * @return  void
	 * @throws  MimeMailException if the email address doesn't follow correct format or the domain doesn't exist
	 */
	public static function validateEmailAddress ($address) {
		if (!self::isEmailAddressValid($address)) {
            throw new MimeMailException('Invalid email address \'' . $address . '\'');
        }
	}

}
