<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui','forms'],$page); ?>
    <title>99Monkeys - <?php echo $page->getTitle(); ?></title>
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
                    <div class="col-md-4 col-md-offset-4 alert-container closed"></div>
                </div>
            </div>

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h5>Modifica Listino</h5>
                    </div>
                    <div class="panel-body">
                        <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post"
                              autocomplete="off">
                            <div class="row">
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="form-group form-group-default">
                                                ​<input type="hidden" class="form-control" id="id" name="id"
                                                        value="<?php echo $priceId ?>"/>
                                                <label for="name">Nome Listino</label>
                                                ​<input type="text" class="form-control" id="name" name="name"
                                                        required="required"
                                                        placeholder="Inserisci il nome del listino"
                                                        value="<?php echo $priceList->name ?>"/>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="shopId">Shop del Listino</label>
                                                <select id="shopId" name="shopId"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona la Lista"
                                                        data-init-plugin="selectize">
                                                    <option value="">seleziona</option>
                                                    <?php
                                                    foreach ($shops as $shop) {
                                                        if ($shop->id == $shopId) {
                                                            echo '<option selected="selected" value="' . $shop->id . '">' . $shop->name . '</option>';
                                                        } else {
                                                            echo '<option value="' . $shop->id . '">' . $shop->name . '</option>';
                                                        }
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group form-group-default">
                                                <label for="dateStart">Valido da</label>
                                                <input type="datetime-local" class="form-control" id="dateStart"
                                                       name="dateStart"
                                                       value="<?php echo (new DateTime($priceList->dateStart))->format('Y-m-d\TH:i'); ?>"/>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group form-group-default">
                                                <label for="dateEnd">Valido fino a</label>
                                                <input type="datetime-local" class="form-control" id="dateEnd"
                                                       name="dateEnd"
                                                       value="<?php echo (new DateTime($priceList->dateEnd))->format('Y-m-d\TH:i'); ?>"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group form-group-default">
                                                <label for="typeVariation"> Tipo Variazione</label>
                                                ​<select id="typeVariation" name="typeVariation"
                                                         class="full-width selectpicker"
                                                         placeholder="Seleziona la Lista"
                                                         data-init-plugin="selectize">
                                                    <?php if($priceList->typeVariation==1){
                                                    echo '<option selected="selected" value="1">Sconto</option>';
                                                    echo '<option value="2">Maggiorazione</option>';
                                                    }else{
                                                        echo '<option  value="1">Sconto</option>';
                                                    echo '<option  selected="selected" value="2">Maggiorazione</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group form-group-default">
                                                <label for="variation">Variazione %</label>
                                                <input type="text" id="variation" name="variation"
                                                value="<?php echo $priceList->variation ?>"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group form-group-default">
                                                <label for="typeVariationSale"> Tipo Variazione Sconti</label>
                                                ​<select id="typeVariationSale" name="typeVariationSale"
                                                         class="full-width selectpicker"
                                                         placeholder="Seleziona la Lista"
                                                         data-init-plugin="selectize">
                                                    <?php if($priceList->typeVariationSale==1){
                                                        echo '<option selected="selected" value="1">Sconto</option>';
                                                        echo '<option value="2">Maggiorazione</option>';
                                                    }else{
                                                        echo '<option  value="1">Sconto</option>';
                                                        echo '<option  selected="selected" value="2">Maggiorazione</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group form-group-default">
                                                <label for="variationSale">Variazione Sconti %</label>
                                                <input type="text" id="variationSale" name="variationSale" value="<?php echo $priceList->variationSale ?>"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php include "parts/footer.php"; ?>
                                            </div>
                                        </div>
                                        <?php include "parts/bsmodal.php"; ?>
                                        <?php include "parts/alert.php"; ?>
                                        <bs-toolbar class="toolbar-definition">
                                            <bs-toolbar-group data-group-label="">
                                                <bs-toolbar-button
                                                        data-tag="a"
                                                        data-icon="fa-floppy-o"
                                                        data-permission="/admin/product/add"
                                                        data-event="bs.price.list.edit"
                                                        data-class="btn btn-default"
                                                        data-rel="tooltip"
                                                        data-title="Salva"
                                                        data-placement="bottom"
                                                ></bs-toolbar-button>
                                            </bs-toolbar-group>
                                        </bs-toolbar>
</body>
</html>