<?php


namespace IUOE;

use Exception;

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

class Represent {

	/**
	 * Endpoint for the public Open North Represent API.
	 *
	 * @var string
	 */
	protected $endpoint = 'http://represent.opennorth.ca';

	/**
	 * Static list of mayoral data for postal codes.
	 *
	 * @var array
	 */
	protected $mayoral_overrides = [];

	/**
	 * Static list of mayoral data for postal codes.
	 *
	 * @var array
	 */
	protected $cabinet_members = [
		'amarjeet.sohi@parl.gc.ca',
		'bardish.chagger@parl.gc.ca',
		'bill.morneau@parl.gc.ca',
		'carla.qualtrough@parl.gc.ca',
		'carolyn.bennett@parl.gc.ca',
		'catherine.mckenna@parl.gc.ca',
		'chrystia.freeland@parl.gc.ca',
		'diane.lebouthillier@parl.gc.ca',
		'dominic.leblanc@parl.gc.ca',
		'harjit.sajjan@parl.gc.ca',
		'jane.philpott@parl.gc.ca',
		'jean-yves.duclos@parl.gc.ca',
		'jim.carr@parl.gc.ca',
		'jody.wilson-raybould@parl.gc.ca',
		'john.mccallum@parl.gc.ca',
		'judy.foote@parl.gc.ca',
		'justin.trudeau@parl.gc.ca',
		'kent.hehr@parl.gc.ca',
		'kirsty.duncan@parl.gc.ca',
		'lawrence.macaulay@parl.gc.ca',
		'marc.garneau@parl.gc.ca',
		'marie-claude.bibeau@parl.gc.ca',
		'maryam.monsef@parl.gc.ca',
		'maryann.mihychuk@parl.gc.ca',
		'melanie.joly@parl.gc.ca',
		'navdeep.bains@parl.gc.ca',
		'patty.hajdu@parl.gc.ca',
		'ralph.goodale@parl.gc.ca',
		'scott.brison@parl.gc.ca',
		'stephane.dion@parl.gc.ca',
	];

	/**
	 * Retrieve all the politicians for a given postal code.
	 *
	 * @param string
	 * @return object
	 */
	public function getPoliticians($postalCode, $street_address, $city)
	{
		$postalCode = $this->formatPostalCode($postalCode);


		$geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?address='.urlencode($street_address.','.$city).'&sensor=false&key=AIzaSyAJKNUbaFkEC2sZcGFAEhjiTl7gN8HPJZA');
		$output= json_decode($geocode);
		if(isset($output->results[0]->geometry->location->lat)) {
			$lat = $output->results[0]->geometry->location->lat;
			$long =  $output->results[0]->geometry->location->lng;
			$postcodes = $this->doRequest("/representatives/?point=".$lat.','.$long);
			$representatives = [];
			$representatives = $postcodes->objects;
		}
		else {
			$postcodes = $this->doRequest("postcodes/{$postalCode}/");
			$representatives = [];

			// Build array of representative from both the centroid and concordance
			if (isset($postcodes->representatives_centroid) && is_array($postcodes->representatives_centroid)) {
				$representatives = array_merge($representatives, $postcodes->representatives_centroid);
			}
			if (isset($postcodes->representatives_concordance) && is_array($postcodes->representatives_concordance)) {
				$representatives = array_merge($representatives, $postcodes->representatives_concordance);
			}
		}


		$data = (object)[
			'city'                  => $city,
			'federal_leaders'       => [
				(object)[
					'first_name' => 'Rona',
					'last_name'  => 'Ambrose',
					'name'       => 'Rona Ambrose',
					'email'      => 'rona.ambrose@parl.gc.ca',
					'party_name' => 'Conservative',
				],
				(object)[
					'first_name' => 'Justin',
					'last_name'  => 'Trudeau',
					'name'       => 'Justin Trudeau',
					'email'      => 'justin.trudeau@parl.gc.ca',
					'party_name' => 'Liberal',
				],
				(object)[
					'first_name' => 'Thomas',
					'last_name'  => 'Mulcair',
					'name'       => 'Thomas Mulcair',
					'email'      => 'thomas.mulcair@parl.gc.ca',
					'party_name' => 'NDP',
				],
				(object)[
					'first_name' => 'Elizabeth',
					'last_name'  => 'May',
					'name'       => 'Elizabeth May',
					'email'      => 'Elizabeth.May@parl.gc.ca',
					'party_name' => 'Green Party',
				],
			],
      'federal_rep'           => null, //
      'provincial_candidates' => [],   //
      'provincial_leaders'    => [],
      'provincial_rep'        => null, //
      'mayoral_rep'           => null, //
      'councillor_rep'        => null, //
      'regional_reps'         => [],   //
		];


		// Extract the Federal, Provincial and Mayoral representatives
		foreach ($representatives as $rep) {
			$rep->id = sha1(serialize($rep));
			if (in_array($rep->elected_office, ['MP'])) {
	/*			$data->federal_rep = $rep;
				$data->federal_rep->cabinet_member = false;
				if (array_search(strtolower($data->federal_rep->email), $this->cabinet_members)) {
					$data->federal_rep->cabinet_member = true;
				} */
			} 
			elseif (in_array($rep->elected_office, ['MLA','MPP','MNA','MHA'])) {
				$data->provincial_rep = $rep;
			}
/*			elseif ($this->contains($rep->elected_office, ['Mayor','Maire'])) {
				$data->mayoral_rep = $rep;
			}
			elseif ($this->contains($rep->elected_office, ['Councillor','Conseillère'])) {
				$data->councillor_rep = $rep;
			} */
		}

		try {
			//$lat = $postcodes->centroid->coordinates[1];
			//$lon = $postcodes->centroid->coordinates[0];
			$candidates = $this->doRequest("candidates/?point={$lat},{$long}");
			if (isset($candidates->objects) && is_array($candidates->objects)) {
				foreach ($candidates->objects as $rep) {
					if ($rep->election_name === 'Legislative Assembly of Ontario 2018') {
						$rep->id = sha1(serialize($rep));
						$data->provincial_candidates[] = $rep;
					}
				}
			}
		}
		catch (Exception $e) {}

		// Check static list for mayoral overrides
		foreach ($this->mayoral_overrides as $override) {
			if (in_array($postalCode, $override['postal_codes'])) {
				$data->mayoral_rep = (object)$override['mayoral_rep'];
			}
		}

		// Retrieve the entire City/Town council
		if (isset($data->mayoral_rep->related->representative_set_url)) {
			$path = str_replace('representative-sets', 'representatives', $data->mayoral_rep->related->representative_set_url);
			$path = trailingslashit($path) . '?limit=1000';

			try {
				$regional = $this->doRequest($path);

				if (isset($regional->objects) && is_array($regional->objects)) {
					foreach ($regional->objects as $rep) {
						if ($this->contains($rep->elected_office, ['Mayor','Maire'])) {
							continue;
						}
						$rep->id = sha1(serialize($rep));
						$data->regional_reps[] = $rep;
					}
				}
			}
			catch (Exception $e) {}
		}

		return $data;
	}

	/**
	 * Cleanse a postal code removing all invalid characters.
	 *
	 * @param string
	 * @return string
	 */
	public function formatPostalCode($postalCode)
	{
		return preg_replace('/[^A-Z0-9]/', '', strtoupper($postalCode));
	}

	/**
	 * Determines if the given string contains the given value,
	 *
	 * @param string
	 * @param mixed
	 * @return boolean
	 */
	protected function contains($haystack, $needles)
	{
		foreach ((array) $needles as $needle) {
			if ($needle != '' && strpos($haystack, $needle) !== false) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Perform an API request using the specified path.
	 *
	 * @param string
	 * @return object
	 */
	protected function doRequest($path)
	{	
		$path = ltrim($path, '/');
		$hash = 'opennorth:'.sha1($path);

		$cache = get_transient($hash);
		if ($cache) {
			return $cache;

		}

		$response = wp_remote_get("$this->endpoint/$path");
		if (is_wp_error($response) || wp_remote_retrieve_response_code($response) != 200) {
			throw new Exception("Represent API request failed.\n\n".print_r($response, true));
		}

		$json = json_decode(wp_remote_retrieve_body($response));

		// Save response JSON in cache for 1 week
		set_transient($hash, $json, strtotime('+1 week') - time());

		return $json;
	}

}
