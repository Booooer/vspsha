// Получение данных от бэка real-time

const interval = 5000

setInterval(getOrders,interval);

let countOrders = $('.panel-order').length
console.log($('.panel-order').length)

function getOrders(){
    $.get('/orders/get',function(data){
        checkNewOrder(data.length)
        let html = ``
        for (let index = 0; index < data.length; index++) {
            html += `<div class="panel-padding panel-order">
                        <div>
                            <article>${data[index].title}</article>
                            <p>Цена: ${data[index].total_sum} руб</p>
                            <p>Размер: ${data[index].file_size}</p>
                            <p>Кол-во: ${data[index].quantity}</p>
                            <button value="${data[index].comment}" id="show-comment" onclick="showComment(this.value)">Показать комментарий</button>
                        </div>
                        <div>
                            <article>${data[index].tel}</article>
                            <div class="order-buttons">
                                <button value=${data[index].file_url} onclick="loadFile($(this).val())">Загрузить файл</button>
                                <button value=${data[index].id} onclick="deleteOrder($(this).val())">Заказ сделан</button>
                            </div>
                        </div>
                    </div>`
        }
        $('.panel-orders').html(html)
    })
}

function showComment(value){
    if (value == "null") {
        Swal.fire({
            icon: 'error',
            title: 'Упс....',
            text: 'Клиент не оставил комментарий (',
            footer: '<a href="#">Почему возникла ошибка?</a>'
          })
        return
    }
    Swal.fire(value)
}

function checkNewOrder(count){
    if (count > countOrders) {
        Swal.fire('У вас новый заказ!')
    }
    countOrders = count
}

// Функционал админ панели

// Триггеры блоков

// let currentTrigger = $('#trigger-account')

function hideAllPanel(){
    $('#order-trigger, #service-trigger, #trigger-account, #other-trigger').removeClass('active-trigger')
    $('.panel-account, .panel-services, .panel-orders, .panel-other').hide(0)
}

$('#order-trigger').click(function(){
    hideAllPanel()
    $(this).addClass('active-trigger')
    $('.panel-orders').show('fast')
})

$('#service-trigger').click(function(){
    hideAllPanel()
    $(this).addClass('active-trigger')
    $('.panel-services').show('fast')
})

$('#trigger-account').click(function(){
    hideAllPanel()
    $(this).addClass('active-trigger')
    $('.panel-account').show('fast')
})

$('#other-trigger').click(function(){
    hideAllPanel()
    $(this).addClass('active-trigger')
    $('.panel-other').show('fast')
})

// Загрузка файла на клиент

function loadFile(value){
    console.log(value)
    if (value === 'null') {
        Swal.fire(
            'Файл не обнаружен',
            'Клиент не прикрепил файл к заказу',
            'question'
          )
          return false;
    }

    Swal.fire({
        title: 'Скачать файл?',
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: 'Скачать',
        denyButtonText: `Нет`,
      }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
          Swal.fire('Скачиваем файл!', '', 'success')

          let link = document.createElement('a')
          link.setAttribute('href',`/load/${value}`)
          link.click()
        } else if (result.isDenied) {
          Swal.fire('Вы отказались скачивать', '', 'info')
        }
      })
}

// Удаление заказа из списка активных

function deleteOrder(id){
    Swal.fire({
        title: 'Вы уверены?',
        text: "В наше время не существует машины времени!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Изменить статус заказа!'
      }).then((result) => {
        if (result.isConfirmed) {
          Swal.fire(
            'Изменено!',
            'Вы сделали заказ неактивным!',
            'success'
          )

          $.ajax({
            headers: {
                'X-Csrf-Token': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            cache: false,
            url: '/disable/order',
            data: {
                'id': id
            },
            dataType: 'json',
            success: function(data) {
                getOrders()
                console.dir(data);
            },
            error: function(err){
                console.dir(err);
            },
        });
        }
      })
}

//// Функционал редактора услуг

// Показ редактора услуг через ajax

$('.btn-edit').click(function(){
    $.ajax({
        headers: {
            'X-Csrf-Token': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        cache: false,
        url: '/service/find',
        data: {
            'id': $(this).val()
        },
        dataType: 'json',
        success: function(data) {
            $('.service-img img').attr('src',`storage/${data.url_info}`)
            $('#service-title').val(data.title)
            $('#service-description').val(data.short_description)
            $('#service-id').val(data.id)

            $('.modal-service-edit').show('fast')

            $('.service-img img').click(function(){
                BigPicture({
                    el: this,
                    imgSrc: $(this).attr('src')
                  });
            })
        },
        error: function(err){
            console.dir(err);
            Swal.fire({
                icon: 'error',
                title: 'Упс....',
                text: 'Что то пошло не так!',
                footer: '<a href="#">Почему возникла ошибка?</a>'
              })
        },
    })
})

// Обновление данных услуги

$('.btn-update-service').click(function(event){
    event.preventDefault()
    let formData = new FormData()

    // добавляем в запрос данные из обычных полей
    $('.service-form').find(':input[name]').not('[type="file"]').each(function() {
        let field = $(this)
        formData.append(field.attr('name'), field.val())
    })

    // добавляем файл
    let filesField = $('.service-form').find('input[type="file"]')
    let fileName = filesField.attr('name')
    let file = filesField.prop('files')[0]

    console.log(fileName)

    formData.append(fileName,file)

    $.ajax({
        headers: {
            'X-Csrf-Token': $('meta[name="csrf-token"]').attr('content')
        },
        contentType: false,
		processData: false,
        type: 'POST',
        cache: false,
        url: '/update/service',
        data: formData,
        dataType: 'json',
        success: function(data) {
            console.log(data)
            $('.modal-service-edit').hide('fast')
            Swal.fire({
                position: 'top-end',
                icon: 'success',
                title: 'Услуга обновлена!',
                showConfirmButton: false,
                timer: 1500
              })
        },
        error: function(err){
            console.log(err)
            $('modal-service-edit').hide('fast')
            Swal.fire({
                icon: 'error',
                title: 'Упс....',
                text: 'Что то пошло не так!',
                footer: '<a href="#">Почему возникла ошибка?</a>'
              })
        }
    })
})

// скрытие редактора услуг

$('.service-edit-close').click(function(){
    $('.modal-service-edit').hide('fast')
})

// Показ фото-описания услуги

$('.btn-show-img').click(function(){
    $('.service-img').toggle('slow')
})

// работа Input type - file

let images = document.querySelectorAll('.image__file')

    Array.prototype.forEach.call(images, function (input) {
        let label = input.nextElementSibling,
        labelVal = label.querySelector('.input__file-button-text').innerText

        input.addEventListener('change', function (e) {
        let countFiles = ''
        if (this.files && this.files.length >= 1)
          countFiles = this.files.length

        if (countFiles)
          label.querySelector('.input__file-button-text').innerText = 'Выбрано файлов: ' + countFiles
        else
          label.querySelector('.input__file-button-text').innerText = labelVal
      })
    })
