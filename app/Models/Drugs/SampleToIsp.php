<?php

namespace App\Models\Drugs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SampleToIsp extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'number', 'document_date', 'envelope_weight', 'observation',
        'reception_id', 'user_id', 'manager_id', 'lawyer_id'
    ];

    public function reception() {
        return $this->belongsTo('App\Models\Drugs\Reception');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function manager() {
        return $this->belongsTo('App\User', 'manager_id');
    }

    public function lawyer() {
        return $this->belongsTo('App\User', 'lawyer_id');
    }

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['document_date', 'deleted_at'];

    protected $table = 'drg_sample_to_isps';
}
