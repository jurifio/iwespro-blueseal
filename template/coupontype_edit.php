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
                        <h5>Modifica tipo coupon</h5>
                        <p><?php echo $coupon->name;?></p>
                    </div>
                    <div class="panel-body">
                        <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post" autocomplete="off">
                            <div class="row clearfix">
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="validity">Validità</label>
                                        <select class="full-width selectpicker" placeholder="Seleziona il periodo di validità" data-init-plugin="selectize" tabindex="-1" title="validity" name="validity" id="validity">
                                            <?php $i=0;
                                                foreach ($possValids as $possValid): ?>
                                                <option value="<?php echo $possValidity[$i]; ?>" required
                                                    <?php echo ($possValidity[$i] == $coupon->validity) ? 'selected="selected"' : ""; ?> >
                                                    <?php echo $possValid . ""?>
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
                                        <label for="amount">Valore</label>
                                        <input type="text" class="form-control" id="amount" name="amount" value="<?php echo $coupon->amount; ?>"/>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default radio radio-success">
                                        <input type="radio" id="amountFixed" name="amountType" value="F" <?php echo ($coupon->amountType == 'F') ? 'checked="checked"' : null;?> />
                                        <label for="amountFixed">Fisso</label>
                                        <input type="radio" id="amountPercentage" name="amountType" value="P" <?php echo ($coupon->amountType == 'P') ? 'checked="checked"' : null;?> />
                                        <label for="amountPercentage">Percentuale</label>
                                        <input type="radio" id="amountPercentageFull" name="amountType" value="G" <?php echo ($coupon->amountType == 'G') ? 'checked="checked"' : null;?> />
                                        <label for="amountPercentageFull">Percentuale sul prezzo pieno</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group form-group-default">
                                        <label for="validForCartTotal">Minimo spesa</label>
                                        <input type="text" class="form-control" id="validForCartTotal" name="validForCartTotal" value="<?php echo $coupon->validForCartTotal; ?>"/>
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
            data-event="bs.coupontype.edit"
            data-title="Salva"
            data-placement="bottom"
            ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>