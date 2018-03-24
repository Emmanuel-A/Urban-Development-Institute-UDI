<?php

namespace IUOE\Ajax;

use IUOE\Mailer;
use IUOE\Signup;
use Swift_Validate;

class ContactUsAction extends Action
{
	public function handle()
	{
		$data = array(
			'name'    => $this->request('name'),
			'email'   => $this->request('email'),
			'message' => $this->request('message'),
		);

		Signup::create($data);

		$body = trim("
			<p>
				<strong>Name:</strong> [name]<br/>
				<strong>Email:</strong> [email]
			</p>
			<p>
				<strong>Message:</strong><br/>
				[message]
			</p>
		");

		$recipients = get_field('contact_us_recipients','option');

		if (!empty($recipients)) {
			$recipients = explode("\n", $recipients);
			$recipients = array_map('trim', $recipients);

			foreach ($recipients as $index => $recipient) {
				if (Swift_Validate::email($recipient) === false) {
					unset($recipients[$index]);
				}
			}
		}

		(new Mailer)->send($body, $data, function($message) use ($recipients) {
			$message->setSubject('Contact Us Submission');
			if (count($recipients)) {
				$message->setTo($recipients);
			}
		});

		$this->response(array(
			'success' => true,
		));
	}
}
