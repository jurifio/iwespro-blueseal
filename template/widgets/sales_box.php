<?php if($trend > 25) $color = 'green';
        else if ($trend < -25) $color= 'red';
        else $color = 'black';
?>
<div class="col-md-12 m-b-10">
    <div class="widget-9 panel no-border <?php echo $class ?> no-margin widget-loader-bar">
        <div class="container-xs-height full-height">
            <div class="row-xs-height">
                <div class="col-xs-height col-top">
                    <div class="panel-heading top-left top-right">
                        <div class="panel-title text-black">
                            <span class="font-montserrat fs-11 all-caps"> <?php echo $title?><i class="fa fa-chevron-right"></i></span>
                        </div>
                        <div class="panel-controls">
                            <ul>
                                <li><a href="#" class="portlet-refresh text-black" data-toggle="refresh"><i class="portlet-icon portlet-icon-refresh"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <?php
         
            ?>
            <div class="row-xs-height">
                <div class="col-xs-height col-top">
                    <div class="row">
                        <div class="col-xs-6">
                            <div class="p-l-20 p-t-15">
                                <?php
                                ?>
                                <?php //var_dump($value); ?>
                                <p>Fatturato</p>
                                <h4 class="no-margin p-b-5 text-<?php echo $color ?>">&euro; <?php echo round($value['current']['customer'],2); ?></h4>
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="p-l-20 p-t-15">
                                <p>Margine</p>
                                <h5 class="no-margin p-b-5">&euro; <?php echo round($value['current']['margin'],2); ?></h5>
                            </div>
                        </div>
                        <br style="clear:both" />
                        <a href="#" class="btn-circle-arrow text-black"><i class="<?php echo $trend > 0 ? "pg-arrow_up" : "pg-arrow_down" ?>"></i></a>
                        <span class="small hint-text">Ricavi: <?php echo round($trend,0); ?>% rispetto all'ultimo periodo</span>
                    </div>
                </div>
            </div>
            <div class="row-xs-height">
                <div class="col-xs-height col-bottom">
                    <div class="progress progress-small m-b-20">
                        <div class="progress-bar progress-bar-black" data-percentage="<?php echo round($periodProgress,0) ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>