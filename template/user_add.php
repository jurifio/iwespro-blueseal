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
                <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post" autocomplete="off">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default clearfix">
                                <div class="panel-heading clearfix">
                                    <h5 class="m-t-10">Informazioni di base</h5>
                                </div>
                                <div class="panel-body clearfix">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group form-group-default required">
                                                        <label for="user_name">Nome</label>
                                                        <input autocomplete="off" type="text" id="user_name" class="form-control" name="user_name" value="" required="required">
                                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group form-group-default required">
                                                        <label for="user_surname">Cognome</label>
                                                        <input id="user_surname" autocomplete="off" type="text" class="form-control" name="user_surname" value="" required="required" />
                                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group form-group-default required">
                                                        <label for="user_email">Email</label>
                                                        <input id="user_email" autocomplete="off" type="text" class="form-control" name="user_email" value="" required="required" />
                                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group form-group-default required">
                                                        <label for="user_password">Password</label>
                                                        <input id="user_password" autocomplete="off" type="text" class="form-control" name="user_password" value="<?php echo bin2hex(mcrypt_create_iv(6, MCRYPT_DEV_URANDOM)); ?>" required="required" />
                                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group form-group-default required">
                                                        <label for="user_phone">Telefono</label>
                                                        <input id="user_phone" autocomplete="off" type="text" class="form-control" name="user_phone" value="" required="required" />
                                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group form-group-default required">
                                                        <label for="user_gender">Sesso</label>
                                                        <select id="user_gender" autocomplete="off" class="form-control" name="user_gender" required="required" >
                                                            <option value=""></option>
                                                            <option value="M">Maschile</option>
                                                            <option value="F">Femminile</option>
                                                        </select>
                                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group form-group-default required">
                                                        <label for="user_birthdate">Data di Nascita</label>
                                                        <input id="user_birthdate" autocomplete="off" type="date" class="form-control" name="user_birthdate" value="" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group form-group-default required">
                                                        <label for="user_fiscal_code">Codice Fiscale</label>
                                                        <input id="user_fiscal_code" autocomplete="off" type="text" class="form-control" name="user_fiscal_code" value="" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
    <bs-toolbar-group data-group-label="Gestione prodotti">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-floppy-o"
            data-permission="/admin/product/add"
            data-event="bs.user.save"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Salva"
            data-placement="bottom"
            ></bs-toolbar-button>
</bs-toolbar>
</body>
</html>