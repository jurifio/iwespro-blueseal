<div class="row">
    <div class="col-md-12">
        <ul class="nav nav-tabs nav-tabs-simple bg-white" role="tablist">
            <?php foreach($langs as $lang): ?>
                <li role="presentation" class="<?php echo ($lang->lang == 'it' ? "active" : "") ?>"><a data-toggle="tab" href="#desc<?php echo $lang->lang?>" aria-controls="desc<?php echo $lang->lang?>" role="tab"><?php echo $lang->name?></a></li>
            <?php endforeach; $langs->rewind(); ?>
        </ul>
        <div class="tab-content bg-white">
            <?php foreach($langs as $lang): ?>
                <div role="tabpanel" class="tab-pane <?php echo ($lang->lang == 'it' ? "active" : "")?>" id="desc<?php echo $lang->lang?>">
                    <form id="" action="" method="post">
                        <div class="row">
                            <div class="col-md-8">
                                <?php foreach ($fields['main'] as $fieldGroup => $field) {
                                    echo $field[$lang->lang];
                                } ?>
                            </div>
                            <div class="col-md-4">
                                <?php foreach ($fields['sidebar'] as $fieldGroup => $field) {
                                    echo $field[$lang->lang];
                                } ?>
                            </div>

                        </div>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>