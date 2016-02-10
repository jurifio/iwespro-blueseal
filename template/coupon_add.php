<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php"?>
    <?php echo $app->getAssets(['ui','forms'], $page); ?>
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
                        <h5>Inserisci un nuovo coupon</h5>
                    </div>
                    <div class="panel-body">
                        <form id="form-project" role="form" action="" method="POST" autocomplete="on">
                            <div class="row clearfix">
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="CouponType">Tipo coupon</label>
                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                        <select class="full-width selectpicker" placeholder="Seleziona il tipo di coupon" data-init-plugin="selectize" tabindex="-1" title="couponTypeId" name="couponTypeId" id="couponTypeId">
                                            <?php foreach ($couponTypes as $couponType): ?>
                                                <option value="<?php echo $couponType->id ?>" required> <?php echo $couponType->name . ""?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix">
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default">
                                        <label for="CouponCode">Codice Coupon</label>
                                        <input type="text" class="form-control" id="code" name="code" value="<?php echo $couponCode; ?> "/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                   <div class="col-sm-6">
                                       <div class="form-group form-group-default">
                                           <label for="validThru">Valido fino a</label>
                                           <input type="datetime-local" class="form-control" id="validThru" name="validThru" value="<?php echo $validThru; ?> " />
                                       </div>
                                   </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default">
                                        <label for="amount">Valore</label>
                                        <input type="text" class="form-control" id="amount" name="amount" required/>
                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default radio radio-success">
                                        <input type="radio" id="amountFixed" name="amountType" value="F" />
                                        <label for="amountFixed">Fisso</label>
                                        <input type="radio" id="amountPercentage" name="amountType" value="P" checked="checked" />
                                        <label for="amountPercentage">Percentuale</label>
                                        <input type="radio" id="amountPercentageFull" name="amountType" value="G" />
                                        <label for="amountPercentageFull">Percentuale sul prezzo pieno</label>
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
            data-event="bs.coupon.add"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Salva"
            data-placement="bottom"
            ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
<script type="application/javascript">
    $('#validThru').val('<?php echo $validThru; ?>');
</script>
</body>
</html>