window.buttonSetup = {
    tag:"a",
    icon:"fa-sitemap",
    permission:"/admin/product/edit",
    event:"bs.category.edit",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Cambia Categoria ai prodotti selezionati",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs.category.edit', function (e, element, button) {
    var bsModal = $('#bsModal');
    var dataTable = $('.dataTable').DataTable();
    var header = $('#bsModal .modal-header h4');
    var body = $('#bsModal .modal-body');
    var cancelButton = $('#bsModal .modal-footer .btn-default');
    var okButton = $('#bsModal .modal-footer .btn-success');
    var selKeys = [];

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

    header.html('Assegna Categorie');

    body.css("text-align", 'left');
    body.html('<div id="categoriesTree"></div>');
    bsModal.modal();
    Pace.ignore(function () {
        var radioTree = $("#categoriesTree");
        if (radioTree.length) {
            radioTree.dynatree({
                initAjax: {
                    url: "/blueseal/xhr/GetCategoryTree"
                },
                autoexpand: true,
                checkbox: true,
                imagePath: "/assets/img/skin/icons_better.gif",
                //		selectMode: ,
                /*		onPostInit: function () {
                 var vars = $("#ProductCategory_id").val().trim();
                 var ids = vars.split(',');
                 for (var i = 0; i < ids.length; i++) {
                 if (this.getNodeByKey(ids[i]) != null) {
                 this.getNodeByKey(ids[i]).select();
                 }
                 }
                 $.map(this.getSelectedNodes(), function (node) {
                 node.makeVisible();
                 });
                 $('#categoriesTree').scrollbar({
                 axis: "y"
                 });
                 },*/
                onSelect: function (select, node) {
                    // Display list of selected nodes
                    var selNodes = node.tree.getSelectedNodes();
                    // convert to title/key array
                    selKeys = $.map(selNodes, function (node) {
                        return node.data.key;
                    });
                    //$("#ProductCategoryId").val(JSON.stringify(selKeys));
                }
            });

            cancelButton.html("Annulla");
            cancelButton.show();
            okButton.html('Cambia').off().on('click', function () {
                if (selKeys.length) {
                    $.ajax({
                        url: '/blueseal/xhr/ProductHasProductCategory',
                        type: 'POST',
                        data: {
                            action: 'updateCat',
                            rows: row,
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
