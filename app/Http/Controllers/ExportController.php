<?php

namespace App\Http\Controllers;

use App\Exports\ParticipantsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function exportParticipants(Request $request)
    {
        // Obtenemos el término de búsqueda desde el frontend
        $search = $request->input('search');

        $fileName = 'participantes_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download(new ParticipantsExport($search), $fileName);
    }
}
