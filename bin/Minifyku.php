<?php

declare (strict_types=1);

use PHPDevsr\Minifyku\Commands\MinifyAll;
// @ intentionally: continue anyway
@\ini_set('memory_limit', '-1');
// Performance boost
\error_reporting(\E_ALL);
\ini_set('display_errors', 'stderr');
\gc_disable();

// Running Command
$minify = new MinifyAll();
$minify->run([]);
