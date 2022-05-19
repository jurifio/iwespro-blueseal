window.buttonSetup = {
    tag:"a",
    icon:"fa-sitemap",
    permission:"/admin/product/edit&&allShops",
    event:"bs-category-edit",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Cambia Categoria ai prodotti selezionati",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-category-edit', function (e, element, button) {
    let bsModal = $('#bsModal');
    let dataTable = $('.dataTable').DataTable();
    let header = $('#bsModal .modal-header h4');
    let body = $('#bsModal .modal-body');
    let cancelButton = $('#bsModal .modal-footer .btn-default');
    let okButton = $('#bsModal .modal-footer .btn-success');
    let selKeys = [];

    let selectedRows = $('.table').DataTable().rows('.selected').data();

    let selectedRowsCount = selectedRows.length;

    if (selectedRowsCount < 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare almeno un modello"
        }).open();
        return false;
    }

    let row = [];
    $.each(selectedRows, function (k, v) {
        row.push(v.id);
    });

    header.html('Assegna Categorie');

    body.css("text-align", 'left');
    body.html('<div id="categoriesTree"></div>');
    bsModal.modal();
    Pace.ignore(function () {
        let radioTree = $("#categoriesTree");
        if (radioTree.length) {
            radioTree.dynatree({
                initAjax: {
                    url: "/blueseal/xhr/CategoryTreeController"
                },
                autoexpand: true,
                checkbox: true,
                imagePath: "/assets/img/skin/icons_better.gif",
                onSelect: function (select, node) {
                    // Display list of selected nodes
                    let selNodes = node.tree.getSelectedNodes();
                    // convert to title/key array
                    selKeys = $.map(selNodes, function (node) {
                        return node.data.key;
                    });
                }
            });

            cancelButton.html("Annulla");
            cancelButton.show();
            okButton.html('Cambia').off().on('click', function () {
                if (selKeys.length) {
                    $.ajax({
                        url: '/blueseal/xhr/ProductSheetModelPrototypeHasProductCategoryManage',
                        type: 'POST',
                        data: {
                            ids: row,
                            newCategories: selKeys
                        }
                    }).done(function (res) {
                        body.html(res);
                        okButton.html('Ok').off().on('click', function () {
                            bsModal.modal('hide');
                            dataTable.ajax.reload(null, false);
                        });
                        cancelButton.hide();
                    });
                } else {
                    body.html('Nessuna categoria selezionata.');
                    okButton.html('Ok').off().on('click', function () {
                        bsModal.modal('hide');
                    });
                    cancelButton.hide();
                }
            });
        }

    });
});
