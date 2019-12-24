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
            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped responsive" width="100%"
                               data-datatable-name="newsletter_template_list"
                               data-controller="EmailTemplateListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="100, 200, 500, 1000, 2000"
                               data-display-length="200">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Id
                                </th>
                                <th data-slug="name"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Nome Template
                                </th>
                                <th data-slug="shopName"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Shop
                                </th>
                                <th data-slug="oldTemplatephp"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Vecchio Template Core
                                </th>
                                <th data-slug="scope"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Utilizzo
                                </th>
                                <th data-slug="description"
                                         data-searchable="true"
                                         data-orderable="true"
                                         class="center">Descrizione
                                </th>
                                <th data-slug="subject"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Oggetto
                                </th>
                                <th data-slug="isActive"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Attivo
                                </th>
                                <th data-slug="template"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">template
                                </th>

                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
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
    <bs-toolbar-group data-group-label="Esportazione">
        <bs-toolbar-button
            data-remote="bs.lists.generate.csv"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.lists.create.newslettertemplate"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.lists.modify.newslettertemplate"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.lists.delete.newslettertemplate"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>