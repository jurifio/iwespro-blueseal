$(document).on('bs.gainplanactivemovement.disable', function () {

    var getVarsArray = [];
    var dataTable = $('.table').DataTable();
    var selectedRows = dataTable.rows('.selected').data();
    var selectedRowsCount = selectedRows.length;

    if (selectedRowsCount != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare un solo documento"
        }).open();
        return false;
    }

    var documentId = selectedRows[0].DT_RowId;

    modal = new $.bsModal('Disabilita  la visualizzazione del documento',
        {
            body: 'Proseguendo sarà disabilitato per sempre il documento <strong>' + documentId + '</strong>!<br />',
            okButtonEvent: function () {
                $.ajax(
                    {
                        url: '/blueseal/xhr/GainPlanMovementDisableManage',
                        method: 'POST',
                        data: {id: documentId}
                    }
                ).done(function (res) {
                    modal.writeBody(res);
                }).fail(function (res) {
                    modal.writeBody('OOPS! C\'è stato un problema!');
                    console.error(res);
                }).always(function () {
                    modal.setOkEvent(function () {
                        modal.hide();
                        dataTable.ajax.reload();
                    });
                });
            }
        }
    );
});

$(document).on('bs.gainplanactivemovement.enable', function () {

    var getVarsArray = [];
    var dataTable = $('.table').DataTable();
    var selectedRows = dataTable.rows('.selected').data();
    var selectedRowsCount = selectedRows.length;

    if (selectedRowsCount != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare un solo documento"
        }).open();
        return false;
    }

    var documentId = selectedRows[0].DT_RowId;

    modal = new $.bsModal('Abilita  la visualizzazione del documento!',
        {
            body: 'Proseguendo sarà abilitato il documento <strong>' + documentId + '</strong>!<br />',
            okButtonEvent: function () {
                $.ajax(
                    {
                        url: '/blueseal/xhr/GainPlanMovementDisableManage',
                        method: 'PUT',
                        data: {id: documentId}
                    }
                ).done(function (res) {
                    modal.writeBody(res);
                }).fail(function (res) {
                    modal.writeBody('OOPS! C\'è stato un problema!');
                    console.error(res);
                }).always(function () {
                    modal.setOkEvent(function () {
                        modal.hide();
                        dataTable.ajax.reload();
                    });
                });
            }
        }
    );
});
$(document).on('bs.gainplanassociations.cost', function () {
    let selectedRows = $('.dataTable').DataTable().rows('.selected').data();

    if (selectedRows.count() != 1) {
        new Alert({
            type: "warning",
            message: "Chiudi un contratto alla volta!"
        }).open();
        return false;
    }

    let id = selectedRows[0].DT_RowId;

    var modal = new $.bsModal('Seleziona Costi ', {
        body: '<label for="gainPlanPassive">Seleziona un costo</label><br />' +
            '<select id="gainPlanPassive" name="gainPlanPassive" class="full-width selectize"></select><br />'
    });
    modal.addClass('modal-wide');
    modal.addClass('modal-high');

    Pace.ignore(function () {
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'GainPlanPassiveMovement'
            },
            dataType: 'json'
        }).done(function (res2) {
            let am=0;
            let am2=0;
            let gainPlanPassive = $('select[name=\"gainPlanPassive\"]');
            if (typeof (gainPlanPassive[0].selectize) != 'undefined') gainPlanPassive[0].selectize.destroy();
            gainPlanPassive.selectize({
                valueField: 'id',
                labelField: 'servicename',
                searchField: ['fornitureName', 'invoice', 'serviceName', 'dateMovement'],
                options: res2,
                render: {
                    item: function (item, escape) {
                        am=parseFloat(item.amount);
                        am2=am.toFixed(2);
                        return '<div>' +
                            '<span class="label">|' + escape(item.fornitureName) + ' | ' + escape(item.dateMovement) + '</span> - ' +
                            '<span class="caption"> | <b>Fatt.:</b> ' + escape(item.invoice) + ' | <b>Serv.:</b>: ' + escape(item.serviceName) + ' | <b>Imp.:</b> € '+ escape(am2) +'</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        am=parseFloat(item.amount);
                        am2=am.toFixed(2);
                        return '<div>' +
                            '<span class="label">|' + escape(item.fornitureName) + ' | ' + escape(item.dateMovement) + '</span> - ' +
                            '<span class="caption"> | <b>Fatt.:</b> ' + escape(item.invoice) + ' | <b>Serv.:</b>: ' + escape(item.serviceName) + ' | <b>Imp.:</b> € '+ escape(am2) +'</span>' +
                            '</div>'
                    }
                }
            });
        });
        modal.showCancelBtn();
        modal.setOkEvent(function () {
            const data = {
                id: id,
                movPassId: $('#gainPlanPassive').val(),
            };
            $.ajax({
                method: 'PUT',
                url: '/blueseal/xhr/GainPlanPassiveMovementAssocAjaxController',
                data: data
            }).done(function (res) {
                modal.writeBody(res);
            }).fail(function (res) {
                modal.writeBody('Errore grave');
            }).always(function (res) {
                modal.setOkEvent(function () {
                    modal.hide();
                    $.refreshDataTable();
                });
                modal.showOkBtn();
            });
        });
    });
});


function lessyear(){
    currentYear=parseInt($('#currentYear').val());
    newYear=currentYear-1;
    link='/blueseal/gainplan/gainplan-attivo-servizi?countYear='+newYear;
    window.open(link,'_self');

}
function moreyear(){
    currentYear=parseInt($('#currentYear').val());
    newYear=currentYear+1;
    link='/blueseal/gainplan/gainplan-attivo-servizi?countYear='+newYear;
    window.open(link,'_self');

}