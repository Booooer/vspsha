<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Фото-копицентр в г.Шадринcк">
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">
    {{-- sweetAlert --}}
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css" rel="stylesheet">
    {{-- cropper  --}}
    <link rel="stylesheet" href="{{ url('css/cropper.css') }}">
    {{-- main styles --}}
    <link rel="stylesheet" href="{{ url('css/app.css') }}">
    <link rel="stylesheet" href="{{ url('css/media.css') }}">
    <title>@yield('title')</title>
</head>
<div class="hidden-image">

</div>
<div class="modal modal-demo" style="display: none">
    <div id="step-crop"></div>
    <img src="" id="image-crop">
    <p>Формат фото</p>
    <select name="photo_size" id="photo-size">
        <option value="10x15">10x15</option>
        <option value="15x21">15x21</option>
        <option value="21x30">21x30</option>
    </select>
    <span class="counter-photo">
        <p style="padding: 15px">Количество</p>
        <div>
            <input type="button" value="-" id="minus-photo">
            <input type="text" name="count_photos" id="count-photo" disabled>
            <input type="button" value="+" id="plus-photo">
        </div>
    </span>
    <button id="btn-crop">Готово!</button>
</div>
<div class="overlay" style="display: none"></div>
<div class="modal modal-about" style="display: none">
    <article>О нас</article>
    <div class="about-info">
        <div class="info-container">
            <div class="info-item">
                <p>Меня зовут Роман, я основатель фото-копи центра «Вспышка» в Шадринске,
                    который в будущем планирую развить в большую сеть копи-центров по всей России.</p>
                <p>Свою деятельность я начал в 2016 году, 14 марта. Выбрать именно этот вид деятельности
                 меня сподвигли две причины – во-первых я знал, как это работает, т.к. имел личный
                    опыт работы в данной сфере деятельности как наёмный сотрудник. А во-вторых, меня
                    не устраивало качество сервиса в уже имеющихся салонах такого плана, и я решил это
                     изменить, открыв свой собственный салон.</p>
            </div>
            <div class="info-item">
                <p>В течение первых двух лет, осваивая различные рекламные методы, с горем пополам, мне
                    удалось собрать первую стабильную аудиторию клиентов, которые начали приносить ощутимый
                     доход. Изначально я думал, что всё будет проще, но как показала практика, с нуля найти свою
                      аудиторию, которая бы начала приносить более или менее стабильный доход не так то просто..</p>
                <p>На следующем этапе я вернул все заемные средства, на которые начал деятельность «Вспышки» и
                     остался её единоличным владельцем. По пути я улучшал как внутреннюю обстановку салона,
                      так и приобретал новое оборудование, чтобы быстрее справляться с увеличивающимся потоком
                      клиентов..</p>
            </div>
        </div>
    </div>
    <div class="about-buttons">
        <button disabled id="btn-back">Вернуться</button>
        <button  id="btn-continue">Узнать больше</button>
    </div>
    <div class="about-close">
        ->
    </div>
</div>
<div class="modal modal-order" style="display: none">
    <div class="order-slider">
        <button type="button" class="btn-back" style="display: none">Назад</button>
        <div class="order-logo">
            <img src="img/Logo.png" alt="Логотип">
        </div>
        <form class="order-container" enctype="multipart/form-data">
                <div class="step-one-phone">
                    <article>Введите номер телефона</article>
                    <input type="tel" value="{{ old('phone') }}" class="form-control" id="phone" name="phone" placeholder="+7 (___) ___-__-__" required>
                    <button value=".step-one-phone" class="btn-next" type="button" id="btn-step-one" disabled>Продолжить</button>
                </div>
                <div class="step-two-service" style="display: none">
                    <article>Выберите услугу</article>
                    <select name="order_name" id="order-name">
                    @foreach ($services as $service)
                        <option value="{{ $service->title }}">{{ $service->title }}</option>
                    @endforeach
                    </select>
                    <button value=".step-two-service" class="btn-next" type="button" id="btn-step-two">Продолжить</button>
                </div>
                <div class="step-three-photo" style="display: none">
                    <article>Выбор фото</article>
                    <span class="hidden-inputs" style="display: none">

                    </span>
                    <span class="input-image">
                        <input name="file[]" type="file" id="input__file" class="input input__file" accept=".png, .jpg, .jpeg" multiple>
                        <label for="input__file" class="input__file-button">
                            <span class="input__file-icon-wrapper"><img class="input__file-icon" src="img/setup-icon.png" alt="Выбрать файл" width="25"></span>
                            <span class="input__file-button-text">Выберите файл</span>
                        </label>
                    </span>
                    {{-- <span class="checkbox">
                        <input type="checkbox" id="checkbox-image">
                        <label for="isImage">Загружаю архивом (.zip, .rar)</label>
                    </span> --}}
                    <input type="hidden" name="count_photos" id="count-photos" value="1" maxlength="3" max="100">
                    <button id="add-file" type="button" disabled style="display: none">Добавить фото</button>
                    <button value=".step-three-photo" class="btn-next" type="button" id="btn-step-three" disabled>Продолжить</button>
                </div>
                <div class="step-four-preview" style="display: none">
                    <article>Предпросмотр фото</article>
                    <div class="previews">

                    </div>
                    <button value=".step-four-preview" class="btn-next" type="button" id=".step-four-preview" onclick="totalSum()">Продолжить</button>
                </div>
                <div class="step-five-total" style="display: none">
                    <table>
                        <tr>
                          <td>Услуга</td>
                          <td>Размер</td>
                          <td>Количество</td>
                        </tr>
                    </table>
                    <article>Предварительная стоимость</article>
                    <input type="hidden" name="total_sum" id="input-sum">
                    <p id="total-sum"><b>0 руб</b></p>
                    <label for="order-description">Комментарий к заказу</label>
                    <textarea name="order_description" class="order-description"></textarea>
                    <button  class="btn-next" type="button" id="btn-accept-order">Отправить заявку</button>
                    <span class="loader-order" style="display: none">
                        <img src="img/loader.gif"/>
                    </span>
                </div>
        </form>
    </div>
    <div class="order-close">
        X
    </div>
</div>
<div class="modal modal-map" style="display: none">
    <article>Мы здесь :)</article>
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1113.0789908652089!2d63.62136838751121!3d56.08518291413338!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x43c785e22a86ac05%3A0x136e8a2074e80c56!2z0KTQvtGC0L4t0LrQvtC_0Lgg0YbQtdC90YLRgCDQktGB0L_Ri9GI0LrQsA!5e0!3m2!1sru!2sru!4v1681307616424!5m2!1sru!2sru" width="100%" height="90%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    <div class="map-close">
        ->
    </div>
</div>
<div class="modal modal-auth" style="display: none">
    <article>Авторизация</article>
    <p>только для сотрудников фото-центра</p>
    <form action="{{ route('auth') }}" method="post">
        @csrf
        <input type="text" name="login"  placeholder="введите логин" required>
        <input type="password" name="password" id=""  placeholder="введите пароль" required>
        <button type="submit">Войти</button>
    </form>
    <div class="auth-close">
        ->
    </div>
</div>
<body>
{{-- Сам Jquery --}}
<script src="https://code.jquery.com/jquery-3.6.4.js" integrity="sha256-a9jBBRygX1Bh5lt8GZjXDzyOB+bWve9EiO7tROUtj/E=" crossorigin="anonymous"></script>
@yield('content')
{{-- Плагин sweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
{{-- Плагин для увеличения картинок --}}
<script src="js/BigPicture.js"></script>
{{-- Плагин Jcrop --}}
<script src="js/jquery-cropper.js"></script>
<script src="js/cropper.js"></script>
{{-- Маска для ввода телефона --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js"></script>
{{-- Основной код js --}}
<script src="js/app.js"></script>
</body>
</html>
