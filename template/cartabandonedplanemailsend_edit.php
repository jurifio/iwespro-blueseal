<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui', 'forms', 'tables'], $page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
    <!--<script src="https://cdn.ckeditor.com/4.8.0/standard-all/ckeditor.js"></script>-->
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
                    <div class="col-md-4 col-md-offset-4 alert-container closed">

                    </div>
                </div>
            </div>
            <input type="hidden" id="cartIdEmailParam1" name="cartIdEmailParam1"
                   value="<?php echo $collectCartAbandonedEmailParam[1]['id']; ?>"/>
            <input type="hidden" id="cartIdEmailParam2" name="cartIdEmailParam2"
                   value="<?php echo $collectCartAbandonedEmailParam[2]['id']; ?>"/>
            <input type="hidden" id="cartIdEmailParam3" name="cartIdEmailParam3"
                   value="<?php echo $collectCartAbandonedEmailParam[3]['id']; ?>"/>
            <input type="hidden" id="couponTypeId1" name="couponTypeId1"
                   value="<?php echo $collectCartAbandonedEmailParam[1]['couponTypeId']; ?>"/>
            <input type="hidden" id="couponTypeId2" name="couponTypeId2"
                   value="<?php echo $collectCartAbandonedEmailParam[2]['couponTypeId']; ?>"/>
            <input type="hidden" id="couponTypeId3" name="couponTypeId3"
                   value="<?php echo $collectCartAbandonedEmailParam[3]['couponTypeId']; ?>"/>


            <?php if (!empty($collectCartAbandonedEmailParam[1]['amount'])) {
                $amount1 = $collectCartAbandonedEmailParam[1]['amount'];
            } else {
                $amount1 = "";
            } ?>
            <input type="hidden" id="amount1" name="amount1" value="<?php echo $amount1; ?>"/>
            <?php if (!empty($collectCartAbandonedEmailParam[2]['amount'])) {
                $amount2 = $collectCartAbandonedEmailParam[2]['amount'];
            } else {
                $amount2 = "";
            } ?>
            <input type="hidden" id="amount2" name="amount2" value="<?php echo $amount2; ?>"/>
            <?php if (!empty($collectCartAbandonedEmailParam[3]['amount'])) {
                $amount3 = $collectCartAbandonedEmailParam[3]['amount'];
            } else {
                $amount3 = "";
            } ?>
            <input type="hidden" id="amount3" name="amount3" value="<?php echo $amount3; ?>"/>
            <?php if (!empty($collectCartAbandonedEmailParam[1]['amountType'])) {
                if ($collectCartAbandonedEmailParam[1]['amountType'] == "P") {
                    $amountType1 = 'P';
                } else {
                    $amountType1 = 'F';
                }

            } else {
                $amountType1 = "";
            } ?>

            <?php if (!empty($collectCartAbandonedEmailParam[1]['amountType'])) {
                if ($collectCartAbandonedEmailParam[1]['amountType'] == "P") {
                    $amountType1 = 'P';
                } else {
                    $amountType1 = 'F';
                }

            } else {
                $amountType1 = "";
            } ?>
            <input type="hidden" id="amountType1" name="amountType1" value="<?php echo $amountType1; ?>"/>
            <?php if (!empty($collectCartAbandonedEmailParam[2]['amountType'])) {
                if ($collectCartAbandonedEmailParam[1]['amountType'] == "P") {
                    $amountType2 = 'P';
                } else {
                    $amountType2 = 'F';
                }

            } else {
                $amountType2 = "";
            } ?>
            <input type="hidden" id="amountType2" name="amountType2" value="<?php echo $amountType2; ?>"/>
            <?php if (!empty($collectCartAbandonedEmailParam[3]['amountType'])) {
                if ($collectCartAbandonedEmailParam[1]['amountType'] == "P") {
                    $amountType3 = 'P';
                } else {
                    $amountType3 = 'F';
                }

            } else {
                $amountType3 = "";
            } ?>
            <input type="hidden" id="amountType3" name="amountType3" value="<?php echo $amountType3; ?>"/>
            <?php if (!empty($collectCartAbandonedEmailParam[1]['validity'])) {
                $validity1 = $collectCartAbandonedEmailParam[1]['validity'];

            } else {
                $validity1 = '';
            } ?>
            <input type="hidden" id="validity1" name="validity1" value="<?php echo $validity1; ?>"/>
            <?php if (!empty($collectCartAbandonedEmailParam[2]['validity'])) {
                $validity2 = $collectCartAbandonedEmailParam[2]['validity'];

            } else {
                $validity2 = '';
            } ?>
            <input type="hidden" id="validity2" name="validity2" value="<?php echo $validity2; ?>"/>
            <?php if (!empty($collectCartAbandonedEmailParam[3]['validity'])) {
                $validity3 = $collectCartAbandonedEmailParam[3]['validity'];

            } else {
                $validity3 = "";
            } ?>
            <input type="hidden" id="validity3" name="validity3" value="<?php echo $validity3; ?>"/>
            <?php if (!empty($collectCartAbandonedEmailParam[1]['validForCartTotal'])) {
                $validForCartTotal1 = $collectCartAbandonedEmailParam[1]['validForCartTotal'];
            } else {
                $validForCartTotal1 = "";
            } ?>
            <input type="hidden" id="validForCartTotal1" name="validForCartTotal1"
                   value="<?php echo $validForCartTotal1; ?>"/>
            <?php if (!empty($collectCartAbandonedEmailParam[2]['validForCartTotal'])) {
                $validForCartTotal2 = $collectCartAbandonedEmailParam[2]['validForCartTotal'];
            } else {
                $validForCartTotal2 = "";
            } ?>
            <input type="hidden" id="validForCartTotal2" name="validForCartTotal2"
                   value="<?php echo $validForCartTotal2; ?>"/>
            <?php if (!empty($collectCartAbandonedEmailParam[3]['validForCartTotal'])) {
                $validForCartTotal3 = $collectCartAbandonedEmailParam[3]['amount'];
            } else {
                $validForCartTotal3 = "";
            } ?>
            <input type="hidden" id="validForCartTotal3" name="validForCartTotal3"
                   value="<?php echo $validForCartTotal3; ?>"/>
            <?php if (!empty($collectCartAbandonedEmailParam[1]['hasFreeShipping'])) {
                if ($collectCartAbandonedEmailParam[1]['hasFreeShipping'] == "1") {
                    $hasFreeShipping1 = '1';
                } else {
                    $hasFreeShipping1 = '0';
                }

            } else {
                $hasFreeShipping1 = "";
            } ?>
            <input type="hidden" id="hasFreeShipping1" name="hasFreeShipping1"
                   value="<?php echo $hasFreeShipping1; ?>"/>
            <?php if (!empty($collectCartAbandonedEmailParam[2]['hasFreeShipping'])) {
                if ($collectCartAbandonedEmailParam[1]['hasFreeShipping'] == "1") {
                    $hasFreeShipping2 = '1';
                } else {
                    $hasFreeShippinge2 = '0';
                }

            } else {
                $hasFreeShipping2 = "";
            } ?>
            <input type="hidden" id="hasFreeShipping2" name="hasFreeShipping2"
                   value="<?php echo $hasFreeShipping2; ?>"/>
            <?php if (!empty($collectCartAbandonedEmailParam[3]['hasFreeShipping'])) {
                if ($collectCartAbandonedEmailParam[1]['hasFreeShipping'] == "1") {
                    $hasFreeShipping3 = '1';
                } else {
                    $hasFreeShipping3 = '0';
                }

            } else {
                $hasFreeShipping3 = "";
            } ?>
            <input type="hidden" id="hasFreeShipping3" name="hasFreeShipping3"
                   value="<?php echo $hasFreeShipping3; ?>"/>
            <?php if (!empty($collectCartAbandonedEmailParam[1]['hasFreeReturn'])) {
                if ($collectCartAbandonedEmailParam[1]['hasFreeReturn'] == "1") {
                    $hasFreeReturn1 = '1';
                } else {
                    $hasFreeReturn1 = '0';
                }

            } else {
                $hasFreeReturn1 = "";
            } ?>
            <input type="hidden" id="hasFreeReturn1" name="hasFreeReturn1" value="<?php echo $hasFreeReturn1; ?>"/>
            <?php if (!empty($collectCartAbandonedEmailParam[2]['hasFreeReturn'])) {
                if ($collectCartAbandonedEmailParam[1]['hasFreeReturn'] == "1") {
                    $hasFreeReturn2 = '1';
                } else {
                    $hasFreeReturn2 = '0';
                }

            } else {
                $hasFreeReturn2 = "";
            } ?>
            <input type="hidden" id="hasFreeReturn2" name="hasFreeReturn2" value="<?php echo $hasFreeReturn2; ?>"/>
            <?php if (!empty($collectCartAbandonedEmailParam[3]['hasFreeReturn'])) {
                if ($collectCartAbandonedEmailParam[1]['hasFreeReturn'] == "1") {
                    $hasFreeReturn3 = '1';
                } else {
                    $hasFreeReturn3 = '0';
                }

            } else {
                $hasFreeReturn3 = "";
            } ?>
            <input type="hidden" id="hasFreeReturn3" name="hasFreeReturn3" value="<?php echo $hasFreeReturn3; ?>"/>

            <div class="container-fluid" style="margin-top: 20px">
                <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post"
                      autocomplete="off">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default clearfix">
                                <div class="panel-heading clearfix">
                                    <h5 class="m-t-10">Pianificazione Mail per Clienti con Carelli Abbandonati</h5>
                                </div>
                                <div class="panel-body clearfix">
                                    <div class="row col-md-4">
                                        <div class="col-md-5">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="firstTemplateId">Template Primo Invio</label>
                                                <select id="firstTemplateId" name="firstTemplateId"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona la Lista"

                                                <?php foreach ($template as $templates): ?>
                                                    <option <?php if ($collectCartAbandonedEmailParam[1]['firstTemplateId'] == $templates->id) echo 'selected="selected"'; ?>
                                                            value="<?php echo $templates->id ?>"> <?php echo $templates->name; ?></option>
                                                <?php endforeach; ?>>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="firstTimeEmailSendDay">Giorni</label>
                                                <input id="firstTimeEmailSendDay" class="form-control"
                                                       placeholder="Inserisci dopo quanti giorni deve essere inviata la  prima mail"
                                                       name="firstTimeEmailSendDay" required="required"
                                                       value="<?php echo $collectCartAbandonedEmailParam[1]['firstTimeEmailSendDay']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="firstTimeEmailSendHour">Ore</label>
                                                <input id="firstTimeEmailSendHour" class="form-control"
                                                       placeholder="Inserisci dopo quante ore deve essere inviata la  prima mail name="
                                                       firstTimeEmailSendHour" required="required"
                                                value="<?php echo $collectCartAbandonedEmailParam[1]['firstTimeEmailSendHour']; ?>
                                                ">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="generateCoupon">Coupon</label>
                                                <select id="generateCoupon" name="generateCoupon"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona la Lista"
                                                <?php if ($collectCartAbandonedEmailParam[1]['couponTypeId'] != 0) {
                                                    $selectedYes = "selected=\"selected\"";
                                                    $selectedNo = "";
                                                } else {
                                                    $selectedNo = "selected=\"selected\"";
                                                    $selectedYes = "";
                                                }

                                                ?>

                                                <option value=""></option>
                                                <option value=""></option>
                                                <option value="1"<?php echo $selectedYes; ?>>Si</option>
                                                <option value="2"<?php echo $selectedNo; ?>>No</option>
                                                data-init-plugin="selectize">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row col-md-8">
                                        <div id="coupondiv">
                                        </div>
                                        <div id="selectemaildiv">
                                        </div>
                                    </div>
                                    <div class="row col-md-4">
                                        <div class="col-md-5">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="secondTemplateId">Template Secondo Invio</label>
                                                <select id="secondTemplateId" name="secondTemplateId"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona la Lista"

                                                <?php foreach ($template as $templates): ?>
                                                    <option <?php if ($templates->id == $collectCartAbandonedEmailParam[1]['secondTemplateId']) echo 'selected="selected"'; ?>
                                                            value="<?php echo $templates->id ?>"> <?php echo $templates->name; ?></option>
                                                <?php endforeach; ?>>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="secondTimeEmailSendDay">Giorni</label>
                                                <input id="secondTimeEmailSendDay" class="form-control"
                                                       placeholder="Inserisci dopo quanti giorni deve essere inviata la  secondas mail"
                                                       name="secondTimeEmailSendDay" required="required"
                                                       value="<?php echo $collectCartAbandonedEmailParam[1]['secondTimeEmailSendDay']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="secondTimeEmailSendHour">Ore</label>
                                                <input id="secondTimeEmailSendHour" class="form-control"
                                                       placeholder="Inserisci dopo quante ore deve essere inviata la  seconda mail name="
                                                       secondTimeEmailSendHour" required="required"
                                                value="<?php echo $collectCartAbandonedEmailParam[1]['secondTimeEmailSendDay']; ?>
                                                ">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="generateCoupon2">Coupon</label>
                                                <select id="generateCoupon2" name="generateCoupon2"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona la Lista"
                                                <?php if ($collectCartAbandonedEmailParam[2]['couponTypeId'] != 0) {
                                                    $selectedYes2 = "selected=\"selected\"";
                                                    $selectedNo2 = "";
                                                } else {
                                                    $selectedNo2 = "selected=\"selected\"";
                                                    $selectedYes2 = "";
                                                }

                                                ?>
                                                <option value=""></option>
                                                <option value=""></option>
                                                <option value="1"<?php echo $selectedYes2; ?>>Si</option>
                                                <option value="2"<?php echo $selectedNo2; ?>>No</option>
                                                data-init-plugin="selectize"
                                                </select>
                                            </div>
                                        </div>
                                        <div id="selectemaildiv2"></div>
                                    </div>
                                    <div class="row col-md-8">
                                        <div id="coupondiv2">
                                        </div>
                                    </div>
                                    <div class="row col-md-4">
                                        <div class="col-md-5">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="thirdTemplateId">Template Terzo Invio</label>
                                                <select id="thirdTemplateId" name="thirdTemplateId"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona la Lista"

                                                <?php foreach ($template as $templates): ?>
                                                    <option <?php if ($templates->id == $collectCartAbandonedEmailParam[1]['thirdTemplateId']) echo 'selected="selected"'; ?>
                                                            value="<?php echo $templates->id ?>"> <?php echo $templates->name; ?></option>
                                                <?php endforeach; ?>>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="thirdTimeEmailSendDay">Giorni</label>
                                                <input id="thirdTimeEmailSendDay" class="form-control"
                                                       placeholder="Inserisci dopo quanti giorni deve essere inviata la terza mail"
                                                       name="thirdTimeEmailSendDay" required="required"
                                                       value="<?php echo $collectCartAbandonedEmailParam[1]['thirdTimeEmailSendDay']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="thirdTimeEmailSendHour">Ore</label>
                                                <input id="thirdTimeEmailSendHour" class="form-control"
                                                       placeholder="Inserisci dopo quante ore deve essere inviata la  terza mail name="
                                                       thirdTimeEmailSendHour" required="required"
                                                value="<?php echo $collectCartAbandonedEmailParam[1]['thirdTimeEmailSendHour']; ?>
                                                ">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="generateCoupon3">Coupon</label>
                                                <select id="generateCoupon3" name="generateCoupon3"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona la Lista"
                                                <?php if ($collectCartAbandonedEmailParam[3]['couponTypeId'] != 0) {
                                                    $selectedYes3 = "selected=\"selected\"";
                                                    $selectedNo3 = "";
                                                } else {
                                                    $selectedNo3 = "selected=\"selected\"";
                                                    $selectedYes3 = "";
                                                }

                                                ?>
                                                <option value=""></option>
                                                <option value=""></option>
                                                <option value="1"<?php echo $selectedYes3; ?>>Si</option>
                                                <option value="2"<?php echo $selectedNo3; ?>>No</option>
                                                data-init-plugin="selectize"
                                                </select>
                                            </div>
                                        </div>
                                        <div id="selectemaildiv3"></div>
                                    </div>
                                    <div class="col-md-8">
                                        <div id="coupondiv3">
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
    <bs-toolbar-group data-group-label="Gestione Pianificazione">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-file-o fa-plus"
                data-permission="AllShops"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.newPlanSendEmail.save"
                data-title="Salva la Pianificazione"
                data-placement="bottom"
                data-href="#"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>