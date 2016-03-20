<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 01.07.2015
 * Time: 18:12
 */
use famoser\phpFrame\Helpers\PartHelper;
use famoser\phpFrame\Services\RuntimeService;
use famoser\phpFrame\Views\ViewBase;


if ($this instanceof ViewBase) { ?>
    <?php if (count($this->getMainMenu()) > 0) { ?>
        <div class="primary-menu clearfix">
            <div class="primary-menu-items">
                <ul class="tiles">
                    <?php
                    foreach ($this->getMainMenu() as $menuEntry) { ?>
                        <li <?php echo PartHelper::getInstance()->getClassForMainMenuItem(RuntimeService::getInstance()->getRouteParams(), RuntimeService::getInstance()->getTotalParams()); ?>>
                            <a href="<?php echo $menuEntry->getHref() ?>/">
                            <span class="<?php echo $menuEntry->getIcon() ?>"
                                  aria-hidden="true"></span><?php echo $menuEntry->getName() ?>
                            </a>
                        </li>
                    <?php }
                    ?>
                </ul>
            </div>

            <div class="secondary-menu-items">
                <ul class="tiles">
                    <li>
                        <a class="tile float-right" href="logout">
                            <span class="flaticon-cancel22" aria-hidden="true"></span>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    <?php } ?>

    <?php if (count($this->getSubMenu()) > 0) {
        ?>
        <div class="submenu-items">
            <ul class="oneline-nav">
                <?php
                foreach ($this->getSubMenu() as $menuEntry) {

                    echo '<li ' . PartHelper::getInstance()->getClassesForMenuSubItem(RuntimeService::getInstance()->getControllerParams(), $menuEntry->getHref()) . '>
                            <a href="' . RuntimeService::getInstance()->getRouteUrl() . "/" . $menuEntry->getHref() . '">' . $menuEntry->getName() . '</a>
                      </li>';
                }
                ?>
            </ul>
        </div>
    <?php } ?>
<?php } ?>