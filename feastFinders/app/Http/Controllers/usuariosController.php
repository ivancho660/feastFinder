<?php

namespace App\Http\Controllers;

use App\Models\usuarios; // Asegúrate de que este es el nombre correcto de tu modelo de usuario
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Str;
use Carbon\Carbon; // Importa la clase Carbon para trabajar con fechas y horas
use App\Models\productos;
use Illuminate\Support\Facades\Log;

class usuariosController extends Controller // <<-- Asegúrate de que el nombre del archivo es UsuariosController.php (con 'U' mayúscula)
{
    protected $emailService;
    protected $appUrl; // Propiedad para almacenar la URL de la aplicación

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
        $this->appUrl = config('app.url'); // Obtiene la URL base de la aplicación desde el archivo .env
    }

    public function index()
    {
        $usuarios = usuarios::all();
        return response()->json($usuarios);
    }

    /**
     * Registrar un nuevo usuario y devolver token JWT
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'direccion' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:usuarios',
            'estado' => 'nullable|string|max:255',
            'imagen' => 'nullable|string',
            'nombre' => 'required|string|max:255',
            'password' => 'required|string|min:8',
            'telefono' => 'required|string|max:255',
            'tipo' => 'nullable|string|max:255',
            'username' => 'required|string|max:255|unique:usuarios'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'errors' => $validator->errors(),
                'data' => $request->all()
            ], 422);
        }

        $imagen = $request->imagen ?? 'images/default.jpg';

        $usuario = usuarios::create([
            'direccion' => $request->direccion,
            'email' => $request->email,
            'estado' => $request->estado ?? 'activo', // Establece 'activo' como valor predeterminado si no se proporciona
            'imagen' => $imagen,
            'nombre' => $request->nombre,
            'password' => bcrypt($request->password), // Usa bcrypt para hashear la contraseña
            'telefono' => $request->telefono,
            'tipo' => $request->tipo ?? 'USER', // Establece 'USER' como valor predeterminado si no se proporciona
            'username' => $request->username
        ]);

        

        $token = auth()->login($usuario); // Inicia sesión el usuario recién creado y genera un token JWT

        return response()->json([
            'success' => true,
            'message' => 'Usuario registrado exitosamente',
            'data' => $usuario,
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60 // Duración del token en segundos
        ], 201);
    }

    /**
     * Iniciar sesión y devolver token JWT
     */
  public function login(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|string|email|max:255',
        'password' => 'required|string|min:8',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);
    }

    $credentials = $request->only('email', 'password');

    try {
        // Intenta autenticar al usuario con las credenciales proporcionadas
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas',
            ], 401);
        }
    } catch (JWTException $e) {
        // Captura cualquier excepción relacionada con JWT
        return response()->json([
            'success' => false,
            'message' => 'No se pudo crear el token',
        ], 500);
    }

    $user = Auth::user(); // ✅ Aquí usas el método correcto

    return response()->json([
        'success' => true,
        'token' => $token,
        'token_type' => 'bearer',
        'expires_in' => auth()->factory()->getTTL() * 60,
        'user' => [
            'id' => $user->id,
            'name' => $user->nombre,
            'email' => $user->email,
            'tipo' => $user->tipo,
            'imagen' => $user->imagen
        ]
    ]);
}


    /**
     * Cerrar sesión e invalidar token JWT
     */
    public function cerrarSesion(Request $request)
    {
        try {
            $token = JWTAuth::getToken(); // Obtiene el token JWT actual

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token no encontrado'
                ], 401);
            }

            JWTAuth::invalidate($token); // Invalida el token
            Auth::logout(); // Cierra la sesión de Laravel

            return response()->json([
                'success' => true,
                'message' => 'Sesión cerrada exitosamente'
            ]);

        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cerrar la sesión',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener el usuario actual autenticado (protegido por JWT)
     */
    public function perfil()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate(); // Autentica al usuario usando el token de la solicitud

            return response()->json([
                'success' => true,
                'user' => $user
            ]);

        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado o token inválido'
            ], 404);
        }
    }

    /**
     * Refrescar token JWT
     */
    public function refreshToken()
    {
        try {
            $newToken = JWTAuth::parseToken()->refresh(); // Refresca el token JWT

            return response()->json([
                'success' => true,
                'token' => $newToken,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
            ]);

        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo refrescar el token'
            ], 401);
        }
    }

    /**
     * Solicitar recuperación de contraseña (para móvil)
     * Genera un código, lo guarda en la DB y lo envía por correo.
     */
    public function solicitarRecuperacion(Request $request)
    {
        // Valida que el email sea requerido, tenga formato de email y exista en la tabla 'usuarios'
        $request->validate([
            'email' => 'required|email|exists:usuarios,email',
        ]);

        $emailDestino = $request->input('email');

        // Busca el usuario por su email
        $usuario = usuarios::where('email', $emailDestino)->first();

        // Si el usuario no se encuentra (aunque la validación 'exists' ya lo cubre), retorna un error.
        if (!$usuario) {
            return response()->json(['message' => 'El correo electrónico no está registrado.'], 404);
        }

        // 1. Genera un código de restablecimiento aleatorio (ej. 6 caracteres alfanuméricos)
        $codigoRestablecimiento = Str::random(6);
        // 2. Define las horas de expiración del código
        $horasExpiracion = 2;

        $usuario->resetToken = $codigoRestablecimiento;

        $usuario->tokenExpiracion = Carbon::now()->addHours($horasExpiracion);
        $usuario->save();

        // 4. Llama al servicio de correo para enviar el email con el código
        try {
            $this->emailService->enviarCorreo(
                $emailDestino,
                'Código de Recuperación de Contraseña',
                $codigoRestablecimiento,
                $horasExpiracion
            );

            return response()->json(['message' => 'Correo de recuperación enviado con éxito.'], 200);

        } catch (\Exception $e) {
            // Aquí usas \Log::error, lo cual es correcto.
            \Log::error('Error al enviar correo de recuperación para ' . $emailDestino . ': ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['message' => 'Error al enviar el correo de recuperación. Intenta de nuevo más tarde.'], 500);
        }
    }

    /**
     * Validar código de recuperación (para móvil)
     * Verifica si el código proporcionado es válido y no ha expirado.
     */
    public function validarCodigoRecuperacion(Request $request)
    {
        // Valida los campos requeridos
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:usuarios,email',
            'codigo' => 'required|string|size:6' // Asegúrate de que 'size' coincida con la longitud de tu token
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Busca el usuario por email, y verifica que el token coincida y no haya expirado
        $usuario = usuarios::where('email', $request->email)
                            ->where('resetToken', $request->codigo)
                            ->where('tokenExpiracion', '>', Carbon::now()) // Compara con la hora actual
                            ->first();

        if (!$usuario) {
            // Si el usuario no se encuentra con ese token o el token ha expirado
            return response()->json([
                'success' => false,
                'message' => 'Código inválido o expirado'
            ], 400);
        }

        // Si el código es válido, genera un token temporal más largo
        // Este token temporal se usará para autorizar el cambio de contraseña.
        // Se sobrescribe el token actual de restablecimiento con este nuevo token temporal.
        $tokenTemporal = Str::random(60); // Un token más largo para la siguiente etapa
        $usuario->resetToken = $tokenTemporal; // Reutilizamos el campo resetToken para este token temporal
        $usuario->save(); // Guarda el nuevo token temporal

        return response()->json([
            'success' => true,
            'message' => 'Código validado correctamente',
            'token_temporal' => $tokenTemporal // Envía el token temporal al cliente
        ]);
    }

    /**
     * Permite al usuario establecer una nueva contraseña usando el token temporal.
     */
    public function restablecerPassword(Request $request)
    {
        // Valida los campos requeridos, incluyendo la confirmación de la contraseña
        $validator = Validator::make($request->all(), [
            'token_temporal' => 'required|exists:usuarios,resetToken', // Verifica que el token temporal exista
            'password' => 'required|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Busca el usuario por el token temporal
        $usuario = usuarios::where('resetToken', $request->token_temporal)->first();
        // Si no se encuentra el usuario, retorna un error
        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Token de restablecimiento inválido o no encontrado.'
            ], 400);
        }

        // Actualiza la contraseña del usuario
        $usuario->password = bcrypt($request->password); // Hashea la nueva contraseña

        // Limpia los campos del token después de usarlo para evitar reusos
        $usuario->resetToken = null;
        $usuario->tokenExpiracion = null;
        $usuario->save(); 

        return response()->json([
            'success' => true,
            'message' => 'Contraseña restablecida exitosamente'
        ]);
    }

    /**
     * Traer el menú de un restaurante específico
     */
    public function traerMenu($nr)
    {
        Log::info("Buscando productos para el restaurante: {$nr}"); 

        if (empty($nr)) {
            Log::warning("El nombre del restaurante proporcionado es inválido.");
            return response()->json([
                'message' => 'El nombre del restaurante no puede estar vacío.',
                'productos' => []
            ], 400); 
        }

        $productosDisponibles = productos::where('nombreR', $nr) 
                                        ->where('cantidad', '>', 0)
                                        ->get();

        if ($productosDisponibles->isEmpty()) {
            Log::warning("No se encontraron productos disponibles para el restaurante: {$nr}");
            return response()->json([
                'message' => 'No se encontraron productos disponibles para este restaurante.',
                'productos' => []
            ], 200);
        } else {
            $productosDisponibles->map(function ($producto) {
                if ($producto->imagen) {
                    $producto->imagen = url('images/' . $producto->imagen);
                }
                return $producto;
            });
            Log::info("Se encontraron {$productosDisponibles->count()} productos disponibles para el restaurante: {$nr}");
        }

        return response()->json([
            'message' => 'Productos obtenidos exitosamente.',
            'productos' => $productosDisponibles
        ]);
    }

}