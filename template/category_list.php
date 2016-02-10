<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php"?>
    <?php echo $app->getAssets(['ui','forms'], $page); ?>
    <title><?php echo $page->getTitle(); ?></title>
</head>
<body class="fixed-header">
<?php include "parts/sidebar.php";?>
<div class="page-container">
    <?php include "parts/header.php" ?>
    <?php include "parts/operations.php" ?>

    <div class="page-content-wrapper">
        <div class="content sm-gutter">
            <div class="container-fluid container-fixed-lg bg-white">
                <div class="row">
                    <div class="col-md-4 col-md-offset-4 alert-container closed">

                    </div>
                </div>
            </div>

            <div class="container-fluid container-fixed-lg bg-white">
                <!-- START PANEL -->
                <div class="panel panel-transparent">
                    <div class="panel-heading">
                        <div class="panel-title">Elenco brands
                        </div>
                        <div class="export-options-container pull-right"></div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped" id="smallTable">
                            <thead>
                                <tr>
                                    <th class="center sorting">Slug</th>
                                    <th class="center sorting">Modifica</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach($categories as $val): ?>

                                <tr class="odd gradeX">
                                    <td class="center"><?php echo $val['slug'];?></td>
                                    <td class="center"><a href="<?php echo $modifica."?productCategoryId=".$val['id'] ?>"><i class="fa fa-pencil-square-o"></i></a></td>
                                </tr>
                                <?php endforeach;?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- END PANEL -->
            </div>
        </div>
        <?php include "parts/footer.php" ?>
    </div>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
</body>
</html>