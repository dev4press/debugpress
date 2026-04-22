<?php

use Dev4Press\Plugin\DebugPress\Panel\Hooks;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>
<div class="debugpress-grid">
    <div class="debugpress-unit one-quarter">
        <?php Hooks::instance()->left(); ?>
    </div>
    <div class="debugpress-unit three-quarters">
        <?php Hooks::instance()->right(); ?>
    </div>
</div>
