<?php

use App\Models\PatientFile;
use App\Models\PatientHistory;
use App\Models\Sketch;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

Route::get('/orders/{history}/print', function (PatientHistory $history) {
    $history->load(['patient', 'prescriptions.medicine', 'diseases', 'clinic']);
    return view('print.history', ['history' => $history, 'clinic' => $history->clinic, 'patient' => $history->patient]);
})->name('order.print');


Route::get('/patient-files/{record}', function (PatientFile $record) {

    if (!$record->patient || !$record->patient->clinic) {
        abort(404);
    }

    $hasAccess = auth()->user()->clinics()
        ->whereKey($record->patient->clinic->Id)
        ->exists();

    if (!$hasAccess) {
        abort(403, 'Unauthorized access to this file.');
    }

    $path = $record->File;

    if (!Storage::disk('local')->exists($path)) {
        abort(404);
    }

    return Storage::disk('local')->response($path);


})->middleware(['auth'])->name('patient.files.download');

Route::get('/patient-sketches/{record}', function (Sketch $record) {

    $patient = $record->patient ?? $record->patientHistory?->patient;

    if (!$patient || !$patient->clinic) {
        abort(404);
    }

    $hasAccess = auth()->user()->clinics()
        ->whereKey($patient->clinic->Id)
        ->exists();

    if (!$hasAccess) {
        abort(403, 'Unauthorized access to this sketch.');
    }

    $value = $record->sketch;

    if (!filled($value)) {
        abort(404);
    }

    if (preg_match('/^data:(image\/[a-zA-Z0-9.+-]+);base64,(.*)$/s', $value, $matches)) {
        $binary = base64_decode(str_replace(' ', '+', $matches[2]), true);

        if ($binary === false) {
            abort(404);
        }

        return response($binary, 200, [
            'Content-Type' => $matches[1],
            'Content-Disposition' => 'inline',
        ]);
    }

    if (Str::startsWith(ltrim($value), '<svg')) {
        return response($value, 200, ['Content-Type' => 'image/svg+xml']);
    }

    if (Str::startsWith($value, ['http://', 'https://', '/'])) {
        return redirect($value);
    }

    if (Storage::disk('public')->exists($value)) {
        return Storage::disk('public')->response($value);
    }

    if (Storage::disk('local')->exists($value)) {
        return Storage::disk('local')->response($value);
    }

    abort(404);
})->middleware(['auth'])->name('patient.sketches.view');
