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
                        <h5>Inserisci un nuovo tag</h5>
                    </div>
                    <div class="panel-body">
                        <form id="form-project" role="form" action="" method="POST" autocomplete="on">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default">
                                        <label for="slug">Slug</label>
                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                        <input type="text" class="form-control" id="slug" name="slug" required/>
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix">
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="sorting">Priorità</label>
                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                        <select class="full-width selectpicker" placeholder="Seleziona la priorità" data-init-plugin="selectize" tabindex="-1" title="sortingId" name="sortingId" id="sortingId">
                                            <?php foreach ($sorting as $val): ?>
                                                <option value="<?php echo $val->id ?>" required >
                                                    <?php echo $val->priority . ""?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <?php
                            foreach ($langs as $lang): ?>
                                <h5><?php echo strtoupper($lang->name); ?></h5>
                                <div class="row clearfix">
                                    <div class="col-md-4">
                                        <div class="form-group form-group-default">
                                            <label>Nome Tag</label>
                                            <input type="text" class="form-control" name="tagName_<?php echo $lang->id; ?>">
                                        </div>
                                    </div>
                                </div>

                                <?php
                            endforeach; ?>

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
            data-permission="/admin/product/add"
            data-event="bs.tag.add"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Salva"
            data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>