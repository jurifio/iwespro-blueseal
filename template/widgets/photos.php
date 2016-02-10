<?php foreach ($photos as $photo): ?>
<div id="photo<?php echo $photo['order'] ?>" style="" class="draggable col-sm-2">
    <div class="row">
        <img class="img-responsive" height="250"
             src="<?php echo $app->image($product->getPhoto($photo['order'], $photo['size']), 'amazon') ?>">
    </div>
    <div class="row">
         <span data-toggle="modal" data-target="#bsModal" class="js-remove pull-right">
             <i class="fa fa-trash-o"></i>
         </span>
        <div class="pf-field"></div>
    </div>
</div>
<?php endforeach; ?>