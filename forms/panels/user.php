<?php

use Dev4Press\Plugin\DebugPress\Panel\User;

?>
<div class="gdpet-grid">
    <div class="gdpet-unit half">
		<?php User::instance()->left(); ?>
    </div>
    <div class="gdpet-unit half">
		<?php User::instance()->right(); ?>
    </div>
</div>