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
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h5>Modifica tag</h5>
                    </div>
                    <div class="panel-body">
                        <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post"
                              autocomplete="off">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group form-group-default">
                                                <label for="slug">Slug</label>
                                                ​<input type="text" class="form-control" id="slug" name="slug"
                                                        required="required"
                                                        value=""/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="sorting">Priorità</label>
                                                <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                                <select class="full-width selectpicker"
                                                        placeholder="Seleziona la priorità"
                                                        data-init-plugin="selectize" tabindex="-1" title="sortingId"
                                                        name="sortingId" id="sortingId"
                                                        required="required" >
                                                    <?php foreach ($sortingPriority as $val): ?>
                                                        <option value="<?php echo $val->id ?>" >
                                                            <?php echo $val->priority . "" ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group form-group-default">
                                                <label for="isPublic">Visibilità</label>
                                                <input type="checkbox" id="isPublic" name="isPublic" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <?php
                                    foreach ($langs as $lang): ?>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group form-group-default">
                                                    <label><?php echo strtoupper($lang->name); ?></label>
                                                    <input type="text" class="form-control"
                                                           name="tagName_<?php echo $lang->id; ?>"
                                                           value="">
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
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