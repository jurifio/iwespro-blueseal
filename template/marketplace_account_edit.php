<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app -> getAssets(['ui', 'forms'], $page); ?>
    <title>BlueSeal - <?php echo $page -> getTitle(); ?></title>
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
                <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post"
                      autocomplete="off">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default clearfix">
                                <div class="panel-heading clearfix">
                                    <h5 class="m-t-10">Informazioni di base</h5>
                                </div>
                                <input id="shop_id" type="hidden" value="" name="shop_id">
                                <div class="panel-body clearfix">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <input type="hidden" id="marketplace_account_id"
                                                   name="marketplace_account_id"
                                                   value="<?php echo $marketplaceCode[0]; ?>">
                                            <input type="hidden" id="marketplace_account_marketplace_id"
                                                   name="marketplace_account_marketplace_id"
                                                   value="<?php echo $marketplaceCode[1]; ?>">
                                            <div class="form-group form-group-default required">
                                                <label for="marketplace_account_name">Nome</label>
                                                <input id="marketplace_account_name" autocomplete="off" type="text"
                                                       class="form-control" name="marketplace_account_name" value=""
                                                       disabled="disabled"
                                                       required="required"/>
                                                <span class="bs red corner label"><i
                                                            class="fa fa-asterisk"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12" id="config-list">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <?php if (isset($marketplaceAccount -> config['productSizeGroup1'])){ ?>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group form-group-default required">
                                                        <div class="form-group form-group-default selectize-enabled">
                                                            <label for="productSizeGroupId1">Selettore
                                                                Gruppo Taglia
                                                                1</label>
                                                            <select id="productSizeGroupId1" name="productSizeGroupId1"
                                                                    class="full-width selectpicker"
                                                                    placeholder="Selezione il Gruppo Taglia"
                                                                    data-init-plugin="selectize"></select>
                                                            <span class="bs red corner label"><i
                                                                        class="fa fa-asterisk"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group form-group-default required">
                                                        <div class="form-group form-group-default selectize-enabled">
                                                            <label for="productSizeGroupId2">Selettore Gruppo
                                                                Taglia
                                                                2</label>
                                                            <select id="productSizeGroupId2" name="productSizeGroupId2"
                                                                    class="full-width selectpicker"
                                                                    placeholder="Selezione il Gruppo Taglia"
                                                                    data-init-plugin="selectize"></select>
                                                            <span class="bs red corner label"><i
                                                                        class="fa fa-asterisk"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php }?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 align-center-column-direction">
                                            <img src="/assets/img/formuladepubblicazione.png"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
        </form>
    </div>
</div>
</div>
<?php include "parts/footer.php" ?>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Gestione MarketplaceAccount">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-floppy-o"
                data-permission="/admin/product/add"
                data-event="bs.marketplace-account.save"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Salva"
                data-placement="bottom"
        ></bs-toolbar-button>
</bs-toolbar>
</body>
</html>