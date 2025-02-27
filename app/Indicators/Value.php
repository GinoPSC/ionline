<?php

namespace App\Indicators;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Value extends Model
{
    use SoftDeletes;
    protected $table = 'ind_values';
    protected $fillable = ['month', 'factor', 'value', 'created_by', 'updated_by'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->updated_by = Auth::id();
            $model->created_by = Auth::id();
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::id();
        });

        static::deleting(function ($model) {
            $model->updated_by = Auth::id();
        });
    }

    public function valueable(){
        return $this->morphTo();
    }

    public function creator(){
        return $this->belongsTo('App\User','created_by')->withTrashed();
    }

    public function editor(){
        return $this->belongsTo('App\IUser','edited_by')->withTrashed();
    }
}