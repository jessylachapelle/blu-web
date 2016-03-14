<?php

//Récupère les informations via le formulaire
$nom      = strip_tags($_post['nom']);
$courriel = strip_tags($_POST['courriel']);
$message  = strip_tags($_POST['message']);
$sujet    = strip_tags($_POST['sujet']);

//Information de contact de la BLU
$emailBLU = "blu@aecs.info";
$messageConfirmation = "Merci de nous écrire, nous avons bien reçu votre message et le traiterons dans les plus bref délais.";

//L'entête du message envoyé à la BLU
$headers = "" .
           "Reply-To: " . $email . "\r\n" .
           "From: " . $from .  " <" . $email .">" . "\r\n" .
           "X-Mailer: PHP/" . phpversion();
$headers .= 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

//L'entête du message de confirmation
$clientHeaders = "" .
                 "Reply-To: $emailPtiteReplique\r\n" .
                 "From: La P'tite Réplique <$emailPtiteReplique>" . "\r\n" .
                 "X-Mailer: PHP/" . phpversion();
$clientHeaders .= 'MIME-Version: 1.0' . "\r\n";
$clientHeaders .= 'Content-type: text/html; charset=utf-8' . "\r\n";


//Envoie le courriel
mail($emailPtiteReplique, $sujet, $message, $headers);
mail($email, "Confirmation d'envoi", $messageConfirmation, $clientHeaders);

header("Location: ../contact.php?sent=true");
?>