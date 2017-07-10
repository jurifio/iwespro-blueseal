<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui', 'forms'], $page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
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
                        <h5>Inserisci un nuovo Coupon</h5>
                    </div>
                    <div class="panel-body">
                        <form id="form-project" role="form" action="" method="POST" autocomplete="on">
                            <div class="row clearfix">
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="CouponType">Tipo coupon</label>
                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                        <select class="full-width selectpicker"
                                                placeholder="Seleziona il tipo di coupon" tabindex="-1"
                                                title="couponTypeId" name="couponTypeId" id="couponTypeId">
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
    <bs-toolbar-group data-group-label="Coupon Smart">
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
    <bs-toolbar-group data-group-label="Tipo Coupon">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-plus"
                data-permission="/admin/marketing"
                data-class="btn btn-default"
                data-rel="noopener"
                data-title="Aggiungi un nuovo Tipo Coupon"
                data-placement="bottom"
                data-href="/blueseal/tipocoupon/aggiungi"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-refresh"
                data-permission="/admin/marketing"
                data-event="bs.coupontype.refresh"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Ricarica Tipi"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>