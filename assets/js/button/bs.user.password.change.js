window.buttonSetup = {
    tag: "a",
    icon: "fa-unlock-alt",
    permission: "/admin/product/edit&&allShops",
    event: "bs.user.password.change",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Cambia Password",
    placement: "bottom"
};

$(document).on('bs.user.password.change', function () {

    var bsModal = $('#bsModal');
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    header.html('Cambia Password');

    var selectedRows = $('.table').DataTable().rows('.selected').data();
    var selectedRowsCount = selectedRows.length;

    if (selectedRowsCount != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare un Utente per poter cambiare la password"
        }).open();
        return false;
    }

    var userId;
    $.each(selectedRows, function (k, v) {
        userId = v.DT_RowId.split('__')[1];
    });

    body.html('<img src="/assets/img/ajax-loader.gif" />');

    Pace.ignore(function () {
        $.ajax({
            url: '/blueseal/xhr/ChangeUserPassword',
            type: "get"
        }).done(function (response) {
            body.html('<input id="changePwdUser" value="'+response+'" label="Nuova Password" aria-label="Nuova Password">');

            okButton.html('Ok').off().on('click', function () {
                okButton.off().on('click', function () {
                    bsModal.modal('hide')
                });

                var putData = {};
                putData.userId = userId;
                putData.password = $('#changePwdUser').val();

                body.html('<img src="/assets/img/ajax-loader.gif" />');
                $.ajax({
                    url: '/blueseal/xhr/ChangeUserPassword',
                    type: 'put',
                    data: putData
                }).done(function (response) {
                    body.html('<p>Password Cambiata: '+response+'</p>');
                }).fail(function (response) {
                    body.html('<p>Errore</p>');
                });
            });
        });
    });

    bsModal.modal();
});
