window.buttonSetup = {
    tag: "a",
    icon: "fa-play-circle",
    permission: "/admin/product/delete&&allShops",
    event: "bs.job.start",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Avvia Job",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs.job.start', function (e, element, button) {

    let dataTable = $('.dataTable').DataTable();
    let selectedRows = dataTable.rows('.selected').data();

    if (selectedRows.length != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare un Job per Avviarlo"
        }).open();
        return false;
    }

    let selectedRow = dataTable.row('.selected').data();
    let jobId = selectedRow.DT_RowId;

    let modal = new $.bsModal('Avvia Job', {});
    modal.showLoader();

    Pace.ignore(function () {
        $.ajax({
            method: "get",
            url: "/blueseal/xhr/JobManage",
            data: {
                jobId: jobId
            },
            dataType: "json"
        }).done(function (job) {
            modal.writeBody('<div class="row">' +
                '<div class="col-xs-6>">' +
                    '<p>Vuoi davvero avviare il job?</p>' +
                '</div>'
            );

            modal.setOkEvent(function () {
                job.manualStart = 1;
                modal.showLoader();
                modal.setOkEvent(function () {
                    modal.hide();
                    $('.table').DataTable().ajax.reload(null, false);
                });
                Pace.ignore(function () {
                    $.ajax({
                        method: "put",
                        url: "/blueseal/xhr/JobManage",
                        data: {
                            job: job
                        },
                    }).done(function (res2) {
                        modal.writeBody('Dati Correttamente Aggiornati');
                    }).fail(function (res) {
                        modal.writeBody('OOPS! C\'è stato un problema. Contatta un amministratore');
                        console.error(res);
                    });
                });
            });
        }).fail(function () {
            modal.writeBody('OOPS! C\'è stato un problema. Contatta un amministratore');
        });


    });
});
