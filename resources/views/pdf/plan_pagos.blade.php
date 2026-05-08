<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; color: #1a1a2e; background: #fff; }

    .copia { padding: 18px 32px; }

    .copia-label { display: inline-block; background: #1e40af; color: #fff; font-size: 8px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; padding: 2px 10px; border-radius: 3px; margin-bottom: 10px; }

    .header { display: table; width: 100%; border-bottom: 2px solid #1e40af; padding-bottom: 10px; margin-bottom: 14px; }
    .header-left { display: table-cell; vertical-align: middle; }
    .header-right { display: table-cell; text-align: right; vertical-align: middle; }
    .empresa-nombre { font-size: 16px; font-weight: bold; color: #1e40af; }
    .empresa-sub { font-size: 9px; color: #64748b; margin-top: 1px; }
    .doc-titulo { font-size: 17px; font-weight: bold; color: #1e40af; }
    .doc-sub { font-size: 9px; color: #64748b; margin-top: 1px; }

    .bloques { display: table; width: 100%; margin-bottom: 12px; }
    .bloque { display: table-cell; width: 50%; vertical-align: top; padding-right: 16px; }
    .bloque:last-child { padding-right: 0; padding-left: 16px; border-left: 1px solid #e2e8f0; }
    .bloque-titulo { font-size: 8px; font-weight: bold; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 5px; }
    .bloque-valor { font-size: 10px; color: #1e293b; line-height: 1.6; }
    .bloque-valor strong { color: #0f172a; }

    .seccion-titulo { font-size: 8px; font-weight: bold; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 7px; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; }

    table.plan { width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 9.5px; }
    table.plan thead tr { background: #1e40af; color: #fff; }
    table.plan thead th { padding: 6px 8px; text-align: left; font-size: 8px; font-weight: bold; letter-spacing: 0.5px; text-transform: uppercase; }
    table.plan thead th.right { text-align: right; }
    table.plan thead th.center { text-align: center; }
    table.plan tbody tr:nth-child(even) { background: #f8fafc; }
    table.plan tbody td { padding: 5px 8px; color: #374151; border-bottom: 1px solid #f1f5f9; }
    table.plan tbody td.right { text-align: right; }
    table.plan tbody td.center { text-align: center; }
    table.plan tbody td.muted { color: #94a3b8; font-size: 9px; }

    .badge { display: inline-block; padding: 2px 7px; border-radius: 10px; font-size: 8px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.3px; }
    .badge-pagado   { background: #dcfce7; color: #166534; }
    .badge-pendiente{ background: #fef9c3; color: #854d0e; }
    .badge-vencido  { background: #fee2e2; color: #991b1b; }

    .resumen { display: table; width: 100%; margin-top: 4px; margin-bottom: 10px; }
    .resumen-item { display: table-cell; text-align: center; padding: 8px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; }
    .resumen-item + .resumen-item { border-left: none; }
    .resumen-num { font-size: 14px; font-weight: bold; color: #1e40af; }
    .resumen-label { font-size: 8px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 2px; }

    .firmas { display: table; width: 100%; margin-top: 14px; border-top: 1px solid #e2e8f0; padding-top: 12px; }
    .firma { display: table-cell; text-align: center; width: 50%; }
    .firma-linea { border-top: 1px solid #94a3b8; margin: 0 24px 4px; }
    .firma-label { font-size: 8px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }

    .footer-copia { margin-top: 8px; text-align: center; font-size: 8px; color: #cbd5e1; }

    .corte { text-align: center; font-size: 9px; color: #94a3b8; letter-spacing: 2px; padding: 4px 0; border-top: 1px dashed #cbd5e1; border-bottom: 1px dashed #cbd5e1; margin: 2px 0; }

    .paid-date { font-size: 8px; color: #6b7280; }
</style>
</head>
<body>

@php
    $meses = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio',
              'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    $copias = ['ORIGINAL', 'DUPLICADO'];
    $pagados   = $contrato->pagos->where('estado', 'pagado')->count();
    $pendientes = $contrato->pagos->where('estado', 'pendiente')->count();
    $vencidos  = $contrato->pagos->where('estado', 'vencido')->count();
    $total     = $contrato->pagos->count();
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
            <div class="doc-titulo">PLAN DE PAGOS</div>
            <div class="doc-sub">Contrato N° {{ str_pad($contrato->id, 6, '0', STR_PAD_LEFT) }}</div>
        </div>
    </div>

    <div class="bloques">
        <div class="bloque">
            <div class="bloque-titulo">Inmueble</div>
            <div class="bloque-valor">
                <strong>{{ $contrato->propiedad->tipo_label }}</strong><br>
                {{ $contrato->propiedad->direccion_completa }}<br>
                @if ($contrato->propiedad->propietario)
                    Propietario: {{ $contrato->propiedad->propietario->nombre_completo }}
                @endif
            </div>
        </div>
        <div class="bloque">
            <div class="bloque-titulo">Inquilino</div>
            <div class="bloque-valor">
                <strong>{{ $contrato->inquilino->nombre_completo }}</strong><br>
                DNI: {{ $contrato->inquilino->dni }}<br>
                @if ($contrato->inquilino->telefono)
                    Tel: +54 {{ $contrato->inquilino->telefono }}
                @endif
            </div>
        </div>
    </div>

    <div class="bloques" style="margin-bottom: 12px;">
        <div class="bloque">
            <div class="bloque-titulo">Período del contrato</div>
            <div class="bloque-valor">
                {{ $contrato->fecha_inicio->format('d/m/Y') }} al {{ $contrato->fecha_fin->format('d/m/Y') }}<br>
                Vencimiento: día <strong>{{ $contrato->dia_vencimiento }}</strong> de cada mes
            </div>
        </div>
        <div class="bloque">
            <div class="bloque-titulo">Alquiler mensual</div>
            <div class="bloque-valor">
                <strong style="font-size:13px;">{{ $contrato->moneda === 'USD' ? 'U$S' : '$' }} {{ number_format($contrato->monto_alquiler, 0, ',', '.') }}</strong><br>
                @if ($contrato->deposito_garantia)
                    Depósito de garantía: {{ $contrato->moneda === 'USD' ? 'U$S' : '$' }} {{ number_format($contrato->deposito_garantia, 0, ',', '.') }}
                @endif
            </div>
        </div>
    </div>

    {{-- Resumen --}}
    <div class="resumen">
        <div class="resumen-item">
            <div class="resumen-num">{{ $total }}</div>
            <div class="resumen-label">Total cuotas</div>
        </div>
        <div class="resumen-item">
            <div class="resumen-num" style="color:#166534;">{{ $pagados }}</div>
            <div class="resumen-label">Pagadas</div>
        </div>
        <div class="resumen-item">
            <div class="resumen-num" style="color:#854d0e;">{{ $pendientes }}</div>
            <div class="resumen-label">Pendientes</div>
        </div>
        <div class="resumen-item">
            <div class="resumen-num" style="color:#991b1b;">{{ $vencidos }}</div>
            <div class="resumen-label">Vencidas</div>
        </div>
    </div>

    <div class="seccion-titulo">Detalle de cuotas</div>
    <table class="plan">
        <thead>
            <tr>
                <th>#</th>
                <th>Período</th>
                <th>Vencimiento</th>
                <th class="right">Monto</th>
                <th class="right">Recargo</th>
                <th class="right">Descuento</th>
                <th class="right">Total</th>
                <th class="center">Estado</th>
                <th class="center">Fecha pago</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($contrato->pagos->sortBy(fn($p) => [$p->periodo_anio, $p->periodo_mes]) as $idx => $pago)
            <tr>
                <td class="muted">{{ $idx + 1 }}</td>
                <td><strong>{{ $meses[$pago->periodo_mes] }} {{ $pago->periodo_anio }}</strong></td>
                <td>{{ $pago->fecha_vencimiento->format('d/m/Y') }}</td>
                <td class="right">{{ number_format($pago->monto, 0, ',', '.') }}</td>
                <td class="right">
                    @if ($pago->recargo > 0)
                        <span style="color:#dc2626;">+{{ number_format($pago->recargo, 0, ',', '.') }}</span>
                    @else
                        <span class="muted">—</span>
                    @endif
                </td>
                <td class="right">
                    @if ($pago->descuento > 0)
                        <span style="color:#16a34a;">-{{ number_format($pago->descuento, 0, ',', '.') }}</span>
                    @else
                        <span class="muted">—</span>
                    @endif
                </td>
                <td class="right"><strong>{{ number_format($pago->total, 0, ',', '.') }}</strong></td>
                <td class="center">
                    @if ($pago->estado === 'pagado')
                        <span class="badge badge-pagado">Pagado</span>
                    @elseif ($pago->estado === 'vencido')
                        <span class="badge badge-vencido">Vencido</span>
                    @else
                        <span class="badge badge-pendiente">Pendiente</span>
                    @endif
                </td>
                <td class="center paid-date">
                    {{ $pago->fecha_pago ? $pago->fecha_pago->format('d/m/Y') : '—' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

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
        Plan de Pagos — Contrato N° {{ str_pad($contrato->id, 6, '0', STR_PAD_LEFT) }} — {{ $copia }} — Emitido el {{ now()->format('d/m/Y H:i') }}
    </div>
</div>

@endforeach

</body>
</html>
