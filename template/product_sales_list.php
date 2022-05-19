<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui', 'forms', 'tables'], $page); ?>
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
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <div class="row" align="center" style="padding-top: 130px;">
                            <div class="col-md-1">
                                <div class="form-group form-group-default">
                                    <label for="stored">Visualizza  lo storico</label>
                                    <?php if($stored==1) {
                                        echo '<input type="checkbox" checked="checked" id="stored" name="stored" value="0"/>';
                                    }else{
                                        echo '<input type="checkbox"  id="stored" name="stored" value="0"/>';
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-1">
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

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped responsive" width="100%"
                               data-datatable-name="product_sales_list" data-controller="ProductSalesListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-product-zero-quantity="<?php echo $productZeroQuantity?>"
                               data-season="<?php echo $season?>"
                               data-product-status="<?php echo $productStatus?>"
                               data-product-shopid="<?php echo $shopid?>"
                               data-product-stored="<?php echo $stored?>"
                               data-product-BrandId="<?php echo $productBrandId?>"
                               data-length-menu-setup="10,20,50,100, 200, 500, 1000, 2000"
                               data-display-length="10">
                            <thead>
                            <tr>
                                <th data-slug="code"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Codice</th>
                                <th data-slug="brand"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Brand</th>
                                <th data-slug="CPF"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="asc"
                                    class="center">CPF</th>
                                <th data-slug="slug"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Cat. & Skus</th>
                                <th data-slug="season"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Stagione</th>
                                <th data-slug="variant"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Variante</th>
                                <th data-slug="dummyPicture"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Immagine</th>
                                <th data-slug="shops"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Shops</th>
                                <th data-slug="price"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">R.P|CF</th>
                                <th data-slug="sale"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">R.P.P.</th>
                                <th data-slug="percentage"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">DIS.</th>
                                <th data-slug="isOnSale"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">ScA</th>
                                <th data-slug="friendRevenue"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">CF|M.UP</th>
                                <th data-slug="friendSaleRevenue"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">CFS|M.UPS</th>
                                <th data-slug="friendPreRevenue"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">CFSP|M.UPSP</th>
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
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label='Gestione delle Promozioni'>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-shopping-cart"
            data-permission="/admin/product/edit"
            data-event="bs.sales.set"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Imposta le promozioni"
            data-placement="bottom"
            data-toggle="modal"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-dollar"
            data-permission="/admin/product/edit"
            data-event="bs.sales.price"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Definisci gli sconti"
            data-placement="bottom"
            data-toggle="modal"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-remote="bs.product.price.manage"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>