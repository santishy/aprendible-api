<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LogoutController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum')
        ];
    }
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        //se borra el token actual donde se hizo sesion al parecer si hace en otr apagina no le eliminaria el token d la base de datos y se mantendria logueado
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }
}
