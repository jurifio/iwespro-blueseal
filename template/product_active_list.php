<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui', 'forms', 'tables'], $page); ?>
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
                        <div class="row" align="center" style="padding-top: 130px;">
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="season">Visualizza tutte le Stagioni</label>
                                    <?php if($season==1) {
                                        echo '<input type="checkbox" checked="checked" id="season" name="season" value="0"/>';
                                    }else{
                                        echo '<input type="checkbox"  id="season" name="season" value="0"/>';
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="productZeroQuantity">Includi Quantità 0</label>
                                    <?php     if($productZeroQuantity==1) {
                                        echo ' <input type="checkbox" checked="checked" id="productZeroQuantity" name="productZeroQuantity" value="0"/>';
                                    }else{
                                        echo ' <input type="checkbox" id="productZeroQuantity"  name="productZeroQuantity" value="0"/>';
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="productStatus">includi tutti gli stati prodotto</label>
                                    <?php if($productStatus){
                                        echo '<input type="checkbox" checked="checked" id="productStatus"  name="productStatus" value="0"/>';
                                    }else{
                                        echo '<input type="checkbox" id="productStatus"  name="productStatus" value="0"/>';
                                    }?>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="productBrandId">Seleziona il Brand </label>
                                    <select id="productBrandId" name="countryId"
                                            class="full-width selectpicker"
                                            placeholder="Seleziona la Lista"
                                            data-init-plugin="selectize">
                                        <?php echo '<option   value="">Seleziona</option>';
                                        foreach ($productBrand as $brand) {
                                            if ($brand->id == $productBrandId) {
                                                echo '<option  selected="selected" value="' . $brand->id . '">' . $brand->name . '</option>';
                                            } else {
                                                echo '<option value="' . $brand->id . '">' . $brand->name . '</option>';
                                            }
                                        }; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="shopid">Seleziona lo Shop</label>
                                    <select id="shopid" name="shopid"
                                            class="full-width selectpicker"
                                            placeholder="Seleziona la Lista"
                                            data-init-plugin="selectize">
                                        <?php  echo '<option   value="">Seleziona</option>';
                                        foreach ($Shop as $shop) {
                                            if ($shop->id == $shopid) {
                                                echo '<option  selected="selected" value="' . $shop->id . '">' . $shop->name . '</option>';
                                            } else {
                                                echo '<option value="' . $shop->id . '">' . $shop->name . '</option>';
                                            }
                                        }; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button class="success" id="btnsearchplus"  name ='btnsearchplus' type="button"><span  class="fa fa-search-plus"> Esegui Ricerca</span></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped responsive" width="100%"
                               data-datatable-name="product_active_list"
                               data-controller="ProductActiveListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="10,20,50, 100, 200, 500"
                               data-display-length="10">
                            <thead>
                            <tr>
                                <th data-slug="code"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Codice
                                </th>
                                <th data-slug="brand"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Brand
                                </th>
                                <th data-slug="shops"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Shops
                                </th>
                                <th data-slug="status"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="desc"
                                    class="center">Stato
                                </th>
                                <th data-slug="dummyPicture"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Immagine
                                </th>
                                <th data-slug="skus"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Skus
                                </th>
                                <th data-slug="price"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Listino
                                </th>
                                <th data-slug="income"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Valore Vendite
                                </th>
                                <th data-slug="sells"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Qty Venduta
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
        <?php include "parts/footer.php" ?>
    </div>
</div>
<?php //include "parts/modalSearchProducts.php"?>
<?php include "parts/bsmodal.php" ?>
<?php include "parts/alert.php" ?>

<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Gestione prodotti">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-file-o fa-plus"
            data-permission="/admin/product/add"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Aggiungi un nuovo prodotto"
            data-placement="bottom"
            data-href="<?php echo $aggiungi; ?>"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-cloud-upload"
            data-permission="/admin/product/publish"
            data-event="bs.pub.product"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Pubblica prodotti"
            data-placement="bottom"
            data-toggle="modal"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-qrcode"
            data-permission="/admin/product/list"
            data-event="bs.print.aztec"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Stampa aztec"
            data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-clone"
            data-permission="/admin/product/add"
            data-event="bs.dupe.product"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Duplica prodotto"
            data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-archive"
            data-permission="/admin/product/mag"
            data-event="bs.add.sku"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Movimenta"
            data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-camera-retro"
            data-permission="/admin/product/edit"
            data-event="bs.manage.photo"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Gestisci foto"
            data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-trash"
            data-permission="/admin/product/delete"
            data-event="bs.del.product"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Elimina prodotto"
            data-placement="bottom"
            data-toggle="modal"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Lista Prestashop">
        <bs-toolbar-button
                data-remote="bs.insert.product.prestashop"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.sku.insert.ean"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>