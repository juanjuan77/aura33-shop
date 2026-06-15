@extends('layouts.app')
@section('title', 'Solicitud Recibida — AURA33')

@section('content')
<div style="padding: 100px 0; text-align:center;">
    <div class="container" style="max-width: 580px;">

        <div style="font-size: 4rem; margin-bottom: 24px;">🌙</div>

        <span class="section-subtitle">¡Solicitud recibida!</span>
        <h1 class="section-title">Muchas gracias</h1>
        <div class="divider"></div>

        <p style="font-size:1.05rem; color:var(--muted); font-weight:300; line-height:1.85; margin: 28px 0;">
            Tu solicitud para el <strong style="color:var(--brand)">Programa Mayorista AURA33</strong> fue recibida correctamente.
            La revisamos de lunes a viernes y te contactaremos en menos de <strong style="color:var(--brand)">48 horas hábiles</strong>
            por email o WhatsApp.
        </p>

        <div style="background:var(--white); border:1px solid rgba(74,59,82,0.08); border-radius:12px; padding:28px; box-shadow:var(--shadow-soft); margin-bottom:36px; text-align:left;">
            <h3 style="font-family:var(--font-serif); font-size:1.15rem; color:var(--brand); margin-bottom:16px; font-weight:400;">¿Qué sigue?</h3>
            <div style="display:flex; flex-direction:column; gap:14px;">
                <div style="display:flex; gap:14px; align-items:flex-start;">
                    <span style="background:rgba(212,175,55,0.15); color:var(--accent); width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.82rem; font-weight:600; flex-shrink:0;">1</span>
                    <div>
                        <strong style="font-size:0.9rem; color:var(--brand);">Revisamos tu solicitud</strong>
                        <p style="font-size:0.82rem; color:var(--muted); margin-top:2px; font-weight:300;">El equipo de AURA33 revisa cada solicitud con atención y cuidado.</p>
                    </div>
                </div>
                <div style="display:flex; gap:14px; align-items:flex-start;">
                    <span style="background:rgba(212,175,55,0.15); color:var(--accent); width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.82rem; font-weight:600; flex-shrink:0;">2</span>
                    <div>
                        <strong style="font-size:0.9rem; color:var(--brand);">Te contactamos</strong>
                        <p style="font-size:0.82rem; color:var(--muted); margin-top:2px; font-weight:300;">Te escribimos por email o WhatsApp con el resultado y los próximos pasos.</p>
                    </div>
                </div>
                <div style="display:flex; gap:14px; align-items:flex-start;">
                    <span style="background:rgba(212,175,55,0.15); color:var(--accent); width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.82rem; font-weight:600; flex-shrink:0;">3</span>
                    <div>
                        <strong style="font-size:0.9rem; color:var(--brand);">Empezás a pedir al mayor</strong>
                        <p style="font-size:0.82rem; color:var(--muted); margin-top:2px; font-weight:300;">Con tu email aprobado, el checkout automáticamente aplica precios mayoristas.</p>
                    </div>
                </div>
            </div>
        </div>

        <a href="{{ route('home') }}" class="btn">Volver al Inicio</a>
        <br><br>
        <a href="{{ route('shop') }}" style="font-size:0.85rem; color:var(--brand-light);">Mientras tanto, explorá la tienda →</a>

    </div>
</div>
@endsection
