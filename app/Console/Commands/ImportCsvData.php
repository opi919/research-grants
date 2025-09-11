<?php

namespace App\Console\Commands;

use App\Models\ResearchGrant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportCsvData extends Command
{
    protected $signature = 'import:csv {folder} {type}';
    protected $description = 'Import all CSV files from a folder into research_grants table';

    public function handle()
    {
        $folder = $this->argument('folder');
        $type   = $this->argument('type'); // 'nih' or 'excel'

        if (!is_dir($folder)) {
            $this->error("Folder not found: {$folder}");
            return 1;
        }

        $files = glob($folder . '/*.csv');

        if (empty($files)) {
            $this->warn("No CSV files found in: {$folder}");
            return 0;
        }

        $this->info("Found " . count($files) . " CSV files in {$folder}");

        foreach ($files as $file) {
            $this->info("Processing file: {$file}");
            $this->importFile($file, $type);
            $this->info("✅ Finished importing file: {$file}");
        }

        $this->info("🎉 Import completed! All files processed.");
        return 0;
    }

    private function importFile(string $file, string $type)
    {
        DB::transaction(function () use ($file, $type) {
            $handle = fopen($file, 'r');
            if (!$handle) {
                $this->error("Could not open file: {$file}");
                return;
            }

            $header = fgetcsv($handle);

            $batchSize = 1000;
            $batch = [];
            $count = 0;

            while (($row = fgetcsv($handle)) !== false) {
                // Skip empty rows
                if (count(array_filter($row)) === 0) {
                    continue;
                }

                // Fix row size mismatch
                if (count($row) < count($header)) {
                    // Pad missing columns with null
                    $row = array_pad($row, count($header), null);
                } elseif (count($row) > count($header)) {
                    // Trim extra columns
                    $row = array_slice($row, 0, count($header));
                }

                $data = array_combine($header, $row);

                if ($type === 'nih') {
                    $batch[] = $this->mapNihData($data);
                } elseif ($type === 'excel') {
                    $batch[] = $this->mapExcelData($data);
                }

                $count++;

                if (count($batch) >= $batchSize) {
                    ResearchGrant::insert($batch);
                    $batch = [];
                    $this->info("Imported {$count} records so far from {$file}...");
                }
            }

            if (!empty($batch)) {
                ResearchGrant::insert($batch);
            }

            fclose($handle);

            $this->info("Total records imported from {$file}: {$count}");
        });
    }

    private function mapNihData($data)
    {
        $cleanAmount = str_replace([',', '$', '-', ' '], '', $data['TOTAL_COST'] ?? '');
        $data['TOTAL_COST'] = is_numeric($cleanAmount) ? $cleanAmount : null;
        return [
            'source' => 'csv1',
            'application_id' => $data['APPLICATION_ID'] ?? null,
            'title' => $data['PROJECT_TITLE'] ?? null,
            'institution_name' => $data['ORG_NAME'] ?? null,
            'city' => $data['ORG_CITY'] ?? null,
            'state' => $data['ORG_STATE'] ?? null,
            'zipcode' => $data['ORG_ZIPCODE'] ?? null,
            'country' => $data['ORG_COUNTRY'] ?? null,
            'award_amount' => $data['TOTAL_COST'] ?? null,
            'start_date' => $this->parseDate($data['PROJECT_START'] ?? null),
            'end_date' => $this->parseDate($data['PROJECT_END'] ?? null),
            'funding_agency' => 'NIH',
            'activity' => $data['ACTIVITY'] ?? null,
            'administering_ic' => $data['ADMINISTERING_IC'] ?? null,
            'application_type' => $data['APPLICATION_TYPE'] ?? null,
            'arra_funded' => ($data['ARRA_FUNDED'] ?? '') === 'Y',
            'cfda_code' => $data['CFDA_CODE'] ?? null,
            'core_project_num' => $data['CORE_PROJECT_NUM'] ?? null,
            'funding_mechanism' => $data['FUNDING_MECHANISM'] ?? null,
            'org_dept' => $data['ORG_DEPT'] ?? null,
            'org_district' => $data['ORG_DISTRICT'] ?? null,
            'program_officer_name' => $data['PROGRAM_OFFICER_NAME'] ?? null,
            'project_terms' => $data['PROJECT_TERMS'] ?? null,
            'direct_cost_amt' => is_numeric($data['DIRECT_COST_AMT'] ?? null) ? $data['DIRECT_COST_AMT'] : null,
            'indirect_cost_amt' => is_numeric($data['INDIRECT_COST_AMT'] ?? null) ? $data['INDIRECT_COST_AMT'] : null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function mapExcelData($data)
    {
        $cleanAmount = str_replace([',', '$', '-', ' '], '', $data['Awarded Amount'] ?? '');
        $data['Awarded Amount'] = is_numeric($cleanAmount) ? $cleanAmount : null;
        return [
            'source' => 'csv2',
            'award_id' => $data['Award Number'] ?? null,
            'title' => $data['Title'] ?? null,
            'institution_name' => $data['Institution'] ?? null,
            'city' => $data['City'] ?? null,
            'state' => $data['State'] ?? null,
            'zipcode' => $data['Zipcode'] ?? null,
            'country' => $data['Country'] ?? null,
            'award_amount' => $data['Awarded Amount'] ?? null,
            'start_date' => $this->parseDate($data['Current Project Period Start Date'] ?? null),
            'end_date' => $this->parseDate($data['Current Project Period End Date'] ?? null),
            'award_type' => $data['Award Type'] ?? null,
            'congressional_district' => $data['118th Congressional District'] ?? null,
            'org_code' => $data['Org. Code'] ?? null,
            'division_name' => $data['Division Name'] ?? null,
            'program_area' => $data['Program Area/Topic-Subtopic'] ?? null,
            'institution_type' => $data['Institution Type'] ?? null,
            'latitude' => is_numeric($data['Latitude'] ?? null) ? $data['Latitude'] : null,
            'longitude' => is_numeric($data['Longitude'] ?? null) ? $data['Longitude'] : null,
            'funding_agency' => $data['Organization'] ?? null,
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
