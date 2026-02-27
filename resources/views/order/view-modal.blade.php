<div style="font-family: Arial, sans-serif; padding: 20px;">
    <style>
        .modal-prescription-container {
            max-width: 100%;
            margin: 0 auto;
        }
        
        .modal-header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .modal-header h1 {
            margin: 0 0 10px 0;
            font-size: 24px;
            color: #1f2937;
        }
        
        .modal-header p {
            margin: 5px 0;
            color: #6b7280;
            font-size: 14px;
        }
        
        .modal-divider {
            border: none;
            border-top: 1px solid #e5e7eb;
            margin: 20px 0;
        }
        
        .modal-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 20px;
        }
        
        .modal-info-item {
            padding: 8px;
            background-color: #f9fafb;
            border-radius: 4px;
        }
        
        .modal-info-item strong {
            color: #374151;
            font-weight: 600;
        }
        
        .modal-info-item span {
            color: #1f2937;
            margin-left: 5px;
        }
        
        .modal-text-right {
            text-align: right;
        }
        
        .modal-prescription-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 14px;
        }
        
        .modal-prescription-table thead {
            background-color: #f3f4f6;
        }
        
        .modal-prescription-table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .modal-prescription-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
            color: #1f2937;
        }
        
        .modal-prescription-table tbody tr:hover {
            background-color: #f9fafb;
        }
        
        .modal-footer {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        
        .modal-follow-up {
            flex: 1;
        }
        
        .modal-follow-up p {
            margin: 5px 0;
            color: #374151;
        }
        
        .modal-follow-up .gujarati-text {
            font-size: 14px;
            color: #6b7280;
        }
        
        .modal-signature {
            text-align: right;
        }
        
        .modal-signature p {
            margin: 5px 0;
            color: #6b7280;
            font-size: 13px;
        }
        
        .modal-print-button {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
        }
        
        .modal-btn-print {
            background-color: #3b82f6;
            color: white;
            padding: 10px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.2s;
        }
        
        .modal-btn-print:hover {
            background-color: #2563eb;
        }
    </style>

    <div class="modal-prescription-container">
        <!-- Header Section -->
        <div class="modal-header">
            <h1>{{ $clinic->ClinicName }}</h1>
            <p>{{ $clinic->Address }}</p>
            <p>Contact: {{ $clinic->MobileNo }} | Email: {{ $clinic->Email }}</p>
        </div>

        <hr class="modal-divider">

        <!-- Patient Info Section -->
        <div class="modal-info-grid">
            <div class="modal-info-item">
                <strong>Name:</strong> 
                <span>{{ $patient->FirstName }} {{ $patient->MiddleName }} {{ $patient->LastName }}</span>
            </div>
            <div class="modal-info-item modal-text-right">
                <strong>Date:</strong> 
                <span>{{ $history->CreatedDate->format('d-m-Y h:i A') }}</span>
            </div>
            <div class="modal-info-item">
                <strong>Diagnosis:</strong> 
                <span>{{ $history->diseases->pluck('Name')->join(' - ') }}</span>
            </div>
            <div class="modal-info-item modal-text-right">
                <strong>Weight:</strong> 
                <span>{{ $patient->Weight }} kg</span>
            </div>
            <div class="modal-info-item">
                <strong>Address/City:</strong> 
                <span>{{ $patient->Address }}</span>
            </div>
            <div class="modal-info-item modal-text-right">
                <strong>Age:</strong> 
                <span>{{ $patient->AgeYear }} Years</span>
            </div>
            <div class="modal-info-item">
                <strong>Mobile:</strong> 
                <span>{{ $patient->MobileNo }}</span>
            </div>
        </div>

        <!-- Prescription Table -->
        <table class="modal-prescription-table">
            <thead>
                <tr>
                    <th>Medicine Name</th>
                    <th>Dose</th>
                    <th>Time of Administration</th>
                    <th>Quantity</th>
                    <th>Anupana</th>
                </tr>
            </thead>
            <tbody>
                @foreach($history->prescriptions as $prescription)
                <tr>
                    <td><strong>{{ $prescription->medicine->Name }}</strong> ({{ $prescription->MedicineFormName }})</td>
                    <td>{{ $prescription->Dose }}</td>
                    <td>{{ $prescription->TimeOfAdministration }}</td>
                    <td>{{ $prescription->Duration }}</td>
                    <td>{{ $prescription->Anupana }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Footer Section -->
        <div class="modal-footer">
            <div class="modal-follow-up">
               
                <p class="gujarati-text">ફરી બતાવવાની તારીખ: <u>{{ $history->NextAppointmentDate?->format('d-m-Y') ?? 'નક્કી નથી' }}</u></p>
            </div>
        </div>

        <!-- Print Button -->
        <div class="modal-print-button">
            <a href="{{ route('order.print', $history) }}" target="_blank" class="modal-btn-print">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z" />
                </svg>
                Print Prescription
            </a>
        </div>
    </div>
</div>