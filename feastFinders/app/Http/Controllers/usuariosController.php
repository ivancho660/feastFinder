<?php

namespace App\Http\Controllers;

use App\Models\usuarios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth; // Facade correcto
use Tymon\JWTAuth\Exceptions\JWTException;

class usuariosController extends Controller
{

    /**
     * Obtener todos los usuarios (protegido por JWT)
     */
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
            'estado' => 'required|string|max:255',
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
            'estado' => $request->estado,
            'imagen' => $imagen,
            'nombre' => $request->nombre,
            'password' => bcrypt($request->password),
            'telefono' => $request->telefono,
            'tipo' => $request->tipo ?? 'USER',
            'username' => $request->username
        ]);

        // Autenticar al usuario recién creado y generar token
        $token = auth()->login($usuario);

        return response()->json([
            'success' => true,
            'message' => 'Usuario registrado exitosamente',
            'data' => $usuario,
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
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
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Credenciales incorrectas',
                ], 401);
            }
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo crear el token',
            ], 500);
        }

        $user = Auth::user();

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
            // Obtener el token actual del request
            $token = JWTAuth::getToken();
            
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token no encontrado'
                ], 401);
            }

            // Invalidar el token
            JWTAuth::invalidate($token);
            
            // Limpiar la autenticación
            Auth::logout();
            
            return response()->json([
                'success' => true,
                'message' => 'Sesión cerrada exitosamente',
                'redirect' => url('/')
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
            $user = JWTAuth::parseToken()->authenticate();
            
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
            $newToken = JWTAuth::parseToken()->refresh();
            
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
}