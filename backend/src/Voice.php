<?php

namespace IUOE;

use Exception;
use IUOE\Mailer;
use IUOE\Represent;

class Voice
{
	public $id;
	public $name;
	public $email;
	public $street_address;
	public $city;
	public $postal_code;
	public $include_minister;
	public $profession;
	public $opt_in;
	public $sent_to;
	public $ip_address;
	public $user_agent;
	public $created_at;

	/**
	 * Retrieve provincial representative associated with the
	 * postal code for the voice.
	 *
	 * @return object
	 */
	public function getProvincialRep()
	{
		try {
			$representatives = (new Represent)->getPoliticians($this->postal_code, $this->street_address, $this->city);
		}
		catch (Exception $e) {
			error_log($e->getMessage());
		}
		if ($representatives && $representatives->provincial_rep) {
			return $representatives;
		}

		throw new Exception('Unable to determine provincial representative.');
	}

	/**
	 * Retrieve the final letter content.
	 *
	 * @return string
	 */
	public function getLetterSubject()
	{
		$content = get_field('for_supporter_of_this_cause_subject','option');

		if ($this->profession === 'Patient using a support program') {
			$content = get_field('patient_using_a_support_program_subject','option');
		} elseif ($this->profession === 'Someone in my family benefits from a patient support program') {
			$content = get_field('for_someone_in_my_family_benefits_subject','option');
		} elseif ($this->profession === 'Caregiver of a patient using a support program') {
			$content = get_field('for_hearing_healthcare_professional_subject','option');
		} elseif ($this->profession === 'Health care Professional') {
			$content = get_field('for_manufacturer_subject','option');
		} elseif ($this->profession === 'Member of a patient organization') {
			$content = get_field('for_patient_subject','option');
		} elseif ($this->profession === 'Supporter of this cause') {
			$content = get_field('for_supporter_of_this_cause_subject','option');
		}

		return $content;
	}

	/**
	 * Retrieve the final letter content.
	 *
	 * @return string
	 */
	public function getLetterBody()
	{
		$content = get_field('for_supporter_of_this_cause_body','option');
		// if ($this->profession === 'Patient using a support program') {
		// 	$content = get_field('patient_using_a_support_program_body','option');
		// } elseif ($this->profession === 'Someone in my family benefits from a patient support program') {
		// 	$content = get_field('for_someone_in_my_family_benefits_body','option');
		// } elseif ($this->profession === 'Caregiver of a patient using a support program') {
		// 	$content = get_field('for_hearing_healthcare_professional_body','option');
		// } elseif ($this->profession === 'Health care Professional') {
		// 	$content = get_field('for_manufacturer_body','option');
		// } elseif ($this->profession === 'Member of a patient organization') {
		// 	$content = get_field('for_patient_body','option');
		// } elseif ($this->profession === 'Supporter of this cause') {
		// 	$content = get_field('for_supporter_of_this_cause_body','option');
		// }

		$reps = $this->getProvincialRep();


		return Mailer::renderMergeTags($content, array(
      			'salutation'     => "",
      			'mp_name'       => $this->getMppNames($reps),
      			'mp_riding'     => $reps->provincial_rep->district_name,
      			'name'           => $this->name,
      			'email'          => $this->getMppEmails($reps),
      			'street_address' => $this->street_address,
      			'city'           => $this->city,
      			'postal_code'    => $this->postal_code,
		));
	}

  	public static function getMppNames(&$reps) {
		$mpp_names = $reps->provincial_rep->name;
		if(isset($reps->provincial_candidates)) {
			foreach($reps->provincial_candidates as $candidate ) {
				if(isset($candidate->email) && $candidate->email != "") 
					$mpp_names .= ', '.$candidate->name;
			}
		}
		return $mpp_names;
  	}
  	
	public static function getMppEmails(&$reps) {
		$mpp_emails = $reps->provincial_rep->email;
		if(isset($reps->provincial_candidates)) {
			foreach($reps->provincial_candidates as $candidate ) {
				if(isset($candidate->email)) 
					$mpp_emails .= ', '.$candidate->email;
			}
		}
		return $mpp_emails;
  	}

	/**
	 * Create a record.
	 *
	 * @param array
	 * @return \IUOE\Voice
	 */
	public static function create(array $data)
	{
		$voice = new self;
		$voice->name           = @$data['name'];
		$voice->email          = @$data['email'];
		$voice->street_address = @$data['street_address'];
		$voice->city           = @$data['city'];
		$voice->postal_code    = @$data['postal_code'];
		$voice->include_minister    = (boolean)@$data['include_minister'];
		$voice->profession     = @$data['profession'];
		$voice->opt_in         = (boolean)@$data['opt_in'];
		$voice->ip_address     = @$_SERVER['REMOTE_ADDR'];
		$voice->user_agent     = @$_SERVER['HTTP_USER_AGENT'];
		$voice->created_at     = current_time('mysql');
		$voice->updated_at     = current_time('mysql');

		if ($voice->save()) {
			return $voice;
		}
	}

	/**
	 * Find a record.
	 *
	 * @param integer
	 * @return \IUOE\Voice
	 */
	public static function find($id)
	{
		global $wpdb;
		$object = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}voices WHERE id = %d", $id));
		if ($object) {
			$voice = new self;
			$voice->id             = (int)$object->id;
			$voice->name           = $object->name;
			$voice->email          = $object->email;
			$voice->street_address = $object->street_address;
			$voice->city           = $object->city;
			$voice->postal_code    = $object->postal_code;
			$voice->include_minister = (boolean)$object->include_minister;
			$voice->profession     = $object->profession;
			$voice->opt_in         = (boolean)$object->opt_in;
			$voice->sent_to        = $object->sent_to;
			$voice->ip_address     = $object->ip_address;
			$voice->user_agent     = $object->user_agent;
			$voice->created_at     = $object->created_at;
			$voice->updated_at     = $object->updated_at;
			return $voice;
		}
	}

	/**
	 * Save a record.
	 *
	 * @return boolean
	 */
	public function save()
	{
		global $wpdb;

		// in WP4.5+ insert/update with fail silently if
		// the column strlen is greater than the column length
		$columns = array(
			'name'           => substr((string)$this->name,           0, 100),
			'email'          => substr((string)$this->email,          0, 100),
			'street_address' => substr((string)$this->street_address, 0, 255),
			'city'           => substr((string)$this->city,           0, 255),
			'postal_code'    => substr((string)$this->postal_code,    0, 7),
			'include_minister'    => (int)$this->include_minister,
			'profession'     => substr((string)$this->profession,     0, 255),
			'opt_in'         => (int)$this->opt_in,
			'sent_to'        => $this->sent_to,
			'ip_address'     => substr((string)$this->ip_address,     0, 255),
			'user_agent'     => substr((string)$this->user_agent,     0, 255),
			'created_at'     => $this->created_at,
			'updated_at'     => current_time('mysql'),
		);

		if (is_null($this->id)) {
			$result = $wpdb->insert("{$wpdb->prefix}voices", $columns);
			if ($result) {
				$this->id = $wpdb->insert_id;
			}
		}
		else {
			$result = $wpdb->update("{$wpdb->prefix}voices", $columns, array('id' => $this->id));
		}

		if ($wpdb->last_error) {
			throw new Exception($wpdb->last_error);
		}

		return ($result !== false);
	}
}
