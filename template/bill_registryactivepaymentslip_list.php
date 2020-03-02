<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui','forms','tables'], $page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
</head>
<body class="fixed-header">
<?php include "parts/sidebar.php"; ?>
<div class="page-container">
    <?php include "parts/header.php" ?>
    <?php include "parts/operations.php" ?>

    <div class="page-content-wrapper">
        <div class="content sm-gutter">
            <div class="container-fluid container-fixed-lg bg-white">
                <div class="row">
                    <div class="col-md-4 col-md-offset-4 alert-container closed">

                    </div>
                </div>
            </div>

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped responsive" width="100%"
                               data-column-filter="true"
                               data-datatable-name="bill_registryactivepaymentslip_list"
                               data-controller="BillRegistryActivePaymentSlipListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="50, 100, 200, 500"
                               data-display-length="100"
                               id="orderTable">
                            <thead>
                            <tr>
                                <th data-slug="recipients"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Cliente</th>
                                <th data-slug="typePayment"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Modalità<br>Pagamento</th>
                                <th data-slug="numberSlip"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Numero<br>Distinta</th>
                                <th data-slug="creationDate"
                                    data-searchable="true"
                                    data-orderable="true" class="center dataFilterType">Data<br>Distinta</th>
                                <th data-slug="paymentDate"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="desc" class="center dataFilterType">Scadenza<br>Distinta</th>
                                <th data-slug="invoices"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Fatture<br>Associate</th>
                                <th data-slug="impAmount"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Importo<br>Fatture</th>
                                <th data-slug="impSlip"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Importo<br>Distinta</th>
                                <th data-slug="submissionDate"
                                    data-searchable="true"
                                    data-orderable="true" class="center dataFilterType">Sottomesso<br>Banca</th>
                                <th data-slug="statusId"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Stato</th>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Id</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include "parts/footer.php" ?>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Gestione Distinte">
        <bs-toolbar-button
            data-remote="bs.lists.generate.csv"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-remote="bs.paymentBill.submit"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-remote="bs.paymentBill.edit"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-remote="bs.paymentBill.print"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-remote="btn.add.paymentbill"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-remote="btn.check.paymentbill"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="btn.send.invoice.movements.paymentbill"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>

</body>
</html>