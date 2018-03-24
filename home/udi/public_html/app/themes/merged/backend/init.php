<?php

require_once __DIR__.'/vendor/autoload.php';

new \IUOE\PdfEndpoint;
new \IUOE\MailerOptionsPage;

iuoe_register_ajax([
	new \IUOE\Ajax\AddYourVoiceStep1Action,
	new \IUOE\Ajax\AddYourVoiceStep2Action,
	new \IUOE\Ajax\ContactUsAction,
	new \IUOE\Ajax\ThankMPStep1Action,
	new \IUOE\Ajax\ThankMPStep2Action,
]);
