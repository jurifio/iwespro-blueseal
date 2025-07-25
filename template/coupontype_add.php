<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui','forms'],$page); ?>
    <title>99Monkeys - <?php echo $page->getTitle(); ?></title>
    <style>
        #form-project .row > div {
            min-height: 75px;
        }
    </style>
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
                        <h5>Inserisci un nuovo tipo coupon</h5>
                    </div>
                    <div class="panel-body">
                        <form id="form-project" role="form" action="" method="POST" autocomplete="on">
                            <div class="row clearfix">
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default">
                                        <label for="name">Nome tipo coupon</label>
                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                        <input type="text" class="form-control" id="name" name="name"/>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="isActive">Seleziona se Attivo
                                        </label>
                                        <select id="isActive"
                                                name="isActive"
                                                class="full-width selectpicker"
                                                placeholder="Selezione se attivo"
                                                data-init-plugin="selectize">
                                            <option value=""></option>
                                            <option value="1" selected="selected">Si</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="validity">Validità</label>
                                        <select class="full-width selectpicker"
                                                placeholder="Seleziona il periodo di validità"
                                                data-init-plugin="selectize" tabindex="-1" title="validity"
                                                name="validity" id="validity">
                                            <?php $i = 0;
                                            foreach ($possValids as $possValid): ?>
                                                <option value="<?php echo $possValidity[$i]; ?>" required>
                                                    <?php echo $possValid . "" ?>
                                                </option>
                                                <?php $i++;
                                            endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix">
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default">
                                        <label for="validForCartTotal">Minimo spesa</label>
                                        <input type="text" class="form-control" id="validForCartTotal"
                                               name="validForCartTotal"/>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group form-group-default">
                                        <label for="hasFreeShipping">Spedizione Gratuita</label>
                                        <input type="checkbox" class="form-control" id="hasFreeShipping"
                                               name="hasFreeShipping"/>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group form-group-default">
                                        <label for="hasFreeReturn">Reso Gratuito</label>
                                        <input type="checkbox" class="form-control" id="hasFreeReturn"
                                               name="hasFreeReturn"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix">
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default">
                                        <label for="amount">Valore</label>
                                        <input type="text" class="form-control" id="amount" name="amount" required/>
                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default radio radio-success">
                                        <input type="radio" id="amountFixed" name="amountType" value="F"/>
                                        <label for="amountFixed">Fisso</label>
                                        <input type="radio" id="amountPercentage" name="amountType" value="P"
                                               checked="checked"/>
                                        <label for="amountPercentage">Percentuale</label>
                                        <input type="radio" id="amountPercentageFull" name="amountType" value="G"/>
                                        <label for="amountPercentageFull">Percentuale sul prezzo pieno</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix">
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="tags">Tags</label>
                                        <select class="full-width selectpicker"
                                                placeholder="Seleziona le tag di validità"
                                                tabindex="-1" title="Tags"
                                                name="tags[]" id="tags">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="remoteShopId">Shop Di Destinazione</label>
                                        <select class="full-width selectpicker"
                                                placeholder="Seleziona Lo Shop"
                                                tabindex="-1" title="Seleziona la Shop"
                                                name="remoteShopId" id="remoteShopId">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3 hide" id="divCampaign">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="campaignId">Campagna Associata</label>
                                        <select class="full-width selectpicker"
                                                placeholder="Seleziona la Campagna"
                                                tabindex="-1" title="Seleziona la Campagna"
                                                name="campaignId" id="campaignId">
                                        </select>
                                    </div>
                                </div>
                            </div>
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
                data-permission="/admin/marketing"
                data-event="bs.coupontype.add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Salva"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>