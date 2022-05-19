window.buttonSetup = {
    tag:"a",
    icon:"fa-calendar",
    permission:"/admin/product/edit&&allShops",
    event:"bs-manage-changeSeason",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Cambia Stagione ai prodotti selezionati",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-manage-changeSeason', function () {

    var bsModal = $('#bsModal');
    var dataTable = $('.dataTable').DataTable();
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var loader = body.html();
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    var getVarsArray = [];
    var selectedRows = $('.table').DataTable().rows('.selected').data();

    var selectedRowsCount = selectedRows.length;

    if (selectedRowsCount < 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare almeno un prodotto"
        }).open();
        return false;
    }

    var i = 0;
    var row = [];
    var getVars = '';
    $.each(selectedRows, function (k, v) {
        row[i] = {};
        var idsVars = v.DT_RowId.split('-');
        row[i].id = idsVars[0];
        row[i].productVariantId = idsVars[1];
        row[i].name = v.name;
        i++;
        //getVars += 'row_' + i + '=' + v.DT_RowId.split('__')[1] + '&';
    });
    $.ajax({
        url: "/blueseal/xhr/ChangeProductsSeason",
        type: "get"
    }).done(function (res) {
        header.html('Modifica Stagione');
        var bodyContent = '<div style="min-height: 220px"><select class="full-width" placehoder="Seleziona lo status" name="productSeasonsId" id="productSeasonsId"><option value=""></option></select></div>';
        body.html(bodyContent);
        var arrRes = JSON.parse(res);
        $('#productSeasonsId').selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: arrRes,
            placeholder: 'Seleziona una stagione',
            render: {
                option: function (item, escape) {
                    return '<div>' +
                        (item.name ? '<span class="name"' +
                        (0 == item.isActive ? ' style="color: #888;" ' : '') +
                        '>' + escape(item.name) + '</span>' : '') +
                        '</div>';
                }
            }
        });
        $('#productSeasonsId').selectize()[0].selectize.setValue(arrRes.length - 1);
    });
    cancelButton.html("Annulla");
    cancelButton.show();

    bsModal.modal('show');

    okButton.html("Cambia Stagione").off().on('click', function (e) {
        var seasonId = $('#productSeasonsId option:selected').val();

        Pace.ignore(function () {
            $.ajax({
                url: "/blueseal/xhr/ChangeProductsSeason",
                type: "POST",
                data: {
                    action: 'updateSeason',
                    rows: row,
                    productSeasonId: seasonId
                }
            }).done(function (res) {
                body.html(res);
            }).fail(function () {
                body.html("OOPS! Modifica non eseguita!");
            }).always(function () {
                okButton.html('Ok');
                okButton.off().on('click', function () {
                    bsModal.modal('hide');
                    dataTable.ajax.reload(null, false);
                });
            });
        });
    });
    bsModal.modal();
});
