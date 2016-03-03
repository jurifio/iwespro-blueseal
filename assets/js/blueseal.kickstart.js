/**
 * Created by Fabrizio Marconi on 14/10/2015.
 */
/**$(document).on('ready', function () {
	updateOnlineUsers();
	setInterval(function () {
		updateOnlineUsers();
	}, 30000);

});

function updateOnlineUsers() {
	var sessionMonitor = $('#sessionMonitor');
	sessionMonitor.html('<img src="/assets/img/ajax-loader.gif" />');
	Pace.ignore(function () {
		$.ajax({
			type: 'GET',
			url: '/blueseal/xhr/SessionMonitor'
		}).done(function (content) {
			sessionMonitor.html('<h2>' + content + ' Utenti Online</h2>');
		});
	});
}*/