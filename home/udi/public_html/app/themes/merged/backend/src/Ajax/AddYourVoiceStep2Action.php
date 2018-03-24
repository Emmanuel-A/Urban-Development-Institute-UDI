<?php

namespace IUOE\Ajax;

use IUOE\Mailer;
use IUOE\Voice;
use Exception;

class AddYourVoiceStep2Action extends Action
{
	public function handle()
	{
		try {	
			$voice = Voice::find($this->request('voice'));
			
			if (!$voice) {
				throw new Exception('Unable to proceed.');
			}

			$representative = $voice->getProvincialRep();
			

			if ($this->request('cc_confirmation') === 'true') {
                        	$recipients['to'] = array(
                        		$representative->email => $representative->name,
                                	'ministre@msss.gouv.qc.ca'      => 'Dr. Gaétan Barrette'
                        	);
				$voice->include_minister = 1;
                	} else {
                        	$recipients['to'] = array(
                        		$representative->email => $representative->name
				);
				$voice->include_minister = 0;
			}	
			$voice->sent_to = implode(',', array_keys($recipients['to']));
                	$voice->email_subject = $voice->letter_subject;
                	$voice->email_body = $voice->letter_body;
                	$voice->save();	
	
			$body = $voice->getLetterBody();
			(new Mailer)->send($body, array(), function($message) use ($representative, $voice, $recipients) {
				$message->setReturnPath(iuoe_get_option('mailer_from_email'));
				$message->setFrom(iuoe_get_option('mailer_from_email'), $voice->name);
				$message->setSender(iuoe_get_option('mailer_from_email'));
				$message->setReplyTo($voice->email, $voice->name);
				$message->setSubject($voice->getLetterSubject());
				$message->setTo($recipients['to']);
			});

			$voice->sent_to =  implode(',', array_keys($recipients['to']));;
			$voice->save();

			$this->response(array(
				'success' => true
			));
		}
		catch (Exception $e) {
			$this->error_response($e->getMessage());
		}
	}
}
