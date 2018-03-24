<?php

namespace IUOE\Ajax;

use IUOE\Voice;
use Exception;

class AddYourVoiceStep1Action extends Action
{
	public function handle()
	{
		try {
			$voice = Voice::create([
				'name'           => $this->request('name'),
				'email'          => $this->request('email'),
				'street_address' => $this->request('street_address'),
				'city'           => $this->request('city'),
				'postal_code'    => strtoupper($this->request('postal_code')),
				'is_engineer'    => $this->request('engineer') === 'true',
				'profession'     => $this->request('profession'),
				'opt_in'         => $this->request('opt_in')   === 'true',
			]);

			$representative = $voice->getProvincialRep();

			$this->response(array(
				'voice'          => $voice->id,
				'letter'         => $voice->getLetterBody(),
				'representative' => array(
					'name'           => $representative->name,
					'email'          => $representative->email,
					'district_name'  => $representative->district_name
//					'cabinet_member' => $representative->cabinet_member,
				),
			));
		}
		catch (Exception $e) {
			$this->error_response($e->getMessage());
		}
	}
}
