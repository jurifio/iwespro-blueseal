<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="https://www.pickyshop.com/it/assets/common.css">
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
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <div class="row" align="center" style="padding-top: 0px;">
                            <div class="col-md-2">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="accountid">Seleziona l'account</label>
                                    <select id="accountid" name="accountid"
                                            class="full-width selectpicker"
                                            placeholder="Seleziona la Lista"
                                            data-init-plugin="selectize">
                                        <?php echo '<option   value="">Seleziona</option>';
                                        foreach ($marketplaceAccount as $account) {
                                            if ($account->id == $marketplaceHasShopId) {
                                                echo '<option  selected="selected" value="' . $account->id . '">' . $account->name . '</option>';
                                            } else {
                                                echo '<option value="' . $account->id . '">' . $account->name . '</option>';
                                            }
                                        }; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button class="success" id="btnsearchplus" name='btnsearchplus' type="button"><span
                                            class="fa fa-search-plus"> Esegui Ricerca</span></button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <?php


                        if (isset($products) && $products->count() < 1):?>
                            <div class="row">
                                <div class="col-xs-12">
                                    <p>Non Ci sono prodotti </p>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="container-fluid">
                            <div class="row product-grid-container">
                                <link itemprop="url"
                                      href="<?php echo \Monkey::app()->baseUrl(false) . \Monkey::app()->router->request()->getUrlPath() ?>"/>
                                <?php $i = 0;
                                foreach ($productsFind as $productFind): ?>
                                <div class="itemListElement col-md-3 col-sm-6 col-xs-6">
                                    <meta itemprop="position" content="<?php echo $i ?>"/>
                                    <?php
                                    /**
                                     * ?>
                                     * @var $product CProduct
                                     * @var $app CWidgetCatalogHelper
                                     * @var $data ->multi CObjectCollection
                                     */

                                    $product = \Monkey::app()->repoFactory->create('Product')->findOneBy(['id' => $productFind['productId'],'productVariantId' => $productFind['productVariantId']]);


                                    $prices = [];
                                    $salePrices = [];
                                    $pricesAreOnARange = false;
                                    $sale = $productFind['isOnSale'];
                                    $sizes = [];
                                    foreach ($product->productPublicSku as $sku) {
                                        if ($sku->stockQty < 1) continue;
                                        if ($sku->price == 0) continue;
                                        $prices[] = $productFind['marketplacePrice'];
                                        $salePrices[] = $productFind['marketplaceSalePrice'];
                                        $nameSize = \Monkey::app()->repoFactory->create('ProductSize')->findOneBy(['id' => $sku->productSizeId]);
                                        $sizes[] = $nameSize->name;

                                    }

                                    if (empty($prices) || ($sale && empty($salePrices))) return;
                                    sort($prices);

                                    if (!(array_sum($prices) / count($prices)) == $prices[0]) {
                                        $pricesAreOnARange = true;
                                    }
                                    if ($sale && !(array_sum($salePrices) / count($salePrices)) == $salePrices[0]) {
                                        $pricesAreOnARange = false;
                                    }
                                    $groupedSkus = $product->productPublicSku;

                                    /** Metatada preparation */
                                    $verboseCategory = $product->getLocalizedProductCategories();
                                    $productName = $product->getName();
                                    $productCpf = $product->printCpf(' ');
                                    $productUrl = $app->productUrl($product);
                                    ?>

                                    <div class="product product-display-box"
                                         data-id="<?php echo $product->printId() ?>"
                                         data-name="<?php echo $productName ?>"
                                         data-list="<?php echo 'catalog'; ?>"
                                         data-brand="<?php echo $product->productBrand->name; ?>"
                                         data-category="<?php echo $verboseCategory ?>"
                                         data-variant="<?php echo $product->productVariant->name ?>"
                                         data-price="<?php echo $prices[0]; ?>"
                                         data-sale-tag="<?php echo $product->tag->findOneByKey('slug','sales') ? 'true' : 'false' ?>"
                                    >
                                        <meta itemprop="category" content="<?php echo $verboseCategory ?>"/>
                                        <meta itemprop="mpn" content="<?php echo $productCpf ?>"/>
                                        <meta itemprop="image"
                                              content="<?php echo $app->image($product->getPhoto(1,\bamboo\domain\entities\CProductPhoto::SIZE_BIG),'amazon',false) ?>"/>
                                        <meta itemprop="name" content="<?php echo $productName ?>"/>
                                        <meta itemprop="url" content="<?php echo $productUrl ?>"/>
                                        <meta itemprop="mainEntityOfPage" content="<?php echo $productUrl ?>"/>
                                        <meta itemprop="color"
                                              content="<?php echo $product->productColorGroup->getLocalizedName() ?>"/>
                                        <meta itemprop="description"
                                              content="<?php echo strip_tags($product->getDescription()) ?>"/>

                                        <?php $sTags = $product->productHasTag; ?>
                                        <div class="product-thumb-info">
                                            <div class="product-thumb-info-content">
                                                <div class="product-thumb-info-image">
                                                    <figure class="animation animated fadeInUp img-holder"
                                                            style="position: relative">

                                                        <img alt="<?php echo $product->productBrand->name . ' - ' . $verboseCategory ?>"
                                                             class="img-responsive"
                                                             src="<?php echo $app->image($product->getPhoto(1,\bamboo\domain\entities\CProductPhoto::SIZE_THUMB),'amazon') ?>"
                                                             data-src="<?php echo $app->image($product->getPhoto(1,\bamboo\domain\entities\CProductPhoto::SIZE_THUMB),'amazon') ?>">
                                                        <?php
                                                        if (!empty($sTags)):
                                                            foreach ($sTags as $pht):
                                                                ?>
                                                                <div class="product-message-parent-bottom">
                                                                    <div class="product-message-special">
                                                                        <?php
                                                                        switch ($pht->position) {
                                                                            case 2: ?>
                                                                                <div class="corner"></div>
                                                                                <div class="testo">
                                                                                    <?php echo $pht->tag->tagTranslation->getFirst()->name; ?>
                                                                                </div>
                                                                                <?php break;
                                                                            case 3: ?>
                                                                                <div class="corner-right"></div>
                                                                                <div class="testo-right">
                                                                                    <?php echo $pht->tag->tagTranslation->getFirst()->name; ?>
                                                                                </div>
                                                                                <?php break;
                                                                        } ?>
                                                                    </div>
                                                                </div>

                                                            <?php endforeach;
                                                        endif; ?>
                                                    </figure>
                                                </div>
                                                <div class="product-thumb-writing">
                                                    <h4>

                                                           <span
                                                                   itemprop="brand"><?php echo($product->productBrand->name); ?></span>
                                                    </h4>
                                                    <span class="item-cat"><small>
                                                                <?php
                                                                if($productFind['titleModified']==1 && $productFind['isOnSale']==1){
                                                                $percSc = number_format(100 * ($prices[0] - $salePrices[0]) / $prices[0],0);
                    $name = $product->productBrand->name
                        . ' Sconto del ' . $percSc . '% da ' . number_format($prices[0],'2','.','') . ' € a ' . number_format($salePrices[0],'2','.','')
                        . ' € ' .
                        $product->itemno
                        . ' ' .
                        $product->productColorGroup->productColorGroupTranslation->findOneByKey('langId',1)->name;

                } else {
                    $name = $product->productCategoryTranslation->findOneByKey('langId',1)->name
                        . ' ' .
                        $product->productBrand->name
                        . ' ' .
                        $product->itemno
                        . ' ' .
                        $product->productColorGroup->productColorGroupTranslation->findOneByKey('langId',1)->name;
                    }?>

                                                            </small><?php echo $name ?></span>
                                                    <div itemprop="offers">
                                                        <link itemprop="availability" href="http://schema.org/InStock"/>
                                                        <meta itemprop="priceCurrency" content="EUR"/>
                                                        <meta itemprop="price" content="<?php echo $prices[0] ?>"/>
                                                        <?php if ($pricesAreOnARange == true): ?>
                                                            <?php if ($sale > 0): ?>
                                                                <span class="oldprice"><?php echo $salePrices[0]; ?> &euro;
                            - <?php echo $salePrices[count($salePrices) - 1]; ?> &euro;</span>&ensp;
                                                                <span class="saleprice"><?php echo $salePrices[0]; ?> &euro;
                                        - <?php echo $salePrices[count($salePrices) - 1]; ?> &euro;</span>
                                                            <?php else: ?>
                                                                <span class="price"><?php echo $prices[0]; ?> &euro;
                                        - <?php echo $prices[count($prices) - 1]; ?> &euro;</span>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <?php if ($sale > 0): ?>
                                                                <span class="oldprice"><?php echo $prices[0]; ?> &euro;</span>&ensp;
                                                                <span
                                                                        class="percentage"><?php echo '- ' . floor(($prices[0] - $salePrices[0]) / $prices[0] * 100); ?>
                                %</span>&ensp;
                                                                <span class="saleprice"><?php echo $salePrices[0]; ?> &euro;</span>

                                                            <?php else: ?>
                                                                <span class="price"><?php echo $prices[0]; ?> &euro;</span>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                        <?php
                                                        $currentUser = \Monkey::app()->getUser();
                                                        $currentUserId = $currentUser->id;
                                                        ?>
                                                    </div>
                                                    <span class="product-message"><?php echo tp('taglie') . implode(' | ',$sizes); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
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
    <bs-toolbar-group data-group-label="Gestione Prodotti">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-exchange"
                data-permission="/admin/product/edit"
                data-event="bs.adding.presta"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Pubblica Prodotti su marketPlace"
                data-placement="bottom"
                data-toggle="modal"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.marketplace.unpublish"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-space-shuttle"
                data-permission="/admin/product/edit"
                data-event="bs.add.presta.product.all"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Pubblica Tutti Prodotti su marketPlace con stato pubblicato"
                data-placement="bottom"
                data-toggle="modal"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Emulatori Jobs">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-flag"
                data-permission="/admin/product/edit"
                data-event="bs.marketplace.prepare.product"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Prepara prodotti per marketplace"
                data-placement="bottom"
                data-toggle="modal"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-flag-checkered"
                data-permission="/admin/product/edit"
                data-event="bs.marketplaceaccountrule.publish.product"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Pubblica prodotti per Account asssociato a Marketplace"
                data-placement="bottom"
                data-toggle="modal"
        ></bs-toolbar-button>

    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>