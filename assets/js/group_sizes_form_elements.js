/* ============================================================
 * Form Elements
 * This file applies various jQuery plugins to form elements
 * For DEMO purposes only. Extract what you need.
 * ============================================================ */


var valuesStr = "";

var autocompleteSizes = function(){
    console.log('yooo');
    $.each($('input[id^="ProductSizeGroup_position_"]'),function() {
        var me = $(this);
        me.autocomplete({
            source: function(request, response) {
                if(valuesStr != "") {
                    var source = valuesStr.split(",");
                    var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( request.term ), "i" );
                    response( $.grep( source, function( item ){
                        return matcher.test( item );
                    }) );
                } else {
                var asd = $(this)[0].element[0].id;
                $.ajax({
                    type: "GET",
                    async: false,
                    url: "/blueseal/xhr/GetAutocompleteSize",
                    data: { value: asd }
                }).done(function(content) {
                    valuesStr = content;
                    var source = content.split(",");
                    var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( request.term ), "i" );
                    response( $.grep( source, function( item ){
                        return matcher.test( item );
                    }) );
                });
                }
            }
        });
    });
};

(function($) {

    'use strict';

    var getBaseURL = function() {
        var url = document.URL;
        return url.substr(0, url.lastIndexOf('/'));
    };



    var elems = Array.prototype.slice.call(document.querySelectorAll('.switchery'));
// Success color: #10CFBD
    elems.forEach(function(html) {
        var switchery = new Switchery(html, {color: '#10CFBD'});
    });

    $(document).ready(function() {

        autocompleteSizes();
        var nowTemp = new Date();
        var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

    });

})(window.jQuery);