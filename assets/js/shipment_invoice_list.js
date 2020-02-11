function openShipmentDetail(invoice,carrier){
var shipmentId='';
var shop='';
var imp='';
var iva='';
var tot='';
    $.ajax({
        url: '/blueseal/xhr/ShipmentInvoiceDetailAjaxController',
        method: 'get',
        data: {
            invoice: invoice

        },
        dataType: 'json'
    }).done(function (res) {

        console.log(res);
        let rawShipmentDetail = res;

            var bodyListForm = '';

            bodyListForm+='<div class="row"><div class="col-md-3"><b>Shop</b></div><div class="col-md-3"><b>Imponibile</b></div><div class="col-md-3"><b>iva</b></div><div class="col-md-3"><b>Totale</b></div></div>';
            $.each(rawShipmentDetail, function (k, v) {


                shop = v.shop;
                imp=v.imp;
                iva=v.iva;
                tot=v.totFat;


                        bodyListForm += '<div class="row"><div class="col-md-3">' + shop + '</div><div class="col-md-3">' + imp + '</div>';
                        bodyListForm += '<div class="col-md-3">' + iva + '</div><div class="col-md-3">' + tot + '</div></div>';


                // $('#rawBrands').append('<option value="'+v.id+'-'+v.shopIdOrigin+'">'+v.brandName+'-'+v.shopName+'</option>');
            });




        let bsModalShipmentDetail = new $.bsModal('Dettaglio  Fattura Carrier <b>'+carrier+'</b> Numero <b>' + invoice+'</b>', {
            body: bodyListForm
        });
        bsModalDetailContract.showCancelBtn();
        bsModalDetailContract.addClass('modal-wide');
        bsModalDetailContract.addClass('modal-high');



    });


}