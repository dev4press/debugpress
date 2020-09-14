<?php

use Dev4Press\Plugin\DebugPress\Panel\Enqueue;

?>
<div class="debugpress-grid">
    <div class="debugpress-unit half">
		<?php Enqueue::instance()->left(); ?>
    </div>
    <div class="debugpress-unit half">
		<?php Enqueue::instance()->right(); ?>
    </div>
</div>
