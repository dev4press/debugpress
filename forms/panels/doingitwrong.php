<?php

use Dev4Press\Plugin\DebugPress\Panel\DoingItWrong;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>
<div class="debugpress-grid">
    <div class="debugpress-unit full">
        <?php DoingItWrong::instance()->single(); ?>
    </div>
</div>
