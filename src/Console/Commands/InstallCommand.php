<?php

namespace TaaStarter\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    protected $signature = 'taa:install {--force : Overwrite existing files}';
    protected $description = 'Install TAA Starterkit agentic workspace, rules, and docs';

    public function handle()
    {
        $this->info('Installing TAA Starterkit Agentic Workspace...');

        $stubPath = __DIR__ . '/../../../stubs';

        // 1. Copy agents and .docs directories
        File::ensureDirectoryExists(base_path('agents'));
        File::ensureDirectoryExists(base_path('.docs'));
        File::copyDirectory($stubPath . '/agents', base_path('agents'));
        File::copyDirectory($stubPath . '/.docs', base_path('.docs'));

        // 2. Copy AI rule configurations
        File::copy($stubPath . '/.cursorrules', base_path('.cursorrules'));
        File::copy($stubPath . '/.cursorignore', base_path('.cursorignore'));

        $this->info('✔ agents/ and .docs/ installed successfully.');
        $this->info('✔ .cursorrules and .cursorignore placed at project root.');
        $this->newLine();$this->info('🚀 TAA Starterkit ready for vibe-coding!');
    }
}
