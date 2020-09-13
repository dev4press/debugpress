<?php

use Dev4Press\Plugin\DebugPress\Panel\bbPress;

?>
<div class="gdpet-grid">
    <div class="gdpet-unit half">
		<?php bbPress::instance()->left(); ?>
    </div>
    <div class="gdpet-unit half">
		<?php bbPress::instance()->right(); ?>
    </div>
</div>
