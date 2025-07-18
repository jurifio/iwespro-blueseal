


$('#btnsearchplus').click(function(){
    var dateStart='datestart='+$('#dateStart').val();
    var dateEnd='&dateend='+$('#dateEnd').val();
    var season='&season=0';
    if ($('#season').prop("checked")) {
        season = '&season=1';
    }else{
        season = '&season=0';
    }

   var productZeroQuantity='';
    if($('#productZeroQuantity').prop('checked')) {
        productZeroQuantity = '&productZeroQuantity=1';
    }else{
        productZeroQuantity = '&productZeroQuantity=0';
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
    }else{
        shop='&shopid=0';
    }
    var stored='&stored=0';
    if ($('#stored').prop("checked")) {
        stored = '&stored=1';
    }else{
        stored = '&stored=0';
    }




    var url='/blueseal/lista-prodotti-venduti?'+dateStart+dateEnd+season+productZeroQuantity+productStatus+productBrand+shop+stored;

    window.location.href=url;
});

(function ($) {
    var arrayLabelShop = $('#arrayLabelShop').val().split(",");
    var arrayQtyShop = $('#arrayQtyShop').val().split(",");
    var arrayValueShop = $('#arrayValueShop').val().split(",");
    var arrayLabelShopDay = $('#arrayLabelShopDay').val().split(",");
    var arrayQtyShopDay = $('#arrayQtyShopDay').val().split(",");
    var arrayValueShopDay = $('#arrayValueShopDay').val().split(",");
    var arrayLabelShopMonth = $('#arrayLabelShopMonth').val().split(",");
    var arrayQtyShopMonth = $('#arrayQtyShopMonth').val().split(",");
    var arrayValueShopMonth = $('#arrayValueShopMonth').val().split(",");
    var arrayLabelBrand = $('#arrayLabelBrand').val().split(",");
    var arrayQtyBrand = $('#arrayQtyBrand').val().split(",");
    var arrayValueBrand = $('#arrayValueBrand').val().split(",");
    let ctxChartShop = document.getElementById("ChartShopMonth").getContext('2d');
    let ChartShopMonth = new Chart(ctxChartShop,
        {
        type: 'bar',
        data: {
            labels: arrayLabelShop,
            datasets: [{
                label: 'Quantità',
                data: arrayQtyShop,
                backgroundColor: [
                    'rgba(158,23,255, 0.2)',
                    'rgba(255,52,41, 0.2)',
                    'rgba(237,255,43, 0.2)',
                    'rgba(130,255,153, 0.2)',
                    'rgba(36,120,255, 0.2)',
                    'rgba(255,46,133, 0.2)',
                    'rgba(255,39,28, 0.2)',
                    'rgba(210,255,173, 0.2)',
                    'rgba(28,255,229, 0.2)',
                    'rgba(158,23,255, 0.2)',
                    'rgba(254,199,255, 0.2)',
                    'rgba(219,255,226, 0.2)',
                    'rgba(255,0,0, 0.2)',
                    'rgba(249,255,128, 0.2)',
                    'rgba(10,255,31, 0.2)',
                    'rgba(52,255,33, 0.2)',
                    'rgba(255,52,41, 0.2)',
                    'rgba(237,255,43, 0.2)',
                    'rgba(130,255,153, 0.2)',
                    'rgba(36,120,255, 0.2)',
                    'rgba(255,46,133, 0.2)',
                    'rgba(255,39,28, 0.2)',
                    'rgba(210,255,173, 0.2)',
                    'rgba(28,255,229, 0.2)',
                    'rgba(158,23,255, 0.2)',
                    'rgba(254,199,255, 0.2)',
                    'rgba(219,255,226, 0.2)',
                    'rgba(255,0,0, 0.2)',
                    'rgba(249,255,128, 0.2)',
                    'rgba(10,255,31, 0.2)',
                    'rgba(52,255,33, 0.2)'

                ],
                borderColor: [
                    'rgba(158,23,255, 1)',
                    'rgba(255,52,41, 1)',
                    'rgba(237,255,43, 1)',
                    'rgba(130,255,153, 1)',
                    'rgba(36,120,255, 1)',
                    'rgba(255,46,133, 1)',
                    'rgba(255,39,28, 1)',
                    'rgba(210,255,173, 1)',
                    'rgba(28,255,229, 1)',
                    'rgba(158,23,255, 1)',
                    'rgba(254,199,255, 1)',
                    'rgba(219,255,226, 1)',
                    'rgba(255,0,0, 1)',
                    'rgba(249,255,128, 1)',
                    'rgba(10,255,31, 1)',
                    'rgba(52,255,33, 1)',
                    'rgba(255,52,41, 1)',
                    'rgba(237,255,43, 1)',
                    'rgba(130,255,153, 1)',
                    'rgba(36,120,255, 1)',
                    'rgba(255,46,133, 1)',
                    'rgba(255,39,28, 1)',
                    'rgba(210,255,173, 1)',
                    'rgba(28,255,229, 1)',
                    'rgba(158,23,255, 1)',
                    'rgba(254,199,255, 1)',
                    'rgba(219,255,226, 1)',
                    'rgba(255,0,0, 1)',
                    'rgba(249,255,128, 1)',
                    'rgba(10,255,31, 1)',
                    'rgba(52,255,33, 1)'
                ],
                borderWidth: 1
            },
                {
                    label: 'Totale €',
                    data: arrayValueShop,
                    backgroundColor: [
                        'rgba(255,52,41, 0.2)',
                        'rgba(158,23,255, 0.2)',
                        'rgba(237,255,43, 0.2)',
                        'rgba(130,255,153, 0.2)',
                        'rgba(36,120,255, 0.2)',
                        'rgba(255,46,133, 0.2)',
                        'rgba(255,39,28, 0.2)',
                        'rgba(210,255,173, 0.2)',
                        'rgba(28,255,229, 0.2)',
                        'rgba(158,23,255, 0.2)',
                        'rgba(254,199,255, 0.2)',
                        'rgba(219,255,226, 0.2)',
                        'rgba(255,0,0, 0.2)',
                        'rgba(249,255,128, 0.2)',
                        'rgba(10,255,31, 0.2)',
                        'rgba(52,255,33, 0.2)',
                        'rgba(255,52,41, 0.2)',
                        'rgba(237,255,43, 0.2)',
                        'rgba(130,255,153, 0.2)',
                        'rgba(36,120,255, 0.2)',
                        'rgba(255,46,133, 0.2)',
                        'rgba(255,39,28, 0.2)',
                        'rgba(210,255,173, 0.2)',
                        'rgba(28,255,229, 0.2)',
                        'rgba(158,23,255, 0.2)',
                        'rgba(254,199,255, 0.2)',
                        'rgba(219,255,226, 0.2)',
                        'rgba(255,0,0, 0.2)',
                        'rgba(249,255,128, 0.2)',
                        'rgba(10,255,31, 0.2)',
                        'rgba(52,255,33, 0.2)'

                    ],
                    borderColor: [
                        'rgba(255,52,41, 1)',
                        'rgba(158,23,255, 1)',
                        'rgba(237,255,43, 1)',
                        'rgba(130,255,153, 1)',
                        'rgba(36,120,255, 1)',
                        'rgba(255,46,133, 1)',
                        'rgba(255,39,28, 1)',
                        'rgba(210,255,173, 1)',
                        'rgba(28,255,229, 1)',
                        'rgba(158,23,255, 1)',
                        'rgba(254,199,255, 1)',
                        'rgba(219,255,226, 1)',
                        'rgba(255,0,0, 1)',
                        'rgba(249,255,128, 1)',
                        'rgba(10,255,31, 1)',
                        'rgba(52,255,33, 1)',
                        'rgba(255,52,41, 1)',
                        'rgba(237,255,43, 1)',
                        'rgba(130,255,153, 1)',
                        'rgba(36,120,255, 1)',
                        'rgba(255,46,133, 1)',
                        'rgba(255,39,28, 1)',
                        'rgba(210,255,173, 1)',
                        'rgba(28,255,229, 1)',
                        'rgba(158,23,255, 1)',
                        'rgba(254,199,255, 1)',
                        'rgba(219,255,226, 1)',
                        'rgba(255,0,0, 1)',
                        'rgba(249,255,128, 1)',
                        'rgba(10,255,31, 1)',
                        'rgba(52,255,33, 1)'
                    ],
                    borderWidth: 1
                }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });
    let ctxChartBrand = document.getElementById("ChartBrandMonth").getContext('2d');
    let ChartBrandMonth = new Chart(ctxChartBrand, {
        type: 'bar',
        data: {
            labels: arrayLabelBrand,
            datasets: [{
                label: 'Quantità',
                data: arrayQtyBrand,
                backgroundColor: [
                    'rgba(158,23,255, 0.2)',
                    'rgba(255,52,41, 0.2)',
                    'rgba(237,255,43, 0.2)',
                    'rgba(130,255,153, 0.2)',
                    'rgba(36,120,255, 0.2)',
                    'rgba(255,46,133, 0.2)',
                    'rgba(255,39,28, 0.2)',
                    'rgba(210,255,173, 0.2)',
                    'rgba(28,255,229, 0.2)',
                    'rgba(158,23,255, 0.2)',
                    'rgba(254,199,255, 0.2)',
                    'rgba(219,255,226, 0.2)',
                    'rgba(255,0,0, 0.2)',
                    'rgba(249,255,128, 0.2)',
                    'rgba(10,255,31, 0.2)',
                    'rgba(52,255,33, 0.2)',
                    'rgba(255,52,41, 0.2)',
                    'rgba(237,255,43, 0.2)',
                    'rgba(130,255,153, 0.2)',
                    'rgba(36,120,255, 0.2)',
                    'rgba(255,46,133, 0.2)',
                    'rgba(255,39,28, 0.2)',
                    'rgba(210,255,173, 0.2)',
                    'rgba(28,255,229, 0.2)',
                    'rgba(158,23,255, 0.2)',
                    'rgba(254,199,255, 0.2)',
                    'rgba(219,255,226, 0.2)',
                    'rgba(255,0,0, 0.2)',
                    'rgba(249,255,128, 0.2)',
                    'rgba(10,255,31, 0.2)',
                    'rgba(52,255,33, 0.2)'

                ],
                borderColor: [
                    'rgba(158,23,255, 1)',
                    'rgba(255,52,41, 1)',
                    'rgba(237,255,43, 1)',
                    'rgba(130,255,153, 1)',
                    'rgba(36,120,255, 1)',
                    'rgba(255,46,133, 1)',
                    'rgba(255,39,28, 1)',
                    'rgba(210,255,173, 1)',
                    'rgba(28,255,229, 1)',
                    'rgba(158,23,255, 1)',
                    'rgba(254,199,255, 1)',
                    'rgba(219,255,226, 1)',
                    'rgba(255,0,0, 1)',
                    'rgba(249,255,128, 1)',
                    'rgba(10,255,31, 1)',
                    'rgba(52,255,33, 1)',
                    'rgba(255,52,41, 1)',
                    'rgba(237,255,43, 1)',
                    'rgba(130,255,153, 1)',
                    'rgba(36,120,255, 1)',
                    'rgba(255,46,133, 1)',
                    'rgba(255,39,28, 1)',
                    'rgba(210,255,173, 1)',
                    'rgba(28,255,229, 1)',
                    'rgba(158,23,255, 1)',
                    'rgba(254,199,255, 1)',
                    'rgba(219,255,226, 1)',
                    'rgba(255,0,0, 1)',
                    'rgba(249,255,128, 1)',
                    'rgba(10,255,31, 1)',
                    'rgba(52,255,33, 1)'
                ],
                borderWidth: 1
            },
                {
                    label: 'Totale €',
                    data: arrayValueBrand,
                    backgroundColor: [
                        'rgba(255,52,41, 0.2)',
                        'rgba(158,23,255, 0.2)',
                        'rgba(237,255,43, 0.2)',
                        'rgba(130,255,153, 0.2)',
                        'rgba(36,120,255, 0.2)',
                        'rgba(255,46,133, 0.2)',
                        'rgba(255,39,28, 0.2)',
                        'rgba(210,255,173, 0.2)',
                        'rgba(28,255,229, 0.2)',
                        'rgba(158,23,255, 0.2)',
                        'rgba(254,199,255, 0.2)',
                        'rgba(219,255,226, 0.2)',
                        'rgba(255,0,0, 0.2)',
                        'rgba(249,255,128, 0.2)',
                        'rgba(10,255,31, 0.2)',
                        'rgba(52,255,33, 0.2)',
                        'rgba(255,52,41, 0.2)',
                        'rgba(237,255,43, 0.2)',
                        'rgba(130,255,153, 0.2)',
                        'rgba(36,120,255, 0.2)',
                        'rgba(255,46,133, 0.2)',
                        'rgba(255,39,28, 0.2)',
                        'rgba(210,255,173, 0.2)',
                        'rgba(28,255,229, 0.2)',
                        'rgba(158,23,255, 0.2)',
                        'rgba(254,199,255, 0.2)',
                        'rgba(219,255,226, 0.2)',
                        'rgba(255,0,0, 0.2)',
                        'rgba(249,255,128, 0.2)',
                        'rgba(10,255,31, 0.2)',
                        'rgba(52,255,33, 0.2)'

                    ],
                    borderColor: [
                        'rgba(255,52,41, 1)',
                        'rgba(158,23,255, 1)',
                        'rgba(237,255,43, 1)',
                        'rgba(130,255,153, 1)',
                        'rgba(36,120,255, 1)',
                        'rgba(255,46,133, 1)',
                        'rgba(255,39,28, 1)',
                        'rgba(210,255,173, 1)',
                        'rgba(28,255,229, 1)',
                        'rgba(158,23,255, 1)',
                        'rgba(254,199,255, 1)',
                        'rgba(219,255,226, 1)',
                        'rgba(255,0,0, 1)',
                        'rgba(249,255,128, 1)',
                        'rgba(10,255,31, 1)',
                        'rgba(52,255,33, 1)',
                        'rgba(255,52,41, 1)',
                        'rgba(237,255,43, 1)',
                        'rgba(130,255,153, 1)',
                        'rgba(36,120,255, 1)',
                        'rgba(255,46,133, 1)',
                        'rgba(255,39,28, 1)',
                        'rgba(210,255,173, 1)',
                        'rgba(28,255,229, 1)',
                        'rgba(158,23,255, 1)',
                        'rgba(254,199,255, 1)',
                        'rgba(219,255,226, 1)',
                        'rgba(255,0,0, 1)',
                        'rgba(249,255,128, 1)',
                        'rgba(10,255,31, 1)',
                        'rgba(52,255,33, 1)'
                    ],
                    borderWidth: 1
                }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });
    let ctxChartShopDay = document.getElementById("ChartShopDay").getContext('2d');
    let ChartShopDay = new Chart(ctxChartShopDay,
        {
            type: 'bar',
            data: {
                labels: arrayLabelShopDay,
                datasets: [{
                    label: 'Quantità total giorno',
                    data: arrayQtyShopDay,
                    backgroundColor: [
                        'rgba(158,23,255, 0.2)',
                        'rgba(255,52,41, 0.2)',
                        'rgba(237,255,43, 0.2)',
                        'rgba(130,255,153, 0.2)',
                        'rgba(36,120,255, 0.2)',
                        'rgba(255,46,133, 0.2)',
                        'rgba(255,39,28, 0.2)',
                        'rgba(210,255,173, 0.2)',
                        'rgba(28,255,229, 0.2)',
                        'rgba(158,23,255, 0.2)',
                        'rgba(254,199,255, 0.2)',
                        'rgba(219,255,226, 0.2)',
                        'rgba(255,0,0, 0.2)',
                        'rgba(249,255,128, 0.2)',
                        'rgba(10,255,31, 0.2)',
                        'rgba(52,255,33, 0.2)',
                        'rgba(255,52,41, 0.2)',
                        'rgba(237,255,43, 0.2)',
                        'rgba(130,255,153, 0.2)',
                        'rgba(36,120,255, 0.2)',
                        'rgba(255,46,133, 0.2)',
                        'rgba(255,39,28, 0.2)',
                        'rgba(210,255,173, 0.2)',
                        'rgba(28,255,229, 0.2)',
                        'rgba(158,23,255, 0.2)',
                        'rgba(254,199,255, 0.2)',
                        'rgba(219,255,226, 0.2)',
                        'rgba(255,0,0, 0.2)',
                        'rgba(249,255,128, 0.2)',
                        'rgba(10,255,31, 0.2)',
                        'rgba(52,255,33, 0.2)'

                    ],
                    borderColor: [
                        'rgba(158,23,255, 1)',
                        'rgba(255,52,41, 1)',
                        'rgba(237,255,43, 1)',
                        'rgba(130,255,153, 1)',
                        'rgba(36,120,255, 1)',
                        'rgba(255,46,133, 1)',
                        'rgba(255,39,28, 1)',
                        'rgba(210,255,173, 1)',
                        'rgba(28,255,229, 1)',
                        'rgba(158,23,255, 1)',
                        'rgba(254,199,255, 1)',
                        'rgba(219,255,226, 1)',
                        'rgba(255,0,0, 1)',
                        'rgba(249,255,128, 1)',
                        'rgba(10,255,31, 1)',
                        'rgba(52,255,33, 1)',
                        'rgba(255,52,41, 1)',
                        'rgba(237,255,43, 1)',
                        'rgba(130,255,153, 1)',
                        'rgba(36,120,255, 1)',
                        'rgba(255,46,133, 1)',
                        'rgba(255,39,28, 1)',
                        'rgba(210,255,173, 1)',
                        'rgba(28,255,229, 1)',
                        'rgba(158,23,255, 1)',
                        'rgba(254,199,255, 1)',
                        'rgba(219,255,226, 1)',
                        'rgba(255,0,0, 1)',
                        'rgba(249,255,128, 1)',
                        'rgba(10,255,31, 1)',
                        'rgba(52,255,33, 1)'
                    ],
                    borderWidth: 1
                },
                    {
                        label: 'Totale giorno €',
                        data: arrayValueShopDay,
                        backgroundColor: [
                            'rgba(255,52,41, 0.2)',
                            'rgba(158,23,255, 0.2)',
                            'rgba(237,255,43, 0.2)',
                            'rgba(130,255,153, 0.2)',
                            'rgba(36,120,255, 0.2)',
                            'rgba(255,46,133, 0.2)',
                            'rgba(255,39,28, 0.2)',
                            'rgba(210,255,173, 0.2)',
                            'rgba(28,255,229, 0.2)',
                            'rgba(158,23,255, 0.2)',
                            'rgba(254,199,255, 0.2)',
                            'rgba(219,255,226, 0.2)',
                            'rgba(255,0,0, 0.2)',
                            'rgba(249,255,128, 0.2)',
                            'rgba(10,255,31, 0.2)',
                            'rgba(52,255,33, 0.2)',
                            'rgba(255,52,41, 0.2)',
                            'rgba(237,255,43, 0.2)',
                            'rgba(130,255,153, 0.2)',
                            'rgba(36,120,255, 0.2)',
                            'rgba(255,46,133, 0.2)',
                            'rgba(255,39,28, 0.2)',
                            'rgba(210,255,173, 0.2)',
                            'rgba(28,255,229, 0.2)',
                            'rgba(158,23,255, 0.2)',
                            'rgba(254,199,255, 0.2)',
                            'rgba(219,255,226, 0.2)',
                            'rgba(255,0,0, 0.2)',
                            'rgba(249,255,128, 0.2)',
                            'rgba(10,255,31, 0.2)',
                            'rgba(52,255,33, 0.2)'

                        ],
                        borderColor: [
                            'rgba(255,52,41, 1)',
                            'rgba(158,23,255, 1)',
                            'rgba(237,255,43, 1)',
                            'rgba(130,255,153, 1)',
                            'rgba(36,120,255, 1)',
                            'rgba(255,46,133, 1)',
                            'rgba(255,39,28, 1)',
                            'rgba(210,255,173, 1)',
                            'rgba(28,255,229, 1)',
                            'rgba(158,23,255, 1)',
                            'rgba(254,199,255, 1)',
                            'rgba(219,255,226, 1)',
                            'rgba(255,0,0, 1)',
                            'rgba(249,255,128, 1)',
                            'rgba(10,255,31, 1)',
                            'rgba(52,255,33, 1)',
                            'rgba(255,52,41, 1)',
                            'rgba(237,255,43, 1)',
                            'rgba(130,255,153, 1)',
                            'rgba(36,120,255, 1)',
                            'rgba(255,46,133, 1)',
                            'rgba(255,39,28, 1)',
                            'rgba(210,255,173, 1)',
                            'rgba(28,255,229, 1)',
                            'rgba(158,23,255, 1)',
                            'rgba(254,199,255, 1)',
                            'rgba(219,255,226, 1)',
                            'rgba(255,0,0, 1)',
                            'rgba(249,255,128, 1)',
                            'rgba(10,255,31, 1)',
                            'rgba(52,255,33, 1)'
                        ],
                        borderWidth: 1
                    }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    let ctxChartShopYear = document.getElementById("ChartShopYear").getContext('2d');
    let ChartShopYear = new Chart(ctxChartShopYear,
        {
            type: 'bar',
            data: {
                labels: arrayLabelShopMonth,
                datasets: [{
                    label: 'Quantità totale mese',
                    data: arrayQtyShopMonth,
                    backgroundColor: [
                        'rgba(158,23,255, 0.2)',
                        'rgba(255,52,41, 0.2)',
                        'rgba(237,255,43, 0.2)',
                        'rgba(130,255,153, 0.2)',
                        'rgba(36,120,255, 0.2)',
                        'rgba(255,46,133, 0.2)',
                        'rgba(255,39,28, 0.2)',
                        'rgba(210,255,173, 0.2)',
                        'rgba(28,255,229, 0.2)',
                        'rgba(158,23,255, 0.2)',
                        'rgba(254,199,255, 0.2)',
                        'rgba(219,255,226, 0.2)',
                        'rgba(255,0,0, 0.2)',
                        'rgba(249,255,128, 0.2)',
                        'rgba(10,255,31, 0.2)',
                        'rgba(52,255,33, 0.2)',
                        'rgba(255,52,41, 0.2)',
                        'rgba(237,255,43, 0.2)',
                        'rgba(130,255,153, 0.2)',
                        'rgba(36,120,255, 0.2)',
                        'rgba(255,46,133, 0.2)',
                        'rgba(255,39,28, 0.2)',
                        'rgba(210,255,173, 0.2)',
                        'rgba(28,255,229, 0.2)',
                        'rgba(158,23,255, 0.2)',
                        'rgba(254,199,255, 0.2)',
                        'rgba(219,255,226, 0.2)',
                        'rgba(255,0,0, 0.2)',
                        'rgba(249,255,128, 0.2)',
                        'rgba(10,255,31, 0.2)',
                        'rgba(52,255,33, 0.2)'

                    ],
                    borderColor: [
                        'rgba(158,23,255, 1)',
                        'rgba(255,52,41, 1)',
                        'rgba(237,255,43, 1)',
                        'rgba(130,255,153, 1)',
                        'rgba(36,120,255, 1)',
                        'rgba(255,46,133, 1)',
                        'rgba(255,39,28, 1)',
                        'rgba(210,255,173, 1)',
                        'rgba(28,255,229, 1)',
                        'rgba(158,23,255, 1)',
                        'rgba(254,199,255, 1)',
                        'rgba(219,255,226, 1)',
                        'rgba(255,0,0, 1)',
                        'rgba(249,255,128, 1)',
                        'rgba(10,255,31, 1)',
                        'rgba(52,255,33, 1)',
                        'rgba(255,52,41, 1)',
                        'rgba(237,255,43, 1)',
                        'rgba(130,255,153, 1)',
                        'rgba(36,120,255, 1)',
                        'rgba(255,46,133, 1)',
                        'rgba(255,39,28, 1)',
                        'rgba(210,255,173, 1)',
                        'rgba(28,255,229, 1)',
                        'rgba(158,23,255, 1)',
                        'rgba(254,199,255, 1)',
                        'rgba(219,255,226, 1)',
                        'rgba(255,0,0, 1)',
                        'rgba(249,255,128, 1)',
                        'rgba(10,255,31, 1)',
                        'rgba(52,255,33, 1)'
                    ],
                    borderWidth: 1
                },
                    {
                        label: 'Totale Mese €',
                        data: arrayValueShopMonth,
                        backgroundColor: [
                            'rgba(255,52,41, 0.2)',
                            'rgba(158,23,255, 0.2)',
                            'rgba(237,255,43, 0.2)',
                            'rgba(130,255,153, 0.2)',
                            'rgba(36,120,255, 0.2)',
                            'rgba(255,46,133, 0.2)',
                            'rgba(255,39,28, 0.2)',
                            'rgba(210,255,173, 0.2)',
                            'rgba(28,255,229, 0.2)',
                            'rgba(158,23,255, 0.2)',
                            'rgba(254,199,255, 0.2)',
                            'rgba(219,255,226, 0.2)',
                            'rgba(255,0,0, 0.2)',
                            'rgba(249,255,128, 0.2)',
                            'rgba(10,255,31, 0.2)',
                            'rgba(52,255,33, 0.2)',
                            'rgba(255,52,41, 0.2)',
                            'rgba(237,255,43, 0.2)',
                            'rgba(130,255,153, 0.2)',
                            'rgba(36,120,255, 0.2)',
                            'rgba(255,46,133, 0.2)',
                            'rgba(255,39,28, 0.2)',
                            'rgba(210,255,173, 0.2)',
                            'rgba(28,255,229, 0.2)',
                            'rgba(158,23,255, 0.2)',
                            'rgba(254,199,255, 0.2)',
                            'rgba(219,255,226, 0.2)',
                            'rgba(255,0,0, 0.2)',
                            'rgba(249,255,128, 0.2)',
                            'rgba(10,255,31, 0.2)',
                            'rgba(52,255,33, 0.2)'

                        ],
                        borderColor: [
                            'rgba(255,52,41, 1)',
                            'rgba(158,23,255, 1)',
                            'rgba(237,255,43, 1)',
                            'rgba(130,255,153, 1)',
                            'rgba(36,120,255, 1)',
                            'rgba(255,46,133, 1)',
                            'rgba(255,39,28, 1)',
                            'rgba(210,255,173, 1)',
                            'rgba(28,255,229, 1)',
                            'rgba(158,23,255, 1)',
                            'rgba(254,199,255, 1)',
                            'rgba(219,255,226, 1)',
                            'rgba(255,0,0, 1)',
                            'rgba(249,255,128, 1)',
                            'rgba(10,255,31, 1)',
                            'rgba(52,255,33, 1)',
                            'rgba(255,52,41, 1)',
                            'rgba(237,255,43, 1)',
                            'rgba(130,255,153, 1)',
                            'rgba(36,120,255, 1)',
                            'rgba(255,46,133, 1)',
                            'rgba(255,39,28, 1)',
                            'rgba(210,255,173, 1)',
                            'rgba(28,255,229, 1)',
                            'rgba(158,23,255, 1)',
                            'rgba(254,199,255, 1)',
                            'rgba(219,255,226, 1)',
                            'rgba(255,0,0, 1)',
                            'rgba(249,255,128, 1)',
                            'rgba(10,255,31, 1)',
                            'rgba(52,255,33, 1)'
                        ],
                        borderWidth: 1
                    }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });





})(jQuery);