<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui', 'forms', 'tables'], $page); ?>
    <title>99Monkeys - <?php echo $page->getTitle(); ?></title>
</head>
<body class="fixed-header">
<?php include "parts/sidebar.php"; ?>
<div class="page-container">
    <?php include "parts/header.php"; ?>
    <?php include "parts/operations.php" ?>

    <div class="page-content-wrapper">
        <div class="content sm-gutter">

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped responsive"
                               width="100%"

                               data-datatable-name="blog_list"
                               data-controller="BlogPostListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="false"
                                    class="center">ID</th>
                                <th data-slug="coverImage"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Cover</th>
                                <th data-slug="typeBlog"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Tipp Blog</th>
                                <th data-slug="title"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Titolo</th>
                                <th data-slug="content"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Estratto</th>
                                <th data-slug="creationDate"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="desc"
                                    class="center dataFilterType">Creazione</th>
                                <th data-slug="url"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Url Post</th>
                                <th data-slug="publishDate"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center dataFilterType">Pubblicazione</th>
                                <th data-slug="stato"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Stato</th>
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
    <bs-toolbar-group data-group-label="Gestione post">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-file-o fa-plus"
            data-permission="/admin/content/add"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Nuovo post"
            data-placement="bottom"
            data-href="<?php echo $addUrl; ?>"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-cloud-upload"
            data-permission="/admin/content/publish"
            data-event="bs.post.publish"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Pubblica i post"
            data-placement="bottom"
        ></bs-toolbar-button>
        <!--
            data-tag="a"
            data-icon="fa-clone"
            data-permission="/admin/content/add"
            data-event="bs.dupe.post"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Duplica un post"
            data-placement="bottom"
            >< -->
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-trash"
            data-permission="/admin/content/delete"
            data-event="bs.post.delete"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Elimina un post"
            data-placement="bottom"
            data-toggle="modal"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>