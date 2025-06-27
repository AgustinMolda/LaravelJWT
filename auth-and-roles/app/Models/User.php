<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * MODELO DE USUARIO
 * 
 * Este modelo representa a los usuarios del sistema y maneja:
 * - Autenticación JWT (JSON Web Tokens)
 * - Protección de datos sensibles
 * - Relaciones con otros modelos
 * - Validación de datos
 */
class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * CAMPOS MASS ASSIGNMENT (ASIGNACIÓN MASIVA)
     * 
     * Estos son los únicos campos que se pueden asignar masivamente
     * usando User::create() o $user->fill(). Esto es una medida de
     * seguridad para evitar que se asignen campos no deseados.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',      // Nombre del usuario
        'role',      // Rol: 'admin' o 'user'
        'email',     // Email único del usuario
        'password',  // Contraseña (se hashea automáticamente)
    ];

    /**
     * CAMPOS OCULTOS
     * 
     * Estos campos NO se incluyen cuando el modelo se convierte a JSON.
     * Es una medida de seguridad para no exponer información sensible
     * como contraseñas o tokens.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',        // Contraseña hasheada (nunca se expone)
        'remember_token',  // Token de "recordar sesión"
    ];

    /**
     * CONVERSIÓN DE TIPOS DE DATOS
     * 
     * Define cómo se deben convertir ciertos campos cuando se
     * recuperan de la base de datos o se guardan en ella.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',  // Convierte a objeto DateTime
            'password' => 'hashed',             // Aplica hash automáticamente
        ];
    }

    /**
     * IDENTIFICADOR JWT
     * 
     * Este método devuelve el identificador único del usuario
     * que se incluirá en el token JWT. Normalmente es el ID
     * del usuario en la base de datos.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey(); // Devuelve el ID del usuario
    }

    /**
     * CLAIMS PERSONALIZADOS JWT
     * 
     * Este método permite agregar información adicional al token JWT.
     * Por ejemplo, podrías incluir el rol del usuario, permisos, etc.
     * En este caso está vacío, pero podrías agregar:
     * 
     * return [
     *     'role' => $this->role,
     *     'name' => $this->name,
     * ];
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return []; // No hay claims personalizados por ahora
    }
}
