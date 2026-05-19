<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Death Certificate - {{ $member->firstname }} {{ $member->lastname }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            body { margin: 0; padding: 0; }
            .no-print { display: none !important; }
            .certificate-page { box-shadow: none !important; margin: 0 !important; }
        }

        body {
            background: #e8e8e8;
            font-family: 'Poppins', sans-serif;
        }

        .certificate-page {
            width: 210mm;
            min-height: 297mm;
            margin: 30px auto;
            background: #fff;
            padding: 0;
            box-shadow: 0 0 30px rgba(0,0,0,0.15);
            position: relative;
            overflow: hidden;
        }

        .cert-border-outer {
            border: 3px solid #1a1a2e;
            margin: 12mm;
            padding: 2mm;
            min-height: calc(297mm - 24mm);
        }

        .cert-border-inner {
            border: 1px solid #1a1a2e;
            padding: 15mm 12mm;
            min-height: calc(297mm - 28mm);
            position: relative;
        }

        .cert-corner {
            position: absolute;
            width: 40px;
            height: 40px;
            border-color: #8b7355;
            border-style: solid;
        }
        .cert-corner.tl { top: 5px; left: 5px; border-width: 3px 0 0 3px; }
        .cert-corner.tr { top: 5px; right: 5px; border-width: 3px 3px 0 0; }
        .cert-corner.bl { bottom: 5px; left: 5px; border-width: 0 0 3px 3px; }
        .cert-corner.br { bottom: 5px; right: 5px; border-width: 0 3px 3px 0; }

        .cert-header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #1a1a2e;
            padding-bottom: 20px;
        }

        .cert-title {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            font-weight: 700;
            color: #1a1a2e;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .cert-subtitle {
            font-size: 14px;
            color: #555;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .cert-ref {
            font-size: 11px;
            color: #888;
            margin-top: 10px;
        }

        .cert-body {
            margin-top: 20px;
        }

        .cert-photo-section {
            text-align: center;
            margin-bottom: 25px;
        }

        .cert-photo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #1a1a2e;
        }

        .cert-photo-placeholder {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 3px solid #1a1a2e;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 42px;
            font-family: 'Playfair Display', serif;
            color: #1a1a2e;
            background: #f0ece3;
        }

        .cert-name {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            font-weight: 600;
            color: #1a1a2e;
            text-align: center;
            margin: 15px 0 25px;
        }

        .cert-details-table {
            width: 100%;
            border-collapse: collapse;
        }

        .cert-details-table td {
            padding: 10px 15px;
            font-size: 13px;
            border-bottom: 1px dotted #ccc;
        }

        .cert-details-table .label {
            font-weight: 600;
            color: #1a1a2e;
            width: 200px;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 1px;
        }

        .cert-details-table .value {
            color: #333;
        }

        .cert-statement {
            margin-top: 30px;
            padding: 20px;
            background: #faf8f5;
            border-left: 4px solid #1a1a2e;
            font-size: 13px;
            line-height: 1.8;
            color: #333;
        }

        .cert-footer {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .cert-sign-block {
            text-align: center;
            min-width: 200px;
        }

        .cert-sign-line {
            border-top: 1px solid #333;
            width: 180px;
            margin: 0 auto 5px;
        }

        .cert-sign-label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .cert-date-issued {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #888;
        }

        .cert-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 100px;
            font-family: 'Playfair Display', serif;
            color: rgba(0,0,0,0.03);
            font-weight: 700;
            white-space: nowrap;
            pointer-events: none;
            z-index: 0;
        }

        .cert-body, .cert-header, .cert-footer {
            position: relative;
            z-index: 1;
        }
    </style>
</head>
<body>

<div class="no-print text-center py-3" style="background: #1a1a2e;">
    <a href="{{ route('user.deceased') }}" class="btn btn-outline-light me-2">
        <i class="bi bi-arrow-left"></i> Back to List
    </a>
    <button onclick="window.print()" class="btn btn-light">
        <i class="bi bi-printer"></i> Print Certificate
    </button>
</div>

<div class="certificate-page">
    <div class="cert-border-outer">
        <div class="cert-border-inner">
            <div class="cert-corner tl"></div>
            <div class="cert-corner tr"></div>
            <div class="cert-corner bl"></div>
            <div class="cert-corner br"></div>

            <div class="cert-watermark">NEXTCOME</div>

            <div class="cert-header">
                <div class="cert-title">Death Certificate</div>
                <div class="cert-subtitle">Official Record of Deceased Family Member</div>
                <div class="cert-ref">Ref: NC-DC-{{ str_pad($member->id, 6, '0', STR_PAD_LEFT) }} &bull; Family: {{ $familyOwner->familyid ?? 'N/A' }}</div>
            </div>

            <div class="cert-body">
                <div class="cert-photo-section">
                    @if($member->photo)
                        <img src="{{ asset('assets/front-end/Memberimgs/' . $member->photo) }}" class="cert-photo" alt="Photo">
                    @else
                        <div class="cert-photo-placeholder">
                            {{ strtoupper(substr($member->firstname, 0, 1)) }}{{ strtoupper(substr($member->lastname, 0, 1)) }}
                        </div>
                    @endif
                </div>

                <div class="cert-name">{{ $member->firstname }} {{ $member->lastname }}</div>

                @php
                    $ageFormatted = $member->getAgeAtDeathFormatted();
                    $genderStr = $member->gender == 1 ? 'Male' : ($member->gender == 2 ? 'Female' : 'Other');
                @endphp

                <table class="cert-details-table">
                    <tr>
                        <td class="label">Full Name</td>
                        <td class="value">{{ $member->firstname }} {{ $member->lastname }}</td>
                    </tr>
                    <tr>
                        <td class="label">Gender</td>
                        <td class="value">{{ $genderStr }}</td>
                    </tr>
                    <tr>
                        <td class="label">Date of Birth</td>
                        <td class="value">{{ $member->birthdate ? \Carbon\Carbon::parse($member->birthdate)->format('F d, Y') : 'Not Recorded' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Place of Birth</td>
                        <td class="value">{{ $member->birthplace ?: 'Not Recorded' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Date of Death</td>
                        <td class="value">{{ $member->deathdate ? \Carbon\Carbon::parse($member->deathdate)->format('F d, Y') : 'Not Recorded' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Place of Death</td>
                        <td class="value">{{ $member->deathplace ?: 'Not Recorded' }}</td>
                    </tr>
                    @if($ageFormatted)
                    <tr>
                        <td class="label">Age at Death</td>
                        <td class="value">{{ $ageFormatted }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="label">Profession</td>
                        <td class="value">{{ $member->profession ?: 'Not Recorded' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Village / Hometown</td>
                        <td class="value">{{ $member->village ?: ($member->home_town ?: 'Not Recorded') }}</td>
                    </tr>
                    @if($parentMember)
                    <tr>
                        <td class="label">Parent / Guardian</td>
                        <td class="value">{{ $parentMember->firstname }} {{ $parentMember->lastname }}</td>
                    </tr>
                    @endif
                    @if($spouseMember)
                    <tr>
                        <td class="label">Spouse</td>
                        <td class="value">{{ $spouseMember->firstname }} {{ $spouseMember->lastname }}</td>
                    </tr>
                    @endif
                </table>

                @if($member->bio)
                <div class="cert-statement">
                    <strong>Biography:</strong> {{ $member->bio }}
                </div>
                @endif

                <div class="cert-statement">
                    This is to certify that <strong>{{ $member->firstname }} {{ $member->lastname }}</strong>
                    @if($member->birthdate)
                        , born on <strong>{{ \Carbon\Carbon::parse($member->birthdate)->format('F d, Y') }}</strong>
                        @if($member->birthplace) in <strong>{{ $member->birthplace }}</strong> @endif
                    @endif
                    , passed away
                    @if($member->deathdate)
                        on <strong>{{ \Carbon\Carbon::parse($member->deathdate)->format('F d, Y') }}</strong>
                    @endif
                    @if($member->deathplace)
                        in <strong>{{ $member->deathplace }}</strong>
                    @endif
                    @if($ageFormatted)
                        at the age of <strong>{{ $ageFormatted }}</strong>
                    @endif
                    . This certificate is generated from the NEXTCOME Family Tree system for family record purposes.
                </div>

                <div class="cert-footer">
                    <div class="cert-sign-block">
                        <div class="cert-sign-line"></div>
                        <div class="cert-sign-label">Family Representative</div>
                    </div>
                    <div class="cert-sign-block">
                        <div class="cert-sign-line"></div>
                        <div class="cert-sign-label">Date</div>
                    </div>
                    <div class="cert-sign-block">
                        <div class="cert-sign-line"></div>
                        <div class="cert-sign-label">Authorized Signature</div>
                    </div>
                </div>

                <div class="cert-date-issued">
                    Certificate generated on {{ \Carbon\Carbon::now()->format('F d, Y \a\t h:i A') }} &bull; NEXTCOME Family Tree System
                </div>
            </div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</body>
</html>
