<?php

use Dev4Press\Plugin\DebugPress\Panel\Query;

?>
<div class="debugpress-grid">
    <div class="debugpress-unit half">
		<?php Query::instance()->left(); ?>
    </div>
    <div class="debugpress-unit half">
		<?php Query::instance()->right(); ?>
    </div>
</div>