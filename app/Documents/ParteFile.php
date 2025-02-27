<?php

namespace App\Documents;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParteFile extends Model
{
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
    protected $fillable = [
      'id','file','name','signature_file_id','parte_id'
    ];


    //relaciones
    public function event() {
        return $this->belongsTo('App\Documents\Parte');
    }

    
    public function signatureFile() {
      return $this->belongsTo('App\Models\Documents\SignaturesFile');
      //return $this->belongsTo('App\Models\Documents\SignaturesFile', 'file_to_sign_id');
  }


    use SoftDeletes;

    /**
    * The table associated with the model.
    *
    * @var string
    */


    protected $table = 'parte_files';
}
