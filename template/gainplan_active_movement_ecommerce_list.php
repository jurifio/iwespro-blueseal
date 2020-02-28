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
                                <?php $sql = "SELECT count(`o`.`id`)  as `count`   from `Order` `o` join `Invoice` `I` on `o`.`id`=`I`.`orderId`    
                                           where MONTH(I.invoiceDate)='1' and YEAR(I.invoiceYear)=" . $currentYear;
                                echo \Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'] . ' doc'; ?>
                            </div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php $sql = "SELECT count(`o`.`id`)  as `count`   from `Order` `o` join `Invoice` `I` on `o`.`id`=`I`.`orderId`    
                                           where MONTH(I.invoiceDate)='2' and YEAR(I.invoiceYear)=" . $currentYear;
                                echo \Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'] . ' doc'; ?></div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php $sql = "SELECT count(`o`.`id`)  as `count`   from `Order` `o` join `Invoice` `I` on `o`.`id`=`I`.`orderId`    
                                           where MONTH(I.invoiceDate)='3' and YEAR(I.invoiceYear)=" . $currentYear;
                                echo \Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'] . ' doc'; ?></div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php $sql = "SELECT count(`o`.`id`)  as `count`   from `Order` `o` join `Invoice` `I` on `o`.`id`=`I`.`orderId`    
                                           where MONTH(I.invoiceDate)='4' and YEAR(I.invoiceYear)=" . $currentYear;
                                echo \Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'] . ' doc'; ?></div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php $sql = "SELECT count(`o`.`id`)  as `count`   from `Order` `o` join `Invoice` `I` on `o`.`id`=`I`.`orderId`    
                                           where MONTH(I.invoiceDate)='5' and YEAR(I.invoiceYear)=" . $currentYear;
                                echo \Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'] . ' doc'; ?></div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php $sql = "SELECT count(`o`.`id`)  as `count`   from `Order` `o` join `Invoice` `I` on `o`.`id`=`I`.`orderId`    
                                           where MONTH(I.invoiceDate)='6' and YEAR(I.invoiceYear)=" . $currentYear;
                                echo \Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'] . ' doc'; ?></div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php $sql = "SELECT count(`o`.`id`)  as `count`   from `Order` `o` join `Invoice` `I` on `o`.`id`=`I`.`orderId`    
                                           where MONTH(I.invoiceDate)='7' and YEAR(I.invoiceYear)=" . $currentYear;
                                echo \Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'] . ' doc'; ?></div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php $sql = "SELECT count(`o`.`id`)  as `count`   from `Order` `o` join `Invoice` `I` on `o`.`id`=`I`.`orderId`    
                                           where MONTH(I.invoiceDate)='8' and YEAR(I.invoiceYear)=" . $currentYear;
                                echo \Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'] . ' doc'; ?></div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php $sql = "SELECT count(`o`.`id`)  as `count`   from `Order` `o` join `Invoice` `I` on `o`.`id`=`I`.`orderId`    
                                           where MONTH(I.invoiceDate)='9' and YEAR(I.invoiceYear)=" . $currentYear;
                                echo \Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'] . ' doc'; ?></div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php $sql = "SELECT count(`o`.`id`)  as `count`   from `Order` `o` join `Invoice` `I` on `o`.`id`=`I`.`orderId`    
                                           where MONTH(I.invoiceDate)='10' and YEAR(I.invoiceYear)=" . $currentYear;
                                echo \Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'] . ' doc'; ?></div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php $sql = "SELECT count(`o`.`id`)  as `count`   from `Order` `o` join `Invoice` `I` on `o`.`id`=`I`.`orderId`    
                                           where MONTH(I.invoiceDate)='11' and YEAR(I.invoiceYear)=" . $currentYear;
                                echo \Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'] . ' doc'; ?></div>
                            <div class="col-md-1" style="border-style: solid;  border-color: beige;">
                                <?php $sql = "SELECT count(`o`.`id`)  as `count`   from `Order` `o` join `Invoice` `I` on `o`.`id`=`I`.`orderId`    
                                           where MONTH(I.invoiceDate)='12' and YEAR(I.invoiceYear)=" . $currentYear;
                                echo \Monkey::app()->dbAdapter->query($sql,[])->fetchAll()[0]['count'] . ' doc '; ?></div>
                        </div>
                        <div class="row" align="center"
                        ">

                        <?php
                        $shopRepo=\Monkey::app()->repoFactory->create('Shop');
                        $orderPaymentMethodRepo=\Monkey::app()->repoFactory->create('OrderPaymentMethod');
                        $countryRepo=\Monkey::app()->repoFactory->create('Country');
                        for ($i=1;$i<13;$i++) {
                            $netTotal=0;

                            $sql='select * FROM OrderLine where (`status` NOT LIKE \'%ORD_CANCEL%\' AND `status` NOT LIKE \'%ORD_FRND_CANC%\' AND `status` NOT LIKE \'%ORD_MISSNG%\')  and MONTH(creationDate)='.$i.' and YEAR(creationDate)=' . $currentYear ;
                            $resultTotalPayment=\Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
                            foreach($resultTotalPayment as $ol) {

                                $order = \Monkey::app()->repoFactory->create('Order')->findOneBy(['id' => $ol['orderId']]);

                                if ( $ol['netPrice']!=null) {


                                    $netTotal += $ol['netPrice'];


                                }
                            }




                            echo '<div class="col-md-1" style="border-style: solid;  border-color: gainsboro;fontsize:xx-small;">' . number_format($netTotal,'0',',','.') . ' &euro; ord</div>';

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
                            $sql='select * FROM OrderLine where (`status` NOT LIKE \'%ORD_CANCEL%\' AND `status` NOT LIKE \'%ORD_FRND_CANC%\' AND `status` NOT LIKE \'%ORD_MISSNG%\')  AND MONTH(creationDate)='.$i.' and YEAR(creationDate)=' . $currentYear ;
                            $resultTotalPayment=\Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
                            foreach($resultTotalPayment as $ol) {

                                $order = \Monkey::app()->repoFactory->create('Order')->findOneBy(['id' => $ol['orderId']]);

                                if ( $ol['netPrice']!=null)  {

                                    $orderPaymentMethod = $orderPaymentMethodRepo->findOneBy(['id' => $order->orderPaymentMethodId]);
                                    $paymentCommissionRate = $orderPaymentMethod->paymentCommissionRate;
                                    if ($ol['remoteShopSellerId'] == 44) {

                                        $amount = $ol['netPrice'];
                                        $imp =$ol['netPrice'] - $ol['vat'];
                                        $cost = $ol['friendRevenue'];
                                        $paymentCommission = ($ol['netPrice'] / 100) * $paymentCommissionRate;
                                        $transParallel =0;
                                        $shippingCost = $ol['shippingCharge'];
                                        $commissionSell=0;
                                        $profit+=$imp-$shippingCost-$cost-$paymentCommission;


                                    } else {
                                        if ($ol['shopId'] != $ol['remoteShopSellerId']) {
                                            $shop = $shopRepo->findOneBy(['id' => $ol['shopId']]);
                                            $paralellFee = $shop->paralellFee;
                                            $imp =  $ol['netPrice'] - $ol['vat'];
                                            $par=$ol['netPrice']/100*$paralellFee;
                                            $transParallel=(($ol['netPrice']-$par)*100/122)-$ol['friendRevenue'];
                                            $amount = $ol['netPrice'];
                                            $paymentCommission = ($ol['netPrice'] / 100) * $paymentCommissionRate;
                                            $cost = 0;
                                            $shippingCost=$ol['shippingCharge'];
                                            $commissionSell=$ol['netPrice'] * 0.11;
                                            $profit+=$commissionSell+$transParallel-$paymentCommission-$shippingCost;


                                        } else {
                                            $shop = $shopRepo->findOneBy(['id' => $ol['shopId']]);
                                            $paralellFee = $shop->paralellFee;
                                            $imp =  $ol['netPrice'] - $ol['vat'];
                                            $transParallel=0;
                                            $amount = $ol['netPrice'];
                                            $paymentCommission = ($ol['netPrice'] / 100) * $paymentCommissionRate;
                                            $cost = 0;
                                            $shippingCost=$ol['shippingCharge'];
                                            $commissionSell=$ol['netPrice'] * 0.11;
                                            $profit+=$commissionSell-$paymentCommission-$shippingCost;

                                        }
                                    }
                                }else{
                                    continue;
                                }
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
                           data-controller="GainPlanActiveMovementEcommerceListAjaxController"
                           data-url="<?php echo $app->urlForBluesealXhr() ?>"
                           data-inner-setup="true"
                           data-length-menu-setup="50, 100, 200, 500"
                           data-display-length="50">
                        <thead>
                        <tr>

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
                            <th data-slug="orderId"
                                data-searchable="true"
                                data-orderable="true" class="center">Numero Ordine
                            </th>
                            <th data-slug="invoiceId"
                                data-searchable="true"
                                data-orderable="true" class="center">Fattura
                            </th>
                            <th data-slug="customerName"
                                data-searchable="true"
                                data-orderable="true" class="center">Cliente/Fornitore
                            </th>
                            <th data-slug="shopName"
                                data-searchable="true"
                                data-orderable="true" class="center">Shop Name
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
                            <th data-slug="deliveryCost"
                                data-searchable="true"
                                data-orderable="true" class="center">Costi<br>Sped.
                            </th>
                            <th data-slug="paymentCommission"
                                data-searchable="true"
                                data-orderable="true" class="center">Commissioni<br>Pagamento
                            </th>
                            <th data-slug="commissionSell"
                                data-searchable="true"
                                data-orderable="true" class="center">Commissioni<br>Venduto
                            </th>
                            <th data-slug="transParallel"
                                data-searchable="true"
                                data-orderable="true" class="center">Trans<br>Parallela
                            </th>
                            <th data-slug="profit"
                                data-searchable="true"
                                data-orderable="true" class="center">Margine
                            </th>
                            <th data-slug="id"
                                data-searchable="true"
                                data-orderable="true" class="center">id
                            </th>
                            <th data-slug="isActive"
                                data-searchable="true"
                                data-orderable="true" class="center">Attivo
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
        <bs-toolbar-button
            data-remote="bs.lists.generate.csv"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>