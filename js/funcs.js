// JavaScript Document
$(document).ready(function() {
	"use strict";
    $('#doit').on('click',function(){getdata();});
});

function getdata() {
	"use strict";
	$('#progtable tbody').empty();
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
				$.ajax({
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