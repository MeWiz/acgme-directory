<?php
# Use the Curl extension to query Google and get back a page of results
$url = "https://apps.acgme.org/ads/Public/Programs/Detail?programId=".$_GET['progid'];
$ch = curl_init();
$timeout = 5;
curl_setopt($ch, CURLOPT_URL, $url) or die (curl_error($ch));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) or die (curl_error($ch));
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout) or die (curl_error($ch));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false) or die (curl_error($ch));
$html = curl_exec($ch) or die (curl_error($ch));
curl_close($ch);

# Create a DOM parser object
$dom = new DOMDocument();

# Parse the HTML from Google.
# The @ before the method call suppresses any warnings that
# loadHTML might throw because of invalid HTML in the page.
@$dom->loadHTML($html);

$rVal=array();
$links=$dom->getElementsByTagName('a');
foreach ($links as $link) {
	$target=$link->getAttribute('href');
	if (substr($target,0,7)==="mailto:") $rVal[]=filter_var(str_replace('?','',substr($target,7)),FILTER_SANITIZE_EMAIL);
}
echo json_encode(array_unique($rVal));
?>
