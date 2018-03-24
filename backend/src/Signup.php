<?php

namespace IUOE;

use Exception;

class Signup
{
	public $id;
	public $name;
	public $email;
	public $message;
	public $ip_address;
	public $user_agent;
	public $created_at;

	/**
	 * Create a record.
	 *
	 * @param array
	 * @return \IUOE\Voice
	 */
	public static function create(array $data)
	{
		$voice = new self;
		$voice->name       = @$data['name'];
		$voice->email      = @$data['email'];
		$voice->message    = @$data['message'];
		$voice->ip_address = @$_SERVER['REMOTE_ADDR'];
		$voice->user_agent = @$_SERVER['HTTP_USER_AGENT'];
		$voice->created_at = current_time('mysql');
		$voice->updated_at = current_time('mysql');

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

		$object = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}signups WHERE id = %d", $id));
		if ($object) {
			$voice = new self;
			$voice->id         = (int)$object->id;
			$voice->name       = $object->name;
			$voice->email      = $object->email;
			$voice->message    = $object->message;
			$voice->ip_address = $object->ip_address;
			$voice->user_agent = $object->user_agent;
			$voice->created_at = $object->created_at;
			$voice->updated_at = $object->updated_at;
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
			'message'        => (string)$this->message,
			'ip_address'     => substr((string)$this->ip_address,     0, 255),
			'user_agent'     => substr((string)$this->user_agent,     0, 255),
			'created_at'     => $this->created_at,
			'updated_at'     => current_time('mysql'),
		);

		if (is_null($this->id)) {
			$result = $wpdb->insert("{$wpdb->prefix}signups", $columns);
			if ($result) {
				$this->id = $wpdb->insert_id;
			}
		}
		else {
			$result = $wpdb->update("{$wpdb->prefix}signups", $columns, array('id' => $this->id));
		}

		if ($wpdb->last_error) {
			throw new Exception($wpdb->last_error);
		}

		return ($result !== false);
	}
}
