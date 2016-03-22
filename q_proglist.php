<?php
// return list of programs matching state and specialty
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
		$rVal[$i]['progid']=$row->getAttribute('data-item-key');
		$rVal[$i]['name']=trim($cells->item(2)->nodeValue);
		$rVal[$i++]['code']=trim($cells->item(0)->nodeValue);
	}
}
echo json_encode($rVal);
?>