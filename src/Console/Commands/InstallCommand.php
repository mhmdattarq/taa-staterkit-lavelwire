<?php

namespace TaaStarter\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    protected $signature = 'taa:install {--force : Overwrite existing files}';
    protected $description = 'Install TAA Starterkit agentic workspace, rules, and docs for VS Code & Cursor';

    public function handle()
    {
        $this->info('Installing TAA Starterkit Agentic Workspace...');

        $stubPath = __DIR__ . '/../../../stubs';

        // 1. Copy agents and .docs directories
        File::ensureDirectoryExists(base_path('agents'));
        File::ensureDirectoryExists(base_path('.docs'));
        File::copyDirectory($stubPath . '/agents', base_path('agents'));
        File::copyDirectory($stubPath . '/.docs', base_path('.docs'));

        // 2. Copy AI workspace configuration files (Cursor & VS Code)
        File::copy($stubPath . '/.cursorrules', base_path('.cursorrules'));
        File::copy($stubPath . '/.cursorignore', base_path('.cursorignore'));

        if (File::exists($stubPath . '/.github')) {
            File::ensureDirectoryExists(base_path('.github'));
            File::copyDirectory($stubPath . '/.github', base_path('.github'));
        }

        // 3. Automatically add workspace files to .gitignore to protect production
        $gitignorePath = base_path('.gitignore');
        if (File::exists($gitignorePath)) {
            $currentContent = File::get($gitignorePath);
            if (!str_contains($currentContent, '.docs/')) {
                $ignoreRules = "\n# TAA Agentic Workspace (Local Dev Only)\n.docs/\nagents/\n.cursorrules\n.cursorignore\n.github/copilot-instructions.md\n";
                File::append($gitignorePath, $ignoreRules);
                $this->info('✔ Workspace rules safely appended to .gitignore.');
            }
        }

        $this->info('✔ agents/ and .docs/ installed successfully.');
        $this->info('✔ VS Code and Cursor rules deployed.');
        $this->newLine();$this->info('🚀 TAA Starterkit ready for vibe-coding in VS Code!');
    }
}
