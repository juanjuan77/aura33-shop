@php $url = $getRecord()?->image_url; @endphp
@if($url)
    <div style="margin-bottom: 8px;">
        <img src="{{ $url }}"
             alt="Imagen actual"
             style="max-height: 220px; border-radius: 10px; object-fit: cover; border: 1px solid #e5e7eb;">
    </div>
@else
    <p style="font-size: 0.85rem; color: #9ca3af; margin-bottom: 8px;">Sin imagen cargada</p>
@endif
