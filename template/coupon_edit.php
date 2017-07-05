<?php
    $validThru = new DateTime($coupon->validThru);
?>
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
                        <h5>Modifica coupon</h5>
                        <p><?php echo $coupon->code;?></p>
                    </div>
                    <div class="panel-body">
                        <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post" autocomplete="off">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group form-group-default">
                                        <label for="validThru">Valido fino a</label>
                                        <input type="datetime-local" class="form-control" id="validThru" name="validThru" value="<?php echo $validThru->format('Y-m-d\TH:i:s'); ?>"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default">
                                        <label for="amount">Valore</label>
                                        <input type="text" class="form-control" id="amount" name="amount" value="<?php echo $coupon->amount; ?>"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group form-group-default radio radio-success">
                                        <input type="radio" id="validTrue" name="valid"  value="1" <?php echo ($coupon->valid == '1') ? 'checked="checked"' : null;?> />
                                        <label for="validTrue">Valido</label>
                                        <input type="radio" id="validFalse" name="valid" value="0" <?php echo ($coupon->valid == '0') ? 'checked="checked"' : null;?> />
                                        <label for="validFalse">Non valido</label>
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
            data-event="bs.coupon.edit"
            data-title="Salva"
            data-placement="bottom"
            ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>