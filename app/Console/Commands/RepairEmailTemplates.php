<?php

namespace App\Console\Commands;

use App\Models\EmailTemplate;
use Database\Seeders\EmailTemplateSeeder;
use Illuminate\Console\Command;

class RepairEmailTemplates extends Command
{
    protected $signature = 'emails:repair-templates
        {--all : Re-apply every system template, overwriting any customised subject/body}';

    protected $description = 'Restore missing or blank system email templates from their canonical definitions.';

    public function handle(): int
    {
        $all = (bool) $this->option('all');
        $created = 0;
        $repaired = 0;
        $skipped = 0;

        foreach (EmailTemplateSeeder::definitions() as $def) {
            $existing = EmailTemplate::where('key', $def['key'])->first();

            // Missing entirely → create it.
            if (! $existing) {
                EmailTemplate::create($def + ['is_active' => true]);
                $this->line("  + created {$def['key']}");
                $created++;
                continue;
            }

            // --all → blunt restore of the canonical name/subject/body/variables.
            if ($all) {
                $existing->update($def);
                $this->line("  ~ reset {$def['key']}");
                $repaired++;
                continue;
            }

            // Default (safe): only patch the fields that are actually blank, so any
            // template an admin has customised is left untouched.
            $patch = [];
            if (blank($existing->body_html)) {
                $patch['body_html'] = $def['body_html'];
            }
            if (blank($existing->subject)) {
                $patch['subject'] = $def['subject'];
            }

            if ($patch) {
                $existing->update($patch);
                $this->line('  ! repaired ' . $def['key'] . ' (' . implode(', ', array_keys($patch)) . ')');
                $repaired++;
            } else {
                $skipped++;
            }
        }

        $this->info("Done. Created: {$created}, repaired: {$repaired}, left untouched: {$skipped}.");

        return self::SUCCESS;
    }
}
