<?php

namespace IUOE\Ajax;

use IUOE\Thanks;
use Exception;

class ThankMPStep1Action extends Action
{
	protected $action = 'thank_mp_step1';

	public function handle()
	{
		try {
			$thanks = Thanks::create([
				'name'           => $this->request('name'),
				'email'          => $this->request('email'),
				'street_address' => $this->request('street_address'),
				'city'           => $this->request('city'),
				'postal_code'    => strtoupper($this->request('postal_code')),
				'is_engineer'    => $this->request('engineer') === 'true',
				'opt_in'         => $this->request('opt_in')   === 'true',
			]);

			$representative = $thanks->getProvincialRep();

			$this->response(array(
				'thanks'          => $thanks->id,
				'letter'         => $thanks->getLetterBody(),
				'representative' => array(
					'name'           => $representative->name,
					'email'          => $representative->email,
					'district_name'  => $representative->district_name,
					'cabinet_member' => $representative->cabinet_member,
				),
			));
		}
		catch (Exception $e) {
			$this->error_response($e->getMessage());
		}
	}
}
