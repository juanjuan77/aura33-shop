<x-filament-panels::page>

<div x-data="{}" class="space-y-6">

    {{-- Filtros --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Mayorista / Local</label>
                <select wire:model.live="selectedWholesaler" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">— Seleccioná un local —</option>
                    @foreach($this->getWholesalers() as $w)
                        <option value="{{ $w->id }}">{{ $w->business_name }} — {{ $w->city }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Categoría</label>
                <select wire:model.live="selectedCategory" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
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
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center text-gray-400">
            <div class="text-4xl mb-3">📦</div>
            <p>No hay productos en consignación para este local.</p>
        </div>
    @else

    {{-- Totales globales --}}
    @php
        $totDelivered = $data->sum('delivered');
        $totSold      = $data->sum('sold');
        $totStock     = $data->sum('stock');
        $totPaid      = $data->sum('paid_qty');
        $totDebe      = $data->sum('debe');
        $totDebeAmt   = $data->sum('debe_amount');
    @endphp
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-center">
            <div class="text-2xl font-bold text-blue-700">{{ $totDelivered }}</div>
            <div class="text-xs text-blue-600 uppercase tracking-wider mt-1">Entregados</div>
        </div>
        <div class="bg-purple-50 border border-purple-200 rounded-xl p-4 text-center">
            <div class="text-2xl font-bold text-purple-700">{{ $totSold }}</div>
            <div class="text-xs text-purple-600 uppercase tracking-wider mt-1">Vendidos</div>
        </div>
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 text-center">
            <div class="text-2xl font-bold text-gray-700">{{ $totStock }}</div>
            <div class="text-xs text-gray-600 uppercase tracking-wider mt-1">En stock cliente</div>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-center">
            <div class="text-2xl font-bold text-green-700">{{ $totPaid }}</div>
            <div class="text-xs text-green-600 uppercase tracking-wider mt-1">Unid. pagas</div>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-center">
            <div class="text-2xl font-bold text-red-700">{{ $totDebe }}</div>
            <div class="text-xs text-red-600 uppercase tracking-wider mt-1">Debe (unid.)</div>
            @if($totDebeAmt > 0)
            <div class="text-xs font-semibold text-red-500 mt-1">${{ number_format($totDebeAmt, 0, ',', '.') }}</div>
            @endif
        </div>
    </div>

    {{-- Tabla por producto --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Categoría</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Producto</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider text-blue-600">Entregados</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider text-purple-600">Vendidos</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Stock cliente</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider text-green-600">Pagados</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider text-red-600">Debe</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-red-600">Monto debe</th>
                </tr>
            </thead>
            <tbody>
                @php $prevCat = null; @endphp
                @foreach($data as $row)
                @if($row['category'] !== $prevCat)
                <tr class="bg-gray-50/60">
                    <td colspan="8" class="px-4 py-2 text-xs font-bold uppercase tracking-widest text-gray-400">
                        {{ $row['category'] }}
                    </td>
                </tr>
                @php $prevCat = $row['category']; @endphp
                @endif
                <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                    <td class="px-4 py-3 text-gray-400 text-xs"></td>
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $row['product_name'] }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-xs font-semibold">{{ $row['delivered'] }}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full text-xs font-semibold">{{ $row['sold'] }}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded-full text-xs font-semibold">{{ $row['stock'] }}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs font-semibold">{{ $row['paid_qty'] }}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($row['debe'] > 0)
                            <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs font-semibold">{{ $row['debe'] }}</span>
                        @else
                            <span class="text-green-500 text-xs font-semibold">✓</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right font-semibold {{ $row['debe_amount'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                        @if($row['debe_amount'] > 0)
                            ${{ number_format($row['debe_amount'], 0, ',', '.') }}
                        @else
                            —
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                <tr>
                    <td colspan="2" class="px-4 py-3 font-bold text-gray-600 text-sm">TOTAL</td>
                    <td class="px-4 py-3 text-center font-bold text-blue-700">{{ $totDelivered }}</td>
                    <td class="px-4 py-3 text-center font-bold text-purple-700">{{ $totSold }}</td>
                    <td class="px-4 py-3 text-center font-bold text-gray-700">{{ $totStock }}</td>
                    <td class="px-4 py-3 text-center font-bold text-green-700">{{ $totPaid }}</td>
                    <td class="px-4 py-3 text-center font-bold text-red-700">{{ $totDebe }}</td>
                    <td class="px-4 py-3 text-right font-bold text-red-700">
                        @if($totDebeAmt > 0) ${{ number_format($totDebeAmt, 0, ',', '.') }} @else — @endif
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Gráfico de ventas por categoría --}}
    @php
        $chartData = $data->groupBy('category')->map(fn($rows) => $rows->sum('sold'))->filter();
        $maxVal    = $chartData->max() ?: 1;
    @endphp
    @if($chartData->isNotEmpty())
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 mb-6">Ventas por categoría</h3>
        <div class="space-y-3">
            @foreach($chartData->sortDesc() as $cat => $qty)
            @php $pct = round($qty / $maxVal * 100); @endphp
            <div class="flex items-center gap-3">
                <div class="w-36 text-xs text-gray-600 font-medium text-right shrink-0">{{ $cat }}</div>
                <div class="flex-1 bg-gray-100 rounded-full h-7 relative">
                    <div class="h-7 rounded-full bg-gradient-to-r from-purple-400 to-purple-600 flex items-center justify-end pr-3"
                         style="width: {{ max($pct, 8) }}%">
                        <span class="text-xs font-bold text-white">{{ $qty }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @endif
    @endif

</div>
</x-filament-panels::page>
