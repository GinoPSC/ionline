<?php

namespace App\Models\RequestForms;

use App\Models\Parameters\PurchaseType;
use App\Models\Parameters\Supplier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class DirectDeal extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use SoftDeletes;


    protected $fillable = [
        'purchase_type_id' ,'description', 'resol_direct_deal', 'resol_contract', 'guarantee_ticket', 'supplier_id'
    ];

    public function purchaseType()
    {
        return $this->belongsTo(PurchaseType::class, 'purchase_type_id');
    }

    public function attachedFiles()
    {
        return $this->hasMany(AttachedFile::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    protected $table = 'arq_direct_deals';

}
