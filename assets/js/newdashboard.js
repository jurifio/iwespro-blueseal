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
        type: 'bar',
        data: {
            labels: arrayLabelOrder,
            datasets: [{
            label: 'Numero Ordini',
                data: arrayCountOrder,
                backgroundColor: [
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
                        beginAtZero:true
                    }
                }]
            }
        }
    });
    let ctxValueOrder = document.getElementById("ChartValueOrder").getContext('2d');
    let ChartValueOrder = new Chart(ctxValueOrder, {
        type: 'line',
        data: {
            labels: arrayLabelOrder,
            datasets: [{
                label: 'Valore Ordini',
                data: arrayOrder,
                backgroundColor: [
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
                        beginAtZero:true
                    }
                }]
            }
        }
    });
    //ChartQtyOrderReturn
    let ctxChartQtyOrderReturn = document.getElementById("ChartQtyOrderReturn").getContext('2d');
    let ChartQtyOrderReturn = new Chart(ctxChartQtyOrderReturn, {
        type: 'line',
        data: {
            labels: arrayLabelOrderReturn,
            datasets: [{
                label: 'Quantita Ordini Resi',
                data: arrayCountOrderReturn,
                backgroundColor: [
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
                        beginAtZero:true
                    }
                }]
            }
        }
    });

    //ChartValueOrderReturn
    let ctxChartValueOrderReturn = document.getElementById("ChartValueOrderReturn").getContext('2d');
    let ChartValueOrderReturn = new Chart(ctxChartValueOrderReturn, {
        type: 'line',
        data: {
            labels: arrayLabelOrderReturn,
            datasets: [{
                label: 'Valori Ordini Resi',
                data: arrayOrderReturn,
                backgroundColor: [
                    'rgba(255,52,41, 0.2)',
                    'rgba(52,255,33, 0.2)',
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
                    'rgba(52,255,33, 1)',
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
                        beginAtZero:true
                    }
                }]
            }
        }
    });
//ChartQtyCart
    let ctxChartQtyCart = document.getElementById("ChartQtyCart").getContext('2d');
    let ChartQtyCart = new Chart(ctxChartQtyCart, {
        type: 'line',
        data: {
            labels: arrayLabelCartTotalNumber,
            datasets: [{
                label: 'Quantita Carrelli',
                data: arrayCartTotalNumber,
                backgroundColor: [
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
                        beginAtZero:true
                    }
                }]
            }
        }
    });
    //ChartQtyCartAbandoned
    let ctxChartQtyCartAbandoned = document.getElementById("ChartQtyCartAbandoned").getContext('2d');
    let ChartQtyCartAbandoned = new Chart(ctxChartQtyCartAbandoned, {
        type: 'line',
        data: {
            labels: arrayLabelCartAbandonedTotalNumber,
            datasets: [{
                label: 'Quantita Carrelli Abbandonati',
                data: arrayCartAbandonedTotalNumber,
                backgroundColor: [
                    'rgba(36,120,255, 1)',
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
                    'rgba(36,120,255, 1)',
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
    //ChartQtyUser
    let ctxChartQtyUser = document.getElementById("ChartQtyUser").getContext('2d');
    let ChartQtyUser = new Chart(ctxChartQtyUser, {
        type: 'line',
        data: {
            labels: arrayLabelTotalUser,
            datasets: [{
                label: 'Totale Utenti  Registrati',
                data: arrayTotalUser,
                backgroundColor: [
                    'rgba(158,23,255, 1)',
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
    let ctxChartQtyUserOnLine = document.getElementById("ChartQtyUserOnLine").getContext('2d');
    let ChartQtyUserOnLine = new Chart(ctxChartQtyUserOnLine, {
        type: 'line',
        data: {
            labels: arrayLabelTotalUserOnLine,
            datasets: [{
                label: 'Totale Utenti  Online',
                data: arrayTotalUserOnLine,
                backgroundColor: [
                    'rgba(254,199,255, 0.2)',
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
                    'rgba(254,199,255, 0.2)',
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
    $('#currentDay').click(function () {
        var typePeriod='day';
        window.location.href = "/blueseal/newdashboard?typePeriod="+typePeriod;

    });
    $('#currentWeek').click(function () {
        var typePeriod='week';
        window.location.href = "/blueseal/newdashboard?typePeriod="+typePeriod;

    });
    $('#currentMonth').click(function () {
        var typePeriod='month';
        window.location.href = "/blueseal/newdashboard?typePeriod="+typePeriod;

    });
    $('#currentYear').click(function () {
        var typePeriod='year';
        window.location.href = "/blueseal/newdashboard?typePeriod="+typePeriod;

    });
    $('#btnsearchplus').click(function () {
        var typePeriod='custom';
        var startDateWork=$('#startDateWork').val()
        var endDateWork=$('#endDateWork').val()
        window.location.href = "/blueseal/newdashboard?typePeriod="+typePeriod+'&startDateWork='+startDateWork+'&endDateWork='+endDateWork;

    });

})(jQuery);

