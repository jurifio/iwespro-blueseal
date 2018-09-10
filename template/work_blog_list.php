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
    <?php include "parts/header.php"; ?>
    <?php include "parts/operations.php" ?>

    <div class="page-content-wrapper">
        <div class="content sm-gutter">

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <div class="blog-work" style="position: relative;">
                            <?php
                            foreach($posts as $post) {
                                $title = $post->postTranslation->getFirst()->title;
                                $id = $post->id;
                                $s = new \bamboo\core\utils\slugify\CSlugify();
                                $address = $app->baseUrl(false) . '/blueseal/work/blog/' .$post->id;
                                ?>
                                <div class="col-xs-6 col-md-4 post-mansory-item animation animated fadeInUp">
                                    <article class="post post-medium">
                                        <div class="post-image single">
                                            <a href="<?php echo $address; ?>"><img class="img-responsive" src="<?php echo $app->image($post->postTranslation->getFirst()->coverImage); ?>" alt="Blog"></a>
                                        </div>
                                        <div class="post-content">
                                            <h3><a href="<?php echo $address; ?>"><?php echo $post->postTranslation->getFirst()->title; ?></a></h3>

                                            <p><?php echo $post->postTranslation->getFirst()->subtitle;  ?></p>
                                            <div class="post-meta post-meta-foot">
                                                <span class="pull-left"><i class="fa fa-clock-o"></i><?php echo $post->publishDate;?></span>
                                                <!--<span class="pull-right"><i class="fa fa-comment-o"></i> <a href="#">212 Comments</a></span>-->
                                            </div>
                                        </div>

                                    </article>
                                </div>
                            <?php } // fine loop posts?>
                        </div>
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

    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>