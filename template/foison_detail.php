<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui','forms','tables'], $page); ?>
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
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default clearfix">
                            <div class="panel-body clearfix">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php
                                        echo $workCategories;
                                        ?>
                                    </div>
                                    <div class="col-md-6" style="display: flex; justify-content: center; align-items: center">
                                        <div class="col-md-2">
                                            <img style="border-radius: 50%; width: 100%" src=<?php echo $foison->user->getProfileImage() ?>>
                                        </div>
                                        <div class="col-md-10">
                                            <div>
                                                <i class="fa fa-user" aria-hidden="true"></i>
                                                <span><?php echo $foison->name . ' ' . $foison->surname ?></span>
                                            </div>
                                            <div>
                                                <i class="fa fa-sort-numeric-desc" aria-hidden="true"></i>
                                                <strong>Rank totale:
                                                    <strong><?php echo $foison->rank ?></strong></strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <button class="btn btn-success" data-toggle="collapse" data-target="#batch">I LOTTI
                </button>
                <div class="container-fluid container-fixed-lg bg-white collapse in" id="batch">
                    <div class="panel panel-transparent">
                        <div class="panel-body">
                            <table class="table table-striped responsive" width="100%"
                                   data-datatable-name="size_full_list"
                                   data-controller="FoisonDetailsListAjaxController"
                                   data-url="<?php echo $app->urlForBluesealXhr() ?>"
                                   data-inner-setup="true"
                                   data-length-menu-setup="100, 200, 500"
                                   data-foisonid="<?php echo $foison->id;?>">
                                <thead>
                                <tr>
                                    <th data-slug="id"
                                        data-searchable="true"
                                        data-orderable="true" class="center"
                                        data-default-order="desc">Id</th>
                                    <th data-slug="workCategory"
                                        data-searchable="true"
                                        data-orderable="true" class="center">Categoria</th>
                                    <th data-slug="name"
                                        data-searchable="true"
                                        data-orderable="true" class="center">Titolo</th>
                                    <th data-slug="descr"
                                        data-searchable="true"
                                        data-orderable="true" class="center">Descrizione</th>
                                    <th data-slug="operatorRankIwes"
                                        data-searchable="true"
                                        data-orderable="true" class="center">ORI</th>
                                    <th data-slug="timingRank"
                                        data-searchable="true"
                                        data-orderable="true" class="center">Timing Rank</th>
                                    <th data-slug="qualityRank"
                                        data-searchable="true"
                                        data-orderable="true" class="center">QualityRank</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>


                <button class="btn btn-success" data-toggle="collapse" data-target="#baseInformation">INFO DI BASE
                </button>
                <div id="baseInformation" class="collapse">
                    <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post"
                          autocomplete="off">
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
                                                            <label for="foison_name">Nome</label>
                                                            <input autocomplete="off" type="text" id="foison_name"
                                                                   class="form-control" name="foison_name"
                                                                   value="<?php echo $foison->name; ?>"
                                                                   required="required">
                                                            <span class="bs red corner label"><i
                                                                        class="fa fa-asterisk"></i></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group form-group-default required">
                                                            <label for="foison_surname">Cognome</label>
                                                            <input id="foison_surname" autocomplete="off" type="text"
                                                                   class="form-control" name="foison_surname"
                                                                   value="<?php echo $foison->surname; ?>"
                                                                   required="required"/>
                                                            <span class="bs red corner label"><i
                                                                        class="fa fa-asterisk"></i></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group form-group-default required">
                                                            <label for="foison_birthdate">Data di Nascita</label>
                                                            <input id="foison_birthdate" autocomplete="off" type="date"
                                                                   class="form-control" name="foison_birthdate"
                                                                   value="<?php echo $foison->user->userDetails->birthDate; ?>"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group form-group-default required">
                                                            <label for="password">Password</label>
                                                            <input placeholder="Reimposta password" id="password"
                                                                   autocomplete="off" type="text" class="form-control"
                                                                   name="password" value=""/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group form-group-default required">
                                                            <label for="foison_fiscal_code">Codice Fiscale</label>
                                                            <input id="foison_fiscal_code" autocomplete="off"
                                                                   type="text" class="form-control"
                                                                   name="foison_fiscal_code"
                                                                   value="<?php echo !is_null($userAddress) ? $userAddress->fiscalCode : ''; ?>"/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group form-group-default required">
                                                            <label for="iban">Iban</label>
                                                            <input id="iban" autocomplete="off" type="text"
                                                                   class="form-control" name="iban"
                                                                   value="<?php if (!is_null($userAddress)) echo $foison->addressBook->iban; ?>"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group form-group-default required">
                                                            <label for="address">Indirizzo</label>
                                                            <input id="address" autocomplete="off" type="text"
                                                                   class="form-control" name="address"
                                                                   value="<?php if (!is_null($userAddress)) echo !is_null($userAddress) ? $userAddress->address : ''; ?>"/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group form-group-default required">
                                                            <label for="province">Provincia</label>
                                                            <input id="province" autocomplete="off" type="text"
                                                                   class="form-control" name="province"
                                                                   value="<?php if (!is_null($userAddress)) echo !is_null($userAddress) ? $userAddress->province : ''; ?>"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group form-group-default required">
                                                            <label for="city">Città</label>
                                                            <input id="city" autocomplete="off" type="text"
                                                                   class="form-control" name="city"
                                                                   value="<?php if (!is_null($userAddress)) echo $userAddress->city; ?>"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group form-group-default required">
                                                            <label for="postcode">Codice Postale</label>
                                                            <input id="postcode" autocomplete="off" type="text"
                                                                   class="form-control" name="postcode"
                                                                   value="<?php if (!is_null($userAddress)) echo $userAddress->postcode; ?>"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group form-group-default required">
                                                            <label for="country">Paese</label>
                                                            <select id="country" name="country">
                                                                <?php

                                                                foreach ($country as $c) { ?>
                                                                    <option value="<?php echo $c->id ?>"><?php echo $c->name; ?></option>
                                                                <?php }
                                                                ?>


                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group form-group-default">
                                                            <label for="foison_phone">Telefono</label>
                                                            <input id="foison_phone" autocomplete="off" type="text"
                                                                   class="form-control" name="foison_phone"
                                                                   value="<?php if (!is_null($userAddress)) echo $userAddress->phone; ?>"/>
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
                </div
            </div>
        </div>
    </div>
    <?php include "parts/footer.php" ?>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Salva i dati">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-floppy-o"
                data-permission="worker"
                data-event="bs.foison.user.address.save"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Salva"
                data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.foison.profile.image.manage"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.foison.curriculum.manage"
        ></bs-toolbar-button>
</bs-toolbar>
</body>
</html>