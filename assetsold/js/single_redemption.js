;(function () {

    $(document).on('bs.clicked.url', function () {

        let dataTable = $('.dataTable').DataTable();
        let selectedRows = dataTable.rows('.selected').data();

        if (selectedRows.length === 1) {
            let emailId = selectedRows[0].emailId;
            let emailAddressId = selectedRows[0].emailAddressId;

            let url = 'newsletter-redemption/single-redemption/clicked-url?' + 'emailId=' + emailId + '&emailAddressId=' + emailAddressId;

            window.open(url, '_blank');
        } else if (selectedRows.length === 0) {
            new Alert({
                type: "warning",
                message: "Seleziona una riga"
            }).open();
            return false;
        } else {
            new Alert({
                type: "warning",
                message: "Seleziona solamente una riga"
            }).open();
            return false;
        }
    });

})();