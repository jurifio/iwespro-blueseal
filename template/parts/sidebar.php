<nav class="page-sidebar" data-pages="sidebar">
    <div id="appMenu" class="sidebar-overlay-slide from-top"></div>
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <img src="/assets/img/blueseal_logo_symbol.png" alt="logo" class="brand"
                 data-src="/assets/img/blueseal_logo_symbol.png" data-src-retina="/assets/img/blueseal_logo_symbol.png"
                 width="42">
            <img class="brand" width="120" src="/assets/img/logo_no_symbol_white.png"
                 data-src-retina="/assets/img/logo_no_symbol_white.png"
                 data-src="/assets/img/logo_no_symbol_white.png"/>
        </div>
    </div>
    <div class="sidebar-menu">
        <ul class="menu-items">
            <?php $c = 1;
            foreach ($sidebar as $section => $groups): ?>
                <li class="<?php echo (isset($groups['sidebarGroups'])) ? "active open" : ""; ?> <?php echo ($c == 1) ? 'm-t-30' : ''; ?>">
                    <a href="javascript:;">
                        <span class="title"><?php echo $groups['title']; ?></span>
                        <span class="arrow <?php echo (isset($groups['sidebarGroups'])) ? "open" : ""; ?>"></span>
                    </a>
                    <span class="icon-thumbnail"><i class="fa <?php echo $groups['icon']; ?>"></i></span>
                <ul class="sub-menu" style="display:none;">
                    <?php $i = 1;
                    foreach ($groups['sidebarGroups'] as $group=>  $content): ?>
                        <?php if (count($content['pages']) == 1): ?>
                            <?php foreach ($content['pages'] as $groupPage): ?>
                                <li class="<?php echo ($i == 1) ? 'm-t-30' : ''; ?>">
                                    <a href="<?php echo $groupPage['url']; ?>" class="detailed">
                                        <span class="title"><?php echo $content['title']; ?></span>
                                    </a>
                                    <span class="icon-thumbnail "><i
                                                class="fa <?php echo $content['icon']; ?>"></i></span>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="<?php echo (isset($content['pages'][$page->getSlug()])) ? "active open" : ""; ?> <?php echo ($i == 1) ? 'm-t-30' : ''; ?>">
                                <a href="javascript:;">
                                    <span class="title"><?php echo $content['title']; ?></span>
                                    <span class="arrow <?php echo (isset($content['pages'][$page->getSlug()])) ? "open" : ""; ?>"></span>
                                </a>
                                <span class="icon-thumbnail"><i class="fa <?php echo $content['icon']; ?>"></i></span>
                                <ul class="sub-menu" style="display:none;">
                                    <?php foreach ($content['pages'] as $slug => $groupPage): ?>
                                        <li class="<?php echo ($page->getSlug() == $slug) ? "active" : "" ?>">
                                            <a href="<?php echo $groupPage['url']; ?>"><?php echo $groupPage['title']; ?></a>
                                            <span class="icon-thumbnail"><i
                                                        class="fa <?php echo $groupPage['icon']; ?>"></i></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        <?php endif; ?>
                        <?php $i++; endforeach; ?>
                </ul>
                <?php $c++; endforeach; ?>
        </ul>
        <div class="clearfix"></div>
    </div>
</nav>