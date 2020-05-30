$('#productZeroQuantity').click(function (e, element, button) {
    if ($('#season').prop("checked")) {
        $('.dataTable').dataTableFilter(element,'productZeroQuantity');
    }
});
$('#season').click(function (e, element, button) {
    if ($('#season').prop("checked")) {
        $('.dataTable').dataTableFilter(element,'season');
    }
});
$('#productStatus').click(function (e, element, button) {
    if ($('#productStatus').prop('checked')) {
        $('.dataTable').dataTableFilter(element,'productStatus');
    }
});