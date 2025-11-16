@php
    $teamToLang = [
        1 => 'ro',
        2 => 'hu',
        3 => 'sl',
        4 => 'au',
        5 => 'ua',
        6 => 'z',
        7 => 'rs',
        8 => 'sk',
    ];

    $currentTeamId = optional(auth()->user())->current_team_id ?? 1;
    $lang = $teamToLang[$currentTeamId] ?? 'ro';

    $strings = [
        'ro' => [
            'title_line1' => 'Diplomă',
            'title_line2' => 'de participare',
            'awarded_to' => 'Se acordă elevului/elevei',
            'from' => 'de la',
            'coordinated_by' => 'coordonat/ă de prof.',
            'for_participation' => 'pentru participarea la concursul',
            'national_phase' => ' - faza națională.',
            'prize' => 'Premiul',
            'in_category' => 'la categoria',
            'years' => 'ani',
            'subcategory' => 'subcategorie',
            'president' => 'Președinte',
            'work_name' => 'Nume Lucrare',
        ],
        'hu' => [
            'title_line1' => 'Oklevél',
            'title_line2' => 'részvételről',
            'awarded_to' => 'Oklevelet kap',
            'from' => 'a(z)',
            'coordinated_by' => 'felkészítő tanár',
            'for_participation' => 'a versenyen való részvételért',
            'national_phase' => ' — országos szakasz.',
            'prize' => 'Díj',
            'in_category' => 'kategóriában',
            'years' => 'éves',
            'subcategory' => 'alkategória',
            'president' => 'Elnök',
            'work_name' => 'Mű címe',
        ],
        'sl' => [
            'title_line1' => 'Priznanje',
            'title_line2' => 'za udeležbo',
            'awarded_to' => 'Se podeli',
            'from' => 'iz',
            'coordinated_by' => 'mentor/ica',
            'for_participation' => 'za sodelovanje na tekmovanju',
            'national_phase' => ' — državno raven.',
            'prize' => 'Nagrada',
            'in_category' => 'v kategoriji',
            'years' => 'let',
            'subcategory' => 'podkategorija',
            'president' => 'Predsednik',
            'work_name' => 'Naslov dela',
        ],
        'au' => [
            'title_line1' => 'Teilnahme',
            'title_line2' => 'Zertifikat',
            'awarded_to' => 'Wird verliehen an',
            'from' => 'von',
            'coordinated_by' => 'betreut von',
            'for_participation' => 'für die Teilnahme am Wettbewerb',
            'national_phase' => ' — Bundesfinale.',
            'prize' => 'Preis',
            'in_category' => 'in der Kategorie',
            'years' => 'Jahre',
            'subcategory' => 'Unterkategorie',
            'president' => 'Präsident',
            'work_name' => 'Werktitel',
        ],
        'z' => [
            'title_line1' => 'Certificate',
            'title_line2' => 'of Participation',
            'awarded_to' => 'Awarded to',
            'from' => 'from',
            'coordinated_by' => 'coordinated by',
            'for_participation' => 'for participation in the contest',
            'national_phase' => ' — national stage.',
            'prize' => 'Prize',
            'in_category' => 'in the category',
            'years' => 'years',
            'subcategory' => 'subcategory',
            'president' => 'President',
            'work_name' => 'Work Title',
        ],
    ];
@endphp

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<!doctype html>
<html lang="ro">

<head>
    <meta charset="utf-8">
    <title>Diplomă {{ $diploma->contest->name }}</title>
    <style>
        html {
            padding: 0px;
            margin: 0px;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            height: 100vh;
            width: 100%;
            padding: 0px;
            font-family: 'DejaVu Sans', sans-serif;
            background-color: transparent;
        }

        .diploma-container {
            width: 100vw;
            heigh: calc(100vh);
            background: transparent;
            position: relative;
            display: block;
            padding: 0px;
            border-radius: 8px;
            overflow: inherit;
        }

        .diploma-left {
            width: 197px;
            height: 100%;
            float: left;
            background-color: #fff;
            border-right: 3px solid #1d4ed8;
            margin-left: 3px;
        }

        .diploma-right {
            width: calc(100% - 200px);
            height: 100%;
            float: left;
            background-image: url('{{ url('waves.png') }}');
            background-size: contain;
            background-position: bottom center;
            background-repeat: no-repeat;
            background-color: #c2d2ff;
            border-left: 3px solid #1d4ed8;
        }


        .logo {
            text-align: center;
            margin-bottom: 10px;
        }

        .boat {
            position: absolute;
            bottom: 150px;
            left: 150px;
            width: 200px;
        }

        .boat img {
            height: 100px;
            float: left;
        }

        .logo {
            display: block;
            max-width: 250px;
            text-align: center
        }

        .logo img {
            height: auto;
            width: 120px;
            margin: 40px auto;
            diplay: block;
        }

        .title {
            text-align: center;
            color: #1d4ed8;
            font-size: 48px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 30px;
            margin-top: 50px;
        }

        .subtitle {
            text-align: center;
            font-size: 14px;
            color: #1e3a8a;
            margin-bottom: 30px;
            margin-top: 30px;
        }

        .recipient-name {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            color: #1d4ed8;
            text-transform: uppercase;
            margin: 30px 0;
        }

        .details {
            text-align: center;
            font-size: 14px;
            color: #1e3a8a;
            margin: 0px 40px;
            line-height: 1.6;
        }

        .details strong {
            font-weight: bold;
        }

        .signature-section {
            text-align: center;
            margin-top: 10px;
        }

        .signature {
            font-size: 18px;
            font-style: italic;
            color: #1d4ed8;
            margin-top: 10px;
        }

        .signature-section img {
            margin: 0 auto;
            width: 100px;
            display: block;
        }

        .position {
            font-size: 16px;
            color: #1e3a8a;
        }

        .footer {
            position: absolute;
            bottom: 50px;
            right: 50px;
            min-width: 400px;
            font-size: 12px;
            color: #1e3a8a;
        }
    </style>
</head>

<body>
<div class="background"></div>
<div class="diploma-container">
    <div class="diploma-left">
        <div class="logo">
            <img src="{{ asset('dam_logo.png') }}" class="mx-auto" alt="DAM ">
            <img src="{{ asset('ipcdr.png') }}" class="mx-auto" alt="ICPDR IKSD">
            <img src="{{ asset('erste.svg') }}" class="mx-auto" alt="Erste">
            <img src="{{ asset('gwp.png') }}" class="mx-auto" alt="GWP">

            @if ($currentTeamId == 1)
                <img src="{{ asset('mmap.png') }}" class="mx-auto" alt="Ministerul Apelor">
                <img src="{{ asset('gwp.jpg') }}" class="mx-auto" alt="GWP ROmania">
            @endif
        </div>
    </div>

    <!-- Right Section -->

    <div class="diploma-right">
        <div class="title">
            {{ $strings[$lang]['title_line1'] }} <br> {{ $strings[$lang]['title_line2'] }}
        </div>

        <div class="subtitle">
            <span>{{ $strings[$lang]['awarded_to'] }}</span><br />
            <span><strong>{{ $diploma->work->details->full_name }}</strong></span>
        </div>

        <div class="details">
            {{ $strings[$lang]['from'] }} <span class="font-bold underline"><strong>{{ $diploma->work->details->school }}</strong></span>
            <br />{{ $strings[$lang]['coordinated_by'] }} <span class="font-bold"><strong>{{ $diploma->work->details->mentor }}</strong></span>
            <br />{{ $strings[$lang]['for_participation'] }} <strong>"{{ $diploma->contest->name }}"</strong>{{ $strings[$lang]['national_phase'] }}
            @php
                if ($diploma->work->award_rank && in_array($diploma->work->award_rank, [1, 2, 3])) {
                    echo '<br /><strong>' . $strings[$lang]['prize'] . ' ' . $diploma->work->award_rank .
                        '</strong> ' . $strings[$lang]['in_category'] . ' <span class="font-bold">' .
                        $diploma->work->details->age_group . ' ' . $strings[$lang]['years'] . '</span>';
                } elseif (in_array($diploma->work->rank, [1, 2, 3])) {
                    echo '<br />' . $strings[$lang]['prize'] . ' ' . $diploma->work->rank .
                        ' ' . $strings[$lang]['in_category'] . ' <span class="font-bold">' .
                        $diploma->work->details->age_group . ' ' . $strings[$lang]['years'] . '</span>, ' .
                        $strings[$lang]['subcategory'] . ' <span class="font-bold">' .
                        $diploma->work->details->type . '</span>';
                }
            @endphp
        </div>

        <div class="signature-section">

            @if ($currentTeamId == 1)
                <img src="{{ asset('signature.png') }}" class="mx-auto" alt="Signature">
                <div class="signature">Procop Ionuț</div>
            @endif
            <div class="position">{{ $strings[$lang]['president'] }}</div>
        </div>

        <div class="footer">
            <div>
                <span class="font-medium">{{ $strings[$lang]['work_name'] }}</span><br />
                <span class="underline">{{ $diploma->work->name }}</span>
            </div>
        </div>

    </div>
</div>

</body>

</html>
