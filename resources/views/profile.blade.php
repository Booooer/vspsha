@extends('layouts.app')
@section('title','Профиль сотрудника')
@section('content')
    <div class="modal modal-service-edit" style="display: none">
        <article>Редактор услуг</article>
        <form class="service-form">
            <input type="hidden" name="service-id" id="service-id">
            <button type="button" class="btn-show-img">Фото-описание v</button>
            <div class="service-img" style="display: none">
                    <img src="img/profile-icon.jpg" alt="">
            </div>
            <label for="">Прикрепите фото-описание</label>
            <div class="service__wrapper">
                <input name="file" type="file" id="image__file" class="input input__file" multiple>
                <label for="image__file" class="input__file-button">
                   <span class="input__file-icon-wrapper"><img class="input__file-icon" src="/img/setup-icon.png" alt="Выбрать файл" width="25"></span>
                   <span class="input__file-button-text">Выберите файл</span>
                </label>
             </div>
             <label for="service-title">Название услуги</label>
             <input type="text" name="service-title" id="service-title">
             <label for="service-description">Небольшое описание(макс - 32 симв.)</label>
             <input type="text" name="service-description" id="service-description">
             <button class="btn-update-service" type="button">Обновить</button>
        </form>
        <div class="service-edit-close">
            ->
        </div>
    </div>
    <main class="main-profile">
        <div class="profile-container">
            <div class="admin-panel">
                <div class="panel-triggers">
                    <p><a href="/" style="color: #fff">Назад</a></p>
                    <p id="trigger-account" class="active-trigger">Аккаунт</p>
                    <p id="order-trigger">Заказы</p>
                    <p id="other-trigger">Прочее</p>
                </div>
                <div class="panel-edit">
                    <div class="panel-account">
                        <img src="img/profile-icon.jpg" alt="">
                        <article>Приветик, {{ $user->login }}</article>
                        <p>Твоя роль - {{ $user->role }}</p>
                    </div>
                    <div class="panel-orders" style="display: none">

                    </div>
                    <div class="panel-other" style="display: none">
                        <h1>Пока в разработке!</h1>
                    </div>
                </div>
            </div>
        </div>
    </main>
<script src="js/checkOrder.js"></script>
@endsection
