<?php

use Dev4Press\Plugin\DebugPress\Panel\System;

?>
<div class="gdpet-grid">
    <div class="gdpet-unit half">
		<?php System::instance()->left(); ?>
    </div>
    <div class="gdpet-unit half">
		<?php System::instance()->right(); ?>
    </div>
</div>