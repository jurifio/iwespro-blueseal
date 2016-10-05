$(document).on('bs.accept.order.lines', function() {
    modal = new bsModal('Accettazione ordini',
        {
            body: '<div class="radio">' +
                '<label><input type="radio" name="lineAccept" value="1">Sì</label>' +
                '</div>' +
                '<div class="radio">' +
                '<label><input type="radio" name="lineAccept" value="0">No</label>' +
                '</div>',
            okButtonEvent: function(){
                var radio = $('input[name="lineAccept"]').val();

            }
        });
});