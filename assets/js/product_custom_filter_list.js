(function ($) {

    let photo = 0;
    let shooting = 0;

    $(document).ready(function () {
        let radioTree = $("#categoriesTree");
        if (radioTree.length) {
            let glyph_opts = {
                preset: "bootstrap3",
                map: {
                    expanderClosed: "glyphicon glyphicon-menu-right",  // glyphicon-plus-sign
                    expanderLazy: "glyphicon glyphicon-menu-right",  // glyphicon-plus-sign
                    expanderOpen: "glyphicon glyphicon-menu-down"  // glyphicon-minus-sign
                }
            };
            radioTree.fancytree({
                extensions: ["childcounter", "glyph", "wide"],
                checkbox: true,
                activeVisible: true,
                quicksearch: true,
                childcounter: {
                    deep: true,
                    hideZeros: true,
                    hideExpanded: true
                },
                debugLevel: 0,
                selectMode: 2,
                source: {
                    url: "/blueseal/xhr/CategoryTreeController",
                    cache: true,
                    complete: function () {
                        $(document).trigger('bs.categoryTree.loaded');
                    }
                },
                glyph: glyph_opts,
                dblclick: function (event, data) {
                    cascadeSelection(data.node);
                    function cascadeSelection(node) {
                        node.setSelected(!node.isSelected());
                        $.each(node.children, function (k, v) {
                            v.setSelected(!node.isSelected());
                            cascadeSelection(v)
                        });
                    }
                }
            });
        }

        $.ajax({
            method:'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'ProductSeason'
            },
            dataType: 'json'
        }).done(function (res) {
            let select = $('#productSeason');
            if(typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: 'name',
                options: res
            });
        });


        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/ProductCustomFilterAjaxController',
        }).done(function (res) {
            let ret = JSON.parse(res);

            $.each(ret.shop, function (k, v) {
                $('#checkShop').append('<div><input type="checkbox" name="' + v + '" value="' + k + '" /> ' + v + '</div>');
            });

            $.each(ret.season, function (k, v) {
                $('#checkSeason').append('<div><input type="checkbox" name="' + v + '" value="' + k + '" /> ' + v + '</div>');
            });


        });

    });

    $('#selectAllShops').click(function(){
        $('#checkShop input:checkbox').each(function(){
            if( $(this).is(':checked') ){
                $(this).prop('checked',false);
            } else {
                $(this).prop('checked',true);
            }

        })
    });

    $('#selectAllSeason').click(function(){
        $('#checkSeason input:checkbox').each(function(){
            if( $(this).is(':checked') ){
                $(this).prop('checked',false);
            } else {
                $(this).prop('checked',true);
            }

        })
    });


    $('#search').on('click', function () {

        let ids = [];
        $.each($("#categoriesTree").fancytree('getTree').getSelectedNodes(), function (k, v) {
            ids.push(v.key);
        });

        if($('input#havePhoto').is(':checked')){
            photo = 1
        } else {
            photo = 0;
        }

        if($('input#haveShooting').is(':checked')){
            shooting = 1
        } else {
            shooting = 0;
        }

        let checkedShop = [];
        let checkedShopName = [];
        $('#checkShop input:checked').each(function(i){
            checkedShop[i] = $(this).val();
            checkedShopName[i] = $(this).attr('name');
        });

        let checkedSeason = [];
        let checkedSeasonName = [];
        $('#checkSeason input:checked').each(function(i){
            checkedSeason[i] = $(this).val();
            checkedSeasonName[i] = $(this).attr('name');
        });

        if(checkedShop.length === 0 || checkedSeason.length === 0 || ids.length === 0){
            new Alert({
                type: "warning",
                message: "Devi selezionare almeno uno shop, una stagione e una categoria"
            }).open();
            return false;
        }

        const data = {
            ids: ids,
            season: checkedSeason,
            photo: photo,
            shooting: shooting,
            shops: checkedShop
        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/ProductCustomFilterAjaxController',
            data: data
        }).done(function (res) {

            if (res === "no"){
                new Alert({
                    type: "danger",
                    message: "Hai selezionato due categorie appartenenti a profondità diverse"
                }).open();
                return false;
            }

            let count = JSON.parse(res);
            let hasPhoto = (photo === 0 ? "No" : "Si");
            let hasShooting = (shooting === 0 ? "No" : "Si");
            let sumCategory = "";
            let sum = 0;

            let shpName = checkedShopName.join(' | ');
            let seasName = checkedSeasonName.join(' | ');

            let encodedSeasons = encodeURIComponent(JSON.stringify(checkedSeason));
            let encodedShops = encodeURIComponent(JSON.stringify(checkedShop));
            let encodedCats = encodeURIComponent(JSON.stringify(ids));

            $("#tableResult tbody tr:not(:first-child)").empty();


            $.each(count, function (k, v) {
                    //preparo categoria
                    let catId = k.split('-')[2];
                    let singleCat = k.split('-')[0] + '-' + k.split('-')[1];

                    $('#tableResult tr:last').after(
                        '<tr>' +
                        '<td data-cat="' + catId + '">' + singleCat + '</td>' +
                        '<td>' + v + '</td>' +
                        '<td>' + seasName + '</td>' +
                        '<td>' + hasPhoto + '</td>' +
                        '<td>' + hasShooting + '</td>' +
                        '<td>' + shpName + '</td>' +
                        '<td>' +
                        '<a href="/blueseal/prodotti/filtri-custom/preview?catid=' + catId + '&seasons=' + encodedSeasons + '&shops=' + encodedShops + '" target="_blank">' +
                        '<button class="btn btn-dafault" disabled>Vedi i prodotti' +
                        '</button> ' +
                        '</a>' +
                        '</td>' +
                        '</tr>');
                    sumCategory += k + "<br />";
                    sum = sum + v;

            });
            $('#tableResult tr:last').after(
                    '<tr style="border-top: 2px solid black; font-weight: bold">' +
                        '<td>' + sumCategory + '</td>' +
                        '<td>' + sum + '</td>' +
                        '<td>' + seasName + '</td>' +
                        '<td>' + hasPhoto + '</td>' +
                        '<td>' + hasShooting + '</td>' +
                        '<td>' + shpName + '</td>' +
                        '<td>' +
                            '<a href="/blueseal/prodotti/filtri-custom/preview?catid=' + encodedCats +'&seasons='+ encodedSeasons + '&shops=' + encodedShops + '" target="_blank">' +
                                '<button class="btn btn-dafault" disabled>Vedi i prodotti' +
                                '</button> ' +
                            '</a>' +
                        '</td>' +
                    '</tr>');

        }).fail(function (res) {

        }).always(function (res) {

        });

    });




})(jQuery);