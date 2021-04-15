@extends('layouts.app')

@section('title', 'Editar Solicitud de Contratación')

@section('content')

@include('service_requests.partials.nav')

<h3>Solicitud de Contratación de Servicios</h3>

  @can('Service Request: additional data rrhh')

    <form method="POST" action="{{ route('rrhh.service-request.update', $serviceRequest) }}" enctype="multipart/form-data">

  @else

    @if($serviceRequest->where('user_id', Auth::user()->id)->orwhere('responsable_id',Auth::user()->id)->count() > 0)
      <form method="POST" action="{{ route('rrhh.service-request.update', $serviceRequest) }}" enctype="multipart/form-data">
    @else
      <!-- si existe una firma, no se deja modificar solicitud -->
      @if($serviceRequest->SignatureFlows->where('type','!=','creador')->whereNotNull('status')->count() > 0)
        <form>
      @else
        <form method="POST" action="{{ route('rrhh.service-request.update', $serviceRequest) }}" enctype="multipart/form-data">
      @endif
    @endif

  @endcan



  @csrf
  @method('PUT')

	<div class="form-row">

    <fieldset class="form-group col col-md">
		    <label for="for_program_contract_type">Tipo</label>
		    <select name="program_contract_type" id="program_contract_type" class="form-control" required>
          <!-- <option value="Semanal" @if($serviceRequest->program_contract_type == 'Semanal') selected @endif >Semanal</option> -->
          <option value="Mensual" @if($serviceRequest->program_contract_type == 'Mensual') selected @endif >Mensual</option>
          <option value="Horas" @if($serviceRequest->program_contract_type == 'Horas') selected @endif >Horas</option>
          <!-- <option value="Otro" @if($serviceRequest->program_contract_type == 'Otro') selected @endif >Otro</option> -->
        </select>
		</fieldset>

    <fieldset class="form-group col col-md">
		    <label for="for_name">Tipo</label>
		    <select name="type" class="form-control" required>
          <option value="Covid" @if($serviceRequest->type == 'Covid') selected @endif>Honorarios - Covid</option>
          <option value="Suma alzada" @if($serviceRequest->type == 'Suma alzada') selected @endif>Suma alzada</option>
          <!-- <option value="Genérico" @if($serviceRequest->type == 'Genérico') selected @endif >Honorarios - Genérico</option> -->
        </select>
		</fieldset>

    <fieldset class="form-group col col-md">
		    <label for="for_subdirection_ou_id">Subdirección</label>
				<select class="form-control selectpicker" data-live-search="true" name="subdirection_ou_id" required="" data-size="5">
          @foreach($subdirections as $key => $subdirection)
            <option value="{{$subdirection->id}}" @if($serviceRequest->subdirection_ou_id == $subdirection->id) selected @endif >{{$subdirection->name}}</option>
          @endforeach
        </select>
		</fieldset>
    <fieldset class="form-group col col-md">
		    <label for="for_responsability_center_ou_id">Centro de Responsabilidad</label>
				<select class="form-control selectpicker" data-live-search="true" name="responsability_center_ou_id" required="" data-size="5" id="responsability_center_ou_id">
          @foreach($responsabilityCenters as $key => $responsabilityCenter)
            <option value="{{$responsabilityCenter->id}}" @if($serviceRequest->responsability_center_ou_id == $responsabilityCenter->id) selected @endif >{{$responsabilityCenter->name}}</option>
          @endforeach
        </select>
		</fieldset>

	</div>

  <div class="form-row">

    <fieldset class="form-group col">
				<label for="for_users">Responsable</label>
				<select name="responsable_id" id="responsable_id" class="form-control selectpicker" data-live-search="true" required="" data-size="5" disabled>
          @foreach($users as $key => $user)
						<option value="{{$user->id}}" @if($user->id == $serviceRequest->SignatureFlows->where('sign_position',1)->first()->responsable_id) selected @endif >{{$user->getFullNameAttribute()}}</option>
					@endforeach
				</select>
		</fieldset>

		<fieldset class="form-group col">
				<label for="for_users">Supervisor</label>
				<select name="users[]" id="users" class="form-control selectpicker" data-live-search="true" required="" data-size="5" disabled>
					@foreach($users as $key => $user)
						<option value="{{$user->id}}" @if($user->id == $serviceRequest->SignatureFlows->where('sign_position',2)->first()->responsable_id) selected @endif >{{$user->getFullNameAttribute()}}</option>
					@endforeach
				</select>
		</fieldset>

	</div>

  <div class="form-row">

    @foreach($serviceRequest->SignatureFlows->where('sign_position','>',2)->where('status','!=',2)->sortBy('sign_position') as $key => $signatureFlows)

      <fieldset class="form-group col-sm-4">
  				<label for="for_users">{{$signatureFlows->employee}}</label>
  				<select name="users[]" id="users" class="form-control selectpicker" data-live-search="true" required="" data-size="5" disabled>
  					@foreach($users as $key => $user)
  						<option value="{{$user->id}}" @if($user->id == $signatureFlows->responsable_id) selected @endif >{{$user->getFullNameAttribute()}}</option>
  					@endforeach
  				</select>
  		</fieldset>

    @endforeach

  </div>

  <br>

  <div class="border border-info rounded">
  <div class="row ml-1 mr-1">

    <fieldset class="form-group col-8 col-md-3">
		    <label for="for_rut">Rut</label>
		    <input type="text" class="form-control" id="for_rut" placeholder="" name="rut" required="required" value="{{ $serviceRequest->employee->id }}" disabled>
		</fieldset>

    <fieldset class="form-group col-1">
        <label for="for_dv">Digito</label>
        <input type="text" class="form-control" id="for_dv" name="dv" readonly value="{{ $serviceRequest->employee->dv }}">
    </fieldset>

    <fieldset class="form-group col col-md">
		    <label for="for_name">Nombre completo</label>
		    <input type="text" class="form-control" id="for_name" placeholder="" name="name" required="required" value="{{ $serviceRequest->employee->getFullNameAttribute() }}" disabled>
		</fieldset>

  </div>

	<div class="row ml-1 mr-1">

    <fieldset class="form-group col col-md">
        <label for="for_nationality">Nacionalidad</label>
        <select name="nationality" class="form-control" disabled>
          <option value=""></option>
          @foreach($countries as $key => $country)
            <option value="{{$country->id}}" @if($serviceRequest->employee->country_id == $country->id) selected @endif>{{$country->name}}</option>
          @endforeach
        </select>
    </fieldset>

    <fieldset class="form-group col col-md">
		    <label for="for_address">Dirección</label>
		    <input type="text" class="form-control" id="foraddress" name="address" value="{{$serviceRequest->address}}">
		</fieldset>

    <fieldset class="form-group col col-md">
		    <label for="for_phone_number">Número telefónico</label>
		    <input type="text" class="form-control" id="for_phone_number" name="phone_number" value="{{$serviceRequest->phone_number}}">
		</fieldset>

		<fieldset class="form-group col col-md">
		    <label for="for_email">Correo electrónico</label>
		    <input type="text" class="form-control" id="for_email" name="email" value="{{$serviceRequest->email}}">
		</fieldset>

  </div>
  </div>

  <br>

  <div class="form-row">

    <fieldset class="form-group col col-md-3">
		    <label for="for_name">Tipo de Contrato</label>
		    <select name="contract_type" class="form-control" required>
          <option value="NUEVO" @if($serviceRequest->contract_type == 'NUEVO') selected @endif >Nuevo</option>
          <option value="ANTIGUO" @if($serviceRequest->contract_type == 'ANTIGUO') selected @endif>Antiguo</option>
          <option value="CONTRATO PERM" @if($serviceRequest->contract_type == 'CONTRATO PERM') selected @endif>Permanente</option>
          <option value="PRESTACION" @if($serviceRequest->contract_type == 'PRESTACION') selected @endif>Prestación</option>
        </select>
		</fieldset>

    <fieldset class="form-group col col-md-3">
		    <label for="for_request_date">Fecha Solicitud</label>
		    <input type="date" class="form-control" id="for_request_date" name="request_date" required value="{{\Carbon\Carbon::parse($serviceRequest->request_date)->format('Y-m-d')}}" min="2020-01-01" max="2022-12-31">
		</fieldset>

    <fieldset class="form-group col col-md-3">
		    <label for="for_start_date">F.Inicio de Contrato</label>
		    <input type="date" class="form-control" id="for_start_date" name="start_date" required value="{{\Carbon\Carbon::parse($serviceRequest->start_date)->format('Y-m-d')}}" min="2020-01-01" max="2022-12-31">
		</fieldset>

    <fieldset class="form-group col col-md-3">
		    <label for="for_end_date">F.Término de Contrato</label>
		    <input type="date" class="form-control" id="for_end_date" name="end_date" required value="{{\Carbon\Carbon::parse($serviceRequest->end_date)->format('Y-m-d')}}" min="2020-01-01" max="2022-12-31">
		</fieldset>

  </div>

  <hr>

  <div class="form-row">

    <fieldset class="form-group col">
        <label for="for_service_description">Descripción Servicio</label>
        <textarea id="service_description" name="service_description" class="form-control" rows="5">{{ $serviceRequest->service_description }}</textarea>
    </fieldset>

  </div>

  <!-- <div class="card" id="control_turnos">
    <div class="card-header">
      Control de Turnos
    </div>
    <ul class="list-group list-group-flush">
      <li class="list-group-item">
        <div class="form-row">
          <fieldset class="form-group col-3">
              <label for="for_estate">Entrada</label>
              <input type="date" class="form-control" name="shift_start_date" id="shift_start_date">
          </fieldset>
          <fieldset class="form-group col">
              <label for="for_estate">Hora</label>
              <input type="time" class="form-control" name="start_hour" id="start_hour">
          </fieldset>
          <fieldset class="form-group col-3">
              <label for="for_estate">Salida</label>
              <input type="date" class="form-control" name="shift_end_date" id="shift_end_date">
          </fieldset>
          <fieldset class="form-group col">
              <label for="for_estate">Hora</label>
              <input type="time" class="form-control" name="end_hour" id="end_hour">
          </fieldset>
          <fieldset class="form-group col">
              <label for="for_estate">Observación</label>
              <input type="text" class="form-control" name="observation" id="observation">
          </fieldset>
          <fieldset class="form-group col">
              <label for="for_estate"><br/></label>

              @can('Service Request: additional data rrhh')

                <button type="button" class="btn btn-primary form-control add-row" id="shift_button_add" formnovalidate="formnovalidate">Ingresar</button>

              @else

                @if($serviceRequest->where('user_id', Auth::user()->id)->orwhere('responsable_id',Auth::user()->id)->count() > 0)
                  @if($serviceRequest->SignatureFlows->where('type','!=','creador')->whereNotNull('status')->count() > 0)
                    <button type="button" class="btn btn-primary form-control add-row" id="shift_button_add" formnovalidate="formnovalidate" disabled>Ingresar</button>
                  @else
                    <button type="button" class="btn btn-primary form-control add-row" id="shift_button_add" formnovalidate="formnovalidate">Ingresar</button>
                  @endif
                @else
                  <button type="button" class="btn btn-primary form-control add-row" id="shift_button_add" formnovalidate="formnovalidate" disabled>Ingresar</button>
                @endif

              @endcan

          </fieldset>
        </div>

        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Select</th>
                    <th>Entrada</th>
                    <th>H.Inicio</th>
                    <th>Salida</th>
                    <th>H.Término</th>
                    <th>Observación</th>
                </tr>
            </thead>
            <tbody>
              @foreach($serviceRequest->shiftControls as $key => $shiftControl)
                <tr>
                  <td><input type='checkbox' name='record'></td>
                  <td><input type="hidden" class="form-control" name="shift_start_date[]" value="{{Carbon\Carbon::parse($shiftControl->start_date)->format('Y-m-d')}}">{{Carbon\Carbon::parse($shiftControl->start_date)->format('Y-m-d')}}</td>
                  <td><input type="hidden" class="form-control" name="shift_start_hour[]" value="{{Carbon\Carbon::parse($shiftControl->start_date)->format('H:i')}}">{{Carbon\Carbon::parse($shiftControl->start_date)->format('H:i')}}</td>
                  <td><input type="hidden" class="form-control" name="shift_end_date[]" value="{{Carbon\Carbon::parse($shiftControl->end_date)->format('Y-m-d')}}">{{Carbon\Carbon::parse($shiftControl->end_date)->format('Y-m-d')}}</td>
                  <td><input type="hidden" class="form-control" name="shift_end_hour[]" value="{{Carbon\Carbon::parse($shiftControl->end_date)->format('H:i')}}">{{Carbon\Carbon::parse($shiftControl->end_date)->format('H:i')}}</td>
                  <td><input type="hidden" class="form-control" name="shift_observation[]" value="{{$shiftControl->observation}}">{{$shiftControl->observation}}</td>
                </tr>
              @endforeach
            </tbody>
        </table>

        @can('Service Request: additional data rrhh')

          <button type="button" class="btn btn-danger delete-row">Eliminar filas</button>

        @else

          @if($serviceRequest->where('user_id', Auth::user()->id)->orwhere('responsable_id',Auth::user()->id)->count() > 0)
            @if($serviceRequest->SignatureFlows->where('type','!=','creador')->whereNotNull('status')->count() > 0)
              <button type="button" class="btn btn-danger delete-row" disabled>Eliminar filas</button>
            @else
              <button type="button" class="btn btn-danger delete-row">Eliminar filas</button>
            @endif
          @else
            <button type="button" class="btn btn-danger delete-row" disabled>Eliminar filas</button>
          @endif

        @endcan

      </li>
    </ul>
  </div> -->

@if($serviceRequest->fulfillments->count()>0)
  @livewire('service-request.shifts-control', ['fulfillment' => $serviceRequest->fulfillments->first()])
@endif

  <br>

  <div class="form-row">

    <!-- <fieldset class="form-group col col-md">
		    <label for="for_estate">Estamento al que corresponde CS</label>
		    <select name="estate" class="form-control" required>
          <option value="Profesional Médico" @if($serviceRequest->estate == 'Profesional Médico') selected @endif >Profesional Médico</option>
          <option value="Profesional" @if($serviceRequest->estate == 'Profesional') selected @endif >Profesional</option>
          <option value="Técnico" @if($serviceRequest->estate == 'Técnico') selected @endif >Técnico</option>
          <option value="Administrativo" @if($serviceRequest->estate == 'Administrativo') selected @endif >Administrativo</option>
          <option value="Farmaceutico" @if($serviceRequest->estate == 'Farmaceutico') selected @endif >Farmaceutico</option>
          <option value="Odontólogo" @if($serviceRequest->estate == 'Odontólogo') selected @endif >Odontólogo</option>
          <option value="Bioquímico" @if($serviceRequest->estate == 'Bioquímico') selected @endif >Bioquímico</option>
          <option value="Auxiliar" @if($serviceRequest->estate == 'Auxiliar') selected @endif >Auxiliar</option>
          <option value="Otro (justificar)" @if($serviceRequest->estate == 'Otro (justificar)') selected @endif >Otro (justificar)</option>
        </select>
		</fieldset> -->

    <fieldset class="form-group col">
		    <label for="for_profession_id">Profesión</label>
		    <select name="profession_id" class="form-control" required id="for_profession_id">
					<option value=""></option>
					@foreach($professions as $profession)
          	<option value="{{$profession->id}}" @if($serviceRequest->profession_id == $profession->id) selected @endif>{{$profession->name}}</option>
					@endforeach
        </select>
		</fieldset>

    <fieldset class="form-group col-6 col-md-3">
		    <label for="for_working_day_type">Jornada de Trabajo</label>
		    <select name="working_day_type" class="form-control" required>
          <!-- <option value="08:00 a 16:48 hrs (L-M-M-J-V)" @if($serviceRequest->working_day_type == '08:00 a 16:48 hrs (L-M-M-J-V)') selected @endif >08:00 a 16:48 hrs (L-M-M-J-V)</option> -->
          <option value="DIURNO" @if($serviceRequest->working_day_type == 'DIURNO') selected @endif >DIURNO</option>
          <option value="TERCER TURNO" @if($serviceRequest->working_day_type == 'TERCER TURNO') selected @endif >TERCER TURNO</option>
          <option value="TERCER TURNO - MODIFICADO" @if($serviceRequest->working_day_type == 'TERCER TURNO - MODIFICADO') selected @endif >TERCER TURNO - MODIFICADO</option>
          <option value="CUARTO TURNO" @if($serviceRequest->working_day_type == 'CUARTO TURNO') selected @endif >CUARTO TURNO</option>
          <option value="CUARTO TURNO - MODIFICADO" @if($serviceRequest->working_day_type == 'CUARTO TURNO - MODIFICADO') selected @endif >CUARTO TURNO - MODIFICADO</option>

          <option value="DIURNO PASADO A TURNO" @if($serviceRequest->working_day_type == 'DIURNO PASADO A TURNO') selected @endif >DIURNO PASADO A TURNO</option>
          <option value="HORA MÉDICA" @if($serviceRequest->working_day_type == 'HORA MÉDICA') selected @endif >HORA MÉDICA</option>
          <option value="HORA EXTRA" @if($serviceRequest->working_day_type == 'HORA EXTRA') selected @endif>HORA EXTRA</option>
					<option value="TURNO EXTRA" @if($serviceRequest->working_day_type == 'TURNO EXTRA') selected @endif>TURNO EXTRA</option>

          <option value="TURNO DE REEMPLAZO" @if($serviceRequest->working_day_type == 'TURNO DE REEMPLAZO') selected @endif>TURNO DE REEMPLAZO</option>

          <option value="OTRO" @if($serviceRequest->working_day_type == 'OTRO') selected @endif >OTRO</option>
        </select>

		</fieldset>

    <fieldset class="form-group col-6 col-md-3">
		    <label for="for_working_day_type_other">Detalle Jornada Trabajo</label>
		    <input type="text" class="form-control" id="for_working_day_type_other" placeholder="" name="working_day_type_other" value="{{ $serviceRequest->working_day_type_other }}">
		</fieldset>

    <fieldset class="form-group col col-md">
		    <label for="for_weekly_hours">Hrs.Semanales</label>
		    <select name="weekly_hours" class="form-control" id="for_weekly_hours" required>
					<option value=""></option>
          <option value="44" @if($serviceRequest->weekly_hours == 44) selected @endif>44</option>
          <option value="33" @if($serviceRequest->weekly_hours == 33) selected @endif>33</option>
					<option value="28" @if($serviceRequest->weekly_hours == 28) selected @endif>28</option>
					<option value="22" @if($serviceRequest->weekly_hours == 22) selected @endif>22</option>
          <option value="11" @if($serviceRequest->weekly_hours == 11) selected @endif>11</option>
        </select>
		</fieldset>

  </div>

  <div class="form-row">

    <fieldset class="form-group col">
		    <label for="for_programm_name">Nombre del programa</label>
		    <!-- <input type="text" class="form-control" id="for_programm_name" placeholder="" name="programm_name" value="{{ $serviceRequest->programm_name }}"> -->
        <select name="programm_name" class="form-control">
          <option value=""></option>
          <option value="Covid19-APS No Médicos" @if($serviceRequest->programm_name == 'Covid19-APS No Médicos') selected @endif >Covid19-APS No Médicos</option>
          <option value="Covid19-APS Médicos" @if($serviceRequest->programm_name == 'Covid19-APS Médicos') selected @endif>Covid19-APS Médicos</option>
          <option value="Covid19 No Médicos" @if($serviceRequest->programm_name == 'Covid19 No Médicos') selected @endif>Covid19 No Médicos</option>
          <option value="Covid19 Médicos" @if($serviceRequest->programm_name == 'Covid19 Médicos') selected @endif>Covid19 Médicos</option>

          @if(Auth::user()->organizationalUnit->establishment_id == 1)
						<option value="CONSULTORIO DE LLAMADA" @if($serviceRequest->programm_name == 'CONSULTORIO DE LLAMADA') selected @endif>CONSULTORIO DE LLAMADA</option>
						<option value="33 MIL HORAS" @if($serviceRequest->programm_name == '33 MIL HORAS') selected @endif>33 MIL HORAS</option>
						<option value="DFL" @if($serviceRequest->programm_name == 'DFL') selected @endif>DFL</option>
						<option value="TURNOS VACANTES" @if($serviceRequest->programm_name == 'TURNOS VACANTES') selected @endif>TURNOS VACANTES</option>
						<option value="OTROS PROGRAMAS HETG" @if($serviceRequest->programm_name == 'OTROS PROGRAMAS HETG') selected @endif>OTROS PROGRAMAS HETG</option>
						<option value="CAMPAÑA INVIERNO" @if($serviceRequest->programm_name == 'CAMPAÑA INVIERNO') selected @endif>CAMPAÑA INVIERNO</option>
						<option value="PABELLON TARDE" @if($serviceRequest->programm_name == 'PABELLON TARDE') selected @endif>PABELLON TARDE</option>
						<option value="PABELLON GINE" @if($serviceRequest->programm_name == 'PABELLON GINE') selected @endif>PABELLON GINE</option>
						<option value="TURNO DE RESIDENCIA" @if($serviceRequest->programm_name == 'TURNO DE RESIDENCIA') selected @endif>TURNO DE RESIDENCIA</option>
					@else
						<option value="PRAPS" @if($serviceRequest->programm_name == 'PRAPS') selected @endif>PRAPS</option>
						<option value="PESPI" @if($serviceRequest->programm_name == 'PESPI') selected @endif>PESPI</option>
						<option value="CHILE CRECE CONTIGO" @if($serviceRequest->programm_name == 'CHILE CRECE CONTIGO') selected @endif>CHILE CRECE CONTIGO</option>
						<option value="OTROS PROGRAMAS SSI" @if($serviceRequest->programm_name == 'OTROS PROGRAMAS SSI') selected @endif>OTROS PROGRAMAS SSI</option>
						<option value="LISTA ESPERA" @if($serviceRequest->programm_name == 'LISTA ESPERA') selected @endif>LISTA ESPERA</option>
						<option value="CAMPAÑA INVIERNO" @if($serviceRequest->programm_name == 'CAMPAÑA INVIERNO') selected @endif>CAMPAÑA INVIERNO</option>
					@endif

        </select>
		</fieldset>

    <fieldset class="form-group col-3 col-md-3">
        <label for="for_digera_strategy">Estrategia Digera Covid</label>
        <select name="digera_strategy" class="form-control" id="digera_strategy">
          <option value=""></option>
          <option value="Camas MEDIAS Aperturadas" @if($serviceRequest->digera_strategy == "Camas MEDIAS Aperturadas") selected @endif>Camas MEDIAS Aperturadas</option>
          <option value="Camas MEDIAS Complejizadas" @if($serviceRequest->digera_strategy == "Camas MEDIAS Complejizadas") selected @endif>Camas MEDIAS Complejizadas</option>
          <option value="Camas UCI Aperturadas" @if($serviceRequest->digera_strategy == "Camas UCI Aperturadas") selected @endif>Camas UCI Aperturadas</option>
          <option value="Camas UCI Complejizadas" @if($serviceRequest->digera_strategy == "Camas UCI Complejizadas") selected @endif>Camas UCI Complejizadas</option>
          <option value="Camas UTI Aperturadas" @if($serviceRequest->digera_strategy == "Camas UTI Aperturadas") selected @endif>Camas UTI Aperturadas</option>
          <option value="Camas UTI Complejizadas" @if($serviceRequest->digera_strategy == "Camas UTI Complejizadas") selected @endif>Camas UTI Complejizadas</option>
          <option value="Cupos Hosp. Domiciliaria" @if($serviceRequest->digera_strategy == "Cupos Hosp. Domiciliaria") selected @endif>Cupos Hosp. Domiciliaria</option>
          <option value="Refuerzo Anatomía Patologica" @if($serviceRequest->digera_strategy == "Refuerzo Anatomía Patologica") selected @endif>Refuerzo Anatomía Patologica</option>
          <option value="Refuerzo Laboratorio" @if($serviceRequest->digera_strategy == "Refuerzo Laboratorio") selected @endif>Refuerzo Laboratorio</option>
          <option value="Refuerzo SAMU" @if($serviceRequest->digera_strategy == "Refuerzo SAMU") selected @endif>Refuerzo SAMU</option>
          <option value="Refuerzo UEH" @if($serviceRequest->digera_strategy == "Refuerzo UEH") selected @endif>Refuerzo UEH</option>
          <option value="Migración Colchane" @if($serviceRequest->digera_strategy == "Migración Colchane") selected @endif>Migración Colchane</option>
        </select>
    </fieldset>

    <!-- <fieldset class="form-group col-12 col-md-3">
		    <label for="for_estate_other">Detalle estamento</label>
		    <input type="text" class="form-control" id="for_estate_other" placeholder="" name="estate_other" value="{{ $serviceRequest->estate_other }}">
		</fieldset> -->

    <fieldset class="form-group col col-md">
        <label for="for_establishment_id">Establecimiento</label>
        <select name="establishment_id" class="form-control" required>
          <option value=""></option>
          @foreach($establishments as $key => $establishment)
            <option value="{{$establishment->id}}" @if($serviceRequest->establishment_id == $establishment->id) selected @endif>{{$establishment->name}}</option>
          @endforeach
        </select>
    </fieldset>

    <fieldset class="form-group col">
		    <label for="for_contractual_condition">Calidad Contractual</label>
        <select name="contractual_condition" class="form-control">
          <option value=""></option>
          <option value="SUPLENTE" @if($serviceRequest->contractual_condition == 'SUPLENTE') selected @endif >SUPLENTE</option>
          <option value="CONTRATA" @if($serviceRequest->contractual_condition == 'CONTRATA') selected @endif>CONTRATA</option>
          <option value="TITULAR" @if($serviceRequest->contractual_condition == 'TITULAR') selected @endif>TITULAR</option>
        </select>
		</fieldset>

  </div>

  <div class="row">
    <!-- <fieldset class="form-group col-3 col-md-3">
        <label for="for_rrhh_team">Equipo RRHH</label>
        <select name="rrhh_team" class="form-control">

          <option value=""></option>
          <option value="Residencia Médica" @if($serviceRequest->rrhh_team == "Residencia Médica") selected @endif>Residencia Médica</option>
          <option value="Médico Diurno" @if($serviceRequest->rrhh_team == "Médico Diurno") selected @endif>Médico Diurno</option>
          <option value="Enfermera Supervisora" @if($serviceRequest->rrhh_team == "Enfermera Supervisora") selected @endif>Enfermera Supervisora</option>
          <option value="Enfermera Diurna" @if($serviceRequest->rrhh_team == "Enfermera Diurna") selected @endif>Enfermera Diurna</option>
          <option value="Enfermera Turno" @if($serviceRequest->rrhh_team == "Enfermera Turno") selected @endif>Enfermera Turno</option>
          <option value="Kinesiólogo Diurno" @if($serviceRequest->rrhh_team == "Kinesiólogo Diurno") selected @endif>Kinesiólogo Diurno</option>
          <option value="Kinesiólogo Turno" @if($serviceRequest->rrhh_team == "Kinesiólogo Turno") selected @endif>Kinesiólogo Turno</option>
          <option value="Téc.Paramédicos Diurno" @if($serviceRequest->rrhh_team == "Téc.Paramédicos Diurno") selected @endif>Téc.Paramédicos Diurno</option>
          <option value="Téc.Paramédicos Turno" @if($serviceRequest->rrhh_team == "Téc.Paramédicos Turno") selected @endif>Téc.Paramédicos Turno</option>
          <option value="Auxiliar Diurno" @if($serviceRequest->rrhh_team == "Auxiliar Diurno") selected @endif>Auxiliar Diurno</option>
          <option value="Auxiliar Turno" @if($serviceRequest->rrhh_team == "Auxiliar Turno") selected @endif>Auxiliar Turno</option>
          <option value="Terapeuta Ocupacional" @if($serviceRequest->rrhh_team == "Terapeuta Ocupacional") selected @endif>Terapeuta Ocupacional</option>
          <option value="Químico Farmacéutico" @if($serviceRequest->rrhh_team == "Químico Farmacéutico") selected @endif>Químico Farmacéutico</option>
          <option value="Bioquímico" @if($serviceRequest->rrhh_team == "Bioquímico") selected @endif>Bioquímico</option>
          <option value="Fonoaudiologo" @if($serviceRequest->rrhh_team == "Fonoaudiologo") selected @endif>Fonoaudiologo</option>

          <option value="Administrativo Diurno" @if($serviceRequest->rrhh_team == "Administrativo Diurno") selected @endif>Administrativo Diurno</option>
          <option value="Administrativo Turno" @if($serviceRequest->rrhh_team == "Administrativo Turno") selected @endif>Administrativo Turno</option>
          <option value="Biotecnólogo Turno" @if($serviceRequest->rrhh_team == "Biotecnólogo Turno") selected @endif>Biotecnólogo Turno</option>
          <option value="Matrona Turno" @if($serviceRequest->rrhh_team == "Matrona Turno") selected @endif>Matrona Turno</option>
          <option value="Matrona Diurno" @if($serviceRequest->rrhh_team == "Matrona Diurno") selected @endif>Matrona Diurno</option>
          <option value="Otros técnicos" @if($serviceRequest->rrhh_team == "Otros técnicos") selected @endif>Otros técnicos</option>
          <option value="Psicólogo" @if($serviceRequest->rrhh_team == "Psicólogo") selected @endif>Psicólogo</option>
          <option value="Tecn. Médico Diurno" @if($serviceRequest->rrhh_team == "Tecn. Médico Diurno") selected @endif>Tecn. Médico Diurno</option>
          <option value="Tecn. Médico Turno" @if($serviceRequest->rrhh_team == "Tecn. Médico Turno") selected @endif>Tecn. Médico Turno</option>
          <option value="Trabajador Social" @if($serviceRequest->rrhh_team == "Trabajador Social") selected @endif>Trabajador Social</option>

          <option value="Nutricionista Diurno" @if($serviceRequest->rrhh_team == "Nutricionista Diurno") selected @endif>Nutricionista Diurno</option>
					<option value="Prevencionista de Riesgo" @if($serviceRequest->rrhh_team == "Prevencionista de Riesgo") selected @endif>Prevencionista de Riesgo</option>

          <option value="Nutricionista turno" @if($serviceRequest->rrhh_team == "Nutricionista turno") selected @endif>Nutricionista turno</option>

        </select>
    </fieldset>

    <fieldset class="form-group col-3 col-md-3">
        <label for="for_digera_strategy">Observaciones</label>
        <input type="text" class="form-control" name="observation" value="{{$serviceRequest->observation}}">
    </fieldset> -->

    <fieldset class="form-group col-3 col-md-3">
        <label for="for_digera_strategy"><br></label>
        @can('Service Request: additional data rrhh')
          <button type="submit" class="form-control btn btn-primary">Guardar</button>
        @else
          <!-- solo el creador de la solicitud puede editar  -->
          @if($serviceRequest->where('user_id', Auth::user()->id)->orwhere('responsable_id',Auth::user()->id)->count() > 0)
            <!-- si existe una firma, no se deja modificar solicitud -->
            @if($serviceRequest->SignatureFlows->where('type','!=','creador')->whereNotNull('status')->count() > 0)
              <button type="submit" class="form-control btn btn-primary" disabled>Guardar</button>
            @else
              <button type="submit" class="form-control btn btn-primary">Guardar</button>
            @endif
          @else
            <button type="submit" class="form-control btn btn-primary" disabled>Guardar</button>
          @endif
        @endcan
    </fieldset>

  </div>


  @can('Service Request: additional data rrhh')

  @else
    <!-- solo el creador de la solicitud puede editar  -->
    @if($serviceRequest->where('user_id', Auth::user()->id)->orwhere('responsable_id',Auth::user()->id)->count() > 0)
      <!-- si existe una firma, no se deja modificar solicitud -->
      @if($serviceRequest->SignatureFlows->where('type','!=','creador')->whereNotNull('status')->count() > 0)
        <div class="alert alert-warning" role="alert">
          No se puede modificar hoja de ruta ya que existen visaciones realizadas.
        </div>
      @else
      @endif
    @else
    @endif
  @endcan



  <br>

  </form>

  @canany(['Service Request: additional data rrhh'])
  <form method="POST" action="{{ route('rrhh.service-request.update_aditional_data', $serviceRequest) }}" enctype="multipart/form-data">
  @csrf
  @method('PUT')

  <div class="card border-danger mb-3">
    <div class="card-header bg-danger text-white">
      Datos adicionales - RRHH
    </div>
      <div class="card-body">

        <div class="form-row">

          <fieldset class="form-group col col-md-3">
					    <label for="for_name">N°Contrato</label>
              <input type="text" class="form-control" name="contract_number" value="{{$serviceRequest->contract_number}}">
					</fieldset>

          <fieldset class="form-group col col-md-3">
					    <label for="for_resolution_number">N° Resolución</label>
              <input type="text" class="form-control" name="resolution_number" value="{{$serviceRequest->resolution_number}}">
					</fieldset>

          <fieldset class="form-group col col-md-3">
              <label for="for_resolution_date">Fecha Resolución</label>
              <input type="date" class="form-control" id="for_resolution_date" name="resolution_date" @if($serviceRequest->resolution_date) value="{{$serviceRequest->resolution_date->format('Y-m-d')}}" @endif>
          </fieldset>

          <fieldset class="form-group col col-md-2">
            <label for="for_sirh_contract_registration">&nbsp;</label>
            <div>
              <a href="{{ route('rrhh.service-request.report.resolution-pdf', $serviceRequest) }}"
                class="btn btn-outline-secondary" target="_blank" title="Resolución">
              <span class="fas fa-file-pdf" aria-hidden="true"></span></a>
            </div>
          </fieldset>

        </div>

        <div class="form-row">

          <fieldset class="form-group col col-md-3">
					    <label for="for_net_amount">Monto Neto</label>
              <input type="text" class="form-control" name="net_amount" value="{{$serviceRequest->net_amount}}">
					</fieldset>

          <fieldset class="form-group col col-md-3">
					    <label for="for_gross_amount">Monto Bruto</label>
              <input type="text" class="form-control" name="gross_amount" value="{{$serviceRequest->gross_amount}}">
					</fieldset>

          <fieldset class="form-group col col-md-3">
              <label for="for_sirh_contract_registration">Registrado en SIRH</label>
              <select name="sirh_contract_registration" class="form-control">
                <option value=""></option>
                <option value="1"  @if($serviceRequest->sirh_contract_registration == '1') selected @endif>Sí</option>
                <option value="0"  @if($serviceRequest->sirh_contract_registration == '0') selected @endif>No</option>
              </select>
          </fieldset>

        </div>

        <button type="submit" class="btn btn-primary mb-3">Guardar</button>

      </div>

  </div>

  <br>
  </form>
  @endcan


  <!-- @canany(['Service Request: additional data finanzas'])
  <form method="POST" action="{{ route('rrhh.service-request.update_aditional_data', $serviceRequest) }}" enctype="multipart/form-data">
  @csrf
  @method('PUT')

  <div class="card border-info mb-3">
    <div class="card-header bg-info text-white">
      Datos adicionales - Finanzas
    </div>
      <div class="card-body">

        <div class="row">
          <fieldset class="form-group col-5 col-md-2">
					    <label for="for_resolution_number">N° Resolución</label>
              <input type="text" class="form-control" disabled name="resolution_number" value="{{$serviceRequest->resolution_number}}">
					</fieldset>

          <fieldset class="form-group col-7 col-md-3">
              <label for="for_resolution_date">Fecha Resolución</label>
              <input type="date" class="form-control" id="for_resolution_date" disabled name="resolution_date" @if($serviceRequest->resolution_date) value="{{$serviceRequest->resolution_date->format('Y-m-d')}}" @endif>
          </fieldset>
        </div>

        <div class="form-row">

          <fieldset class="form-group col-6 col-md-2">
              <label for="for_bill_number">N° Boleta</label>
              <input type="text" class="form-control" name="bill_number" value="{{$serviceRequest->bill_number}}">
          </fieldset>

          <fieldset class="form-group col-6 col-md-2">
              <label for="for_total_hours_paid">Tot. hrs pagadas per.</label>
              <input type="text" class="form-control" name="total_hours_paid" value="{{$serviceRequest->total_hours_paid}}">
          </fieldset>

          <fieldset class="form-group col-6 col-md-2">
              <label for="for_total_paid">Total pagado</label>
              <input type="text" class="form-control" name="total_paid" value="{{$serviceRequest->total_paid}}">
          </fieldset>

          <fieldset class="form-group col-6 col-md-3">
              <label for="for_payment_date">Fecha pago</label>
              <input type="date" class="form-control" id="for_payment_date" name="payment_date" required @if($serviceRequest->payment_date) value="{{$serviceRequest->payment_date->format('Y-m-d')}}" @endif>
          </fieldset>

        </div>

        <button type="submit" class="btn btn-info">Guardar</button>

      </div>

  </div>

  <br>
  </form>
  @endcan -->



  @canany(['Service Request: additional data oficina partes'])
  <form method="POST" action="{{ route('rrhh.service-request.update_aditional_data', $serviceRequest) }}" enctype="multipart/form-data">
  @csrf
  @method('PUT')

  <div class="card border-success mb-3">
    <div class="card-header bg-success text-white">
      Datos adicionales - Oficina de Partes
    </div>
      <div class="card-body">

        <div class="form-row">

          <fieldset class="form-group col col-md">
					    <label for="for_resolution_number">N° Resolución</label>
              <input type="text" class="form-control" name="resolution_number" value="{{$serviceRequest->resolution_number}}">
					</fieldset>

          <fieldset class="form-group col col-md">
              <label for="for_resolution_date">Fecha Resolución</label>
              <input type="date" class="form-control" id="for_resolution_date" name="resolution_date" @if($serviceRequest->resolution_date) value="{{$serviceRequest->resolution_date->format('Y-m-d')}}" @endif>
          </fieldset>

        </div>

        <button type="submit" class="btn btn-primary">Guardar</button>

      </div>

  </div>

  </form>
  @endcan

<hr>

<form method="POST" action="{{ route('rrhh.service-request.signature-flow.store') }}" enctype="multipart/form-data">
@csrf


<div class="card">
  <div class="card-header">
    Aprobaciones de Solicitud
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="card-table table table-sm table-bordered small">
          <thead>
            <tr>
              <th scope="col">Fecha</th>
              <th scope="col">U.Organizacional</th>
              <th scope="col">Cargo</th>
              <th scope="col">Usuario</th>
              <th scope="col">Tipo</th>
              <th scope="col">Estado</th>
              <th scope="col">Observación</th>
            </tr>
          </thead>
          <tbody>
            <!-- aceptado o rechazado -->
            @if($serviceRequest->SignatureFlows->where('status',2)->count()==0)
              @foreach($serviceRequest->SignatureFlows->sortBy('sign_position') as $key => $SignatureFlow)
                @if($SignatureFlow->status === null)
                  <tr class="bg-light">
                @elseif($SignatureFlow->status === 0)
                  <tr class="bg-danger">
                @elseif($SignatureFlow->status === 1)
                  <tr>
                @elseif($SignatureFlow->status === 2)
                  <tr class="bg-warning">
                @endif
                   <td>{{ $SignatureFlow->signature_date}}</td>
                   <td>{{ $SignatureFlow->organizationalUnit->name}}</td>
                   <td>{{ $SignatureFlow->employee }}</td>
                   <td>{{ $SignatureFlow->user->getFullNameAttribute() }}</td>
                   <td>{{ $SignatureFlow->type }}</td>
                   <td>@if($SignatureFlow->status === null)
                       @elseif($SignatureFlow->status === 1) Aceptada
                       @elseif($SignatureFlow->status === 0) Rechazada
                       @elseif($SignatureFlow->status === 2) Devuelta
                       @endif
                  </td>
                   <td>{{ $SignatureFlow->observation }}</td>
                 </tr>

                 @if($SignatureFlow->status === 0 && $SignatureFlow->observation != null)
                 <tr>
                   <td class="text-right" colspan="6">Observación rechazo: {{$SignatureFlow->observation}}</td>
                 </tr>
                 @endif
             @endforeach
            @else
            <!-- devolucion -->
              @foreach($serviceRequest->SignatureFlows->sortBy('created_at') as $key => $SignatureFlow)
                @if($SignatureFlow->status === null)
                  <tr class="bg-light">
                @elseif($SignatureFlow->status === 0)
                  <tr class="bg-danger">
                @elseif($SignatureFlow->status === 1)
                  <tr>
                @elseif($SignatureFlow->status === 2)
                  <tr class="bg-warning">
                @endif
                   <td>{{ $SignatureFlow->signature_date}}</td>
                   <td>{{ $SignatureFlow->organizationalUnit->name}}</td>
                   <td>{{ $SignatureFlow->employee }}</td>
                   <td>{{ $SignatureFlow->user->getFullNameAttribute() }}</td>
                   <td>{{ $SignatureFlow->type }}</td>
                   <td>@if($SignatureFlow->status === null)
                       @elseif($SignatureFlow->status === 1) Aceptada
                       @elseif($SignatureFlow->status === 0) Rechazada
                       @elseif($SignatureFlow->status === 2) Devuelta
                       @endif
                  </td>
                   <td>{{ $SignatureFlow->observation }}</td>
                 </tr>

                 @if($SignatureFlow->status === 0 && $SignatureFlow->observation != null)
                 <tr>
                   <td class="text-right" colspan="6">Observación rechazo: {{$SignatureFlow->observation}}</td>
                 </tr>
                 @endif
             @endforeach
            @endif
          </tbody>
      </table>
      </div>
      <div class="form-row">
        <fieldset class="form-group col col-md-3">
            <label for="for_name">Tipo</label>
            <input type="text" class="form-control" name="employee" value="{{$employee}}" readonly="readonly">
            <input type="hidden" class="form-control" name="service_request_id" value="{{$serviceRequest->id}}">
        </fieldset>
        <fieldset class="form-group col col-md-3">
            <label for="for_name">Estado Solicitud</label>
            <select name="status" class="form-control">
              <option value="">Seleccionar una opción</option>
              <option value="1">Aceptar</option>
              <option value="0">Rechazar</option>
              <option value="2">Devolver</option>
            </select>
        </fieldset>
        <fieldset class="form-group col col-md-5">
            <label for="for_observation">Observación</label>
            <input type="text" class="form-control" id="for_observation" placeholder="" name="observation">
        </fieldset>
        <fieldset class="form-group col col-md-1">
            <label for="for_button"><br></label>
            <button type="submit" id="for_button" class="form-control btn btn-primary">Guardar</button>
        </fieldset>
    </div>
  </div>
</div>


</form>


@can('Service Request: delete request')
  <br>
  <form method="POST" action="{{ route('rrhh.service-request.destroy-with-parameters') }}" enctype="multipart/form-data" class="d-inline">
      @csrf
      @method('POST')
      <input type="hidden" name="id" value="{{$serviceRequest->id}}">

      <div class="form-group row">
        <div class="col-sm-3">
        </div>
        <div class="col-sm-6">
          <input type="text" class="form-control" name="observation" placeholder="Observación">
        </div>
        <div class="col-sm-3">
          <button type="submit" class="form-control btn btn-danger">Eliminar solicitud</button>
        </div>
      </div>
  </form>
@endcan

    @canany(['Service Request: audit'])
    <br /><hr />
    <div style="height: 300px; overflow-y: scroll;">
        @include('service_requests.requests.partials.audit', ['audits' => $serviceRequest->audits] )
    </div>
    @endcanany

@endsection

@section('custom_js')
<script type="text/javascript">

	$( document ).ready(function() {

    //temporal, solicitado por eduardo
    if ($('select[id=responsability_center_ou_id] option').filter(':selected').text() == "Departamento de Salud Ocupacional" ||
        $('select[id=responsability_center_ou_id] option').filter(':selected').text() == "Extensión Hospital -Estadio" ||
        $('select[id=responsability_center_ou_id] option').filter(':selected').text() == "Sección Administrativa Honorarios Covid" ||
        $('select[id=responsability_center_ou_id] option').filter(':selected').text() == "Servicio de Cirugía" ||
        $('select[id=responsability_center_ou_id] option').filter(':selected').text() == "Servicio de Ginecología y Obstetricia" ||
        $('select[id=responsability_center_ou_id] option').filter(':selected').text() == "Servicio de Medicina" ||
        $('select[id=responsability_center_ou_id] option').filter(':selected').text() == "Unidad de Alimentación y Nutrición" ||
        $('select[id=responsability_center_ou_id] option').filter(':selected').text() == "Unidad de Gestión de Camas" ||
        $('select[id=responsability_center_ou_id] option').filter(':selected').text() == "Unidad de Ginecología" ||
        $('select[id=responsability_center_ou_id] option').filter(':selected').text() == "Unidad de Medicina Física y Rehabilitación" ||
        $('select[id=responsability_center_ou_id] option').filter(':selected').text() == "Unidad de Movilización" ||
        $('select[id=responsability_center_ou_id] option').filter(':selected').text() == "Unidad de Salud Ocupacional" ||
        $('select[id=responsability_center_ou_id] option').filter(':selected').text() == "Unidad Imagenología") {
      $('#digera_strategy').val("Camas MEDIAS Complejizadas");
    }

    if ($('select[id=responsability_center_ou_id] option').filter(':selected').text() == "Servicio de Anestesia y Pabellones" ||
        $('select[id=responsability_center_ou_id] option').filter(':selected').text() == "Servicio Unidad Paciente Crítico Adulto" ||
        $('select[id=responsability_center_ou_id] option').filter(':selected').text() == "Servicio Unidad Paciente Crítico Pediatrico"){
      $('#digera_strategy').val("Camas UCI Complejizadas");
    }

    if ($('select[id=responsability_center_ou_id] option').filter(':selected').text() == "Unidad de Hospitalización Domiciliaria"){
      $('#digera_strategy').val("Cupos Hosp. Domiciliaria");
    }

    if ($('select[id=responsability_center_ou_id] option').filter(':selected').text() == "Subdirección de Gestion Asistencial / Subdirección Médica" ||
        $('select[id=responsability_center_ou_id] option').filter(':selected').text() == "Unidad Laboratorio Clínico"){
      $('#digera_strategy').val("Refuerzo Laboratorio");
    }

    if ($('select[id=responsability_center_ou_id] option').filter(':selected').text() == "Establecimientos de Red de Urgencias"){
      $('#digera_strategy').val("Refuerzo SAMU");
    }

    if ($('select[id=responsability_center_ou_id] option').filter(':selected').text() == "Consultorio General Urbano Dr. Hector Reyno" ||
        $('select[id=responsability_center_ou_id] option').filter(':selected').text() == "Servicio de Emergencia Hospitalaria" ||
        $('select[id=responsability_center_ou_id] option').filter(':selected').text() == "Servicio Urgencia Ginecoobstetricia"){
      $('#digera_strategy').val("Refuerzo UEH");
    }





    if ($('select[id=responsability_center_ou_id] option').filter(':selected').text() == "xxx") {
      $('#digera_strategy').val("xxx");
    }

    if ($('#program_contract_type').val() == "Horas") {
      $("#control_turnos").show();
      $('#for_weekly_hours').attr('disabled', 'disabled');
    }else{
      $("#control_turnos").hide();
      $('#for_weekly_hours').removeAttr('disabled');
    }


		$('#program_contract_type').on('change', function() {
			if (this.value == "Horas") {
				$('#for_daily_hours').val("");
				$('#for_nightly_hours').val("");
				$('#for_daily_hours').attr('readonly', true);
				$('#for_nightly_hours').attr('readonly', true);
        $('#for_weekly_hours').attr('disabled', 'disabled');
				$("#control_turnos").show();
			}else{
				$('#for_daily_hours').attr('readonly', false);
				$('#for_nightly_hours').attr('readonly', false);
        $('#for_weekly_hours').removeAttr('disabled');
				$("#control_turnos").hide();
			}
		});

  	$(".add-row").click(function(){
        var shift_start_date = $("#shift_start_date").val();
        var start_hour = $("#start_hour").val();
        var shift_end_date = $("#shift_end_date").val();
        var end_hour = $("#end_hour").val();
  			var observation = $("#observation").val();
        var markup = "<tr><td><input type='checkbox' name='record'></td><td> <input type='hidden' class='form-control' name='shift_start_date[]' id='shift_start_date' value='"+ shift_start_date +"'>"+ shift_start_date +"</td><td> <input type='hidden' class='form-control' name='shift_start_hour[]' id='start_hour' value='"+ start_hour +"'>" + start_hour + "</td><td> <input type='hidden' class='form-control' name='shift_end_date[]' id='shift_end_date' value='"+ shift_end_date +"'>"+ shift_end_date +"</td><td> <input type='hidden' class='form-control' name='shift_end_hour[]' id='end_hour' value='"+ end_hour +"'>" + end_hour + "</td><td> <input type='hidden' class='form-control' name='shift_observation[]' id='observation' value='"+ observation +"'>" + observation + "</td></tr>";
        $("table tbody").append(markup);

  			// $("#shift_date").val("");
        // $("#start_hour").val("");
  			// $("#end_hour").val("");
  			// $("#observation").val("");
    });

  	// Find and remove selected table rows
    $(".delete-row").click(function(){
        $("table tbody").find('input[name="record"]').each(function(){
        	if($(this).is(":checked")){
                $(this).parents("tr").remove();
            }
        });
    });

  });
</script>
@endsection
