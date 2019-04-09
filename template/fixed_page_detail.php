<?php
/**
 * Created by PhpStorm.
 * User: jurif
 * Date: 08/01/2018
 * Time: 16:35
 */
?>
    <!DOCTYPE html>
    <html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui', 'forms', 'tables'], $page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
    <script type="application/javascript"
            src="https://cloud.tinymce.com/5/tinymce.min.js?apiKey=z3tiwzxrspg36g21tiusdfsqt9f27isw6547l88aw19e0qej"></script>
</head>
<body class="fixed-header">
<?php include "parts/sidebar.php"; ?>
<div class="page-container">
    <?php include "parts/header.php" ?>
    <?php include "parts/operations.php" ?>

    <div class="page-content-wrapper">

        <div class="content sm-gutter">
            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <div class="row">
                            <div class="panel-body">
                                <strong style="display: block">Tipo pagina</strong>
                                <select id="fixedPageType">
                                    <?php if (!is_null($fixedPage)): ?>
                                        <option value="<?php echo $fixedPage->fixedPageTypeId ?>"> <?php echo $fixedPage->fixedPageType->name ?></option>
                                    <?php else:
                                        foreach ($fixedPageTypes as $fixedPageType):
                                            ?>
                                            <option value="<?php echo $fixedPageType->id ?>"><?php echo $fixedPageType->name; ?></option>
                                        <?php
                                        endforeach;
                                    endif; ?>
                                </select>
                            </div>

                            <div class="panel-body">
                                <strong style="display: block">Lingua</strong>
                                <select id="lang">
                                    <?php if (!is_null($fixedPage)): ?>
                                        <option value="<?php echo $fixedPage->langId ?>"> <?php echo $fixedPage->lang->name ?></option>
                                    <?php else:
                                        foreach ($langs as $lang):
                                            ?>
                                            <option value="<?php echo $lang->id ?>"><?php echo $lang->name; ?></option>
                                        <?php
                                        endforeach;
                                    endif; ?>
                                </select>
                            </div>

                            <?php if ((!is_null($fixedPage) && $fixedPage->fixedPageTypeId != 3) || is_null($fixedPage)): ?>
                                <div id="optionalPart">
                                    <div class="panel-body">
                                        <strong style="display: block">Inserisci il title</strong>
                                        <input type="text" id="title" placeholder="Title"
                                               value="<?php if (!is_null($fixedPage)) echo $fixedPage->title; ?>"
                                               style="width: 30%;">
                                    </div>
                                    <div class="panel-body">
                                        <strong style="display: block">Inserisci il sottotitolo</strong>
                                        <input type="text" id="subTitle" placeholder="Sottotitolo"
                                               value="<?php if (!is_null($fixedPage)) echo $fixedPage->subtitle; ?>"
                                               style="width: 30%;">
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="panel-body">
                                <strong style="display: block">Inserisci lo slug</strong>
                                <input type="text" id="slug" placeholder="Slug"
                                       value="<?php if (!is_null($fixedPage)) echo $fixedPage->slug; ?>"
                                       style="width: 30%;">
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <textarea id="page-fixed-content"
                                          style="height: 300px"><?php if (!is_null($fixedPage)) echo $fixedPage->text; ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <strong style="display: block">Inserisci il TAG TITLE</strong>
                        <input type="text" id="titleTag" placeholder="Tag title (Caratteri: min 50 | max 60)"
                               value="<?php if (!is_null($fixedPage)) echo $fixedPage->titleTag; ?>"
                               style="width: 30%;">
                    </div>
                    <div class="panel-body">
                        <strong style="display: block">Inserisci la META DESCRIPTION</strong>
                        <textarea id="metaDescription" placeholder="Meta Description (Caratteri: min 50 | max 300)"
                                  style="width: 30%;"
                                  rows="10"><?php if (!is_null($fixedPage)) echo $fixedPage->metaDescription; ?></textarea>
                    </div>
                </div>
            </div>
        </div>
        <?php include "parts/footer.php" ?>
    </div>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Gestione">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-save"
                data-permission="allShops"
                data-event="bs.fixedPageSave"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Salva"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
    </html><?php
