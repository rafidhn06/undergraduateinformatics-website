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

    public function test_extract_public_archive_skips_storage_snapshot(): void
    {
        $deployDir = sys_get_temp_dir() . '/deploy-test-' . uniqid();
        mkdir($deployDir, 0777, true);

        $publicZip = $deployDir . '/public.zip';
        $zip = new ZipArchive();
        $zip->open($publicZip, ZipArchive::CREATE);
        $zip->addFromString('index.php', 'index');
        $zip->addFromString('storage/stale.jpg', 'stale');
        $zip->close();

        $target = $deployDir . '/public-html';
        $probe = new \App\Http\Controllers\Internal\DeployController();
        $method = new \ReflectionMethod($probe, 'extractArchive');
        $result = $method->invoke($probe, $publicZip, $target, true);

        $this->assertTrue($result['ok']);
        $this->assertFileExists($target . '/index.php');
        $this->assertFileDoesNotExist($target . '/storage/stale.jpg');
    }

    public function test_replaces_stale_storage_directory_with_symlink(): void
    {
        $base = sys_get_temp_dir() . '/deploy-test-' . uniqid();
        $target = $base . '/app-storage';
        $link = $base . '/public-html/storage';
        mkdir($target, 0777, true);
        mkdir(dirname($link) . '/storage', 0777, true);
        file_put_contents($target . '/keep.txt', 'keep');
        file_put_contents(dirname($link) . '/storage/stale.txt', 'stale');

        $probe = new \App\Http\Controllers\Internal\DeployController();
        $method = new \ReflectionMethod($probe, 'ensureStorageLink');
        $result = $method->invoke($probe, $link, $target);

        $this->assertTrue($result['ok']);
        $this->assertTrue(is_link($link));
        $this->assertSame(realpath($target), realpath($link));
        $this->assertFileDoesNotExist($target . '/stale.txt');
        $this->assertFileExists($link . '/keep.txt');
    }
}
