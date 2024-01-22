<?php

/**
 * Requires the "PHP Email Form" library
 * The "PHP Email Form" library is available only in the pro version of the template
 * The library should be uploaded to: vendor/php-email-form/php-email-form.php
 * For more info and help: https://bootstrapmade.com/php-email-form/
 */

// Replace contact@example.com with your real receiving email address
$receiving_email_address = 'hafizshahman1@gmail.com';

if (file_exists($php_email_form = '../assets/vendor/php-email-form/php-email-form.php')) {
  include($php_email_form);
} else {
  die('Unable to load the "PHP Email Form" Library!');
}

$contact = new PHP_Email_Form;
$contact->ajax = true;

$contact->to = $receiving_email_address;
$contact->from_name = $_POST['name'];
$contact->from_email = $_POST['email'];
$contact->subject = $_POST['subject'];

// Uncomment below code if you want to use SMTP to send emails. You need to enter your correct SMTP credentials

$contact->smtp = array(
  'host' => 'sandbox.smtp.mailtrap.io',
  'username' => '136e206f1cd793',
  'password' => '70b132da55a11f',
  'port' => '2525'
);


$contact->add_message($_POST['name'], 'From');
$contact->add_message($_POST['email'], 'Email');
$contact->add_message($_POST['message'], 'Message', 10);

echo $contact->send();
?>
<?php
$to = "hafizshahman1@gmail.com";
$subject = $_POST['subject'];

// Load the HTML template from a file
$message = file_get_contents('forms/email.html');

// Replace placeholders in the template with actual content
$message = str_replace('{name}', 'John Doe', $message);
$message = str_replace('{message}', 'This is a test email.', $message);

$headers = "From: sender@example.com\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=UTF-8\r\n";

$mailSuccess = mail($to, $subject, $message, $headers);

if ($mailSuccess) {
  echo "Email sent successfully.";
} else {
  echo "Error: Unable to send email.";
}
?>
<?php

/**
 * This example shows how to handle a simple contact form safely.
 */

//Import PHPMailer class into the global namespace
use PHPMailer\PHPMailer\PHPMailer;

//Don't run this unless we're handling a form submission
if (array_key_exists('email', $_POST)) {
  date_default_timezone_set('Etc/UTC');
  require '../vendor/autoload.php';
  $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

  //Create a new PHPMailer instance
  $mail = new PHPMailer();
  //Send using SMTP to localhost (faster and safer than using mail()) – requires a local mail server
  //See other examples for how to use a remote server such as gmail
  $mail->isSMTP();
  $mail->Host = 'localhost';
  $mail->Port = 25;

  //Use a fixed address in your own domain as the from address
  //**DO NOT** use the submitter's address here as it will be forgery
  //and will cause your messages to fail SPF checks
  $mail->setFrom('from@example.com', 'First Last');
  //Choose who the message should be sent to
  //You don't have to use a <select> like in this example, you can simply use a fixed address
  //the important thing is *not* to trust an email address submitted from the form directly,
  //as an attacker can substitute their own and try to use your form to send spam
  $addresses = [
    'sales' => 'sales@example.com',
    'support' => 'support@example.com',
    'accounts' => 'accounts@example.com',
  ];
  //Validate address selection before trying to use it
  if (array_key_exists('dept', $_POST) && array_key_exists($_POST['dept'], $addresses)) {
    $mail->addAddress($addresses[$_POST['dept']]);
  } else {
    //Fall back to a fixed address if dept selection is invalid or missing
    $mail->addAddress('support@example.com');
  }
  //Put the submitter's address in a reply-to header
  //This will fail if the address provided is invalid,
  //in which case we should ignore the whole request
  if ($mail->addReplyTo($_POST['email'], $_POST['name'])) {
    $mail->Subject = 'PHPMailer contact form';
    //Keep it simple - don't use HTML
    $mail->isHTML(false);
    //Build a simple message body
    $mail->Body = <<<EOT
Email: {$_POST['email']}
Name: {$_POST['name']}
Message: {$_POST['message']}
EOT;

    //Send the message, check for errors
    if (!$mail->send()) {
      //The reason for failing to send will be in $mail->ErrorInfo
      //but it's unsafe to display errors directly to users - process the error, log it on your server.
      if ($isAjax) {
        http_response_code(500);
      }

      $response = [
        "status" => false,
        "message" => 'Sorry, something went wrong. Please try again later.'
      ];
    } else {
      $response = [
        "status" => true,
        "message" => 'Message sent! Thanks for contacting us.'
      ];
    }
  } else {
    $response = [
      "status" => false,
      "message" => 'Invalid email address, message ignored.'
    ];
  }

  if ($isAjax) {
    header('Content-type:application/json;charset=utf-8');
    echo json_encode($response);
    exit();
  }
}
?>