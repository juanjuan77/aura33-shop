<x-filament-panels::page>
<style>
.cr-wrap { font-family: inherit; }

.cr-filters {
    background: linear-gradient(135deg, #faf8ff 0%, #f5f0fb 100%);
    border: 1px solid #e8dff5;
    border-radius: 16px;
    padding: 24px 28px;
    margin-bottom: 28px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}
@media(max-width:640px){ .cr-filters { grid-template-columns: 1fr; } }

.cr-filter-label {
    display: block;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: #7c3aed;
    margin-bottom: 8px;
}

.cr-select-wrap {
    position: relative;
}
.cr-select-wrap::after {
    content: '▾';
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    color: #7c3aed;
    font-size: 0.9rem;
}
.cr-filter-select {
    width: 100%;
    border: 1.5px solid #ddd6fe !important;
    border-radius: 10px !important;
    padding: 10px 36px 10px 14px !important;
    font-size: 0.88rem !important;
    color: #3b1f6e !important;
    background: white !important;
    outline: none !important;
    appearance: none !important;
    -webkit-appearance: none !important;
    box-shadow: none !important;
    background-image: none !important;
}
.cr-filter-select:focus {
    border-color: #7c3aed !important;
    box-shadow: 0 0 0 2px rgba(124,58,237,0.15) !important;
}

.cr-stats {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 14px;
    margin-bottom: 28px;
}
@media(max-width:900px){ .cr-stats { grid-template-columns: repeat(3,1fr); } }
@media(max-width:580px){ .cr-stats { grid-template-columns: repeat(2,1fr); } }

.cr-stat {
    border-radius: 16px;
    padding: 20px 16px;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.cr-stat::before {
    content: '';
    position: absolute;
    top: -20px; right: -20px;
    width: 70px; height: 70px;
    border-radius: 50%;
    opacity: 0.12;
    background: currentColor;
}
.cr-stat--blue   { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
.cr-stat--purple { background: #faf5ff; color: #7e22ce; border: 1px solid #e9d5ff; }
.cr-stat--gray   { background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; }
.cr-stat--green  { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
.cr-stat--red    { background: #fff5f5; color: #b91c1c; border: 1px solid #fecaca; }

.cr-stat-num {
    font-size: 2rem;
    font-weight: 800;
    line-height: 1;
    margin-bottom: 6px;
}
.cr-stat-label {
    font-size: 0.68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    opacity: 0.8;
}
.cr-stat-sub {
    font-size: 0.75rem;
    font-weight: 600;
    margin-top: 4px;
    opacity: 0.9;
}

.cr-table-wrap {
    background: white;
    border: 1px solid #ede9f5;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(124,58,237,0.06);
    margin-bottom: 28px;
}

.cr-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }

.cr-table thead tr {
    background: linear-gradient(135deg, #f5f0fb, #faf8ff);
    border-bottom: 2px solid #ede9f5;
}
.cr-table th {
    padding: 14px 16px;
    text-align: center;
    font-size: 0.68rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.09em;
    white-space: nowrap;
}
.cr-table th:first-child, .cr-table th:nth-child(2) { text-align: left; }
.cr-table th.th-blue   { color: #1d4ed8; }
.cr-table th.th-purple { color: #7e22ce; }
.cr-table th.th-gray   { color: #475569; }
.cr-table th.th-green  { color: #15803d; }
.cr-table th.th-red    { color: #b91c1c; }

.cr-table td {
    padding: 12px 16px;
    border-bottom: 1px solid #f5f0fb;
    vertical-align: middle;
}

.cr-cat-row td {
    padding: 8px 16px;
    background: linear-gradient(90deg, #f5f0fb, #faf8ff);
    font-size: 0.68rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    color: #7c3aed;
    border-bottom: none;
}

.cr-table tr:last-child td { border-bottom: none; }
.cr-table tbody tr:hover td { background: #faf8ff; }

.cr-table tfoot tr {
    background: linear-gradient(135deg, #f5f0fb, #faf8ff);
    border-top: 2px solid #ede9f5;
}
.cr-table tfoot td {
    padding: 14px 16px;
    font-weight: 800;
    font-size: 0.85rem;
    border-bottom: none;
}

.cr-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 32px;
    padding: 3px 10px;
    border-radius: 50px;
    font-size: 0.78rem;
    font-weight: 700;
}
.cr-badge--blue   { background: #dbeafe; color: #1d4ed8; }
.cr-badge--purple { background: #ede9fe; color: #6d28d9; }
.cr-badge--gray   { background: #f1f5f9; color: #475569; }
.cr-badge--green  { background: #dcfce7; color: #15803d; }
.cr-badge--red    { background: #fee2e2; color: #b91c1c; }

.cr-ok { color: #15803d; font-size: 1.1rem; font-weight: 800; }

.cr-chart-wrap {
    background: white;
    border: 1px solid #ede9f5;
    border-radius: 16px;
    padding: 28px;
    box-shadow: 0 2px 12px rgba(124,58,237,0.06);
}
.cr-chart-title {
    font-size: 0.72rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: #7c3aed;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.cr-chart-row {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 14px;
}
.cr-chart-label {
    width: 130px;
    font-size: 0.8rem;
    color: #4b2e83;
    text-align: right;
    flex-shrink: 0;
    font-weight: 500;
}
.cr-chart-bar-bg {
    flex: 1;
    background: #f0edf8;
    border-radius: 99px;
    height: 32px;
    overflow: hidden;
}
.cr-chart-bar-fill {
    height: 32px;
    border-radius: 99px;
    background: linear-gradient(90deg, #7c3aed, #a855f7, #c084fc);
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding-right: 12px;
    min-width: 40px;
    transition: width 0.6s ease;
}
.cr-chart-bar-val {
    font-size: 0.8rem;
    font-weight: 800;
    color: white;
}

.cr-empty {
    text-align: center;
    padding: 60px 20px;
    background: linear-gradient(135deg, #faf8ff, #f5f0fb);
    border: 1px solid #ede9f5;
    border-radius: 16px;
    color: #7c3aed;
}
</style>

<div class="cr-wrap">

    {{-- Filtros --}}
    <div class="cr-filters">
        <div>
            <label class="cr-filter-label">💎 Mayorista / Local</label>
            <div class="cr-select-wrap">
                <select wire:model.live="selectedWholesaler" class="cr-filter-select">
                    <option value="">— Seleccioná un local —</option>
                    @foreach($this->getWholesalers() as $w)
                        <option value="{{ $w->id }}">{{ $w->business_name }} — {{ $w->city }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label class="cr-filter-label">🏷️ Categoría</label>
            <div class="cr-select-wrap">
                <select wire:model.live="selectedCategory" class="cr-filter-select">
                    <option value="">Todas las categorías</option>
                    @foreach($this->getCategories() as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    @if($this->selectedWholesaler)
    @php $data = $this->getReportData(); @endphp

    @if($data->isEmpty())
        <div class="cr-empty">
            <div style="font-size:3rem; margin-bottom:12px;">📦</div>
            <p style="font-size:1rem; font-weight:600;">No hay productos en consignación para este local.</p>
        </div>
    @else

    @php
        $totDelivered = $data->sum('delivered');
        $totSold      = $data->sum('sold');
        $totStock     = $data->sum('stock');
        $totPaid      = $data->sum('paid_qty');
        $totDebe      = $data->sum('debe');
        $totDebeAmt   = $data->sum('debe_amount');
    @endphp

    {{-- Stats --}}
    <div class="cr-stats">
        <div class="cr-stat cr-stat--blue">
            <div class="cr-stat-num">{{ $totDelivered }}</div>
            <div class="cr-stat-label">Entregados</div>
        </div>
        <div class="cr-stat cr-stat--purple">
            <div class="cr-stat-num">{{ $totSold }}</div>
            <div class="cr-stat-label">Vendidos</div>
        </div>
        <div class="cr-stat cr-stat--gray">
            <div class="cr-stat-num">{{ $totStock }}</div>
            <div class="cr-stat-label">Stock cliente</div>
        </div>
        <div class="cr-stat cr-stat--green">
            <div class="cr-stat-num">{{ $totPaid }}</div>
            <div class="cr-stat-label">Pagados</div>
        </div>
        <div class="cr-stat cr-stat--red">
            <div class="cr-stat-num">{{ $totDebe }}</div>
            <div class="cr-stat-label">Debe (unid.)</div>
            @if($totDebeAmt > 0)
            <div class="cr-stat-sub">${{ number_format($totDebeAmt, 0, ',', '.') }}</div>
            @endif
        </div>
    </div>

    {{-- Tabla --}}
    <div class="cr-table-wrap">
        <table class="cr-table">
            <thead>
                <tr>
                    <th style="color:#7c3aed;">Categoría</th>
                    <th style="color:#4b2e83;">Producto</th>
                    <th class="th-blue">Entregados</th>
                    <th class="th-purple">Vendidos</th>
                    <th class="th-gray">Stock cliente</th>
                    <th class="th-green">Pagados</th>
                    <th class="th-red">Debe</th>
                    <th class="th-red" style="text-align:right;">Monto debe</th>
                </tr>
            </thead>
            <tbody>
                @php $prevCat = null; @endphp
                @foreach($data as $row)
                @if($row['category'] !== $prevCat)
                <tr class="cr-cat-row">
                    <td colspan="8">◆ {{ $row['category'] }}</td>
                </tr>
                @php $prevCat = $row['category']; @endphp
                @endif
                <tr>
                    <td></td>
                    <td style="font-weight:600; color:#3b1f6e;">{{ $row['product_name'] }}</td>
                    <td style="text-align:center;"><span class="cr-badge cr-badge--blue">{{ $row['delivered'] }}</span></td>
                    <td style="text-align:center;"><span class="cr-badge cr-badge--purple">{{ $row['sold'] }}</span></td>
                    <td style="text-align:center;"><span class="cr-badge cr-badge--gray">{{ $row['stock'] }}</span></td>
                    <td style="text-align:center;"><span class="cr-badge cr-badge--green">{{ $row['paid_qty'] }}</span></td>
                    <td style="text-align:center;">
                        @if($row['debe'] > 0)
                            <span class="cr-badge cr-badge--red">{{ $row['debe'] }}</span>
                        @else
                            <span class="cr-ok">✓</span>
                        @endif
                    </td>
                    <td style="text-align:right; font-weight:700; color:{{ $row['debe_amount'] > 0 ? '#b91c1c' : '#15803d' }};">
                        @if($row['debe_amount'] > 0) ${{ number_format($row['debe_amount'], 0, ',', '.') }}
                        @else —
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" style="color:#7c3aed;">TOTAL</td>
                    <td style="text-align:center; color:#1d4ed8;">{{ $totDelivered }}</td>
                    <td style="text-align:center; color:#7e22ce;">{{ $totSold }}</td>
                    <td style="text-align:center; color:#475569;">{{ $totStock }}</td>
                    <td style="text-align:center; color:#15803d;">{{ $totPaid }}</td>
                    <td style="text-align:center; color:#b91c1c;">{{ $totDebe }}</td>
                    <td style="text-align:right; color:#b91c1c;">
                        @if($totDebeAmt > 0) ${{ number_format($totDebeAmt, 0, ',', '.') }} @else — @endif
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Gráfico --}}
    @php
        $chartData = $data->groupBy('category')->map(fn($r) => $r->sum('sold'))->filter()->sortDesc();
        $chartMax  = $chartData->max() ?: 1;
    @endphp
    @if($chartData->isNotEmpty())
    <div class="cr-chart-wrap">
        <div class="cr-chart-title">
            <span>📊</span> Productos más vendidos por categoría
        </div>
        @foreach($chartData as $cat => $qty)
        @php $pct = round($qty / $chartMax * 100); @endphp
        <div class="cr-chart-row">
            <div class="cr-chart-label">{{ $cat }}</div>
            <div class="cr-chart-bar-bg">
                <div class="cr-chart-bar-fill" style="width: {{ max($pct, 12) }}%;">
                    <span class="cr-chart-bar-val">{{ $qty }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    @endif
    @endif
</div>
</x-filament-panels::page>
