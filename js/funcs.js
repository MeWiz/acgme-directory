// JavaScript Document
var queries_in_progress=[];

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
			$.each(ret, function(key, val) {
				// create new row for each program
				$('#progtable').append("<tr id='t_"+val.progid+"'><td>"+val.code+"</td><td>"+val.name+"</td></tr>");
				// query email addresses for this program
				queries_in_progress[queries_in_progress.length]=$.ajax({
					url: 'q_proginfo.php',
					type: 'GET',
					data: {
						progid: val.progid
					},
					dataType:"json",
					success: function(proginfo) {
						var row_id="#t_"+proginfo.progid;	// id for row to which cells added
						var email_str=proginfo.email.join(", ");
						$(row_id).append("<td>"+email_str+"</td>");
					},
					error: function(jqXHR) {
						console.log(jqXHR.responseText);
					}
				});
			});
		},
		error: function (jqXHR) {
			console.log(jqXHR.responseText);
		}
	});
}