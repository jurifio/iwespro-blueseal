window.buttonSetup = {
    tag: "a",
    icon: "fa-ship",
    permission: "/admin/product/delete&&allShops",
    event: "bs.order.tracker.send",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Invia Codice Tracker",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs.order.tracker.send', function (e, element, button) {

    const rows = $.getDataTableSelectedRowsData();

    let modal = new $.bsModal('Preparazione Spedizioni', {
        body: '<div class="form-group form-group-default">' +
        '<label for="carrier">Vettore</label>' +
        '<select id="#carrier" name="carrier" class="full-width"></select>' +
        '</div>'
    });

    Pace.ignore(function () {
        $.ajax({
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'Carrier'
            },
            dataType: 'json'
        }).done(function (res2) {
            var select = $('select[name=\"carrier\"]');
            if (select.length > 0 && typeof select[0].selectize != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2,
            });
            select[0].selectize.setValue(1);
        });

        $(document).on('change','select[name="carrier"]',function () {

        });

        modal.okButton.html('Invia').off().on('click', function () {

        });
    });

});
