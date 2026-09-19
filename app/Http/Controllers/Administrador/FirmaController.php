<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Models\Detallepago;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Firma digitalizada de quien valida los pagos.
 *
 * Se guarda contra el usuario, no contra el condominio, porque el recibo debe
 * llevar la firma de quien de verdad validó ese pago. Cuando cambie la mesa
 * directiva, los recibos viejos siguen mostrando la firma correcta y los
 * nuevos llevan la del tesorero entrante.
 *
 * Solo aparece en los recibos que descarga el vecino. Los que la tesorería
 * imprime para entregar en mano salen sin firma, para firmarse de puño.
 */
class FirmaController extends Controller
{
    private const CARPETA = 'firmas';

    public function index()
    {
        $usuario = Auth::user();

        return view('administrador.firma', [
            'firma' => $usuario->firma,
            'urlFirma' => $usuario->firma
                ? Storage::disk('public')->url(self::CARPETA.'/'.$usuario->firma)
                : null,
            'recibosFirmados' => $this->recibosQueLlevaranMiFirma($usuario),
        ]);
    }

    /**
     * Cuántos recibos ya validados saldrían con esta firma.
     *
     * Sirve para que el tesorero entienda el alcance antes de subirla o de
     * borrarla: no es un adorno del perfil, sale impresa en documentos que
     * los vecinos descargan.
     */
    private function recibosQueLlevaranMiFirma(User $usuario): int
    {
        if (! Schema::hasColumn('detallepagos', 'validado_por')) {
            return 0;
        }

        return Detallepago::where('estado', 'pagado')
            ->where('validado_por', $usuario->id)
            ->count();
    }

    public function guardar(Request $request)
    {
        try {
            $request->validate([
                // PNG de preferencia: el fondo transparente se ve bien sobre
                // la línea del recibo. 2 MB es de sobra para una firma.
                'firma' => 'required|image|mimes:png,jpg,jpeg|max:2048',
            ], [
                'firma.required' => 'Selecciona la imagen de tu firma.',
                'firma.image' => 'El archivo debe ser una imagen.',
                'firma.mimes' => 'La firma debe ser PNG o JPG. Se recomienda PNG con fondo transparente.',
                'firma.max' => 'La imagen no debe pesar más de 2 MB.',
            ]);

            $usuario = Auth::user();

            $nombre = 'firma-'.$usuario->id.'-'.time().'.'.$request->file('firma')->extension();
            $request->file('firma')->storeAs(self::CARPETA, $nombre, 'public');

            /*
             * Se prueba con el archivo YA guardado, generando un PDF de verdad
             * con la imagen dentro. dompdf necesita la extensión GD para los
             * PNG y, si no está, no falla la imagen: falla el documento
             * completo. Vale más que el error lo vea aquí el tesorero, una
             * vez, que 45 vecinos al intentar bajar su recibo.
             *
             * La firma anterior no se toca hasta que la nueva pasa la prueba.
             */
            $rutaNueva = Storage::disk('public')->path(self::CARPETA.'/'.$nombre);

            if (! \App\Services\PdfService::seIncrustaSinError($rutaNueva)) {
                $this->borrarArchivo($nombre);

                $esPng = strtolower($request->file('firma')->extension()) === 'png';

                return response()->json([
                    'success' => false,
                    'header' => '⚠️ Esta imagen no se puede usar',
                    'message' => $esPng
                        ? 'Este servidor no puede incrustar imágenes PNG en un PDF, porque le '
                            .'falta la extensión GD de PHP. Guarda tu firma como JPG y vuelve a '
                            .'subirla: se ve igual, solo que con fondo blanco en vez de '
                            .'transparente. Tu firma anterior sigue como estaba.'
                        : 'No se pudo incrustar esa imagen en un PDF de prueba. Intenta con otro '
                            .'archivo JPG. Tu firma anterior sigue como estaba.',
                ], 422);
            }

            // Ya pasó: ahora sí se retira la anterior.
            $this->borrarArchivo($usuario->firma);

            $usuario->update(['firma' => $nombre]);

            return response()->json([
                'success' => true,
                'header' => 'Firma guardada ✅',
                'message' => 'A partir de ahora los recibos que descarguen los vecinos de los pagos que tú validaste saldrán con tu firma.',
                'url' => Storage::disk('public')->url(self::CARPETA.'/'.$nombre),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'header' => '⚠️ Revisa el archivo',
                'message' => implode(' ', $e->validator->errors()->all()),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al guardar la firma: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'header' => '❌ Error',
                'message' => 'No se pudo guardar la firma.',
            ], 500);
        }
    }

    public function eliminar()
    {
        try {
            $usuario = Auth::user();

            $this->borrarArchivo($usuario->firma);
            $usuario->update(['firma' => null]);

            return response()->json([
                'success' => true,
                'header' => 'Firma eliminada ✅',
                'message' => 'Los recibos que descarguen los vecinos volverán a salir con la línea en blanco.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error al eliminar la firma: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'header' => '❌ Error',
                'message' => 'No se pudo eliminar la firma.',
            ], 500);
        }
    }

    private function borrarArchivo(?string $nombre): void
    {
        if ($nombre && Storage::disk('public')->exists(self::CARPETA.'/'.$nombre)) {
            Storage::disk('public')->delete(self::CARPETA.'/'.$nombre);
        }
    }
}
