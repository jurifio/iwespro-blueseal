<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui','forms','tables'],$page); ?>
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
            <div class="container-fluid container-fixed-lg bg-white" style="padding-top: -180px;">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped responsive" width="100%"
                               data-datatable-name="product_fast_list"
                               data-controller="ProductUserListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-product-zero-quantity="<?php echo $productZeroQuantity?>"
                               data-season="<?php echo $season?>"
                               data-product-status="<?php echo $productStatus?>"
                               data-product-shopid="<?php echo $shopid?>"
                               data-product-BrandId="<?php echo $productBrandId?>"
                               data-length-menu-setup="10,20,50,100, 200, 500, 1000, 2000"
                               data-display-length="20">
                            <thead>
                            <tr>
                                <th data-slug="code"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Codice
                                </th>
                                <th data-slug="shop"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Shop
                                </th>
                                <th data-slug="colorGroup"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Gruppo Colore
                                </th>
                                <th data-slug="colorNameManufacturer"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Colore Produttore
                                </th>
                                <th data-slug="season"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Stagione
                                </th>

                                <th data-slug="cpf"
                                    data-searchable="true"
                                    data-orderable="true" class="center">CPF
                                </th>
                                <!--<th class="center">Gruppo Taglie</th>-->

                                <th data-slug="dummy"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Dummy
                                </th>
                                <th data-slug="hasPhotos"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Ha Foto
                                </th>
                                <th data-slug="productName"
                                    data-searchable="false"
                                    data-orderable="false" class="center">Nome
                                </th>
                                <th data-slug="hasDetails"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Ha Dett.
                                </th>
                                <th data-slug="brand"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Brand
                                </th>
                                <th data-slug="productSizeGroup"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Grup.Taglie Pubblico
                                </th>
                                <th data-slug="categoryId"
                                    data-searchable="true"
                                    data-orderable="true" class="center categoryFilterType">Categorie
                                </th>
                                <th data-slug="tags"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-visible="false" class="center">Tags
                                </th>
                                <th data-slug="hasQty"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Quantità disp.
                                </th>

                                <th data-slug="stock"
                                    data-searchable="true"
                                    data-orderable="false"
                                    class="center">Taglie
                                </th>
                                <th data-slug="activePrice"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Prezzo Attivo
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
    <bs-toolbar-group data-group-label="Attributi Prodotti">
        <bs-toolbar-button
                data-remote="bs.product.merge"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Etichette personalizzate">
        <bs-toolbar-button
                data-remote="bs.product.tag.new.season"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.tag.new.brand"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.special.tag.custom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>