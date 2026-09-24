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

        // 3. Inject / Update .gitignore baris per baris secara pasti
        $gitignorePath = base_path('.gitignore');
        if (File::exists($gitignorePath)) {
            $lines = file($gitignorePath, FILE_IGNORE_NEW_LINES);

            // Filter dan buang semua entri TAA lama jika sudah ada
            $filteredLines = array_filter($lines, function ($line) {
                $trimmed = trim($line);
                return !in_array($trimmed, [
                    '# TAA Agentic Workspace (Local Dev Only)',
                    '.docs/',
                    '/.docs/',
                    'agents/',
                    '/agents/',
                    '.agents/',
                    '/.agents/',
                    '.cursorrules',
                    '/.cursorrules',
                    '.cursorignore',
                    '/.cursorignore',
                    '.github/copilot-instructions.md',
                    '/.github/copilot-instructions.md',
                ]);
            });

            $cleanContent = rtrim(implode("\n", $filteredLines));
            $newRules = "\n\n# TAA Agentic Workspace (Local Dev Only)\n/.docs/\n/agents/\n/.agents/\n/.cursorrules\n/.cursorignore\n/.github/copilot-instructions.md\n";

            File::put($gitignorePath, $cleanContent . $newRules);
            $this->info('✔ .gitignore updated with strict root paths.');
        }

        // 4. Bersihkan Git tracking cache agar langsung redup di VS Code
        if (File::exists(base_path('.git'))) {
            $items = ['.docs', 'agents', '.agents', '.cursorrules', '.cursorignore', '.github/copilot-instructions.md'];
            foreach ($items as $item) {
                Process::fromShellCommandLine("git rm -r --cached {$item} 2>/dev/null", base_path())->run();
            }
        }

        $this->info('✔ agents/ and .docs/ installed successfully.');
        $this->info('✔ VS Code and Cursor rules deployed.');
        $this->newLine();
        $this->info('mhmdattrq Starterkit ready for make money!!!🚀');
    }
}
