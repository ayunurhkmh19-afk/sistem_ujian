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
        }

        /* CONTAINER KARTU */
        .card-container { 
            border: 1px solid #000; 
            padding: 15px; 
            margin-bottom: 1.2cm; 
            height: 80mm; /* Tinggi kartu fixed */
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
            font-size: 14px; 
            font-weight: bold; 
            margin: 0;
            text-transform: uppercase;
        }
        .header-text h2 { 
            font-size: 12px; 
            font-weight: bold; 
            margin: 2px 0; 
            text-transform: uppercase;
        }
        .header-text p {
            font-size: 10px;
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
            width: 70px; 
            vertical-align: top; 
        }
        .photo-box { 
            width: 60px; 
            height: 80px; 
            border: 1px solid #ccc; 
            text-align: center; 
            line-height: 80px; 
            color: #999; 
            font-size: 10px;
            background: #f0f0f0;
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
            padding: 3px 0; 
            vertical-align: top; 
        }
        .details .label { 
            width: 100px; 
        }
        
        /* FOOTER TANDA TANGAN */
        .footer { 
            position: absolute; 
            bottom: 15px; 
            right: 15px; 
            text-align: center; 
        }
        .footer p { 
            margin: 0; 
            padding: 0; 
        }
        .footer .name { 
            margin-top: 50px; 
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
    @foreach($allocations as $index => $allocation)
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
                
                <!-- LOGO KANAN (Kosongkan atau isi jika ada logo dinas/tutwuri) -->
                <div class="logo"></div>
            </div>

            <!-- CONTENT -->
            <div class="content">
                <div class="photo">
                    <div class="photo-box">FOTO</div>
                </div>
                <div class="details">
                    <table>
                        <!-- Data Siswa -->
                        <tr>
                            <td class="label">Nama Peserta</td>
                            <td>: <b>{{ strtoupper($allocation->student->name) }}</b></td>
                        </tr>
                        <tr>
                            <td class="label">Nomor Induk (NIS)</td>
                            <td>: {{ $allocation->student->nis }}</td>
                        </tr>
                        <tr>
                            <td class="label">Kelas</td>
                            <td>: {{ $allocation->student->class }}</td>
                        </tr>
                        <tr>
                            <td class="label">Ruang Ujian</td>
                            <td>: {{ $allocation->room->name }}</td>
                        </tr>
                        <tr>
                            <td class="label">Nomor Meja</td>
                            <td>: <b style="font-size: 14px;">{{ str_pad($allocation->desk_number, 2, '0', STR_PAD_LEFT) }}</b></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- FOOTER (Tanda Tangan) -->
            <div class="footer">
                <p>Kepala Sekolah</p>
                <p class="name">...................................</p>
                <p>NIP. ...........................</p>
            </div>
        </div>

        <!-- Page Break setiap 3 kartu (agar muat di A4 dan rapi) -->
        @if(($index + 1) % 3 == 0)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>