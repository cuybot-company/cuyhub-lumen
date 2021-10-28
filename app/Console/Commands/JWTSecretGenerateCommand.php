<?php

namespace App\Console\Commands;

use Illuminate\Console\ConfirmableTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class JWTSecretGenerateCommand extends Command
{
    use ConfirmableTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'key:jwt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate JWT secret';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $key = $this->GenerateRandomSecret();

        if (!$this->setKeyInEnvironmentFile($key)) {
            return;
        }

        $this->laravel['config']['jwt.secret'] = $key;

        $this->info("JWT Secret [$key] set successfully.");
    }

    protected function GenerateRandomSecret()
    {
        return Str::random(64);
    }

    /**
     * Set the application key in the environment file.
     *
     * @param  string  $key
     * @return bool
     */
    protected function setKeyInEnvironmentFile($key)
    {
        $currentKey = $this->laravel['config']['jwt.secret'] ?: env('JWT_SECRET');

        if (strlen($currentKey) !== 0 && (!$this->confirmToProceed())) {
            return false;
        }

        $this->writeNewEnvironmentFileWith($key);

        return true;
    }

    /**
     * Write a new environment file with the given key.
     *
     * @param  string  $key
     * @return void
     */
    protected function writeNewEnvironmentFileWith($key)
    {
        file_put_contents($this->laravel->basePath('.env'), preg_replace(
            $this->keyReplacementPattern(),
            'JWT_SECRET=' . $key,
            file_get_contents($this->laravel->basePath('.env'))
        ));
    }

    /**
     * Get a regex pattern that will match env JWT_SECRET with any random key.
     *
     * @return string
     */
    protected function keyReplacementPattern()
    {
        $currentKey = $this->laravel['config']['jwt.secret'] ?: env('JWT_SECRET');
        $escaped = preg_quote('=' . $currentKey, '/');

        return "/^JWT_SECRET{$escaped}/m";
    }
}
