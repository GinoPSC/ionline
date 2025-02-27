<?php

namespace App\Http\Livewire\RequestForm;

use Livewire\Component;
use App\Models\RequestForms\RequestForm;
use App\Models\RequestForms\EventRequestForm;
use App\Models\Parameters\BudgetItem;
use App\Rrhh\Authority;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\RequestFormSignNotification;

class PrefinanceAuthorization extends Component
{
    public $organizationalUnit, $userAuthority, $position, $requestForm, $eventType, $comment,
           $lstBudgetItem, $program, $sigfe, $codigo;
    public $arrayItemRequest = [['budgetId' => '']];
    public $round_trips, $baggages;

    protected $rules = [
        'comment' => 'required|min:6',
    ];


    protected $messages = [
        'comment.required'  => 'Debe ingresar un comentario antes de rechazar Formulario.',
        'comment.min'       => 'Mínimo 6 caracteres.',
    ];


    public function mount(RequestForm $requestForm, $eventType, $roundTrips, $baggages) {
      $this->eventType          = $eventType;
      $this->requestForm        = $requestForm;
      $this->round_trips        = $roundTrips;
      $this->baggages           = $baggages;
      $this->comment    = '';
      $this->codigo             = '';
      $this->lstBudgetItem      = BudgetItem::all();
      $this->organizationalUnit = auth()->user()->organizationalUnit->name;
      $this->userAuthority      = auth()->user()->getFullNameAttribute();
      $this->position           = auth()->user()->position;
      $this->program            = $requestForm->program;
      $this->sigfe              = $requestForm->sigfe;
    }


    public function resetError(){
      $this->resetErrorBag();
    }


    public function acceptRequestForm() {
      $this->validate(
        [
            'sigfe'                        =>  'required',
            'program'                      =>  'required',
            'arrayItemRequest'             =>  'required|min:'.(count($this->requestForm->itemRequestForms)+1)
        ],
        [
            'sigfe.required'               =>  'Ingrese valor para  SIGFE.',
            'program.required'             =>  'Ingrese un Programa Asociado.',
            'arrayItemRequest.min'         =>  'Debe seleccionar todos los items presupuestario.',
        ],
      );
      foreach($this->requestForm->itemRequestForms as $item){
        $item->budget_item_id = $this->arrayItemRequest[$item->id]['budgetId'];
        $item->save();
      }
      $event = $this->requestForm->eventRequestForms()->where('event_type', $this->eventType)->where('status', 'pending')->first();
      if(!is_null($event)){
          //  $this->requestForm->status = 'pending';
          $this->requestForm->program = $this->program;
          $this->requestForm->sigfe = $this->sigfe;
          $this->requestForm->save();
          $event->signature_date = Carbon::now();
          $event->position_signer_user = $this->position;
          $event->status  = 'approved';
          $event->comment = $this->comment;
          $event->signerUser()->associate(auth()->user());
          $event->save();

          $nextEvent = $event->requestForm->eventRequestForms->where('cardinal_number', $event->cardinal_number + 1);

           if(!$nextEvent->isEmpty()){
               //Envío de notificación para visación.
               $now = Carbon::now();
               //manager
               $type = 'manager';
               $mail_notification_ou_manager = Authority::getAuthorityFromDate($nextEvent->first()->ou_signer_user, Carbon::now(), $type);
               //secretary
               // $type_adm = 'secretary';
               // $mail_notification_ou_secretary = Authority::getAuthorityFromDate($nextEvent->first()->ou_signer_user, Carbon::now(), $type_adm);

               $emails = [$mail_notification_ou_manager->user->email];

               if($mail_notification_ou_manager){
                  Mail::to($emails)
                    ->cc(env('APP_RF_MAIL'))
                    ->send(new RequestFormSignNotification($event->requestForm, $nextEvent->first()));
               }
           }

           session()->flash('info', 'Formulario de Requerimientos Nro.'.$this->requestForm->folio.' AUTORIZADO correctamente!');
           return redirect()->route('request_forms.pending_forms');
          }
      session()->flash('danger', 'Formulario de Requerimientos Nro.'.$this->requestForm->folio.' NO se puede Autorizar!');
      return redirect()->route('request_forms.pending_forms');
    }


    public function rejectRequestForm() {
      $this->validate();
      $event = $this->requestForm->eventRequestForms()->where('event_type', $this->eventType)->where('status', 'pending')->first();
      if(!is_null($event)){
           $this->requestForm->status = 'rejected';
           $this->requestForm->save();
           $event->signature_date = Carbon::now();
           $event->comment = $this->comment;
           $event->position_signer_user = $this->position;
           $event->status = 'rejected';
           $event->signerUser()->associate(auth()->user());
           $event->save();
           session()->flash('info', 'Formulario de Requerimientos Nro.'.$this->requestForm->folio.' fue RECHAZADO!');
           return redirect()->route('request_forms.pending_forms');
          }
      session()->flash('danger', 'Formulario de Requerimientos Nro.'.$this->requestForm->folio.' NO se puede Rechazar!');
      return redirect()->route('request_forms.pending_forms');
    }


    public function render() {
        return view('livewire.request-form.prefinance-authorization');
    }
}
