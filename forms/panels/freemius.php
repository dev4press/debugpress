<?php

use Dev4Press\Plugin\DebugPress\Panel\Freemius;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>
<div class="debugpress-grid">
    <div class="debugpress-unit half">
        <?php Freemius::instance()->left(); ?>
    </div>
    <div class="debugpress-unit half">
        <?php Freemius::instance()->right(); ?>
    </div>
</div>
