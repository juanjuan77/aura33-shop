<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Informe Consignación — {{ $wholesaler->business_name }}</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 13px; color: #1a1a2e; background: white; padding: 32px; }

.header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom: 28px; padding-bottom: 20px; border-bottom: 2px solid #7c3aed; }
.brand { font-size: 1.8rem; font-weight: 800; color: #3b1f6e; letter-spacing: -0.03em; }
.brand span { color: #a855f7; }
.doc-title { text-align: right; }
.doc-title h2 { font-size: 1.1rem; color: #7c3aed; font-weight: 700; }
.doc-title p { font-size: 0.78rem; color: #888; margin-top: 2px; }

.local-info { background: #faf8ff; border: 1px solid #ede9f5; border-radius: 10px; padding: 16px 20px; margin-bottom: 24px; display:flex; gap: 40px; flex-wrap: wrap; }
.local-info-item label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #a78bfa; display: block; }
.local-info-item span  { font-size: 0.9rem; color: #3b1f6e; font-weight: 600; }

.tiles { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 24px; }
.tile { border: 1px solid #ede9f5; border-radius: 10px; padding: 14px 16px; text-align: center; }
.tile-n { font-size: 1.3rem; font-weight: 800; color: #3b1f6e; }
.tile-l { font-size: 0.62rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: #a78bfa; margin-top: 4px; }
.tile--green .tile-n { color: #15803d; }
.tile--red   .tile-n { color: #b91c1c; }

table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
thead tr { background: #3b1f6e; color: white; }
thead th { padding: 9px 12px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; text-align: left; }
thead th.num { text-align: center; }
tbody tr:nth-child(even) { background: #faf8ff; }
tbody tr:hover { background: #f3e8ff; }
td { padding: 9px 12px; border-bottom: 1px solid #ede9f5; font-size: 0.85rem; color: #2d1f4e; }
td.num { text-align: center; }
td.mono { font-family: monospace; text-align: right; }
.cat-row td { background: #ede9fe; font-size: 0.68rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: #6d28d9; padding: 5px 12px; }

tfoot tr { background: #3b1f6e; }
tfoot td { color: white; font-weight: 800; padding: 10px 12px; font-size: 0.85rem; }

.badge { display: inline-block; padding: 2px 8px; border-radius: 50px; font-size: 0.72rem; font-weight: 700; }
.badge-blue   { background: #eff6ff; color: #1d4ed8; }
.badge-purple { background: #faf5ff; color: #7e22ce; }
.badge-gray   { background: #f5f5f5; color: #555; }
.badge-green  { background: #f0fdf4; color: #15803d; }
.badge-red    { background: #fef2f2; color: #b91c1c; }

.footer { margin-top: 28px; padding-top: 14px; border-top: 1px solid #ede9f5; display: flex; justify-content: space-between; font-size: 0.72rem; color: #aaa; }

.print-btn { position: fixed; bottom: 24px; right: 24px; background: #7c3aed; color: white; border: none; border-radius: 50px; padding: 12px 24px; font-size: 0.88rem; font-weight: 700; cursor: pointer; box-shadow: 0 4px 12px rgba(124,58,237,0.4); }

@media print {
    .print-btn { display: none; }
    body { padding: 16px; }
    @page { margin: 1.5cm; }
}
</style>
</head>
<body>

<div class="header">
    <div class="brand">AURA<span>33</span></div>
    <div class="doc-title">
        <h2>Informe de Consignación</h2>
        <p>Generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</div>

<div class="local-info">
    <div class="local-info-item">
        <label>Local</label>
        <span>{{ $wholesaler->business_name }}</span>
    </div>
    <div class="local-info-item">
        <label>Contacto</label>
        <span>{{ $wholesaler->name }}</span>
    </div>
    <div class="local-info-item">
        <label>Ciudad</label>
        <span>{{ $wholesaler->city }}, {{ $wholesaler->province }}</span>
    </div>
    <div class="local-info-item">
        <label>Email</label>
        <span>{{ $wholesaler->email }}</span>
    </div>
</div>

<div class="tiles">
    <div class="tile">
        <div class="tile-n">${{ number_format($data['totals']['total_entregado'], 0, ',', '.') }}</div>
        <div class="tile-l">Total entregado</div>
    </div>
    <div class="tile tile--green">
        <div class="tile-n">${{ number_format($data['totals']['total_paid'], 0, ',', '.') }}</div>
        <div class="tile-l">Total cobrado</div>
    </div>
    <div class="tile {{ ($data['totals']['total_entregado'] - $data['totals']['total_paid']) > 0 ? 'tile--red' : '' }}">
        <div class="tile-n">${{ number_format(max(0, $data['totals']['total_entregado'] - $data['totals']['total_paid']), 0, ',', '.') }}</div>
        <div class="tile-l">Saldo pendiente</div>
    </div>
    <div class="tile">
        <div class="tile-n">{{ $data['totals']['stock'] }}</div>
        <div class="tile-l">Unidades en stock</div>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>Producto</th>
            <th class="num">Entregadas</th>
            <th class="num">Vendidas</th>
            <th class="num">En stock</th>
            <th class="num">Pagas</th>
            <th class="num">Debe (u.)</th>
            <th style="text-align:right;">Monto debe</th>
        </tr>
    </thead>
    <tbody>
        @php $prevCat = null; @endphp
        @foreach($data['report'] as $row)
        @if($row['category'] !== $prevCat)
        <tr class="cat-row"><td colspan="7">{{ $row['category'] }}</td></tr>
        @php $prevCat = $row['category']; @endphp
        @endif
        <tr>
            <td style="font-weight:600;">{{ $row['product_name'] }}</td>
            <td class="num">{{ $row['delivered'] }}</td>
            <td class="num">{{ $row['sold'] }}</td>
            <td class="num">{{ $row['stock'] }}</td>
            <td class="num">{{ $row['paid_qty'] }}</td>
            <td class="num" style="{{ $row['debe'] > 0 ? 'color:#b91c1c; font-weight:700;' : 'color:#15803d;' }}">
                {{ $row['debe'] > 0 ? $row['debe'] : '✓' }}
            </td>
            <td class="mono" style="{{ $row['debe_amount'] > 0 ? 'color:#b91c1c; font-weight:700;' : 'color:#15803d;' }}">
                {{ $row['debe_amount'] > 0 ? '$'.number_format($row['debe_amount'],0,',','.') : '—' }}
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td>TOTAL</td>
            <td class="num">{{ $data['totals']['delivered'] }}</td>
            <td class="num">{{ $data['totals']['sold'] }}</td>
            <td class="num">{{ $data['totals']['stock'] }}</td>
            <td class="num">{{ $data['totals']['paid_qty'] }}</td>
            <td class="num">{{ $data['totals']['debe'] }}</td>
            <td class="mono">${{ number_format($data['totals']['debe_amount'], 0, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>

<div class="footer">
    <span>AURA33 · Cristales & Energía · aura33.com.ar</span>
    <span>{{ $wholesaler->business_name }} · {{ now()->format('d/m/Y') }}</span>
</div>

<button class="print-btn" onclick="window.print()">🖨️ Imprimir / Guardar PDF</button>

</body>
</html>
