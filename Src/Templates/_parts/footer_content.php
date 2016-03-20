<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 01.07.2015
 * Time: 18:02
 */


use famoser\phpFrame\Views\ViewBase;

if ($this instanceof ViewBase) { ?>
    <!-- end content -->
    </div>
    </div> <!-- Ende tab-content -->
    <div class="endspacer"></div>
    </div><!-- Ende container -->
    </div><!-- Ende tab-content-slider -->
    </div><!-- Ende mobile-container -->
    <footer>
        <div class="col-md-12 footer-info">
            <p>Copyright © <?= date("Y") ?> <a href="<?= $this->getPageAuthorUrl() ?>" target="_blank"><?= $this->getPageAuthor() ?></a></p>
        </div>
    </footer>
    <script type="text/javascript" src="/js/scripts.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            register()
        });
    </script>
    </body>
    </html>

<?php } ?>