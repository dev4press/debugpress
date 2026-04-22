<?php

use Dev4Press\Plugin\DebugPress\Panel\Enqueue;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>
<div class="debugpress-grid">
    <div class="debugpress-unit half">
        <?php Enqueue::instance()->left(); ?>
    </div>
    <div class="debugpress-unit half">
        <?php Enqueue::instance()->right(); ?>
    </div>
</div>
