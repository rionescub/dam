<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * The deployed storage directory belongs to the web server, so the test run
     * keeps its scratch files (faked disks, compiled views, logs) elsewhere.
     */
    public function createApplication()
    {
        $app = require Application::inferBasePath().'/bootstrap/app.php';

        $storagePath = sys_get_temp_dir().'/dam-testing-storage';

        foreach (['app/public', 'framework/cache', 'framework/sessions', 'framework/testing/disks', 'framework/views', 'logs'] as $directory) {
            if (! is_dir($path = "{$storagePath}/{$directory}")) {
                mkdir($path, 0755, true);
            }
        }

        $app->useStoragePath($storagePath);

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
