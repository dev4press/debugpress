<?php

use Dev4Press\Plugin\DebugPress\Panel\User;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>
<div class="debugpress-grid">
    <div class="debugpress-unit half">
        <?php User::instance()->left(); ?>
    </div>
    <div class="debugpress-unit half">
        <?php User::instance()->right(); ?>
    </div>
</div>