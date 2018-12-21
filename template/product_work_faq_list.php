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

            <div class="container" style="margin-top: 90px">
                <div class="panel-group" id="faqAccordion">

                    <input type="hidden" value="<?php echo $allShops; ?>" id="permCheck">

                    <div>
                        <div class="form-group"> <!-- Email field !-->
                            <label for="search" class="control-label">Cerca</label>
                            <input type="text" class="form-control" id="search" name="search" placeholder="Cerca qui">
                        </div>
                    </div>

                    <div id="allFaq">

                        <?php
                        /** @var \bamboo\domain\entities\CFaqArgument $faqArgument */
                        foreach ($faqArguments as $faqArgument):

                            echo '
                                  <div style="margin: 20px 0px 5px 0px">
                                    <strong>'.$faqArgument->text.'</strong>
                                  </div>
                                    <div class="allArgs" id="sec-'. $faqArgument->id .'">';


                           /** @var \bamboo\domain\entities\CFaq $faq */
                           foreach ($faqArgument->faq as $faq): ?>

                               <div class="panel panel-default ">
                                   <div class="panel-heading accordion-toggle question-toggle collapsed"
                                        data-toggle="collapse"
                                        data-parent="#faqAccordion" data-target="#<?php echo $faq->id; ?>">
                                       <h4 class="panel-title">
                                           <a href="#" class="ing"><?php if($allShops) echo '(Id: '. $faq->id . ')'; ?> D: <?php echo $faq->question; ?></a>
                                       </h4>
                                   </div>
                                   <div id="<?php echo $faq->id; ?>" class="panel-collapse collapse" style="height: 0px;">
                                       <div class="panel-body">
                                           <h5><span class="label label-primary">Risposta</span></h5>

                                           <p>
                                               <?php echo $faq->answer; ?>
                                           </p>
                                       </div>
                                   </div>
                               </div>

                           <?php endforeach;
                           echo '</div>';
                           endforeach; ?>
                    </div>
                </div>
                <!--/panel-group-->
            </div>
        </div>
    </div>
    <?php include "parts/footer.php" ?>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <?php if ($allShops): ?>
        <bs-toolbar-group data-group-label="Faq">
            <bs-toolbar-button
                    data-tag="a"
                    data-icon="fa-plus"
                    data-permission="/admin/product/add"
                    data-class="btn btn-default"
                    data-rel="tooltip"
                    data-event="bs.add.new.faq"
                    data-title="Crea una nuova faq"
                    data-placement="bottom"
            ></bs-toolbar-button>
            <bs-toolbar-button
                    data-tag="a"
                    data-icon="fa-pencil"
                    data-permission="/admin/product/add"
                    data-class="btn btn-default"
                    data-rel="tooltip"
                    data-event="bs.modify.faq"
                    data-title="Modifica Faq"
                    data-placement="bottom"
            ></bs-toolbar-button>
            <bs-toolbar-button
                    data-tag="a"
                    data-icon="fa-close"
                    data-permission="/admin/product/add"
                    data-class="btn btn-default"
                    data-rel="tooltip"
                    data-event="bs.delete.faq"
                    data-title="Elimina Faq"
                    data-placement="bottom"
            ></bs-toolbar-button>
        </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Argomenti">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-plus"
                data-permission="/admin/product/add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.add.new.argument"
                data-title="Crea un nuovo argomento"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <?php endif; ?>
</bs-toolbar>
</body>
</html>