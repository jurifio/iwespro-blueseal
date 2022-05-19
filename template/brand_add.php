<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php"?>
    <?php echo $app->getAssets(['ui','forms'], $page); ?>
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
                <div class="panel panel-transparent">
                    <div class="panel-heading">
                        <h5>Inserisci un nuovo brand</h5>
                    </div>
                    <div class="panel-body">
                        <form id="form-project" role="form" action="" method="POST" autocomplete="on">
                            <div class="row clearfix">
                                <div class="col-md-4">
                                    <div class="form-group form-group-default required">
                                        <label for="ProductBrand_name">Nome Brand</label>
                                        <input type="text" class="form-control" id="ProductBrand_name" name="ProductBrand_name" value="<?php echo isset($brandEdit) ? $brandEdit->name : "" ?>" required/>
                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-group-default">
                                        <label for="ProductBrand_slug">Slug Brand</label>
                                        <input type="text" class="form-control" id="ProductBrand_slug" name="ProductBrand_slug" value="<?php echo isset($brandEdit) ? $brandEdit->slug : "" ?>" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <input type="hidden" id="allShops" name="allShops" value="<?php echo $allShops?>"/>
                                    <?php if ($allShops=='1'):?>
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="shopId">Seleziona Se ha uno Shop</label>
                                        <select id="shopId" name="shopId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                        </select>
                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                    </div>
                                        <? else:?>
                                        <?php $userHasShop=\Monkey::app()->repoFactory->create('UserHasShop')->findOneBy(['userId'=>$currentUser]);?>
                                        <input type="hidden" id="shopSelected" name="shopSelected" value="<?php echo $userHasShop->shopId?>"/>
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="shopId">Seleziona Se ha uno Shop</label>
                                            <select id="shopId" name="shopId"
                                                    class="full-width selectpicker"
                                                    placeholder="Seleziona la Lista"
                                                    data-init-plugin="selectize">
                                            </select>
                                            <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                        </div>
                                        <?endif?>
                                    </div>
                                </div>
                            </div>
                            <?php foreach($langs as $lang){
                                echo '<div class="row clearfix"> 
                                 <div class="form-group form-group-default">
                                        <label for="ProductBrandTranslation_'.$lang->id.'">Traduzione '.$lang->name.'</label>
                                        <textarea class="form-control" rows="20" cols="180" name="ProductBrandTranslation_'.$lang->id.'" id="ProductBrandTranslation_'.$lang->id.'"></textarea>
                                  </div>
                                 </div>';
                            }?>

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
            data-event="bs.brand.add"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Salva"
            data-placement="bottom"
            ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>