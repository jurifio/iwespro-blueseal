window.buttonSetup = {
    tag:"a",
    icon:"fa-tag",
    permission:"/admin/order/list&&allShops",
    event:"bs-print-coupon",
    class:"btn btn-default",
    rel:"tooltip",
    title:"stampa il Coupon Post Vendita",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-print-coupon', function () {

    let order = (new URL(document.location)).searchParams.get("order");

    if (order == null){
        let selectedRows = $('.table').DataTable().rows('.selected').data();

        if(selectedRows.length != 1) {
            new Alert({
                type: "warning",
                message: "Seleziona una riga"
            }).open();
            return false;
        }

     var  orderId = selectedRows[0].DT_RowId;
        var couponGenerateId=selectedRows[0].couponGenerateId;
    }
    var body='';
    if(couponGenerateId!=null) {
        body='Visualizza e stampa il Coupon Post Vendita';
    }else{
        body='non Puoi Stampare il Coupon in quanto l\'ordine non è stato ancora accettato';
    }
    
    let bsModal = new $.bsModal('Stampa Il Coupon', {
        body:body
    });

    bsModal.showCancelBtn();
    if(couponGenerateId!=null) {
        bsModal.setOkEvent(function () {

            let extUrl = `/blueseal/xhr/CouponPostSellingOnlyPrintAjaxController?orderId=`+orderId;
            window.open(extUrl, "_blank");


            bsModal.showCancelBtn();
            bsModal.setOkEvent(function () {
                bsModal.hide();
            });
        });
    }
});