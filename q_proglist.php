<?php
// return list of programs matching state and specialty
// looks for table rows with the class of "listview-row" and then the cells in that row

$url = "https://apps.acgme.org/ads/Public/Programs/Search?stateId=".$_GET['state']."&specialtyId=".$_GET['specialty'];
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
$i=0;
$rows=$dom->getElementsByTagName('tr');
foreach ($rows as $row) {
	if (trim($row->getAttribute('class'))==="listview-row") {
		$cells=$row->getElementsByTagName('td');
		$rVal[$i]['progid']=$row->getAttribute('data-item-key');	// this is the program ID used for q_proglist
		$rVal[$i]['name']=trim($cells->item(3)->nodeValue);
		$rVal[$i++]['code']=trim($cells->item(1)->nodeValue);	// acgme program code
	}
}
echo json_encode($rVal);
?>