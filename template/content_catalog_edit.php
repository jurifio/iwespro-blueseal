<?php
    $widget = $structure->getWidget($widgetType);
    $widget->setWidgetConfig($widgetId);
?>
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
                <div class="row">
                    <div class="col-sm-8">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="panel panel-default clearfix">
                                    <div class="panel-heading clearfix">
                                        <h5><?php echo $page->getTitle(); ?></h5>
                                    </div>
                                    <div class="panel-body clearfix">
                                        <input type="hidden" name="widgetType" value="<?php echo $widgetType; ?>" />
                                        <input type="hidden" name="widgetId" value="<?php echo $widgetId; ?>" />
                                        <input type="hidden" name="widgetLang" value="<?php echo $widgetLang; ?>" />
                                        <?php
                                        echo $widget->makeForm('text',$widgetLang,$widgetPath);
                                        echo $widget->makeForm('buttons',$widgetLang,$widgetPath);
                                        echo $widget->makeForm('color',$widgetLang,$widgetPath);
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="panel panel-default clearfix">
                                    <div class="panel-heading clearfix">
                                        <h5>Griglia</h5>
                                    </div>
                                    <div class="panel-body clearfix">
                                        <?php echo $widget->makeForm('grids',$widgetLang,$widgetPath); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="panel panel-default clearfix">
                                    <div class="panel-heading clearfix">
                                        <h5>Immagine</h5>
                                    </div>
                                    <div class="panel-body clearfix">
                                        <?php echo $widget->makeForm('files',$widgetLang,$widgetPath,$assetPath); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="panel panel-default clearfix">
                                    <div class="panel-heading clearfix">
                                        <h5>Animazione</h5>
                                    </div>
                                    <div class="panel-body clearfix">
                                        <?php echo $widget->makeForm('animations',$widgetLang,$widgetPath); ?>
                                    </div>
                                </div>
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
    <bs-toolbar-group data-group-label="Widget">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-floppy-o"
            data-permission="/admin/content/edit"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-event="bs.content.edit"
            data-title="Salva"
            data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-image"
            data-permission="/admin/content/edit"
            data-event="bs.content.image"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Carica immagine"
            data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>