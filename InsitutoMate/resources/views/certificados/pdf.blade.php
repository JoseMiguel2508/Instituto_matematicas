<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificado de Conclusión</title>
    <style>
        @page { margin: 0; size: a4 landscape; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            background: #fff;
            width: 100%;
            height: 100%;
            display: block;
        }
        .certificado-container {
            width: 950px;
            height: 680px;
            margin: 20px auto;
            border: 15px solid #1e3a8a; /* Deep blue border */
            padding: 20px;
            text-align: center;
            position: relative;
            box-sizing: border-box;
            background: #fff;
        }
        .logo {
            width: 100px;
            height: 100px;
            background: #2563eb;
            color: #fff;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            font-weight: bold;
            margin-bottom: 20px;
            line-height: 100px;
        }
        .header-title {
            font-size: 38px;
            color: #1e40af;
            text-transform: uppercase;
            letter-spacing: 4px;
            margin: 0 0 10px 0;
            font-weight: bold;
        }
        .subtitle {
            font-size: 20px;
            color: #64748b;
            margin-bottom: 20px;
        }
        .text-presenta {
            font-size: 20px;
            margin-bottom: 20px;
        }
        .student-name {
            font-size: 42px;
            color: #0f172a;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 20px;
            border-bottom: 2px solid #cbd5e1;
            display: inline-block;
            padding-bottom: 5px;
        }
        .description {
            font-size: 20px;
            line-height: 1.6;
            margin: 20px 80px;
            color: #475569;
        }
        .course-name {
            font-size: 28px;
            color: #0284c7;
            font-weight: bold;
        }
        .date {
            font-size: 16px;
            margin-top: 20px;
            color: #64748b;
        }
        .signatures {
            margin-top: 50px;
            width: 100%;
            display: table;
        }
        .signature-block {
            display: table-cell;
            width: 50%;
            text-align: center;
        }
        .signature-line {
            width: 200px;
            border-top: 1px solid #333;
            margin: 0 auto 10px auto;
        }
        .signature-name {
            font-size: 18px;
            font-weight: bold;
        }
        .signature-title {
            font-size: 14px;
            color: #64748b;
        }
        .watermark {
            position: absolute;
            top: 250px;
            left: 200px;
            font-size: 150px;
            color: rgba(30, 58, 138, 0.03);
            transform: rotate(-30deg);
            z-index: -1;
            pointer-events: none;
            font-weight: bold;
        }
        

    </style>
</head>
<body>

<div class="certificado-container">
    <div class="watermark">INSTITUTO MATE</div>
    
    <!-- Fallback simple logo if no image is available -->
    <div class="logo">IM</div>

    <h1 class="header-title">Certificado de Conclusión</h1>
    <div class="subtitle">Instituto de Matemáticas UPDS</div>

    <div class="text-presenta">Se otorga el presente certificado a:</div>

    <div class="student-name">{{ $estudiante->persona->nombre_completo }}</div>

    <div class="description">
        Por haber completado y aprobado satisfactoriamente todos los requisitos académicos correspondientes a la especialidad de:
        <br><br>
        <span class="course-name">{{ $especialidad->nombre }}</span>
    </div>

    <div class="date">
        Emitido en Santa Cruz de la Sierra, el {{ $fecha }}
    </div>

    <div class="signatures">
        <div class="signature-block">
            <div class="signature-line"></div>
            <div class="signature-name">Lic. Carlos Director</div>
            <div class="signature-title">Director Académico</div>
        </div>
        <div class="signature-block">
            <div class="signature-line"></div>
            <div class="signature-name">Ing. Ana Coordinadora</div>
            <div class="signature-title">Coordinadora de Especialidad</div>
        </div>
    </div>
</div>

</body>
</html>
