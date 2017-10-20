(function () {
    $.ajax({
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'ProductSize'
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
            td.html('<select class=""></select>');
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
            if(working) return;
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
                    url: '/blueseal/xhr/ProductSizeGroupManage',
                    data: {
                        productSizeGroupId: td.data('column'),
                        productSizeId: value,
                        position: td.closest('tr').data('position')
                    },
                    dataType: "json"
                }).done(function (res) {
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
                    if(res.products) {
                        message+="<ul>";
                        for(let product of res.products) {
                            message+="<li>"+product.productId+'-'+product.productVariantId+"</li>";
                        }
                    }
                    td.html(td.data(savedHtmlDataName));
                    new $.bsModal(title,{
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
                case 27:
                    e.preventDefault();
                    undoCell($(this));
            }
        });
    });

})();