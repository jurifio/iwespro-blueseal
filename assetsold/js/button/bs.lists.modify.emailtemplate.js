window.buttonSetup = {
    tag: "a",
    icon: "fa-pencil",
    permission: "allShops||worker",
    event: "bs-emailtemplate-modify",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Modifica il Template",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-emailtemplate-modify', function (e, element, button) {

    let dataTable = $('.dataTable').DataTable();
    let selectedRows = dataTable.rows('.selected').data();




    let emailTemplateId = selectedRows[0].DT_RowId;
   let urldef='/blueseal/email/email-template-modifica/'+emailTemplateId;


    location.href = urldef;

});