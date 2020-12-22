window.buttonSetup = {
    tag: "a",
    icon: "fa-link",
    permission: "allShops",
    event: "bs-add-guide",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Aggiungi Guida",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-add-guide', function (e, element, button) {
    var dataTable = $('.dataTable').DataTable();
    var selectedRows = dataTable.rows('.selected').data();
    let appsId = [];
    //id-variantId in array
    $.each(selectedRows, function (k, v) {
        appsId.push(v.id);
    });

    if (selectedRows.length == 0) {
        new Alert({
            type: "warning",
            message: "Seleziona almeno un Applicazione"
        }).open();
        return false;
    }


    let bsModal = new $.bsModal('Aggiungi help all\'applicazione', {
        body: `<div class="row">
               <div class="form-group form-group-default selectize-enabled">
                                        <label for="postId">Seleziona help on line</label>
                                        <select id="postId" name="postId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                        </select>
               </div>
                </div>`

    });

    $.ajax({
        url: '/blueseal/xhr/PostHelpManageAjaxController',
        method: 'get',
        data: {
            blogId: "3"
        },
        dataType: 'json'

    }).done(function (res2) {
        let select = $('#postId');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();

        select.selectize({
            valueField: 'id',
            labelField: 'title',
            searchField: ['title', 'subtitle'],
            options: res2,
            render: {
                item: function (item, escape) {
                    return '<div>' +
                        '<span class="label">' + escape(item.title) + '</span> - ' +
                        '<span class="caption">' + escape(item.subtitle) + '</span>' +
                        '</div>'
                },
                option: function (item, escape) {
                    return '<div>' +
                        '<span class="label">' + escape(item.title) + '</span> - ' +
                        '<span class="caption">' + escape(item.subtitle) + '</span>' +
                        '</div>'
                }
            }
        });
    });


    bsModal.setOkEvent(function () {


        $.ajax({
            method: "post",
            url: "/blueseal/xhr/PostHelpManageAjaxController",
            data: {
                postId: $('#postId').val(),
                appsId: appsId,
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