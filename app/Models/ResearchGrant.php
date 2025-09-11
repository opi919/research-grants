<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class ResearchGrant extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'source',
        'award_id',
        'title',
        'pi_name',
        'institution_name',
        'city',
        'state',
        'zipcode',
        'country',
        'award_amount',
        'start_date',
        'end_date',
        'award_type',
        'funding_agency',
        'application_id',
        'activity',
        'administering_ic',
        'application_type',
        'arra_funded',
        'cfda_code',
        'core_project_num',
        'funding_mechanism',
        'org_dept',
        'org_district',
        'program_officer_name',
        'project_terms',
        'direct_cost_amt',
        'indirect_cost_amt',
        'congressional_district',
        'org_code',
        'division_name',
        'program_area',
        'institution_type',
        'latitude',
        'longitude',
        'awd_id',
        'agcy_id',
        'tran_type',
        'cfda_num',
        'po_email',
        'po_phone',
        'abstract',
        'dir_abbr',
        'div_abbr'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'award_amount' => 'decimal:2',
        'direct_cost_amt' => 'decimal:2',
        'indirect_cost_amt' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'arra_funded' => 'boolean',
    ];

    // Scout search configuration
    public function toSearchableArray()
    {
        return [
            'title' => $this->title,
            'pi_name' => $this->pi_name,
            'institution_name' => $this->institution_name,
            'abstract' => $this->abstract,
            'city' => $this->city,
            'state' => $this->state,
            'funding_agency' => $this->funding_agency,
        ];
    }

    // Scopes for filtering
    public function scopeByInstitution($query, $institution)
    {
        return $query->where('institution_name', 'like', "%{$institution}%");
    }

    public function scopeByPI($query, $pi)
    {
        return $query->where('pi_name', 'like', "%{$pi}%");
    }

    public function scopeByAmountRange($query, $min, $max)
    {
        return $query->whereBetween('award_amount', [$min, $max]);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('start_date', [$startDate, $endDate]);
    }

    public function scopeByState($query, $state)
    {
        return $query->where('state', $state);
    }

    public function scopeByFundingAgency($query, $agency)
    {
        return $query->where('funding_agency', 'like', "%{$agency}%");
    }
}
