<?php

namespace TaaStarter\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class InstallCommand extends Command
{
    protected $signature = 'taa:install {--force : Overwrite existing files}';
    protected $description = 'Install TAA Starterkit agentic workspace, rules, and docs for VS Code & Cursor';

    public function handle()
    {
        $this->info('Installing TAA Starterkit Agentic Workspace...');

        $stubPath = __DIR__ . '/../../../stubs';

        // 1. Copy agents dan .docs directories
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

        // 3. Inject ke .gitignore otomatis dengan format root path lengkap
        $gitignorePath = base_path('.gitignore');
        if (File::exists($gitignorePath)) {
            $currentContent = File::get($gitignorePath);

            // Bersihkan entri lama jika formatnya belum pakai leading slash
            if (str_contains($currentContent, '# TAA Agentic Workspace')) {
                $cleanedContent = preg_replace('/# TAA Agentic Workspace.*?(?=(\n\n|\Z))/s', '', $currentContent);
                File::put($gitignorePath, trim($cleanedContent));
                $currentContent = File::get($gitignorePath);
            }

            if (!str_contains($currentContent, '/.docs/')) {
                $ignoreRules = "\n\n# TAA Agentic Workspace (Local Dev Only)\n/.docs/\n/agents/\n/.agents/\n/.cursorrules\n/.cursorignore\n/.github/copilot-instructions.md\n";
                File::append($gitignorePath, $ignoreRules);
                $this->info('✔ Workspace rules automatically secured in .gitignore.');
            }
        }

        // 4. Otomatis lepas cache git agar file langsung redup (ignored) tanpa command manual
        if (File::exists(base_path('.git'))) {
            $process = new Process([
                'git',
                'rm',
                '-r',
                '--cached',
                '.docs',
                'agents',
                '.agents',
                '.cursorrules',
                '.cursorignore',
                '.github/copilot-instructions.md'
            ], base_path());

            // Jalankan tanpa melempar error jika filenya memang belum pernah ter-track
            $process->run();
        }

        $this->info('✔ agents/ and .docs/ installed successfully.');
        $this->info('✔ VS Code and Cursor rules deployed.');
        $this->newLine();
        $this->info('mhmdattrq Starterkit ready for make money!!!🚀');
    }
}
