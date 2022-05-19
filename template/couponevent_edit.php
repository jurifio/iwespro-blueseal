<?php
$startDate = new DateTime($coupon->startDate);
$endDate = new DateTime($coupon->endDate);
?>
<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui','forms'],$page); ?>
    <title>99Monkeys - <?php echo $page->getTitle(); ?></title>
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
                                    <h2>Per disattivare il Coupon Evento basta modificare la data di fine Coupon
                                        con una data passata</h2>
                                </div>
                            </div>
                            <div class="row clearfix">
                                <div class="col-sm-12">
                                    <?php $now=new DateTime();

                                    if ($now<$endDate){
                                        $active='<span style="color:green">Attivo</span>';
                                    }else{
                                        $active='<span style="color:red">Non Attivo</span>';
                                    }
                                    ?>
                                    <h2>Stato del Coupon Evento:<?php echo $active;?></h2>
                                </div>
                            </div>
                            <?php if (\Monkey::app()->getUser()->getId()==17370):?>
                            <div clas="row clearfix">
                                <div class="col-sm-12">
                                    <?php  if ($now<$endDate){
                                    echo '<img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQeVOVLKYZf34ij4KzLC0OBsY6c25mvJewm8w&usqp=CAU"/>';
                                }else{
                                     echo '<img src="data:image/jpeg;base64,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"/>';
                                }?>
                                </div>
                            </div>
                            <?php endif ?>
                            <div class="row clearfix">
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default">
                                        <label for="startDate">Valido da</label>
                                        <input type="datetime-local" class="form-control" id="startDate"
                                               name="startDate"
                                               value="<?php echo $startDate->format('Y-m-d\TH:i:s'); ?>"/>
                                    </div>
                                </div>
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
                                            <?php if ($coupon->isAnnounce == 1) {
                                                echo '<option value="1" selected="selected">Si</option>';
                                                echo '<option value="0">No</option>';
                                            } else {
                                                echo '<option value="1">Si</option>';
                                                echo '<option value="0" selected="selected">No</option>';
                                            } ?>
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