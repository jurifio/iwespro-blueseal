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
                                <div class="panel-body clearfix">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <input type="hidden" id="countField"
                                                   name="countField"
                                                   value="<?php echo $countField; ?>">
                                            <input type="hidden" id="marketplaceAccountId"
                                                   name="marketplaceAccountId"
                                                   value="<?php echo $marketplaceCode[0]; ?>">
                                            <input type="hidden" id="marketplaceId"
                                                   name="marketplaceId"
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
                                    <?php if (isset($marketplaceAccount->config['activeAutomatic'])){?>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default required">
                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="activeAutomatic">Attivazione CPC Specifici</label>
                                                    <select id="activeAutomatic" name="activeAutomatic"
                                                            class="full-width selectpicker"
                                                            placeholder="Seleziona lo stato"
                                                            data-init-plugin="selectize">
                                                        <option value="">seleziona lo stato</option>
                                                        <option value="1">Attivo</option>
                                                        <option value="0">Non Attivo</option>
                                                    </select>
                                                    <span class="bs red corner label"><i
                                                                class="fa fa-asterisk"></i></span>
                                                </div>
                                        </div>
                                    </div>
                                        <?php }?>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <?php if (isset($marketplaceAccount -> config['productSizeGroup1'])){ ?>
                                            <div class="row">
                                                <div class="col-md-2">
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
                                                <div class="col-md-2">
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
                                                <div class="col-md-2">
                                                    <div class="form-group form-group-default required">
                                                        <div class="form-group form-group-default selectize-enabled">
                                                            <label for="productSizeGroupId3">Selettore Gruppo
                                                                Taglia
                                                                3</label>
                                                            <select id="productSizeGroupId3" name="productSizeGroupId3"
                                                                    class="full-width selectpicker"
                                                                    placeholder="Selezione il Gruppo Taglia"
                                                                    data-init-plugin="selectize"></select>
                                                            <span class="bs red corner label"><i
                                                                        class="fa fa-asterisk"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group form-group-default required">
                                                        <div class="form-group form-group-default selectize-enabled">
                                                            <label for="productSizeGroupId3">Selettore Gruppo
                                                                Taglia
                                                                4</label>
                                                            <select id="productSizeGroupId4" name="productSizeGroupId4"
                                                                    class="full-width selectpicker"
                                                                    placeholder="Selezione il Gruppo Taglia"
                                                                    data-init-plugin="selectize"></select>
                                                            <span class="bs red corner label"><i
                                                                        class="fa fa-asterisk"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group form-group-default required">
                                                        <div class="form-group form-group-default selectize-enabled">
                                                            <label for="productSizeGroupId3">Selettore Gruppo
                                                                Taglia
                                                                5</label>
                                                            <select id="productSizeGroupId5" name="productSizeGroupId5"
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