;(function () {

    $(document).on('bs.add.fields', function () {

        let bsModal = new $.bsModal('Seleziona i campi da aggiungere', {
            body: `
                   <div id="fieldsCheck">
                       <div>
                           <input id="stock" type="checkbox" value="stock">
                           <label for="stock">Taglie</label>
                       </div>
                       <div>
                           <input id="externalId" type="checkbox" value="externalId">
                           <label for="externalId">ID Origine</label>
                       </div>
                       <div>
                           <input id="season" type="checkbox" value="season">
                           <label for="season">Stagione</label>
                       </div>
                       <div>
                           <input id="hasDetails" type="checkbox" value="hasDetails">
                           <label for="hasDetails">Dettagli</label>
                       </div>
                   </div>                    
                   `
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {

            let url = '/blueseal/prodotti/catalog?';
            $('#fieldsCheck input:checked').each(function () {
                url = url + $(this).val() + '=1&'
            });

            window.open(url, '_blank');
        });
    });



    $(document).on('bs.information.add', function () {

        let selectedRow = $('.table').DataTable().rows('.selected').data();

        if (selectedRow.length != 1) {
            new Alert({
                type: "warning",
                message: "Devi selezionare solo una riga"
            }).open();
            return false;
        }

        let bsModal = new $.bsModal('Seleziona le info', {
            body: `
                   <div id="fieldsCheckGet">
                       <div>
                           <input id="sizes" type="checkbox" value="sizes">
                           <label for="sizes">Taglie</label>
                       </div>
                       <div>
                           <input id="externalId" type="checkbox" value="externalId">
                           <label for="externalId">ID Origine</label>
                       </div>
                       <div>
                           <input id="season" type="checkbox" value="season">
                           <label for="season">Stagione</label>
                       </div>
                       <div>
                           <input id="details" type="checkbox" value="details">
                           <label for="details">Dettagli</label>
                       </div>
                   </div>                    
                   `
        });

        let prodId = selectedRow[0].DT_RowId;

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {

            let checkedFields = [];
            $('#fieldsCheckGet input:checked').each(function () {
                checkedFields.push($(this).val());
            });

            const data = {
                prodId: prodId,
                checkedFields: checkedFields
            };

            $.ajax({
                method: 'get',
                url: '/blueseal/xhr/ProductCatalogManageAjaxController',
                data: data,
                dataType: 'json'
            }).done(function (data) {

                let allData = $(`#${prodId} td:first`);

                if (data['sizes'] != undefined && $("#sizeField").length == 0) {

                    if (data['sizes'].rows.length === 0) {
                        allData.append('Quantità non inserite');
                    } else {

                        let thead = '<thead><tr>';
                        for (let thd in data['sizes'].head) {
                            if (!data['sizes'].head.hasOwnProperty(thd)) continue;
                            thead += '<th>' + data['sizes'].head[thd] + '</th>';
                        }
                        thead += '</tr></thead>';

                        let td;
                        let row;
                        let padding;
                        let body = $('<tbody></tbody>');
                        for (let k in data['sizes'].rows) {
                            row = '<tr>';
                            let rowD = data['sizes'].rows[k];
                            for (let i in data['sizes'].head) {
                                if (!data['sizes'].head.hasOwnProperty(i)) continue;
                                if (i === '0') {
                                    row += '<td>' + rowD[i] + '</td>';
                                } else if (i === '1') {
                                    row += '<td>' + rowD[i].substring(0, 6) + ".." + '</td>';
                                } else if (typeof rowD[i] === 'undefined') {
                                    row += '<td>0</td>';
                                } else {
                                    td = rowD[i];
                                    padding = Number(td.padding);
                                    row += '<td class="' + (padding !== 0 ? 'colorRed' : '') + '">' + (Number(td.qty) - padding) + '</td>';
                                }
                            }
                            row += '</tr>';
                            body.append($(row));
                        }

                        let sizes = '<div style="margin-bottom: 5px;" id="sizeField"><strong>Taglie</strong><br></div><table class="nested-table inner-size-table" data-product-id="' + prodId + '">' + thead + body[0].innerHTML + '</table></div>';
                        allData.append(sizes);   //it doesn't exist
                    }
                }

                if (data['externalId'] != undefined && $("#externalField").length == 0) {
                    let externalId = '<div style="margin-bottom: 5px;" id="externalField"><strong>External Id</strong><br>' + data['externalId'] + '</div>';
                    allData.append(externalId);
                }

                if (data['season'] != undefined && $("#seasonField").length == 0) {
                    let season = '<div style="margin-bottom: 5px;" id="seasonField"><strong>Stagione</strong><br>' + data['season'] + '</div>';
                    allData.append(season);
                }

                if (data['details'] != undefined && $("#detailField").length == 0) {
                    let details = '<div style="margin-bottom: 5px;"  id="detailField>"><strong>Dettagli</strong><br>' + data['details'] +  '</div>';
                    allData.append(details);
                }


                bsModal.hide();
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            })
        });
    });

})();