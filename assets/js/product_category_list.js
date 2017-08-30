(function ($) {
    const treeSelector = "#categoryTree";
    $(document).ready(function () {
        glyph_opts = {
            preset: "bootstrap3",
            map: {
                expanderClosed: "glyphicon glyphicon-menu-right",  // glyphicon-plus-sign
                expanderLazy: "glyphicon glyphicon-menu-right",  // glyphicon-plus-sign
                expanderOpen: "glyphicon glyphicon-menu-down"  // glyphicon-minus-sign
            }
        };
        var datailsContainer = $('#categoryDetails');
        $(treeSelector).fancytree({
            extensions: ["dnd", "edit", "glyph", "wide"],
            glyph: glyph_opts,
            checkbox: false,
            source: {
                url: "/blueseal/xhr/CategoryTreeController"
            },
            activate: function (event, data) {
                datailsContainer.html('<img src="/assets/img/ajax-loader.gif" />');
                Pace.ignore(function () {
                    $.ajax({
                        url: "/blueseal/xhr/ProductCategoryController",
                        data: {
                            id: data.node.key
                        },
                        dataType: 'json'
                    }).done(function (res) {
                        var translations = 0;
                        var translationLenght = res.productCategoryTranslation.length;
                        for(var k = 0; k < translationLenght; k++) if(res.productCategoryTranslation[k].name.length > 1) translations++;
                        var html = '<div class="row">' +
                                        '<div class="clo-sm-12">' +
                                            '<ul>' +
                                                '<li><strong>Id Categoria:</strong><span>' + res.id + '</span></li>' +
                                                '<li><strong>Slug Categoria:</strong><span>' + res.slug + '</span></li>' +
                                                '<li><strong>Traduzioni:</strong><span>' + translations + '</span></li>' +
                                                '<li><strong>Associazioni con i Marketplace: </strong><span>' + res.marketplaceAccountCategory.length+ '</span></li>' +
                                                '<li><strong>Associazioni con i Dizionari: </strong><span>' + res.dictionaryCategory.length+ '</span></li>' +
                                                '<li><strong>Associazioni con i Prodotti: </strong><span>' + res.product.length+ '</span></li>' +
                                                '<li><strong>Associazioni con i Marketplace Discendenti: </strong><span>' + res.descendantMarketplaceAccountCategory + '</span></li>' +
                                                '<li><strong>Associazioni con i Dizionari Discendenti: </strong><span>' + res.descendantDictionaryCategory + '</span></li>' +
                                                '<li><strong>Associazioni con i Prodotti Discendenti: </strong><span>' + res.descendantProduct + '</span></li>' +
                                            '</ul>' +
                                        '</div>' +
                                    '</div>';
                        datailsContainer.html(html);
                    })
                })
            },
            dnd: {
                autoExpandMS: 400,
                focusOnClick: true,
                preventVoidMoves: true, // Prevent dropping nodes 'before self', etc.
                preventRecursiveMoves: true, // Prevent dropping nodes on own descendants
                dragStart: function (node, data) {
                    /** This function MUST be defined to enable dragging for the tree.
                     *  Return false to cancel dragging of node.
                     */
                    return true;
                },
                dragEnter: function (node, data) {
                    /** data.otherNode may be null for non-fancytree droppables.
                     *  Return false to disallow dropping on node. In this case
                     *  dragOver and dragLeave are not called.
                     *  Return 'over', 'before, or 'after' to force a hitMode.
                     *  Return ['before', 'after'] to restrict available hitModes.
                     *  Any other return value will calc the hitMode from the cursor position.
                     */
                    // Prevent dropping a parent below another parent (only sort
                    // nodes under the same parent)
                    /*           if(node.parent !== data.otherNode.parent){
                                return false;
                              }
                              // Don't allow dropping *over* a node (would create a child)
                              return ["before", "after"];
                    */
                    return true;
                },
                dragDrop: function (node, data) {
                    /**
                     * This function MUST be defined to enable dropping of items on
                     *  the tree.
                     */

                    var destinationParent = data.node.parent;
                    var source = data.otherNode;

                    if (source.parent.key === destinationParent.key) return false;
                    var bsModal = new $.bsModal('Conferma Spostamento', {
                        body: "<p>Sei sicuro di voler spostare la categoria: " + source.title + " nella categoria " + destinationParent.title + "?</p>"
                    });

                    bsModal.showCancelBtn();
                    bsModal.setOkEvent(function () {
                        bsModal.showLoader();
                        $.ajax({
                            url: "/blueseal/xhr/CategoryTreeController",
                            method: "put",
                            data: {
                                node: source.key,
                                newParent: destinationParent.title === 'root' ? 1 : destinationParent.key
                            }
                        }).done(function (res) {
                            data.otherNode.moveTo(node, data.hitMode);
                            bsModal.hide();
                            return true;
                        }).fail(function (res) {
                            return false;
                        });
                    });
                }
            }
        });
    });

    $(document).on('bs.category.insert', function () {
        var tree = $(treeSelector).fancytree("getTree"),
            node = tree.getActiveNode();
        if (!node) node = tree.getRootNode();
        var bsModal = new $.bsModal('Aggiungi Categoria', {
            body: '<p>Il nuovo nodo verrà inserito come figlio di ' + node.title + '</p>' +
            '<div class="form-group form-group-default required">' +
            '<label for="new-category-name">Nome</label>' +
            '<input autocomplete="off" id="new-category-name" class="form-control" name="new-category-name" required>' +
            '</div>' +
            '<div class="form-group form-group-default required">' +
            '<label for="new-category-description">Descrizione</label>' +
            '<input autocomplete="off" id="new-category-description" class="form-control" name="new-category-description">' +
            '</div>'
        });
        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            var input = $('input#new-category-name').val(),
                description = $('input#new-category-description').val();

            if (typeof input === 'undefined' && input.length < 2) return false;
            bsModal.showLoader();
            bsModal.hideCancelBtn();
            $.ajax({
                url: "/blueseal/xhr/CategoryTreeController",
                method: "post",
                data: {
                    parent: node.key,
                    title: input,
                    description: description
                }
            }).done(function (res) {
                node.addChildren({
                    title: input,
                    key: res
                });
                bsModal.hide();
                return true;
            }).fail(function (res) {
                bsModal.writeBody('ERRORE');
            })
        });
    });

    $(document).on('bs.category.delete', function () {
        "use strict";
        var tree = $(treeSelector).fancytree("getTree"),
            node = tree.getActiveNode();
        if (!node) {
            return false;
        }
        var bsModal = new $.bsModal('Eliminazione Categoria',
            {
                body: 'Sei Siuro di vole eliminare questa categoria e tutte le sottocategorie??'
            });
        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            bsModal.showLoader();
            bsModal.setOkEvent(function () {
                bsModal.hide()
            });
            bsModal.hideCancelBtn();
            bsModal.hideOkBtn();
            $.ajax({
                url: "/blueseal/xhr/CategoryTreeController",
                method: "delete",
                data: {
                    node: node.key,
                }
            }).done(function (res) {
                bsModal.writeBody('Categoria Eliminata');
                bsModal.showOkBtn();
                node.remove();
                return true;
            }).fail(function (res) {
                res = JSON.parse(res.responseText);
                var html = '<p>ERRORE!</p>';
                if(res.length > 0) {
                    html+='<br />' +
                        '<p>Non è stato possibile eliminare la categoria ' +
                        'perchè ci alcuni prodotti sono ancora associati ' +
                        'ad essa o a qualcuno dei suoi discendenti:' +
                        '</p>' +
                        '<ul>';
                    const maxRows = 20;
                    var arrayLength = res.length;
                    for (var i = 0; i < arrayLength; i++) {
                        //Do something
                        html += '<li>'+res[i]+'</li>';
                        if(i > maxRows) {
                            html += '<li>.....</li>'
                            break;
                        }
                    }

                    html+='</ul>';

                    bsModal.writeBody(html);
                } else {
                    html+='<br /><p>Conttattare un tecnico</p>';
                    bsModal.writeBody(html);
                }
                bsModal.showOkBtn();
                return false;
            })
        });
    });
})(jQuery);