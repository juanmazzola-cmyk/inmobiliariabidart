<?php

namespace App\Livewire;

use App\Models\Configuracion;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;


#[Layout('layouts.guest')]
#[Title('Iniciar sesión')]
class Login extends Component
{
    public string $usuario    = '';
    public string $contrasena = '';
    public bool   $remember   = false;
    public string $mensajeRecuperacion = '';

    public function ingresar(): void
    {
        $this->validate([
            'usuario'    => 'required',
            'contrasena' => 'required',
        ], [
            'usuario.required'    => 'Ingresá tu usuario.',
            'contrasena.required' => 'Ingresá tu contraseña.',
        ]);

        $user = User::where('name', $this->usuario)->first();

        if (!$user || !Hash::check($this->contrasena, $user->password)) {
            $this->addError('usuario', 'Usuario o contraseña incorrectos.');
            return;
        }

        Auth::login($user, $this->remember);
        session()->regenerate();
        $this->redirect(route('dashboard'), navigate: true);
    }

    public function recuperarContrasena(): void
    {
        $config = Configuracion::get();

        if (!$config->login_whatsapp) {
            $this->mensajeRecuperacion = 'No hay WhatsApp configurado. Contactá al administrador.';
            return;
        }

        // Generar nueva contraseña y guardar
        $nueva = strtoupper(substr(str_shuffle('abcdefghjkmnpqrstuvwxyz'), 0, 3)) . rand(100, 999);
        $user  = User::first();
        if ($user) {
            $user->update(['password' => Hash::make($nueva)]);
        }

        // Redirigir a WhatsApp con la contraseña en el mensaje
        $numero = preg_replace('/\D/', '', $config->login_whatsapp);
        if (!str_starts_with($numero, '54')) {
            $numero = '54' . $numero;
        }
        $mensaje = urlencode("Tu nueva contraseña del sistema inmobiliario es: *{$nueva}*");
        $this->redirect("https://wa.me/{$numero}?text={$mensaje}");
    }

    public function render()
    {
        return view('livewire.login');
    }
}
