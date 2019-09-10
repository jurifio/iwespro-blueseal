window.buttonSetup = {
    tag: "a",
    icon: "fa-list",
    permission: "/admin/product/delete&&allShops",
    event: "bs-job-loglist",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Rinomina Job",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-job-loglist', function (e, element, button) {

    let dataTable = $('.dataTable').DataTable();
    let selectedRows = dataTable.rows('.selected').data();
    if (selectedRows.length === 1) {

        var idJob = selectedRows[0].id;
        var scope = selectedRows[0].scope;
        var nameJob=selectedRows[0].name;
        var priority=selectedRows[0].priority;



        let bsModal = new $.bsModal('Rinomina il Job o aggiorna la priorita', {
            body: '<div><p>Visualizza il Log del Job n. <strong>'+ idJob +'</strong></p>' +
                '<p><strong>ambito:</strong></p>'+ scope +'</p>' +
                '<p><strong>nome:</strong></p>' + nameJob + '</div>' +
                '<div id="result"></div>'
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {

                name: $('input#changeName').val(),
                id :   idJob,
                priority :priority

            };
            $.ajax({
                method: 'get',
                url: '/blueseal/xhr/JobManageLogListAjaxController',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    $.refreshDataTable();
                    bsModal.hide();
                    //window.location.reload();
                });
                bsModal.showOkBtn();
            });
        });
    } else if (selectedRows.length < 1){
        new Alert({
            type: "warning",
            message: "Devi selezionare una riga"
        }).open();
        return false;
    } else {
        new Alert({
            type: "warning",
            message: "Puoi aggiornare una riga alla volta"
        }).open();
        return false;
    }

});