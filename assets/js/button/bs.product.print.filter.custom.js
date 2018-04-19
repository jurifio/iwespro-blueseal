window.buttonSetup = {
    tag:"a",
    icon:"fa-print",
    permission:"/admin/product/list",
    event:"bs-print-product-custom-filter",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Stampa il risultato sottostante",
    placement:"bottom"
};

$(document).on('bs-print-product-custom-filter', function () {

    var divToPrint = document.getElementById('printThis');
    var htmlToPrint = '' +
        '<style type="text/css">' +
        'table th, table td {' +
        'border:1px solid #000;'+
        '}' +
        '</style>';
    htmlToPrint += divToPrint.outerHTML;
    newWin = window.open("");
    newWin.document.write(htmlToPrint);
    newWin.print();
    newWin.close();
});
