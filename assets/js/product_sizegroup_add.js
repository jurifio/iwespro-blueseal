;(function () {
    $.ajax({
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'ProductSize',
            orderBy: [
                'name'
            ]
        },
        dataType: 'json'
    }).done(function (productSizes) {
        "use strict";
        let locked = false;
        const productSizeIdDataName = 'productsizeid';
        const savedHtmlDataName = 'savedValue';

        const editCell = function (td) {
            if (locked) return;
            if (td.find('select').length > 0) return;
            locked = true;
            td.data(savedHtmlDataName, td.html());
            const value = td.data(productSizeIdDataName);
            td.html('<select class="">' +
                '<option value="false"></option>' +
                '</select>');
            for (let productSize of productSizes) {
                td.find('select').append('<option value="' + productSize.id + '">' + productSize.name + '</option>');
            }
            td.find('select').val(value).focus();
        };

        const undoCell = function (cellInput) {
            const td = cellInput.closest('td');
            td.html(td.data(savedHtmlDataName));
            new Alert({
                type: "warning",
                message: "Annullato"
            }).open();
            setTimeout(function () {
                locked = false;
            }, 100);

        };

        let working = false;
        const saveCell = function (cellInput) {
            "use strict";
            if (!locked) return;
            if (working) return;
            working = true;
            const newHtml = cellInput.find('option:selected').html();
            const value = cellInput.find('select').val();
            const td = cellInput.closest('td');
            if (value === td.data('productsizeid')) {
                undoCell(cellInput);
                return;
            }
            Pace.ignore(function () {
                $.ajax({
                    method: 'put',
                    url: '/blueseal/xhr/ProductSizeGroupPositionManage',
                    data: {
                        productSizeGroupId: td.data('column'),
                        productSizeId: value,
                        position: td.closest('tr').data('position')
                    },
                    dataType: "JSON"
                }).done(function (res) {
                    res = JSON.parse(res);
                    td.data(productSizeIdDataName, value);
                    td.html(newHtml);
                    new Alert({
                        type: "success",
                        message: "Taglia Salvata"
                    }).open();
                }).fail(function (res) {
                    res = res.responseJSON;
                    let title = "Errore nel salvataggio delle taglie";
                    let message = res.message + '<br />';
                    if (res.products) {
                        message += "<ul>";
                        for (let product of res.products) {
                            message += "<li>" + product.productId + '-' + product.productVariantId + "</li>";
                        }
                    }
                    td.html(td.data(savedHtmlDataName));
                    new $.bsModal(title, {
                        body: message
                    });
                }).always(function () {
                    locked = false;
                    working = false;
                })
            });

        };

        $(document).on('keyup', 'table.table.size-table tbody td.edit-cell', function (e) {
            if (e.keyCode === 13) {
                e.preventDefault();
                editCell($(this));
            }
        });

        $(document).on('click', 'table.table.size-table tbody td.edit-cell', function () {
            editCell($(this));
        });

        $(document).on('blur', 'table.table.size-table tbody td.edit-cell select', function () {
            saveCell($(this).closest('td'));
        });

        $(document).on('keydown', 'table.table.size-table tbody td.edit-cell select', function (e) {
            switch (e.keyCode) {
                case 27:
                    e.preventDefault();
                    undoCell($(this));
                    break;
                case 9:
                    let td = $(this).closest('td');
                    let present = td.attr('tabindex');
                    let bigger = present + 1;
                    let element = $('.table tbody [tabindex="' + bigger + '"]');

                    if (element.length > 0) {
                        element.focus();
                        return;
                    }
                    let rightOne = td;
                    $('.table tbody td[tabindex]').each(function () {
                        let actual = $(this).attr('tabindex');
                        if (present <= actual <= bigger) {
                            bigger = actual;
                            rightOne = $(this).attr('tabindex');
                        }
                    });
                    rightOne.next().focus();
                case 13:
                    e.preventDefault();
                    saveCell($(this).closest('td'));
                    break;
            }
        });
    });

    $(document).on('bs-group-delete', function () {
        let bsModal = new $.bsModal('Aggiungi Gruppo', {
            body: '<p>Elimina una colonna Gruppo Taglia</p>' +
            '<div class="form-group form-group-default required">' +
                '<label for="productSizeGroupId">ID Gruppo Taglia</label>' +
                '<input autocomplete="off" type="number" id="productSizeGroupId" ' +
                    'placeholder="ID Gruppo Taglia" class="form-control" name="productSizeGroupId" required="required">' +
            '</div>'
        });

        bsModal.setOkEvent(function () {
            const productSizeGroupId = $('#productSizeGroupId').val();
            if(productSizeGroupId.length > 0) {
                bsModal.showLoader();
                Pace.ignore(function () {
                    $.ajax({
                        url: '/blueseal/xhr/ProductSizeGroupManage',
                        method: 'delete',
                        data: {
                            productSizeGroupId: productSizeGroupId
                        },
                        dataType: 'json'
                    }).done(function () {
                        bsModal.writeBody('Gruppo Taglie eliminato!');
                    }).fail(function (res) {
                        let newBody = '<p>Errore nel cancellare il gruppo taglia</p>';
                        if(res.responseJSON && res.responseJSON.products) {
                            newBody += '<ul>';
                            let i = 0;
                            for(let product of res.responseJSON.products)
                            {
                                newBody += '<li>'+product.id+'-'+product.productVariantId+'</li>'
                                if(++i > 100){
                                    newBody+= '<li>...</li>';
                                    break;
                                }
                            }
                            newBody+='</ul>'
                        }
                        bsModal.writeBody(newBody);
                    }).always(function () {
                        bsModal.setOkEvent(function () {
                            window.location.reload();
                        });
                    });
                });
            }
        })
    });

    $(document).on('bs-group-add', function () {
        let bsModal = new $.bsModal('Aggiungi Gruppo', {
            body: '<p>Aggiugi una nuova colonna Gruppo Taglia</p>' +
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
                    'placeholder="Nome Pubblico" class="form-control" name="publicName" required="required">' +
            '</div>'
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {
                name: $('input#productSizeGroupName').val(),
                locale: $('input#locale').val(),
                publicName: $('input#publicName').val(),
                macroName: $('input#productSizeGroupMacroName').val()
            };
            bsModal.showLoader();
            bsModal.hideOkBtn();
            bsModal.hideCancelBtn();
            Pace.ignore(function () {
                $.ajax({
                    method: 'post',
                    url: '/blueseal/xhr/ProductSizeGroupManage',
                    data: data,
                    dataType: "json"
                }).done(function (res) {
                    bsModal.writeBody('Gruppo Taglie creato con successo!');
                }).fail(function (res) {
                    bsModal.writeBody('Errore nella creazione del gruppo taglie: <br />' + e.responseJSON.message);
                }).always(function (res) {
                    bsModal.setOkEvent(function () {
                        window.location.reload();
                    });
                    bsModal.showOkBtn();
                });
            });
        });
    });

    const deleteRow = function (rowNum, versus) {
        if (versus === undefined || versus === 'false') versus = false;

        return $.ajax({
            method: 'delete',
            url: '/blueseal/xhr/CProductSizeGroupRowManage',
            data: {
                rowNum: rowNum,
                versus: versus,
                macroName: $('input#productSizeGroupMacroName').val()
            },
            dataType: "json"
        })
    };

    $(document).on('bs-group-row-add', function () {
        let bsModal = new $.bsModal('Inserisci Riga', {
            body: '<p>Inserisci una nuova riga dopo: (questo eliminerà l\'ultima riga della tabella)</p>' +
            '<div class="form-group form-group-default required">' +
            '<label for="starterRow">Riga</label>' +
            '<input autocomplete="off" type="text" id="starterRow" ' +
            'placeholder="Riga" class="form-control" name="starterRow" required="required">' +
            '</div>' +
            '<div class="form-group form-group-default required selectize-enabled">' +
            '<label for="versus">Seleziona il verso</label>' +
            '<select id="versus" name="versus" class="full-width selectize">' +
            '<option value="up">In sù</option>' +
            '<option value="down">In giù</option>' +
            '</select>' +
            '</div>'
        });

        $('select#versus').selectize();

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const versus = $('select#versus').val();
            const rowNum = $('input#starterRow').val();
            bsModal.showLoader();
            bsModal.hideOkBtn();
            bsModal.hideCancelBtn();
            Pace.ignore(function () {
                deleteRow(false)
                    .done(function () {
                        Pace.ignore(function () {
                            $.ajax({
                                method: 'put',
                                url: '/blueseal/xhr/ProductSizeGroupRowManage',
                                data: {
                                    rowNum: rowNum,
                                    versus: versus,
                                    macroName: $('input#productSizeGroupMacroName').val()
                                },
                                dataType: "json"
                            }).done(function () {
                                bsModal.writeBody('Riga inserita');
                                bsModal.setOkEvent(function () {
                                    window.location.reload();
                                });
                                bsModal.showOkBtn();
                            }).fail(function () {
                                bsModal.writeBody('Errore nell\'inserire la riga');
                                bsModal.setOkEvent(function () {
                                    window.location.reload();
                                });
                            })
                        });
                    }).fail(function () {
                    bsModal.writeBody('Non è stato possibile eliminare la riga indicata');
                    bsModal.setOkEvent(function () {
                        bsModal.hide();
                    });
                    bsModal.showOkBtn();
                });
            });
        })
    });

    $(document).on('bs-group-row-delete', function () {
        let bsModal = new $.bsModal('Elimina riga', {
            body: '<p>Elimina la riga</p>' +
            '<div class="form-group form-group-default required">' +
            '<label for="deleteRow">Riga</label>' +
            '<input autocomplete="off" type="number" min="0" max="36" step="1" id="deleteRow" ' +
            'placeholder="Riga" class="form-control" name="deleteRow" required="required">' +
            '</div>' +
            '<div class="form-group form-group-default required selectize-enabled">' +
            '<label for="versus">Scorri Tabella</label>' +
            '<select id="versus" name="versus" class="full-width selectize">' +
            '<option selected="selected" value="false">No</option>' +
            '<option value="up">In sù</option>' +
            '<option value="down">In giù</option>' +
            '</select>' +
            '</div>'
        });
        $('select#versus').selectize();

        bsModal.setOkEvent(function () {
            const rowToDelete = $('input#deleteRow').val();
            const selectedVersus = $('select#versus').val();
            if (rowToDelete) {
                deleteRow(rowToDelete, selectedVersus)
                    .done(function () {
                        bsModal.writeBody('Riga Eliminata');
                        bsModal.setOkEvent(function () {
                            window.location.reload();
                            bsModal.hide();
                        });
                        bsModal.showOkBtn();
                    }).fail(function () {
                    bsModal.writeBody('Non è stato possibile eliminare la riga indicata');
                    bsModal.setOkEvent(function () {
                        bsModal.hide();
                    });
                    bsModal.showOkBtn();
                })
            }

        })

    });

    let oldValue = null;
    const insertInput = function(th,name) {
        "use strict";
        if(th.find('input').length > 0) return;
        oldValue = th.html();
        th.html('<input style="width: 100%" >');
        th.find('input').attr('name',th.data('name'));
        th.find('input').val(oldValue);
        th.find('input').focus();
    };
    $(document).on('click', 'table.table.size-table thead th.editable', function (e) {
        insertInput($(this));
    });
    $(document).on('keyup','table.table.size-table thead th input', function (e) {
        if (e.keyCode === 13) {
            e.preventDefault();
            var input = $(this);
            let data = {};
            let newVal = $(this).val();
            data[$(this).attr('name')] = newVal
            data.productSizeGroupId = $(this).closest('th').data('column');
            Pace.ignore(function () {
                $.ajax({
                    method: 'put',
                    url: '/blueseal/xhr/ProductSizeGroupManage',
                    data: data
                }).done(function (res) {
                    new Alert({
                        type: "success",
                        message: "Salvato"
                    }).open();
                    oldValue = newVal;
                }).fail(function (res) {
                    new Alert({
                        type: "danger",
                        message: "Errore nel salvataggio"
                    }).open();
                }).always(function () {
                    input.closest('th').html(oldValue);
                });
            });
        }
    });
    $(document).on('blur','table.table.size-table thead th input', function () {
        $(this).closest('th').html(oldValue);
    });
})();