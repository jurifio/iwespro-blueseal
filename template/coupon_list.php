<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui', 'forms', 'tables'], $page); ?>
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
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <div class="row">
                        <div class="col-md-1">
                            <div class="form-group form-group-default required">
                                <label for="ischkActive">Solo Attivi</label>
                               <?php if($isChkActive==1){
                                echo'<input id="ischkActive" autocomplete="off" type="checkbox" class="form-control" name="ischkActive" checked="checked" value="" />';
                                }else{
                                   echo'<input id="ischkActive" autocomplete="off" type="checkbox" class="form-control" name="ischkActive"  value="" />';
                                }?>
                            </div>
                           </div>
                            <div class="col-md-1">
                                <div class="form-group form-group-default required">
                                    <label for="isChkUser">Solo utenti</label>
                                    <?php if($isChkUser==1){
                                        echo'<input id="isChkUser" autocomplete="off" type="checkbox" class="form-control" name="isChkUser" checked="checked" value="" />';
                                    }else{
                                        echo'<input id="isChkUser" autocomplete="off" type="checkbox" class="form-control" name="isChkUser"  value="" />';
                                    }?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped responsive" width="100%"
                               data-datatable-name="coupon_list"
                               data-controller="CouponListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="50, 100, 200, 500"
                               data-display-length="50"
                               data-isActive="<?php echo $isChkActive; ?>"
                                data-isUser="<?php echo $isChkUser; ?>">
                            <thead>
                            <tr>
                                <th data-slug="code"
                                    data-searchable="true"
                                    data-orderable="false"
                                    class="center">Codice</th>
                                <th data-slug="remoteShopName"
                                    data-searchable="true"
                                    data-orderable="false"
                                    class="center">Shop Seller</th>
                                <th data-slug="couponType"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Tipo</th>
                                <th data-slug="issueDate"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="desc"
                                    class="center dataFilterType">Data emissione</th>
                                <th data-slug="validThru"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center dataFilterType">Data scadenza</th>
                                <th data-slug="amount"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Valore</th>
                                <th data-slug="validForCartTotal"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Minimo di spesa</th>
                                <th data-slug="utente"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Utilizzatore</th>
                                <th data-slug="newsletterUserEmail"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Assegnazione Utente </th>
                                <th data-slug="orderId"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Su ordine</th>
                                <th data-slug="valid"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Valido</th>
                                <th data-slug="isActive"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Attivo</th>
                                <th data-slug="couponEvent"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Coupon Multiplo</th>
                                <th data-slug="ip"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Indirizzo Ip</th>
                                <th data-slug="ipFilter"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">filtro</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php include "parts/footer.php" ?>
    </div>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Gestione coupon">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-file-o fa-plus"
            data-permission="/admin/marketing"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Aggiungi un nuovo coupon"
            data-placement="bottom"
            data-href="/blueseal/coupon/aggiungi"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-trash"
            data-permission="/admin/marketing"
            data-event="bs.coupon.del"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Elimina coupon"
            data-placement="bottom"
            data-toggle="modal"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-eur"
            data-permission="/admin/marketing"
            data-event="bs.tipocoupon"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Tipo coupon"
            data-placement="bottom"
            data-toggle="modal"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-download"
                data-permission="/admin/marketing"
                data-event="bs.align.newsletteruser"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Donwload Utenti Newsletter con coupon"
                data-placement="bottom"
                data-toggle="modal"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>