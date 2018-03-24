<?php

namespace IUOE;

use IUOE\Mail\LogTransport;
use Exception;
use Swift_Mailer;
use Swift_MailTransport;
use Swift_Message;
use Swift_SmtpTransport;

class Mailer
{
	/**
	 * Send a message.
	 *
	 * @param mixed
	 * @param array
	 * @param Closure|null
	 * @return integer
	 */
	public function send($html, array $data = array(), $callable = null)
	{
		if ($callable && !is_callable($callable)) {
			throw new InvalidArgumentException;
		}

		$message = Swift_Message::newInstance();

		// Render merge tags and set message body
		$html = self::renderMergeTags($html, $data);
		$message->setBody($html, 'text/html');

		// Allow message to be customized
		if ($callable) {
			$callable($message);
		}

		// If in Test Mode the set the recipient to the Test Mode recipients
		if (iuoe_get_option('mailer_test_mode') === 'on') {
			$recipients = array_map('trim', explode(',', iuoe_get_option('mailer_test_rcpt')));
			$message->setTo($recipients);
		}

		// If no `To` has been added then send to
		// the Wordpress admin as a default
		if (count($message->getTo()) === 0) {
			$message->setTo(get_option('admin_email'));
		}

		// If no `From` has been set then set to the default
		if (count($message->getFrom()) === 0) {
			$message->setFrom(__(iuoe_get_option('mailer_from_email')));
		}

		$mailer = Swift_Mailer::newInstance($this->getSwiftTransport());
		return $mailer->send($message);
	}

	/**
	 * Render a merge tags.
	 *
	 * @param string
	 * @param array
	 * @return string
	 */
	public static function renderMergeTags($content, array $mergeTags = array())
	{
		$keys = array_keys($mergeTags);
		foreach ($keys as &$key) {
			$key = '[' . trim($key,'[]') . ']';
		}

		return str_replace($keys, array_values($mergeTags), $content);
	}

	/**
	 * Retrieve SwiftMailer Transport based on settings.
	 *
	 * @return \Swift_Transport
	 */
	protected function getSwiftTransport()
	{
		$transport = iuoe_get_option('mailer_transport');

		if ($transport === 'LOG') {
			return LogTransport::newInstance(iuoe_get_option('log_file'));
		}

		if ($transport === 'SMTP') {
			$transport = Swift_SmtpTransport::newInstance(
				iuoe_get_option('smtp_hostname'),
				iuoe_get_option('smtp_port')
			);

			$username = iuoe_get_option('smtp_username');
			if (!empty($username)) {
				$transport->setUsername($username);
			}

			$password = iuoe_get_option('smtp_password');
			if (!empty($password)) {
				$transport->setPassword($password);
			}

			$encryption = iuoe_get_option('smtp_encryption');
			if (!empty($encryption)) {
				$transport->setEncryption($encryption);
			}

			return $transport;
		}

		return Swift_MailTransport::newInstance();
	}
}
