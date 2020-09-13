<?php

use Dev4Press\Plugin\DebugPress\Panel\Errors;

?>
<div class="gdpet-grid">
    <div class="gdpet-unit full">
		<?php Errors::instance()->single(); ?>
    </div>
</div>
