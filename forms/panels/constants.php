<?php

use Dev4Press\Plugin\DebugPress\Panel\Constants;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>
<div class="debugpress-grid">
    <div class="debugpress-unit half">
        <?php Constants::instance()->left(); ?>
    </div>
    <div class="debugpress-unit half">
        <?php Constants::instance()->right(); ?>
    </div>
</div>
