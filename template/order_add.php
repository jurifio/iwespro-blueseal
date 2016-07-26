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
                <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post"
                      autocomplete="off">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="panel panel-default clearfix">
                                <div class="panel-heading clearfix">
                                    <h5 class="m-t-10">Informazioni Utente</h5>
                                </div>
                                <div class="panel-body clearfix">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="user">Utente</label>
                                                <select id="user" class="full-width selectpicker"
                                                        placeholder="Seleziona un utente" name="user"
                                                        required="required"></select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default clearfix">
                                <div class="panel-body clearfix">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="adress">Indirizzo</label>
                                                <select id="address" name="address" class="full-width selectpicker"
                                                        placeholder="Seleziona un indirizzo"
                                                        data-init-plugin="selectize">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default clearfix">
                                <div class="panel-body clearfix">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div id="newAddressForm">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group form-group-default required">
                                                            <label for="user_address_name">Nome</label>
                                                            <input autocomplete="off" type="text" id="user_address_name"
                                                                   class="form-control"
                                                                   required="required">
                                                            <span class="bs red corner label"><i
                                                                        class="fa fa-asterisk"></i></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group form-group-default required">
                                                            <label for="user_address_surname">Surname</label>
                                                            <input autocomplete="off" type="text"
                                                                   id="user_address_surname"
                                                                   class="form-control"
                                                                   required="required">
                                                            <span class="bs red corner label"><i
                                                                        class="fa fa-asterisk"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group form-group-default required">
                                                            <label for="user_address_phone">Phone</label>
                                                            <input autocomplete="off" type="text"
                                                                   id="user_address_phone"
                                                                   class="form-control"

                                                                   required="required">
                                                            <span class="bs red corner label"><i
                                                                        class="fa fa-asterisk"></i></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group form-group-default selectize-enabled required">
                                                            <label for="user_address_country">Paese</label>
                                                            <select id="user_address_country"
                                                                    class="full-width selectpicker"
                                                                    placeholder="Seleziona il Paese"
                                                                    data-init-plugin="selectize">
                                                                <option value="it">Italia</option>
                                                                <option value="uk">United Kindom</option>
                                                            </select>
                                                            <span class="bs red corner label"><i
                                                                        class="fa fa-asterisk"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group form-group-default required">
                                                            <label for="user_address_address">Address</label>
                                                            <input autocomplete="off" type="text"
                                                                   id="user_address_address"
                                                                   class="form-control"
                                                                   required="required">
                                                            <span class="bs red corner label"><i
                                                                        class="fa fa-asterisk"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group form-group-default required">
                                                            <label for="user_address_address2">Address2</label>
                                                            <input autocomplete="off" type="text"
                                                                   id="user_address_address2"
                                                                   class="form-control"
                                                                   required="required">
                                                            <span class="bs red corner label"><i
                                                                        class="fa fa-asterisk"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group form-group-default required">
                                                            <label for="user_address_postcode">PostCode</label>
                                                            <input autocomplete="off" type="text"
                                                                   id="user_address_postcode"
                                                                   class="form-control"
                                                                   required="required">
                                                            <span class="bs red corner label"><i
                                                                        class="fa fa-asterisk"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group form-group-default required">
                                                            <label for="user_address_province">Provice</label>
                                                            <input autocomplete="off" type="text"
                                                                   id="user_address_province"
                                                                   class="form-control"
                                                                   required="required">
                                                            <span class="bs red corner label"><i
                                                                        class="fa fa-asterisk"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group form-group-default required">
                                                            <label for="user_address_city">City</label>
                                                            <input autocomplete="off" type="text" id="user_address_city"
                                                                   class="form-control"
                                                                   required="required">
                                                            <span class="bs red corner label"><i
                                                                        class="fa fa-asterisk"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <a href="#" id="formAddressSubmit">Invia</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="panel panel-default clearfix">
                                <div class="panel-heading clearfix">
                                    <h5 class="m-t-10">Dettagli Ordine</h5>
                                </div>
                                <div class="panel-body clearfix">

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
    <bs-toolbar-group data-group-label="Gestione ordini">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-file-o fa-plus"
                data-permission="/admin/order/add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Aggiungi un nuovo ordine manuale"
                data-placement="bottom"
                data-href="#"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>

</body>
</html>