<?php

namespace IUOE\Ajax;

use IUOE\Mailer;
use IUOE\Thanks;
use Exception;

class ThankMPStep2Action extends Action
{
	protected $action = 'thank_mp_step2';

	public function handle()
	{
		try {
			$thanks = Thanks::find($this->request('thanks'));

			if (!$thanks) {
				throw new Exception('Unable to proceed.');
			}

			$representative = $thanks->getProvincialRep();

			$body = $thanks->getLetterBody();
			(new Mailer)->send($body, array(), function($message) use ($representative, $thanks) {
				$message->setReturnPath(iuoe_get_option('mailer_from_email'));
				$message->setFrom(iuoe_get_option('mailer_from_email'), $thanks->name);
				$message->setSender(iuoe_get_option('mailer_from_email'));
				$message->setReplyTo($thanks->email, $thanks->name);
				$message->setSubject($thanks->getLetterSubject());
				$message->setTo($representative->email, $representative->name);
			});

			$thanks->sent_to = $representative->email;
			$thanks->save();

			$this->response(array(
				'success' => true
			));
		}
		catch (Exception $e) {
			$this->error_response($e->getMessage());
		}
	}
}
