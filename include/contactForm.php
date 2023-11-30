<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Importe les addresses mails des admins.
require_once("config\admin_mails.php");
require_once("config\mailer_config.php");

const RESULT_CODE_OK    = 0;
const RESULT_CODE_ERR   = 1;

// Messages
const MESSAGES = array(
    "en" => array(
        "bad_captcha" => "Bad captcha. Please try again.",
        "msg_sent" => "Your message has been sent.",
        "msg_error" => "Message could not be sent. Details: ",
        "bad_post" => "Message could not be sent: Invalid POST data.",
    ),
    "fr" => array(
        "bad_captcha" => 'Mauvais captcha, veuillez réessayer.',
        "msg_sent" => "Votre message a bien été envoyé.",
        "msg_error" => "Votre message n'a pas été envoyé. Details: ",
        "bad_post" => "Votre message n'a pas été envoyé: Requête POST invalide."
    )
);

// Crée un nouvel objet vide
$response = new stdClass();

$response->result_code = RESULT_CODE_ERR;

if (isset($_POST['firstName'], $_POST['lastName'], $_POST['email'], $_POST['subject'], $_POST['message'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    $lang = $_POST['lang'];


    if (isset($_POST['h-captcha-response']) && !empty($_POST['h-captcha-response'])) {
        $secret = 'secret';
        $verifyResponse = file_get_contents('https://hcaptcha.com/siteverify?secret=' . $secret . '&response=' . $_POST['h-captcha-response'] . '&remoteip=' . $_SERVER['REMOTE_ADDR']);
        $responseData = json_decode($verifyResponse);
        if (!$responseData->success) {
            $response->message = MESSAGES[$lang]["bad_captcha"]; 
        } else {
            try {
                // Crée une nouvelle instance de PHP Mailer et la configure.
                // Voir config\mailer_config.php pour plus de détails.
                $mail = configure_phpmailer(new PHPMailer(true));

                //EMAIL SETTING
                $mail->setFrom($email);
                $mail->FromName = join(" ", [$firstName, $lastName]);
                $mail->addReplyTo($email);

                // Adresses
                foreach (ADMIN_MAILS as $admin_mail) {
                    $mail->addAddress($admin_mail);
                }

                // BCC
                foreach (BCC_MAILS as $bcc_mail) {
                    $mail->addBCC($bcc_mail, 'admin');
                }

            
                

                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';// Set email format to HTML

                $mail->Subject = $subject;
                $mail->Body = $message;
                $mail->AltBody = $message;

                if ($mail->send()) {
                    $response->message = MESSAGES[$lang]["msg_sent"];
                    $response->result_code = RESULT_CODE_OK;
                } else {
                    $response->message = MESSAGES[$lang]["msg_error"] . $mail->ErrorInfo;
                } 
            } catch (Exception $e) {
                $response->message = MESSAGES[$lang]["msg_error"] . $e->getMessage();
            }
        }
    }  
} else {
    $response->message = MESSAGES[$lang]["bad_post"];
}

echo json_encode($response);
die();