<?php

use App\Models\ImageCapture;
use App\Models\Patient;
use App\Models\PatientFile;
use App\Models\PatientHistory;
use App\Models\PatientRecord;
use App\Models\Sketch;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

Route::get('/orders/{history}/print', function (PatientHistory $history) {
    $history->load(['patient', 'prescriptions.medicine', 'diseases', 'clinic']);
    return view('print.history', ['history' => $history, 'clinic' => $history->clinic, 'patient' => $history->patient]);
})->name('order.print');

Route::get('/orders/{history}/print-meds', function (PatientHistory $history) {
    $history->load(['patient', 'prescriptions.medicine']);
    return view('print.history-meds', ['history' => $history, 'patient' => $history->patient]);
})->name('order.print-meds');

$authorizePatientMedia = function (?Patient $patient, string $label): void {
    if (! $patient || ! $patient->clinic) {
        abort(404);
    }

    $hasAccess = auth()->user()?->clinics()
        ->whereKey($patient->clinic->Id)
        ->exists();

    if (! $hasAccess) {
        abort(403, "Unauthorized access to this {$label}.");
    }
};

$streamStoredMedia = function (?string $value) {
    if (! filled($value)) {
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

    if (Str::startsWith($value, ['http://', 'https://'])) {
        $publicDiskUrl = config('filesystems.disks.public.url');
        $resolvedStoragePath = null;

        if (filled($publicDiskUrl)) {
            $valueUrl = parse_url($value);
            $publicUrl = parse_url($publicDiskUrl);
            $publicUrlHost = $publicUrl['host'] ?? null;
            $publicUrlPath = rtrim($publicUrl['path'] ?? '', '/');
            $valueUrlHost = $valueUrl['host'] ?? null;
            $valueUrlPath = $valueUrl['path'] ?? '';

            if (
                filled($publicUrlHost) &&
                filled($valueUrlHost) &&
                strcasecmp($valueUrlHost, $publicUrlHost) === 0 &&
                filled($publicUrlPath) &&
                Str::startsWith($valueUrlPath, "{$publicUrlPath}/")
            ) {
                $resolvedStoragePath = Str::after($valueUrlPath, "{$publicUrlPath}/");
            }
        }

        if (filled($resolvedStoragePath)) {
            $value = $resolvedStoragePath;
        } else {
            return redirect($value);
        }
    }

    if (Str::startsWith($value, '/storage/')) {
        $value = Str::after($value, '/storage/');
    } elseif (Str::startsWith($value, '/')) {
        return redirect($value);
    }

    $publicPaths = array_unique(array_filter([
        $value,
        Str::startsWith($value, 'storage/') ? Str::after($value, 'storage/') : null,
        Str::startsWith($value, '/storage/') ? Str::after($value, '/storage/') : null,
    ]));

    foreach ($publicPaths as $path) {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->response($path, basename($path), [], 'inline');
        }
    }

    if (Storage::disk('local')->exists($value)) {
        return Storage::disk('local')->response($value, basename($value), [], 'inline');
    }

    abort(404);
};

Route::get('/patient-files/{record}', function (PatientFile $record) use ($authorizePatientMedia, $streamStoredMedia) {
    $authorizePatientMedia($record->patient ?? $record->patientHistory?->patient, 'file');

    return $streamStoredMedia($record->File);
})->middleware(['auth'])->name('patient.files.download');

Route::get('/patient-images/{record}', function (Patient $record) use ($authorizePatientMedia, $streamStoredMedia) {
    $authorizePatientMedia($record, 'image');

    return $streamStoredMedia($record->Image);
})->middleware(['auth'])->name('patient.images.view');

Route::get('/patient-sketches/{record}', function (Sketch $record) use ($authorizePatientMedia, $streamStoredMedia) {
    $authorizePatientMedia($record->patient ?? $record->patientHistory?->patient, 'sketch');

    return $streamStoredMedia($record->sketch);
})->middleware(['auth'])->name('patient.sketches.view');

Route::get('/patient-captures/{record}', function (ImageCapture $record) use ($authorizePatientMedia, $streamStoredMedia) {
    $authorizePatientMedia($record->patient ?? $record->patientHistory?->patient, 'capture');

    return $streamStoredMedia($record->capture);
})->middleware(['auth'])->name('patient.captures.view');

Route::get('/patient-records/{record}', function (PatientRecord $record) use ($authorizePatientMedia, $streamStoredMedia) {
    $authorizePatientMedia($record->patient ?? $record->patientHistory?->patient, 'report');

    return $streamStoredMedia($record->capture);
})->middleware(['auth'])->name('patient.records.view');
