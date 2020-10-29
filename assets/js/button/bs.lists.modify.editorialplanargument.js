window.buttonSetup = {
    tag: "a",
    icon: "fa-pencil-square-o",
    permission: "/admin/product/delete&&allShops",
    event: "bs-editorialplanargument-edit",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Edita l\'Argomento",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-editorialplanargument-edit', function (e, element, button) {
    let dataTable = $('.dataTable').DataTable();
    let selectedRows = dataTable.rows('.selected').data();


    if (selectedRows.length != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare un Argomento per Modificarlo"
        }).open();
        return false;
    }

    let editorialPlanId = selectedRows[0].id;
    var editorialPlanSocialId='';
    if(selectedRows[0].editorialPlanSocialId!=null) {
        editorialPlanSocialId = selectedRows[0].editorialPlanSocialId;
    }
    var workCategoryId='';
    if(selectedRows[0].workCategoryId!=null) {
        workCategoryId = selectedRows[0].workCategoryId;
    }
    let bsModal = new $.bsModal('Invio', {
        body: '<p>Modifica l\'Argomento Selezionato</p>' +
        '<div class="row">' +
        '<div class="col-xs-6>">' +
        '<label for="editorialPlanArgumentTitleArgument">Nome Argomento</label>' +
        '<input autocomplete="on" type="text" id="editorialPlanArgumentTitleArgument" ' +
        'class="form-control" name="editorialPlanArgumentTitleArgument" value="' + selectedRows[0].titleArgument + '">' +
        '</div>' +
        '</div>' +
        '<div class="row">' +
        '<div class="col-xs-6>">' +
        '<label for="editorialPlanArgumentType">Tipo Argomento </label>' +
        '<input autocomplete="on" type="text" id="editorialPlanArgumentType" ' +
        'class="form-control" name="editorialPlanArgumentType" value="' + selectedRows[0].type + '">' +
        '</div>'+
        '</div>'+
        '<div class="row">' +
        '<div class="col-xs-6>">' +
        '<label for="editorialPlanArgumentDescription">Descrizione Argomento </label> ' +
        '<textarea id=\"editorialPlanArgumentDescription\" class=\"form-control\" ' +
        'name="editorialPlanArgumentDescription">' + selectedRows[0].descriptionArgument + '</textarea>' +
        '</div>' +
        '</div>'+
            `<div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-default selectize-enabled">
                        <label for="editorialPlanSocialId">Seleziona il Social Correlato</label>
                        <select id="editorialPlanSocialId" name="editorialPlanSocialId"
                                class="full-width selectpicker"
                                placeholder="Seleziona la Lista"
                                data-init-plugin="selectize">
                        </select>
                    </div>
                </div>
            </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group form-group-default selectize-enabled">
                <label for="workCategoryId">Seleziona La Categoria Operator</label>
                <select id="workCategoryId" name="workCategoryId"
                        class="full-width selectpicker"
                        placeholder="Seleziona la Lista"
                        data-init-plugin="selectize">
                </select>
            </div>
        </div>
    </div>`

    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'WorkCategory'

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#workCategoryId');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
            render: {
                item: function (item, escape) {
                    return '<div>' +
                        '<span class="label">' + escape(item.name) + ' | ' + escape(item.slug) + '</span> - ' +
                        '<span class="caption">sezionale id:' + escape(item.sectionalCodeId) + '</span>' +
                        '</div>'
                },
                option: function (item, escape) {
                    return '<div>' +
                        '<span class="label">' + escape(item.name) + ' | ' + escape(item.slug) + '</span> - ' +
                        '<span class="caption">sezionale id:' + escape(item.sectionalCodeId) + '</span>' +
                        '</div>'
                        '</div>'
                }
            },
            onInitialize: function () {
                var selectize = this;
                selectize.setValue(workCategoryId);
            }
        });
    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'EditorialPlanSocial'

        },
        dataType: 'json'
    }).done(function (res2) {
        var select2 = $('#editorialPlanSocialId');
        if (typeof (select2[0].selectize) != 'undefined') select2[0].selectize.destroy();
        select2.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
            render: {
                item: function (item, escape) {
                    return '<div>' +
                        '<span class="label">' + escape(item.name) + ' | ' + escape(item.iconSocial) + '</span> - ' +
                        '<span class="caption">colore:' + escape(item.color) + '</span>' +
                        '</div>'
                },
                option: function (item, escape) {
                    return '<div>' +
                        '<span class="label">' + escape(item.name) + ' | ' + escape(item.iconSocial) + '</span> - ' +
                        '<span class="caption">colore:' + escape(item.color) + '</span>' +
                        '</div>'
                }
            },
            onInitialize: function () {
                var selectize = this;
                selectize.setValue(editorialPlanSocialId);
            }
        });
    });
    if(editorialPlanSocialId!=null){
        $('#editorialPlanSocialId').selectize()[0].selectize.setValue(editorialPlanSocialId);
    }
    if(workCategoryId!=null){
        $('#workCategoryId').selectize()[0].selectize.setValue(workCategoryId);
    }






    bsModal.setOkEvent(function () {

        let id = selectedRows[0].id;
        let titleArgument = $('#editorialPlanArgumentTitleArgument').val();
        let type = $('#editorialPlanArgumentType').val();
        let descriptionArgument =$('#editorialPlanArgumentDescription').val();


        $.ajax({
            method: "put",
            url: "/blueseal/xhr/EditorialPlanArgumentManage",
            data: {
                id: id,
                titleArgument: titleArgument,
                type:type,
                descriptionArgument:descriptionArgument,
                workCategoryId:workCategoryId,
                editorialPlanSocialId:editorialPlanSocialId
            }
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                window.location.reload();
                bsModal.hide();
                // window.location.reload();
            });
            bsModal.showOkBtn();
        });
    });
});
