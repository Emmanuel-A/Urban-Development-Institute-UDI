<?php

function iuoe_register_ajax(array $handlers) {
	foreach ($handlers as $handler) {
		if ($handler instanceof \IUOE\Ajax\Action) {
			$action = $handler->getAction();
			add_action("wp_ajax_{$action}", array($handler, 'handle'));
			if ($handler->isPublic()) {
				add_action("wp_ajax_nopriv_{$action}", array($handler, 'handle'));
			}
		}
	}
}

function iuoe_get_option($name, $default = null) {
	$options = get_option('iuoe');
	if (isset($options[$name]) === true) {
		return $options[$name];
	}
	return $default;
}

function uioe_send_letter($subject, $body, array $data) {
	(new \IUOE\Mailer)->send($body, $data, function($message) use ($subject, $data) {
		$message->setReturnPath(iuoe_get_option('mailer_from_email'));
		$message->setFrom(iuoe_get_option('mailer_from_email'));
		$message->setSender(iuoe_get_option('mailer_from_email'));
		$message->setFrom(iuoe_get_option('mailer_from_email'), $data['name']);
		$message->setSender(iuoe_get_option('mailer_from_email'));
		$message->setReplyTo($data['email'], $data['name']);
		$message->setSubject($subject);
	});
}
