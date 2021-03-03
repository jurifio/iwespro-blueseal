<?php
$startDate = new DateTime($coupon->startDate);
$endDate = new DateTime($coupon->endDate);
?>
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
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h5>Modifica evento coupon</h5>
                        <p><?php echo $coupon->name; ?></p>
                    </div>
                    <div class="panel-body">
                        <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post"
                              autocomplete="off">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default required">
                                        <label for="Name">Nome evento coupon</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                               value="<?php echo $coupon->name; ?>" required/>
                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default">
                                        <label for="description">Descrizione</label>
                                        ​<textarea id="description" rows="4" cols="90" name="description"
                                                   value="<?php echo $coupon->description; ?>"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                <h1>Per disattivare il Coupon Evento basta modificare la data di fine Coupon Evento con una data passata</h1>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default">
                                        <label for="startDate">Valido da</label>
                                        <input type="datetime-local" class="form-control" id="startDate"
                                               name="startDate"
                                               value="<?php echo $startDate->format('Y-m-d\TH:i:s'); ?>"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default">
                                        <label for="endDate">Valido fino a</label>
                                        <input type="datetime-local" class="form-control" id="endDate" name="endDate"
                                               value="<?php echo $endDate->format('Y-m-d\TH:i:s'); ?>"/>
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
                                            <?php if($coupon->isAnnounce==1){
                                                echo '<option value="1" selected="selected">Si</option>';
                                                echo '<option value="0">No</option>';
                                            }else{
                                                echo '<option value="1">Si</option>';
                                                echo '<option value="0" selected="selected">No</option>';
                                            }?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <input type='hidden' id="shopSelected" name="shopSelected"
                                           value="<?php echo $coupon->remoteShopId ?>"/>
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="remoteShopId">Shop Di Destinazione</label>
                                        <select class="full-width selectpicker"
                                                placeholder="Seleziona Lo Shop"
                                                tabindex="-1" title="Seleziona la Shop"
                                                name="remoteShopId" id="remoteShopId">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <input type='hidden' id="couponTypeSelectedId" name="couponTypeSelectedId"
                                               value="<?php echo $coupon->couponTypeId ?>"/>
                                        <label for="couponTypeId">Tipo Coupon Campagna</label>
                                        <select class="full-width selectpicker"
                                                placeholder="Seleziona il tipo di coupon" data-init-plugin="selectize"
                                                tabindex="-1" title="couponTypeId" name="couponTypeId"
                                                id="couponTypeId">
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="summernote-wrapper">
                                        <label>Contenuto del Banner</label>
                                        <textarea id="couponText" name="couponText" class="summer"
                                                  data-json="PostTranslation.content"
                                                  rows="50"><?php $coupon->couponText; ?></textarea>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include "parts/footer.php"; ?>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-floppy-o"
                data-permission="/admin/marketing"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.couponevent.edit"
                data-title="Salva"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>