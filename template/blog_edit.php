<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php"?>
    <?php echo $app->getAssets(['ui','forms'], $page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
</head>
<body class="fixed-header">
<?php include "parts/sidebar.php";?>
<div class="page-container">
    <?php include "parts/header.php";?>
    <?php include "parts/operations.php";?>
    <div class="page-content-wrapper">
        <div class="content sm-gutter">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-9">
                        <div class="panel panel-default clearfix landing-header">
                            <div class="panel-heading clearfix">
                                <h5>Nuovo post</h5>
                                <input type="hidden" data-json="Post.blogId" value="<?php echo $post->blogId ?>" />
                                <input type="hidden" data-json="Post.id" value="<?php echo $post->id ?>" />
                                <input type="hidden" data-json="PostTranslation.langId" value="1" />
                            </div>
                            <div class="panel-body clearfix">
                                <div class="row">
                                    <div class="col-sm-12 col-xs-12">
                                        <div class="form-group form-group-default required">
                                            <label>Titolo</label>
                                            <input type="text" data-json="PostTranslation.title" value="<?php echo $post->postTranslation->getFirst()->title ?>" autocomplete="off" class="form-control" required />
                                            <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-xs-12">
                                        <div class="form-group form-group-default">
                                            <label>Sottotitolo</label>
                                            <input type="text" data-json="PostTranslation.subtitle" value="<?php echo $post->postTranslation->getFirst()->subtitle ?>" autocomplete="off" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <div class="summernote-wrapper">
                                                <label>Contenuto del post</label>
                                                <textarea class="summer" data-json="PostTranslation.content" rows="50"><?php echo $post->postTranslation->getFirst()->content ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-xs-12">
                                    <div class="form-group form-group-default">
                                        <label>Autore (visibile dagli utenti)</label>
                                        <input type="text" data-json="Post.author" value="<?php echo $post->author ?>" autocomplete="off" class="form-control" />
                                    </div>
                                </div>


                                <div class="col-sm-12 col-xs-12">
                                    <div class="form-group form-group-default">
                                        <strong style="display: block">Inserisci il TAG TITLE</strong>
                                        <input type="text" id="titleTag" placeholder="Tag title (Caratteri: min 50 | max 60)"
                                               data-json="PostTranslation.titleTag" value="<?php echo $post->postTranslation->getFirst()->titleTag; ?>" style="width: 30%;">
                                    </div>
                                </div>

                                <div class="col-sm-12 col-xs-12">
                                    <div class="form-group form-group-default">
                                        <strong style="display: block">Inserisci la META DESCRIPTION</strong>
                                        <textarea id="metaDescription" placeholder="Meta Description (Caratteri: min 50 | max 300)" style="width: 30%;"
                                                  data-json="PostTranslation.metaDescription" rows="10"><?php echo $post->postTranslation->getFirst()->metaDescription; ?></textarea>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="panel panel-default clearfix">
                            <div class="panel-heading clearfix">
                                <h5>Metadati</h5>
                            </div>
                            <div class="panel-body clearfix">
                                <p><strong><i class="fa fa-user"></i> Autore</strong> <?php echo $post->userDetails->name.' '.$post->userDetails->surname ?></span></p>
                                <p><strong><i class="fa fa-calendar"></i> Data creazione</strong> <?php echo $post->creationDate; ?></p>
                                <input type="hidden" data-json="Post.userId" value="<?php echo $post->userDetails->userId; ?>"/>
                            </div>
                        </div>
                        <div class="panel panel-default clearfix">
                            <div class="panel-heading clearfix">
                                <h5 data-toggle="popover" data-placement="left" data-container="body" data-title="Pubblicazione posticipata" data-content="Impostare una data di pubblicazione nel futuro farà in modo che il post si auto-pubblichi in quella data/ora."><i class="fa fa-question-circle"></i> Pubblicazione</h5>
                            </div>
                            <div class="panel-body clearfix">
                                <div class="form-group form-group-default">
                                    <label>Data pubblicazione</label>
                                    <input type="datetime-local" data-json="Post.publishDate" value="<?php echo (new DateTime($post->publishDate))->format('Y-m-d\TH:i'); ?>" autocomplete="off" class="form-control" title="Data pubblicazione"/>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default clearfix">
                            <div class="panel-heading clearfix">
                                <h5>Classificazione</h5>
                            </div>
                            <div class="panel-body clearfix">
                                <div class="form-group form-group-default selectize-enabled required">
                                    <label>Categorie</label>
                                    <select data-json="PostHasPostCategory.id" class="full-width" multiple="multiple" placeholder="Seleziona una o più categorie" data-init-plugin="selectize" tabindex="-1" title="" required>
                                        <?php foreach ($cats as $cat): ?>
                                        <option value="<?php echo $cat->id ?>"<?php if($post->postCategory->findOneByKey('id',$cat->id)) echo ' selected="selected"'; ?>><?php echo $cat->postCategoryTranslation->getFirst()->name ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group form-group-default selectize-enabled required">
                                    <label>Tag</label>
                                    <select data-json="PostHasPostTag.id" class="full-width" multiple="multiple" placeholder="Seleziona una o più tag" data-init-plugin="selectize" tabindex="-1" title="" required>
                                        <?php foreach ($tags as $tag): ?>
                                        <option value="<?php echo $tag->id ?>"<?php if($post->postTag->findOneByKey('id',$tag->id)) echo ' selected="selected"';?>><?php echo $tag->postTagTranslation->getFirst()->name ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default clearfix">
                            <div class="panel-heading clearfix">
                                <h5 data-toggle="popover" data-placement="left" data-container="body" data-title="Formato e dimensioni" data-content="Formato 16:9: larghezza minima 1140px"><i class="fa fa-question-circle"></i> Cover photo</h5>
                            </div>
                            <div class="panel-body clearfix">
                                <img src="<?php echo !empty($post->postTranslation->getFirst()->coverImage) ? "/assets/".$post->postTranslation->getFirst()->coverImage : $defaultImage ?>" id="cover" class="img-responsive image-uploader" />
                                <div class="form-group form-group-default" style="display:none;">
                                    <label>Cover photo</label>
                                    <input type="file" data-json="PostTranslation.coverImage" autocomplete="off" class="form-control" title="Cover photo"/>
                                </div>
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
    <bs-toolbar-group data-group-label="&nbsp;">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-floppy-o"
            data-permission="/admin/content/add"
            data-event="bs.post.save"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Salva"
            data-placement="bottom"
        ></bs-toolbar-button><bs-toolbar-button
            data-tag="a"
            data-icon="fa-trash-o"
            data-permission="/admin/content/delete"
            data-event="bs.post.delete"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Elimina"
            data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Stato del post">
        <bs-toolbar-select
            data-tag="select"
            data-icon="fa-random"
            data-permission="/admin/content/publish"
            data-rel="tooltip"
            data-button="false"
            data-placement="bottom"
            data-class="btn btn-default"
            data-json="Post.postStatusId"
            data-title="Modifica stato"
            data-event="bs.post.changestatus"
            data-options='<?php echo json_encode($statuses); ?>'
        ></bs-toolbar-select>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>