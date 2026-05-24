<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kartu Ujian - {{ $session->title }}</title>
    <style>
        /* SETUP KERTAS A4 */
        @page { 
            size: A4; 
            margin: 1cm; 
        }
        body { 
            font-family: 'Helvetica', sans-serif; 
            font-size: 11px; 
            color: #333;
        }

        /* CONTAINER KARTU */
        .card-container { 
            border: 1.5px solid #000; 
            padding: 15px; 
            margin-bottom: 0.8cm; 
            height: auto; 
            min-height: 110mm;
            box-sizing: border-box; 
            position: relative; 
            width: 100%; 
            page-break-inside: avoid !important; 
        }

        /* HEADER KOP SURAT */
        .header { 
            display: table; 
            width: 100%; 
            border-bottom: 2px solid #000; 
            padding-bottom: 5px; 
        }
        .logo { 
            display: table-cell; 
            width: 60px; 
            vertical-align: middle; 
            text-align: center;
        }
        .logo img { 
            width: 55px; 
            height: auto; 
        }
        .header-text { 
            display: table-cell; 
            text-align: center; 
            vertical-align: middle; 
        }
        .header-text h1 { 
            font-size: 13px; 
            font-weight: bold; 
            margin: 0;
            text-transform: uppercase;
        }
        .header-text h2 { 
            font-size: 11px; 
            font-weight: bold; 
            margin: 2px 0; 
            text-transform: uppercase;
        }
        .header-text p {
            font-size: 9px;
            margin: 0;
        }

        /* CONTENT AREA */
        .content { 
            margin-top: 10px; 
            display: table; 
            width: 100%; 
        }
        .photo { 
            display: table-cell; 
            width: 75px; 
            vertical-align: top; 
        }
        .photo-box { 
            width: 65px; 
            height: 85px; 
            border: 1px solid #999; 
            text-align: center; 
            line-height: 85px; 
            color: #666; 
            font-size: 10px;
            background: #f0f0f0;
            font-weight: bold;
        }
        .details { 
            display: table-cell; 
            vertical-align: top; 
            padding-left: 15px; 
        }
        .details table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        .details td { 
            padding: 2px 0; 
            vertical-align: top; 
            font-size: 10px;
        }
        .details .label { 
            width: 110px; 
            font-weight: bold;
        }
        
        /* TIMETABLE SCHEDULE */
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            font-size: 9px;
        }
        .schedule-table th, .schedule-table td {
            border: 1px solid #333;
            padding: 4px 6px;
            text-align: left;
        }
        .schedule-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8px;
        }

        /* FOOTER TANDA TANGAN */
        .footer-container {
            margin-top: 15px;
            width: 100%;
            display: table;
        }
        .footer-left {
            display: table-cell;
            width: 65%;
        }
        .footer-right {
            display: table-cell;
            width: 35%;
            text-align: center;
            font-size: 9px;
        }
        .footer-right p {
            margin: 0;
            padding: 0;
        }
        .footer-right .name {
            margin-top: 40px;
            font-weight: bold;
            text-decoration: underline;
        }
        
        /* PAGE BREAK */
        .page-break { 
            page-break-after: always; 
        }
    </style>
</head>
<body>
    @php
        // Group all allocations by student to print exactly 1 dynamic schedule card per student
        $groupedAllocations = $allocations->groupBy('student_id');
    @endphp

    @foreach($groupedAllocations as $studentId => $studentAllocations)
        @php
            $firstAlloc = $studentAllocations->first();
            $student = $firstAlloc->student;
        @endphp

        <div class="card-container">
            <!-- HEADER -->
            <div class="header">
                <!-- LOGO SEKOLAH (KIRI) -->
                <div class="logo">
                    <img src="{{ public_path('images/logo-sekolah.png') }}" alt="Logo">
                </div>
                
                <div class="header-text">
                    <h1>KARTU PESERTA UJIAN</h1>
                    <h2>SMA NEGERI 3 BONTANG</h2>
                    <p>{{ strtoupper($session->title) }}</p>
                    <p>Tahun Pelajaran {{ date('Y') }}/{{ date('Y')+1 }}</p>
                </div>
                
                <!-- LOGO KANAN -->
                <div class="logo"></div>
            </div>

            <!-- CONTENT -->
            <div class="content">
                <div class="photo">
                    <div class="photo-box">3 x 4</div>
                </div>
                <div class="details">
                    <table>
                        <!-- Data Siswa -->
                        <tr>
                            <td class="label">Nama Peserta</td>
                            <td>: <b>{{ strtoupper($student->name) }}</b></td>
                        </tr>
                        <tr>
                            <td class="label">Nomor Induk (NIS)</td>
                            <td>: {{ $student->nis }}</td>
                        </tr>
                        <tr>
                            <td class="label">Kelas</td>
                            <td>: {{ $student->studentClass?->name ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- TIMETABLE JADWAL UJIAN -->
            <table class="schedule-table">
                <thead>
                    <tr>
                        <th style="width: 25%;">Hari, Tanggal</th>
                        <th style="width: 25%;">Waktu (Sesi)</th>
                        <th style="width: 25%;">Mata Pelajaran</th>
                        <th style="width: 15%;">Ruangan</th>
                        <th style="width: 10%; text-align: center;">Meja</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($studentAllocations as $alloc)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($alloc->schedule->exam_date)->translatedFormat('l, d M Y') }}</td>
                            <td>
                                {{ substr($alloc->schedule->timeSession->start_time, 0, 5) }} - {{ substr($alloc->schedule->timeSession->end_time, 0, 5) }}
                                <br>
                                <span style="font-size: 8px; color: #666;">({{ $alloc->schedule->timeSession->name }})</span>
                            </td>
                            <td style="font-weight: bold;">{{ $alloc->schedule->subject->name }}</td>
                            <td>{{ $alloc->room->name }}</td>
                            <td style="text-align: center; font-weight: bold;">{{ str_pad($alloc->desk_number, 2, '0', STR_PAD_LEFT) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- FOOTER (Tanda Tangan) -->
            <div class="footer-container">
                <div class="footer-left"></div>
                <div class="footer-right">
                    <p>Bontang, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                    <p>Kepala Sekolah,</p>
                    <p class="name">...................................</p>
                    <p>NIP. ...........................</p>
                </div>
            </div>
        </div>

        <!-- Page Break setiap 2 kartu agar muat rapi di A4 portrait -->
        @if(($loop->iteration) % 2 == 0 && !$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>