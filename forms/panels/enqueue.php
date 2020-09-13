<?php

use Dev4Press\Plugin\DebugPress\Panel\Enqueue;

?>
<div class="gdpet-grid">
    <div class="gdpet-unit half">
		<?php Enqueue::instance()->left(); ?>
    </div>
    <div class="gdpet-unit half">
		<?php Enqueue::instance()->right(); ?>
    </div>
</div>
