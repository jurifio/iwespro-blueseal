$(document).on('bs.roulette.add', function (e, element, button) {
    window.location = '/blueseal/prodotti/roulette?roulette=' + $(element).val();
});


$(document).on('column-visibility.dt',function (e, settings, column, state) {
    var dt = $('table[data-datatable-name]').DataTable();
    if(state) {
        $('table.inner-size-table').each(function(k,v) {
            disegnaLaTabellaDiMerda($(v));
        });
    }
});

function disegnaLaTabellaDiMerda(table) {
    "use strict";
    if(table.data('loaded') == 'true') return;
    var container = table.closest('td');
    var data = table.data('productId');
    container.html('Loading...');
    Pace.ignore(function () {
        "use strict";

        $.ajax({
            "url": "/blueseal/xhr/ProductSizeTable",
            "data": {"productId": data},
            "dataType": "json"
        }).done(function (data) {
            if (data.rows.length == 0) {
                container.html('Quantità non inserite');
                return;
            }
            var head = $('<thead></thead>');
            for (var i in data.head) {
                var thd = data.head[i];
                var hhead = $('<th>' + thd + '</th>');
                head.append(hhead);
            }
            table.append(head);
            var td;
            var row;
            var body = $('<tbody></tbody>');
            for (var k in data.rows) {
                row = '<tr>';
                var rowD = data.rows[k];
                for (i in data.head) {
                    if(i == 0) {
                        row+='<td>'+rowD[i]+'</td>';
                    } else if (typeof rowD[i] == 'undefined') {
                        row += '<td>0</td>';
                    } else {
                        td = rowD[i];
                        row += '<td class="' + (td.padding ? 'red' : '' ) + '">' + (td.qty - td.padding) + '</td>';
                    }
                }
                row += '</tr>';
                body.append($(row));
            }
            table.append(body);
            table.data('loaded','true');
            container.html(table);
        });
    });
}