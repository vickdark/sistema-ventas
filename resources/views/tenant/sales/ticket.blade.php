<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket de Venta #{{ str_pad($sale->nro_venta, 8, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page {
            margin: 0;
            size: 80mm auto;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px; /* Slightly smaller for better fit */
            width: 74mm; /* Safety margin */
            max-width: 74mm;
            margin: 0 auto;
            background: #fff;
            color: #000;
            padding: 3mm 0;
        }
        
        /* Screen Preview */
        @media screen {
            body {
                background: #f0f0f0;
                width: 100%;
                max-width: 100%;
                padding: 20px;
                display: flex;
                justify-content: center;
            }
            .ticket-container {
                background: #fff;
                width: 74mm;
                padding: 10px;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                margin: auto;
            }
        }

        @media print {
            .ticket-container {
                width: 100%;
                box-shadow: none;
                padding: 0;
            }
            body {
                background: #fff;
                padding: 0;
            }
        }

        .header, .footer {
            text-align: center;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        
        .separator {
            border-top: 1px dashed #000;
            margin: 5px 0;
            display: block;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            text-align: left;
            border-bottom: 1px dashed #000;
            padding-bottom: 2px;
            font-size: 10px;
        }
        td {
            padding: 2px 0;
            vertical-align: top;
        }
        
        .product-name {
            display: block;
            width: 100%;
        }
        
        .totals {
            margin-top: 5px;
            text-align: right;
        }
        
        .totals-row {
            display: flex;
            justify-content: space-between;
        }

        .mb-1 { margin-bottom: 2px; }
        .mb-2 { margin-bottom: 5px; }
        .mt-2 { margin-top: 5px; }
    </style>
</head>
<body onload="window.print()">

    <div class="ticket-container">
        <div class="header">
            <h2 style="margin: 0; padding: 5px 0;" class="uppercase">{{ tenant('id') }}</h2>
            <div class="mb-1">RUC: 20601234567</div>
            <div class="mb-1">Dirección: Av. Principal 123 - Centro</div>
            <div class="mb-2">Tel: (01) 123-4567</div>
            
            <div class="separator"></div>
            
            <div class="font-bold">TICKET DE VENTA</div>
            <div class="font-bold">#{{ str_pad($sale->nro_venta, 8, '0', STR_PAD_LEFT) }}</div>
            <div>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y H:i A') }}</div>
        </div>

        <div class="separator"></div>

        <div class="client-info">
            <div><span class="font-bold">CLIENTE:</span> {{ strtoupper($sale->client->name) }}</div>
            <div><span class="font-bold">Doc:</span> {{ $sale->client->nit_ci }}</div>
            @if($sale->payment_type)
            <div><span class="font-bold">PAGO:</span> {{ $sale->payment_type }}</div>
            @endif
            @if($sale->user)
            <div><span class="font-bold">ATENDIDO POR:</span> {{ strtoupper($sale->user->name) }}</div>
            @endif
        </div>

        <div class="separator"></div>

        <table class="items">
            <thead>
                <tr>
                    <th style="width: 10%;">CANT</th>
                    <th style="width: 60%;">DESCRIPCION</th>
                    <th style="width: 30%; text-align: right;">IMPORTE</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                <tr>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                    <td>
                        <span class="product-name uppercase">{{ $item->product->name }}</span>
                        <small style="font-size: 10px;">{{ number_format($item->price, 2) }} unit</small>
                    </td>
                    <td class="text-right">{{ number_format($item->price * $item->quantity, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="separator"></div>

        <div class="totals">
            <div class="totals-row">
                <span>SUBTOTAL:</span>
                <span>$ {{ number_format($sale->total_paid, 2) }}</span>
            </div>
            <div class="totals-row">
                <span>IGV (0%):</span>
                <span>$ 0.00</span>
            </div>
            <div class="separator" style="border-style: solid;"></div>
            <div class="totals-row font-bold" style="font-size: 14px;">
                <span>TOTAL:</span>
                <span>$ {{ number_format($sale->total_paid, 2) }}</span>
            </div>
            <div class="text-left" style="margin-top: 5px; font-size: 10px;">
                SON: {{ number_format($sale->total_paid, 2) }} PESOS
            </div>
        </div>

        <div class="separator"></div>

        <div class="footer">
            <p class="mb-1">¡GRACIAS POR SU PREFERENCIA!</p>
            <p class="mb-1" style="font-size: 10px;">Este documento no tiene valor fiscal</p>
            <p style="font-size: 10px;">Sistema de Ventas v1.0</p>
        </div>
    </div>

</body>
</html>
