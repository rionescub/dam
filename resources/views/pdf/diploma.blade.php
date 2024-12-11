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
            margin-bottom: 20px;
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
            width: 150px;
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
                <img src="{{ asset('mmap.png') }}" class="mx-auto" alt="Ministerul Apelor">
                <img src="{{ asset('gwp.jpg') }}" class="mx-auto" alt="GWP ROmania">
            </div>
        </div>

        <!-- Right Section -->

        <div class="diploma-right">
            <div class="title">
                Diplomă <br> de participare</span>
            </div>

            <div class="subtitle">
                <span class="">Se acordă elevului/elevei</span><br />
                <span class=""><strong>{{ $diploma->work->details->full_name }}</strong></span>
            </div>

            <div class="details">
                de la <span class="font-bold underline"><strong>{{ $diploma->work->details->school }}</strong>
                </span>
                <br />coordonat/ă de prof. <span class="font-bold"><strong>
                        {{ $diploma->work->details->mentor }}</strong> </span>
                <br />pentru participarea la concursul <strong> "{{ $diploma->contest->name }}"</strong> - faza
                națională.</span>
                @php
                    $types = [
                        'img' => 'Imagine',
                        'artwork' => 'Lucrare de artă',
                        'video' => 'video',
                    ];
                    if ($diploma->work->award_rank && in_array($diploma->work->award_rank, [1, 2, 3])) {
                        echo '<br /><strong> Premiul ' .
                            $diploma->work->award_rank .
                            '</strong>  la categoria <span class="font-bold">' .
                            $diploma->work->details->age_group .
                            ' ani </span>';
                    } elseif (in_array($diploma->work->rank, [1, 2, 3])) {
                        echo '<br />Premiul ' .
                            $diploma->work->rank .
                            ' la categoria <span class="font-bold">' .
                            $diploma->work->details->age_group .
                            ' ani </span>, subcategorie <span class="font-bold">' .
                            $types[$diploma->work->details->type] .
                            ' </span>';
                    }
                @endphp
                <br /><br /><br /><span class="font-bold text-blue-900 text-2xl italic mt-10">Asociația Parteneriatul
                    Global al Apei
                    din
                    România </span>


            </div>

            <!-- Signature Section -->
            <div class="signature-section">
                <img src="{{ asset('signature.png') }}" class="mx-auto" alt="Signature">
                <div class="signature">Procop Ionuț</div>
                <div class="position">Președinte</div>
            </div>

            <div class="boat">
                <img src="{{ asset('boat.png') }}" alt="Boat">
            </div>

            <!-- Participant Info Bottom Right -->
            <div class="footer">
                <div>
                    <span class="font-medium">Nume Lucrare</span>
                    <br />
                    <span class="underline">{{ $diploma->work->name }}</span>

                </div>
            </div>
        </div>
    </div>

</body>

</html>
