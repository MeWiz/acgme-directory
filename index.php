<?php
# Use the Curl extension to query Google and get back a page of results
$url = "https://apps.acgme.org/ads/Public/Programs/Search";
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

$states=$specialty=array();
$select_states=$dom->getElementById('stateFilter');
foreach($select_states->getElementsByTagName('option') as $opt) {
	$id=$opt->getAttribute('value');
	if (is_numeric($id)) $states[$id]=trim($opt->nodeValue);
}

$select_spl=$dom->getElementById('specialtyFilter');
foreach($select_spl->getElementsByTagName('option') as $opt) {
	$id=$opt->getAttribute('value');
	if (is_numeric($id)) $specialty[$id]=trim($opt->nodeValue);
}
?>
<html>
<head>
</head>
<body>
<form action="q_proglist.php" method="post">
<select id="state" name="state">
<?php foreach ($states as $key=>$val) echo "\t<option value='$key'>$val</option>\r\n"; ?>
</select>
<select id="specialty" name="specialty">
<?php foreach ($specialty as $key=>$val) echo "\t<option value='$key'>$val</option>\r\n"; ?>
</select>
<input type="submit" name="submit" id="submit" value="Submit">
</form>
</body>