<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->renameTables($this->tableRenames());
    }

    public function down(): void
    {
        $this->renameTables(array_flip($this->tableRenames()));
    }

    private function renameTables(array $renames): void
    {
        $tables = Schema::getTableListing(schemaQualified: false);

        foreach ($renames as $from => $to) {
            $actualFrom = $this->findTable($from, $tables);

            if ($actualFrom === null || $actualFrom === $to || $this->findTable($to, $tables) !== null) {
                continue;
            }

            Schema::rename($actualFrom, $to);

            $index = array_search($actualFrom, $tables, true);

            if ($index !== false) {
                $tables[$index] = $to;
            } else {
                $tables[] = $to;
            }
        }
    }

    private function findTable(string $expected, array $tables): ?string
    {
        foreach ($tables as $table) {
            if ($table === $expected) {
                return $table;
            }
        }

        foreach ($tables as $table) {
            if (strcasecmp($table, $expected) === 0) {
                return $table;
            }
        }

        return null;
    }

    private function tableRenames(): array
    {
        return [
            '__EFMigrationsHistory' => '__efmigrationshistory',
            'Abbreviations' => 'abbreviations',
            'Anupanas' => 'anupanas',
            'AspNetUsers' => 'aspnetusers',
            'AstavidhyaParikshas' => 'astavidhyaparikshas',
            'BodyPartOrFoods' => 'bodypartorfoods',
            'CalendarAppointments' => 'calendarappointments',
            'Cities' => 'cities',
            'DiseaseInvestigations' => 'diseaseinvestigations',
            'DiseaseLaboratoryReports' => 'diseaselaboratoryreports',
            'Diseases' => 'diseases',
            'DiseaseSymptoms' => 'diseasesymptoms',
            'DiseaseTypeLaboratoryReports' => 'diseasetypelaboratoryreports',
            'DiseaseTypeMedicines' => 'diseasetypemedicines',
            'DiseaseTypes' => 'diseasetypes',
            'DiseaseTypeSymptoms' => 'diseasetypesymptoms',
            'Doctors' => 'doctors',
            'DoctorUsers' => 'doctorusers',
            'Glossaries' => 'glossaries',
            'HetuPariksas' => 'hetupariksas',
            'LaboratoryReports' => 'laboratoryreports',
            'MainPrakrutiBodyPartOrFoods' => 'mainprakrutibodypartorfoods',
            'MainPrakrutis' => 'mainprakrutis',
            'MedicineForms' => 'medicineforms',
            'Medicines' => 'medicines',
            'ModernSymptoms' => 'modernsymptoms',
            'OtherDiagnosises' => 'otherdiagnosises',
            'Panchakarmas' => 'panchakarmas',
            'PatientHistories' => 'patienthistories',
            'PatientHistoryDiseases' => 'patienthistorydiseases',
            'PatientHistoryImages' => 'patienthistoryimages',
            'PatientHistoryLaboratoryReports' => 'patienthistorylaboratoryreports',
            'PatientHistoryMedicines' => 'patienthistorymedicines',
            'PatientHistoryModernSymptom' => 'patienthistorymodernsymptom',
            'PatientHistoryPanchakarmas' => 'patienthistorypanchakarmas',
            'PatientHistoryRogaPariksas' => 'patienthistoryrogapariksas',
            'PatientHistorySymptoms' => 'patienthistorysymptoms',
            'PatientHistoryVitals' => 'patienthistoryvitals',
            'PatientPrakrutis' => 'patientprakrutis',
            'Patients' => 'patients',
            'RogaPariksas' => 'rogapariksas',
            'States' => 'states',
            'Symptoms' => 'symptoms',
            'TestMedicines' => 'testmedicines',
            'TimeOfAdministrations' => 'timeofadministrations',
            'WomenHistories' => 'womenhistories',
        ];
    }
};
