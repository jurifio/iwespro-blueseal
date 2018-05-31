;(function () {


    $(document).on('bs-message-add', function () {
        let bsModal = new $.bsModal('Assegna un utente', {
            body: `
            <div>
            <p>Titolo</p>
            <textarea style="width: 400px; height: 100px" id="title"></textarea>
            </div>
            <div>
            <p>Messaggio</p>
            <textarea style="height: 400px; width: 400px" id="mex"></textarea>
            </div>
            <div>
            <p>Seleziona la priorità</p>
            <select id="pr">
            <option value="L">Bassa</option>
            <option value="M" selected="selected">Media</option>
            <option value="H">Alta</option>
            </select>
            </div>
            `
        });

        bsModal.addClass('modal-wide');
        bsModal.addClass('modal-high');

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {
                title: $('#title').val(),
                mex: $('#mex').val(),
                pr: $('#pr').val()
            };
                $.ajax({
                    method: 'post',
                    url: '/blueseal/xhr/MessageManage',
                    data: data
                }).done(function (res) {
                    bsModal.writeBody(res);
                }).fail(function (res) {
                    bsModal.writeBody('Errore grave');
                }).always(function (res) {
                    bsModal.setOkEvent(function () {
                        $.refreshDataTable();
                        bsModal.hide();
                        //window.location.reload();
                    });
                    bsModal.showOkBtn();
                });
        });
    });



    $(document).on('bs-update-message', function () {

        let selectedRows = $('.table').DataTable().rows('.selected').data();

        if(selectedRows.length != 1){
            new Alert({
                type: "warning",
                message: "Puo modificare un messaggio alla volta"
            }).open();
            return false;
        }

        let mId = selectedRows[0].row_id;

        $.ajax({
            method:'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'Message',
                condition: {id: mId}
            },
            dataType: 'json'
        }).done(function (res) {
            $('#title').val(res[0].title);
            $('#mex').val(res[0].text);
            $('#pr').val(res[0].priority);
        });

        let bsModal = new $.bsModal('Assegna un utente', {
            body: `
            <div>
            <p>Titolo</p>
            <textarea style="width: 400px; height: 100px" id="title"></textarea>
            </div>
            <div>
            <p>Messaggio</p>
            <textarea style="height: 400px; width: 400px" id="mex"></textarea>
            </div>
            <div>
            <p>Seleziona la priorità</p>
            <select id="pr">
            <option value="L">Bassa</option>
            <option value="M">Media</option>
            <option value="H">Alta</option>
            </select>
            </div>
            `
        });

        bsModal.addClass('modal-wide');
        bsModal.addClass('modal-high');

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {
                title: $('#title').val(),
                mex: $('#mex').val(),
                pr: $('#pr').val(),
                m: mId
            };
            $.ajax({
                method: 'put',
                url: '/blueseal/xhr/MessageManage',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    $.refreshDataTable();
                    bsModal.hide();
                    //window.location.reload();
                });
                bsModal.showOkBtn();
            });
        });
    });


    $(document).on('bs-delete-message', function () {

        let selectedRows = $('.table').DataTable().rows('.selected').data();

        if(selectedRows.length < 1){
            new Alert({
                type: "warning",
                message: "Non hai selezionato nessun messaggio da eliminare"
            }).open();
            return false;
        }

        let ids = [];
        $.each(selectedRows, function (k, v) {
            ids.push(v.row_id);
        });

        let bsModal = new $.bsModal('Assegna un utente', {
            body: `<p>Sicuro di voler eliminare i messaggi selezionati?</p>`
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {
                ids: ids
            };
            $.ajax({
                method: 'delete',
                url: '/blueseal/xhr/MessageManage',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    $.refreshDataTable();
                    bsModal.hide();
                    //window.location.reload();
                });
                bsModal.showOkBtn();
            });
        });

    });

    })();