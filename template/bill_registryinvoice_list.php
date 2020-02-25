<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui','forms','tables'],$page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
</head>
<body class="fixed-header">
<?php include "parts/sidebar.php"; ?>
<div class="page-container">
    <?php include "parts/header.php"; ?>
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
                        <div class="row">
                            <div class="col-md-1">gennaio</div>
                            <div class="col-md-1">Febbraio</div>
                            <div class="col-md-1">Marzo</div>
                            <div class="col-md-1">Aprile</div>
                            <div class="col-md-1">Maggio</div>
                            <div class="col-md-1">Giugno</div>
                            <div class="col-md-1">Luglio</div>
                            <div class="col-md-1">Agosto</div>
                            <div class="col-md-1">Settembre</div>
                            <div class="col-md-1">Ottobre</div>
                            <div class="col-md-1">Novembre</div>
                            <div class="col-md-1">Dicembre</div>
                        </div>
                        <div class="row">
                        <?php
                        $currentYear = (new DateTime()) -> format('Y');


                        for ($i=1;$i<13;$i++) {
                            $sql = 'select sum(amountPayment) as amountPayment from BillRegistryTimeTable where MONTH(dateEstimated)=' . $i . ' and YEAR(dateEstimated)=' . $currentYear;
                            $resultTotalPayment = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
                            foreach ($resultTotalPayment as $sumPayment) {
                                echo '<div class="col-md-1">' . money_format('%.2n',$sumPayment['amountPayment']) . ' &euro;</div>';
                            }
                        }
                        ?>


                        </div>

                    </div>
                </div>
            </div>
            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped responsive" width="100%"
                               data-datatable-name="bill_registryinvoice_list"
                               data-controller="BillRegistryInvoiceListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="100, 200, 500">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Codice
                                </th>
                                <th data-slug="companyName"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Cliente
                                </th>
                                <th data-slug="invoiceNumber"
                                    data-searchable="true"
                                    data-orderable="true" class="center">invoiceNumber
                                </th>
                                <th data-slug="invoiceDate"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Data Fattura
                                </th>
                                <th data-slug="netPrice"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Importo Netto
                                </th>
                                <th data-slug="vat"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Iva
                                </th>
                                <th data-slug="grossTotal"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Totale
                                </th>
                                <th data-slug="typePayment"
                                    data-is-visible="true"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Tipo Pagamento
                                </th>
                                <th data-slug="isPaid"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Pagata
                                </th>
                                <th data-slug="isSent"
                                    data-is-visible="false"
                                    data-searchable="false"
                                    data-orderable="false" class="center">Inviata
                                </th>
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
    <bs-toolbar-group data-group-label="Gestione Fatture">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-cog"
                data-permission="/admin/product/add"
                data-event="bs.invoice.generate"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Genera Fatture "
                data-placement="bottom">
        </bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-plus-circle"
                data-permission="/admin/product/add"
                data-event="bs.invoice.add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Aggiungi Fattura"
                data-placement="bottom">
        </bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-print"
                data-permission="/admin/product/add"
                data-event="bs.invoice.print"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="stampa Fattura"
                data-placement="bottom">
        </bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>