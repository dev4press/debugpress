<?php

use Dev4Press\Plugin\DebugPress\Panel\Tools;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>
<div class="debugpress-grid">
    <div class="debugpress-unit one-third">
        <?php Tools::instance()->left(); ?>
    </div>
    <div class="debugpress-unit one-third">
        <?php Tools::instance()->middle(); ?>
    </div>
    <div class="debugpress-unit one-third">
        <?php Tools::instance()->right(); ?>
    </div>
</div>
