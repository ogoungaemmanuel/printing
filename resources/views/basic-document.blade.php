<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Document' }}</title>
    <style>
        @page {
            margin: 1in;
            size: {{ $paperSize ?? 'A4' }};
        }
        
        body {
            font-family: {{ $fontFamily ?? 'Arial, sans-serif' }};
            font-size: {{ $fontSize ?? '12px' }};
            line-height: 1.4;
            color: #333;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .content {
            margin-bottom: 50px;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            padding: 10px;
            border-top: 1px solid #ccc;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }
        .italic { font-style: italic; }
        .underline { text-decoration: underline; }
    </style>
</head>
<body>
    @if(isset($header))
    <div class="header">
        {!! $header !!}
    </div>
    @endif

    <div class="content">
        {!! $content ?? 'No content provided' !!}
    </div>

    @if(isset($footer))
    <div class="footer">
        {!! $footer !!}
    </div>
    @endif
</body>
</html>