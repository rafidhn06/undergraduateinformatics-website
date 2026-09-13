<?php

namespace Tests\Feature\Internal;

use Illuminate\Support\Facades\Config;
use Tests\TestCase;
use ZipArchive;

class DeployFinalizeTest extends TestCase
{
    public function test_reports_degraded_storage_link_without_failing_deploy(): void
    {
        Config::set('deploy.token', 'secret-token');
        $deployDir = sys_get_temp_dir() . '/deploy-test-' . uniqid();
        mkdir($deployDir, 0777, true);
        Config::set('deploy.directory', $deployDir);

        $probe = new \App\Http\Controllers\Internal\DeployController();
        $method = new \ReflectionMethod($probe, 'ensureStorageLink');
        $result = $method->invoke($probe);

        $this->assertArrayHasKey('ok', $result);
        $this->assertArrayHasKey('detail', $result);
        $this->assertTrue($result['ok']);
    }

    public function test_rejects_archive_with_traversal_entry(): void
    {
        Config::set('deploy.token', 'secret-token');
        $deployDir = sys_get_temp_dir() . '/deploy-test-' . uniqid();
        mkdir($deployDir, 0777, true);
        Config::set('deploy.directory', $deployDir);

        $evilZip = $deployDir . '/evil.zip';
        $zip = new ZipArchive();
        $zip->open($evilZip, ZipArchive::CREATE);
        $zip->addFromString('../evil.txt', 'evil');
        $zip->close();

        $probe = new \App\Http\Controllers\Internal\DeployController();
        $method = new \ReflectionMethod($probe, 'extractArchive');
        $result = $method->invoke($probe, $evilZip, sys_get_temp_dir());

        $this->assertFalse($result['ok']);
    }
}
