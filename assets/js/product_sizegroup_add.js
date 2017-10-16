(function () {
    let locked = false;

    $(document).on('click', 'table.table.size-table tbody td', function () {
        if (locked) return;
        if ($(this).find('input').length > 0) return;
        locked = true;
        const content = $(this).html();

        $(this).html('<input class="" type="text">');
        $(this).find('input').val(content).focus();
    });

    const saveCell = function(cellInput) {
        "use strict";
        //if(!locked) return;
        const value = cellInput.val();
        cellInput.closest('td').html(value);
        locked = false;
    };

    $(document).on('blur', 'table.table.size-table tbody td input', function () {
        saveCell($(this));
    });

    $(document).on('keyup', 'table.table.size-table tbody td input', function (e) {
        if (e.keyCode === 13) {
            saveCell($(this));
        }
    });

})();