$('#btnsearchplus').click(function(){
    var season='season=0';
    if ($('#season').prop("checked")) {
        season='season=1';
    }
    var productZeroQuantity='&productZeroQuantity=0';
    if($('#ProductZeroQuantity').prop('checked')){
        productZeroQuantity='&productZeroQuantity=1';
    }
    var productStatus='&productStatus=0';
    if($('#productStatus').prop('checked')){
        productStatus='&productStatus=1'
    }
    var url='/blueseal/lista-prodotti-veloce?'+season+productZeroQuantity+productStatus;

    window.location.href=url;
});