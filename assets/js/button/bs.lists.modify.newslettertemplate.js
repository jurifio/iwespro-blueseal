window.buttonSetup = {
    tag: "a",
    icon: "fa-pencil",
    permission: "/admin/product/delete&&allShops",
    event: "bs-newslettertemplate-modify",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Modifica il Template",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-newslettertemplate-modify', function (e, element, button) {

    let dataTable = $('.dataTable').DataTable();
    let selectedRows = dataTable.rows('.selected').data();




    let newsletterTemplateId = selectedRows[0].id;
    var initial =newsletterTemplateId.indexOf("newsletter");
    var initial =initial -1;
    var templatelink = newsletterTemplateId.substr(initial,100 );
    var final =templatelink.indexOf('">');
    final=final -1;
    var templatelinkdef =templatelink.substr(1,final);


    location.href = templatelinkdef;

});