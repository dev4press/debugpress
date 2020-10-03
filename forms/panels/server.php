<?php

use Dev4Press\Plugin\DebugPress\Panel\Server;

?>
<div class="debugpress-grid">
    <div class="debugpress-unit half">
		<?php Server::instance()->left(); ?>
    </div>
    <div class="debugpress-unit half">
		<?php Server::instance()->right(); ?>
    </div>
</div>