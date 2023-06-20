@extends('layouts.app')
@section('title','Вспышка - Фотоцентр')
@section('content')
@if (isset($errors))
    @foreach ($errors as $error)
       {{  $error }}
    @endforeach
@endif
<div class="burger-menu" style="display: none">
    <div class="burger-content">
        <a href="#" id="trigger-about">О нас</a>
        @guest
        <a href="#" id="trigger-auth">Войти</a>
        @endguest
        @auth
        <a href="{{ route('profile') }}">В профиль</a>
        @endauth
        <a href="#" id="trigger-map">Где мы?</a>
        <a href="#" id="burger-trigger-order">Заказать фото</a>
    </div>
</div>
<header class="header">
    <div class="bg-video">
        <div class="overlay-video">

        </div>
        <video src="storage/bg-video.mp4" loop="" muted="" autoplay="autoplay" class="fullscreen-bg__video">

        </video>
    </div>
    <div class="header-menu">
        <div id="trigger-burger">
            <img src="img/burger-menu.png" alt="">
            <p>Меню</p>
        </div>
        <nav class="left-nav">
            <a href="#" id="burger-trigger-about">&nbsp&nbsp&nbspО нас</a>
            @guest
            <a href="#" id="burger-trigger-auth">Войти</a>
            @endguest
            @auth
            <a href="{{ route('profile') }}">В профиль</a>
            @endauth
        </nav>
        <div class="logo">
            <img src="img/Logo.png" alt="" srcset="">
        </div>
        <nav class="right-nav">
            <a href="#" id="burger-trigger-map">Где мы?</a>
            <a href="#" id="burger-trigger-order">Заказать фото</a>
        </nav>
    </div>
    <div class="header-content">
        <h1>Фото-копи центр "Вспышка"</h1>
        <div class="content-info" style="text-align: center">
            <h2>Сделаем быстро и качественно!</h2>
            <h3>г.Шадринск, ул.Карла Либкнехта, 16</h3>
            <input type="button" id="trigger-order" value="Заказать фото">
        </div>
    </div>
</header>
<script src="js/order-form.js"></script>
@endsection
