<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui','forms'], $page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
</head>
<body class="fixed-header">
    <?php include "parts/sidebar.php"; ?>
    <div class="page-container">
    <?php include "parts/header.php"; ?>
    <?php include "parts/operations.php"; ?>
        <div class="page-content-wrapper">
            <div class="content sm-gutter">
                <div class="container-fluid container-fixed-lg bg-white">
                    <div class="row">
                        <div class="col-md-4 col-md-offset-4 alert-container closed"></div>
                    </div>
                </div>
                <div class="container-fluid container-fixed-lg bg-white">
                    <div class="panel panel-transparent">
                        <div class="panel-body">
                            <div class="row">
                                <p class="m-t-10">Informazioni Prodotto</p>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group form-group-default">
                                            <label>Codice Prodotto Fornitore</label>
                                            <input style="color: black" disabled autocomplete="off" type="text"
                                                   class="form-control"
                                                   value="<?php echo isset($productEdit->itemno) ? $productEdit->itemno : "" ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group form-group-default">
                                            <label>Codice variante</label>
                                            <input style="color: black" autocomplete="off" type="text" class="form-control"
                                                   value="<?php echo $productEdit->productVariant->name ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group form-group-default">
                                            <label>Brand</label>
                                            <input style="color: black" autocomplete="off" type="text" class="form-control"
                                                   value="<?php echo $productEdit->productBrand->name ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group form-group-default">
                                            <label>Shop</label>
                                            <input style="color: black" autocomplete="off" type="text" class="form-control"
                                                   value="<?php echo isset($shop) ? $shop->name : '---' ?>" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <form id="form-project" role="form" action="" method="POST" autocomplete="off">
                                <input type="hidden" name="id" value="<?php echo $productEdit->id ?>">
                                <input type="hidden" name="productVariantId"
                                       value="<?php echo $productEdit->productVariantId ?>">
                                <input type="hidden" name="shopId" value="<?php echo isset($shop) ? $shop->id : 0 ?>">

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group form-group-default">
                                            <label>In Saldo</label>
                                            <input id="isOnSale" name="isOnSale" type="checkbox" class="form-control form-inline"
                                                   data-target="salePrice" <?php echo !$productEdit->productSku->isEmpty() && $productEdit->productSku->getFirst()->isOnSale == 1 ? 'checked="checked"' : '' ;?> value="1">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group form-group-default">
                                            <label>Costo</label>
                                            <input id="costs" type="text" class="form-control master" data-target="cost"
                                                   value="">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group form-group-default">
                                            <label>Listino</label>
                                            <input id="prices" type="text" class="form-control master" data-target="price"
                                                   value="">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group form-group-default">
                                            <label>Listino Scontato</label>
                                            <input id="salePrices" type="text" class="form-control master"
                                                   data-target="salePrice" value="">
                                        </div>
                                    </div>
                                </div>
                                <h4>Quantità in stock</h4>
                                <?php
                                $i = 1;
                                $prod = $productSizeGroup->productSize->count();
                                foreach ($productSizeGroup->productSize as $val): ?>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default">
                                                <label> Quantità Taglia <?php echo $val->name; ?></label>
                                                <?php $z = false;
                                                if ($productSkuEdit != null) {
                                                    $z = $productSkuEdit->findOneByKey("productSizeId", $val->id);
                                                } ?>
                                                <input tabindex="<?php echo $i++; ?>" type="text" class="form-control"
                                                       name="ProductSku_stockQty_<?php echo $val->id ?>"
                                                       value="<?php echo $z != false ? $z->stockQty : "" ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default required">
                                                <label>Costo Taglia <?php echo $val->name; ?></label>
                                                <input tabindex="<?php echo ($prod * 1) + $i; ?>"
                                                       id="cost_<?php echo $val->id ?>" type="text"
                                                       class="form-control slave" data-target="cost"
                                                       name="ProductSku_value_<?php echo $val->id ?>"
                                                       value="<?php echo $z != false ? $z->value : "" ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default required">
                                                <label>Prezzo Taglia <?php echo $val->name; ?></label>
                                                <input tabindex="<?php echo ($prod * 2) + $i; ?>"
                                                       id="price_<?php echo $val->id ?>" type="text"
                                                       class="form-control slave"
                                                       name="ProductSku_price_<?php echo $val->id ?>"
                                                       value="<?php echo $z != false ? $z->price : "" ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default">
                                                <label>Prezzo Taglia Scontato <?php echo $val->name; ?></label>
                                                <input tabindex="<?php echo ($prod * 3) + $i; ?>"
                                                       id="salePrice_<?php echo $val->id ?>" type="text"
                                                       class="form-control slave"
                                                       name="ProductSku_salePrice_<?php echo $val->id ?>"
                                                       value="<?php echo $z != false ? $z->salePrice : "" ?>">
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <?php if (isset($productSkuEdit) && !$productSkuEdit->isEmpty()): ?>
                                    <input type="hidden" name="ProductSku_edit" value="<?php echo "true" ?>">
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <?php include "parts/footer.php" ?>
    <?php include "parts/bsmodal.php"; ?>
    <?php include "parts/alert.php"; ?>
    <bs-toolbar class="toolbar-definition">
        <bs-toolbar-group data-group-label="Gestione prodotti">
            <bs-toolbar-button
                    data-tag="a"
                    data-icon="fa-floppy-o"
                    data-permission="/admin/product/add"
                    data-class="btn btn-default"
                    data-rel="tooltip"
                    data-event="bs.sku.edit"
                    data-title="Salva"
                    data-placement="bottom"
            ></bs-toolbar-button>
        </bs-toolbar-group>
    </bs-toolbar>
</body>
</html>