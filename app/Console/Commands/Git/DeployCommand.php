<?php

namespace App\Console\Commands\Git;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class DeployCommand extends Command
{
    protected $signature = 'app:deploy';

    protected $description = 'Deploy application';

    public function handle(): int
    {
        $this->call('optimize:clear');
        $this->call('optimize');

        $status = Process::run('git status --short')->output();

        $added = [];
        $modified = [];
        $deleted = [];

        foreach (explode("\n", $status) as $line) {

            $line = rtrim($line);

            if (empty($line)) {
                continue;
            }

            $type = trim(substr($line, 0, 2));
            $file = trim(substr($line, 3));

            if (str_contains($type, 'A')) {
                $added[] = $file;
            }

            if (str_contains($type, 'M')) {
                $modified[] = $file;
            }

            if (str_contains($type, 'D')) {
                $deleted[] = $file;
            }
        }

        $message = "Deploy: " . Carbon::now()->format('Y-m-d H:i:s');

        if ($added) {
            $message .= "\n\n+ " . count($added) . " files added";

            foreach ($added as $file) {
                $message .= "\n" . $file;
            }
        }

        if ($modified) {
            $message .= "\n\n~ " . count($modified) . " files modified";

            foreach ($modified as $file) {
                $message .= "\n" . $file;
            }
        }

        if ($deleted) {
            $message .= "\n\n- " . count($deleted) . " files deleted";

            foreach ($deleted as $file) {
                $message .= "\n" . $file;
            }
        }

        $this->info($message);

        Process::run('git add .');

        $commit = Process::run(
            'git commit -m ' . escapeshellarg($message)
        );

        if (str_contains($commit->output(), 'nothing to commit')) {
            $this->warn('Nothing to commit');
            return self::SUCCESS;
        }

        Process::run('git push');

        $this->info('Deploy completed');

        return self::SUCCESS;
    }
}