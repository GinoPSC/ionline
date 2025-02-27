@extends('layouts.app')

@section('title', 'Staff')

@section('content')

@include('replacement_staff.nav')

<h5>Edición de Staff</h5>

<br>

<form method="POST" class="form-horizontal" action="{{ route('replacement_staff.update', $replacementStaff) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="form-row">
        <fieldset class="form-group col-sm-2">
            <label for="for_run">RUT</label>
            <input type="text" class="form-control" name="run" id="for_run" value="{{ $replacementStaff->run }}" readonly>
        </fieldset>
        <fieldset class="form-group col-sm-1">
            <label for="for_dv">DV</label>
            <input type="text" class="form-control" name="dv" id="for_dv" value="{{ $replacementStaff->dv }}" readonly>
        </fieldset>

        <fieldset class="form-group col-sm-3">
            <label for="for_birthday">Fecha Nacimiento</label>
            <input type="date" class="form-control" id="for_birthday" name="birthday" value="{{ $replacementStaff->birthday->format('Y-m-d')  }}" required readonly>
        </fieldset>
    </div>

    <div class="form-row">
        <fieldset class="form-group col-3">
            <label for="for_name">Nombres</label>
            <input type="text" class="form-control" name="name" id="for_name" value="{{ $replacementStaff->name }}" readonly>
        </fieldset>
        <fieldset class="form-group col-3">
            <label for="for_name">Apellido Paterno</label>
            <input type="text" class="form-control" name="fathers_family" id="for_fathers_family" value="{{ $replacementStaff->fathers_family }}" readonly>
        </fieldset>
        <fieldset class="form-group col-3">
            <label for="for_name">Apellido Materno</label>
            <input type="text" class="form-control" name="mothers_family" id="for_mothers_family" value="{{ $replacementStaff->mothers_family }}" readonly>
        </fieldset>
        <fieldset class="form-group col-3">
            <label for="for_gender" >Género</label>
            <select name="gender" id="for_gender" class="form-control" required readonly>
                <option value="">Seleccione...</option>
                <option value="male" {{ ($replacementStaff->gender == 'male')?'selected':'' }}>Masculino</option>
                <option value="female" {{ ($replacementStaff->gender == 'female')?'selected':'' }}>Femenino</option>
                <option value="other" {{ ($replacementStaff->gender == 'other')?'selected':'' }}>Otro</option>
                <option value="unknown" {{ ($replacementStaff->gender == 'unknown')?'selected':'' }}>Desconocido</option>
            </select>
        </fieldset>
    </div>

    <div class="form-row">
        <fieldset class="form-group col-6">
            <label for="for_email">Correo Electrónico</label>
            <input type="text" class="form-control" name="email" id="for_email" value="{{ $replacementStaff->email }}" required readonly>
        </fieldset>
        <fieldset class="form-group col-3">
            <label for="for_telephone">Teléfono Movil</label>
            <input type="text" class="form-control" name="telephone" id="for_telephone"  placeholder="+569xxxxxxxx" value="{{ $replacementStaff->telephone }}" required readonly>
        </fieldset>
        <fieldset class="form-group col-3">
            <label for="for_telephone2">Teléfono Fijo</label>
            <input type="text" class="form-control" name="telephone2" id="for_telephone2"  placeholder="572xxxxxx" value="{{ $replacementStaff->telephone2 }}" readonly>
        </fieldset>
    </div>

    <div class="form-row">
        @livewire('replacement-staff.commune-region-select', ['replacementStaff' => $replacementStaff])

        <fieldset class="form-group col">
            <label for="for_address">Dirección</label>
            <input type="text" class="form-control" name="address" id="for_address" value="{{ $replacementStaff->address }}" required readonly>
        </fieldset>
    </div>

    <div class="form-row">
      <fieldset class="form-group col-6">
          <label for="for_status">Disponibilidad</label>
          <select name="status" id="for_status" class="form-control" required readonly>
              <option value="">Seleccione...</option>
              <option value="immediate_availability" {{ ($replacementStaff->status == 'immediate_availability')?'selected':'' }}>Inmediata</option>
              <option value="working_external" {{ ($replacementStaff->status == 'working_external')?'selected':'' }}>Trabajando</option>
          </select>
      </fieldset>
      <div class="col">
        <p>Curriculum Vitae</p>
        <a href="{{ route('replacement_staff.view_file', $replacementStaff) }}"
            class="btn btn-outline-secondary btn-sm"
            title="Ir"
            target="_blank"> <i class="far fa-eye"></i></a>
        <a class="btn btn-outline-secondary btn-sm"
            href="{{ route('replacement_staff.download_file', $replacementStaff) }}"
            target="_blank"><i class="fas fa-download"></i>
        </a>
      </div>
    </div>

    <button type="submit" class="btn btn-primary float-right" disabled>Guardar <i class="fas fa-save"></i></button>

</form>

<br><br>

<hr>

<div class="card">
    <div class="card-header">
        <h5>Perfil Profesional:</h5>
    </div>
    <div class="card-body">
        @if($replacementStaff->profiles->count() > 0)
          <div class="table-responsive">
            <table class="table table-sm table-striped table-bordered">
                <thead class="text-center small">
                    <tr>
                        <th style="width: 11%">Fecha Registro</th>
                        <th>Estamento</th>
                        <th>Título</th>
                        <th>Experiencia</th>
                        <th>Fecha Titulación</th>
                        <th>Años Exp.</th>
                        <th style="width: 10%"></th>
                    </tr>
                </thead>
                <tbody class="small">
                    @foreach($replacementStaff->profiles as $profile)
                    <tr>
                        <td>{{ $profile->updated_at->format('d-m-Y H:i:s') }}</td>
                        <td>{{ $profile->profile_manage->name }}</td>
                        <td>{{ ($profile->profession_manage) ? $profile->profession_manage->name : ''  }}</td>
                        <td>{{ $profile->ExperienceValue }}</td>
                        <td align="center">{{ ($profile->degree_date) ? $profile->degree_date->format('d-m-Y') : '' }}</td>
                        <td align="center">{{ $profile->YearsOfDegree }}</td>
                        <td>
                            <a href="{{ route('replacement_staff.view_profile.show_file', $profile) }}" class="btn btn-outline-secondary btn-sm" title="Ir" target="_blank"> <i class="far fa-eye"></i></a>
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('replacement_staff.view_profile.download', $profile) }}" target="_blank"><i class="fas fa-download"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
          </div>
        @endif
    </div>
</div>

<br>

<div class="card">
    <div class="card-header">
        <h5>Perfeccionamiento / Capacitaciones</h5>
    </div>
    <div class="card-body">
        @if($replacementStaff->trainings->count() > 0)
          <div class="table-responsive">
            <table class="table table-sm table-striped table-bordered">
                <thead class="text-center small">
                    <tr>
                        <th style="width: 11%">Fecha Registro</th>
                        <th>Nombre de Capacitación</th>
                        <th>N° de Horas Realizadas</th>
                        <th>Archivo</th>
                        <th style="width: 10%"></th>
                    </tr>
                </thead>
                <tbody class="small">
                    @foreach($replacementStaff->trainings as $training)
                    <tr>
                        <td>{{ $training->updated_at->format('d-m-Y H:i:s') }}</td>
                        <td>{{ $training->training_name }}</td>
                        <td class="text-center">{{ $training->hours_training }}</td>
                        <td class="text-center">
                            @if(pathinfo($training->file, PATHINFO_EXTENSION) == 'pdf')
                            <i class="fas fa-file-pdf fa-2x"></i>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('replacement_staff.view_training.show_file', $training) }}" class="btn btn-outline-secondary btn-sm" title="Ir" target="_blank"> <i class="far fa-eye"></i></a>
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('replacement_staff.view_training.download', $training) }}" target="_blank"><i class="fas fa-download"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
          </div>
        @endif
    </div>
</div>

@endsection

@section('custom_js')

@endsection
