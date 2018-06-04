window.buttonSetup = {
    tag:"a",
    icon:"fa-step-backward",
    permission:"allShops",
    event:"bs-change-product-state-batch",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Notifica lo stato del lotto",
    placement:"bottom",
    toggle:"modal"
};


    $(document).on('bs-change-product-state-batch', function () {
        let selectedRows = $('.table').DataTable().rows('.selected').data();



        if(selectedRows.length < 1){
            new Alert({
                type: "warning",
                message: "Devi selezionare uno o più Prodotti per poter cambiare lo stato della lavorazione"
            }).open();
            return false;
        }

        let workType = window.location.href.split('/').slice(-2)[0];

        let selectedElement = [];

        switch (workType){
            case 'normalizzazione-prodotti':
                $.each(selectedRows, function (k, v) {
                    selectedElement.push(v.id);
                });
                break;
            case 'dettagli-brand':
                $.each(selectedRows, function (k, v) {
                    selectedElement.push(v.DT_RowId);
                });
                break;
        }


        let cat = selectedRows[0].work_category;

        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'WorkCategorySteps',
                condition: {workCategoryId: cat}
            },
            dataType: 'json'
        }).done(function (res) {
            var select = $('#categories');
            if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: 'name',
                options: res
            });
        });

        let bsModal = new $.bsModal('Cambia stato della lavorazione', {
            body: `<div>
                        <p>Seleziona lo stato</p>
                        <select id="categories">
                        </select>
                   </div>`
        });


        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {
                prod: selectedElement,
                cat: $('#categories').val(),
                pb: window.location.href.substring(window.location.href.lastIndexOf('/') + 1),
                workType: workType
            };
            $.ajax({
                method: 'put',
                url: '/blueseal/xhr/ProductBatchDetailsManage',
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

