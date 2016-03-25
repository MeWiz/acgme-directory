<?php
// takes javascript var and outputs as csv

// first delete old files in dir
$now=time();
$files=glob("tmp/*");
foreach ($files as $file) {
	if (is_file($file))
		if ($now-filemtime($file)>=60*60*24) unlink($file);
}

// create new file
$filename=strtolower("tmp/".$_POST['state']).rand(100,999).".csv";
$out = fopen($filename, 'w');

$maxemails=0;
$str=array();
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
echo $filename;
?>