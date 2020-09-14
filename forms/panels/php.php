<?php

use Dev4Press\Plugin\DebugPress\Panel\PHP;

?>
<div class="debugpress-grid">
    <div class="debugpress-unit half">
		<?php PHP::instance()->left(); ?>
    </div>
    <div class="debugpress-unit half">
		<?php PHP::instance()->right(); ?>
    </div>
</div>
