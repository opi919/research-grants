<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('research_grants', function (Blueprint $table) {
            $table->id();
            $table->string('source')->comment('csv1, csv2, or json'); // To track data source

            // Common fields across all sources
            $table->string('award_id')->nullable();
            $table->string('title', 1000)->nullable();
            $table->string('pi_name', 500)->nullable();
            $table->string('institution_name', 500)->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->text('zipcode')->nullable();
            $table->string('country')->nullable();
            $table->string('award_amount')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('award_type')->nullable();
            $table->string('funding_agency')->nullable();

            // CSV1 specific fields (NIH data)
            $table->string('application_id')->nullable();
            $table->string('activity')->nullable();
            $table->string('administering_ic')->nullable();
            $table->string('application_type')->nullable();
            $table->boolean('arra_funded')->nullable();
            $table->string('cfda_code')->nullable();
            $table->string('core_project_num')->nullable();
            $table->string('funding_mechanism')->nullable();
            $table->string('org_dept')->nullable();
            $table->string('org_district')->nullable();
            $table->string('program_officer_name')->nullable();
            $table->text('project_terms')->nullable();
            $table->decimal('direct_cost_amt', 15, 2)->nullable();
            $table->decimal('indirect_cost_amt', 15, 2)->nullable();

            // CSV2/Excel specific fields
            $table->string('congressional_district')->nullable();
            $table->string('org_code')->nullable();
            $table->string('division_name')->nullable();
            $table->string('program_area')->nullable();
            $table->string('institution_type')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // JSON specific fields (NSF data)
            $table->string('awd_id')->nullable();
            $table->string('agcy_id')->nullable();
            $table->string('tran_type')->nullable();
            $table->string('cfda_num')->nullable();
            $table->string('po_email')->nullable();
            $table->string('po_phone')->nullable();
            $table->text('abstract')->nullable();
            $table->string('dir_abbr')->nullable();
            $table->string('div_abbr')->nullable();

            $table->timestamps();

            // Indexes for better search performance
            $table->index([DB::raw('institution_name(191)'), DB::raw('city(191)'), DB::raw('state(191)')], 'idx_institution_city_state');
            $table->index([DB::raw('title(191)')], 'idx_title');
            $table->index([DB::raw('pi_name(191)')], 'idx_pi_name');
            $table->index([DB::raw('award_amount')], 'idx_award_amount');
            $table->index([DB::raw('start_date'), DB::raw('end_date')], 'idx_start_date_end_date');
            $table->index([DB::raw('funding_agency')], 'idx_funding_agency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('research_grants');
    }
};
