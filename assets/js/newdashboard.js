(function ($) {

    var arrayLabelOrder=$('#arrayLabelOrder').val().split(",");
    var arrayOrder=$('#arrayOrder').val().split(",");
    var arrayCountOrder=$('#arrayCountOrder').val().split(",");
    var arrayOrderReturn=$('#arrayOrderReturn').val().split(",");
        var arrayLabelOrderReturn=$('#arrayLabelOrderReturn').val().split(",");
        var arrayCountOrderReturn=$('#arrayCountOrderReturn').val().split(",");

        var arrayTotalUser =$('#arrayTotalUser').val().split(",");
        var arrayLabelTotalUser=$('#arrayLabelTotalUser').val().split(",");
        var arrayTotalUserOnLine=$('#arrayTotalUserOnLine').val().split(",");
        var arrayLabelTotalUserOnLine=$('#arrayLabelTotalUserOnLine').val().split(",");
        var arrayLabelCartTotalNumber=$('#arrayCountOrderReturn').val().split(",");
        var arrayLabelCartAbandonedTotalNumber=$('#arrayLabelCartAbandonedTotalNumber').val().split(",");
        var arrayCartTotalNumber=$('#arrayCartTotalNumber').val().split(",");
        var arrayCartAbandonedTotalNumber=$('#arrayCartAbandonedTotalNumber').val().split(",");



    let ctxQtyOrder = document.getElementById("ChartQtyOrder").getContext('2d');
    let ChartQtyOrder = new Chart(ctxQtyOrder, {
        type: 'line',
        data: {
            labels: arrayLabelOrder,
            datasets: [{
            label: 'Numero Ordini',
                data: arrayCountOrder,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)',
                    'rgba(186, 159, 64, 0.2)',
                    'rgba(160, 159, 64, 0.2)',
                    'rgba(50, 159, 64, 0.2)',
                    'rgba(70, 159, 64, 0.2)',
                    'rgba(80, 159, 64, 0.2)',
                    'rgba(10, 159, 64, 0.2)',
                    'rgba(11, 159, 64, 0.2)',
                    'rgba(11, 159, 64, 0.2)',
                    'rgba(11, 159, 64, 0.2)',
                    'rgba(11, 159, 64, 0.2)',
                    'rgba(11, 159, 64, 0.2)',
                    'rgba(11, 159, 64, 0.2)',
                    'rgba(11, 159, 64, 0.2)',
                    'rgba(11, 159, 64, 0.2)',
                    'rgba(11, 159, 64, 0.2)',
                    'rgba(11, 159, 64, 0.2)',
                    'rgba(11, 159, 64, 0.2)',
                    'rgba(11, 159, 64, 0.2)',
                    'rgba(11, 159, 64, 0.2)',
                    'rgba(11, 159, 64, 0.2)',
                    'rgba(11, 159, 64, 0.2)',
                    'rgba(11, 159, 64, 0.2)',
                    'rgba(11, 159, 64, 0.2)',
                    'rgba(11, 159, 64, 0.2)',
                    'rgba(11, 159, 64, 0.2)',

                ],
                borderColor: [
                    'rgba(255,99,132,1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(186, 159, 64, 1)',
                    'rgba(160, 159, 64, 1)',
                    'rgba(50, 159, 64, 1)',
                    'rgba(70, 159, 64, 1)',
                    'rgba(80, 159, 64, 1)',
                    'rgba(10, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero:true
                    }
                }]
            }
        }
    });
    let ctxValueOrder = document.getElementById("ChartValueOrder").getContext('2d');
    let ChartValueOrder = new Chart(ctxValueOrder, {
        type: 'bar',
        data: {
            labels: ['Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'],
            datasets: [{
                label: 'Valore Ordini €',
                data: [150.00, 160.00,15.00,100.00,10.00,150.00,180.00,15.00,10.00,150.00, 160.00,15.00],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)',
                    'rgba(186, 159, 64, 0.2)',
                    'rgba(160, 159, 64, 0.2)',
                    'rgba(50, 159, 64, 0.2)',
                    'rgba(70, 159, 64, 0.2)',
                    'rgba(80, 159, 64, 0.2)',
                    'rgba(10, 159, 64, 0.2)'

                ],
                borderColor: [
                    'rgba(255,99,132,1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(186, 159, 64, 1)',
                    'rgba(160, 159, 64, 1)',
                    'rgba(50, 159, 64, 1)',
                    'rgba(70, 159, 64, 1)',
                    'rgba(80, 159, 64, 1)',
                    'rgba(10, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero:true
                    }
                }]
            }
        }
    });



})(jQuery);

