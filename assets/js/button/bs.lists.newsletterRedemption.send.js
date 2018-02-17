window.buttonSetup = {
    tag: "a",
    icon: "fa-line-chart",
    permission: "/admin/product/edit&&allShops",
    event: "bs-newNewsletterRedemption-send",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Compara Risultati Newsletter",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-newNewsletterRedemption-send', function () {

    let dataTable = $('.dataTable').DataTable();
        let selectedRows = dataTable.rows('.selected').data();
        if (selectedRows.length === 2) {

            var idNewsletterRedemption = selectedRows[0].id;
            var idNewsletterRedemption2 = selectedRows[1].id;
            var idNewsletterRedemptionName = selectedRows[0].newsletterName;
            var idNewsletterRedemptionName2 = selectedRows[1].newsletterName;
            var emailAddressCount = selectedRows[0].emailAddressCount;
            var emailAddressCount2 = selectedRows[1].emailAddressCount;
            var sendingTime = selectedRows[0].sendingTime;
            var sendingTime2 = selectedRows[1].sendingTime;
            var openTimeSinceSent = selectedRows[0].openTimeSinceSent;
            var openTimeSinceSent2 = selectedRows[1].openTimeSinceSent;
            var clickTimeSinceOpened = selectedRows[0].clickTimeSinceOpened;
            var clickTimeSinceOpened2 = selectedRows[1].clickTimeSinceOpened;
            var aliveTime = selectedRows[0].aliveTime;
            var aliveTime2 = selectedRows[1].aliveTime;
            var sentPercent = selectedRows[0].sentPercent;
            var sentPercent2 = selectedRows[1].sentPercent;
            var openedPercent = selectedRows[0].openedPercent;
            var openedPercent2 = selectedRows[1].openedPercent;
            var clickedPercent = selectedRows[0].clickedPercent;
            var clickedPercent2 = selectedRows[1].clickedPercent;

            let bsModal = new $.bsModal('Invio', {
                body: '<p>Compara i Risultati per le Newsletters selezionate</p>' +
                '<div class="form-group form-group-default required">' +
                '<label for="deleteMacroGroup">Invio</label>' +
                '<div><p>Premere ok per visualizzare  i Grafici delle Newsletter con id:'+ idNewsletterRedemption +' e id:' + idNewsletterRedemption2 +' il messaggio di generazione completata</p></div>' +
                '</div>'
            });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {

            let url = 'newsletter/newsletter-redemption-compara?' + 'nId=' + encodeURIComponent(idNewsletterRedemption) + '&nId2=' + encodeURIComponent(idNewsletterRedemption2) +
                                                                    '&nName=' + encodeURIComponent(idNewsletterRedemptionName) + '&nName2=' + encodeURIComponent(idNewsletterRedemptionName2) +
                                                                    '&eAC=' + encodeURIComponent(emailAddressCount) + '&eAC2=' + encodeURIComponent(emailAddressCount2) +
                                                                    '&sTime=' + encodeURIComponent(sendingTime) + '&sTime2=' + encodeURIComponent(sendingTime2) +
                                                                    '&oTSS=' + encodeURIComponent(openTimeSinceSent) + '&oTSS2=' + encodeURIComponent(openTimeSinceSent2) +
                                                                    '&cTSO=' + encodeURIComponent(clickTimeSinceOpened) + '&cTSO2=' + encodeURIComponent(clickTimeSinceOpened2) +
                                                                    '&aT=' + encodeURIComponent(aliveTime) + '&aT2=' + encodeURIComponent(aliveTime2) +
                                                                    '&sP=' + encodeURIComponent(sentPercent) + '&sP2=' + encodeURIComponent(sentPercent2) +
                                                                    '&oP=' + encodeURIComponent(openedPercent) + '&oP2=' + encodeURIComponent(openedPercent2) +
                                                                    '&cP=' + encodeURIComponent(clickedPercent) + '&cP2=' + encodeURIComponent(clickedPercent2);
            window.open(url, '_self');

        });

    } else if (selectedRows.length < 1){
        new Alert({
            type: "warning",
            message: "Devi selezionare almeno due righe riga"
        }).open();
        return false;

    }

});