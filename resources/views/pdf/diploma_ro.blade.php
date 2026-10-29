{{-- Romanian diploma, matches public/diploma_Artistul_Dunării_2026.pdf.
     Layers: sponsor column (diploma_ro_sponsors.jpg), waves (diploma_ro_bg.jpeg) on a
     flat panel matching its top colour, signature, DAM logo, then the text
     (positions in pt, A4 landscape). --}}
@php
    $s = $strings['ro'];
    $details = $diploma->work->details;
@endphp
<!doctype html>
<html lang="ro">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Diplomă {{ $diploma->contest->name }}</title>
    <style>
        @font-face {
            font-family: 'Crimson Text';
            font-style: normal;
            font-weight: normal;
            src: url('{{ public_path('fonts/CrimsonText-Regular.ttf') }}') format('truetype');
        }

        @page {
            margin: 0;
        }

        html,
        body {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Crimson Text', 'DejaVu Serif', serif;
            font-size: 18pt;
            line-height: 0.839;
            color: #1e3a8a;
        }

        .sponsors {
            position: absolute;
            top: 0;
            left: 0;
            width: 155.5pt;
            height: 594pt;
        }

        .panel {
            position: absolute;
            top: 0;
            left: 155.5pt;
            width: 686.4pt;
            height: 594pt;
            background-color: #f7f7f7;
        }

        /* full panel width, bottom-aligned, keeping the image's 924x578 ratio */
        .waves {
            position: absolute;
            top: 164.6pt;
            left: 155.5pt;
            width: 686.4pt;
            height: 429.4pt;
        }

        .signature-image {
            position: absolute;
            top: 417.5pt;
            left: 288.8pt;
            width: 146pt;
            height: 84.5pt;
        }

        /* dam_logo_glow.png is dam_logo.png padded by 100px with a white glow baked in
           (DomPDF has no box-shadow/filter); the logo itself sits where the reference has it */
        .dam-logo {
            position: absolute;
            top: 467.8pt;
            left: 650.1pt;
            width: 176.1pt;
            height: 111.6pt;
        }

        .content {
            position: absolute;
            top: 24.3pt;
            left: 196pt;
            width: 600pt;
            text-align: center;
        }

        .title {
            font-size: 36pt;
            line-height: 0.734;
            color: #12457e;
        }

        /* body text is centred ~4pt left of the title in the reference */
        .awarded {
            margin-top: 32.7pt;
            padding-right: 8.4pt;
        }

        .details {
            margin-top: 35.6pt;
            padding-right: 8.4pt;
        }

        .signature {
            position: absolute;
            top: 376.5pt;
            left: 246pt;
            width: 200pt;
            text-align: center;
            line-height: 0.711;
        }

        .signature .name {
            color: #000;
        }

        .signature .position {
            padding-left: 12.4pt;
        }
    </style>
</head>

<body>
<img class="sponsors" src="{{ public_path('diploma_ro_sponsors.jpg') }}" alt="">
<div class="panel"></div>
<img class="waves" src="{{ public_path('diploma_ro_bg.jpeg') }}" alt="">
<img class="signature-image" src="{{ public_path('diploma_ro_signature.png') }}" alt="">
<img class="dam-logo" src="{{ public_path('dam_logo_glow.png') }}" alt="DAM">

<div class="content">
    <div class="title">{{ mb_strtoupper($s['title_line1']) }}<br>{{ mb_strtoupper($s['title_line2']) }}</div>

    <div class="awarded">
        {{ $s['awarded_to'] }}<br>
        {{ $details->full_name }}<br>
        pentru lucrarea<br>
        {{ $diploma->work->name }}
    </div>

    <div class="details">
        {{ $s['from'] }} {{ $details->school }}<br>
        {{ $s['coordinated_by'] }} {{ $details->mentor }}<br>
        {{ $s['for_participation'] }} "{{ $diploma->contest->name }}"{{ $s['national_phase'] }}
        @if ($diploma->work->award_rank && in_array($diploma->work->award_rank, [1, 2, 3]))
            <br>{{ $s['prize'] }} {{ $diploma->work->award_rank }} {{ $s['in_category'] }} {{ $details->age_group }} {{ $s['years'] }}.
        @elseif (in_array($diploma->work->rank, [1, 2, 3]))
            <br>{{ $s['prize'] }} {{ $diploma->work->rank }} {{ $s['in_category'] }} {{ $details->age_group }} {{ $s['years'] }} și {{ $s['subcategory'] }} {{ $details->type }}.
        @endif
    </div>
</div>

<div class="signature">
    <div class="name">Irina Șerban-Atanasiu</div>
    <div class="position">{{ $s['president'] }}</div>
</div>
</body>

</html>
