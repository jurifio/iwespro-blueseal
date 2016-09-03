<form id="form-movement" enctype="multipart/form-data" role="form" action="" method="post" autocomplete="off" class="form">
    <div class="container mag-container">
        <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-8 offset-2">
                    <div class="mag-movementDate">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-group-default">
                                    <label for="mag-movementDate">Data
                                        <input type="date" name="mag-movementDate" class="form-control mag-movementDateInput" id="mag-movementDate" value="<?php echo date('Y-m-d');?>" required />
                                    </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group form-group-default">
                                    <label for="mag-movementCause">Causale
                                        <select class="mag-movementCause" placeholder="Seleziona una causale" name="mag-movementCause" required >
                                            <option value=""></option>
                                            <?php
                                                foreach($causes as $v){
                                                    echo '<option value="' . $v->id .'">' . $v->name . '</option>';
                                                }
                                            ?>
                                        </select>
                                    </label>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>

        </div>
        </div>
        <div class="panel panel-default mag-products-container">
            <div class="panel-body">
                <div class="row mag-alerts" style="visibility: hidden;">
                    <div class="col-md-12 alert alert-danger">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group-inline mag-searchBlock">
                            <div class="form-group form-group-default">
                                <label for="search-item">Cerca il prodotto</label>
                                <input name="search-item" class="form-group-inline form-control search-item" />
                                <button type="submit" class="btn btn-active search-btn">Cerca</button>
                                <br class="clearfix" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-body mag-product-list">
                <div class="row mag-product">
                    <div class="col-md-12">
                        <div class="form-group form-group-default">
                            <h5 class="product-title"></h5>
                            <button class="product-close btn btn-complete">x</button>
                            <br class="clear" />
                            <div class="row mag-sizes-block text-center">
                                <table class="nested-table mag-sizesTable">
                                    <thead>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <!--<div class="row mag-add-line-control">
                                <button class="btn btn-default pull-right btn-add-movement">Aggiungi movimento</button>
                            </div>

                            <div class="mag-movements" data-sizes="">
                                <div class="row mag-movementLine">
                                    <div class="form-group form-group-default">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="col-sm-5 ml-size-block">
                                                    <label>Taglia
                                                    <select class="form-control ml-size mandatory" placeholder="Seleziona una taglia"></select>
                                                    </label>
                                                </div>
                                                <div class="col-sm-5 ml-qtMove-block">
                                                    <label>Quantità
                                                    <input type="number" class="form-control ml-qtMove mandatory" val="0">
                                                    </label>
                                                </div>
                                                <div class="col-sm-2 lineClose-block">
                                                    <button class="btn btn-complete lineClose">X</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>-->
                            <br style="clear: both;" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-body">
            <div class="row mag-submit">
                <div class="col-md-12 text-right">
                    <button class="btn btn-active submit">Inserisci</button>
                </div>
            </div>
            </div>
        </div>
    </div>
</form>