window.buttonSetup = {
    tag: "a",
    icon: "fa-exchange",
    permission: "/admin/product/edit&&allShops",
    event: "bs-product-shooting-change",
    class: "btn btn-default",
    rel: "tooltip",
    title: " Modifica shooting del Prodotto",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-product-shooting-change', function (e, element, button) {
    var dataTable = $('.table').DataTable();
    let products = [];
    let shop = [];
    let getVarsArray = [];
    let selectedRows = $('.table').DataTable().rows('.selected').data();

    let selectedRowsCount = selectedRows.length;

    if (selectedRowsCount < 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare uno o più Prodotti per poterli taggare"
        }).open();
        return false;
    }

    let i = 0;
    let pieces = 0;
    $.each(selectedRows, function (k, v) {
        if (v.shooting != 'no') {
            products.push(v.DT_RowId);
            i++;
        }

    });

    let bsModal = new $.bsModal('Modifica  o associa lo shooting dei   prodotti ', {
        body: '<div class="form-group form-group-default required">' +
        '<label for="shootingId">Seleziona lo Shooting</label>' +
                '<select id="shootingId" name="shootingId">' +
            '<option selected value>Seleziona un\'opzione</option>' +
            '</select>' +
            '</div>'
    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'Shooting'

        },
        dataType: 'json'
    }).done(function (res2) {
        var select2 = $('#shootingId');
        if (typeof (select2[0].selectize) != 'undefined') select2[0].selectize.destroy();
        select2.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
            render: {
                item: function (item, escape) {
                    return '<div>' +
                        '<span class="label">' + escape(item.id) + ' | ' + escape(item.friendDdt) +  ' | ' + escape(item.year) + '</span> - ' +
                        '<span class="caption">pezzi:' + escape(item.pieces) + '</span>' +
                        '</div>'
                },
                option: function (item, escape) {
                    return '<div>' +
                        '<span class="label">' + escape(item.id) + ' | ' + escape(item.friendDdt) +  ' | ' + escape(item.year) + '</span> - ' +
                        '<span class="caption">pezzi:' + escape(item.pieces) + '</span>' +
                        '</div>'
                }
            },
            onInitialize: function () {

            }
        });
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        const data = {
            products: products,
            shootingId:$('#shootingId').val()
        };
        $.ajax({
            method: 'POST',
            url: '/blueseal/xhr/ProductShootingChangeAjaxController',
            data: data
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                bsModal.showOkBtn();

                bsModal.hide();
                dataTable.ajax.reload();
            });
            bsModal.showOkBtn();
        });
    });
})
;

