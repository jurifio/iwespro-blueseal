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
                        <div class="row" align="center">
                            <?php $currentYear = (new DateTime()) -> format('Y');?>
                            <div class="col-md-1" style="border-style: solid;  border-color: grey;">Gennaio</div>
                            <div class="col-md-1" style="border-style: solid;  border-color: grey;">Febbraio</div>
                            <div class="col-md-1" style="border-style: solid;  border-color: grey;">Marzo</div>
                            <div class="col-md-1" style="border-style: solid;  border-color: grey;">Aprile</div>
                            <div class="col-md-1" style="border-style: solid;  border-color: grey;">Maggio</div>
                            <div class="col-md-1" style="border-style: solid;  border-color: grey;">Giugno</div>
                            <div class="col-md-1" style="border-style: solid;  border-color: grey;">Luglio</div>
                            <div class="col-md-1" style="border-style: solid;  border-color: grey;">Agosto</div>
                            <div class="col-md-1" style="border-style: solid;  border-color: grey;">Settembre</div>
                            <div class="col-md-1" style="border-style: solid;  border-color: grey;">Ottobre</div>
                            <div class="col-md-1" style="border-style: solid;  border-color: grey;">Novembre</div>
                            <div class="col-md-1" style="border-style: solid;  border-color: grey;">Dicembre</div>
                        </div>
                        <div class="row" align="center">
                            <?php $currentYear = (new DateTime()) -> format('Y');?>
                            <div class="col-md-1"  style="border-style: solid;  border-color: beige;">
                                <?php  $sql="SELECT count(id)  as `count`   from BillRegistryInvoice 
                                           where MONTH(invoiceDate)='1' and YEAR(invoiceDate)=" . $currentYear;
                                echo \Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'].' doc';?>
                            </div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php  $sql="SELECT count(id)  as `count`   from BillRegistryInvoice 
                                           where MONTH(invoiceDate)='2' and YEAR(invoiceDate)=" . $currentYear;
                                echo \Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'].' doc';?></div>
                        <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php  $sql="SELECT count(id)  as `count`  from BillRegistryInvoice 
                                           where MONTH(invoiceDate)='3' and YEAR(invoiceDate)=" . $currentYear;
                                echo \Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'].' doc';?></div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php  $sql="SELECT count(id)  as `count`   from BillRegistryInvoice 
                                           where MONTH(invoiceDate)='4' and YEAR(invoiceDate)=" . $currentYear;
                                echo \Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'].' doc';?></div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php  $sql="SELECT count(id)  as `count`   from BillRegistryInvoice 
                                           where MONTH(invoiceDate)='5' and YEAR(invoiceDate)=" . $currentYear;
                                echo \Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'].' doc';?></div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php  $sql="SELECT count(id)  as `count`   from BillRegistryInvoice 
                                           where MONTH(invoiceDate)='6' and YEAR(invoiceDate)=" . $currentYear;
                                echo \Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'].' doc';?></div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php  $sql="SELECT count(id)  as `count`   from BillRegistryInvoice 
                                           where MONTH(invoiceDate)='7' and YEAR(invoiceDate)=" . $currentYear;
                                echo \Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'].' doc';?></div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php  $sql="SELECT count(id)  as `count` from BillRegistryInvoice 
                                           where MONTH(invoiceDate)='8' and YEAR(invoiceDate)=" . $currentYear;
                                echo \Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'].' doc';?></div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php  $sql="SELECT count(id)  as `count`   from BillRegistryInvoice 
                                           where MONTH(invoiceDate)='9' and YEAR(invoiceDate)=" . $currentYear;
                                echo \Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'].' doc';?></div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php  $sql="SELECT count(id)  as `count`   from BillRegistryInvoice 
                                           where MONTH(invoiceDate)='10' and YEAR(invoiceDate)=" . $currentYear;
                                echo \Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'].' doc';?></div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php  $sql="SELECT count(id)  as `count`   from BillRegistryInvoice 
                                           where MONTH(invoiceDate)='11' and YEAR(invoiceDate)=" . $currentYear;
                                echo \Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'].' doc';?></div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php  $sql="SELECT count(id)  as `count`  from BillRegistryInvoice 
                                           where MONTH(invoiceDate)='12' and YEAR(invoiceDate)=" . $currentYear;
                                echo \Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'].' doc';?></div>
                        </div>
                        <div class="row" align="center"">
                        <?php
                        for ($i=1;$i<13;$i++) {
                            $sql = 'select sum(amountPayment) as amountPayment from BillRegistryTimeTable where MONTH(dateEstimated)=' . $i . ' and YEAR(dateEstimated)=' . $currentYear;
                            $resultTotalPayment = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
                            foreach ($resultTotalPayment as $sumPayment) {
                                echo '<div class="col-md-1" style="border-style: solid;  border-color: gainsboro;">' . number_format($sumPayment['amountPayment']) . ' &euro;</div>';
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
                                <?php if ($allShops) : ?>
                                <th data-slug="companyName"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Cliente
                                </th>
                                <?php endif; ?>
                                <th data-slug="invoiceNumber"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Numero<br>Fattura
                                </th>
                                <th data-slug="invoiceDate"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Data<br>Fattura
                                </th>
                                <th data-slug="subject"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Oggetto<br>Fattura
                                </th>
                                <th data-slug="netPrice"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Imponibile
                                </th>
                                <th data-slug="vat"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Iva
                                </th>
                                <th data-slug="grossTotal"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Importo<br>Fattura
                                </th>
                                <th data-slug="typePayment"
                                    data-is-visible="true"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Tipo Pagamento
                                </th>
                                <th data-slug="rowPayment"
                                    data-is-visible="true"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Scadenze<br>Distinta
                                </th>

                                <th data-slug="status"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Stato
                                </th>
                                <?php if ($allShops) : ?>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Codice
                                </th>
                                <th data-slug="sendToLegal"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Fatturata<br>Elettronicamente
                                </th>
                                <?php endif; ?>
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
                data-permission="AllShops"
                data-event="bs.invoice.generate"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Genera Fatture "
                data-placement="bottom">
        </bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-plus-circle"
                data-permission="AllShops"
                data-event="bs.invoice.add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Aggiungi Fattura"
                data-placement="bottom">
        </bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-eraser"
            data-permission="AllShops"
            data-event="bs.invoice.delete"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Cancella Fattura"
            data-placement="bottom">
        </bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-recycle"
                data-permission="AllShops"
                data-event="btn.delete.invoice.fromactivepaymentbill"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Disassocia Fatture da Relativa Distinta"
                data-placement="bottom">
        </bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-print"
                data-permission="/admin/product/add"
                data-event="bs.invoice.print"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Stampa Fattura"
                data-placement="bottom">
        </bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-envelope"
                data-permission="AllShops"
                data-event="bs.invoice.sendEmail"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Invia Fattura di Cortesia al Cliente"
                data-placement="bottom">
        </bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-gavel"
                data-permission="AllShops"
                data-event="bs.invoice.sendLegal"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Invia a Fatture in Cloud"
                data-placement="bottom">
        </bs-toolbar-button>
        <bs-toolbar-button
            data-remote="btn.generate.selectactivepaymentbill"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>