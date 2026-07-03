<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estudiante;
use App\Models\Especialidad;
use App\Models\Curso;
use App\Models\DetalleInscripcion;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class CertificadoController extends Controller
{
    /**
     * Verificar aprobación y generar PDF
     */
    public function generarCertificado($id_estudiante, $id_especialidad)
    {
        $estudiante = Estudiante::with('persona')->findOrFail($id_estudiante);
        $especialidad = Especialidad::findOrFail($id_especialidad);

        // 1. Obtener todos los cursos de esta especialidad
        $cursosEspecialidad = Curso::where('id_especialidad', $id_especialidad)->get();
        $totalCursos = $cursosEspecialidad->count();

        if ($totalCursos == 0) {
            return back()->withErrors(['Error' => 'La especialidad no tiene cursos registrados.']);
        }

        // 2. Obtener todas las notas finales del estudiante en esta especialidad
        // Necesitamos buscar en DetalleInscripcion -> Nota
        $detalles = DetalleInscripcion::with(['notaFinal', 'grupo.curso'])
            ->whereHas('inscripcion.matricula', function($q) use ($id_estudiante, $id_especialidad) {
                $q->where('id_estudiante', $id_estudiante)
                  ->where('id_especialidad', $id_especialidad);
            })
            ->get();

        $cursosAprobados = [];
        $notasCursos = [];

        foreach ($detalles as $detalle) {
            if ($detalle->notaFinal && $detalle->notaFinal->nota >= 11) {
                $idCurso = $detalle->grupo->id_curso;
                if (!in_array($idCurso, $cursosAprobados)) {
                    $cursosAprobados[] = $idCurso;
                    $notasCursos[$idCurso] = $detalle->notaFinal->nota;
                }
            }
        }

        // 3. Verificar si aprobó todos los cursos
        if (count($cursosAprobados) < $totalCursos) {
            $faltantes = $totalCursos - count($cursosAprobados);
            return back()->withErrors(['Certificado' => "El estudiante no puede obtener el certificado. Aprobó ".count($cursosAprobados)." de {$totalCursos} materias de la especialidad."]);
        }

        // Si pasó todo, generar PDF
        $data = [
            'estudiante' => $estudiante,
            'especialidad' => $especialidad,
            'fecha' => Carbon::now()->isoFormat('D \d\e MMMM \d\e YYYY'),
            'notas' => $notasCursos,
            'cursos' => $cursosEspecialidad
        ];

        $pdf = Pdf::loadView('certificados.pdf', $data);
        $pdf->setPaper('a4', 'landscape'); // Certificado apaisado

        $nombreArchivo = 'Certificado_' . str_replace(' ', '_', $estudiante->persona->nombres) . '.pdf';
        
        return $pdf->download($nombreArchivo);
    }
}
