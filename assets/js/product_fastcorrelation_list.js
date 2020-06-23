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
    var stored='&stored=0';
    if ($('#stored').prop("checked")) {
        stored = '&stored=1';
    }
    var productShooting='&productShooting=0';
    if ($('#productShooting').prop("checked")) {
        productShooting = '&productShooting=1';
    }
    var url='/blueseal/lista-prodotti-correlati-veloce?'+season+productZeroQuantity+productStatus+productBrand+shop+stored+productShooting;

    window.location.href=url;
});