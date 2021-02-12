(function ($) {

    var arrayLabelOrder = $('#arrayLabelOrder').val().split(",");
    var arrayOrder = $('#arrayOrder').val().split(",");
    var arrayCountOrder = $('#arrayCountOrder').val().split(",");
    var arrayOrderReturn = $('#arrayOrderReturn').val().split(",");
    var arrayLabelOrderReturn = $('#arrayLabelOrderReturn').val().split(",");
    var arrayCountOrderReturn = $('#arrayCountOrderReturn').val().split(",");
    var arrayTotalUser = $('#arrayTotalUser').val().split(",");
    var arrayLabelTotalUser = $('#arrayLabelTotalUser').val().split(",");
    var arrayTotalUserOnLine = $('#arrayTotalUserOnLine').val().split(",");
    var arrayLabelTotalUserOnLine = $('#arrayLabelTotalUserOnLine').val().split(",");
    var arrayLabelCartTotalNumber = $('#arrayCountOrderReturn').val().split(",");
    var arrayLabelCartAbandonedTotalNumber = $('#arrayLabelCartAbandonedTotalNumber').val().split(",");
    var arrayCartTotalNumber = $('#arrayCartTotalNumber').val().split(",");
    var arrayCartAbandonedTotalNumber = $('#arrayCartAbandonedTotalNumber').val().split(",");
    var arrayLabelOrderCompare = $('#arrayLabelOrderCompare').val().split(",");
    var arrayOrderCompare = $('#arrayOrderCompare').val().split(",");
    var arrayCountOrderCompare = $('#arrayCountOrderCompare').val().split(",");
    var arrayOrderReturnCompare = $('#arrayOrderReturnCompare').val().split(",");
    var arrayLabelOrderReturnCompare = $('#arrayLabelOrderReturnCompare').val().split(",");
    var arrayCountOrderReturnCompare = $('#arrayCountOrderReturnCompare').val().split(",");

    var arrayTotalUserCompare = $('#arrayTotalUserCompare').val().split(",");
    var arrayLabelTotalUserCompare = $('#arrayLabelTotalUserCompare').val().split(",");
    var arrayTotalUserOnLineCompare = $('#arrayTotalUserOnLineCompare').val().split(",");
    var arrayLabelTotalUserOnLineCompare = $('#arrayLabelTotalUserOnLineCompare').val().split(",");
    var arrayLabelCartTotalNumberCompare = $('#arrayCountOrderReturnCompare').val().split(",");
    var arrayLabelCartAbandonedTotalNumberCompare = $('#arrayLabelCartAbandonedTotalNumberCompare').val().split(",");
    var arrayCartTotalNumberCompare = $('#arrayCartTotalNumberCompare').val().split(",");
    var arrayCartAbandonedTotalNumberCompare = $('#arrayCartAbandonedTotalNumberCompare').val().split(",");

    var isCompare = $('#isCompare').val();
    if (isCompare != 1) {

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
                            beginAtZero: true
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
                            beginAtZero: true
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
                            beginAtZero: true
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
                            beginAtZero: true
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
                            beginAtZero: true
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
                            beginAtZero: true
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
                            beginAtZero: true
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
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    } else {
        let ctxQtyOrder = document.getElementById("ChartQtyOrder").getContext('2d');
        let ChartQtyOrder = new Chart(ctxQtyOrder, {
            type: 'bar',
            data: {
                labels: arrayLabelOrder,
                datasets: [{
                    label: 'Numero Ordini Anno corrente',
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
                },
                    {
                        label: 'Numero Ordini  Anno Precedente',
                        data: arrayCountOrderCompare,
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
                            beginAtZero: true
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
                    label: 'Valore Ordini Anno Corrente',
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
                },
                    {
                        label: 'Valore Ordini Anno Precedente',
                        data: arrayOrderCompare,
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
                            beginAtZero: true
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
                    label: 'Quantita Ordini Resi Anno Corrente',
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
                }, {
                    label: 'Quantita Ordini Resi Anno Precedente',
                    data: arrayCountOrderReturnCompare,
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
                            beginAtZero: true
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
                    label: 'Valori Ordini Resi Anno Corrente',
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
                },
                    {
                        label: 'Valori Ordini Resi Anno Precedente',
                        data: arrayOrderReturnCompare,
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
                            beginAtZero: true
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
                    label: 'Quantita Carrelli Anno Corrente',
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
                },
                    {
                        label: 'Quantita Carrelli Anno Precedente',
                        data: arrayCartTotalNumberCompare,
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
                            beginAtZero: true
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
                    label: 'Quantita Carrelli Abbandonati Anno Corrente',
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
                },
                    {
                        label: 'Quantita Carrelli Abbandonati Anno Precedente',
                        data: arrayCartAbandonedTotalNumberCompare,
                        backgroundColor: [
                            'rgba(255,52,41, 0.2)',
                            'rgba(36,120,255, 1)',
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
                            'rgba(36,120,255, 1)',
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
        //ChartQtyUser
        let ctxChartQtyUser = document.getElementById("ChartQtyUser").getContext('2d');
        let ChartQtyUser = new Chart(ctxChartQtyUser, {
            type: 'line',
            data: {
                labels: arrayLabelTotalUser,
                datasets: [{
                    label: 'Totale Utenti  Registrati Anno Corrente',
                    data: arrayTotalUser,
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
                        label: 'Totale Utenti  Registrati Anno Precedente',
                        data: arrayTotalUserCompare,
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
        let ctxChartQtyUserOnLine = document.getElementById("ChartQtyUserOnLine").getContext('2d');
        let ChartQtyUserOnLine = new Chart(ctxChartQtyUserOnLine, {
            type: 'line',
            data: {
                labels: arrayLabelTotalUserOnLine,
                datasets: [{
                    label: 'Totale Utenti  Online Anno Corrente',
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
                },
                    {
                        label: 'Totale Utenti  Online Anno Precedente',
                        data: arrayTotalUserOnLineCompare,
                        backgroundColor: [
                            'rgba(255,52,41, 0.2)',
                            'rgba(254,199,255, 0.2)',
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
                            'rgba(254,199,255, 0.2)',
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
    }
    $('#currentDay').click(function () {
        let typePeriod = 'day';
        let isCompare = $('#isCompare').val();
        window.location.href = "/blueseal/newdashboard?typePeriod=" + typePeriod + '&isCompare=' + isCompare;

    });
    $('#currentWeek').click(function () {
        let typePeriod = 'week';
        let isCompare = $('#isCompare').val();
        window.location.href = "/blueseal/newdashboard?typePeriod=" + typePeriod + '&isCompare=' + isCompare;

    });
    $('#currentMonth').click(function () {
        let typePeriod = 'month';
        let isCompare = $('#isCompare').val();
        window.location.href = "/blueseal/newdashboard?typePeriod=" + typePeriod + '&isCompare=' + isCompare;

    });
    $('#currentYear').click(function () {
        let typePeriod = 'year';
        let isCompare = $('#isCompare').val();
        window.location.href = "/blueseal/newdashboard?typePeriod=" + typePeriod + '&isCompare=' + isCompare;

    });
    $('#btnsearchplus').click(function () {
        var typePeriod = 'custom';
        let isCompare = $('#isCompare').val();
        var startDateWork = $('#startDateWork').val()
        var endDateWork = $('#endDateWork').val()
        window.location.href = "/blueseal/newdashboard?typePeriod=" + typePeriod + '&startDateWork=' + startDateWork + '&endDateWork=' + endDateWork + '&isCompare=' + isCompare;

    });

})(jQuery);

