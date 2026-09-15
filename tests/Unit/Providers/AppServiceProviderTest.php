<?php

namespace Tests\Unit\Providers;

use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class AppServiceProviderTest extends TestCase
{
    public function test_keeps_default_public_path_when_unconfigured(): void
    {
        $default = $this->app->publicPath();

        Config::set('app.public_path', null);
        (new AppServiceProvider($this->app))->register();

        $this->assertSame($default, $this->app->publicPath());
    }

    public function test_binds_public_path_from_config(): void
    {
        $default = $this->app->publicPath();
        $custom = sys_get_temp_dir() . '/public-path-test-' . uniqid();
        mkdir($custom, 0777, true);

        try {
            Config::set('app.public_path', $custom);
            (new AppServiceProvider($this->app))->register();

            $this->assertSame($custom, $this->app->publicPath());
            $this->assertSame($custom . '/build/manifest.json', public_path('build/manifest.json'));
        } finally {
            $this->app->usePublicPath($default);
            Config::set('app.public_path', null);
            rmdir($custom);
        }
    }
}
