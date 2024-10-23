<style>
    @page {
        size: A4 landscape;
        margin: 10mm;
    }

    body {
        margin: 0;
        padding: 0;
        border: 1mm solid #991B1B;
        height: 188mm;
    }

    .border-pattern {
        position: absolute;
        left: 4mm;
        top: -6mm;
        height: 200mm;
        width: 267mm;
        border: 1mm solid #991B1B;
        /* http://www.heropatterns.com/ */
        background-color: #d6d6e4;
        background-image: url("data:image/svg+xml,%3Csvg width='16' height='16' viewBox='0 0 16 16' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 0h16v2h-6v6h6v8H8v-6H2v6H0V0zm4 4h2v2H4V4zm8 8h2v2h-2v-2zm-8 0h2v2H4v-2zm8-8h2v2h-2V4z' fill='%23991B1B' fill-opacity='1' fill-rule='evenodd'/%3E%3C/svg%3E");
    }

    .content {
        position: absolute;
        left: 10mm;
        top: 10mm;
        height: 178mm;
        width: 245mm;
        border: 1mm solid #991B1B;
        background: white;
    }

    .inner-content {
        border: 1mm solid #1b2899;
        margin: 4mm;
        padding: 10mm;
        height: 148mm;
        text-align: center;
    }

    h1 {
        text-transform: uppercase;
        font-size: 48pt;
        margin-bottom: 0;
    }

    h2 {
        font-size: 24pt;
        margin-top: 0;
        padding-bottom: 1mm;
        display: inline-block;
        border-bottom: 1mm solid #1b2899;
    }

    h2::after {
        content: "";
        display: block;
        padding-bottom: 4mm;
        border-bottom: 1mm solid #1b2899;
    }

    h3 {
        font-size: 20pt;
        margin-bottom: 0;
        margin-top: 10mm;
    }

    p {
        font-size: 16pt;
    }

    .badge {
        width: 40mm;
        height: 40mm;
        position: absolute;
        right: 10mm;
        bottom: 10mm;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='currentColor'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z' /%3E%3C/svg%3E");
    }
</style>

<body>
    <div class="border-pattern">
        <div class="content">
            <div class="inner-content">
                <h1>Diploma</h1>
                <h3>This Certificate Is Proudly Presented To</h3>
                <p>{{ $workDetails->full_name }}</p>
                <p>From {{ $workDetails->school }}, {{ $workDetails->city }}</p>
                <p>For Placing {{ $diploma->work->rank }}</p>
                <p>with {{ $diploma->work->name }}</p>
                <p>in {{ $diploma->contest->name }}</p>
                <p>On {{ $diploma->created_at->format('M d, Y') }}</p>
            </div>
        </div>
    </div>
</body>