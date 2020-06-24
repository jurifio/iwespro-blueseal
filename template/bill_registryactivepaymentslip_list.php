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
                                echo '<div class="col-md-1" style="border-style: solid;  border-color: gainsboro;">Scad: ' . money_format('%.2n',$sumPayment['amountPayment']) . ' &euro;</div>';
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
                            echo '<div class="col-md-1" style="border-style: solid;  border-color: gainsboro;">Sald: ' . money_format('%.2n',$sumPayment['amountPayment']) . ' &euro;</div>';
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
                        echo '<div class="col-md-1" style="border-style: solid;  border-color: gainsboro;"> Da Sald:' . money_format('%.2n',$sumPayment['diff']) . ' &euro;</div>';
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
                                <th data-slug="companyName"
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
                                    data-orderable="true" class="center">Fatture<br>Associate<br>(Importo)</th>
                                <th data-slug="impAmount"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Importo<br>Fatture</th>
                                <th data-slug="impSlip"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Importo<br>Distinta<br>Attiva</th>
                                <th data-slug="paymentBillId"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Pagamento<br> con <br>Distinta<br>Passiva N.</th>
                                <th data-slug="impPassive"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Importo<br>Distinta<br>Passiva</th>
                                <th data-slug="impSaldoPassive"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Saldo<br>Distinta<br>Passiva</th>
                                <th data-slug="negativeAmount"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Importo<br>a Saldo<br>Distinte<br>Attivo-Passivo</th>
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
                data-remote="btn.add.activepaymentbill"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-remote="btn.generate.activepaymentbill"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-hand-lizard-o"
            data-permission="/admin/product/add"
            data-event="btn.assoc.paymentBillNegative"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Associazione Con Distinta Passiva"
            data-placement="bottom">
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-remote="bs.lists.generate.csv"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-remote="bs.paymentBill.submit"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.paymentActiveBill.pay"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-remote="bs.paymentActiveBill.edit"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-remote="bs.paymentBill.print"
        ></bs-toolbar-button>

        <bs-toolbar-button
            data-remote="btn.check.paymentbill"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="btn.send.invoice.movements.activepaymentbill"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="btn.print.invoice.movements.activepaymentbill"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-remote="btn.send.invoice.notice.activepaymentbill"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>

</body>
</html>