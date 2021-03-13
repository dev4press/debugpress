<?php

use Dev4Press\Plugin\DebugPress\Panel\Plugins;

?>
<div class="debugpress-grid">
    <div class="debugpress-unit half">
		<?php Plugins::instance()->left(); ?>
    </div>
    <div class="debugpress-unit half">
		<?php Plugins::instance()->right(); ?>
    </div>
</div>