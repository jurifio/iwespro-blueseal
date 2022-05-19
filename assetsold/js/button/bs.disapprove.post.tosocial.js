window.buttonSetup = {
    tag: "a",
    icon: "fa-thumbs-down",
    permission: "/admin/product/edit&&allShops",
    event: "bs-disapprove-post-tosocial",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Revisiona il Post",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-disapprove-post-tosocial', function (e, element, button) {
    let dataTable = $('.dataTable').DataTable();
    let selectedRows = dataTable.rows('.selected').data();

    if (selectedRows.length != 1) {
        new Alert({
            type: "warning",
            message: "Seleziona un Post alla Volta"
        }).open();
        return false;
    }

    let postId = selectedRows[0].row_id;
    let bsModal = new $.bsModal('Revisiona il  Post', {
        body: `<div class="row"><div class="col-md-12">Motivo della revisione Post</div></div>
               <div class="row"><div class="col-md-12">
               <div class="form-group form-group-default selectize-enabled">
                                            <label for="motive">Motivo</label>
                                            <textarea id="motive" cols="80" rows="10" name="motive"
                                                      placeholder="Inserisci le motivazioni e le eventuali specifiche per la revisione"></textarea>
                                        </div>
</div></div>`

    });


    bsModal.setOkEvent(function () {

        let id = selectedRows[0].row_id;


        $.ajax({
            method: "post",
            url: "/blueseal/xhr/EditorialPlanDetailDisapproveAjaxController",
            data: {
                editorialPlanDetailId: id,
                motive:$('#motive').val()
            }
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                $.refreshDataTable();
                bsModal.hide();
                // window.location.reload();
            });
            bsModal.showOkBtn();
        });
    });
});