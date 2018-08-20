<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php"?>
    <?php echo $app->getAssets(['ui','forms','tables'], $page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
</head>
<body class="fixed-header">
<?php include "parts/sidebar.php";?>
<div class="page-container">
    <?php include "parts/header.php";?>
    <?php include "parts/operations.php" ?>

    <div class="page-content-wrapper">
        <div class="content sm-gutter">

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <div class="col-md-3">
                        <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#checkGender">GENDER</button>
                        <div id="checkGender" class="collapse">
                            <?php foreach ($gendRes as $genderId => $numGP): ?>
                                <div><input type="checkbox" name="<?php echo $genderId ?>" value="<?php echo $genderId ?>" /> <?php echo $numGP['name'] . "(". $numGP['count'] .")"; ?> </div>
                            <?php endforeach; ?>
                        </div>
                        </div>

                        <div class="col-md-3">
                            <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#checkMacroCatGroup">MACRO CATEGORIE</button>
                            <div id="checkMacroCatGroup" class="collapse">
                                <?php foreach ($macroCatRes as $macroCatiD => $numMCP): ?>
                                    <div><input type="checkbox" name="<?php echo $macroCatiD ?>" value="<?php echo $macroCatiD ?>" /> <?php echo $numMCP['name'] . "(". $numMCP['count'] .")"; ?> </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#checkCatGroup">CATEGORIE</button>
                            <div id="checkCatGroup" class="collapse">
                                <?php foreach ($catGroupRes as $catGroupId => $numCP): ?>
                                    <div><input type="checkbox" name="<?php echo $catGroupId ?>" value="<?php echo $catGroupId ?>" /> <?php echo $numCP['name'] . "(". $numCP['count'] .")"; ?> </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#checkMaterial">MATERIALI</button>
                            <div id="checkMaterial" class="collapse">
                                <?php foreach ($matRes as $matId => $numMP): ?>
                                    <div><input type="checkbox" name="<?php echo $matId ?>" value="<?php echo $matId ?>" /> <?php echo $numMP['name'] . "(". $numMP['count'] .")"; ?> </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include "parts/footer.php"?>
    </div>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Elimina categorie">
        <bs-toolbar-button
                data-remote="bs.delete.category.research.fason"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>