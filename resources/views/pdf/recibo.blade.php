<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; color: #1a1a2e; background: #fff; }

    .copia { padding: 18px 32px; }

    .copia-label { display: inline-block; background: #1e40af; color: #fff; font-size: 8px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; padding: 2px 10px; border-radius: 3px; margin-bottom: 10px; }

    /* Header */
    .header { display: table; width: 100%; border-bottom: 2px solid #1e40af; padding-bottom: 10px; margin-bottom: 14px; }
    .header-left { display: table-cell; vertical-align: middle; }
    .header-right { display: table-cell; text-align: right; vertical-align: middle; }
    .empresa-nombre { font-size: 16px; font-weight: bold; color: #1e40af; }
    .empresa-sub { font-size: 9px; color: #64748b; margin-top: 1px; }
    .recibo-titulo { font-size: 17px; font-weight: bold; color: #1e40af; }
    .recibo-numero { font-size: 11px; color: #64748b; margin-top: 1px; }

    /* Bloques info */
    .bloques { display: table; width: 100%; margin-bottom: 14px; }
    .bloque { display: table-cell; width: 50%; vertical-align: top; padding-right: 16px; }
    .bloque:last-child { padding-right: 0; padding-left: 16px; border-left: 1px solid #e2e8f0; }
    .bloque-titulo { font-size: 8px; font-weight: bold; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 5px; }
    .bloque-valor { font-size: 10px; color: #1e293b; line-height: 1.5; }
    .bloque-valor strong { color: #0f172a; }

    /* Detalle */
    .detalle-titulo { font-size: 8px; font-weight: bold; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 7px; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; }
    table.desglose { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    table.desglose td { padding: 5px 8px; font-size: 10px; }
    table.desglose tr:nth-child(odd) { background: #f8fafc; }
    .concepto { color: #475569; }
    .importe { text-align: right; font-weight: 500; }
    .separador { border-top: 2px solid #1e40af; }
    .total-row td { padding: 7px 8px; font-weight: bold; font-size: 12px; }
    .total-label { color: #1e40af; }
    .total-valor { text-align: right; color: #1e40af; }

    .badge-pagado { display: inline-block; background: #dcfce7; color: #166534; padding: 3px 12px; border-radius: 20px; font-size: 9px; font-weight: bold; letter-spacing: 0.5px; text-transform: uppercase; }

    .firmas { display: table; width: 100%; margin-top: 18px; border-top: 1px solid #e2e8f0; padding-top: 12px; }
    .firma { display: table-cell; text-align: center; width: 50%; }
    .firma-linea { border-top: 1px solid #94a3b8; margin: 0 24px 4px; }
    .firma-label { font-size: 8px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }

    .footer-copia { margin-top: 10px; text-align: center; font-size: 8px; color: #cbd5e1; }

    /* Línea de corte */
    .corte { text-align: center; font-size: 9px; color: #94a3b8; letter-spacing: 2px; padding: 4px 0; border-top: 1px dashed #cbd5e1; border-bottom: 1px dashed #cbd5e1; margin: 2px 0; }
</style>
</head>
<body>

@php
    $meses = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio',
              'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    $copias = ['ORIGINAL', 'DUPLICADO'];
@endphp

@foreach ($copias as $i => $copia)

@if ($i === 1)
<div class="corte">✂ &nbsp;&nbsp;&nbsp; CORTE &nbsp;&nbsp;&nbsp; ✂</div>
@endif

<div class="copia">
    <div><span class="copia-label">{{ $copia }}</span></div>

    <div class="header">
        <div class="header-left">
            <div class="empresa-nombre">{{ $config->razon_social ?: $config->nombre }}</div>
            <div class="empresa-sub">{{ $config->direccion }}</div>
        </div>
        <div class="header-right">
            <div class="recibo-titulo">RECIBO DE PAGO</div>
            <div class="recibo-numero">N° {{ str_pad($pago->id, 6, '0', STR_PAD_LEFT) }}</div>
        </div>
    </div>

    <div class="bloques">
        <div class="bloque">
            <div class="bloque-titulo">Inquilino</div>
            <div class="bloque-valor">
                <strong>{{ $pago->contrato->inquilino->nombre_completo }}</strong><br>
                DNI: {{ $pago->contrato->inquilino->dni }}<br>
                @if ($pago->contrato->inquilino->telefono)
                    Tel: +54 {{ $pago->contrato->inquilino->telefono }}<br>
                @endif
                @if ($pago->contrato->inquilino->email)
                    {{ $pago->contrato->inquilino->email }}
                @endif
            </div>
        </div>
        <div class="bloque">
            <div class="bloque-titulo">Propiedad</div>
            <div class="bloque-valor">
                <strong>{{ $pago->contrato->propiedad->tipo_label }}</strong><br>
                {{ $pago->contrato->propiedad->direccion_completa }}<br>
            </div>
        </div>
    </div>

    <div class="bloques" style="margin-bottom: 14px;">
        <div class="bloque">
            <div class="bloque-titulo">Período</div>
            <div class="bloque-valor">
                <strong>{{ $meses[$pago->periodo_mes] }} {{ $pago->periodo_anio }}</strong><br>
                Vencimiento: {{ $pago->fecha_vencimiento->format('d/m/Y') }}
            </div>
        </div>
        <div class="bloque">
            <div class="bloque-titulo">Fecha de pago</div>
            <div class="bloque-valor">
                <strong>{{ $pago->fecha_pago ? $pago->fecha_pago->format('d/m/Y') : now()->format('d/m/Y') }}</strong><br>
                Medio: {{ $pago->medio_pago_label }}<br>
                @if ($pago->numero_comprobante)
                    Comprobante: {{ $pago->numero_comprobante }}
                @endif
            </div>
        </div>
    </div>

    <div class="detalle-titulo">Detalle del importe</div>
    <table class="desglose">
        <tr>
            <td class="concepto">Alquiler — {{ $meses[$pago->periodo_mes] }} {{ $pago->periodo_anio }}</td>
            <td class="importe">$ {{ number_format($pago->monto, 2, ',', '.') }}</td>
        </tr>
        @if ($pago->recargo > 0)
        <tr>
            <td class="concepto">Recargo por mora</td>
            <td class="importe" style="color:#dc2626;">+ $ {{ number_format($pago->recargo, 2, ',', '.') }}</td>
        </tr>
        @endif
        @if ($pago->descuento > 0)
        <tr>
            <td class="concepto">Descuento{{ $pago->descuento_categoria ? ' — ' . $pago->descuento_categoria : '' }}</td>
            <td class="importe" style="color:#16a34a;">- $ {{ number_format($pago->descuento, 2, ',', '.') }}</td>
        </tr>
        @endif
        <tr class="separador"><td></td><td></td></tr>
        <tr class="total-row">
            <td class="total-label">TOTAL ABONADO</td>
            <td class="total-valor">$ {{ number_format($pago->total, 2, ',', '.') }}</td>
        </tr>
    </table>

    <div style="text-align:center; margin: 10px 0;">
        <span class="badge-pagado">&#10003; PAGO REGISTRADO</span>
    </div>

    @if ($pago->observaciones)
    <div style="background:#f8fafc; border-left:2px solid #94a3b8; padding:7px 10px; margin:8px 0; font-size:9px; color:#475569;">
        <strong>Obs:</strong> {{ $pago->observaciones }}
    </div>
    @endif

    <div class="firmas">
        <div class="firma">
            <div class="firma-linea"></div>
            <div class="firma-label">{{ $config->razon_social ?: $config->nombre }}</div>
        </div>
        <div class="firma">
            <div class="firma-linea"></div>
            <div class="firma-label">Firma Inquilino / Aclaración</div>
        </div>
    </div>

    <div class="footer-copia">
        Recibo N° {{ str_pad($pago->id, 6, '0', STR_PAD_LEFT) }} — {{ $copia }} — Emitido el {{ now()->format('d/m/Y H:i') }}
    </div>
</div>

@endforeach

</body>
</html>
