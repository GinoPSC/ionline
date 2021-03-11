<?php

namespace App\Models\ServiceRequests;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceRequest extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'responsable_id','user_id','subdirection_ou_id', 'responsability_center_ou_id',
        'type', 'rut', 'name', 'request_date', 'start_date', 'end_date', 'contract_type','contractual_condition',
        'service_description', 'programm_name', 'other', 'normal_hour_payment', 'amount',
        'program_contract_type', 'weekly_hours', 'daily_hours', 'nightly_hours', 'estate',
        'estate_other', 'working_day_type', 'working_day_type_other', 'subdirection_id',
        'responsability_center_id','budget_cdp_number', 'budget_item', 'budget_amount',
        'budget_date', 'contract_number','month_of_payment','establishment_id','nationality',
        'digera_strategy','rrhh_team','gross_amount', 'net_amount','sirh_contract_registration',
        'resolution_number','resolution_date','bill_number','total_hours_paid','total_paid',
        'payment_date','address','phone_number','email','verification_code'

    ];

    public function MonthOfPayment() {
      if ($this->payment_date) {
        if ($this->payment_date->format('m') == 1) {
          return "Enero";
        }elseif ($this->payment_date->format('m') == 2) {
          return "Febrero";
        }elseif ($this->payment_date->format('m') == 3) {
          return "Marzo";
        }elseif ($this->payment_date->format('m') == 4) {
          return "Abril";
        }elseif ($this->payment_date->format('m') == 5) {
          return "Mayo";
        }elseif ($this->payment_date->format('m') == 6) {
          return "Junio";
        }elseif ($this->payment_date->format('m') == 7) {
          return "Julio";
        }elseif ($this->payment_date->format('m') == 8) {
          return "Agosto";
        }elseif ($this->payment_date->format('m') == 9) {
          return "Septiembre";
        }elseif ($this->payment_date->format('m') == 10) {
          return "Octubre";
        }elseif ($this->payment_date->format('m') == 11) {
          return "Noviembre";
        }elseif ($this->payment_date->format('m') == 12) {
          return "Diciembre";
        }
      }
    }

    public function programm_name_id(){
      dd($this->programm_name);
      if (str_contains($this->programm_name,'No médico')) {
        dd("no médico");
      }
    }

    public function working_day_type_description(){
      if ($this->working_day_type == "DIURNO") {
        return "un largo de 08:00 a 20:00 hrs., una noche de 20:00 a 08:00 hrs. y dos días libres";
      }
      if ($this->working_day_type == "TERCER TURNO") {
        return "dos largos de 08:00 a 20:00 hrs., dos una noches de 20:00 a 08:00 hrs. y dos días libres";
      }
      if ($this->working_day_type == "CUARTO TURNO") {
        return "horario diruno";
      }
    }

    public function user(){
      return $this->belongsTo('App\User','responsable_id');
    }

    public function SignatureFlows() {
    		return $this->hasMany('\App\Models\ServiceRequests\SignatureFlow');
    }

    public function establishment() {
    		return $this->belongsTo('\App\Establishment');
    }

    // public function responsabilityCenter() {
    // 		return $this->belongsTo('\App\Models\ServiceRequests\ResponsabilityCenter');
    // }

    public function subdirection() {
    		return $this->belongsTo('\App\Rrhh\OrganizationalUnit','subdirection_ou_id');
    }

    public function responsabilityCenter() {
        return $this->belongsTo('\App\Rrhh\OrganizationalUnit','responsability_center_ou_id');
    }

    public function shiftControls() {
    		return $this->hasMany('\App\Models\ServiceRequests\ShiftControl');
    }

    public function fulfillments() {
    		return $this->hasMany('\App\Models\ServiceRequests\Fulfillment');
    }

    public static function getPendingRequests()
    {
      // $serviceRequestsPendingsCount = ServiceRequest::whereHas("SignatureFlows", function($subQuery) {
      //                                              $subQuery->where('user_id',Auth::user()->id)
      //                                                       ->orwhere('responsable_id',Auth::user()->id);
      //                                              $subQuery->whereNull('status');
      //                                            })
      //                                            ->where('user_id','!=',Auth::user()->id)
      //                                            ->orderBy('id','asc')
      //                                            ->count();

      $user_id = Auth::user()->id;
      $serviceRequests = ServiceRequest::whereHas("SignatureFlows", function($subQuery) use($user_id){
                                           $subQuery->where('responsable_id',$user_id);
                                           $subQuery->orwhere('user_id',$user_id);
                                         })
                                         ->orderBy('id','asc')
                                         ->get();
      $cont = 0;
      foreach ($serviceRequests as $key => $serviceRequest) {
        if ($serviceRequest->SignatureFlows->where('status','===',0)->count() == 0) {
          foreach ($serviceRequest->SignatureFlows->sortBy('sign_position') as $key2 => $signatureFlow) {
            if ($user_id == $signatureFlow->responsable_id) {
              if ($signatureFlow->status == NULL) {
                if ($serviceRequest->SignatureFlows->where('sign_position',$signatureFlow->sign_position-1)->first()->status == NULL) {
                }else{
                  $cont += 1;
                }

              }else{
              }
            }
          }
        }
      }

      return $cont;
    }


    protected $table = 'doc_service_requests';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['request_date', 'start_date', 'end_date', 'budget_date', 'payment_date', 'resolution_date'];
}
