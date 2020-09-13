<?php

use Dev4Press\Plugin\DebugPress\Panel\Content;

?>
<div class="gdpet-grid">
    <div class="gdpet-unit half">
		<?php Content::instance()->left(); ?>
    </div>
    <div class="gdpet-unit half">
		<?php Content::instance()->right(); ?>
    </div>
</div>
