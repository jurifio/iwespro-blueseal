window.buttonSetup = {
    tag: "a",
    icon: "fa-arrow-down",
    permission: "/admin/product/delete&&allShops",
    event: "bs.shipment.delivery.time",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Segnala Partenza Spedizione",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs.shipment.delivery.time', function (e, element, button) {

    let dataTable = $('.dataTable').DataTable();

    let selectedRows = dataTable.rows('.selected').data();

    if (selectedRows.length != 1) {

        return false;
    }

    let shipmentId = selectedRows[0].DT_RowId;

    if(selectedRows[0].deliveryDate && selectedRows[0].deliveryDate.length != 0) {
        new Alert({
            type: "warning",
            message: "Tempo già registrato"
        }).open();
        return false;
    }

    let today = new Date().toISOString().slice(0, 10);
    let now = new Date().toTimeString().slice(0,8);
    let modal = new $.bsModal('Segnala Arrivo Spedizione', {
        body: '<div class="row">' +
                '<div class="col-xs-6>">' +
                '<label for="shipmentDate">Data di Arrivo</label>' +
                '<input autocomplete="off" type="date" id="shipmentDate" ' +
                'class="form-control" name="shipmentDate" value="'+today+'">' +
                '</div>'+
                '<div class="col-xs-6>">' +
                '<label for="shipmentTime">Orario</label>' +
                '<input autocomplete="off" type="time" id="shipmentTime" ' +
                'class="form-control" name="shipmentTime" value="'+now+'">' +
                '</div>'
    });

    modal.setOkEvent(function () {
        let dateTime = $('#shipmentDate').val()+' '+$('#shipmentTime').val();
        modal.showLoader();
        modal.setOkEvent(function () {
            modal.hide();
            $('.table').DataTable().ajax.reload(null, false);
        });


        Pace.ignore(function () {
            $.ajax({
                method: "get",
                url: "/blueseal/xhr/ShipmentManageController",
                data: {
                    shipmentId: shipmentId
                },
                dataType: "json"
            }).done(function (res2) {
                res2.deliveryDate = dateTime;
                Pace.ignore(function () {
                    $.ajax({
                        method: "put",
                        url: "/blueseal/xhr/ShipmentManageController",
                        data: {
                            shipment: res2
                        },
                    }).done(function (res2) {
                        modal.writeBody(res2);
                    }).fail(function () {
                        modal.writeBody('Errore');
                    });
                });
            }).fail(function () {
                modal.writeBody('Errore');
            });
        });


    });
});
