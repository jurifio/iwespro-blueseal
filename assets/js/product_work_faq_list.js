;(function () {


    $(document).on('bs.add.new.faq', function () {

        // Bottone per aggiunta faq
        let bsModal = new $.bsModal('Aggiungi una nuova faq', {
            body: `
                <div>
                    <div>
                    <select id="selectArgument"></select>
                    </div>
                    <div>
                        <p><strong>Domanda:</strong></p>
                        <textarea id="question" rows="10" cols="50"></textarea>
                    </div>
                    <div>
                        <p><strong>Risposta:</strong></p>
                        <textarea id="answer" rows="10" cols="50"></textarea>
                    </div>
                </div>
            `
        });

        $.ajax({
            url: '/blueseal/xhr/GetTableContent',
            type: 'GET',
            data: {
                table: 'FaqArgument',
                condition: {faqTypeId: 1}
            },
            dataType: 'json'
        }).done(function (res) {
            let argSel = $('#selectArgument');
            if(typeof (argSel[0].selectize) != 'undefined') argSel[0].selectize.destroy();
            argSel.selectize({
                valueField: 'id',
                labelField: ['text'],
                options: res
            });
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {
                arg: $('#selectArgument').val(),
                q: $('#question').val(),
                a: $('#answer').val()
            };
            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/ProductWorkFaqAjaxController',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    bsModal.hide();
                    window.location.reload();
                });
                bsModal.showOkBtn();
            });
        });

    });


    // Funzione ricerca
    $(document).on('keyup', '#search', function (e) {
        elem = $(e.target);
        if (13 == e.charCode) {
            e.preventDefault();
        }
        let val = elem.val();
        let dataOption = [];
        let url = '';

        if (3 < val.length) {
            let query = elem.val();
            url = 'ProductWorkFaqAjaxController';
            dataOption.push({
                search: query
            });
            faqAjax(url, dataOption);
        } else if (0 == val.length) {
            url = 'GetTableContent';
            dataOption.push({
                table: 'Faq',
                condition: {faqTypeId: 1}
            });
            faqAjax(url, dataOption);
        }

    });

    function faqAjax(url, dataOption) {
        Pace.ignore(function () {
            $.ajax({
                url: '/blueseal/xhr/' + url,
                type: 'GET',
                data: dataOption[0],
                dataType: 'json'
            }).done(function (res) {
                $('.allArgs').empty();
                let perm = $('#permCheck').val();
                $.each(res, function (k, v) {

                let txtPerm = perm == 1 ? `(Id: ${v['id']})` : '';

                    $(`#sec-${v['faqArgumentId']}`).append(
                        `
                      <div class="panel panel-default ">
                        <div class="panel-heading accordion-toggle question-toggle collapsed"
                             data-toggle="collapse"
                             data-parent="#faqAccordion" data-target="#${v['id']}">
                            <h4 class="panel-title">
                                <a href="#" class="ing">${txtPerm} D: ${v['question']}</a>
                            </h4>

                        </div>
                        <div id="${v['id']}" class="panel-collapse collapse" style="height: 0px;">
                            <div class="panel-body">
                                <h5><span class="label label-primary">Risposta</span></h5>

                                <p>
                                    ${v['answer']}
                                </p>
                            </div>
                        </div>
                    </div>
                      `
                    )
                });
            });
        });
    }

    $(document).on('bs.modify.faq', function () {

        // Bottone per aggiunta faq
        let bsModal = new $.bsModal('Modifica faq', {
            body: `
                <div>
                    <div>
                        <p><strong>Inserisci l'id della faq</strong></p>
                        <input type="number" min="1" step="1" id="idFaq">
                    </div>
                    <div>
                        <p><strong>Domanda:</strong></p>
                        <textarea id="question" rows="10" cols="50"></textarea>
                    </div>
                    <div>
                        <p><strong>Risposta:</strong></p>
                        <textarea id="answer" rows="10" cols="50"></textarea>
                    </div>
                </div>
            `
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {
                idFaq: $('#idFaq').val(),
                q: $('#question').val(),
                a: $('#answer').val()
            };
            $.ajax({
                method: 'put',
                url: '/blueseal/xhr/ProductWorkFaqAjaxController',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    bsModal.hide();
                    window.location.reload();
                });
                bsModal.showOkBtn();
            });
        });

    });


    $(document).on('bs.delete.faq', function () {

        // Bottone per eliminazione faq
        let bsModal = new $.bsModal('Elimina faq', {
            body: `
                <div>
                    <div>
                        <p><strong>Inserisci l'id della faq da cancellare</strong></p>
                        <input type="number" min="1" step="1" id="idFaq">
                    </div>
                </div>
            `
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {
                idFaq: $('#idFaq').val()
            };
            $.ajax({
                method: 'delete',
                url: '/blueseal/xhr/ProductWorkFaqAjaxController',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    bsModal.hide();
                    window.location.reload();
                });
                bsModal.showOkBtn();
            });
        });

    });

})();