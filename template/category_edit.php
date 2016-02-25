<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php"; ?>
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
                    <div class="col-md-4 col-md-offset-4 alert-container closed">

                    </div>
                </div>
            </div>

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <form id="form-project" role="form"
                              action="<?php echo $app->urlForBlueseal() ?>/category?productCategoryId=<?php echo $_GET['productCategoryId'] ?>"
                              method="POST" autocomplete="on">

                            <?php
                            $depth = 1;
                            foreach ($categories as $cat): ?>
                                <div class="row">
                                    <?php
                                    $n = floor((12 - 1) / ($langs->count() + 1)); ?>
                                    <div class="col-sm-<?php echo $n ?>">
                                        <div class="form-group form-group-default">
                                            <label><?php echo $cat->slug . ' - slug ' ?></label>
                                            <input type="text" class="form-control"
                                                   name="<?php echo 'cat_' . $cat->id . '_slug' ?>"
                                                   value="<?php echo $cat->slug ?>">
                                        </div>
                                    </div>
                                    <?php
                                    foreach ($langs as $lang): ?>
                                        <div class="col-sm-<?php echo $n ?>">
                                            <div class="form-group form-group-default">
                                                <label><?php echo $cat->slug . ' - ' . $lang->name ?></label>
                                                <input type="text" class="form-control"
                                                       name="<?php echo 'cat_' . $cat->id . '_' . $lang->id; ?>"
                                                       value="<?php
                                                       if ($catLang = $categoryLang->findOneBy(['productCategoryId'=>$cat->id,'langId'=>$lang->id]))
                                                       //if ($val = $cat->productCategoryHasLang->findOneByKey('langId', $lang->id))
                                                           echo $catLang->name;
                                                       else echo '' ?>">
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    <div class="col-sm-1">
                                        <a href="<?php echo $elenco_prodotti . '?search=' . urlencode('productCategoryId:' . $cat->id) ?>"><i
                                                class="fa fa-search"></i></a>
                                    </div>

                                </div>
                            <?php endforeach; ?>
                            <button class="btn btn-success" type="submit">Modifica</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php include "parts/footer.php" ?>
    </div>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
</body>
</html>