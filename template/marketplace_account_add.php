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
                                        <div class="col-md-12 hidden">
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
                                                <input id="marketplace_account_name" autocomplete="off" type="hidden"
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
                                    <?php if (isset($marketplaceAccount->config['activeAutomatic'])){ ?>
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
                                        <?php } ?>
                                        <div class="row" id="source_label_productCategoryGroup">
                                            <div class="col-md-12">Esclusione Categorie
                                            </div>
                                        </div>
                                        <div class="row" id="source_group_productCategoryGroup">
                                            <div class="col-md-12">
                                                <?php if (isset($marketplaceAccount->config['productCategoryIdEx1'])) { ?>
                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            <div class="form-group form-group-default required">
                                                                <div class="form-group form-group-default selectize-enabled">
                                                                    <label for="productCategoryIdEx1">Selettore
                                                                        Categoria 1 da Escludere
                                                                    </label>
                                                                    <select id="productCategoryIdEx1"
                                                                            name="productCategoryIdEx1"
                                                                            class="full-width selectpicker"
                                                                            placeholder="Selezione la Categoria 1 da Escludere "
                                                                            data-init-plugin="selectize">
                                                                      <?php echo $productCategoryEx1Option?>
                                                                    </select>
                                                                    <span class="bs red corner label"><i
                                                                                class="fa fa-asterisk"></i></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group form-group-default required">
                                                                <div class="form-group form-group-default selectize-enabled">
                                                                    <label for="productCategoryIdEx2">Selettore
                                                                        Categoria 2 da Escludere
                                                                    </label>
                                                                    <select id="productCategoryIdEx2"
                                                                            name="productCategoryIdEx2"
                                                                            class="full-width selectpicker"
                                                                            placeholder="Selezione la Categoria 2 da Escludere "
                                                                            data-init-plugin="selectize">
                                                                        <?php echo $productCategoryEx2Option?>
                                                                    </select>
                                                                    <span class="bs red corner label"><i
                                                                                class="fa fa-asterisk"></i></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group form-group-default required">
                                                                <div class="form-group form-group-default selectize-enabled">
                                                                    <label for="productCategoryIdEx3">Selettore
                                                                        Categoria 3 da Escludere
                                                                    </label>
                                                                    <select id="productCategoryIdEx3"
                                                                            name="productCategoryIdEx3"
                                                                            class="full-width selectpicker"
                                                                            placeholder="Selezione la Categoria 3 da Escludere "
                                                                            data-init-plugin="selectize">
                                                                        <?php echo $productCategoryEx3Option?>
                                                                    </select>
                                                                    <span class="bs red corner label"><i
                                                                                class="fa fa-asterisk"></i></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group form-group-default required">
                                                                <div class="form-group form-group-default selectize-enabled">
                                                                    <label for="productCategoryIdEx4">Selettore
                                                                        Categoria 4 da Escludere
                                                                    </label>
                                                                    <select id="productCategoryIdEx4"
                                                                            name="productCategoryIdEx4"
                                                                            class="full-width selectpicker"
                                                                            placeholder="Selezione la Categoria 4 da Escludere "
                                                                            data-init-plugin="selectize">
                                                                        <?php echo $productCategoryEx4Option?>
                                                                    </select>
                                                                    <span class="bs red corner label"><i
                                                                                class="fa fa-asterisk"></i></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group form-group-default required">
                                                                <div class="form-group form-group-default selectize-enabled">
                                                                    <label for="productCategoryIdEx5">Selettore
                                                                        Categoria 5 da Escludere
                                                                    </label>
                                                                    <select id="productCategoryIdEx5"
                                                                            name="productCategoryIdEx5"
                                                                            class="full-width selectpicker"
                                                                            placeholder="Selezione la Categoria 5 da Escludere "
                                                                            data-init-plugin="selectize">
                                                                        <?php echo $productCategoryEx5Option?>
                                                                    </select>
                                                                    <span class="bs red corner label"><i
                                                                                class="fa fa-asterisk"></i></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-2">
                                                                <div class="form-group form-group-default required">
                                                                    <div class="form-group form-group-default selectize-enabled">
                                                                        <label for="productCategoryIdEx6">Selettore
                                                                            Categoria 6 da Escludere
                                                                        </label>
                                                                        <select id="productCategoryIdEx6"
                                                                                name="productCategoryIdEx6"
                                                                                class="full-width selectpicker"
                                                                                placeholder="Selezione la Categoria 6 da Escludere "
                                                                                data-init-plugin="selectize">
                                                                            <?php echo $productCategoryEx6Option?>
                                                                        </select>
                                                                        <span class="bs red corner label"><i
                                                                                    class="fa fa-asterisk"></i></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <?php if (isset($marketplaceAccount->config['productCategoryId1'])) { ?>
                                                    <div class="row">
                                                        <div class="col-md-3" id="source_productGroupId1">
                                                            <div class="form-group form-group-default required">
                                                                <div class="form-group form-group-default selectize-enabled">
                                                                    <label for="productCategoryId1">Selettore
                                                                        Categoria per il range
                                                                        1</label>
                                                                    <select id="productCategoryId1"
                                                                            name="productCategoryId1"
                                                                            class="full-width selectpicker"
                                                                            placeholder="Selezione la Categoria 1"
                                                                            data-init-plugin="selectize">
                                                                        <?php echo $productCategory1Option?>
                                                                    </select>
                                                                    <span class="bs red corner label"><i
                                                                                class="fa fa-asterisk"></i></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3" id="source_productGroupId2">
                                                            <div class="form-group form-group-default required">
                                                                <div class="form-group form-group-default selectize-enabled">
                                                                    <label for="productCategoryId2">Selettore
                                                                        Categoria per il range
                                                                        2</label>
                                                                    <select id="productCategoryId2"
                                                                            name="productCategoryId2"
                                                                            class="full-width selectpicker"
                                                                            placeholder="Selezione la Categoria 2"
                                                                            data-init-plugin="selectize">
                                                                        <?php echo $productCategory2Option?>
                                                                    </select>
                                                                    <span class="bs red corner label"><i
                                                                                class="fa fa-asterisk"></i></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3" id="source_productGroupId3">
                                                            <div class="form-group form-group-default required">
                                                                <div class="form-group form-group-default selectize-enabled">
                                                                    <label for="productCategoryId3">Selettore
                                                                        Categoria per il range
                                                                        3</label>
                                                                    <select id="productCategoryId3"
                                                                            name="productCategoryId3"
                                                                            class="full-width selectpicker"
                                                                            placeholder="Selezione la Categoria 3"
                                                                            data-init-plugin="selectize">
                                                                        <?php echo $productCategory3Option?>
                                                                    </select>
                                                                    <span class="bs red corner label"><i
                                                                                class="fa fa-asterisk"></i></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3" id="source_productGroupId4">
                                                            <div class="form-group form-group-default required">
                                                                <div class="form-group form-group-default selectize-enabled">
                                                                    <label for="productCategoryId4">Selettore
                                                                        Categoria per il range
                                                                        4</label>
                                                                    <select id="productCategoryId4"
                                                                            name="productCategoryId4"
                                                                            class="full-width selectpicker"
                                                                            placeholder="Selezione la Categoria 4"
                                                                            data-init-plugin="selectize">
                                                                        <?php echo $productCategory4Option?>
                                                                    </select>
                                                                    <span class="bs red corner label"><i
                                                                                class="fa fa-asterisk"></i></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3" id="source_productGroupId5">
                                                            <div class="form-group form-group-default required">
                                                                <div class="form-group form-group-default selectize-enabled">
                                                                    <label for="productCategoryId5">Selettore
                                                                        Categoria per il range
                                                                        5</label>
                                                                    <select id="productCategoryId5"
                                                                            name="productCategoryId5"
                                                                            class="full-width selectpicker"
                                                                            placeholder="Selezione la Categoria 5"
                                                                            data-init-plugin="selectize">
                                                                        <?php echo $productCategory5Option?>
                                                                    </select>
                                                                    <span class="bs red corner label"><i
                                                                                class="fa fa-asterisk"></i></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <!--gruppo taglie inizio -->
                                            <div class="row" id="source_label_productSizeGroup">
                                                <div class="col-md-12">Esclusione Gruppi Taglia
                                                </div>
                                            </div>
                                        <div class="row" id="source_group_productSizeGroup">
                                            <div class="col-md-12">
                                                <?php if (isset($marketplaceAccount->config['productSizeGroupEx1'])) { ?>
                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            <div class="form-group form-group-default required">
                                                                <div class="form-group form-group-default selectize-enabled">
                                                                    <label for="productSizeGroupEx1">Selettore
                                                                        Gruppo Taglia 1 da Escludere
                                                                    </label>
                                                                    <select id="productSizeGroupEx1"
                                                                            name="productSizeGroupEx1"
                                                                            class="full-width selectpicker"
                                                                            placeholder="Selezione il Gruppo Taglia da Escludere"
                                                                            data-init-plugin="selectize">
                                                                        <option value='<?php echo $productSizeGroupEx1?>'><?php echo $productSizeGroupEx1Text;?></option>
                                                                    </select>
                                                                    <span class="bs red corner label"><i
                                                                                class="fa fa-asterisk"></i></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group form-group-default required">
                                                                <div class="form-group form-group-default selectize-enabled">
                                                                    <label for="productSizeGroupEx2">Selettore Gruppo
                                                                        Taglia
                                                                        2 da Escludere</label>
                                                                    <select id="productSizeGroupEx2"
                                                                            name="productSizeGroupEx2"
                                                                            class="full-width selectpicker"
                                                                            placeholder="Selezione il Gruppo Taglia"
                                                                            data-init-plugin="selectize">
                                                                        <option value='<?php echo $productSizeGroupEx2?>'><?php echo $productSizeGroupEx2Text;?></option>
                                                                    </select>
                                                                    <span class="bs red corner label"><i
                                                                                class="fa fa-asterisk"></i></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group form-group-default required">
                                                                <div class="form-group form-group-default selectize-enabled">
                                                                    <label for="productSizeGroupEx3">Selettore Gruppo
                                                                        Taglia
                                                                        3 da Escludere</label>
                                                                    <select id="productSizeGroupEx3"
                                                                            name="productSizeGroupEx3"
                                                                            class="full-width selectpicker"
                                                                            placeholder="Selezione il Gruppo Taglia"
                                                                            data-init-plugin="selectize">
                                                                        <option value='<?php echo $productSizeGroupEx3?>'><?php echo $productSizeGroupEx3Text;?></option>
                                                                    </select>
                                                                    <span class="bs red corner label"><i
                                                                                class="fa fa-asterisk"></i></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group form-group-default required">
                                                                <div class="form-group form-group-default selectize-enabled">
                                                                    <label for="productSizeGroupEx4">Selettore Gruppo
                                                                        Taglia
                                                                        4 da Escludere</label>
                                                                    <select id="productSizeGroupEx4"
                                                                            name="productSizeGroupEx4"
                                                                            class="full-width selectpicker"
                                                                            placeholder="Selezione il Gruppo Taglia"
                                                                            data-init-plugin="selectize">
                                                                        <option value='<?php echo $productSizeGroupEx4?>'><?php echo $productSizeGroupEx4Text;?></option>
                                                                    </select>
                                                                    <span class="bs red corner label"><i
                                                                                class="fa fa-asterisk"></i></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group form-group-default required">
                                                                <div class="form-group form-group-default selectize-enabled">
                                                                    <label for="productSizeGroupEx5">Selettore Gruppo
                                                                        Taglia
                                                                        5 da Escludere</label>
                                                                    <select id="productSizeGroupEx5"
                                                                            name="productSizeGroupEx5"
                                                                            class="full-width selectpicker"
                                                                            placeholder="Selezione il Gruppo Taglia"
                                                                            data-init-plugin="selectize">
                                                                        <option value='<?php echo $productSizeGroupEx5?>'><?php echo $productSizeGroupEx5Text;?></option>
                                                                    </select>
                                                                    <span class="bs red corner label"><i
                                                                                class="fa fa-asterisk"></i></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group form-group-default required">
                                                                <div class="form-group form-group-default selectize-enabled">
                                                                    <label for="productSizeGroupEx6">Selettore Gruppo
                                                                        Taglia
                                                                        6 da Escludere</label>
                                                                    <select id="productSizeGroupEx6"
                                                                            name="productSizeGroupEx6"
                                                                            class="full-width selectpicker"
                                                                            placeholder="Selezione il Gruppo Taglia"
                                                                            data-init-plugin="selectize">
                                                                        <option value='<?php echo $productSizeGroupEx6?>'><?php echo $productSizeGroupEx6Text;?></option>
                                                                    </select>
                                                                    <span class="bs red corner label"><i
                                                                                class="fa fa-asterisk"></i></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <?php if (isset($marketplaceAccount->config['productSizeGroup1'])) { ?>
                                                    <div class="row">
                                                        <div class="col-md-3" id="source_productSizeGroupId1">
                                                            <div class="form-group form-group-default required">
                                                                <div class="form-group form-group-default selectize-enabled">
                                                                    <label for="productSizeGroupId1">Selettore
                                                                        Gruppo Taglia per il range
                                                                        1</label>
                                                                    <select id="productSizeGroupId1"
                                                                            name="productSizeGroupId1"
                                                                            class="full-width selectpicker"
                                                                            placeholder="Selezione il Gruppo Taglia"
                                                                            data-init-plugin="selectize">
                                                                        <option value='<?php echo $productSizeGroupId1?>'><?php echo $productSizeGroupId1Text;?></option>
                                                                    </select>
                                                                    <span class="bs red corner label"><i
                                                                                class="fa fa-asterisk"></i></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3" id="source_productSizeGroupId2">
                                                            <div class="form-group form-group-default required">
                                                                <div class="form-group form-group-default selectize-enabled">
                                                                    <label for="productSizeGroupId2">Selettore Gruppo
                                                                        Taglia per il range
                                                                        2</label>
                                                                    <select id="productSizeGroupId2"
                                                                            name="productSizeGroupId2"
                                                                            class="full-width selectpicker"
                                                                            placeholder="Selezione il Gruppo Taglia"
                                                                            data-init-plugin="selectize">
                                                                        <option value='<?php echo $productSizeGroupId2?>'><?php echo $productSizeGroupId2Text;?></option>
                                                                    </select>
                                                                    <span class="bs red corner label"><i
                                                                                class="fa fa-asterisk"></i></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3" id="source_productSizeGroupId3">
                                                            <div class="form-group form-group-default required">
                                                                <div class="form-group form-group-default selectize-enabled">
                                                                    <label for="productSizeGroupId3">Selettore Gruppo
                                                                        Taglia per il range
                                                                        3</label>
                                                                    <select id="productSizeGroupId3"
                                                                            name="productSizeGroupId3"
                                                                            class="full-width selectpicker"
                                                                            placeholder="Selezione il Gruppo Taglia"
                                                                            data-init-plugin="selectize">
                                                                        <option value='<?php echo $productSizeGroupId3?>'><?php echo $productSizeGroupId3Text;?></option>
                                                                    </select>
                                                                    <span class="bs red corner label"><i
                                                                                class="fa fa-asterisk"></i></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3" id="source_productSizeGroupId4">
                                                            <div class="form-group form-group-default required">
                                                                <div class="form-group form-group-default selectize-enabled">
                                                                    <label for="productSizeGroupId4">Selettore Gruppo
                                                                        Taglia per il range
                                                                        4 </label>
                                                                    <select id="productSizeGroupId4"
                                                                            name="productSizeGroupId4"
                                                                            class="full-width selectpicker"
                                                                            placeholder="Selezione il Gruppo Taglia"
                                                                            data-init-plugin="selectize">
                                                                        <option value='<?php echo $productSizeGroupId4?>'><?php echo $productSizeGroupId4Text;?></option>
                                                                    </select>
                                                                    <span class="bs red corner label"><i
                                                                                class="fa fa-asterisk"></i></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3" id="source_productSizeGroupId5">
                                                            <div class="form-group form-group-default required">
                                                                <div class="form-group form-group-default selectize-enabled">
                                                                    <label for="productSizeGroupId5">Selettore Gruppo
                                                                        Taglia per il range
                                                                        5</label>
                                                                    <select id="productSizeGroupId5"
                                                                            name="productSizeGroupId5"
                                                                            class="full-width selectpicker"
                                                                            placeholder="Selezione il Gruppo Taglia"
                                                                            data-init-plugin="selectize">
                                                                        <option value='<?php echo $productSizeGroupId5?>'><?php echo $productSizeGroupId5Text;?></option>
                                                                    </select>
                                                                    <span class="bs red corner label"><i
                                                                                class="fa fa-asterisk"></i></span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                <?php } ?>
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