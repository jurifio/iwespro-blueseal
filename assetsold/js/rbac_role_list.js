(function ($) {
    const treeSelector = "#rolesTree";
    $(document).ready(function () {
        var glyph_opts = {
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
                url: "/blueseal/xhr/RbacRolesTreeController"
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

    $(document).on('bs.roles.insert', function () {
        var tree = $(treeSelector).fancytree("getTree"),
            node = tree.getActiveNode();
        if (!node) node = tree.getRootNode();
        var bsModal = new $.bsModal('Aggiungi ruolo', {
            body: '<p>Il nuovo nodo verrà inserito come figlio di ' + node.title + '</p>' +
            '<div class="form-group form-group-default required">' +
            '<label for="new-role-name">Nome</label>' +
            '<input autocomplete="off" id="new-role-name" class="form-control" name="new-role-name" required>' +
            '</div>' +
            '<div class="form-group form-group-default required">' +
            '<label for="new-role-description">Descrizione</label>' +
            '<input autocomplete="off" id="new-role-description" class="form-control" name="new-role-description">' +
            '</div>'
        });
        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            var input = $('input#new-role-name').val(),
                description = $('input#new-role-description').val();

            if (typeof input === 'undefined' && input.length < 2) return false;
            bsModal.showLoader();
            bsModal.hideCancelBtn();
            $.ajax({
                url: "/blueseal/xhr/RbacRolesTreeController",
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

    /*
    $(document).on('bs.roles.delete', function () {
        "use strict";
        var tree = $(treeSelector).fancytree("getTree"),
            node = tree.getActiveNode();
        if (!node) {
            return false;
        }
        var bsModal = new $.bsModal('Eliminazione ruolo',
            {
                body: 'Sei Siuro di vole eliminare questo ruolo e tutti i suoi discendenti??'
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
                url: "/blueseal/xhr/RbacRolesTreeController",
                method: "delete",
                data: {
                    node: node.key,
                }
            }).done(function (res) {
                bsModal.writeBody('Ruolo eliminato');
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
    });*/
})(jQuery);