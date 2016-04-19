var alertHtml = "" +
	"<div class=\"alert alert-dismissible alert-warning fade in\" role=\"alert\">" +
	"<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">" +
	"<span aria-hidden=\"true\">&times;</span></button>" +
	"<strong class=\"alert-title\">{title}</strong> <span class=\"alert-message\">{body}</span> </div>";

var tagList = "";

$(document).on('bs.log.download', function (e,element,button) {
	if(window.running != true) {
		window.downloadIconSave = element.html();
		element.html('<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>');
		e.preventDefault();
		$.ajax({
			type: "POST",
			async: false,
			url:"/blueseal/xhr/JobLogDownloadController",
			data: {
				job: 2
			}
		}).progress(function() {

		}).done(function() {
			window.running = true;
			element.trigger('click');
		}).fail(function() {
		});
	} else {
		window.running = false;
		element.html(window.downloadIconSave);
	}
});