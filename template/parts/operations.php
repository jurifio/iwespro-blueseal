<div class="operations">
    <div class="row">
        <div class="col-md-12">
            <ul class="breadcrumb">
                <li><p>BlueSeal</p></li>

                <?php
                $find=$page->getTitle();
                $findPageTranslation=\Monkey::app()->repoFactory->create('PageTranslation')->findOneBy(['title'=>$find]);
                $findPost=\Monkey::app()->repoFactory->create('Page')->findOneBy(['id'=>$findPageTranslation->pageId]);
                $postId=$findPost->postId;
                if($postId!=null){
                    $help='<a target="_blank" href="/blueseal/help/blog/'.$postId.'" class="btn btn-light" role="button"><i class="fa fa-info" aria-hidden="true"></i> Help Online </a>';
                } else {
                    $help='';
                }?>
                <li><a href="<?php echo $page->getUrl(); ?>" class="active"><?php echo $page->getTitle(); ?></a></li> <?php echo $help;?>
            </ul>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 toolbar-container">
            <div class="bs-toolbar"></div>
        </div>
    </div>
</div>