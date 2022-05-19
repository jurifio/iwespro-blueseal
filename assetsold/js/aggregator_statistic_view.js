(function ($) {
    const treeSelector = "#categoryTree";
    var categoryProduct='';
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
            checkbox: true,
            icon:false,
            selectMode: 3,
            source: {
                url: "/blueseal/xhr/CategoryTreeController"
            },
            activate: function (event, data) {
                $("#echoActive1").text(data.node.key);

                var selKeys = $.map(data.tree.getSelectedNodes(), function(node){
                    return node.key;
                });
                $("#echoSelection1").text(selKeys.join(", "));
                categoryProduct=selKeys.join(", ");
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
                }
            }
        });
    });

    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'MarketplaceAccount'

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#marketplaceAccount');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
        });
    });







})(jQuery);
$(function() {
    $('input[name="daterange"]').daterangepicker({
        timePicker: true,
        startDate: moment().startOf('hour'),
        endDate: moment().startOf('hour').add(32, 'hour'),
        locale: {
            format: 'DD-MM-YYYY',
            cancelLabel: "Cancella",
            applyLabel: "Applica"
        },
        ranges: {
            'Oggi': [moment(), moment()],
            'Ieri': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Ultimi 7 Giorni': [moment().subtract(6, 'days'), moment()],
            'Ultimi 30 giorni': [moment().subtract(29, 'days'), moment()],
            'Questo Mese': [moment().startOf('month'), moment().endOf('month')],
            'Scorso Mese': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        alwaysShowCalendars: true,
        autoUpdateInput: false,
        drops: "down",
    });
});





$('#marketplaceAccount').change(function () {
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/CSelectAggregatorStatisticRenderAjaxController',
        data: {
            marketplaceAccount: $('#marketplaceAccount').val(),
        },
        dataType: 'json'
    }).done(function (res2) {
        $('#firstGraph').empty();


    });
});