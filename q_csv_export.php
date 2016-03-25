<?php
// takes javascript var and outputs as csv

$out = fopen("php://output", 'w');

$maxemails=0;
foreach($_POST['csv_data'] as $csv_data) {
	// identify the max emails present in the entire data set
	if (count($csv_data['email'])>$maxemails) $maxemails=count($csv_data['email']);
}

$headerline=array("program code","program name","address");
for ($i=1;$i<=$maxemails;$i++) $headerline[]="email".$i;
fputcsv($out, $headerline);

foreach($_POST['csv_data'] as $csv_data) {
	$csvrow=array($csv_data['code'], $csv_data['name'],	$csv_data['address']);
	for ($i=0;$i<$maxemails;$i++) {
		if (isset($csv_data['email'][$i])) $csvrow[]=$csv_data['email'][$i];
		else $csvrow[]="-";
	}
	fputcsv($out,$csvrow);
}

fclose($out);
?>