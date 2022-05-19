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
                        <h5>modifica Paese</h5>
                    </div>
                    <div class="panel-body">
                        <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post"
                              autocomplete="off">
                            <div class="row">
                                <input type="hidden" id="countryId" name="countryId" value="<?php echo $country->id;?>"/>
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default">
                                        <label for="name">Nome</label>
                                        ​<input type="text" class="form-control" id="name" name="name"
                                                required="required"
                                                value="<?php echo $country->name; ?>"/>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default">
                                        <label for="capital">Capitale</label>
                                        ​<input type="text" class="form-control" id="capital" name="capital"
                                                required="required"
                                                value="<?php echo $country->capital; ?>"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default">
                                        <label for="shippingCost">Costo Spedizione</label>
                                        ​<input type="text" class="form-control" id="shippingCost" name="shippingCost"
                                                required="required"
                                                value="<?php echo $country->shippingCost; ?>"/>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default">
                                        <label for="freeShippingLimit">Limite di Spedizioni Gratuite</label>
                                        ​<input type="text" class="form-control" id="freeShippingLimit"
                                                name="freeShippingLimit"
                                                required="required"
                                                value="<?php echo $country->freeShippingLimit; ?>"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default">
                                        <label for="ISO">ISO</label>
                                        ​<input type="text" class="form-control" id="ISO" name="ISO"
                                                required="required"
                                                value="<?php echo $country->ISO; ?>"/>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default">
                                        <label for="ISO3">ISO3</label>
                                        ​<input type="text" class="form-control" id="ISO3"
                                                name="ISO3"
                                                required="required"
                                                value="<?php echo $country->ISO3; ?>"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default">
                                        <label for="continent">continente</label>
                                        ​<input type="text" class="form-control" id="continent" name="continent"
                                                required="required"
                                                value="<?php echo $country->continent; ?>"/>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default">
                                        <label for="tld">tld</label>
                                        ​<input type="text" class="form-control" id="tld"
                                                name="tld"
                                                required="required"
                                                value="<?php echo $country->tld; ?>"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <input type='hidden' id="currencyDisplaySelected" name="currencyDisplaySelected"
                                           value="<?php echo $country->currencyDisplay ?>"/>
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="currencyDisplay">Valuta Visualizzata</label>
                                        <select class="full-width selectpicker"
                                                placeholder="Seleziona La Valuta"
                                                tabindex="-1" title="Seleziona La Valuta"
                                                name="currencyDisplay" id="currencyDisplay">
                                            <?php foreach($currency as $curr){
                                              if($curr->id==$country->currencyDisplay){
                                                  echo '<option  selected="selected" value="'.$curr->id.'">'.$curr->code.'</option>';
                                              }  else{
                                                  echo '<option   value="'.$curr->id.'">'.$curr->code.'</option>';
                                              }

                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <input type='hidden' id="currencyPaymentSelected" name="currencyPaymentSelected"
                                           value="<?php echo $country->currencyPayment ?>"/>
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="currencyPayment">Valuta di Pagamento</label>
                                        <select class="full-width selectpicker"
                                                placeholder="Seleziona La Valuta"
                                                tabindex="-1" title="Seleziona La Valuta"
                                                name="currencyPayment" id="currencyPayment">
                                            <?php foreach($currency as $curr){
                                                if($curr->id==$country->currencyPayment){
                                                    echo '<option  selected="selected" value="'.$curr->id.'">'.$curr->code.'</option>';
                                                }  else{
                                                    echo '<option   value="'.$curr->id.'">'.$curr->code.'</option>';
                                                }

                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default">
                                        <label for="currencyCode">Codice Valuta</label>
                                        ​<input type="text" class="form-control" id="currencyCode" name="currencyCode"
                                                required="required"
                                                value="<?php echo $country->currencyCode; ?>"/>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default">
                                        <label for="currency">Valuta</label>
                                        ​<input type="text" class="form-control" id="currency"
                                                name="currency"
                                                required="required"
                                                value="<?php echo $country->currency; ?>"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default">
                                        <label for="phone">prefisso Telefonico</label>
                                        ​<input type="text" class="form-control" id="phone" name="phone"
                                                required="required"
                                                value="<?php echo $country->phone; ?>"/>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default">
                                        <label for="postCodeFormat">Formato CAP</label>
                                        ​<input type="text" class="form-control" id="postCodeFormat"
                                                name="postCodeFormat"
                                                required="required"
                                                value="<?php echo $country->postCodeFormat; ?>"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default">
                                        <label for="postCodeRegex"></label>
                                        ​<input type="text" class="form-control" id="postCodeRegex" name="postCodeRegex"
                                                required="required"
                                                value="<?php echo $country->postCodeRegex; ?>"/>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default">
                                        <label for="langs">lingua Locale</label>
                                        ​<input type="text" class="form-control" id="langs"
                                                name="langs"
                                                required="required"
                                                value="<?php echo $country->langs; ?>"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group form-group-default">
                                        <label for="vat">Regime Iva</label>
                                        ​<input type="text" class="form-control" id="vat" name="vat"
                                                required="required"
                                                value="<?php echo $country->vat; ?>"/>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="extraUe">Paese ExtraCee</label>
                                        <select class="full-width selectpicker"
                                                placeholder="Seleziona "
                                                tabindex="-1" title="Seleziona"
                                                name="extraUe" id="extraUe">
                                            <?php if($country->extraue==1){
                                                echo '<option value="0">Paese Cee</option>';
                                                echo '<option selected="selected" value="1">Paese  ExtraCee</option>';
                                            }else{
                                                 echo '<option  selected="selected" value="0">Paese Cee</option>';
                                                echo '<option value="1">Paese  ExtraCee</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <input type="hidden" id="langSel" name="langSel" value="<?php echo $country->currentLang;?>"/>
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="currentLang">Lingua Predefinita</label>
                                        <select class="full-width selectpicker"
                                                placeholder="Seleziona La Lingua"
                                                tabindex="-1" title="Seleziona La Lingua"
                                                name="currentLang" id="currentLang">
                                            <?php foreach($lang as $lg){
                                                if($lg->id==$country->currentLang){
                                                    echo '<option  selected="selected" value="'.$lg->id.'">'.$lg->name.'</option>';
                                                }  else{
                                                    echo '<option   value="'.$lg->id.'">'.$lg->name.'</option>';
                                                }

                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
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
                data-permission="/admin/product/add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.country.save"
                data-title="Salva"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>