<?php
function headersToString($headers) {
  $headersString = "";
  foreach($headers as $header) {
    $headersString .= "$header\r\n";
  }

  return $headersString;
}

if (isset($_POST['name']) && $_POST['email'] && $_POST['message'] && $_POST['subject']) {
  $name = strip_tags($_POST['name']);
  $email = strip_tags($_POST['email']);
  $message = strip_tags($_POST['message']);
  $subject = strip_tags($_POST['subject']);

  $emailBLU = "blu@aecs.info";
  $confirmationSubject = "Confirmation d'envoi";
  $confirmationMessage = "Merci de nous écrire, nous avons bien reçu votre message et le traiterons dans les plus bref délais.";
  $phpVersion = phpversion();

  $headers = [
    "Reply-To: $email",
    "From: $name <$email>",
    "X-Mailer: PHP/$phpVersion",
    "MIME-Version: 1.0",
    "Content-type: text/html; charset='utf-8'",
  ];

  $clientHeaders = [
    "Reply-To: $emailBLU",
    "From: BLU <$emailBLU>",
    "X-Mailer: PHP/$phpVersion",
    "MIME-Version: 1.0",
    "Content-type: text/html; charset=utf-8",
  ];

  $success = mail($emailBLU, $subject, $message, headersToString($headers));
  
  if ($success) {
    mail($email, $confirmationSubject, $confirmationMessage, headersToString($clientHeaders));
    return header("Location: ../contact.php?sent=true");
  }
}

return header("Location: ../contact.php?sent=false&name=$name&email=$email&subject=$subject&message=$message");
?>
