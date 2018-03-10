(function ($) {
    function getParameterByName(name, url) {
        if (!url) url = window.location.href;
        name = name.replace(/[\[\]]/g, "\\$&");
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
    }
  /*  const variable = $.decodeGetStringFromUrl(window.location.href);
    alert(window.location.href);*/
    let eAC =  getParameterByName('eAC');
    let Name = getParameterByName('nName');
    let Name2 = getParameterByName('nName2');
    let eAC2 = getParameterByName('eAC2');
    let oP = getParameterByName('oP');
    let oP2 = getParameterByName('oP2');
    oP = oP.substring(0, oP.length - 1);
    oP2 = oP2.substring(0, oP2.length - 1);
    let cP = getParameterByName('cP');
    let cP2 = getParameterByName('cP2');
    cP = cP.substring(0, cP.length - 1);
    cP2 = cP2.substring(0, cP2.length - 1);
    let sTime = getParameterByName('sTime');
    let sTime2 = getParameterByName('sTime2');
    sTime = sTime.substring(0, sTime.length - 1);
    sTime2 = sTime2.substring(0, sTime2.length - 1);
    let oTSS = getParameterByName('oTSS');
    let oTSS2 = getParameterByName('oTSS2');
    oTSS = oTSS.substring(0, oTSS.length - 1);
    oTSS2 = oTSS2.substring(0, oTSS2.length - 1);
    let cTSO = getParameterByName('cTSO');
    let cTSO2 = getParameterByName('cTSO2');
    cTSO = cTSO.substring(0, cTSO.length - 1);
    cTSO2 = cTSO2.substring(0, cTSO2.length - 1);
    let aT = getParameterByName('aT');
    let aT2 = getParameterByName('aT2');
    aT = aT.substring(0, aT.length - 1);
    aT2 = cTSO2.substring(0, aT2.length - 1);



    let ctxDelivered = document.getElementById("ChartDelivered").getContext('2d');
    let ChartDelivered = new Chart(ctxDelivered, {
        type: 'bar',
        data: {
            labels: [Name, Name2],
            datasets: [{
                label: 'inviate %',
                data: [eAC, eAC2],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255,99,132,1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
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
    let ctxOpened = document.getElementById("ChartOpened").getContext('2d');
    let ChartOpened = new Chart(ctxOpened, {
        type: 'bar',
        data: {
            labels: [Name, Name2],
            datasets: [{
                label: 'Aperte %',
                data: [oP  , oP2],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255,99,132,1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
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
    let ctxClicked = document.getElementById("ChartClicked").getContext('2d');
    let ChartClicked = new Chart(ctxClicked, {
        type: 'bar',
        data: {
            labels: [Name, Name2],
            datasets: [{
                label: 'Cliccate %',
                data: [cP  , cP2],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255,99,132,1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
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
    let ctxChartSentTime = document.getElementById("ChartSentTime").getContext('2d');
    let ChartSentTime = new Chart(ctxChartSentTime, {
        type: 'bar',
        data: {
            labels: [Name, Name2],
            datasets: [{
                label: 'Tempo di invio in Secondi',
                data: [sTime  , sTime2],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255,99,132,1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
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
    let ctxChartOpenedTime = document.getElementById("ChartOpenedTime").getContext('2d');
    let ChartOpenedTime = new Chart(ctxChartOpenedTime, {
        type: 'bar',
        data: {
            labels: [Name, Name2],
            datasets: [{
                label: 'Tempo dall\' Apertura in Secondi',
                data: [oTSS  , oTSS2],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255,99,132,1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
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
    let ctxChartAccessTime = document.getElementById("ChartAccessTime").getContext('2d');
    let ChartAccessTime = new Chart(ctxChartAccessTime, {
        type: 'bar',
        data: {
            labels: [Name, Name2],
            datasets: [{
                label: 'Tempo di accesso dall\' Apertura in Secondi',
                data: [aT  , aT2],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255,99,132,1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
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
    let ctxChartAccessTimeLastClick = document.getElementById("ChartAccessTimeLastClick").getContext('2d');
    let ChartAccessTimeLastClick = new Chart(ctxChartAccessTimeLastClick, {
        type: 'bar',
        data: {
            labels: [Name, Name2],
            datasets: [{
                label: 'Tempo di Apertura da Ultimo Click',
                data: [cTSO  , cTSO2],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255,99,132,1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
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


    $('.openstat').on('click', function () {
        $('#s-consegnate').removeClass("hide");
    });
    $('.openstataperte').on('click', function () {

        $('#s-aperte').removeClass("hide");
    });
    $('.openstatcliccate').on('click', function () {

        $('#s-cliccate').removeClass("hide");
    });
    $('.openstattinvio').on('click', function () {

        $('#s-tinvio').removeClass("hide");
    });
    $('.openstatapertura').on('click', function () {

        $('#s-tapertura').removeClass("hide");
    });
    $('.openstatfirstclic').on('click', function () {

        $('#s-tfirstclick').removeClass("hide");
    });
    $('.openstattlastclick').on('click', function () {

        $('#s-tlastclick').removeClass("hide");
    });


})(jQuery);

