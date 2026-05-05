<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Liquidación {{ $liquidacion->periodo_label }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #333; padding: 20px; }

        .header { display: table; width: 100%; margin-bottom: 25px; border-bottom: 3px solid #1e40af; padding-bottom: 15px; }
        .header-left { display: table-cell; width: 60%; vertical-align: top; }
        .header-right { display: table-cell; width: 40%; text-align: right; vertical-align: top; }
        .empresa-nombre { font-size: 22px; font-weight: bold; color: #1e40af; }
        .empresa-sub { font-size: 11px; color: #666; margin-top: 3px; }
        .doc-titulo { font-size: 16px; font-weight: bold; color: #1e40af; }
        .doc-numero { font-size: 11px; color: #666; margin-top: 4px; }
        .doc-fecha { font-size: 11px; color: #333; margin-top: 2px; }

        .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 10px; font-weight: bold; }
        .badge-borrador { background: #f3f4f6; color: #374151; }
        .badge-emitida { background: #fef9c3; color: #854d0e; }
        .badge-pagada { background: #dcfce7; color: #166534; }

        .section { margin-bottom: 18px; }
        .section-title { font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; color: #6b7280; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; margin-bottom: 8px; }

        .two-col { display: table; width: 100%; margin-bottom: 18px; }
        .col { display: table-cell; width: 50%; vertical-align: top; }
        .col:first-child { padding-right: 15px; }

        .info-row { margin-bottom: 5px; }
        .info-label { font-weight: bold; color: #374151; }
        .info-value { color: #111827; }

        table.detalle { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        table.detalle th { background: #1e40af; color: white; padding: 7px 10px; text-align: left; font-size: 10px; font-weight: bold; }
        table.detalle th.right { text-align: right; }
        table.detalle td { padding: 7px 10px; border-bottom: 1px solid #e5e7eb; font-size: 11px; }
        table.detalle td.right { text-align: right; }
        table.detalle tr:nth-child(even) td { background: #f9fafb; }
        table.detalle tr.subtotal td { font-weight: bold; background: #eff6ff; border-top: 2px solid #bfdbfe; }
        table.detalle tr.total td { font-weight: bold; background: #1e40af; color: white; font-size: 13px; border-top: none; }

        .resumen-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 15px; margin-bottom: 18px; }
        .resumen-row { display: table; width: 100%; padding: 4px 0; }
        .resumen-concepto { display: table-cell; color: #374151; }
        .resumen-monto { display: table-cell; text-align: right; font-weight: bold; color: #111827; }
        .resumen-row.deduccion .resumen-monto { color: #dc2626; }
        .resumen-row.neto { border-top: 2px solid #1e40af; margin-top: 6px; padding-top: 8px; }
        .resumen-row.neto .resumen-concepto { font-weight: bold; font-size: 13px; color: #1e40af; }
        .resumen-row.neto .resumen-monto { font-size: 16px; color: #1e40af; }

        .firma-section { display: table; width: 100%; margin-top: 40px; }
        .firma-col { display: table-cell; width: 50%; text-align: center; padding: 0 20px; }
        .firma-linea { border-top: 1px solid #374151; padding-top: 5px; margin-top: 40px; font-size: 10px; color: #374151; }

        .footer { margin-top: 30px; border-top: 1px solid #e5e7eb; padding-top: 10px; text-align: center; font-size: 9px; color: #9ca3af; }

        .observaciones-box { background: #fffbeb; border-left: 3px solid #f59e0b; padding: 8px 12px; margin-bottom: 15px; }

        .copia-page { page-break-after: always; }
        .copia-label { display: inline-block; background: #1e40af; color: #fff; font-size: 9px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; padding: 3px 12px; border-radius: 3px; margin-bottom: 14px; }
    </style>
</head>
<body>

@php $copias = ['ORIGINAL', 'DUPLICADO']; @endphp
@foreach ($copias as $i => $copia)
<div class="{{ $i === 0 ? 'copia-page' : '' }}">
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
            <div style="margin-top:6px;">
                <span class="badge badge-{{ $liquidacion->estado }}">{{ strtoupper($liquidacion->estado_label) }}</span>
            </div>
        </div>
    </div>

    {{-- PERIODO --}}
    <div class="section">
        <div class="section-title">Período</div>
        <p style="font-size:14px; font-weight:bold; color:#1e40af;">{{ $liquidacion->periodo_label }}</p>
    </div>

    {{-- PROPIETARIO E INMUEBLE --}}
    <div class="two-col">
        <div class="col">
            <div class="section">
                <div class="section-title">Propietario</div>
                <div class="info-row">
                    <span class="info-value" style="font-weight:bold; font-size:12px;">
                        {{ $liquidacion->propietario->nombre_completo }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">DNI: </span>
                    <span class="info-value">{{ $liquidacion->propietario->dni }}</span>
                </div>
                @if($liquidacion->propietario->cuit)
                <div class="info-row">
                    <span class="info-label">CUIT: </span>
                    <span class="info-value">{{ $liquidacion->propietario->cuit }}</span>
                </div>
                @endif
                @if($liquidacion->propietario->email)
                <div class="info-row">
                    <span class="info-label">Email: </span>
                    <span class="info-value">{{ $liquidacion->propietario->email }}</span>
                </div>
                @endif
                @if($liquidacion->propietario->cbu)
                <div class="info-row">
                    <span class="info-label">CBU: </span>
                    <span class="info-value">{{ $liquidacion->propietario->cbu }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Banco: </span>
                    <span class="info-value">{{ $liquidacion->propietario->banco }}</span>
                </div>
                @endif
            </div>
        </div>
        <div class="col">
            <div class="section">
                <div class="section-title">Inmueble</div>
                <div class="info-row">
                    <span class="info-value" style="font-weight:bold; font-size:12px;">
                        {{ $liquidacion->propiedad->tipo_label }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-value">{{ $liquidacion->propiedad->direccion_completa }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Inquilino: </span>
                    <span class="info-value">{{ $liquidacion->contrato->inquilino->nombre_completo }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Contrato desde: </span>
                    <span class="info-value">{{ $liquidacion->contrato->fecha_inicio->format('d/m/Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Contrato hasta: </span>
                    <span class="info-value">{{ $liquidacion->contrato->fecha_fin->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- DETALLE LIQUIDACIÓN --}}
    <div class="section">
        <div class="section-title">Detalle de la Liquidación</div>
        <table class="detalle">
            <thead>
                <tr>
                    <th>Concepto</th>
                    <th class="right">Monto</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Alquiler cobrado — {{ $liquidacion->periodo_label }}</td>
                    <td class="right">$ {{ number_format($liquidacion->monto_alquiler, 2, ',', '.') }}</td>
                </tr>

                @if($gastos->count() > 0)
                    @foreach($gastos as $gasto)
                    <tr>
                        <td style="color:#dc2626;">Gasto deducible: {{ $gasto->concepto }}
                            @if($gasto->proveedor) ({{ $gasto->proveedor }})@endif
                        </td>
                        <td class="right" style="color:#dc2626;">- $ {{ number_format($gasto->monto, 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                @endif

                <tr class="subtotal">
                    <td>Subtotal (alquiler - gastos)</td>
                    <td class="right">$ {{ number_format($liquidacion->monto_alquiler - $liquidacion->total_gastos, 2, ',', '.') }}</td>
                </tr>

                <tr>
                    <td style="color:#dc2626;">Comisión inmobiliaria @if($liquidacion->descuento_tipo !== 'valor')({{ number_format($liquidacion->comision_porcentaje, 1) }}%)@endif</td>
                    <td class="right" style="color:#dc2626;">- $ {{ number_format($liquidacion->monto_comision, 2, ',', '.') }}</td>
                </tr>

                <tr class="total">
                    <td>TOTAL A ACREDITAR AL PROPIETARIO</td>
                    <td class="right">$ {{ number_format($liquidacion->monto_neto, 2, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- RESUMEN VISUAL --}}
    <div class="resumen-box">
        <div class="resumen-row">
            <div class="resumen-concepto">Alquiler cobrado</div>
            <div class="resumen-monto">$ {{ number_format($liquidacion->monto_alquiler, 2, ',', '.') }}</div>
        </div>
        <div class="resumen-row deduccion">
            <div class="resumen-concepto">Gastos deducibles ({{ $gastos->count() }} ítem/s)</div>
            <div class="resumen-monto">- $ {{ number_format($liquidacion->total_gastos, 2, ',', '.') }}</div>
        </div>
        <div class="resumen-row deduccion">
            <div class="resumen-concepto">Comisión inmobiliaria @if($liquidacion->descuento_tipo !== 'valor')({{ number_format($liquidacion->comision_porcentaje, 1) }}%)@endif</div>
            <div class="resumen-monto">- $ {{ number_format($liquidacion->monto_comision, 2, ',', '.') }}</div>
        </div>
        <div class="resumen-row neto">
            <div class="resumen-concepto">NETO A COBRAR</div>
            <div class="resumen-monto">$ {{ number_format($liquidacion->monto_neto, 2, ',', '.') }}</div>
        </div>
    </div>

    {{-- GASTOS DETALLE (si hay) --}}
    @if($gastos->count() > 0)
    <div class="section">
        <div class="section-title">Gastos Deducibles del Período</div>
        <table class="detalle">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Categoría</th>
                    <th>Concepto</th>
                    <th>Proveedor</th>
                    <th>Comprobante</th>
                    <th class="right">Monto</th>
                </tr>
            </thead>
            <tbody>
                @foreach($gastos as $gasto)
                <tr>
                    <td>{{ $gasto->fecha->format('d/m/Y') }}</td>
                    <td>{{ $gasto->categoria_label }}</td>
                    <td>{{ $gasto->concepto }}</td>
                    <td>{{ $gasto->proveedor ?? '—' }}</td>
                    <td>{{ $gasto->comprobante ?? '—' }}</td>
                    <td class="right">$ {{ number_format($gasto->monto, 2, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr class="subtotal">
                    <td colspan="5">Total gastos</td>
                    <td class="right">$ {{ number_format($gastos->sum('monto'), 2, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

    {{-- OBSERVACIONES --}}
    @if($liquidacion->observaciones)
    <div class="observaciones-box">
        <strong>Observaciones:</strong> {{ $liquidacion->observaciones }}
    </div>
    @endif

    {{-- ESTADO PAGO --}}
    @if($liquidacion->estado === 'pagada')
    <div style="background:#dcfce7; border:1px solid #86efac; border-radius:6px; padding:10px 15px; margin-bottom:15px;">
        <strong style="color:#166534;">✓ PAGADO al propietario el {{ $liquidacion->fecha_pago_propietario->format('d/m/Y') }}</strong>
        @if($liquidacion->medio_pago)
        — Medio de pago: {{ ucfirst($liquidacion->medio_pago) }}
        @endif
    </div>
    @endif

    {{-- FIRMAS --}}
    <div class="firma-section">
        <div class="firma-col">
            <div class="firma-linea">
                {{ $config->razon_social ?: $config->nombre }}
            </div>
        </div>
        <div class="firma-col">
            <div class="firma-linea">
                Firma Propietario<br>
                {{ $liquidacion->propietario->nombre_completo }}
            </div>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        Documento generado el {{ now()->format('d/m/Y \a \l\a\s H:i') }} hs. — {{ $copia }}
        — Esta es una liquidación informativa de honorarios y rendición de cuentas.
    </div>

</div>
@endforeach

</body>
</html>
