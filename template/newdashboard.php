<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php"; ?>
    <?php echo $app->getAssets(['ui','forms','charts'],$page); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
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
                            <div class="col-md-6">
                                <h5 class="m-t-6">Filtri</h5>
                            </div>
                            <div class="col-md-6">
                                <h5 class="m-t-6">Filtro personalizzato</h5>
                            </div>
                        </div>
                        <div class="row" align="center">
                            <div class="col-md-5">
                                <button class="openstatelaborate btn-primary" id="currentDay">Giorno Corrente</button>
                                <button class="openstatpending btn-primary" id="currentWeek">Settimana Corrente</button>
                                <button class="openstatpending btn-primary" id="currentMonth">Mese Corrente</button>
                                <button class="openstataccettate btn-primary" id="currentYear">Anno Corrrente</button>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group form-group-default required">
                                    <label for="isCompare">Compara</label>
                                    <?php if($isCompare==1){
                                        echo'<input id="isCompare" autocomplete="off" type="checkbox" class="form-control" name="isCompare" checked="checked" value="1" />';
                                    }else{
                                        echo'<input id="isCompare" autocomplete="off" type="checkbox" class="form-control" name="isCompare"  value="0" />';
                                    }?>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="startDateWork">Applica Dalla Data</label>
                                    <input type="datetime-local" id="startDateWork" class="form-control"
                                           placeholder="filtro da data "
                                           name="startDateWork"
                                           value=""
                                           required="required">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="endDateWork">alla Data</label>
                                    <input type="datetime-local" id="endDateWork" class="form-control"
                                           placeholder="Inserisci la Data di Inizio "
                                           name="endDateWork"
                                           value=""
                                           required="required">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default selectize-enabled">
                                    <button class="success" id="btnsearchplus" name='btnsearchplus' type="button"><span
                                                class="fa fa-search-plus"> Esegui Ricerca</span></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" id="arrayOrder" name="arrayOrder" value="<?php echo $arrayOrder ?>"/>
            <input type="hidden" id="arrayLabelOrder" name="arrayLabelOrder" value="<?php echo $arrayLabelOrder ?>"/>
            <input type="hidden" id="arrayOrderReturn" name="arrayOrderReturn" value="<?php echo $arrayOrderReturn ?>"/>
            <input type="hidden" id="arrayLabelOrderReturn" name="arrayLabelOrderReturn"
                   value="<?php echo $arrayLabelOrderReturn ?>"/>
            <input type="hidden" id="arrayCountOrderReturn" name="arrayCountOrderReturn"
                   value="<?php echo $arrayCountOrderReturn ?>"/>
            <input type="hidden" id="arrayCountOrder" name="arrayCountOrder" value="<?php echo $arrayCountOrder ?>"/>
            <input type="hidden" id="arrayTotalUser" name="arrayTotalUser" value="<?php echo $arrayTotalUser ?>"/>
            <input type="hidden" id="arrayLabelTotalUser" name="arrayLabelTotalUser"
                   value="<?php echo $arrayLabelTotalUser ?>"/>
            <input type="hidden" id="arrayTotalUserOnLine" name="arrayTotalUserOnLine"
                   value="<?php echo $arrayTotalUserOnLine ?>"/>
            <input type="hidden" id="arrayLabelTotalUserOnLine" name="arrayLabelTotalUserOnLine"
                   value="<?php echo $arrayLabelTotalUserOnLine ?>"/>
            <input type="hidden" id="arrayLabelCartTotalNumber" name="arrayLabelCartTotalNumber"
                   value="<?php echo $arrayLabelCartTotalNumber ?>"/>
            <input type="hidden" id="arrayLabelCartAbandonedTotalNumber" name="arrayLabelCartAbandonedTotalNumber"
                   value="<?php echo $arrayLabelCartAbandonedTotalNumber ?>"/>
            <input type="hidden" id="arrayCartTotalNumber" name="arrayCartTotalNumber"
                   value="<?php echo $arrayCartTotalNumber ?>"/>
            <input type="hidden" id="arrayCartAbandonedTotalNumber" name="arrayCartAbandonedTotalNumber"
                   value="<?php echo $arrayCartAbandonedTotalNumber ?>"/>
            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <div class="row" align="center">
                            <div class="col-md-4" id="textColumnData">
                                <div class="row" align="center">
                                    <div class="col-md-12  panel-title text-black"
                                         style="border-style: solid;  border-color: grey;" id="divLabelQtyOrder">
                                        Report per <?php echo $title; ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4  panel-title text-black"
                                         style="border-style: solid;  border-color: grey;" id="divLabelQtyOrder">
                                        Tot N. Ordini
                                    </div>
                                    <div class="col-md-2  panel-title text-black"
                                         style="border-style: solid;  border-color: grey;" id="divQtyValueOrder">
                                        <?php echo $stats[0]['quantityOrder']; ?>
                                    </div>
                                    <div class="col-md-4 panel-title text-black"
                                         style="border-style: solid;  border-color: grey;" id="divLabelValueTotalOrder">
                                        Tot. € Ordini
                                    </div>
                                    <div class="col-md-2 panel-title text-black"
                                         style="border-style: solid;  border-color: grey;" id="divValueValueTotalOrder">
                                        <?php echo number_format($stats[0]['totalOrder'],2,',',''); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 panel-title text-black"
                                         style="border-style: solid;  border-color: grey;" id="divLabelQtyOrderReturn">
                                        Tot. N. Resi
                                    </div>
                                    <div class="col-md-2 panel-title text-black"
                                         style="border-style: solid;  border-color: grey;" id="divValueQtyOrderReturn">
                                        <?php echo $stats[0]['quantityOrderReturn']; ?>
                                    </div>
                                    <div class="col-md-4 panel-title text-black"
                                         style="border-style: solid;  border-color: grey;"
                                         id="divLabelValueOrderReturn">
                                        Tot. € Resi
                                    </div>
                                    <div class="col-md-2 panel-title text-black"
                                         style="border-style: solid;  border-color: grey;"
                                         id="divValueValueOrderReturn">
                                        <?php echo number_format($stats[0]['totalOrderReturn'],2,',',''); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 panel-title text-black"
                                         style="border-style: solid;  border-color: grey;" id="divLabelQtyCart">
                                        Tot. Carrelli Attivi
                                    </div>
                                    <div class="col-md-2 panel-title text-black"
                                         style="border-style: solid;  border-color: grey;" id="divValueQtyCart">
                                        <?php echo $stats[0]['cartTotal']; ?>
                                    </div>
                                    <div class="col-md-4 panel-title text-black"
                                         style="border-style: solid;  border-color: grey;"
                                         id="divLabelQtyCartAbbandoned">
                                        Tot. N.Carrelli Abb.
                                    </div>
                                    <div class="col-md-2 panel-title text-black"
                                         style="border-style: solid;  border-color: grey;"
                                         id="divValueQtyCartAbbandoned">
                                        <?php echo $stats[0]['cartAbandonedTotal']; ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 panel-title text-black"
                                         style="border-style: solid;  border-color: grey;"
                                    " id="divLabelQtyUser">
                                    Tot. N.Utenti iscritti
                                </div>
                                <div class="col-md-2 panel-title text-black"
                                     style="border-style: solid;  border-color: grey;"
                                " id="divValueQtyUser">
                                <?php echo $stats[0]['totalUser']; ?>
                            </div>
                            <div class="col-md-4 panel-title text-black"
                                 style="border-style: solid;  border-color: grey;"
                            " id="divLabelQtyUserOnline">
                            Tot. N. Utenti Online
                        </div>
                        <div class="col-md-2 panel-title text-black" style="border-style: solid;  border-color: grey;"
                        " id="divValueQtyUserOnLine">
                        <?php echo $stats[0]['totalUserOnline']; ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8 panel-title text-black" style="border-style: solid;  border-color: grey;"
                    " id="divLabelQtyProduct">
                    Tot N. Prodotti
                </div>
                <div class="col-md-4 panel-title text-black" style="border-style: solid;  border-color: grey;"
                " id="divValueQtyProduct">
                <?php echo $stats[0]['totalProduct']; ?>
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
        <div class="row" id="orderRowReturn">
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
            <div class="col-md-6" id="s-cartnumberAbandoned">
                <h5 class="m-t-10">Carrelli Abbandonati</h5>
                <canvas id="ChartQtyCartAbandoned"></canvas>
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