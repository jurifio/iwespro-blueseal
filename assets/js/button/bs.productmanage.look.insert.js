window.buttonSetup = {
    tag: "a",
    icon: "fa-object-group",
    permission: "/admin/product/edit",
    event: "bs-productmanage-look-insert",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Inserisci Prodotti in Un look ",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-productmanage-look-insert', function () {
    var dataTable = $('.table').DataTable();
    var selectedRows = dataTable.rows('.selected').data();

    if (1 > selectedRows.length) {
        new Alert({
            type: "warning",
            message: "Devi selezionare almeno un prodotto"
        }).open();
        return false;
    }

    var row = [];
    $.each(selectedRows, function (k, v) {
        row.push(v.DT_RowId);
    });

    let shopId = selectedRows[0].shopId;


    let bsModal = new $.bsModal('Seelziona il Look ', {
        body: `<div class="row">
                 <div class="form-group form-group-default selectize-enabled">
                                        <label for="code">seleziona la Correlazione</label>
                                        <select id="code" name="code"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                        </select>
                                    </div>
                </div>
                `
    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/ProductHasProductLookManageAjaxController',
        dataType: 'json'
    }).done(function (res2) {
        let select = $('#code');
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
            render: {
                option: function (item, escape) {
                    let rendResCats = '';

                    if(item.img == null && item.name == null){
                        rendResCats = '<div>' +
                            escape(item.id) +
                            '</div>';
                    } else if(item.img != null && item.name == null){
                        rendResCats = '<div>' +
                            escape(item.id) + ' | ' +
                            "<img style='width: 50px' src='" + escape(item.img) + "'>" +
                            '</div>';
                    } else if (item.img == null && item.name != null){
                        rendResCats = '<div>' +
                            escape(item.id) + ' | ' +
                            escape(item.name) + ' | ' +
                            escape(item.shopName) +
                            '</div>';
                    } else {
                        rendResCats = '<div>' +
                            escape(item.id) + ' | ' +
                            escape(item.name) + ' | ' +
                            escape(item.shopName) + ' | ' +
                            "<img style='width: 50px' src='" + escape(item.img) + "'>" +
                            '</div>';
                    }

                    return rendResCats;
                },
                item: function (item, escape) {
                    return '<div>' +
                        escape(item.name) +
                        '</div>';
                }
            }
        });

    });



    bsModal.addClass('modal-wide');
    bsModal.addClass('modal-high');
    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {



        const data = {
            row: row,
            code: $('#code').val(),
        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/ProductHasProductLookManageAjaxController',
            data: data
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function () {
            bsModal.writeBody('Si è verificato un errore')
        }).always(function () {
            bsModal.setOkEvent(function () {
                bsModal.hide();
                $('.table').DataTable().ajax.reload();
            });
        });
    });
});