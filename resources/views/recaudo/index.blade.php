@extends('layouts.app')

@section('title', 'Recaudo')
@section('header', 'Nuevo recibo de pago')

@push('head')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .receipt-preview {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        overflow: hidden;
        font-family: 'Courier New', Courier, monospace;
    }
    .receipt-header {
        background-color: #2b3a67;
        color: white;
        padding: 20px;
        position: relative;
    }
    .dashed-line {
        border-bottom: 2px dashed #ccc;
        margin: 15px 0;
    }
    .stamp {
        border: 2px solid #ddd;
        border-radius: 50%;
        color: #ddd;
        font-size: 14px;
        font-weight: bold;
        padding: 10px;
        transform: rotate(-15deg);
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        bottom: 40px;
        right: 40px;
    }
    .select2-container .select2-selection--single {
        height: 38px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-md-7">
        <div class="card">
            <div class="card-body">
                <p class="text-muted">Registra un pago, autocompleta la cantidad en letras y previsualiza el recibo en tiempo real.</p>
                <form action="{{ route('recaudo.store') }}" method="POST" id="recaudoForm">
                    @csrf
                    <input type="hidden" name="numero_recibo" id="input_numero_recibo" value="{{ $nextRecibo }}">
                    
                    <div class="d-flex mb-4">
                        <button type="submit" class="btn btn-primary mr-2"><i class="fas fa-save"></i> Guardar</button>
                        <button type="reset" class="btn btn-light border mr-2"><i class="fas fa-eraser"></i> Limpiar</button>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>FECHA</label>
                            <input type="date" class="form-control" name="fecha" id="fecha" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-8 form-group">
                            <label>RECIBIMOS DEL SEÑOR(A)</label>
                            <select class="form-control select2" name="user_id" id="user_id" required>
                                <option value="">Escribe un nombre o documento...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" data-name="{{ $user->name }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>VALOR (COP)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                                <input type="number" class="form-control" name="valor" id="valor" value="0" min="0" step="any" required>
                            </div>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>CANTIDAD</label>
                            <input type="number" class="form-control" name="cantidad" id="cantidad" value="1" min="1" step="any" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>VALOR PARCIAL</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                                <input type="text" class="form-control" id="valor_parcial_display" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>CANTIDAD EN LETRAS</label>
                        <input type="text" class="form-control bg-light" id="cantidad_letras" readonly placeholder="Se completará al ingresar el valor parcial">
                    </div>

                    <div class="form-group">
                        <label>CONCEPTO</label>
                        <textarea class="form-control" name="concepto" id="concepto" rows="2" placeholder="Ej. Pago anticipado"></textarea>
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-concepto">Pago de arrendamiento mes</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-concepto">Abono a factura</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-concepto">Pago de servicio</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-concepto">Pago anticipado</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header bg-light">
                <h3 class="card-title font-weight-bold">RECIBOS RECIENTES</h3>
                <span class="badge badge-secondary float-right">{{ $recaudos->count() }} registros</span>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>PAGADOR / CONCEPTO</th>
                            <th>FECHA</th>
                            <th>VALOR</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recaudos as $rec)
                        <tr>
                            <td>#{{ $rec->numero_recibo }}</td>
                            <td>
                                <strong>{{ $rec->user->name ?? 'Usuario Desconocido' }}</strong><br>
                                <small class="text-muted">{{ Str::limit($rec->concepto, 50) }}</small>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($rec->fecha)->format('d/m') }}</td>
                            <td>${{ number_format($rec->valor_parcial, 0, ',', '.') }}</td>
                            <td>
                                <form action="{{ route('recaudo.destroy', $rec->id) }}" method="POST" onsubmit="return confirm('¿Eliminar este recibo?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No hay recibos recientes.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Live Preview -->
    <div class="col-md-5">
        <div class="card bg-light">
            <div class="card-header border-0 d-flex justify-content-between align-items-center">
                <span class="text-success"><i class="fas fa-circle"></i> VISTA PREVIA EN VIVO</span>
            </div>
            <div class="card-body">
                <div class="receipt-preview">
                    <div class="receipt-header d-flex justify-content-between">
                        <div>
                            <small class="d-block mb-1">ENTIDAD CORPORATIVA S.A.S.</small>
                            <h4 class="mb-0 font-weight-bold">Recibo de Pago</h4>
                        </div>
                        <div class="text-right">
                            <small class="d-block mb-1">N°</small>
                            <h4 class="mb-0 font-weight-bold" id="prev_numero">{{ $nextRecibo }}</h4>
                        </div>
                    </div>
                    <div class="p-4" style="position: relative;">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted text-sm">FECHA</span>
                            <strong id="prev_fecha">{{ date('d/m/Y') }}</strong>
                        </div>
                        <div class="dashed-line"></div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted text-sm" style="width:150px;">RECIBIMOS DE</span>
                            <div class="text-right">
                                <strong id="prev_nombre">Nombre del pagador</strong>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-3 bg-light p-3 rounded">
                            <span class="text-muted font-weight-bold">VALOR TOTAL</span>
                            <h4 class="mb-0 font-weight-bold text-primary" id="prev_valor">$ 0</h4>
                        </div>
                        
                        <div class="mb-3 mt-4">
                            <span class="text-muted text-sm d-block mb-1">LA SUMA DE</span>
                            <strong id="prev_letras" style="font-style: italic;">...</strong>
                        </div>
                        
                        <div class="mb-5">
                            <span class="text-muted text-sm d-block mb-1">POR CONCEPTO DE</span>
                            <span id="prev_concepto">...</span>
                        </div>
                        
                        <div class="mt-5 pt-3" style="border-top: 1px solid #ddd; width: 60%; text-align: center;">
                            <small class="text-muted">FIRMA - RECIBÍ CONFORME</small>
                        </div>
                        
                        <div class="stamp">BORRADOR</div>
                    </div>
                </div>
                
                <div class="text-right mt-3">
                    <button class="btn btn-sm btn-outline-secondary" onclick="window.print()"><i class="fas fa-print"></i> Imprimir</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Escribe un nombre o NIT...",
            allowClear: true
        });

        // Simple function to convert numbers to words (spanish)
        const Unidades = (num) => {
            switch(num)
            {
                case 1: return 'UN';
                case 2: return 'DOS';
                case 3: return 'TRES';
                case 4: return 'CUATRO';
                case 5: return 'CINCO';
                case 6: return 'SEIS';
                case 7: return 'SIETE';
                case 8: return 'OCHO';
                case 9: return 'NUEVE';
            }
            return '';
        }

        const Decenas = (num) => {
            let decena = Math.floor(num/10);
            let unidad = num - (decena * 10);
            switch(decena)
            {
                case 1:
                    switch(unidad)
                    {
                        case 0: return 'DIEZ';
                        case 1: return 'ONCE';
                        case 2: return 'DOCE';
                        case 3: return 'TRECE';
                        case 4: return 'CATORCE';
                        case 5: return 'QUINCE';
                        default: return 'DIECI' + Unidades(unidad);
                    }
                case 2:
                    switch(unidad)
                    {
                        case 0: return 'VEINTE';
                        default: return 'VEINTI' + Unidades(unidad);
                    }
                case 3: return DecenasY('TREINTA', unidad);
                case 4: return DecenasY('CUARENTA', unidad);
                case 5: return DecenasY('CINCUENTA', unidad);
                case 6: return DecenasY('SESENTA', unidad);
                case 7: return DecenasY('SETENTA', unidad);
                case 8: return DecenasY('OCHENTA', unidad);
                case 9: return DecenasY('NOVENTA', unidad);
                case 0: return Unidades(unidad);
            }
        }

        const DecenasY = (strSin, numUnidades) => {
            if (numUnidades > 0) return strSin + ' Y ' + Unidades(numUnidades)
            return strSin;
        }

        const Centenas = (num) => {
            let centenas = Math.floor(num / 100);
            let decenas = num - (centenas * 100);
            switch(centenas)
            {
                case 1:
                    if (decenas > 0) return 'CIENTO ' + Decenas(decenas);
                    return 'CIEN';
                case 2: return 'DOSCIENTOS ' + Decenas(decenas);
                case 3: return 'TRESCIENTOS ' + Decenas(decenas);
                case 4: return 'CUATROCIENTOS ' + Decenas(decenas);
                case 5: return 'QUINIENTOS ' + Decenas(decenas);
                case 6: return 'SEISCIENTOS ' + Decenas(decenas);
                case 7: return 'SETECIENTOS ' + Decenas(decenas);
                case 8: return 'OCHOCIENTOS ' + Decenas(decenas);
                case 9: return 'NOVECIENTOS ' + Decenas(decenas);
            }
            return Decenas(decenas);
        }

        const Seccion = (num, divisor, strSingular, strPlural) => {
            let cientos = Math.floor(num / divisor)
            let resto = num - (cientos * divisor)
            let letras = '';
            if (cientos > 0)
                if (cientos > 1) letras = Centenas(cientos) + ' ' + strPlural;
                else letras = strSingular;
            if (resto > 0) letras += '';
            return letras;
        }

        const Miles = (num) => {
            let divisor = 1000;
            let cientos = Math.floor(num / divisor)
            let resto = num - (cientos * divisor)
            let strMiles = Seccion(num, divisor, 'UN MIL', 'MIL');
            let strCentenas = Centenas(resto);
            if(strMiles == '') return strCentenas;
            return strMiles + ' ' + strCentenas;
        }

        const Millones = (num) => {
            let divisor = 1000000;
            let cientos = Math.floor(num / divisor)
            let resto = num - (cientos * divisor)
            let strMillones = Seccion(num, divisor, 'UN MILLON DE', 'MILLONES DE');
            let strMiles = Miles(resto);
            if(strMillones == '') return strMiles;
            return strMillones + ' ' + strMiles;
        }

        const numeroALetras = (num) => {
            var data = { num: num, enteros: Math.floor(num), letrasMonedaPlural: 'PESOS', letrasMonedaSingular: 'PESO' };
            if (data.enteros == 0) return 'CERO ' + data.letrasMonedaPlural;
            if (data.enteros == 1) return Millones(data.enteros) + ' ' + data.letrasMonedaSingular;
            else return Millones(data.enteros) + ' ' + data.letrasMonedaPlural;
        }

        function updatePreview() {
            const fecha = $('#fecha').val();
            if(fecha) {
                const parts = fecha.split('-');
                $('#prev_fecha').text(`${parts[2]}/${parts[1]}/${parts[0]}`);
            }

            const nombre = $('#user_id option:selected').data('name');
            $('#prev_nombre').text(nombre ? nombre : 'Nombre del pagador');

            const valor = parseFloat($('#valor').val()) || 0;
            const cantidad = parseFloat($('#cantidad').val()) || 1;
            const valorParcial = valor * cantidad;
            
            $('#valor_parcial_display').val(valorParcial.toLocaleString('es-CO'));
            $('#prev_valor').text('$ ' + valorParcial.toLocaleString('es-CO'));
            
            if (valorParcial > 0) {
                const letras = numeroALetras(valorParcial);
                $('#cantidad_letras').val(letras);
                $('#prev_letras').text(letras);
            } else {
                $('#cantidad_letras').val('');
                $('#prev_letras').text('...');
            }

            const concepto = $('#concepto').val();
            $('#prev_concepto').text(concepto ? concepto : '...');
        }

        $('#fecha, #user_id, #valor, #cantidad, #concepto').on('change input', updatePreview);

        $('.btn-concepto').click(function() {
            $('#concepto').val($(this).text());
            updatePreview();
        });

        updatePreview();
    });
</script>
@endpush
