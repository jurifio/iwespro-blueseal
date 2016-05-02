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
                <div class="row">
                    <div class="col-md-5">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h5>Visualizza tags</h5>
                            </div>
                            <div class="panel-body">
                                <ul class="tag-list">
                                <?php foreach($tags as $tag) {
                                    echo '<li id="'.$tag->id.'"><span>'.$tag->postTagTranslation->getFirst()->name.'</span></li>';
                                } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h5>Aggiungi tag</h5>
                            </div>
                            <div class="panel-body">
                                <form name="add-category">
                                    <div class="col-md-6">
                                        <div class="form-group form-group-default">
                                            <label>Nome Tag</label>
                                            <input type="text"
                                                   name="PostTagTranslation.name"
                                                   value=""
                                                   autocomplete="off"
                                                   class="form-control"/>
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
                data-permission="/admin/marketing"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.postTag.add"
                data-title="Aggiungi"
                data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-trash-o"
                data-permission="/admin/marketing"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.postTag.delete"
                data-title="Elimina"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>