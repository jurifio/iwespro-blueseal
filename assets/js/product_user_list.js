$('#btnsearchplus').click(function(){
    var season='season=0';
    if ($('#season').prop("checked")) {
        season = 'season=1';
    }
    var productZeroQuantity='&productZeroQuantity=0';
    if($('#productZeroQuantity').prop('checked')) {
        productZeroQuantity = '&productZeroQuantity=1';
    }
    var productStatus='&productStatus=0';
    if($('#productStatus').prop('checked')) {
        productStatus = '&productStatus=1';
    }
    var  productBrand='&productBrandId=0';
    if($('#productBrandId').val()!=0) {
        productBrand = '&productBrandId='+$('#productBrandId').val();
    }
    var  shop='&shopid=0';
    if($('#shopid').val()!=0) {
        shop = '&shopid='+$('#shopid').val();
    }
    var url='/blueseal/lista-prodotti-veloce?'+season+productZeroQuantity+productStatus+productBrand+shop;

    window.location.href=url;
});