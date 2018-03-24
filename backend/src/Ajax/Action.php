<?php

namespace IUOE\Ajax;

abstract class Action
{
	/**
	 * @var boolean
	 */
	protected $public = true;

	/**
	 * @var string
	 */
	protected $action = null;

	/**
	 * Handle an ajax request.
	 *
	 * @return void
	 */
	abstract public function handle();

	/**
	 * Retrieve a POST varaiable.
	 *
	 * @param string
	 * @param mixed
	 */
	public function request($key, $default = null)
	{
		$value = filter_input(INPUT_POST, $key, FILTER_SANITIZE_STRING);

		if (is_null($default) === false && empty($value)) {
			return $default;
		}

		return $value;
	}

	/**
	 * Send an HTTP response.
	 *
	 * @param mixed
	 * @param integer
	 */
	public function response($content, $status = 200)
	{
		http_response_code($status);
		wp_send_json($content);
		exit;
	}

	/**
	 * Send an HTTP response.
	 *
	 * @param mixed
	 */
	public function error_response($content)
	{
		$this->response(array('error' => $content), 500);
	}

	/**
	 * Whether this action is public or only for authenticated users.
	 *
	 * @return boolean
	 */
	public function isPublic()
	{
		return $this->public;
	}

	/**
	 * Get the action for this instance.
	 *
	 * @return string
	 */
	public function getAction()
	{
		if (strlen($this->action)) {
			return $this->action;
		}

		$action = preg_replace('/^.*?([^\\\\]*)Action$/u', '$1', get_class($this));
		$action = strtolower(preg_replace('/(?<!^)([A-Z])/u', '_$1', $action));

		return sanitize_key($action);
	}
}
