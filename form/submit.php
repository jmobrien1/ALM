<?php
header('Content-Type: text/html; charset=utf-8');
$to = 'info@allegiancelistmarketing.com';
$card = htmlspecialchars($_POST['card'] ?? '');
$name = htmlspecialchars($_POST['name'] ?? '');
$email = htmlspecialchars($_POST['email'] ?? '');
$company = htmlspecialchars($_POST['company'] ?? '');
$notes = htmlspecialchars($_POST['notes'] ?? '');

$subj = "List Request (card $card) from $name";
$body = "Card ID: $card\nName: $name\nEmail: $email\nCompany: $company\n\nNotes:\n$notes\n";
$hdrs = "From: noreply@allegiancelistmarketing.com\r\nReply-To: $email\r\n";

if (mail($to, $subj, $body, $hdrs)) {
  echo "<p>Thanks! We received your request and will contact you soon.</p>";
} else {
  http_response_code(500);
  echo "<p>Sorry, there was an error sending your request. Please email info@allegiancelistmarketing.com.</p>";
}
