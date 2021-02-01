window.buttonSetup = {
  tag: "a",
  icon: "fa-eraser",
  permission: "allShops",
  event: "bs-lists-delete-planningwork",
  class: "btn btn-default",
  rel: "tooltip",
  title: "Cancella Attività",
  placement: "bottom",
  toggle: "modal"
};

$(document).on('bs-lists-delete-planningwork', function (e, element, button) {

  let dataTable = $('.dataTable').DataTable();
  let selectedRows = dataTable.rows('.selected').data();


  if (selectedRows.length != 1) {
    new Alert({
      type: "warning",
      message: "Devi selezionare un Attività per eliminarla"
    }).open();
    return false;
  }

  let newsletterId = selectedRows[0].DT_RowId;
  let bsModal = new $.bsModal('Cancellazione', {
    body: '<p>Cancella il Template selezionato</p>'
  });


  bsModal.setOkEvent(function () {

    let id = selectedRows[0].DT_RowId;


    $.ajax({
      method: "delete",
      url: "/blueseal/xhr/PlanningWorkDeleteAjaxController",
      data: {
        id: id
      }
    }).done(function (res) {
      bsModal.writeBody(res);
    }).fail(function (res) {
      bsModal.writeBody(res);
    }).always(function (res) {
      bsModal.setOkEvent(function () {
        window.location.reload();
        bsModal.hide();
        // window.location.reload();
      });
      bsModal.showOkBtn();
    });
  });
});