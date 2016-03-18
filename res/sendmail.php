<?php
if (isset($_POST['name']) && $_POST['email'] && $_POST['message'] && $_POST['subject']) {
  $name = strip_tags($_POST['name']);
  $email = strip_tags($_POST['email']);
  $message = strip_tags($_POST['message']);
  $subject = strip_tags($_POST['subject']);

  $emailBLU = "blu@aecs.info";
  $confirmationSubject = "Confirmation d'envoi";
  $confirmationMessage = "Merci de nous écrire, nous avons bien reçu votre message et le traiterons dans les plus bref délais.";

  $headers = "Reply-To: $email \r\n
             From: $name <$email> \r\n
             X-Mailer: PHP/" . phpversion() . "\r\n
             MIME-Version: 1.0 \r\n
             Content-type: text/html;
             charset='utf-8' \r\n";

  $clientHeaders = "Reply-To: $emailBLU \r\n
                   From: BLU <$emailBLU> \r\n
                   X-Mailer: PHP/" . phpversion() . "\r\n
                   MIME-Version: 1.0 \r\n
                   Content-type: text/html;
                   charset=utf-8' \r\n";

  mail($emailBLU, $subject, $message, $headers);
  mail($email, $confirmationSubject, $confirmationMessage, $clientHeaders);

  return header("Location: ../contact.php?sent=true");
} return header("Location: ../contact.php?error=403&sent=false");
?>
