<!doctype html>
<html>
<head>
<meta charset="utf-8">
<link href="css/styles.css" rel="stylesheet" type="text/css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script type="text/javascript" src="js/funcs.js"></script>
<title>ACGME Data Extractor</title>
</head>

<body>
<div data-role="page" id="page1">
<div data-role="header">
<h1>Accredited program search</h1>
</div>
<div data-role="content">
<div id="state_spl_div">
<?php
// get the master table of state names and specialty codes from the ACGME website
$url = "https://apps.acgme.org/ads/Public/Programs/Search";
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

$states=$specialty=array();
$select_states=$dom->getElementById('stateFilter');
foreach($select_states->getElementsByTagName('option') as $opt) {
	$id=$opt->getAttribute('value');
	if (is_numeric($id)) $states[$id]=trim($opt->nodeValue);
}

$select_spl=$dom->getElementById('specialtyFilter');
foreach($select_spl->getElementsByTagName('option') as $opt) {
	if ($opt->getAttribute('class')==='optionChild') continue; // skip fellowships
	$id=$opt->getAttribute('value');
	if (is_numeric($id)) $specialty[$id]=trim($opt->nodeValue);
}
?>
<form>
<select id="state" name="state">
<?php foreach ($states as $key=>$val) echo "\t<option value='$key'>$val</option>\r\n"; ?>
</select>
<select id="specialty" name="specialty" data-native-menu="false">
<?php foreach ($specialty as $key=>$val) echo "\t<option value='$key'>$val</option>\r\n"; ?>
</select>
<input type="button" name="doit" id="doit" value="Search &gt;&gt;">
</form>
</div>
<div id="progs">
<table border="0" cellpadding="0" cellspacing="0" id="progtable">
<thead>
<tr><th>Program ID</th><th>Program Name</th><th>emails</th></tr>
</thead>
</table>
</div>
</div>
<div data-role="footer">
<h4>&nbsp;</h4>
</div>
</body>
</html>
