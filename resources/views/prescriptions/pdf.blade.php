<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Prescription-{{ $record->appointment->appointment_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        .header {
            border-bottom: 2px solid #0284c7;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        .clinic-name {
            font-size: 20px;
            font-weight: bold;
            color: #0284c7;
            margin: 0 0 4px 0;
        }
        .clinic-sub {
            font-size: 9px;
            color: #64748b;
            margin: 0;
        }
        .doc-box {
            text-align: right;
        }
        .doc-name {
            font-size: 13px;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
        }
        .doc-spec {
            font-size: 10px;
            color: #0284c7;
            font-weight: bold;
            margin: 2px 0;
        }
        .doc-sub {
            font-size: 9px;
            color: #64748b;
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .patient-table {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .patient-table td {
            padding: 6px 10px;
            font-size: 10px;
        }
        .patient-label {
            color: #64748b;
            text-transform: uppercase;
            font-size: 8px;
            font-weight: bold;
        }
        .patient-val {
            color: #0f172a;
            font-weight: bold;
        }
        .vitals-bar {
            background-color: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .vitals-bar td {
            padding: 6px 8px;
            text-align: center;
            font-size: 9px;
        }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #0369a1;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 3px;
            margin-top: 10px;
            margin-bottom: 6px;
        }
        .rx-sym {
            font-size: 24px;
            font-weight: bold;
            color: #0284c7;
            font-style: italic;
            margin-right: 5px;
        }
        .rx-table {
            margin-top: 8px;
            margin-bottom: 15px;
        }
        .rx-table th {
            background-color: #0284c7;
            color: #ffffff;
            font-size: 9px;
            text-transform: uppercase;
            padding: 6px 8px;
            text-align: left;
        }
        .rx-table td {
            padding: 7px 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
        }
        .rx-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            background-color: #e0f2fe;
            color: #0369a1;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        .notes-box {
            background-color: #fafafa;
            border: 1px solid #e5e7eb;
            padding: 8px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 10px;
        }
        .footer {
            margin-top: 30px;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
        }
        .sig-line {
            border-top: 1px dashed #94a3b8;
            width: 180px;
            margin-top: 40px;
            text-align: center;
            font-size: 9px;
            color: #64748b;
            padding-top: 4px;
        }
        .stamp-box {
            border: 2px dashed #0284c7;
            width: 120px;
            height: 60px;
            border-radius: 6px;
            text-align: center;
            color: #0284c7;
            font-size: 8px;
            font-weight: bold;
            padding-top: 20px;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <table>
            <tr>
                <td style="width: 60%; vertical-align: top;">
                    <h1 class="clinic-name">MediCare Health & Diagnostics</h1>
                    <p class="clinic-sub">
                        124 Health Sciences Avenue, Medical District<br>
                        Phone: +1 (800) 555-0199 • Email: info@medicare-clinic.com<br>
                        Reg. License: MC-CLINIC-2026-X • ISO 9001:2015 Certified
                    </p>
                </td>
                <td style="width: 40%; vertical-align: top;" class="doc-box">
                    <p class="doc-name">Dr. {{ $record->doctor->user->name }}</p>
                    <p class="doc-spec">{{ $record->doctor->specialization }}</p>
                    <p class="doc-sub">{{ $record->doctor->qualifications }}</p>
                    <p class="doc-sub">Dept of {{ $record->doctor->department->name }} • Room {{ $record->doctor->room_number }}</p>
                </td>
            </tr>
        </table>
    </div>

    <!-- Patient Info Banner -->
    <div class="patient-table">
        <table>
            <tr>
                <td>
                    <span class="patient-label">Patient Name</span><br>
                    <span class="patient-val">{{ $record->patient->name }}</span>
                </td>
                <td>
                    <span class="patient-label">Age / Gender</span><br>
                    <span class="patient-val">
                        @if($record->patient->date_of_birth)
                            {{ $record->patient->date_of_birth->age }} yrs / {{ ucfirst($record->patient->gender ?? 'N/A') }}
                        @else
                            {{ ucfirst($record->patient->gender ?? 'N/A') }}
                        @endif
                    </span>
                </td>
                <td>
                    <span class="patient-label">Blood Group</span><br>
                    <span class="patient-val">{{ $record->patient->blood_group ?? 'N/A' }}</span>
                </td>
                <td>
                    <span class="patient-label">Consultation Date</span><br>
                    <span class="patient-val">{{ $record->created_at->format('d M Y') }}</span>
                </td>
                <td>
                    <span class="patient-label">Ticket Ref</span><br>
                    <span class="patient-val">#{{ $record->appointment->appointment_number }}</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Vitals Signs Strip -->
    @if($record->blood_pressure || $record->pulse_rate || $record->temperature || $record->weight_kg)
    <div class="vitals-bar">
        <table>
            <tr>
                @if($record->blood_pressure)
                <td>
                    <span class="patient-label">Blood Pressure</span><br>
                    <strong style="color: #0284c7;">{{ $record->blood_pressure }}</strong> mmHg
                </td>
                @endif
                @if($record->pulse_rate)
                <td>
                    <span class="patient-label">Heart Pulse</span><br>
                    <strong style="color: #0284c7;">{{ $record->pulse_rate }}</strong> bpm
                </td>
                @endif
                @if($record->temperature)
                <td>
                    <span class="patient-label">Temperature</span><br>
                    <strong style="color: #0284c7;">{{ $record->temperature }}</strong> &deg;F
                </td>
                @endif
                @if($record->weight_kg)
                <td>
                    <span class="patient-label">Weight</span><br>
                    <strong style="color: #0284c7;">{{ $record->weight_kg }}</strong> kg
                </td>
                @endif
            </tr>
        </table>
    </div>
    @endif

    <!-- Symptoms & Clinical Diagnosis -->
    <table>
        <tr>
            <td style="width: 50%; vertical-align: top; padding-right: 10px;">
                <div class="section-title">Reported Symptoms & Chief Complaint</div>
                <div class="notes-box">
                    {{ $record->symptoms ?: 'Standard consultation checkup reported.' }}
                </div>
            </td>
            <td style="width: 50%; vertical-align: top; padding-left: 10px;">
                <div class="section-title">Clinical Diagnosis & Findings</div>
                <div class="notes-box">
                    <strong style="color: #0f172a;">{{ $record->diagnosis }}</strong>
                    @if($record->notes)
                        <p style="margin: 4px 0 0 0; color: #475569;">{{ $record->notes }}</p>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- Electronic Prescription (Rx) Section -->
    <div style="margin-top: 10px;">
        <span class="rx-sym">&#8478;</span>
        <span style="font-size: 13px; font-weight: bold; color: #0284c7; text-transform: uppercase;">Medical Prescription</span>
    </div>

    @if($record->prescriptions->count() > 0)
    <table class="rx-table">
        <thead>
            <tr>
                <th style="width: 30%;">Medication / Brand</th>
                <th style="width: 15%;">Dosage</th>
                <th style="width: 20%;">Frequency</th>
                <th style="width: 15%;">Duration</th>
                <th style="width: 20%;">Instructions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($record->prescriptions as $item)
            <tr>
                <td>
                    <strong>{{ $item->medicine_name }}</strong>
                </td>
                <td>{{ $item->dosage }}</td>
                <td><span class="badge">{{ $item->frequency }}</span></td>
                <td>{{ $item->duration }}</td>
                <td style="color: #475569;">{{ $item->instructions ?: 'As directed' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p style="font-size: 10px; color: #64748b; font-style: italic; margin-bottom: 15px;">No systemic medications prescribed during this visit.</p>
    @endif

    <!-- Medical Advice & Follow-Up -->
    @if($record->advice || $record->follow_up_date)
    <table style="margin-bottom: 15px;">
        <tr>
            <td style="width: 70%; vertical-align: top;">
                @if($record->advice)
                <div class="section-title">Dietary & Lifestyle Advice</div>
                <p style="font-size: 10px; color: #334155; margin: 0;">{{ $record->advice }}</p>
                @endif
            </td>
            <td style="width: 30%; vertical-align: top; text-align: right;">
                @if($record->follow_up_date)
                <div class="section-title" style="text-align: right;">Next Follow-Up</div>
                <p style="font-size: 11px; font-weight: bold; color: #0284c7; margin: 0;">
                    {{ $record->follow_up_date->format('l, F j, Y') }}
                </p>
                @endif
            </td>
        </tr>
    </table>
    @endif

    <!-- Footer & Signatures -->
    <div class="footer">
        <table>
            <tr>
                <td style="width: 40%; vertical-align: bottom;">
                    <div class="stamp-box">
                        CLINIC OFFICIAL STAMP
                    </div>
                    <p style="font-size: 8px; color: #94a3b8; margin-top: 6px;">
                        This document is a certified digital health record.
                    </p>
                </td>
                <td style="width: 20%;"></td>
                <td style="width: 40%; vertical-align: bottom; text-align: right;">
                    <div style="display: inline-block;">
                        <p style="font-size: 12px; font-family: cursive; color: #0369a1; margin: 0 0 5px 0;">Dr. {{ $record->doctor->user->name }}</p>
                        <div class="sig-line">
                            Attending Doctor's Signature<br>
                            License: MED-{{ $record->doctor->id }}884-X
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
