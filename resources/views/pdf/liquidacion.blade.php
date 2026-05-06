<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Liquidación {{ $liquidacion->periodo_label }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 9px; color: #333; }

        .copia { padding: 12px 16px 8px; }
        .copia-label { display: inline-block; background: #1e40af; color: #fff; font-size: 8px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; padding: 2px 10px; border-radius: 3px; margin-bottom: 8px; }

        .header { display: table; width: 100%; margin-bottom: 10px; border-bottom: 2px solid #1e40af; padding-bottom: 8px; }
        .header-left { display: table-cell; width: 60%; vertical-align: top; }
        .header-right { display: table-cell; width: 40%; text-align: right; vertical-align: top; }
        .empresa-nombre { font-size: 15px; font-weight: bold; color: #1e40af; }
        .empresa-sub { font-size: 8px; color: #666; margin-top: 2px; }
        .doc-titulo { font-size: 12px; font-weight: bold; color: #1e40af; }
        .doc-numero { font-size: 8px; color: #666; margin-top: 2px; }
        .doc-fecha { font-size: 8px; color: #333; }

        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 8px; font-weight: bold; }
        .badge-emitida { background: #fef9c3; color: #854d0e; }
        .badge-pagada { background: #dcfce7; color: #166534; }

        .two-col { display: table; width: 100%; margin-bottom: 8px; }
        .col { display: table-cell; width: 50%; vertical-align: top; }
        .col:first-child { padding-right: 10px; }

        .section-title { font-size: 8px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; color: #6b7280; border-bottom: 1px solid #e5e7eb; padding-bottom: 2px; margin-bottom: 4px; }
        .info-row { margin-bottom: 2px; }
        .info-label { font-weight: bold; color: #374151; }
        .periodo { font-size: 11px; font-weight: bold; color: #1e40af; margin-bottom: 8px; }

        .resumen-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 8px 12px; margin-bottom: 8px; }
        .resumen-row { display: table; width: 100%; padding: 2px 0; }
        .resumen-concepto { display: table-cell; color: #374151; }
        .resumen-monto { display: table-cell; text-align: right; font-weight: bold; color: #111827; }
        .resumen-row.deduccion .resumen-monto { color: #dc2626; }
        .resumen-row.neto { border-top: 2px solid #1e40af; margin-top: 4px; padding-top: 5px; }
        .resumen-row.neto .resumen-concepto { font-weight: bold; font-size: 11px; color: #1e40af; }
        .resumen-row.neto .resumen-monto { font-size: 13px; color: #1e40af; }

        table.detalle { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        table.detalle th { background: #1e40af; color: white; padding: 4px 6px; text-align: left; font-size: 8px; font-weight: bold; }
        table.detalle th.right { text-align: right; }
        table.detalle td { padding: 3px 6px; border-bottom: 1px solid #e5e7eb; font-size: 8px; }
        table.detalle td.right { text-align: right; }
        table.detalle tr:nth-child(even) td { background: #f9fafb; }
        table.detalle tr.subtotal td { font-weight: bold; background: #eff6ff; }

        .pagado-box { background: #dcfce7; border: 1px solid #86efac; border-radius: 4px; padding: 5px 10px; margin-bottom: 6px; color: #166534; font-weight: bold; }

        .firma-section { display: table; width: 100%; margin-top: 12px; }
        .firma-col { display: table-cell; width: 50%; text-align: center; padding: 0 15px; }
        .firma-linea { border-top: 1px solid #374151; padding-top: 3px; margin-top: 20px; font-size: 8px; color: #374151; }

        .footer { margin-top: 6px; text-align: center; font-size: 7px; color: #9ca3af; }

        .corte { border: none; border-top: 2px dashed #ccc; margin: 6px 0; font-size: 8px; color: #aaa; text-align: center; }
    </style>
</head>
<body>

@php $copias = ['ORIGINAL', 'DUPLICADO']; @endphp
@foreach ($copias as $i => $copia)

@if($i === 1)
<div class="corte">✂ &nbsp; CORTE &nbsp; ✂</div>
@endif

<div class="copia">
<span class="copia-label">{{ $copia }}</span>

    {{-- CABECERA --}}
    <div class="header">
        <div class="header-left">
            <div class="empresa-nombre">{{ $config->razon_social ?: $config->nombre }}</div>
            <div class="empresa-sub">{{ $config->direccion }}</div>
        </div>
        <div class="header-right">
            <div class="doc-titulo">LIQUIDACIÓN A PROPIETARIO</div>
            <div class="doc-numero">N° {{ str_pad($liquidacion->id, 6, '0', STR_PAD_LEFT) }}</div>
            <div class="doc-fecha">{{ $liquidacion->fecha_liquidacion->format('d/m/Y') }}</div>
            <div style="margin-top:4px;">
                <span class="badge badge-{{ $liquidacion->estado }}">{{ strtoupper($liquidacion->estado_label) }}</span>
            </div>
        </div>
    </div>

    {{-- PERÍODO --}}
    <div class="periodo">{{ $liquidacion->periodo_label }}</div>

    {{-- PROPIETARIO E INMUEBLE --}}
    <div class="two-col">
        <div class="col">
            <div class="section-title">Propietario</div>
            <div class="info-row" style="font-weight:bold; font-size:10px;">{{ $liquidacion->propietario->nombre_completo }}</div>
            <div class="info-row"><span class="info-label">DNI: </span>{{ $liquidacion->propietario->dni }}</div>
            @if($liquidacion->propietario->cuit)
            <div class="info-row"><span class="info-label">CUIT: </span>{{ $liquidacion->propietario->cuit }}</div>
            @endif
            @if($liquidacion->propietario->cbu)
            <div class="info-row"><span class="info-label">CBU: </span>{{ $liquidacion->propietario->cbu }}</div>
            @endif
        </div>
        <div class="col">
            <div class="section-title">Inmueble</div>
            <div class="info-row" style="font-weight:bold; font-size:10px;">{{ $liquidacion->propiedad->tipo_label }}</div>
            <div class="info-row">{{ $liquidacion->propiedad->direccion_completa }}</div>
            <div class="info-row"><span class="info-label">Inquilino: </span>{{ $liquidacion->contrato->inquilino->nombre_completo }}</div>
            <div class="info-row"><span class="info-label">Contrato: </span>{{ $liquidacion->contrato->fecha_inicio->format('d/m/Y') }} al {{ $liquidacion->contrato->fecha_fin->format('d/m/Y') }}</div>
        </div>
    </div>

    {{-- RESUMEN --}}
    <div class="resumen-box">
        <div class="resumen-row">
            <div class="resumen-concepto">Alquiler cobrado</div>
            <div class="resumen-monto">$ {{ number_format($liquidacion->monto_alquiler, 2, ',', '.') }}</div>
        </div>
        @if($gastos->count() > 0)
            @foreach($gastos as $gasto)
            <div class="resumen-row deduccion">
                <div class="resumen-concepto">Gastos deducibles — {{ $gasto->categoria_label }}</div>
                <div class="resumen-monto">- $ {{ number_format($gasto->monto, 2, ',', '.') }}</div>
            </div>
            @endforeach
        @elseif($liquidacion->total_gastos > 0)
        <div class="resumen-row deduccion">
            <div class="resumen-concepto">Gastos deducibles</div>
            <div class="resumen-monto">- $ {{ number_format($liquidacion->total_gastos, 2, ',', '.') }}</div>
        </div>
        @endif
        <div class="resumen-row deduccion">
            <div class="resumen-concepto">Comisión inmobiliaria @if($liquidacion->descuento_tipo !== 'valor')({{ number_format($liquidacion->comision_porcentaje, 1) }}%)@endif</div>
            <div class="resumen-monto">- $ {{ number_format($liquidacion->monto_comision, 2, ',', '.') }}</div>
        </div>
        <div class="resumen-row neto">
            <div class="resumen-concepto">NETO A COBRAR</div>
            <div class="resumen-monto">$ {{ number_format($liquidacion->monto_neto, 2, ',', '.') }}</div>
        </div>
    </div>


    {{-- OBSERVACIONES --}}
    @if($liquidacion->observaciones)
    <div style="background:#fffbeb; border-left:3px solid #f59e0b; padding:5px 10px; margin-bottom:6px; font-size:8px;">
        <strong>Observaciones:</strong> {{ $liquidacion->observaciones }}
    </div>
    @endif

    {{-- ESTADO PAGO --}}
    @if($liquidacion->estado === 'pagada')
    <div class="pagado-box">
        ✓ PAGADO al propietario el {{ $liquidacion->fecha_pago_propietario->format('d/m/Y') }}
        @if($liquidacion->medio_pago) — {{ ucfirst($liquidacion->medio_pago) }} @endif
    </div>
    @endif

    {{-- FIRMAS --}}
    <div class="firma-section">
        <div class="firma-col">
            <div class="firma-linea">{{ $config->razon_social ?: $config->nombre }}</div>
        </div>
        <div class="firma-col">
            <div class="firma-linea">Firma Propietario — {{ $liquidacion->propietario->nombre_completo }}</div>
        </div>
    </div>

    <div class="footer">{{ $copia }} — Generado el {{ now()->format('d/m/Y H:i') }} hs.</div>

</div>
@endforeach

</body>
</html>
