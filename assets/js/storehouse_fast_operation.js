$(document).on('keypress', 'input#barcode',function(a,b,c,d,e,f) {

    var key = a.which;
    var target = $(a.currentTarget);
    console.log(a,key,target.val().length);

    if(target.val().length == 10) {
        //blabla
        console.log('fai le cose con',target.val());
        Pace.ignore(function () {
            $.ajax({
                url: "/blueseal/xhr/ReadStorageOperationLineFromBarcode",
                type: "GET",
                data: {
                    barcode: target.val(),
                    shop: $('#shopId').val()
                }
            }).done(function (res) {
                $('ul#linesList li').last().append(res);
            }).fail(function (res) {
                new Alert({
                    type: "danger",
                    message: res
                }).open();
                return false;
            }).always(function () {
                target.val('');
                target.focus();
            });
        });
    }


});