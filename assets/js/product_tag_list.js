$(document).on('bs.product.tag', function () {

    var bsModal = $('#bsModal');
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    header.html('Tagga Prodotti');

    var getVarsArray = [];
    var selectedRows = $('.table').DataTable().rows('.selected').data();
    var selectedRowsCount = selectedRows.length;

    if (selectedRowsCount < 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare uno o più Prodotti per poterli taggare"
        }).open();
        return false;
    }

    $.each(selectedRows, function (k, v) {
        var rowId = v.DT_RowId.split('__');
        getVarsArray.push(rowId[1] + '__' + rowId[2]);
    });


    body.html('<img src="/assets/img/ajax-loader.gif" />');

    Pace.ignore(function () {
        $.ajax({
            url: '/blueseal/xhr/ProductTag',
            type: "get",
            data: {
                rows: getVarsArray
            }
        }).done(function (response) {
            body.html(response);
            okButton.html('Ok').off().on('click', function () {
                okButton.on('click', function () {
                    bsModal.modal('hide')
                });
                var action;
                var message;
                switch ($('.tab-pane.active').eq(0).attr('id')) {
                    case 'add':
                        action = 'post';
                        message = 'Tag Applicate';
                        break;
                    case 'delete':
                        action = 'put';
                        message = 'Tag Rimosse';
                        break;
                }

                var getTagsArray = [];
                $.each($('.tree-selected'), function () {
                    getTagsArray.push($(this).attr('id'));
                });
                body.html('<img src="/assets/img/ajax-loader.gif" />');
                $.ajax({
                    url: '/blueseal/xhr/ProductTag',
                    type: action,
                    data: {
                        rows: getVarsArray,
                        tags: getTagsArray
                    }
                }).done(function (response) {
                    body.html('<p>' + message + '</p>');
                    okButton.on('click', function () {
                        bsModal.modal('hide');
                        $('.table').DataTable().ajax.reload();
                    });
                }).fail(function (response) {
                    body.html('<p>Errore</p>');
                });
            });

        });
    });

    bsModal.modal();
});

$(document).on('bs.priority.edit', function () {

	var bsModal = $('#bsModal');
	var header = $('.modal-header h4');
	var body = $('.modal-body');
	var cancelButton = $('.modal-footer .btn-default');
	var okButton = $('.modal-footer .btn-success');

	header.html('Assegna Priorità Prodotti');

	var getVarsArray = [];
	var selectedRows = $('.table').DataTable().rows('.selected').data();
	var selectedRowsCount = selectedRows.length;

	if (selectedRowsCount < 1) {
		new Alert({
			type: "warning",
			message: "Devi selezionare uno o più Prodotti per poterli modificare"
		}).open();
		return false;
	}

	$.each(selectedRows, function (k, v) {
		var rowId = v.DT_RowId.split('__');
		getVarsArray.push(rowId[1] + '-' + rowId[2]);
	});
	body.html('<img src="/assets/img/ajax-loader.gif" />');

	Pace.ignore(function () {
		$.ajax({
			url: '/blueseal/xhr/ProductPriority',
			type: "get",
			data: {
				rows: getVarsArray
			}
		}).done(function (response) {
			var priorities = JSON.parse(response);
			var html = '<div style="height: 200px" class="form-group form-group-default selectize-enabled full-width">';
			html += '<select class="full-width" placeholder="Seleziona la priorità" data-init-plugin="selectize" title="" name="priorityId" id="priorityId" required>';
			html += '</select>';
			html += '</div>';

			body.html(html);
			$('#priorityId').selectize({
				valueField: 'id',
				labelField: 'priority',
				searchField: ['priority'],
				options: priorities
			});
			okButton.html('Ok').off().on('click', function () {
				var priority = $('#priorityId').eq(0).val();
				if('undefined' == priority) return;
				okButton.on('click', function () {
					bsModal.modal('hide')
				});

				body.html('<img src="/assets/img/ajax-loader.gif" />');
				$.ajax({
					url: '/blueseal/xhr/ProductPriority',
					type: "PUT",
					data: {
						rows: getVarsArray,
						priority: priority
					}
				}).done(function (response) {
					body.html('<p>Produtti aggiornati: ' +  response + '</p>');
					okButton.on('click', function () {
						bsModal.modal('hide');
						$('.table').DataTable().ajax.reload();
					});
				}).fail(function (response) {
					body.html('<p>Errore</p>');
				});
			});

		});
	});

	bsModal.modal();
});

$(document).on('click', ".tag-list > li", function (a, b, c) {
    if ($(this).hasClass('tree-selected')) {
        $(this).removeClass('tree-selected');
    } else {
        $(this).addClass('tree-selected');
    }
});

