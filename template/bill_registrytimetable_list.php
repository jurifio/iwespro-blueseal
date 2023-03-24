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
                            <div class="col-md-4">
                                <button class="success" id="lessYear" onclick="lessyear()" type="button"><span
                                        class="fa fa-backward"></span></button>
                            </div>
                            <div id="year" class="col-md-4"><?php echo $currentYear ?>
                                <input type="hidden" id="currentYear" name="currentYear"
                                       value="<?php echo $currentYear ?>"/>
                            </div>
                            <div class="col-md-4">
                                <button class="success" id="moreYear" onclick="moreyear()" type="button"><span
                                        class="fa fa-forward"></span></button>
                            </div>

                        </div>
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
                                <?php  $sql="SELECT count(id)  as `count`   from BillRegistryTimeTable
                                           where MONTH(dateEstimated)='1' and YEAR(dateEstimated)=" . $currentYear;
                                echo 'N.scad: '.\Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'];?>
                            </div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php  $sql="SELECT count(id)  as `count`   from BillRegistryTimeTable
                                           where MONTH(dateEstimated)='2' and YEAR(dateEstimated)=" . $currentYear;
                                echo 'N.scad: '.\Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'];?>
                            </div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php  $sql="SELECT count(id)  as `count`   from BillRegistryTimeTable
                                           where MONTH(dateEstimated)='3' and YEAR(dateEstimated)=" . $currentYear;
                                echo 'N.scad: '.\Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'];?>
                            </div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php  $sql="SELECT count(id)  as `count`   from BillRegistryTimeTable
                                           where MONTH(dateEstimated)='4' and YEAR(dateEstimated)=" . $currentYear;
                                echo 'N.scad: '.\Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'];?>
                            </div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php  $sql="SELECT count(id)  as `count`   from BillRegistryTimeTable
                                           where MONTH(dateEstimated)='5' and YEAR(dateEstimated)=" . $currentYear;
                                echo 'N.scad: '.\Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'];?>
                            </div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php  $sql="SELECT count(id)  as `count`   from BillRegistryTimeTable
                                           where MONTH(dateEstimated)='6' and YEAR(dateEstimated)=" . $currentYear;
                                echo 'N.scad: '.\Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'];?>
                            </div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php  $sql="SELECT count(id)  as `count`   from BillRegistryTimeTable
                                           where MONTH(dateEstimated)='7' and YEAR(dateEstimated)=" . $currentYear;
                                echo 'N.scad: '.\Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'];?>
                            </div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php  $sql="SELECT count(id)  as `count`   from BillRegistryTimeTable
                                           where MONTH(dateEstimated)='8' and YEAR(dateEstimated)=" . $currentYear;
                                echo 'N.scad: '.\Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'];?>
                            </div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php  $sql="SELECT count(id)  as `count`   from BillRegistryTimeTable
                                           where MONTH(dateEstimated)='9' and YEAR(dateEstimated)=" . $currentYear;
                                echo 'N.scad: '.\Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'];?>
                            </div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php  $sql="SELECT count(id)  as `count`   from BillRegistryTimeTable
                                           where MONTH(dateEstimated)='10' and YEAR(dateEstimated)=" . $currentYear;
                                echo 'N.scad: '.\Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'];?>
                            </div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php  $sql="SELECT count(id)  as `count`   from BillRegistryTimeTable
                                           where MONTH(dateEstimated)='11' and YEAR(dateEstimated)=" . $currentYear;
                                echo 'N.scad: '.\Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'];?>
                            </div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php  $sql="SELECT count(id)  as `count`   from BillRegistryTimeTable
                                           where MONTH(dateEstimated)='12' and YEAR(dateEstimated)=" . $currentYear;
                                echo 'N.scad: '.\Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'];?>
                            </div>
                        </div>
                        <div class="row" align="center"">
                        <?php
                        for ($i=1;$i<13;$i++) {
                            $sql = 'select sum(amountPayment) as amountPayment from BillRegistryTimeTable where MONTH(dateEstimated)=' . $i . ' and YEAR(dateEstimated)=' . $currentYear;
                            $resultTotalPayment = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
                            foreach ($resultTotalPayment as $sumPayment) {
                                echo '<div class="col-md-1" style="border-style: solid;  border-color: gainsboro;">Scad: ' . number_format($sumPayment['amountPayment']) . ' &euro;</div>';
                            }
                        }
                        ?>


                    </div>
                    <div class="row" align="center"">
                    <?php
                    for ($i=1;$i<13;$i++) {
                        $sql = 'select sum(amountPaid) as amountPayment from BillRegistryTimeTable where MONTH(dateEstimated)=' . $i . ' and YEAR(dateEstimated)=' . $currentYear;
                        $resultTotalPayment = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
                        foreach ($resultTotalPayment as $sumPayment) {
                            echo '<div class="col-md-1" style="border-style: solid;  border-color: gainsboro;">Sald: ' . number_format($sumPayment['amountPayment']) . ' &euro;</div>';
                        }
                    }
                    ?>


                </div>
                <div class="row" align="center"">
                <?php
                for ($i=1;$i<13;$i++) {
                    $sql = 'select (sum(amountPayment)-sum(amountPaid)) as diff from BillRegistryTimeTable where MONTH(dateEstimated)=' . $i . ' and YEAR(dateEstimated)=' . $currentYear;
                    $resultTotalPayment = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
                    foreach ($resultTotalPayment as $sumPayment) {
                        echo '<div class="col-md-1" style="border-style: solid;  border-color: gainsboro;"> Da Sald:' . number_format($sumPayment['diff']) . ' &euro;</div>';
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
                               data-datatable-name="bill_registrytimetable_list"
                               data-controller="BillRegistryTimeTableListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="100, 200, 500">
                            <thead>
                            <tr>
                                <th data-slug="companyName"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Cliente
                                </th>

                                <th data-slug="invoiceNumber"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Numero<br>Fattura
                                </th>
                                <th data-slug="invoiceDate"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Data<br>Fattura
                                </th>

                                <th data-slug="grossTotal"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Importo<br>Fattura
                                </th>
                                <th data-slug="typePayment"
                                    data-is-visible="true"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Tipo<br>Pagamento
                                </th>
                                <th data-slug="dateEstimated"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Data<br>Scadenza
                                </th>

                                <th data-slug="numberSlip"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Prog.<br>Distinta<br>Attiva
                                </th>
                                <th data-slug="amountPayment"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Importo<br>Scadenza
                                </th>
                                <th data-slug="paymentBillId"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Pagamento<br> con <br>Distinta<br>Passiva N.</th>
                                <th data-slug="amountNegative"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Importo<br>Distinta<br>Passiva</th>
                                <th data-slug="amountPaid"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Importo<br>Pagato
                                </th>
                                <th data-slug="datePayment"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Data<br>Pagamento
                                </th>
                                <th data-slug="restPaid"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Importo<br>da Saldare
                                </th>
                                <th data-slug="status"
                                    data-is-visible="false"
                                    data-searchable="false"
                                    data-orderable="false" class="center">Stato
                                </th>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Codice
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
    <bs-toolbar-group data-group-label="Gestione Scadenziario">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-calendar-check-o"
                data-permission="/admin/product/add"
                data-event="bs.timetable.modify"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Registra Pagamento"
                data-placement="bottom">
        </bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>