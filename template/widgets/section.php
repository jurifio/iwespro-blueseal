<?php
$status = [];
foreach ($translationStatus as $lang => $langStatus) {
    if ($langStatus) {
        $status[] = '<span style="lang-status-ok">'.$lang.'</span>';
    } else {
        $status[] = '<span style="lang-status-ko">'.$lang.'</span>';
    }
}
?>
<li class="item padding-15" data-section="<?php echo $section; ?>" data-type="<?php echo $widgetType; ?>" data-id="<?php echo $widgetId; ?>">
    <div class="thumbnail-wrapper d32">
        <i class='fa <?php echo $icon; ?>' style="font-size:32px"></i>
    </div>
    <div class="inline m-l-15">
        <p class="recipients no-margin hint-text small"><?php echo $description; ?></p>
        <p class="subject no-margin"><?php echo $content; ?></p>
    </div>
    <div class="datetime"><?php echo $lastUpdate; ?></div>
    <div class="clearfix"></div>
</li>