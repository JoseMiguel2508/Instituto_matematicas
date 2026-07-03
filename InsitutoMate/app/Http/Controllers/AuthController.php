<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LogActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->intended('/dashboard');
        }
        return view('auth.login');
    }

    /**
     * Handle the login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Find the user by username
        $user = User::where('username', $credentials['username'])->first();

        if (!$user) {
            return back()->withErrors([
                'username' => 'El usuario ingresado no existe.',
            ])->onlyInput('username');
        }

        // Check if user is active
        if ($user->estado !== 'Activo') {
            return back()->withErrors([
                'username' => 'Este usuario está inactivo o bloqueado.',
            ])->onlyInput('username');
        }

        // Validate password against password_hash
        if (Hash::check($credentials['password'], $user->password_hash)) {
            // Reset failed attempts and update last access
            $user->update([
                'intentos_fallidos' => 0,
                'ultimo_acceso' => Carbon::now(),
            ]);

            // Authenticate user in session
            Auth::login($user);

            // Log activity
            LogActividad::create([
                'id_usuario' => $user->id_usuario,
                'accion' => 'LOGIN',
                'tabla_afectada' => 'USUARIO',
                'id_registro_afectado' => $user->id_usuario,
                'datos_anteriores' => null,
                'datos_nuevos' => 'Inicio de sesión exitoso',
                'fecha_hora' => Carbon::now(),
                'direccion_ip' => $request->ip(),
                'modulo' => 'Seguridad',
            ]);

            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        }

        // Password failed
        $user->increment('intentos_fallidos');
        
        if ($user->intentos_fallidos >= 5) {
            $user->update(['estado' => 'Bloqueado']);
            
            // Log block event
            LogActividad::create([
                'id_usuario' => $user->id_usuario,
                'accion' => 'UPDATE',
                'tabla_afectada' => 'USUARIO',
                'id_registro_afectado' => $user->id_usuario,
                'datos_anteriores' => 'Activo',
                'datos_nuevos' => 'Bloqueado por intentos fallidos',
                'fecha_hora' => Carbon::now(),
                'direccion_ip' => $request->ip(),
                'modulo' => 'Seguridad',
            ]);

            return back()->withErrors([
                'username' => 'El usuario ha sido bloqueado debido a demasiados intentos fallidos.',
            ])->onlyInput('username');
        }

        return back()->withErrors([
            'password' => 'Contraseña incorrecta.',
        ])->onlyInput('username');
    }

    /**
     * Logout the user.
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            // Log logout
            LogActividad::create([
                'id_usuario' => $user->id_usuario,
                'accion' => 'LOGOUT',
                'tabla_afectada' => 'USUARIO',
                'id_registro_afectado' => $user->id_usuario,
                'datos_anteriores' => null,
                'datos_nuevos' => 'Cierre de sesión',
                'fecha_hora' => Carbon::now(),
                'direccion_ip' => $request->ip(),
                'modulo' => 'Seguridad',
            ]);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
