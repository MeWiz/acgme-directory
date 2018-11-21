// JavaScript Document
var queries_in_progress=[];
var csv_export_data=[];

$(document).ready(function() {
	"use strict";
    $('#doit').on('click',function(){getdata();});
	$('#csv_link').on('click', function(){csv_export();}).hide();
});

function csv_export() {
	"use strict";
	if (csv_export_data.length===0) { return; }
	
	csv_export_data=csv_export_data.filter(function(val){return val;}); 	//reindex array
	$.ajax({
		url: 'q_csv_export.php',
		type: 'POST',
		data: {
			csv_data: csv_export_data
		},
		success: function(ret) {
			var data=encodeURI('data:text/csv;charset=utf-8,'+ret);
			var lnk=document.createElement('a');
			lnk.setAttribute('href', data);
			lnk.setAttribute('download', 'export.csv');
			lnk.click();
		},
		error: function (jqXHR, textStatus) {
			console.log("q_csv_export err! "+textStatus+jqXHR.responseText);
		}
	});		
}

function getdata() {
	"use strict";
	
	// reset table, cancel pending queries
	$.each(queries_in_progress, function(key, val) {
		// explicitly abort all queries that were called in previous function, even if already aborted
		val.abort();
	});
	$('#csv_link').hide();
	$('#progtable tbody').empty();
	queries_in_progress=[];
	csv_export_data=[];
	
	console.log("q_proglist with state "+$('#state').val()+" & specialty "+$('#specialty').val());
	$.ajax({
		url: 'q_proglist.php',
		type: 'GET',
		data: {
			state: $('#state').val(),
			specialty: $('#specialty').val()
		},
		dataType: "json",
		success: function(ret) {
			if (ret.length===0) {
				// no programs found
				$('#progtable').append("<tr><td colspan='4'><i>No programs found for this state/specialty</i></td></tr>");
				return;
			}
			$.each(ret, function(key, val) {
				// create new row for each program
//				console.log("Searching info for prog "+val.progid);
				$('#progtable').append("<tr id='t_"+val.progid+"'><td>"+val.code+"</td><td>"+val.name+"</td></tr>");
				csv_export_data[val.progid]={code: val.code, name: val.name};
				var referer='https://apps.acgme.org/ads/Public/Programs/Search?stateId='+$('#state').val()+'&specialtyId='+$('#specialty').val()+'&specialtyCategoryTypeId=&numCode=&city='

				// query additional info for this program
				queries_in_progress[queries_in_progress.length]=$.ajax({
					url: 'q_proginfo.php',
					type: 'GET',
					data: {
						progid: val.progid,
						referer: referer
					},
					dataType:"json",
					success: function(proginfo) {
						var row_id="#t_"+proginfo.progid;	// id for row to which cells added
						
						// add address
						csv_export_data[proginfo.progid].address=proginfo.address;
						var add=proginfo.address.replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + '<br />' + '$2');
						$(row_id).append("<td>"+add+"</td>");
						
						// add emails
						if (proginfo.email.length===0) {
							// no emails found
							$(row_id).append("<td><i>None found</i></td>");
						}
						else {
							csv_export_data[proginfo.progid].email=proginfo.email;
							var email_str="";
							if (proginfo.email.length>1) {
								email_str=proginfo.email.join("<br />");
							}
							else {
								email_str=proginfo.email;
							}
							$(row_id).append("<td>"+email_str+"</td>");
						}
						// ideally, show the csv export link after the last query returns - this code makes it show after the first proginfo query returns
						$('#csv_link').show();
					},
					error: function(jqXHR, textStatus) {
						console.log("q_proginfo err! "+textStatus+jqXHR.responseText);
					}
				});
			});
//			console.log(csv_export_data);
		},
		error: function (jqXHR, textStatus) {
			console.log("q_proglist err! "+textStatus+jqXHR.responseText);
		}
	});
}