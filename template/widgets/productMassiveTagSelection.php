<div class="panel">
    <div class="filters-set">
        <div class="select-season">
            <label for="selectSeason">Seleziona una stagione</label>
            <select id="selectSeason" class="filterTags">
                <option selected value>Seleziona un'opzione</option>
                <?php foreach ($seasons as $season) {
                    echo '<option value="' . $season->id . '">' . $season->name . '</option>';
                } ;?>
            </select>
        </div>

        <div class="select-brand">
            <label for="selectBrand">Seleziona un brand</label>
            <select id="selectBrand" class="filterTags">
                <option selected value>Seleziona un'opzione</option>
                <?php foreach ($brands as $brand) {
                    echo '<option value="' . $brand->id . '">' . $brand->name . '</option>';
                } ;?>
            </select>
        </div>

        <div class="select-color">
            <label for="selectColor">Seleziona un colore</label>
            <select id="selectColor" class="filterTags">
                <option selected value>Seleziona un'opzione</option>
                <?php foreach ($colors as $color) {
                    echo '<option value="' . $color->id . '">' . $color->name . '</option>';
                } ;?>
            </select>
        </div>

    </div>

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
                    <ul class="tag-list" id="tag-list-remove">
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>