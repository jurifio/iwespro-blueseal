<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui','forms'],$page); ?>
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
                    <div class="col-md-4 col-md-offset-4 alert-container closed"></div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel-heading clearfix">
                            <h5 class="m-t-12">Informazioni di base</h5>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <input type="hidden" name="billRegistryGroupProductId" id="billRegistryGroupProductId"
                                       value="<?php echo $brgp->id; ?>"
                                <div class="form-group form-group-default">
                                    <label for="groupCodeProduct">Codice Gruppo Prodotti</label>
                                    <input id="groupCodeProduct" autocomplete="off" type="text"
                                           class="form-control" name="groupCodeProduct"
                                           value="<?php echo $brgp->codeProduct; ?>"
                                    />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="groupNameProduct">Nome Gruppo Prodotti</label>
                                    <input id="groupNameProduct" autocomplete="off" type="text"
                                           class="form-control" name="groupNameProduct"
                                           value="<?php echo $brgp->name; ?>"
                                    />
                                </div>
                            </div>
                            <div class="col-md-4">

                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="groupBillRegistryCategoryProductId">Seleziona La Categoria </label>
                                    <select id="groupBillRegistryCategoryProductId"
                                            name="groupBillRegistryCategoryProductId"
                                            class="full-width selectpicker"
                                            placeholder="Seleziona la Lista"
                                            data-init-plugin="selectize">
                                        <?php foreach ($brg as $category) {

                                            if ($category->id == $brgp->billRegistryCategoryProductId) {
                                                echo '<option value="' . $category->id . '" selected="selected">' . $category->name . '</option>';
                                            } else {
                                                echo '<option value="' . $category->id . '">' . $category->name . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <input type="hidden" name="typeTaxesId" id="typeTaxesId"
                                       value="<?php echo $brgp->billRegistryTypeTaxesId; ?>"
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="groupBillRegistryTypeTaxesId">Seleziona L'aliquota </label>
                                    <select id="groupBillRegistryTypeTaxesId" name="groupBillRegistryTypeTaxesId"
                                            class="full-width selectpicker"
                                            placeholder="Seleziona la Lista"
                                            data-init-plugin="selectize">
                                        <?php foreach ($brtt as $taxes) {

                                            if ($taxes->id == $brgp->billRegistryTypeTaxesId) {
                                                echo '<option value="' . $taxes->id . '" selected="selected">' . $taxes->description . '</option>';
                                            } else {
                                                echo '<option value="' . $taxes->id . '">' . $taxes->description . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="groupIsActive">attivo</label>
                                    <?php if ($brgp->isActive == 1) {
                                        echo '<input type="checkbox" checked class="form-control" id="groupIsActive" name="groupIsActive">';
                                    } else {
                                        echo '<input type="checkbox" class="form-control" id="groupIsActive" name="groupIsActive">';
                                    } ?>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="groupUm">Unita di misura</label>
                                    <input id="groupUm" autocomplete="off" type="text"
                                           class="form-control" name="groupUm"
                                           value="<?php echo $brgp->um;?>"
                                    />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-group-default">
                                    <label for="groupDescription">Descrizione</label>
                                    <input id="groupDescription" autocomplete="off" type="text"
                                           class="form-control" name="groupDescription" value="<?php echo $brgp->description;?>"
                                    />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="groupProductType">Seleziona il tipo Gruppo Prodotto </label>
                                    <select id="groupProductType" name="groupProductType"
                                            class="full-width selectpicker"
                                            placeholder="Seleziona la Lista"
                                            data-init-plugin="selectize">
                                        <?php
                                        $productSel='';
                                        $moduleSel='';
                                        $serviceSel='';
                                        if ($brgp->productType=='Service'){
                                         $serviceSel='selected="selected"';
                                        }elseif($brgp->productType=='Product') {
                                            $productSel='selected="selected"';
                                        }elseif($brgp->productType=='Module'){
                                            $moduleSel='selected="selected"';
                                        }
                                        ?>
                                        <option  <?php echo  $serviceSel;?> value="Service">Servizio</option>
                                        <option <?php echo  $productSel;?> value="Product">Prodotto</option>
                                        <option<?php echo  $moduleSel;?> value="Module">Modulo</option>
                                    </select>

                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="groupCost">Prezzo acquisto</label>
                                    <input id="groupCost" autocomplete="off" type="text"
                                           class="form-control" name="groupCost"
                                           value="<?php echo number_format($brgp->cost,2,'.','');?>"
                                    />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="groupPrice">Prezzo Vendita</label>
                                    <input id="groupPrice" autocomplete="off" type="text"
                                           class="form-control" name="groupPrice"
                                           value="<?php echo number_format($brgp->price,2,'.','');?>"
                                    />
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
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Operazioni Gruppo Prodotti">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-floppy-o"
                data-permission="/admin/product/add"
                data-event="bs.GroupProductIwes.save"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Salva"
                data-placement="bottom">
        </bs-toolbar-button>
</bs-toolbar>
</body>
</html>