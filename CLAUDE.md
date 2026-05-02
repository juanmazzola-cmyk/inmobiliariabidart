# CLAUDE.md — Sistema de Gestión Inmobiliaria

## Stack técnico

- **PHP 8.2** / **Laravel 12**
- **Livewire 4** (`livewire/livewire ^4.2`)
- **Tailwind CSS v4** via Vite (`@tailwindcss/vite ^4.0`)
- **DomPDF** (`barryvdh/laravel-dompdf ^3.1`) para PDF de recibos y liquidaciones
- **XAMPP** local: servidor Apache + MySQL, base de datos `inmobiliaria`
- **Vite 7** — después de cambios en `resources/css/app.css` siempre correr `npm run build`

---

## Estructura de módulos

### Rutas (`routes/web.php`)

| Ruta | Componente | Nombre |
|------|-----------|--------|
| `/dashboard` | `Dashboard` | `dashboard` |
| `/propietarios` | `Propietarios` | `propietarios.index` |
| `/inquilinos` | `Inquilinos` | `inquilinos.index` |
| `/propiedades` | `Propiedades` | `propiedades.index` |
| `/propiedades-venta` | `PropiedadesVenta` | `propiedades-venta.index` |
| `/contratos` | `Contratos` | `contratos.index` |
| `/pagos` | `Pagos` | `pagos.index` |
| `/gastos` | `Gastos` | `gastos.index` |
| `/liquidaciones` | `Liquidaciones` | `liquidaciones.index` |
| `/reportes` | `Reportes` | `reportes.index` |
| `/configuracion` | `Configuracion` | `configuracion.index` |
| `/pagos/{id}/recibo` | closure PDF | `pagos.recibo` |
| `/liquidaciones/{id}/pdf` | closure PDF | `liquidaciones.pdf` |
| `/configuracion/backup` | closure SQL dump | `configuracion.backup` |

### Modelos (`app/Models/`)

| Modelo | Tabla | Relaciones clave |
|--------|-------|-----------------|
| `Propietario` | `propietarios` | `propiedades`, `propiedadesVenta`, `liquidaciones` |
| `Inquilino` | `inquilinos` | `contratos` |
| `Propiedad` | `propiedades` | `propietario` (nullable), `contratos` |
| `PropiedadVenta` | `propiedades_venta` | `propietario` (nullable) |
| `Contrato` | `contratos` | `propiedad`, `inquilino`, `pagos` |
| `Pago` | `pagos` | `contrato` |
| `Gasto` | `gastos` | `propiedad` |
| `Liquidacion` | `liquidaciones` | `propietario`, `pagos` |
| `Configuracion` | `configuracion` | — (singleton: `firstOrCreate(['id'=>1])`) |

### Componentes Livewire (`app/Livewire/`)

Todos usan `#[Layout('layouts.app')]` y `#[Title('...')]`.

- `Dashboard` — KPIs, alquileres vencidos, contratos próximos a vencer, marca de agua
- `Propietarios` — CRUD + conteo de propiedades alquiler/venta como links
- `Inquilinos` — CRUD
- `Propiedades` — CRUD alquileres con fotos, filtro por propietario (`filtroPropietario`), lightbox galería
- `PropiedadesVenta` — CRUD ventas con fotos, filtro por propietario (`filtroPropietario`), lightbox galería
- `Contratos` — CRUD + modal de aumento de alquiler (% o valor fijo) + campo comisión en $
- `Pagos` — registro de cobros, descarga de recibo PDF (dos copias: ORIGINAL/DUPLICADO en una hoja)
- `Gastos` — CRUD gastos por propiedad
- `Liquidaciones` — generación de liquidaciones con descuento por % o valor fijo (ARS/USD), PDF (dos páginas: ORIGINAL/DUPLICADO)
- `Reportes` — reportes varios incluyendo propiedades vendidas
- `Configuracion` — datos de la empresa, logo, backup/restore de base de datos

---

## Convenciones y patrones importantes

### Nombres en Title Case
Los accessors `getNombreCompletoAttribute()` en `Inquilino` y `Propietario` usan `mb_convert_case(..., MB_CASE_TITLE)`:
```php
public function getNombreCompletoAttribute(): string
{
    return mb_convert_case($this->apellido, MB_CASE_TITLE) . ', ' . mb_convert_case($this->nombre, MB_CASE_TITLE);
}
```
Formato resultante: `García, Juan`. Soporta caracteres especiales (ñ, á, etc.).

### Separador de miles: punto (formato argentino)
Todos los valores monetarios usan `number_format($valor, 0, ',', '.')`.

Para inputs de texto con formato:
- Usar `wire:model.blur` + hook `updated*` en el componente que formatea al salir del campo
- Siempre hacer `str_replace('.', '', $valor)` antes de cualquier operación numérica en PHP
- Ejemplo en `Contratos.php`: `updatedMontoAlquiler()`, `updatedDepositoGarantia()`, `updatedValorAumento()`

### Cursor pointer global
En `resources/css/app.css` hay una regla `@layer base` para mostrar la mano en todos los elementos clickeables. Después de editar este archivo correr `npm run build`.

### Scroll en páginas con layout flex
El layout `resources/views/layouts/app.blade.php` usa flexbox. Para que `overflow-y-auto` funcione en el contenido, los hijos flex necesitan `min-h-0`:
```html
<div class="flex-1 flex flex-col min-h-0 overflow-hidden">
    <main class="flex-1 min-h-0 overflow-y-auto p-6">
```

### Configuracion (singleton)
`Configuracion::get()` usa `firstOrCreate(['id' => 1])`. La migración es `2024_01_01_000090_create_configuracion_table.php`.

### Filtro de propietario entre páginas
`Propiedades` y `PropiedadesVenta` tienen `#[Url] public string $filtroPropietario = ''`.
Al navegar desde Propietarios con `?filtroPropietario={id}`, la vista muestra un chip con el nombre y un botón para limpiar el filtro.

### Modal de aumento de alquiler (Contratos)
Botón ↑ (verde) en contratos activos/vencidos. Propiedades del componente:
`modalAumento`, `aumentoContratoId`, `tipoAumento` (porcentaje|valor), `valorAumento`, `montoActualAumento`, `monedaAumento`, `actualizarCuotasAumento`.
Métodos: `abrirModalAumento()`, `aplicarAumento()`, `cerrarModalAumento()`.

### Rescindir vs Eliminar contratos
Dos acciones distintas en la tabla de contratos:
- **Rescindir** (× naranja) — solo activos/vencidos. Cambia estado a `rescindido`, libera la propiedad a `disponible`. Conserva todos los pagos y liquidaciones.
- **Eliminar** (🗑 rojo) — disponible en todos los estados. Borra el contrato físicamente; el CASCADE en BD elimina automáticamente todos sus pagos y liquidaciones.

La FK `contrato_id` en `pagos` y `liquidaciones` usa `ON DELETE CASCADE` (migración `2026_05_01_022406_add_cascade_delete_to_pagos_and_liquidaciones.php`).

### Fotos de propiedades (alquiler y venta)
- Se guardan en `storage/app/public/propiedades-alquiler/` y `storage/app/public/propiedades-venta/`
- Columna `fotos JSON nullable` en ambas tablas, cast `'fotos' => 'array'` en los modelos
- Accessor `getPrimeraFotoAttribute()` en `Propiedad` y `PropiedadVenta`
- Los componentes usan `WithFileUploads`, propiedades: `$fotosActuales`, `$fotosEliminar`, `$nuevasFotos`
- Validación: `'nuevasFotos.*' => 'nullable|image|max:3072'` (máx 3 MB por foto)

### Lightbox galería (Alpine.js)
Ambas páginas de propiedades usan `x-data="{ fotos: [], indice: -1 }"` en el div raíz.
- Al hacer click en una foto se asigna `fotos = @js([...urls])` e `indice = N`
- El lightbox es una pantalla completa con fondo blanco (`fixed inset-0 bg-white`)
- Barra superior con botón "← Volver" y contador "N / Total"
- Flechas ‹ › a los costados de la imagen; teclas ← → también navegan
- Tira de miniaturas al pie; la activa se resalta con `ring-2 ring-blue-500`
- Al pasar fotos de PHP a Alpine usar `@js(collect($p->fotos ?? [])->map(fn($f) => asset('storage/'.$f))->values()->all())`

### Descuento en Liquidaciones
El modal de nueva liquidación tiene un toggle **% Porcentaje / $ Valor fijo**:
- Porcentaje: calcula `monto_comision = alquiler * pct / 100`
- Valor fijo: el usuario ingresa el monto directamente (con selector ARS/USD para display)
  - Al guardar con valor fijo: `comision_porcentaje` se calcula en reverso (`monto / alquiler * 100`)
- Propiedades: `nuevaDescuentoTipo` ('porcentaje'|'valor'), `nuevaDescuentoValorFijo`, `nuevaDescuentoMoneda`

### PDFs con dos copias
- **Recibo de pago** (`resources/views/pdf/recibo.blade.php`): dos copias en una hoja A4 separadas por línea de corte `✂ CORTE ✂`. Cada copia tiene etiqueta ORIGINAL / DUPLICADO.
- **Liquidación** (`resources/views/pdf/liquidacion.blade.php`): dos páginas separadas (`page-break-after: always`), cada una con etiqueta ORIGINAL / DUPLICADO.
- Ambos PDFs muestran `$config->razon_social ?: $config->nombre` en el encabezado y firma.
- El recibo recibe `$config` desde la closure en `routes/web.php`. La liquidación lo recibe desde `Liquidaciones::generarPdf()`.

### Teléfonos con prefijo +54
- Los inputs de teléfono en Propietarios, Inquilinos y Configuración usan un addon visual fijo "+54" (izquierda del input).
- El valor guardado en BD es solo el número local (ej: `9 11 1234-5678`).
- En todos los lugares de display se antepone "+54 " al número almacenado.

### Tarjetas de propiedades (alquiler y venta) — estilo unificado
- Ambas páginas usan el mismo estilo de card: badge azul para tipo (`bg-blue-100 text-blue-700`), emojis `📐` para superficie y `🚗` para cochera, `👤` para propietario.
- El badge de estado usa: disponible → azul, alquilada/vendida → verde, reservada → amarillo, en reparación/inactiva → gris.
- La barra de búsqueda/filtros está envuelta en un panel `bg-white rounded-xl border shadow-sm p-4`, con `flex flex-wrap gap-3 items-end` y el botón `ml-auto`.

### Razón social en header y PDFs
- El header del layout (`layouts/app.blade.php`) muestra `Configuracion::get()->razon_social ?: nombre` arriba a la derecha.
- Los PDFs de recibo y liquidación también usan la razón social en encabezado y firma.

### Validaciones — mensajes en español
Siempre definir mensajes personalizados en `$messages` para todas las reglas usadas (incluyendo `.min`, `.required`, `.numeric`, etc.) para evitar que aparezcan claves crudas tipo `validation.min.string`.

---

## Marca de agua en Dashboard
Posición `fixed`, `z-0`, `pointer-events-none`, `opacity: 0.06`.
CSS padding-left/top para centrar en el área de contenido (sidebar 256px, header 64px).
- Si hay logo subido: muestra `$config->logo_path`
- Si no: muestra SVG de casa inline

---

## Pagos — terminología
- El modal de registro dice **"Comprobante N°"** (no "Cuota N°")
- No hay campo "N° Comprobante" separado en el formulario

---

## Migraciones ejecutadas
Todas las migraciones en `database/migrations/` están aplicadas, incluyendo:
- `2024_01_01_000090_create_configuracion_table.php` (tabla `configuracion`)
- `2026_04_30_162656_add_comision_monto_to_contratos_table.php` (campo `comision_monto`)
- `2026_04_30_164520_add_fotos_to_propiedades_table.php` (campo `fotos JSON`)
- `2026_04_30_170139_make_propietario_id_nullable_in_propiedades_table.php` (`propietario_id` nullable en `propiedades`)

---

## Comandos frecuentes

```bash
# Compilar CSS/JS (obligatorio después de cambiar app.css o app.js)
npm run build

# Correr migraciones pendientes
php artisan migrate

# Limpiar caché de vistas/config
php artisan optimize:clear
```

---

## Notas de desarrollo

- El proyecto usa `git` (inicializado). Commit inicial: `88b07a4`.
- El entorno es Windows 10 con XAMPP. Los paths son `C:\xampp\htdocs\inmobiliaria\`.
- Para abrir el proyecto: `http://localhost/inmobiliaria/public/`
- La URL base en producción puede cambiar; los assets usan `asset()` y `storage/`.
- Las fotos de propiedades en alquiler se guardan en `storage/app/public/propiedades-alquiler/`.
- Las fotos de propiedades en venta se guardan en `storage/app/public/propiedades-venta/`.
- El logo de configuración se guarda en `storage/app/public/` (path guardado en `configuracion.logo_path`).
- El backup de base de datos es un dump SQL puro generado en PHP (sin `mysqldump`), importable desde el mismo panel de Configuración.
