<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php"; ?>
    <?php echo $app->getAssets(['ui','forms','charts'],$page); ?>
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
                            <div class="col-md-12">
                                <h5 class="m-t-10">Tipo di Visualizzazione</h5>
                            </div>
                        </div>
                        <div class="row" align="center">
                            <div class="col-md-12">
                                <button class="openstatelaborate btn-primary" id="currentDay">Giorno Corrente</button>
                                <button class="openstatpending btn-primary" id="currentMonth">Mese Corrente</button>
                                <button class="openstataccettate btn-primary" id="currentYear">Anno Corrrente</button>
                                <button class="openstatconsegnate btn-primary" id="lastDay">Ieri</button>
                                <button class="openstataperte btn-primary" id="lastMonth">Mese Passato</button>
                                <button class="openstatcliccate btn-primary" id="lastYear">Anno Passato</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <div class="row" align="center">
                            <div class="col-md-4" id="textColumnData">
                               <div class="row">
                                   <div class="col-md-3  panel-title text-black" style="border-style: solid;  border-color: grey;" id="divLabelQtyOrder">
                                       totale Numero Ordini
                                   </div>
                                   <div class="col-md-3  panel-title text-black" style="border-style: solid;  border-color: grey;" id="divQtyValueOrder">
                                       <?php echo $stats[0]['quantityOrder'];?>
                                   </div>
                                    <div class="col-md-3 panel-title text-black" style="border-style: solid;  border-color: grey;" id="divLabelValueTotalOrder">
                                        Totale Valore Ordini
                                    </div>
                                    <div class="col-md-3 panel-title text-black" style="border-style: solid;  border-color: grey;" id="divValueValueTotalOrder">
                                        <?php echo $stats[0]['totalOrder'];?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 panel-title text-black" style="border-style: solid;  border-color: grey;" id="divLabelQtyOrderReturn">
                                       Totale numero Resi
                                    </div>
                                    <div class="col-md-3 panel-title text-black" style="border-style: solid;  border-color: grey;" id="divValueQtyOrderReturn">
                                        <?php echo $stats[0]['quantityOrderReturn'];?>
                                    </div>
                                    <div class="col-md-3 panel-title text-black" style="border-style: solid;  border-color: grey;" id="divLabelValueOrderReturn">
                                        Totale importo Resi
                                    </div>
                                    <div class="col-md-3 panel-title text-black" style="border-style: solid;  border-color: grey;" id="divValueValueOrderReturn">
                                        <?php echo $stats[0]['totalOrderReturn'];?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 panel-title text-black" style="border-style: solid;  border-color: grey;" id="divLabelQtyCart">
                                        Totale Carrelli Attivi
                                    </div>
                                    <div class="col-md-3 panel-title text-black" style="border-style: solid;  border-color: grey;" id="divValueQtyCart">
                                        <?php echo $stats[0]['cartTotalNumber'];?>
                                    </div>
                                    <div class="col-md-3 panel-title text-black" style="border-style: solid;  border-color: grey;" id="divLabelValueCart">
                                       Totale Valore Carrelli Attivi
                                    </div>
                                    <div class="col-md-3 panel-title text-black" style="border-style: solid;  border-color: grey;" id="divValueValueCart">
                                        <?php echo $stats[0]['cartTotalValue'];?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 panel-title text-black" style="border-style: solid;  border-color: grey;" id="divLabelQtyCartAbbandoned">
                                        Totale numero  Carrelli Abbandonati
                                    </div>
                                    <div class="col-md-3 panel-title text-black" style="border-style: solid;  border-color: grey;" id="divValueQtyCartAbbandoned">
                                        <?php echo $stats[0]['cartAbandonedTotal'];?>
                                    </div>
                                    <div class="col-md-3 panel-title text-black" style="border-style: solid;  border-color: grey;" id="divLabelValueCartAbbandoned">
                                        Totale valore Carrelli Abbandonati
                                    </div>
                                    <div class="col-md-3 panel-title text-black" style="border-style: solid;  border-color: grey;" id="divValueValueCartAbbandoned">
                                        <?php echo $stats[0]['cartAbandonedTotalValue'];?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 panel-title text-black" style="border-style: solid;  border-color: grey;"" id="divLabelQtyUser">
                                        Totale numero  Utenti iscritti
                                    </div>
                                    <div class="col-md-3 panel-title text-black" style="border-style: solid;  border-color: grey;"" id="divValueQtyUser">
                                        <?php echo $stats[0]['totalUser'];?>
                                    </div>
                                    <div class="col-md-3 panel-title text-black" style="border-style: solid;  border-color: grey;"" id="divLabelQtyUserOnline">
                                        Totale numero  Utenti Online
                                    </div>
                                    <div class="col-md-3 panel-title text-black" style="border-style: solid;  border-color: grey;"" id="divValueQtyUserOnLine">
                                        <?php echo $stats[0]['totalUserOnline'];?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 panel-title text-black" style="border-style: solid;  border-color: grey;"" id="divLabelQtyProduct">
                                       Totale numero Prodotti
                                    </div>
                                    <div class="col-md-6 panel-title text-black" style="border-style: solid;  border-color: grey;"" id="divValueQtyProduct">
                                        <?php echo $stats[0]['totalProduct'];?>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-8" id="chartColumnData">
                                <div class="row" id="orderRow">
                                    <div class="col-md-6" id="s-ordernumber">
                                        <h5 class="m-t-10">Numero Ordini</h5>
                                        <canvas id="ChartQtyOrder"></canvas>
                                    </div>
                                    <div class="col-md-6" id="s-ordervalue">
                                        <h5 class="m-t-10">Valore Ordini</h5>
                                        <canvas id="ChartValueOrder"></canvas>
                                    </div>
                                </div>
                                <div class="row" id="orderRow">
                                    <div class="col-md-6" id="s-orderreturnnumber">
                                        <h5 class="m-t-10">Numero Resi</h5>
                                        <canvas id="ChartQtyOrderReturn"></canvas>
                                    </div>
                                    <div class="col-md-6" id="s-orderreturnvalue">
                                        <h5 class="m-t-10">Valore Resi</h5>
                                        <canvas id="ChartValueOrderReturn"></canvas>
                                    </div>
                                </div>
                                <div class="row" id="cartRow">
                                    <div class="col-md-6" id="s-cartnumber">
                                        <h5 class="m-t-10">Carrelli </h5>
                                        <canvas id="ChartQtyCart"></canvas>
                                    </div>
                                    <div class="col-md-6" id="s-cartvalue">
                                        <h5 class="m-t-10">Valore Carreli</h5>
                                        <canvas id="ChartValueOrderReturn"></canvas>
                                    </div>
                                </div>
                                <div class="row" id="cartRowAbandoned">
                                    <div class="col-md-6" id="s-cartnumberAbandoned">
                                        <h5 class="m-t-10">Carrelli Abbandonati</h5>
                                        <canvas id="ChartQtyCartAbandoned"></canvas>
                                    </div>
                                    <div class="col-md-6" id="s-cartvalueAbandoned">
                                        <h5 class="m-t-10">Valore Carreli Abbandonati</h5>
                                        <canvas id="ChartValueCartAbandoned"></canvas>
                                    </div>
                                </div>
                                <div class="row" id="userRow">
                                    <div class="col-md-6" id="s-usernumber">
                                        <h5 class="m-t-10">totale Utenti Registrati</h5>
                                        <canvas id="ChartQtyUser"></canvas>
                                    </div>
                                    <div class="col-md-6" id="s-usernumberonline">
                                        <h5 class="m-t-10">totale Utenti Online</h5>
                                        <canvas id="ChartQtyUserOnLine"></canvas>
                                    </div>
                                </div>
                                <div class="row" id="productRow">
                                    <div class="col-md-6" id="s-productnumber">
                                        <h5 class="m-t-10">totale Prodotti</h5>
                                        <canvas id="ChartQtyProduct"></canvas>
                                    </div>
                                    <div class="col-md-6" id="s-productaverage">
                                        <h5 class="m-t-10">Giacenza Media</h5>
                                        <canvas id="ChartAverageProduct"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
</div>
<?php include "parts/footer.php" ?>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>

</body>
</html>