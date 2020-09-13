<?php

use Dev4Press\Plugin\DebugPress\Panel\Query;

?>
<div class="gdpet-grid">
    <div class="gdpet-unit half">
		<?php Query::instance()->left(); ?>
    </div>
    <div class="gdpet-unit half">
		<?php Query::instance()->right(); ?>
    </div>
</div>