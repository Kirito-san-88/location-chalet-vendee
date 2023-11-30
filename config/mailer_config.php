<?php

use PHPMailer\PHPMailer\PHPMailer;

define("SMTP_AUTH", true);
define("SMTP_SECURE", "ssl");
define("MAIL_PORT", 465);
define("MAIL_HOST", "ssl0.ovh.net");
define("MAIL_USER", "admin@test.fr");
define("MAIL_PASS", "test");

// Configuration pour PHP Mailer
function configure_phpmailer(PHPMailer $php_mailer): ?PHPMailer {
	// Quitte la fonction en cas de paramètre null/undefined.
	if (empty($php_mailer) || is_null($php_mailer)) return null;

	// SMTP SETTING
	$php_mailer->isSMTP();
	$php_mailer->Host = MAIL_HOST;
	$php_mailer->SMTPAuth = SMTP_AUTH;
	$php_mailer->Username = MAIL_USER;
	$php_mailer->Password = MAIL_PASS;
	$php_mailer->Port = MAIL_PORT;
	$php_mailer->SMTPSecure = SMTP_SECURE;

	return $php_mailer;
}