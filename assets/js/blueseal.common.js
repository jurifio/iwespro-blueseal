$(document).ready(function () {
    var sessionMonitorContainer = $('#sessionMonitor');
    if(sessionMonitorContainer.length === 1) {
        Pace.ignore(function () {
            $.ajax({
                url: "/blueseal/xhr/SessionMonitor",
                dataType: "JSON"
            }).done(function (res) {
                sessionMonitorContainer.find('#s1').html(res.traffic.sessions);
                sessionMonitorContainer.find('#u1').html(res.traffic.users);
                sessionMonitorContainer.find('#l1').html(res.load.m1);
            })
        })
    }
});