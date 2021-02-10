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

    let Name = getParameterByName('nName');
    let Name2 = getParameterByName('nName2');
    let eAC =  getParameterByName('eAC');
    let eAC2 = getParameterByName('eAC2');
    let eACP =  getParameterByName('eACP');
    let eACP2 = getParameterByName('eACP2');
    let eACN =  getParameterByName('eACN');
    let eACN2 = getParameterByName('eACN2');
    let eACA=  getParameterByName('eACA');
    let eACA2 = getParameterByName('eACA2');
    let eACD =  getParameterByName('eACD');
    let eACD2 = getParameterByName('eACD2');
    let eACDD =  getParameterByName('eACDD');
    let eACDD2 = getParameterByName('eACDD2');

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



    let ctxSent = document.getElementById("ChartQtyOrder").getContext('2d');
    let ChartSent = new Chart(ctxSent, {
        type: 'bar',
        data: {
            labels: [Name, Name2],
            datasets: [{
            label: 'Numero Ordini',
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



})(jQuery);

