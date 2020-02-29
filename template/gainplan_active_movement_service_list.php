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
                                <button class="success" id="lessYear" onclick="lessyear()" type="button"><span  class="fa fa-backward"></span></button>
                            </div>
                            <div id="year" class="col-md-4"><?php echo $currentYear?>
                                <input type="hidden" id="currentYear" name="currentYear" value="<?php echo $currentYear?>"/>
                            </div>
                            <div class="col-md-4">
                                <button class="success" id="moreYear" onclick="moreyear()" type="button"><span  class="fa fa-forward"></span></button>
                            </div>

                        </div>
                        <div class="row" align="center">

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
                            <?php //$currentYear = (new DateTime())->format('Y'); ?>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php $sql = "SELECT count(`I`.`id`)  as `count`   from `GainPlan` I where  typeMovement=2  and
                                            MONTH(I.dateMovement)='1' and YEAR(I.dateMovement)=" . $currentYear;
                                echo \Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'] . ' doc'; ?>
                            </div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php $sql = "SELECT count(`I`.`id`)  as `count`   from `GainPlan` I where  typeMovement=2  and
                                            MONTH(I.dateMovement)='2' and YEAR(I.dateMovement)=" . $currentYear;
                                echo \Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'] . ' doc'; ?>
                            </div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php $sql = "SELECT count(`I`.`id`)  as `count`   from `GainPlan` I where  typeMovement=2  and
                                            MONTH(I.dateMovement)='3' and YEAR(I.dateMovement)=" . $currentYear;
                                echo \Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'] . ' doc'; ?>
                            </div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php $sql = "SELECT count(`I`.`id`)  as `count`   from `GainPlan` I where  typeMovement=2  and
                                            MONTH(I.dateMovement)='4' and YEAR(I.dateMovement)=" . $currentYear;
                                echo \Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'] . ' doc'; ?>
                            </div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php $sql = "SELECT count(`I`.`id`)  as `count`   from `GainPlan` I where  typeMovement=2  and
                                            MONTH(I.dateMovement)='5' and YEAR(I.dateMovement)=" . $currentYear;
                                echo \Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'] . ' doc'; ?>
                            </div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php $sql = "SELECT count(`I`.`id`)  as `count`   from `GainPlan` I where  typeMovement=2  and
                                            MONTH(I.dateMovement)='6' and YEAR(I.dateMovement)=" . $currentYear;
                                echo \Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'] . ' doc'; ?>
                            </div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php $sql = "SELECT count(`I`.`id`)  as `count`   from `GainPlan` I where  typeMovement=2  and
                                            MONTH(I.dateMovement)='7' and YEAR(I.dateMovement)=" . $currentYear;
                                echo \Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'] . ' doc'; ?>
                            </div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php $sql = "SELECT count(`I`.`id`)  as `count`   from `GainPlan` I where  typeMovement=2  and
                                            MONTH(I.dateMovement)='8' and YEAR(I.dateMovement)=" . $currentYear;
                                echo \Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'] . ' doc'; ?>
                            </div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php $sql = "SELECT count(`I`.`id`)  as `count`   from `GainPlan` I where  typeMovement=2  and
                                            MONTH(I.dateMovement)='9' and YEAR(I.dateMovement)=" . $currentYear;
                                echo \Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'] . ' doc'; ?>
                            </div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php $sql = "SELECT count(`I`.`id`)  as `count`   from `GainPlan` I where  typeMovement=2  and
                                            MONTH(I.dateMovement)='10' and YEAR(I.dateMovement)=" . $currentYear;
                                echo \Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'] . ' doc'; ?>
                            </div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php $sql = "SELECT count(`I`.`id`)  as `count`   from `GainPlan` I where  typeMovement=2  and
                                            MONTH(I.dateMovement)='11' and YEAR(I.dateMovement)=" . $currentYear;
                                echo \Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'] . ' doc'; ?>
                            </div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php $sql = "SELECT count(`I`.`id`)  as `count`   from `GainPlan` I where  typeMovement=2  and
                                            MONTH(I.dateMovement)='12' and YEAR(I.dateMovement)=" . $currentYear;
                                echo \Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'] . ' doc'; ?>
                            </div>

                        </div>
                        <div class="row" align="center"">

                        <?php
                        $shopRepo=\Monkey::app()->repoFactory->create('Shop');
                        $orderPaymentMethodRepo=\Monkey::app()->repoFactory->create('OrderPaymentMethod');
                        $countryRepo=\Monkey::app()->repoFactory->create('Country');
                        for ($i=1;$i<13;$i++) {
                            $amount=0;
                            $cost=0;
                            $imp=0;
                            $paymentCommission=0;
                            $shippingCost=0;
                            $profit=0;
                            $commissionSell=0;
                            $transParallel=0;
                            $sql='select * FROM GainPlan where typeMovement=2  AND MONTH(dateMovement)='.$i.' and YEAR(dateMovement)=' . $currentYear ;
                            $resultTotalPayment=\Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
                            $gpmRepo=\Monkey::app()->repoFactory->create('GainPlanPassiveMovement');

                            foreach($resultTotalPayment as $ol) {
                                $gpm=$gpmRepo->findBy(['gainPlanId'=>$ol['id']]);

                                $imp =  $ol['amount']*100/122;


                                $profit+=$imp;

                            }


                            echo '<div class="col-md-1" style="border-style: solid;  border-color: gainsboro;fontsize:xx-small;">' . number_format($imp,'0',',','.') . ' &euro; Fatt</div>';

                        }
                        ?>


                    </div>
                    <div class="row" align="center"
                    ">

                    <?php
                    $shopRepo=\Monkey::app()->repoFactory->create('Shop');
                    $orderPaymentMethodRepo=\Monkey::app()->repoFactory->create('OrderPaymentMethod');
                    $countryRepo=\Monkey::app()->repoFactory->create('Country');
                    for ($i=1;$i<13;$i++) {
                        $amount=0;
                        $cost=0;
                        $imp=0;
                        $paymentCommission=0;
                        $shippingCost=0;
                        $profit=0;
                        $commissionSell=0;
                        $transParallel=0;
                        $sql='select * FROM GainPlan where typeMovement=2  AND MONTH(dateMovement)='.$i.' and YEAR(dateMovement)=' . $currentYear ;
                        $resultTotalPayment=\Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
                        $gpmRepo=\Monkey::app()->repoFactory->create('GainPlanPassiveMovement');

                        foreach($resultTotalPayment as $ol) {
                                        $gpm=$gpmRepo->findBy(['gainPlanId'=>$ol['id']]);
                                        foreach($gpm as $mcost){
                                            $cost+=$mcost->amount;
                                        }


                                        $imp =  $ol['amount']*100/122;


                                        $profit+=$imp-$cost;

                        }


                        echo '<div class="col-md-1" style="border-style: solid;  border-color: gainsboro;fontsize:xx-small;">' . number_format($profit,'0',',','.') . ' &euro; marg</div>';

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
                               data-datatable-name="gainplan_list"
                               data-controller="GainPlanActiveMovementServiceListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="50, 100, 200, 500"
                               data-display-length="50">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true" class="center">id
                                </th>
                                <th data-slug="dateMovement"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="desc"
                                    class="center dataFilterType">Data Movimento
                                </th>
                                <th data-slug="season"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Stagione
                                </th>
                                <th data-slug="invoiceId"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Fattura
                                </th>
                                <th data-slug="customerName"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Cliente/Fornitore
                                </th>
                                <th data-slug="shopId"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Shop Name
                                </th>
                                <th data-slug="isActive"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Attivo
                                </th>
                                <th data-slug="MovementPassiveCollect"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Fattura Contro<BR>Partita/Fornitore
                                </th>
                                <th data-slug="amount"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Importo
                                </th>
                                <th data-slug="imp"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Imponibile
                                </th>
                                <th data-slug="cost"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Costo
                                </th>
                                <th data-slug="profit"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Margine
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
    <bs-toolbar-group data-group-label="Gestione Gain Plan">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-file-o fa-plus"
                data-permission="allShops"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Aggiungi  un nuovo acquisto  manuale"
                data-placement="bottom"
                data-href="/blueseal/registri/gainplan-passivo/aggiungi"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-handshake-o"
                data-permission="allShops"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Associa un acquisto esistente ad una vendita"
                data-placement="bottom"
                data-event="bs.gainplanassociations.cost"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-toggle-off"
                data-permission="allShops"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Disabilita Vendita (!!!)"
                data-placement="bottom"
                data-event="bs.gainplanactivemovement.disable"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-toggle-on"
                data-permission="allShops"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Abilita Vendita (!!!)"
                data-placement="bottom"
                data-event="bs.gainplanactivemovement.enable"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Estrai">
        <bs-toolbar-button
            data-remote="bs.lists.generate.csv"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>