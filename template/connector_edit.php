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
                    <div class="col-sm-9 replicaContainer">
                        <div class="panel panel-default clearfix">
                            <div class="panel-heading clearfix">
                                <h5>Importatori Connettori
                                <?php echo ' '. $shop->title;?>
                                </h5>
                            </div>
                            <div class="panel panel-default clearfix" id="section">
                                <div class="panel-heading clearfix">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="panel-title">
                                                <h5>Connettore #<bs-rcounter data-target="section" /></h5>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="size">Gruppo taglia</label>
                                                <select data-json="section.size" class="full-width selectpicker" placeholder="Sel. gruppo taglia" data-init-plugin="selectize" tabindex="-1" title="">
                                                    <option></option>
                                                    <?php foreach ($productSizeGroup as $sizeGr) {?>
                                                        <option value="<?php echo $sizeGr->id ?>">
                                                            <?php echo $sizeGr->macroName . ' ' . $sizeGr->locale . ''?>
                                                        </option>
                                                    <?php }?>
                                                </select>
                                            </div>
                                        </div>
                                        <!--<div class="col-sm-3">
                                            <bs-button data-tag="a" data-icon="fa-plus" data-class="btn btn-default" data-event="bs.replica.section"></bs-button>
                                        </div>-->
                                    </div>
                                </div>
                                <div class="panel clearfix">
                                    <div class="panel panel-body clearfix">
                                        <div class="fieldReplicaContainer">
                                        <div class="row" id="field">
                                            <div class="col-sm-2">
                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="modifier">Modificatore</label>
                                                    <select data-json="field.modifier" class="full-width selectpicker" placeholder="Sel. modificatore" data-init-plugin="selectize" tabindex="-1" title="">
                                                        <option></option>
                                                        <?php foreach ($importerFieldModifier as $fieldModifier): ?>
                                                            <option value="<?php echo $fieldModifier->id; ?>" required>
                                                                <?php echo $fieldModifier->modifier . ""?>
                                                            </option>
                                                            <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="field">Campo</label>
                                                    <select data-json="field.field" class="full-width selectpicker" placeholder="Sel. campo" data-init-plugin="selectize" tabindex="-1" title="">
                                                        <option></option>
                                                        <?php foreach ($importerField as $field): ?>
                                                            <option value="<?php echo $field->id; ?>" required>
                                                                <?php echo $field->title . ""?>
                                                            </option>
                                                            <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="operator">Operatore</label>
                                                    <select data-json="field.operator" class="full-width selectpicker" placeholder="Sel. operatore" data-init-plugin="selectize" tabindex="-1" title="">
                                                        <option></option>
                                                        <?php foreach ($importerOperator as $operator): ?>
                                                            <option value="<?php echo $operator->id; ?>" required>
                                                                <?php echo $operator->title . ""?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div class="form-group form-group-default">
                                                    <label for="value">Valore</label>
                                                    <input type="text" data-json="field.value" class="form-control" />
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="logic">Connettore</label>
                                                    <select data-json="field.connector" class="full-width selectpicker" placeholder="Sel. connettore" data-init-plugin="selectize" tabindex="-1" title="">
                                                        <option></option>
                                                        <?php foreach ($importerLogicConnector as $logicConnector): ?>
                                                            <option value="<?php echo $logicConnector->id; ?>" required>
                                                                <?php echo $logicConnector->name . ""?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <br><bs-button data-tag="a" data-icon="fa-plus" data-class="btn btn-default" data-event="bs.replica.field"></bs-button>
                                            </div>
                                        </div>
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="panel panel-default clearfix">
                            <div class="panel-heading clearfix">
                                <h5>Gruppi taglie</h5>
                            </div>
                            <div class="panel-body clearfix">
                                <?php foreach ($productSizeGroup as $sizeGroup) {
                                    if ($sizeGroup->id == $value) {
                                        echo '<p><strike>' . $sizeGroup->macroName . ' ' . $sizeGroup->locale. '</strike></p>';
                                    } else {
                                        echo '<p>' . $sizeGroup->macroName . ' ' . $sizeGroup->locale. '</p>';
                                    }
                                }?>
                            </div>
                        </div>

                    </div>
                </div>
                <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post" autocomplete="off">
                    <input type="hidden" name="json" id="json" />
                </form>
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
            data-event="bs.save.connector"
            data-title="Salva"
            data-placement="bottom"
            ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>