<?php
// extract email IDs from program info page
// searches for mailto: links in DOM
// returns json with all unique email IDs, empty if no emails found

$url = "https://apps.acgme.org/ads/Public/Programs/Detail?programId=".$_GET['progid'];
$ch = curl_init();
$timeout = 5;
curl_setopt($ch, CURLOPT_URL, $url) or die (curl_error($ch));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) or die (curl_error($ch));
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout) or die (curl_error($ch));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false) or die (curl_error($ch));
$html = curl_exec($ch) or die (curl_error($ch));
curl_close($ch);

$dom = new DOMDocument();
@$dom->loadHTML($html);

$rVal=array();
$links=$dom->getElementsByTagName('a');		// find link
foreach ($links as $link) {
	$target=$link->getAttribute('href');
	if (substr($target,0,7)==="mailto:") {
		// ignore "mailto:" part, remove any weird question marks, then validate with the email filter
		$rVal[]=filter_var(str_replace('?','',substr($target,7)),FILTER_SANITIZE_EMAIL);
	}
}
echo json_encode(array_unique($rVal));
?>
