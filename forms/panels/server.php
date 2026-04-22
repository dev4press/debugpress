<?php

use Dev4Press\Plugin\DebugPress\Panel\Server;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>
<div class="debugpress-grid">
    <div class="debugpress-unit half">
        <?php Server::instance()->left(); ?>
    </div>
    <div class="debugpress-unit half">
        <?php Server::instance()->right(); ?>
    </div>
</div>