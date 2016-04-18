<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui', 'forms'], $page); ?>
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
                        <div class="col-md-12">
                            <div class="panel panel-default clearfix">
                                <div class="panel-heading clearfix">
                                    <h5 class="m-t-10">Seleziona opzioni per importare il file</h5>
                                </div>
                                <div class="panel-body clearfix">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="shop">Seleziona lo shop</label>
                                                <select name="shopId"
                                                        class="full-width"
                                                        placeholder="Seleziona lo shop"
                                                        data-init-plugin="selectize" title=""
                                                        id="shop" required>
                                                    <?php foreach ($shops as $shop): ?>
                                                        <option value="<?php echo $shop->id ?>"><?php echo $shop->name ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="action">Seleziona l'azione</label>
                                                <select name="action"
                                                        class="full-width"
                                                        placeholder="Seleziona l'azione"
                                                        data-init-plugin="selectize" title=""
                                                        id="action" required>
                                                    <option value="add">Add</option>
                                                    <option value="revise">Revise</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group form-group-default required">
                                                    <label for="csvRows">Righe del file</label>
                                                    <input id="csvRows" autocomplete="off"
                                                           type="number" class="form-control"
                                                           name="csvRows"
                                                           value=""
                                                           required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group form-group-default required">
                                                    <label for="csvRows">Righe del file</label>
                                                    <input id="dummyFile"
                                                           type="file"
                                                           accept=".csv"
                                                           value=""
                                                           name="importerFile"
                                                           required/>
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
    <?php include "parts/footer.php"; ?>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Invio">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-floppy-o"
                data-permission="/admin/product/add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.file.send"
                data-title="Salva"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>