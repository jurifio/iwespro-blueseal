function openShipmentDetail(invoice){
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
            bodyListForm += '<table id="tableShipmentDetailRowList"><tr class="header4"><th align="center" style="width:20%;">Spedizione N.</th><th align="center" style="width:20%;">Shop</th><th  align="center" style="width:20%;">Imponibile</th><th align="center"  style="width:20%;">iva</th><th align="center" style="width:20%;">Totale</th></tr>';
            $.each(rawShipmentDetail, function (k, v) {


                shop = v.shop;
                imp=v.imp;
                iva=v.iva;
                tot=v.totFat;
                shipmentId=v.shipmentId;

                        bodyListForm += '<tr><td align="center">' + shipmentId + '</td><td align="center">' + shop + '</td><td align="center">' + imp + '</td>';
                        bodyListForm += '<td align="center">' + iva + '</td><td align="center">' + tot + '</td></tr>';


                // $('#rawBrands').append('<option value="'+v.id+'-'+v.shopIdOrigin+'">'+v.brandName+'-'+v.shopName+'</option>');
            });
            bodyListForm += '</table><div id="editContractDetailDiv"></div><div id="addPaymentDiv" class="hide"></div><div id="addProductDiv" class="hide"></div>';



        let bsModalShipmentDetail = new $.bsModal('Dettaglio  Fattura Carrier Numero ' + invoice, {
            body: bodyListForm
        });
        bsModalShipmentDetail.showCancelBtn();



    });


}