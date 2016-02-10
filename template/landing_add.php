<?php $date = new DateTime(); ?>
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
                    <div class="col-sm-9 replicaContainer">
                        <div class="panel panel-default clearfix landing-header">
                            <div class="panel-heading clearfix">
                                <h5>Intestazione della landing page</h5>
                                <input type="hidden" data-json="updatedBy" value="<?php echo $user->getName()." ".$user->getSurname()?>" />
                                <input type="hidden" data-json="creationDate" value="" />
                                <input type="hidden" data-json="updateDate" value="" />
                            </div>
                            <div class="panel-body clearfix">
                                <div class="row">
                                    <div class="col-sm-8 col-xs-12">
                                        <div class="form-group form-group-default required">
                                            <label for="title">Titolo</label>
                                            <input type="text" data-json="title" autocomplete="off" class="form-control" required />
                                            <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 col-xs-11">
                                        <div class="form-group form-group-default required">
                                            <label for="code">Codice</label>
                                            <input type="text" data-json="code" autocomplete="off" class="form-control" required />
                                            <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-1 col-xs-1 pull-right">
                                        <bs-button data-tag="a" data-icon="fa-random" data-class="btn btn-default btn-form" data-event="bs.landing.randomcode"></bs-button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group form-group-default required">
                                            <label for="subtitle">Sottotitolo</label>
                                            <input type="text" data-json="subtitle" autocomplete="off" class="form-control"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group form-group-default required">
                                            <label for="introTitle">Titolo introduttivo</label>
                                            <input type="text" data-json="introTitle" autocomplete="off" class="form-control"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <div class="summernote-wrapper">
                                                <label for="introText">Testo</label>
                                                <textarea class="summer" data-json="introText" rows="10"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group form-group-default">
                                            <label>Banner</label>
                                            <input type="text" data-json="banner" autocomplete="off" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group form-group-default">
                                            <label>Colore</label>
                                            <div class="input-group color-picker">
                                                <input type="text" value="" data-json="bannerColor" class="form-control" />
                                                <span class="input-group-addon"><i></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group form-group-default">
                                            <label>Link</label>
                                            <input type="text" data-json="bannerLink" autocomplete="off" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-default clearfix">
                            <div class="panel-heading clearfix">
                                <h5>Landing network</h5>
                            </div>
                            <div class="panel-body clearfix">
                                <div class="row">
                                    <div class="col-sm-12 m-b-10">
                                        <bs-button data-tag="a" data-icon="fa-plus" data-class="btn btn-default" data-event="bs.replica.link"></bs-button>
                                        <span class="buttonLabel">Aggiungi un link</span>
                                    </div>
                                </div>
                                <div class="linkReplicaContainer">
                                    <div class="row" id="link">
                                        <div class="col-sm-6">
                                            <div class="form-group form-group-default">
                                                <label>Etichetta</label>
                                                <input type="text" data-json="links.label" autocomplete="off" class="form-control"/>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group form-group-default">
                                                <label>Link</label>
                                                <input type="text" data-json="links.href" autocomplete="off" class="form-control"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-default clearfix">
                            <div class="panel-heading clearfix">
                                <h5>Sezioni della landing</h5>
                            </div>
                            <div class="panel-body clearfix">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <bs-button data-tag="a" data-icon="fa-plus" data-class="btn btn-default" data-event="bs.replica.section"></bs-button>
                                        <span class="buttonLabel">Aggiungi una nuova sezione</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-default clearfix" id="section">
                            <div class="panel-heading clearfix">
                                <div class="panel-title">
                                    <h5>Sezione #<bs-rcounter data-target="section" /></h5>
                                </div>
                                <div class="panel-controls">
                                    <ul>
                                        <li><i class="fa fa-close"></i></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="panel-body clearfix">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group form-group-default required">
                                            <label>Titolo</label>
                                            <input type="text" data-json="section.title" autocomplete="off" class="form-control" required />
                                            <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <div class="summernote-wrapper">
                                                <label>Testo</label>
                                                <textarea class="summer" data-json="section.text" rows="10"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 styleRepeat">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <div class="form-group form-group-default">
                                                    <label>Banner</label>
                                                    <input type="text" data-json="section.banner" autocomplete="off" class="form-control" />
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group form-group-default">
                                                    <label>Colore</label>
                                                    <div class="input-group color-picker">
                                                        <input type="text" value="" data-json="section.bannerColor" class="form-control" />
                                                        <span class="input-group-addon"><i></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group form-group-default">
                                                    <label>Link</label>
                                                    <input type="text" data-json="section.bannerLink" autocomplete="off" class="form-control" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12 m-b-10">
                                                <bs-button data-tag="a" data-icon="fa-plus" data-class="btn btn-default" data-event="bs.replica.style"></bs-button>
                                                <span class="buttonLabel">Aggiungi una vista del catalogo</span>
                                            </div>
                                        </div>
                                        <div class="row" id="style">
                                            <div class="col-sm-12">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="form-group form-group-default selectize-enabled required">
                                                            <label>Brand</label>
                                                            <select data-json="style.brand" class="full-width" placeholder="Seleziona il brand" data-init-plugin="selectize" tabindex="-1" title="" required>
                                                                <option></option>
                                                                <?php foreach ($brandList as $brand): ?>
                                                                <option value="<?php echo $brand->id ?>"><?php echo $brand->name ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="form-group form-group-default selectize-enabled required">
                                                            <label>Categoria</label>
                                                            <select data-json="style.category" class="full-width" placeholder="Seleziona una categoria" data-init-plugin="selectize" tabindex="-1" title="" required>
                                                                <option></option>
                                                                <?php foreach ($categoryList as $id => $path): ?>
                                                                <option value="<?php echo $id;?>"><?php echo $path; ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="panel panel-default clearfix canonical">
                            <div class="panel-heading clearfix">
                                <h5>Metadati</h5>
                            </div>
                            <div class="panel-body clearfix">
                                <p><strong><i class="fa fa-user"></i> Autore</strong> <?php echo $user->getName()." ".$user->getSurname()?></span></p>
                                <p><strong><i class="fa fa-calendar"></i> Data creazione</strong> <?php echo $date->format('d-m-Y'); ?></p>
                                <div class="form-group form-group-default required">
                                    <label>Canonical</label>
                                    <input type="text" data-json="canonical" class="form-control" required="required" aria-required="true" />
                                    <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default clearfix followme landing-summary">
                            <div class="panel-heading clearfix">
                                <h5>Struttura</h5>
                            </div>
                            <div class="panel-body clearfix">
                                <!-- filled by Echo -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-9">
                        <div class="panel panel-default clearfix">
                            <div class="panel-heading clearfix">
                                <h5>Potrebbe piacerti anche</h5>
                            </div>
                            <div class="panel-body clearfix">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group form-group-default selectize-enabled required">
                                            <label>Categorie</label>
                                            <select data-json="youmightlike" class="full-width" multiple="multiple" placeholder="Seleziona una o più categorie" data-init-plugin="selectize" tabindex="-1" title="" required>
                                                <option></option>
                                                <?php foreach ($categoryList as $id => $path): ?>
                                                    <option value="<?php echo $id;?>"><?php echo $path; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post" autocomplete="off">
                    <input type="hidden" name="json" id="json" />
                </form>
            </div>
        </div>
        <?php include "parts/footer.php"?>
    </div>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Gestione landing">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-floppy-o"
            data-permission="/admin/marketing"
            data-event="bs.save.landing"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Salva"
            data-placement="bottom"
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-eye"
            data-permission="/admin/marketing"
            data-event="bs.preview.landing"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Anteprima"
            data-placement="bottom"
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-trash"
            data-permission="/admin/marketing"
            data-event="bs.del.landing"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Elimina"
            data-placement="bottom"
            data-toggle="modal"
            data-target="#bsModal"
            ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>