<?php

use Dev4Press\Plugin\DebugPress\Panel\Request;

?>
<div class="gdpet-grid">
    <div class="gdpet-unit half">
		<?php Request::instance()->left(); ?>
    </div>
    <div class="gdpet-unit half">
		<?php Request::instance()->right(); ?>
    </div>
</div>