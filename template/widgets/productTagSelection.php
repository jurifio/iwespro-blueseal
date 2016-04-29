<ul class="tag-list">
	<?php foreach ($tags as $tag) {
		echo '<li id="'.$tag->id.'"><span>'.$tag->tagTranslation->getFirst()->name.'</span></li>';
	} ?>
</ul>