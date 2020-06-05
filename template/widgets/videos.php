<?php foreach ($photos as $photo): ?>
<div id="photo<?php echo $photo['order'] ?>" style="" class="draggable col-sm-2">
    <div class="row">
        <video width="150" height="240"  controls autoplay>
            <source src="https://cdn.iwes.it/test/VID_20200507_171039.mp4" type="video/mp4">
            <source src="https://www.iwes.pro/test/VID_20200507_171039.mp4" type="video/ogg">

    </div>
    </div>
    <div class="row">
         <span data-toggle="modal" data-target="#bsModal" class="js-remove pull-right">
             <i class="fa fa-trash-o"></i>
         </span>
        <div class="pf-field"></div>
    </div>
</div>
<?php endforeach; ?>