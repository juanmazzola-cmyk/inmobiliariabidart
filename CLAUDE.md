# CLAUDE.md — Sistema de Gestión Inmobiliaria

## Stack técnico

- **PHP 8.2** / **Laravel 12**
- **Livewire 4** (`livewire/livewire ^4.2`)
- **Tailwind CSS v4** via Vite (`@tailwindcss/vite ^4.0`)
- **DomPDF** (`barryvdh/laravel-dompdf ^3.1`) para PDF de recibos y liquidaciones
- **XAMPP** local: servidor Apache + MySQL, base de datos `inmobiliaria`
- **Vite 7** — después de cambios en `resources/css/app.css` **o al agregar nuevas clases Tailwind en templates blade** siempre correr `npm run build` y commitear `public/build/`

---

## Estructura de módulos

### Rutas (`routes/web.php`)

Todas las rutas (excepto login) están dentro de `Route::middleware('auth')->group(...)`.

| Ruta | Componente | Nombre |
|------|-----------|--------|
| `/login` | `Login` | `login` (middleware `guest`) |
| `/logout` | closure POST | `logout` |
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
| `/contratos/{id}/plan-pagos` | closure PDF | `contratos.plan-pagos` |

### Modelos (`app/Models/`)

| Modelo | Tabla | Relaciones clave |
|--------|-------|-----------------|
| `Propietario` | `propietarios` | `propiedades`, `propiedadesVenta`, `liquidaciones` |
| `Inquilino` | `inquilinos` | `contratos` |
| `Propiedad` | `propiedades` | `propietario` (nullable), `contratos` |
| `PropiedadVenta` | `propiedades_venta` | `propietario` (nullable) |
| `Contrato` | `contratos` | `propiedad`, `inquilino`, `pagos` |
| `Pago` | `pagos` | `contrato` |
| `Gasto` | `gastos` | `propiedad`, `liquidacion` (nullable), `pago` (nullable) |
| `Liquidacion` | `liquidaciones` | `propietario`, `pagos` |
| `CategoriaGasto` | `categorias_gastos` | — |
| `Configuracion` | `configuracion` | — (singleton: `firstOrCreate(['id'=>1])`) |

### Componentes Livewire (`app/Livewire/`)

- `Login` — usa `#[Layout('layouts.guest')]`. Autentica por campo `name` (no email). Tiene "¿Olvidaste tu contraseña?" que genera contraseña aleatoria y redirige a WhatsApp.
- `Dashboard` — KPIs en dos filas (solo informativos, no son links). Fila 1: Alquileres (naranja, edificio), En Venta (violeta, casa), Contratos Activos (verde). Fila 2: Cobranza del Mes (azul, signo $), Saldo Pendiente (rojo). Marca de agua, alquileres vencidos, contratos próximos a vencer. Ver sección "Dashboard" más abajo para detalles de las tablas inferiores.
- `Propietarios` — CRUD + conteo de propiedades alquiler/venta como texto no clickeable. DNI opcional. Al guardar, convierte nombre y apellido a Title Case. Iconos de acción: naranja (edificio → alquiler), violeta (casa → venta), azul (editar), rojo (eliminar). Navegan con modal pre-abierto vía `?abrirConPropietario={id}`.
- `Inquilinos` — CRUD. Al guardar, convierte nombre, apellido y ocupación a Title Case.
- `Propiedades` — CRUD alquileres con fotos, filtro por propietario (`filtroPropietario`), lightbox galería. `#[Url] public ?int $abrirConPropietario = null` → `mount()` pre-abre el modal con propietario seleccionado. Búsqueda incluye inquilino actual. Íconos de card: verde (nuevo contrato), azul (editar), rojo (eliminar). El texto "X contrato/s" es no clickeable.
- `PropiedadesVenta` — CRUD ventas con fotos, filtro por propietario (`filtroPropietario`), lightbox galería. Mismo patrón `abrirConPropietario` que `Propiedades`.
- `Contratos` — CRUD + modal de aumento de alquiler (% o valor fijo) + campo comisión en $. Sin botón "Aplicar Incrementos" (eliminado). En el sidebar se muestra como **"Contratos de alquiler"**. Botón impresora (violeta) genera PDF de plan de pagos. `#[Url] public ?int $abrirConPropiedad = null` → `mount()` pre-abre el modal de nuevo contrato con esa propiedad seleccionada (navegación desde card de propiedad).
- `Pagos` — registro de cobros, descarga de recibo PDF (dos copias: ORIGINAL/DUPLICADO en una hoja). KPI separado en **Pendiente** (amarillo) y **Vencido** (rojo). Estados: `pagado`, `pendiente`, `vencido`. Botón Editar para pagos ya cobrados (`editarPago()`). Panel naranja de gastos disponibles del inmueble; al abrir "Cobrar" se auto-aplican todos los gastos disponibles y el estado queda en `pagado`.
- `Gastos` — CRUD gastos por propiedad. Categorías cargadas dinámicamente desde `CategoriaGasto`. Columna "Estado" muestra si el gasto fue descontado en una cuota, en una liquidación, o está pendiente. Búsqueda por nombre de inquilino o propietario. Muestra dirección completa + propietario + inquilino activo.
- `Liquidaciones` — generación de liquidaciones con descuento por % o valor fijo (ARS/USD), PDF dos copias en una hoja A4 con línea de corte. Estados: solo `emitida` y `pagada`. Gastos ingresados en el modal como lista dinámica (categoría + monto), se crean registros `Gasto` vinculados via `liquidacion_id` al guardar.
- `Reportes` — reportes varios incluyendo propiedades vendidas. **No incluye**: rendición por propietario, gastos deducibles por categoría, ni cuotas cobradas por período (se eliminaron).
- `Configuracion` — datos de la empresa, categorías de gastos, credenciales de acceso al sistema. **La sección de subir logo fue eliminada** — el campo `logo_path` existe en BD pero no se usa desde la UI.

---

## Autenticación

### Login
- Componente `Login` con layout `layouts.guest` (fondo degradado oscuro, centrado).
- Autentica por `users.name` (no por email): `User::where('name', $usuario)->first()` + `Hash::check()`.
- Campo "Recordarme" usa `Auth::login($user, $this->remember)`.
- `bootstrap/app.php` tiene `$middleware->redirectGuestsTo('/login')` para que el middleware `auth` redirija correctamente.
- El header del layout incluye botón "Salir" → POST `/logout`.

### Recuperación de contraseña por WhatsApp
- Botón "¿Olvidaste tu contraseña?" en el formulario de login.
- Genera contraseña aleatoria (3 letras mayúsculas + 3 números), la guarda hasheada en `users.password`.
- Redirige directamente a `https://wa.me/{numero}?text={mensaje}` (usando `$this->redirect()`, no `window.open`).
- El número de WhatsApp se configura en Configuración → "Acceso al sistema" → campo "WhatsApp para recuperación".
- Si no hay WhatsApp configurado, muestra aviso en pantalla.
- **La contraseña nunca se muestra en pantalla** — solo va al WhatsApp del propietario.

### Credenciales configurables
- Usuario y WhatsApp se guardan en la tabla `configuracion` (`login_usuario`, `login_whatsapp`).
- Al guardar en Configuración, se sincroniza `users.name` (y `users.password` si se ingresó nueva contraseña).
- La contraseña en Configuración es opcional: dejarla vacía no la cambia.

---

## Categorías de gastos

- Almacenadas en la tabla `categorias_gastos` (modelo `CategoriaGasto`), no hardcodeadas.
- Se gestionan desde Configuración → sección "Categorías de gastos" (agregar / eliminar).
- **Las categorías se guardan como nombre de display directamente** (ej: `"Reparación"`, no `"reparacion"`).
- `Gasto::getCategoriaLabelAttribute()` simplemente devuelve `$this->categoria` (sin conversión).
- El select de categoría en el modal de gastos y el filtro de la lista usan `@foreach ($categorias as $cat)`.
- Categorías iniciales sembradas en la migración: Administración, Expensas, Impuesto, Otro, Reparación, Servicio.

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

### wire:model y Alpine — no mezclar en el mismo input
**Nunca usar `wire:model` y `x-model` de Alpine en el mismo `<input>`**: genera conflictos donde el valor no llega al servidor al ejecutar una acción. Si se necesita reactividad Alpine sobre un campo Livewire, usar `$wire.$watch('propiedad', ...)` en `x-init`, o usar `wire:model.blur` y lógica `@if` en Blade para mostrar/ocultar elementos dependientes.

### Cursor pointer global
En `resources/css/app.css` hay una regla `@layer base` para mostrar la mano en todos los elementos clickeables. Después de editar este archivo correr `npm run build`.

### npm run build — cuándo es obligatorio
Correr `npm run build` y commitear `public/build/` en cualquiera de estos casos:
- Se modificó `resources/css/app.css` o `resources/js/app.js`
- Se agregaron **nuevas clases de color Tailwind** en templates blade que no existían antes en el build (ej: `bg-purple-100`, `text-orange-500`). Si no se rebuilda, las clases nuevas no aparecen en producción.

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

### Abrir modal con datos pre-cargados desde otra página
Patrón compartido por varios componentes: `#[Url] public ?int $abrirConX = null`. En `mount()`, si el parámetro está seteado, llama al método de apertura del modal y pre-completa el campo correspondiente.

| Componente | Param URL | Campo pre-cargado | Navegación desde |
|---|---|---|---|
| `Propiedades` | `abrirConPropietario` | `propietarioId` | Propietarios |
| `PropiedadesVenta` | `abrirConPropietario` | `propietarioId` | Propietarios |
| `Contratos` | `abrirConPropiedad` | `propiedadId` | Card de propiedad (ícono verde) |

### Búsqueda con orWhere — siempre agrupar
Al combinar búsqueda por texto (`orWhere`/`orWhereHas`) con filtros (`when`), el `or` debe estar dentro de un `where(fn($sub) => ...)` para evitar que "sangre" a otros filtros:
```php
->when($this->busqueda, fn($q) => $q->where(fn($sub) =>
    $sub->whereHas('relacion1', ...)
        ->orWhereHas('relacion2', ...)
))
```

### Modal de aumento de alquiler (Contratos)
Botón ↑ (verde) en contratos activos/vencidos. Propiedades del componente:
`modalAumento`, `aumentoContratoId`, `tipoAumento` (porcentaje|valor), `valorAumento`, `montoActualAumento`, `monedaAumento`, `actualizarCuotasAumento`.
Métodos: `abrirModalAumento()`, `aplicarAumento()`, `cerrarModalAumento()`.
`aplicarAumento()` aplica el porcentaje sobre el monto de **cada cuota individualmente** (no sobre el monto base del contrato), preservando los escalonamientos.

### Incremento automático de alquiler (Contratos)
- Campos en `contratos`: `incremento_automatico` (bool), `porcentaje_incremento`, `meses_incremento`, `ultimo_incremento_at` (date nullable).
- Al **crear** un contrato con `incremento_automatico = true`, `generarCuotasMensuales()` aplica el porcentaje escalonado directamente: cada `meses_incremento` meses el monto de la cuota se incrementa.
- Comando `php artisan contratos:incrementar` también aplica incrementos a contratos activos (scheduler diario). Actualiza `monto_alquiler`, `ultimo_incremento_at` y cuotas pendientes/vencidas.
- Scheduler en `routes/console.php`: `Schedule::command('contratos:incrementar')->dailyAt('08:00')`.
- Para que corra automáticamente en producción: configurar cron en cPanel → `* * * * * php artisan schedule:run`.

### Gastos en Liquidaciones
- En el modal de nueva liquidación, los gastos se ingresan como lista dinámica: categoría (select) + monto (number input) + botón "+ Agregar".
- Se pueden agregar N gastos; cada uno aparece listado en rojo con botón × para quitar.
- Al guardar, se crean registros en tabla `gastos` con `liquidacion_id` seteado (vinculación directa).
- `total_gastos` en la liquidación se calcula sumando `$this->nuevosGastos`.
- El PDF busca gastos por: 1) `liquidacion->gastos()`, 2) propiedad+mes/año, 3) todos los gastos de la propiedad sin `liquidacion_id`.

### Gastos relacionados en Pagos (descuento automático)
- Al abrir el modal "Cobrar" para un pago, `cargarGastos()` busca los gastos del inmueble que estén sin vincular (`pago_id IS NULL`) del período o de los últimos 3 meses sin liquidar.
- Si hay gastos disponibles, se auto-aplican todos: `$this->descuento = suma`, `$this->estado = 'pagado'`, `$this->gastosAplicadosIds = [ids]`.
- El modal muestra aviso verde "Descuento aplicado automáticamente" y panel naranja con los gastos disponibles (los aplicados muestran "✓ Aplicado").
- Al guardar, los gastos en `gastosAplicadosIds` reciben `pago_id = $pagoId` en la tabla `gastos`, marcándolos como usados.
- Al editar un pago existente (`editarPago()`), se cargan los `gastosAplicadosIds` previos del pago, luego se anulan los vínculos y se re-vinculan al guardar. Esto evita que los gastos queden huérfanos.
- El campo descuento usa solo `wire:model.blur` (sin Alpine `x-model`) para evitar conflictos de binding. La categoría de descuento aparece con `@if($descuento > 0)`.

### Estados de Pago
Los pagos tienen tres estados posibles:
- `pagado` — cobrado (con o sin descuento de gasto aplicado) (badge verde)
- `pendiente` — aún no vencido (badge amarillo)
- `vencido` — pasó la fecha de vencimiento sin cobrar (badge rojo)

El KPI "Cobrado" suma `where('estado', 'pagado')`. El botón "Cobrar" se oculta cuando el estado es `pagado`. El estado `descontado` no existe en pagos — el descuento se refleja en el campo `descuento` y en la columna "Estado" de gastos.

### Período abreviado en Pagos
`Pago::getPeriodoLabelAttribute()` devuelve las primeras 3 letras del mes + 2 últimos dígitos del año: `"Ene 26"`, `"May 26"`, etc.

### Descuento con categoría en Pagos
- Columna `descuento_categoria` (string nullable) en tabla `pagos`.
- En el modal, cuando `$descuento > 0` (evaluado en Blade con `@if`), se muestra un select de categorías vinculado con `wire:model="descuentoCategoria"`.
- El recibo PDF muestra `"Descuento — Categoría"` si hay categoría, o solo `"Descuento"` si no.
- El badge del recibo dice siempre "PAGO REGISTRADO" (no varía según si hay descuento).

### Estado de Gasto (columna "Estado" en lista)
La columna "Estado" en la lista de gastos muestra:
- Badge verde **"✓ Descontado"** cuando `pago_id` está seteado (fue usado como descuento en una cuota). Al pasar el mouse muestra el período de la cuota.
- Badge azul **"En liquidación"** cuando `liquidacion_id` está seteado.
- Texto gris "Deducible" si `deducible = true` pero aún no usado.
- Texto gris claro "No ded." si `deducible = false`.

### Rescindir vs Eliminar contratos
Dos acciones distintas en la tabla de contratos:
- **Rescindir** (× naranja) — solo activos/vencidos. Cambia estado a `rescindido`, libera la propiedad a `disponible`. Conserva todos los pagos y liquidaciones.
- **Eliminar** (🗑 rojo) — disponible en todos los estados. Borra el contrato físicamente; el CASCADE en BD elimina automáticamente todos sus pagos y liquidaciones.

La FK `contrato_id` en `pagos` y `liquidaciones` usa `ON DELETE CASCADE` (migración `2026_05_01_022406_add_cascade_delete_to_pagos_and_liquidaciones.php`).

### Fotos de propiedades (alquiler y venta)
- **En producción (DonWeb):** se guardan en `public/storage/propiedades-alquiler/` y `public/storage/propiedades-venta/` (directorio real, sin symlink). El disco `public` en `config/filesystems.php` usa `public_path('storage')` como root. El `.cpanel.yml` crea estas carpetas con `mkdir -p` en cada deploy.
- **En local:** el disco `public` apunta a `public/storage/` que es un symlink a `storage/app/public/`, por lo que los archivos terminan en `storage/app/public/propiedades-alquiler/` igual que antes.
- Se accede siempre via `asset('storage/' . $path)` — sin cambios en vistas.
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
- La columna `descuento_tipo` en la tabla `liquidaciones` guarda cómo se ingresó el descuento
- El PDF muestra `(X%)` junto a "Comisión inmobiliaria" solo si `descuento_tipo === 'porcentaje'`
- Al seleccionar contrato, `updatedNuevaContratoId()` pre-carga la comisión: si el contrato tiene `comision_monto` fijo lo usa como valor fijo; si tiene `comision_porcentaje` lo usa como porcentaje. El monto base se toma del pago del período si existe.

### PDFs con dos copias
- **Recibo de pago** (`resources/views/pdf/recibo.blade.php`): dos copias en una hoja A4 separadas por línea de corte `✂ CORTE ✂`. Cada copia tiene etiqueta ORIGINAL / DUPLICADO. No muestra el propietario. Si hay descuento con categoría muestra `"Descuento — Categoría"`. Si el estado es `descontado` el badge dice "PAGO CON DESCUENTO REGISTRADO".
- **Liquidación** (`resources/views/pdf/liquidacion.blade.php`): dos copias en **una sola hoja A4** con línea de corte (no page-break). Muestra cada gasto como `"Gastos deducibles — Categoría"` en el resumen. Los gastos se obtienen via `liquidacion->gastos()` (por `liquidacion_id`), con fallback a propiedad+mes/año, y luego a todos los gastos de la propiedad sin filtro.
- **Plan de pagos** (`resources/views/pdf/plan_pagos.blade.php`): dos copias en una hoja A4 portrait con `✂ CORTE ✂`. Muestra tabla completa de cuotas con monto, vencimiento, estado y fecha de pago. Ruta: `contratos.plan-pagos`. Botón impresora violeta en la tabla de contratos.
- Ambos PDFs muestran `$config->razon_social ?: $config->nombre` en el encabezado y firma.
- El recibo recibe `$config` desde la closure en `routes/web.php` Y desde `Pagos::generarRecibo()` (ambos métodos deben pasarlo). La liquidación lo recibe desde `Liquidaciones::generarPdf()`.

### Teléfonos con prefijo +54
- Los inputs de teléfono en Propietarios, Inquilinos y Configuración usan un addon visual fijo "+54" (izquierda del input).
- El valor guardado en BD es solo el número local (ej: `9 11 1234-5678`).
- En todos los lugares de display se antepone "+54 " al número almacenado.

### Tarjetas de propiedades (alquiler y venta) — estilo unificado
- Ambas páginas usan el mismo estilo de card: badge azul para tipo (`bg-blue-100 text-blue-700`), emojis `📐` para superficie y `🚗` para cochera, `👤` para propietario.
- El badge de estado usa: disponible → azul, alquilada/vendida → verde, reservada → amarillo, en reparación/inactiva → gris.
- La barra de búsqueda/filtros está envuelta en un panel `bg-white rounded-xl border shadow-sm p-4`, con `flex flex-wrap gap-3 items-end` y el botón `ml-auto`.

### Razón social en header y PDFs
- El header del layout (`layouts/app.blade.php`) muestra `$cfg->razon_social ?: $cfg->nombre` arriba a la derecha. Se obtiene con un único `@php $cfg = Configuracion::get()` para evitar doble query.
- Los PDFs de recibo y liquidación también usan la razón social en encabezado y firma.

### Modal de confirmación global (Alpine.js)
Todas las acciones de eliminar/rescindir usan un modal personalizado en lugar del `confirm()` nativo del navegador.

- Definido en `layouts/app.blade.php` como `x-show="$store.confirm.show"` con animaciones Alpine.
- Alpine store `$store.confirm` con propiedades: `show`, `titulo`, `mensaje`, `accionLabel`, `colorBtn`, `action`.
- **Uso en cualquier blade**: `@click="$store.confirm.open(() => $wire.metodo(id), { opciones })"`
- Opciones disponibles: `titulo`, `mensaje`, `accionLabel`, `colorBtn` ('red' o 'orange').
- Sin opciones usa defaults: título "¿Confirmar eliminación?", mensaje estándar, botón rojo "Eliminar".
- Ejemplo con opciones (rescindir): `$store.confirm.open(() => $wire.rescindir(id), { titulo: '¿Rescindir contrato?', mensaje: '...', accionLabel: 'Rescindir', colorBtn: 'orange' })`
- **No usar `wire:confirm`** — reemplazado en todos los módulos por este patrón.

### Validaciones — mensajes en español
Siempre definir mensajes personalizados en `$messages` para todas las reglas usadas (incluyendo `.min`, `.required`, `.numeric`, etc.) para evitar que aparezcan claves crudas tipo `validation.min.string`.

### Sidebar — estructura y etiquetas
- Sección **Gestión**: Dashboard, Propietarios, Inquilinos, Casas en alquiler, Casas en venta, Contratos de alquiler
- Sección **Pagos**: Gastos deducibles, **Cobro alquileres** (→ `pagos.index`), **Pago propietarios** (→ `liquidaciones.index`)
- Sección **Reportes**: Reportes
- Sección **Sistema**: Configuración
- Encabezados de sección en `text-amber-400`

---

## Dashboard — tablas inferiores

### Alquileres vencidos
- Muestra pagos con `estado = 'pendiente'` cuya `fecha_vencimiento < now()->subDays(11)`.
- **No depende del campo `estado = 'vencido'`** — se detecta por fecha, sin modificar la BD.
- Si el inquilino paga y se registra el cobro (estado pasa a `pagado`), desaparece automáticamente.
- Cada fila muestra: nombre del inquilino, dirección completa de la propiedad, nombre del propietario (si tiene), monto, fecha de vencimiento, y link "Ir al contrato →".
- Máximo 8 filas, ordenadas por fecha de vencimiento ascendente.

### Contratos próximos a vencer
- Muestra contratos con `estado = 'activo'` y `fecha_fin <= now()->addDays(60)`.
- Color de la fecha: **naranja** si faltan entre 30 y 60 días, **rojo** si faltan menos de 30.
- Cada fila muestra: nombre del inquilino, dirección completa, nombre del propietario (si tiene), fecha de vencimiento, y link "Ir al contrato →".
- Máximo 5 filas, ordenadas por fecha de vencimiento ascendente.
- El KPI "Contratos Activos" también usa 60 días para el conteo de "por vencer".

### Reportes — contratos venciendo
La sección "Contratos que vencen en los próximos 60 días" en Reportes también usa el umbral de 60 días (antes era 90).

## Marca de agua en Dashboard
Posición `fixed`, `z-0`, `pointer-events-none`, `opacity: 0.06`.
CSS padding-left/top para centrar en el área de contenido (sidebar 256px, header 64px).
- Siempre muestra SVG de casa inline (la sección de logo fue eliminada de Configuración).

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
- `2026_05_01_022406_add_cascade_delete_to_pagos_and_liquidaciones.php` (ON DELETE CASCADE en `pagos` y `liquidaciones`)
- `2026_05_02_000001_add_valor_referencia_to_propiedades_table.php` (campo `valor_referencia` decimal nullable en `propiedades`)
- `2026_05_05_210555_add_descuento_tipo_to_liquidaciones_table.php` (campo `descuento_tipo` string en `liquidaciones`, default `'porcentaje'`)
- `2026_05_05_225616_add_login_fields_to_configuracion_table.php` (campos `login_usuario` default `'admin'` y `login_whatsapp` nullable en `configuracion`)
- `2026_05_05_235237_create_categorias_gastos_table.php` (tabla `categorias_gastos` con seed de 6 categorías)
- `2026_05_06_185600_add_ultimo_incremento_to_contratos_table.php` (campo `ultimo_incremento_at` date nullable en `contratos`)
- `2026_05_06_223709_add_descuento_categoria_to_pagos_table.php` (campo `descuento_categoria` string nullable en `pagos`)
- `2026_05_08_000001_add_pago_id_to_gastos_table.php` (campo `pago_id` FK nullable → `pagos`, `nullOnDelete` en `gastos`)

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

- El proyecto usa `git`. Repositorio: `https://github.com/juanmazzola-cmyk/inmobiliariabidart.git`. Rama única: `main` (la rama `demo` fue eliminada).
- El entorno es Windows 10 con XAMPP. Los paths son `C:\xampp\htdocs\inmobiliaria\`.
- Para abrir el proyecto localmente: `http://localhost/inmobiliaria/public/`
- Las fotos de propiedades en alquiler se guardan en `public/storage/propiedades-alquiler/` (producción) o `storage/app/public/propiedades-alquiler/` (local via symlink).
- Las fotos de propiedades en venta se guardan en `public/storage/propiedades-venta/` (producción) o `storage/app/public/propiedades-venta/` (local via symlink).
- El campo `configuracion.logo_path` existe en BD pero ya no se usa (sección de logo eliminada de la UI).

## Producción — DonWeb hosting compartido

- **URL:** `https://inmobiliariabidart.proyectosia.com.ar`
- **Plan:** DonWeb Plan 2 (sin SSH ni terminal)
- **Estructura en servidor:**
  - `public_html/inmobiliariabidart/` → raíz web del subdominio (contenido de `public/`)
  - `public_html/inmobiliariabidart_app/` → aplicación Laravel completa
- **`public/index.php` en servidor** tiene paths modificados apuntando a `/../inmobiliariabidart_app/`
- **BD:** `c2761827_inmobid` (host: localhost)
- **Deploy:** automático via webhook GitHub → Feroz panel (git-based). Al hacer `git push` a `main`, Feroz detecta el push y despliega automáticamente.
- **`.cpanel.yml`** ejecuta post-deploy: `php artisan migrate --force` y `php artisan optimize:clear`.
- **`vendor/` y `public/build/`** están commiteados en el repo (el servidor no tiene composer ni npm).
- **Migraciones manuales en producción:** recrear un `setup.php` temporal (ver historial git) en `public/`, ejecutarlo desde el browser y borrarlo inmediatamente. El archivo fue eliminado del repositorio por seguridad.
- **Fotos en producción:** funcionan correctamente. El disco `public` usa `public_path('storage')` como root (sin symlink). Ver sección "Fotos de propiedades".

### Deploy automático (flujo normal)
1. Hacer cambios locales
2. `npm run build` si se modificó CSS/JS
3. `git add`, `git commit`, `git push`
4. El webhook dispara el deploy en Feroz automáticamente
5. `.cpanel.yml` corre migraciones y limpia caché

### setup.php
Script PHP temporal para ejecutar migraciones, limpiar caché y resetear usuario en producción (sin SSH). **Fue eliminado del repositorio** por seguridad — recrearlo cuando sea necesario a partir del historial git (`git show HEAD~N:public/setup.php`). Siempre borrarlo del servidor después de usarlo.

---

## Acceso externo con ngrok

Para exponer el proyecto via ngrok:
1. Actualizar en `.env`:
   ```
   APP_URL=https://<subdominio>.ngrok-free.dev/inmobiliaria/public
   ASSET_URL=https://<subdominio>.ngrok-free.dev/inmobiliaria/public
   ```
2. Correr `php artisan optimize:clear`
3. En `bootstrap/app.php` ya está configurado `$middleware->trustProxies(at: '*')` para que Laravel detecte correctamente el scheme `https` desde los headers de ngrok.

> La URL de ngrok gratuito cambia en cada reinicio del túnel — solo hay que actualizar las dos variables del `.env` y limpiar caché.
