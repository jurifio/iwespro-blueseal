<div style="display:none">
    <img src="/assets/img/blueseal_logo_symbol_black.png" />
</div>
<div class="header ">
    <div class="pull-left full-height visible-sm visible-xs">
        <div class="sm-action-bar">
            <a href="#" class="btn-link toggle-sidebar" data-toggle="sidebar">
                <span class="icon-set menu-hambuger"></span>
            </a>
        </div>
    </div>
    <!--<div class="pull-right full-height visible-sm visible-xs">
        <div class="sm-action-bar">
            <a href="#" class="btn-link" data-toggle="quickview" data-toggle-element="#quickview">
                <span class="icon-set menu-hambuger-plus"></span>
            </a>
        </div>
    </div>-->
    <div class="pull-left sm-table">
        <div class="header-inner">
            <div class="brand inline">
                <?php
                if($app->user()->shop->count() == 1 && file_exists($app->application()->rootPath()."/client/public/media".$app->user()->shop->getFirst()->getShopLogo())) {
                    $logo = $app->user()->shop->getFirst()->getShopLogo();
                } else {
                    $logo = "img/logo_no_symbol.png";
                }
                ?>
                <img class="shop-logo" src="/assets/<?php echo $logo ?>" alt="logo" data-src="/assets/<?php echo $logo ?>" data-src-retina="/assets/<?php echo $logo ?>" />
            </div>
        </div>
    </div>

    <div class="pull-left sm-table">
        <div class="header-inner">
            <?php if($app->user()->hasPermission('allShops') ): ?>
            <div id="sessionMonitor">
                <table class="mini-traffic-display">
                   <thead>
                        <tr>
                            <th>Minuti</th>
                            <th>Sessioni</th>
                            <th>Di cui loggate</th>
                            <th>Carico Server</th>
                            <th>Job Attivi</th>
                            <th>Coda Eventi</th>
                        </tr>
                   </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td><span id="s1"></span></td>
                            <td><span id="u1"></span></td>
                            <td><span id="l1"></span></td>
                            <td><span id="ja"></span></td>
                            <td><span id="eq"></span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!--<div class=" pull-right">
        <div class="header-inner">
            <a href="#" class="btn-link icon-set menu-hambuger-plus m-l-20 sm-no-margin hidden-sm hidden-xs" data-toggle="quickview" data-toggle-element="#quickview"></a>
        </div>
    </div>-->
    <div class="pull-right">
        <div class="visible-lg visible-md m-t-10 user-account">
            <div class="pull-left p-r-10 p-t-10 fs-16 font-heading">
                <span class="semi-bold"><?php echo $app->user()->userDetails->name?></span>
                <span class="text-master"><?php echo $app->user()->userDetails->surname?></span>
            </div>
            <div class="thumbnail-wrapper d32 circular inline m-t-5">
                <img src="/assets/img/profiles/default.jpg" alt="" data-src="/assets/img/profiles/default.jpg" data-src-retina="/assets/img/profiles/default.jpg" width="55" height="55">
            </div>
            <div class="pull-left p-r-10 p-t-10 fs-14 font-heading">
                &nbsp;<a href="<?php echo $app->urlFor('blueseal/logout') ?>"><span class="semi-bold">Esci</span></a>
            </div>
        </div>
    </div>
</div>