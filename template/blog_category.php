<?php function drawCategories($category)
{
    echo '<ol class="recursive-dented-list">';
    foreach ($category as $item) {
        echo '<li id="'.$item->id.'"><span>';
        echo $item->postCategoryTranslation->getFirst()->name;
        echo '</span></li>';
        if (!$item->childrenPostCategory->isEmpty()) {
            drawCategories($item->childrenPostCategory);
        }

    }
    echo '</ol>';
} ?>
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
                                <h5>Visualizza struttura</h5>
                            </div>
                            <div class="panel-body">
                                <?php drawCategories($rootCats); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h5>Aggiungi Categoria</h5>
                            </div>
                            <div class="panel-body">
                                <form name="add-category">
                                    <div class="col-md-6">
                                        <div class="form-group form-group-default">
                                            <label>Nome Categoria</label>
                                            <input type="text"
                                                   name="PostCategoryTranslation.name"
                                                   value=""
                                                   autocomplete="off"
                                                   class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group form-group-default selectize-enabled">

                                            <label for="Shop">Shop</label>
                                            <select class="full-width"
                                                    placeholder="Seleziona il padre"
                                                    data-init-plugin="selectize" title=""
                                                    name="PostCategory.parentPostCategoryId"
                                                    id="PostCategory.parentPostCategoryId">
                                                <option></option>
                                                <?php foreach ($cats as $cat): ?>
                                                    <option value="<?php echo $cat->id ?>"><?php echo $cat->postCategoryTranslation->getFirst()->name ?></option>
                                                <?php endforeach; ?>
                                            </select>

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
                data-event="bs.postCategory.add"
                data-title="Salva"
                data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-trash-o"
                data-permission="/admin/marketing"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.postCategory.delete"
                data-title="Salva"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>