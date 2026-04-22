<?php

use Dev4Press\Plugin\DebugPress\Panel\Roles;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>
<div class="debugpress-grid">
    <div class="debugpress-unit half">
        <?php Roles::instance()->left(); ?>
    </div>
    <div class="debugpress-unit half">
        <?php Roles::instance()->right(); ?>
    </div>
</div>
