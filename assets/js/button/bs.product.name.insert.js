/**
 * Created by Fabrizio Marconi on 09/09/2016.
 */

window.buttonSetup = {
    tag: "a",
    icon: "fa-pencil",
    permission: "/admin/product/edit||worker",
    event: "bs-product-name-insert",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Inserisci un nuovo nome prodotto",
    placement: "bottom"
};

$(document).on('bs-product-name-insert', function (e, element, button) {
    modal = new $.bsModal(
        'Inserisci un nuovo nome per i prodotti',
        {
            body: '<div class="alert alert-danger modal-alert" style="display: none">Il campo <strong>Italiano</strong> è obbligatorio</div>' +
            '<form id="detailAdd"><div class="form-group">' +
            '<label>Italiano*</label>' +
            '<input type="text" class="form-control new-name-ita" name="newDettIta" />' +
            '</div></form>',
            okLabel: 'Aggiungi',
            okButtonEvent: function () {
                var field = $('.new-name-ita');

                if ('' === field.val()) {
                    $('.modal-alert').css('display', 'block');
                } else {
                    $.ajax({
                            type: "POST",
                            async: false,
                            url: "/blueseal/xhr/ProductNameAdd",
                            data: {
                                name: field.val()
                            }
                        }
                    ).done(function (res) {
                        if ('ok' == res) {
                            modal.writeBody('Il nuovo nome è stato inserito');
                        } else {
                            modal.writeBody(res);
                        }
                        modal.hideCancelBtn();
                        modal.setLabel('ok', 'Ok');
                        modal.setOkEvent(function () {
                            modal.hide();
                        });
                    });
                    return false;
                }
            }
        }
    );

    $('.new-name-ita').on('keyup', function (e) {
        var self = $(this);
        var value = self.val();
        if (13 != e.keyCode) {
            var value = $(this).val();
            $.ajax({
                url: '/blueseal/xhr/ProductNameAdd',
                method: 'GET',
                data: {name: value}
            }).done (function (res) {
                if ('ok' == res) {
                    self.closest('.form-group').removeClass('error');
                    modal.okButton.prop ('disabled', false);
                } else {
                    self.closest('.form-group').addClass('error');
                    modal.okButton.prop ('disabled', true);
                    modal.setCancelButton (function(){
                        modal.okButton.prop ('disabled', false);
                        modal.hide();
                    });
                    $ ('button.close').off ().on ('click', function (){
                        modal.okButton.prop ('disabled', false);
                        modal.hide();
                    });
                }
            });
        } else {
            e.preventDefault();
        }
    });
});