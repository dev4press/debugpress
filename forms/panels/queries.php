<?php

use Dev4Press\Plugin\DebugPress\Panel\Queries;

?>
<div class="gdpet-grid">
    <div class="gdpet-unit one-quarter">
		<?php Queries::instance()->left(); ?>
    </div>
    <div class="gdpet-unit three-quarters">
		<?php Queries::instance()->right(); ?>
    </div>
</div>