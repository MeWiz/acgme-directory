// JavaScript Document
var queries_in_progress=[];
var csv_export_data=[];

$(document).ready(function() {
	"use strict";
    $('#doit').on('click',function(){getdata();});
});

function getdata() {
	"use strict";
	
	// reset table, cancel pending queries
	$('#progtable tbody').empty();
	$.each(queries_in_progress, function(key, val) {
		// explicitly abort all queries that were called in previous function, even if already aborted
		val.abort();
	});
	queries_in_progress=[];
	
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

				// query additional info for this program
				queries_in_progress[queries_in_progress.length]=$.ajax({
					url: 'q_proginfo.php',
					type: 'GET',
					data: {
						progid: val.progid
					},
					dataType:"json",
					success: function(proginfo) {
						var row_id="#t_"+proginfo.progid;	// id for row to which cells added
						
						// add address
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
					},
					error: function(jqXHR) {
						console.log(jqXHR.responseText);
					}
				});
			});
//			console.log(csv_export_data);
		},
		error: function (jqXHR) {
			console.log(jqXHR.responseText);
		}
	});
}