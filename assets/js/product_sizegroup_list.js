;(function () {

    $(document).on('bs-macroGroup-add', function () {
        let bsModal = new $.bsModal('Aggiungi Gruppo', {
            body: '<p>Aggiugi un nuovo macrogruppo</p>' +
                '<div class="form-group form-group-default required">' +
                '<label for="productSizeMacroGroup">Nome macrogruppo</label>' +
                '<input autocomplete="off" type="text" id="productSizeMacroGroup" ' +
                'placeholder="Nome macrogruppo" class="form-control" name="productSizeMacroGroup" required="required">' +
                '</div>' +
                '<div class="form-group form-group-default required">' +
                '<label for="productSizeGroupName">Nome Gruppo Taglia</label>' +
                '<input autocomplete="off" type="text" id="productSizeGroupName" ' +
                'placeholder="Nome Gruppo Taglia" class="form-control" name="productSizeGroupName" required="required">' +
                '</div>' +
                '<div class="form-group form-group-default required">' +
                '<label for="locale">Locale</label>' +
                '<input autocomplete="off" type="text" id="locale" ' +
                'placeholder="Locale" class="form-control" name="locale" required="required">' +
                '</div>' +
                '<div class="form-group form-group-default required">' +
                '<label for="publicName">Nome Pubblico</label>' +
                '<input autocomplete="off" type="text" id="publicName" ' +
                '   placeholder="Nome Pubblico" class="form-control" name="publicName" required="required">' +
                '</div>'
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {
                name: $('input#productSizeMacroGroup').val(),
                productSizeGroupName: $('input#productSizeGroupName').val(),
                locale: $('input#locale').val(),
                publicName: $('input#publicName').val(),
            };
            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/SizeMacroGroupManage',
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
    });


    $(document).on('bs-macroGroup-delete', function () {

        let dataTable = $('.dataTable').DataTable();
        let selectedRows = dataTable.rows('.selected').data();
        if (selectedRows.length === 1) {

            var idMacroGroup = selectedRows[0].id;

            let bsModal = new $.bsModal('Elimina Gruppo', {
                body: '<p>Elimina macrogruppo</p>' +
                    '<div class="form-group form-group-default required">' +
                    '<label for="deleteMacroGroup">Elimina macrogruppo</label>' +
                    '<div><p>Premere ok per cancellare il macrogruppo con id:' + idMacroGroup + '</p></div>' +
                    '</div>'
            });

            bsModal.showCancelBtn();
            bsModal.setOkEvent(function () {
                const data = {
                    idMacroGroup: idMacroGroup,
                };
                $.ajax({
                    method: 'delete',
                    url: '/blueseal/xhr/SizeMacroGroupManage',
                    data: data
                }).done(function (res) {
                    bsModal.writeBody(res);
                }).fail(function (res) {
                    bsModal.writeBody('Errore grave');
                }).always(function (res) {
                    bsModal.setOkEvent(function () {
                        $.refreshDataTable();
                        bsModal.hide();
                    });
                    bsModal.showOkBtn();
                });
            });

        } else if (selectedRows.length < 1) {
            new Alert({
                type: "warning",
                message: "Devi selezionare una riga"
            }).open();
            return false;
        } else {
            new Alert({
                type: "warning",
                message: "Puoi cancellare solamente un macrogruppo alla volta"
            }).open();
            return false;
        }

    });


    //Aggiorna il nome del macrogruppo
    $(document).on('bs-update-nameMacroGroup', function () {

        let dataTable = $('.dataTable').DataTable();
        let selectedRows = dataTable.rows('.selected').data();

        if (selectedRows.length === 1) {

            var idMacroGroup = selectedRows[0].id;
            var actualNameMacroGroup = selectedRows[0].name;

            let bsModal = new $.bsModal('Cambia nome al macrogruppo', {
                body: '<br>Cambia il nome del macrogruppo con id: ' + idMacroGroup + '</br>' +
                    'Nome attuale: ' + actualNameMacroGroup + '</p>' +
                    '<div class="form-group form-group-default required">' +
                    '<label for="productSizeSlug">Nome macrogruppo</label>' +
                    '<input autocomplete="off" type="text" id="newMacroGroupName" ' +
                    'placeholder="Nome macrogruppo" class="form-control" name="newMacroGroupName" required="required">' +
                    '</div>'
            });

            bsModal.showCancelBtn();
            bsModal.setOkEvent(function () {
                const data = {
                    idMacroGroup: idMacroGroup,
                    nameMacroGroup: $('input#newMacroGroupName').val()
                };
                $.ajax({
                    method: 'put',
                    url: '/blueseal/xhr/SizeMacroGroupManage',
                    data: data
                }).done(function (res) {
                    bsModal.writeBody(res);
                }).fail(function (res) {
                    bsModal.writeBody('Errore grave');
                }).always(function (res) {
                    bsModal.setOkEvent(function () {
                        //refresha solo tabella e non intera pagina
                        $.refreshDataTable();
                        bsModal.hide();
                    });
                    bsModal.showOkBtn();
                });
            });
        } else if (selectedRows.length < 1) {
            new Alert({
                type: "warning",
                message: "Non hai selelezionato nessun Gruppo"
            }).open();
        } else if (selectedRows.length > 1) {
            new Alert({
                type: "warning",
                message: "Puoi modificare il nome di un solo Macrogruppo alla volta"
            }).open();
        }
    });
    $(document).on('bs-update-size-grouplocale', function () {
        let dataTable = $('.dataTable').DataTable();
        let selectedRows = dataTable.rows('.selected').data();


        if (selectedRows.length === 1) {
            let bsModal = new $.bsModal('Gestisci locale per il Gruppo Taglia', {
                body: '<p>Combinazione Gruppo Taglie->Paese->Categoria</p>' +
                    '<label for="Paese">Paese</label>' +
                    '<select id="country" class="full-width selectize" name="country"></select>' +
                    '<div id="categoriesTree"></div'
            });

            var valueArray = [];

            var selKeys = [];
            /* bsModal.addClass('modal-wide');
             bsModal.addClass('modal-high');*/

            Pace.ignore(function () {
                $.ajax({
                    method: 'GET',
                    url: '/blueseal/xhr/GetTableContent',
                    data: {
                        table: 'Country'

                    },
                    dataType: 'json'
                }).done(function (res2) {
                    var select = $('#country');
                    if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
                    select.selectize({
                        valueField: 'id',
                        labelField: 'name',
                        maxItems: 250,
                        searchField: 'name',
                        options: res2,
                        onItemAdd: function (val) {
                            valueArray.push(val);
                        }

                    });

                });
            });
            Pace.ignore(function () {
            var radioTree = $("#categoriesTree");

                radioTree.dynatree({
                    initAjax: {
                        url: "/blueseal/xhr/CategoryTreeController"
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
            });
            var body = $('#bsModal .modal-body');
            var cancelButton = $('#bsModal .modal-footer .btn-default');
            var okButton = $('#bsModal .modal-footer .btn-success');
                cancelButton.html("Annulla");
                cancelButton.show();
                okButton.html("Cambia").off().on('click', function () {
                    Pace.ignore(function () {
                    if (selKeys.length) {

                       $.ajax({
                            type: "put",
                            url: "/blueseal/xhr/ProductSizeGroupUpdateLocale",
                            data: {
                                id:selectedRows[0].id,
                                newCategories: selKeys,
                                newCountry: valueArray
                            }
                        }).done(function (res) {
                            bsModal.writeBody(res);
                        }).fail(function (res) {
                            bsModal.writeBody('Errore grave');
                        }).always(function (res) {
                            bsModal.setOkEvent(function () {
                                //refresha solo tabella e non intera pagina
                                bsModal.hide();
                            });
                            bsModal.showOkBtn();
                        });
                    } else {
                        body.html('Nessuna categoria selezionata.');
                        okButton.html('Ok').off().on('click', function () {
                            bsModal.modal('hide');
                        });
                        cancelButton.hide();
                    }
                });

                });

        }
    });
})();

