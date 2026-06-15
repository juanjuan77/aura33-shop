@extends('layouts.app')
@section('title', 'Nosotros — AURA33')

@section('content')

<section style="padding: 100px 0;">
    <div class="container" style="max-width: 800px;">

        <div class="section-header">
            <span class="section-subtitle">Nuestra historia</span>
            <h1 class="section-title">Somos AURA33</h1>
            <div class="divider"></div>
        </div>

        <div style="font-size:1rem; color:var(--muted); font-weight:300; line-height:2; margin-top:40px;">
            <p style="margin-bottom:24px;">
                Nacimos de la convicción de que <em style="font-family:var(--font-serif); color:var(--brand); font-style:italic;">la energía que nos rodea importa</em>.
                Desde Funes, Santa Fe, llevamos cristales naturales a quienes buscan transformar su vida desde adentro.
            </p>
            <p style="margin-bottom:24px;">
                Cada botella, cada torre, cada oráculo que pasa por nuestras manos es seleccionado con intención.
                Trabajamos solo con piedras 100% naturales, y compartimos toda la información energética para que puedas conectar de verdad con tu cristal.
            </p>
            <p>
                Creemos en el poder del agua cargada con la vibración de la Tierra. Creemos en los ciclos lunares, en los chakras y en que
                <em style="font-family:var(--font-serif); color:var(--brand); font-style:italic;">cuando algo vibra en tu frecuencia, lo sabés.</em>
            </p>
        </div>

        <div style="margin-top: 60px; text-align:center;">
            <a href="{{ route('shop') }}" class="btn">Ver la Tienda</a>
            &nbsp;&nbsp;
            <a href="{{ route('wholesale.info') }}" class="btn-outline btn">Quiero ser Mayorista</a>
        </div>

    </div>
</section>

@endsection
