<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\ResearchGrant;

class ImportJsonData extends Command
{
    protected $signature = 'add:json {folder}';
    protected $description = 'Import all JSON files from a folder into research_grants table';

    public function handle()
    {
        $folder = $this->argument('folder');

        if (!is_dir($folder)) {
            $this->error("Folder not found: {$folder}");
            return 1;
        }

        $files = glob($folder . '/*.json');

        if (empty($files)) {
            $this->warn("No JSON files found in: {$folder}");
            return 0;
        }

        $this->info("Found " . count($files) . " JSON files in {$folder}");

        foreach ($files as $file) {
            $this->info("Processing file: {$file}");

            DB::transaction(function () use ($file) {
                $content = file_get_contents($file);
                $data = json_decode($content, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    $this->error("Invalid JSON in file: {$file}");
                    return;
                }

                // Handle both single object and array of objects
                $records = isset($data[0]) ? $data : [$data];

                $batchSize = 1000;
                $batch = [];
                $count = 0;

                foreach ($records as $record) {
                    $batch[] = $this->mapJsonData($record);
                    $count++;

                    if (count($batch) >= $batchSize) {
                        ResearchGrant::insert($batch);
                        $batch = [];
                        $this->info("Imported {$count} records so far from {$file}...");
                    }
                }

                // Insert remaining records
                if (!empty($batch)) {
                    ResearchGrant::insert($batch);
                }

                $this->info("Finished importing file: {$file}");
            });
        }

        $this->info("✅ Import completed! All files processed.");
        return 0;
    }

    private function mapJsonData($data)
    {
        $piName = '';
        if (isset($data['pi']) && is_array($data['pi']) && !empty($data['pi'])) {
            $pi = $data['pi'][0];
            $piName = trim(($pi['pi_first_name'] ?? '') . ' ' . ($pi['pi_mid_init'] ?? '') . ' ' . ($pi['pi_last_name'] ?? ''));
        }

        return [
            'source' => 'json',
            'awd_id' => $data['awd_id'] ?? null,
            'award_id' => $data['awd_id'] ?? null,
            'title' => $data['awd_titl_txt'] ?? null,
            'pi_name' => $piName,
            'institution_name' => $data['inst']['inst_name'] ?? null,
            'city' => $data['inst']['inst_city_name'] ?? null,
            'state' => $data['inst']['inst_state_name'] ?? null,
            'zipcode' => $data['inst']['inst_zip_code'] ?? null,
            'country' => $data['inst']['inst_country_name'] ?? null,
            'award_amount' => is_numeric($data['tot_intn_awd_amt'] ?? null) ? $data['tot_intn_awd_amt'] : null,
            'start_date' => $this->parseDate($data['awd_eff_date'] ?? null),
            'end_date' => $this->parseDate($data['awd_exp_date'] ?? null),
            'funding_agency' => $data['agcy_id'] ?? 'NSF',
            'agcy_id' => $data['agcy_id'] ?? null,
            'tran_type' => $data['tran_type'] ?? null,
            'cfda_num' => $data['cfda_num'] ?? null,
            'po_email' => $data['po_email'] ?? null,
            'po_phone' => $data['po_phone'] ?? null,
            'abstract' => $data['awd_abstract_narration'] ?? null,
            'dir_abbr' => $data['dir_abbr'] ?? null,
            'div_abbr' => $data['div_abbr'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function parseDate($dateString)
    {
        if (empty($dateString)) {
            return null;
        }

        try {
            return \Carbon\Carbon::parse($dateString)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
