<div class="panel">
    <ul class="nav nav-tabs nav-tabs-simple">
        <li class="active">
            <a data-toggle="tab" href="#add">Assegna Tag</a>
        </li>
        <li>
            <a data-toggle="tab" href="#delete">Rimuuovi Tag</a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="add">
            <div class="row">
                <div class="col-md-12">
                    <h3>
                        <span class="semi-bold">Aggiungi Tag</span>
                    </h3>
                </div>
                <div class="col-md-12">
                    <ul class="tag-list">
                        <?php foreach ($allTags as $tag) {
                            echo '<li id="' . $tag->id . '"><span>' . $tag->getLocalizedName() . '</span></li>';
                        } ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="delete">
            <div class="row">
                <div class="col-md-12">
                    <h3>
                        <span class="semi-bold">Rimuovi Tag</span>
                    </h3>
                </div>
                <div class="col-md-12">
                    <ul class="tag-list">
                        <?php foreach ($deleteTags as $tag) {
                            echo '<li id="' . $tag->id . '"><span>' . $tag->getLocalizedName() . '</span></li>';
                        } ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>