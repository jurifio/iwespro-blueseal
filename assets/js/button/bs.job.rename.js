window.buttonSetup = {
    tag: "a",
    icon: "fa-hand-rock-o",
    permission: "/admin/product/delete&&allShops",
    event: "bs-job-rename",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Rinomina Job",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-job-rename', function (e, element, button) {

    let dataTable = $('.dataTable').DataTable();
    let selectedRows = dataTable.rows('.selected').data();
    if (selectedRows.length === 1) {

        var idJob = selectedRows[0].id;
        var scope = selectedRows[0].scope;
        var nameJob=selectedRows[0].name;



        let bsModal = new $.bsModal('Rinomina il Job', {
            body: '<div><p>Rinomina il Job n. <strong>'+ id +'</strong></p>' +
                '<p><strong>ambito:</strong></p>'+ scope +'</p>' +
                '<p><strong>nome</strong></p>' + nameJob + '</div>' +
                '<div class="form-group form-group-default required">' +
                '<label for="changeName">Nome da Assegnare</label>' +
                '<input autocomplete="off" type="text" id="changeName" ' +
                'placeholder="Nome da Assegnare" class="form-control" name="changeName" required="required">' +
                '</div>'

        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {

                name: $('input#changeName').val(),
                id :   idJob
            };
            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/JobManageNameController',
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