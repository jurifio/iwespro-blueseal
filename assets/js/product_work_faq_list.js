;(function () {


    $(document).on('bs.add.new.faq', function () {

        // Bottone per aggiunta faq
        let bsModal = new $.bsModal('Aggiungi una nuova faq', {
            body: `
                <div>
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
                $('#allFaq').empty();
                $.each(res, function (k, v) {
                    $('#allFaq').append(
                        `
                      <div class="panel panel-default ">
                        <div class="panel-heading accordion-toggle question-toggle collapsed"
                             data-toggle="collapse"
                             data-parent="#faqAccordion" data-target="#${v['id']}">
                            <h4 class="panel-title">
                                <a href="#" class="ing">D: ${v['question']}</a>
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

})();