@extends('layouts.app')
@section('title', 'Formulario de requerimiento')
@section('content')

<link href="{{ asset('css/bootstrap-select.min.css') }}" rel="stylesheet" type="text/css"/>
<h4 class="mb-3">Compra</h4>

@include('request_form.partials.nav')

<div class="row">
    <div class="col-sm-8">
        <div class="table-responsive">
            <h6><i class="fas fa-info-circle"></i> Detalle Formulario</h6>
            <table class="table table-sm table-striped table-bordered">
                <!-- <thead>
                    <tr class="table-active">
                        <th colspan="2">Formulario Contratación de Personal </th>
                    </tr>
                </thead> -->
                <tbody class="small">
                    <tr>
                        <th class="table-active" scope="row">Fecha de Creación</th>
                        <td>{{ $requestForm->created_at->format('d-m-Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th class="table-active" style="width: 33%">Nombre</th>
                        <td>{{ $requestForm->name }}</td>
                    </tr>
                    <tr>
                        <th class="table-active" style="width: 33%">Gasto Estimado</th>
                        <td>${{ number_format($requestForm->estimated_expense,0,",",".") }}</td>
                    </tr>
                    <tr>
                        <th class="table-active" scope="row">Nombre del Solicitante</th>
                        <td>{{ $requestForm->user ? $requestForm->user->FullName : 'Usuario eliminado' }}</td>
                    </tr>
                    <tr>
                        <th class="table-active" scope="row">Unidad Organizacional</th>
                        <td>{{ $requestForm->user ? $requestForm->userOrganizationalUnit->name : 'Usuario eliminado' }}</td>
                    </tr>
                    <tr>
                        <th class="table-active" scope="row">Adminitrador de Contrato</th>
                        <td>{{ $requestForm->contractManager ? $requestForm->contractManager->FullName : 'Usuario eliminado' }}</td>
                    </tr>
                    <tr>
                        <th class="table-active" scope="row">Mecanismo de Compra</th>
                        <td>{{ $requestForm->getPurchaseMechanism()}}</td>
                    </tr>
                    <tr>
                        <th class="table-active" scope="row">Tipo de Compra</th>
                        <td>{{ $requestForm->purchaseType->name }}</td>
                    </tr>
                    <tr>
                        <th class="table-active" scope="row">Unidad de Compra</th>
                        <td>{{ $requestForm->purchaseUnit->name  }}</td>
                    </tr>
                    <tr>
                        <th class="table-active" scope="row">Programa Asociado</th>
                        <td>{{ $requestForm->program }}</td>
                    </tr>
                    <tr>
                        <th class="table-active" scope="row">Folio SIGFE</th>
                        <td>{{ $requestForm->sigfe }}</td>
                    </tr>
                    <tr>
                        <th class="table-active" scope="row">Justificación de Adquisición</th>
                        <td>{{ $requestForm->justification }}</td>
                    </tr>

                </tbody>
            </table>
        </div>

        <div class="float-right">
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#exampleModal">
                Editar Mecanismo de Compra
            </button>

            @include('request_form.purchase.modals.select_purchase_mechanism')

            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#requestBudget" @if($isBudgetEventSignPending) disabled @endif >
                Solicitar presupuesto
            </button>

            @include('request_form.purchase.modals.request_new_budget')
        </div>
    </div>
    <div class="col-sm-4">
        <h6><i class="fas fa-paperclip"></i> Adjuntos</h6>
        <div class="list-group">
            @foreach($requestForm->requestFormFiles as $requestFormFile)
              <a href="{{ route('request_forms.show_file', $requestFormFile) }}" class="list-group-item list-group-item-action py-2 small" target="_blank">
                <i class="fas fa-file"></i> {{ $requestFormFile->name }} -
                <i class="fas fa-calendar-day"></i> {{ $requestFormFile->created_at->format('d-m-Y H:i') }}</a>
            @endforeach
        </div>
    </div>
</div>

<br>

<div class="table-responsive">
    <h6><i class="fas fa-signature"></i> Proceso de Firmas</h6>
    <table class="table table-sm table-striped table-bordered">
        <tbody class="text-center small">
            <tr>
              @foreach($requestForm->eventRequestForms as $event)
                <th>{{ $event->signerOrganizationalUnit->name }}</th>
              @endforeach
            </tr>
            <tr>
              @foreach($requestForm->eventRequestForms as $event)
                <td>
                  @if($event->StatusValue == 'Pendiente')
                    <span>
                      <i class="fas fa-clock"></i> {{ $event->StatusValue }} <br>
                    </span>
                  @endif
                  @if($event->StatusValue == 'Aprobado')
                    <span style="color: green;">
                      <i class="fas fa-check-circle"></i> {{ $event->StatusValue }} <br>
                    </span>
                    <i class="fas fa-user"></i> {{ $event->signerUser->FullName }}<br>
                    <i class="fas fa-calendar-alt"></i> {{ Carbon\Carbon::parse($event->signature_date)->format('d-m-Y H:i:s') }}<br>
                  @endif
                  @if($event->StatusValue == 'Rechazado')
                    <span style="color: Tomato;">
                      <i class="fas fa-times-circle"></i> {{ $event->StatusValue }} <br>
                    </span>
                    <i class="fas fa-user"></i> {{ $event->signerUser->FullName }}<br>
                    <i class="fas fa-calendar-alt"></i> {{ Carbon\Carbon::parse($event->signature_date)->format('d-m-Y H:i:s') }}<br>
                  @endif
                </td>
              @endforeach
            </tr>
        </tbody>
    </table>
</div>

<br>

<div class="row">
    <div class="col-sm">
        <div class="table-responsive">
            <h6><i class="fas fa-shopping-cart"></i> Lista de Bienes y/o Servicios:</h6>
            @if($requestForm->purchase_mechanism_id == 1)
                @if($requestForm->purchase_type_id == 1)
                <form method="POST" class="form-horizontal" action="{{ route('request_forms.supply.create_petty_cash', $requestForm) }}" enctype="multipart/form-data">
                @endif
                @if($requestForm->purchase_type_id == 2)
                <form method="POST" class="form-horizontal" action="{{ route('request_forms.supply.create_internal_oc', $requestForm) }}">
                @endif
                @if($requestForm->purchase_type_id == 3)
                <form method="POST" class="form-horizontal" action="{{ route('request_forms.supply.create_fund_to_be_settled', $requestForm) }}">
                @endif
            @endif

            @if($requestForm->purchase_mechanism_id == 4 && $requestForm->purchase_type_id == 12)
            <form method="POST" class="form-horizontal" action="{{ route('request_forms.supply.create_fund_to_be_settled', $requestForm) }}">
            @endif

            @csrf
            @method('POST')

            <table class="table table-sm table-striped table-bordered small">
                <thead class="text-center">
                    <tr>
                        <th>Item</th>
                        <th>Estado</th>
                        <th>Cod.Presup.</th>
                        <th>Artículo</th>
                        <th>UM</th>
                        <th>Especificaciones Técnicas</th>
                        <th>Archivo</th>
                        <th>Cantidad</th>
                        <th>Valor U.</th>
                        <th>Impuestos</th>
                        <th>Total Item</th>
                        <th colspan="2"></th>
                        <!-- <th></th> -->
                    </tr>
                </thead>
                <tbody>
                    @foreach($requestForm->itemRequestForms as $key => $item)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ $item->status }}</td>
                        <td>{{ $item->budgetItem()->first()->fullName() }}</td>
                        <td>{{ $item->article }}</td>
                        <td>{{ $item->unit_of_measurement }}</td>
                        <td>{{ $item->specification }}</td>
                        <td align="center">
                            @if($item->article_file)
                            <a href="{{ route('request_forms.show_item_file', $item) }}" target="_blank">
                              <i class="fas fa-file"></i></a>
                            @endif
                        </td>
                        <td align="right">
                          <input type="number" class="form-control form-control-sm text-right" step="0.01" min="0.1" id="for_quantity" name="quantity[]"
                              value="{{ old('quantity.'.$key, $item->quantity) }}">
                        </td>
                        <td align="right">
                          <input type="number" class="form-control form-control-sm text-right" step="0.01" min="1" id="for_unit_value" name="unit_value[]"
                              value="{{ old('unit_value.'.$key, $item->unit_value) }}">
                        </td>
                        <td align="right">
                          <input type="text" class="form-control form-control-sm text-right" id="for_tax" name="tax[]"
                              value="{{ $item->tax }}" readonly>
                        </td>
                        <td align="right">
                          <input type="number" class="form-control form-control-sm text-right" step="0.01" min="1" id="for_item_total" name="item_total[]"
                              value="{{ old('item_total.'.$key, $item->expense) }}" readonly>
                        </td>
                        <td align="center">
                            <fieldset class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="item_id[{{$key}}]" onclick="disabledSaveBtn()"
                                        id="for_item_id" value="{{ $item->id }}" {{ $item->id == old('item_id.'.$key, '') ? 'checked' : '' }} @if($isBudgetEventSignPending) disabled @endif>
                                </div>
                            </fieldset>
                        </td>
                        <td align="center">
                            <a href="">
                              <span style="color: Tomato;">
                                <i class="fas fa-times-circle"></i>
                              </span>
                            </a>
                        </td>
                    </tr>
                  @endforeach
                </tbody>
                <tfoot>
                    <tr>
                      <td colspan="9"></td>
                      <td class="text-right">Valor Total</td>
                      <td align="right">
                          <input type="number" step="0.01" min="1" class="form-control form-control-sm text-right" id="total_amount" name="total_amount" readonly>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<br>

<!-- Menores a 3 UTM -->
@if($requestForm->purchase_mechanism_id == 1)
    @if($requestForm->purchase_type_id == 1)
    @include('request_form.purchase.partials.petty_cash_form')
    @endif

    @if($requestForm->purchase_type_id == 2)
    @include('request_form.purchase.partials.internal_purchase_order_form')
    @endif

    @if($requestForm->purchase_type_id == 3)
    @include('request_form.purchase.partials.fund_to_be_settled_form')
    @endif

@endif

<!-- LICITACIÓN PUBLICA -->
@if($requestForm->purchase_mechanism_id == 4)
    @include('request_form.purchase.partials.tender_form')
@endif

<br>

@if($requestForm->purchasingProcess && $requestForm->purchasingProcess->details->count() > 0)

<div class="row">
    <div class="col-sm">
        <div class="table-responsive">
            <h6><i class="fas fa-shopping-cart"></i> {{ $requestForm->purchaseUnit->name }} registradas al Proceso de Compra:</h6>

            <table class="table table-sm table-striped table-bordered small">
                <thead class="text-center">
                    <tr>
                        <th>Item</th>
                        <th>Fecha</th>
                        <!-- <th>Mecanismo de Compra</th> -->
                        <th>Tipo de compra</th>
                        <th>Cod.Presup.</th>
                        <th>Artículo</th>
                        <th>UM</th>
                        <th>Especificaciones Técnicas</th>
                        <th>Archivo</th>
                        <th>Cantidad</th>
                        <th>Valor U.</th>
                        <th>Impuestos</th>
                        <th>Total Item</th>
                        <th></th>
                        <!-- <th></th>  -->
                    </tr>
                </thead>
                <tbody>
                    @foreach($requestForm->purchasingProcess->details as $key => $detail)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ $requestForm->purchasingProcess->start_date }}</td>
                        <!-- <td>{{ $requestForm->purchasingProcess->purchaseMechanism->name }}</td> -->
                        <td>{{ $detail->pivot->getPurchasingTypeName() }}</td>
                        <td>{{ $detail->budgetItem->fullName() ?? '' }}</td>
                        <td>{{ $detail->article }}</td>
                        <td>{{ $detail->unit_of_measurement }}</td>
                        <td>{{ $detail->specification }}</td>
                        <td align="center">
                            @if($detail->article_file)
                            <a href="{{ route('request_forms.show_item_file', $detail) }}" target="_blank">
                              <i class="fas fa-file"></i></a>
                            @endif
                        </td>
                        <td align="right">{{ $detail->pivot->quantity }}</td>
                        <td align="right">${{ number_format($detail->pivot->unit_value,0,",",".") }}</td>
                        <td>{{ $detail->tax }}</td>
                        <td align="right">${{ number_format($detail->pivot->expense,0,",",".") }}</td>
                        <!-- <td align="center">
                            <fieldset class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="item_id[]" onclick="disabledSaveBtn()"
                                      id="for_item_id" value="{{ $item->id }}">
                                </div>
                            </fieldset>
                        </td> -->
                        <td>
                        <button type="button" id="btn_items_{{$key}}" class="btn btn-link btn-sm" data-toggle="modal" data-target="#Receipt-{{$detail->pivot->id}}">
                            <i class="fas fa-receipt"></i>
                        </button>
                        @include('request_form.purchase.modals.detail_purchase')

                        </td>
                    </tr>
                  @endforeach
                </tbody>
                <tfoot>
                    <tr>
                      <td colspan="10"></td>
                      <th class="text-right">Valor Total</td>
                      <th class="text-right">${{ number_format($requestForm->purchasingProcess->getExpense(),0,",",".") }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endif

<br><br><br>


@endsection

@section('custom_js')

<script type="text/javascript">

var withholding_tax = {2021: 0.115, 2022: 0.1225, 2023: 0.13, 2024: 0.1375, 2025: 0.145, 2026: 0.1525, 2027: 0.16, 2028: 0.17}
var year = new Date().getFullYear();

calculateAmount();

function totalValueWithTaxes(value, tax){
    if(tax == 'iva') return value * 1.19;
    if(tax == 'bh') return withholding_tax[year] ? Math.round(value / (1 - withholding_tax[year])) : Math.round(value / (1 - withholding_tax[Object.keys(withholding_tax).pop()]));
    return value;
}

$('#for_quantity,#for_unit_value').on('change keyup',function(){
    var tr    = $(this).closest('tr')
    var qty   = tr.find('input[name="quantity[]"]')
    var price = tr.find('input[name="unit_value[]"]')
    var tax   = tr.find('input[name="tax[]"]')
    var total = tr.find('input[name="item_total[]"]')
    var grand_total = $('#total_amount')

    total.val(Math.round(qty.val() * totalValueWithTaxes(price.val(), tax.val())));

    var grandTotal=0;
    $('table').find('input[name="item_total[]"]').each(function(){
        if(!isNaN($(this).val()))
            grandTotal += parseInt($(this).val());
    });

    if(isNaN(grandTotal))
        grandTotal = 0;
    grand_total.val(grandTotal)

    calculateAmount(true)
});

document.getElementById("save_btn").disabled = {{ old('_token') === null ? 'true' : 'false' }}

function disabledSaveBtn() {
    // Get the checkbox
    var checkBox = document.getElementById("for_applicant_id");
    // If the checkbox is checked, display the output text
    if (document.querySelectorAll('input[type="checkbox"]:checked').length > 0){
        document.getElementById("save_btn").disabled = false;
        calculateAmount(true);
    } else {
        document.getElementById("save_btn").disabled = true;
        calculateAmount(true);
    }
}

function calculateAmount(checked = false) {
    var total = 0;
    $('input[type="checkbox"]' + (checked ? ':checked' : '')).each(function(){
        var val = Math.round($(this).parents("tr").find('input[name="item_total[]"]').val());
        total += val;
    });

    $(checked ? '#for_amount' : '#total_amount').val(total);
}
</script>

@endsection

@section('custom_js_head')

@endsection
