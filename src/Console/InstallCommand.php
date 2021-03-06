<?php

namespace TheBugSoftware\Teamwork\Console;

use Illuminate\Console\Command;
use Illuminate\Console\DetectsApplicationNamespace;
use Illuminate\Support\Str;

class InstallCommand extends Command
{
    use DetectsApplicationNamespace;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'teamwork:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install all of the Teamwork resources';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->comment('Publishing Teamwork Service Provider...');
        $this->callSilent('vendor:publish', ['--tag' => 'provider']);

        $this->comment('Publishing Teamwork Configuration...');
        $this->callSilent('vendor:publish', ['--tag' => 'config']);

        $this->registerTeamworkServiceProvider();

        $this->info('Teamwork scaffolding installed successfully.');
    }

    /**
     * Register the Teamwork service provider in the application configuration file.
     *
     * @return void
     */
    protected function registerTeamworkServiceProvider()
    {
        $namespace = Str::replaceLast('\\', '', $this->getAppNamespace());

        $appConfig = file_get_contents(config_path('app.php'));

        if (Str::contains($appConfig, $namespace . '\\TheBugSoftware\Teamwork\TeamworkServiceProvider::class')) {
            return;
        }

        file_put_contents(config_path('app.php'), str_replace(
            '/*
         * Package Service Providers...
         */' . PHP_EOL,
            '/*
         * Package Service Providers...
         */' . PHP_EOL . "        TheBugSoftware\Teamwork\TeamworkServiceProvider::class," . PHP_EOL,
            $appConfig
        ));
    }
}
