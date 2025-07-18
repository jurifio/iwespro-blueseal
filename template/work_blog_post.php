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

            <!-- Begin page top -->
            <section class="page-top">
                <div class="container">
                    <div class="page-top-in text-center">
                        <h1>
                            <?php
                                echo $post->postTranslation->getFirst()->title;
                             ?>
                        </h1>
                    </div>
                </div>
            </section>
            <!-- End page top -->

            <div class="container">
                <div class="row">
                    <div class="col-md-offset-1 col-md-10">
                        <div class="blog-posts single-post">
                                <article class="post post-large blog-single-post">
                                    <h4><?php echo $post->postTranslation->getFirst()->subtitle; ?></h4>
                                    <div class="post-image single">
                                        <img class="img-responsive"
                                             src="<?php echo $app->image($post->postTranslation->getFirst()->coverImage); ?>"
                                             alt="Blog">
                                    </div>
                                    <div class="post-content">
                                        <?php echo $post->postTranslation->getFirst()->content ?>
                                    </div>
                                </article>
                            <br />&nbsp;<br />&nbsp;<br />&nbsp;<br />
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
</body>
</html>