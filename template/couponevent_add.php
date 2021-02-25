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
                        <h5>Inserisci un nuovo evento coupon</h5>
                    </div>
                    <div class="panel-body">
                        <form id="form-project" role="form" action="" method="POST" autocomplete="on">
                            <div class="row clearfix">
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default required">
                                        <label for="Name">Nome evento coupon</label>
                                        <input type="text" class="form-control" id="name" name="name" required/>
                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default">
                                        <label for="Description">Descrizione</label>
                                        ​<textarea id="description" rows="4" cols="90" name="description"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default">
                                        <label for="startDate">Valido da</label>
                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                        <input type="date" class="form-control" id="startDate" name="startDate"
                                               value=""/>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default">
                                        <label for="endDate">Valido fino a</label>
                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                        <input type="date" class="form-control" id="endDate" name="endDate" value=""/>
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix">
                                <div class="col-sm-3">
                                    <div class="form-group form-group-default">
                                        <label for="isAnnounce">Banner Visibile sulla barra annunci</label>
                                        <select class="full-width selectpicker"
                                                placeholder="Seleziona"
                                                tabindex="-1" title="Seleziona"
                                                name="isAnnounce" id="isAnnounce">
                                            <option value="1">Si</option>
                                            <option value="0">No</option>
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
                                <div id="divCouponType" class="col-sm-6 hide">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="couponTypeId">Tipo coupon</label>
                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                        <select class="full-width selectpicker"
                                                placeholder="Seleziona il tipo di coupon" data-init-plugin="selectize"
                                                tabindex="-1" title="couponTypeId" name="couponTypeId"
                                                id="couponTypeId">

                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix">
                                <div class="col-sm-12">
                                    <div class="summernote-wrapper">
                                        <label>Contenuto del Banner</label>
                                        <textarea id="couponText" name="couponText" class="summer"
                                                  data-json="PostTranslation.content"
                                                  rows="50"></textarea>
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
    <bs-toolbar-group data-group-label="Operazioni">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-floppy-o"
                data-permission="/admin/marketing"
                data-event="bs.couponevent.add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Salva"
                data-placement="bottom">
        </bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
<script type="application/javascript">
    $('#startDate').val('<?php echo $startDate; ?>');
    $('#endDate').val('<?php echo $endDate; ?>');
</script>
</body>
</html>