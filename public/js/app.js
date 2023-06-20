// Переменные для обрезки
let dataCrop = []
let arrayCanvas = []
let files = []
let file
let input
let cropCounter = 0
let image
let isReady = false
var options = {
        aspectRatio: 21 / 30,
        height: 280,
        width: 380,
        minContainerWidth: 380,
        minContainerHeight: 280,
        maxContainerHeight: 280,
        maxContainerWidth: 380,
        viewMode: 0,
        background: false,
        autoCrop: true,
        cropBoxResizable: false,
        cropBoxMovable: true,
        movable: true,
}

// Маска для ввода телефона

$(document).ready(function() {
    $("#phone").inputmask({'mask': '+7 (999) 999-99-99'})
  })

// Jcrop in action

// function start

function checkBeforeCrop(){
    cropCounter = 0
    $('#checkbox-image').prop('disabled',false)
    let fileExtension = ['jpg', 'jpeg', 'png']
    // input[0] = ''
    // input = this.files
    files = []
    for (let index = 0; index < this.files.length; index++) {
        files[index] = this.files[index]
        if ((this.files[index].name.split('.').pop().toLowerCase(), fileExtension) == -1) {
            Swal.fire({
                icon: 'error',
                title: 'Неверный формат!',
                text: 'Выберите другой файл',
                footer: '<a href="#">Почему возникла ошибка?</a>'
              })
              return false
        }
    }

    // if (!file) {
    //     console.log('Файл не обнаружен')
    //     $('#btn-step-three').prop('disabled',true)
    //     return
    // }

    // let size = file.size // размер в байта
    if (!isImage) {
        console.log('Это не картинка')
        $('#checkbox-image').prop('disabled',true)
        $('#btn-step-three').prop('disabled',false)
        return false
    }

    cropImage()
}

$(`#input__file`).change(checkBeforeCrop)

$('#photo-size').change(function(){
    options.aspectRatio = getFormat()
    console.log('aspectRatio is ' + options.aspectRatio)
    // restart
    image.cropper('destroy')
    image.cropper(options)
})

function cropImage(){
    console.log('Начинаю обрезку...')

    let formData = getFormData()

    $.ajax({
        headers: {
            'X-Csrf-Token': $('meta[name="csrf-token"]').attr('content')
        },
        contentType: false,
		processData: false,
        type: 'POST',
        cache: false,
        url: '/save/image',
        data: formData,
        dataType: 'json',
        success: function(data) {
            $('#step-crop').html(`${cropCounter + 1}/<span>${files.length}</span>`)
            $('#image-crop').attr('src',`storage/${data}`)
            $('.modal-demo').show('fast')
            console.log(data)

            if (cropCounter > 0) {
                image.cropper('destroy')
            }

            image = $('#image-crop')
            image.cropper(options)
            $('#btn-crop').prop('disabled', false)
        },
        error: function(err){
            console.log(err)
        }
    })
    .done(setInterval(() =>{
        if (isReady) {
            isReady = false
            setTimeout(() =>{
                // обнуление кол-ва нового фото
                $('#count-photo').val(1)
                cropImage()
            },200)
        }
    },200))
}

$('#btn-crop').click(function(){
    $('#btn-crop').prop('disabled', true)
    cropCounter++
    // // создаём инпут файл
    // createInput()

    $('.input__file-button-text').text('Выбрано файлов: ' + cropCounter)
    // получение данных обрезки
    let data = image.cropper('getData')
    let cropImage = image.cropper('getCroppedCanvas',{
        maxWidth: 480,
        maxHeight: 300,
        width: 480,
        height: 300,
    })
    // добавление фото в предпросмотр
    arrayCanvas[cropCounter] = cropImage
    $('.previews').append(arrayCanvas[cropCounter])

    // пополнение обьекта данными об обрезке
    dataCrop[`crop${cropCounter}`] = [Math.round(data.x),
                        Math.round(data.y),
                        Math.round(data.width),
                        Math.round(data.height),
                        parseInt($('#count-photo').val()),
                        $('#photo-size').val()]

    $('#cord-x').val(Math.round(data.x))
    $('#cord-y').val(Math.round(data.y))
    $('#cord-width').val(Math.round(data.width))
    $('#cord-height').val(Math.round(data.height))

    if (cropCounter == files.length) {
        $('#btn-step-three').prop('disabled',false)
        $('.modal-demo').hide('fast')
    }

    isReady = true
})

function getFormData(){
    let formData = new FormData()
    // добавляем файл
    let fileName = `file_${cropCounter}`
    let numberFile = 'numberFile'

    console.log('Выводим такой файл - ' + files[cropCounter])

    formData.append(fileName,files[cropCounter])
    formData.append(numberFile,cropCounter)

    return formData
}

// получение aspectRatio

function getFormat(){
    console.log($('#photo-size').val())
    switch ($('#photo-size').val()) {
        case "21x30":
            return 21 / 30

        case "15x21":
            return 15 / 21

        default:
            return 10 / 15
    }
}

// Появление блока - О нас, Заказа, Карты, Авторизации

$('#trigger-about, #burger-trigger-about').click(function(){
    $('.burger-menu').hide('fast')
    $('.modal').hide(0)
    $('.modal-about, .overlay').show('fast')
})

$('#trigger-order, #burger-trigger-order').click(function(){
    $('.burger-menu').hide('fast')
    $('.modal').hide(0)
    $('.modal-order, .overlay').show('fast')
    // gerUrlImage($('#order-name').val())
})

$('#trigger-map, #burger-trigger-map').click(function(){
    $('.burger-menu').hide('fast')
    $('.modal').hide(0)
    $('.modal-map, .overlay').show('fast')
})

$('#trigger-auth, #burger-trigger-auth').click(function(){
    $('.burger-menu').hide('fast')
    $('.modal').hide(0)
    $('.modal-auth, .overlay').show('fast')
})
// Скрытие блока - О нас, Заказа, Карты, Авторизации

$('.about-close').click(function(){
    $('.modal-about, .overlay').hide('fast')
})

$('.order-close').click(function(){
    $('.modal-order, .overlay').hide('fast')
})

$('.map-close').click(function(){
    $('.modal-map, .overlay').hide('fast')
})

$('.auth-close').click(function(){
    $('.modal-auth, .overlay').hide('fast')
})
// слайдер - О нас

let position = 0

$('#btn-continue').click(function(){
    $('#btn-back').attr('disabled', false)
    position -= $('.info-item').width();
    console.log(position)
    $('.info-container').css('transform',`translateX(${position}px)`)
    if (position >= ($('.info-item').width() * -2)) {
        $('#btn-continue').attr('disabled', true)
    }
})

$('#btn-back').click(function(){
    $('#btn-continue').attr('disabled', false)
    position += $('.info-item').width();
    console.log(position)
    $('.info-container').css('transform',`translateX(${position}px)`)
    if (position == 0) {
        $('#btn-back').attr('disabled', true)
    }
})

////////////////////////////////////////////

$('#btn-accept-order').click(function(event){
    $(this).prop('disabled',true)
    event.preventDefault()
    let checkbox = document.getElementById('checkbox-image')
    $('.loader-order').show('fast')
    $(this).prop('disabled', true)

    let formDataOrder = new FormData()
    // добавляем в запрос данные из обычных полей
    $('.order-container').find(':input[name]').not('[type="file"]').each(function() {
        let field = $(this)
        formDataOrder.append(field.attr('name'), field.val())
    })

    for (let index = 0; index < files.length; index++) {
        let fileName = `file_${index}`
        formDataOrder.append(fileName,$('#input__file').prop('files')[index])
    }
    // добавляем файл

    // for (let index = 0; index < cropCounter; index++) {
    //     let file = filesField.prop('files')[index]
    //     let fileName = `file_${index}`
    //     formDataOrder.append(fileName,file)
    // }

    // добавляем данные обрезки
    for (let index = 0; index < cropCounter; index++) {
        console.log('добавление...')
        let name = `cropX${index}`
        formDataOrder.append(name,dataCrop[`crop${index + 1}`][0])

        name = `cropY${index}`
        formDataOrder.append(name,dataCrop[`crop${index + 1}`][1])

        name = `cropWidth${index}`
        formDataOrder.append(name,dataCrop[`crop${index + 1}`][2])

        name = `cropHeight${index}`
        formDataOrder.append(name,dataCrop[`crop${index + 1}`][3])

        name = `count_${index}`
        formDataOrder.append(name,dataCrop[`crop${index + 1}`][4])

        name = `size_${index}`
        formDataOrder.append(name,dataCrop[`crop${index + 1}`][5])
    }

    // всё кол-во загружаемых файлов
    let filesCount = 'filesCount'
    formDataOrder.append(filesCount, cropCounter)

    console.log(formDataOrder)
    $.ajax({
        headers: {
            'X-Csrf-Token': $('meta[name="csrf-token"]').attr('content')
        },
        contentType: false,
		processData: false,
        type: 'POST',
        cache: false,
        url: '/order',
        data: formDataOrder,
        dataType: 'json',
        success: function(data) {
            $("#btn-accept-order").prop('disabled',false)
            $('.loader-order').hide('fast')
            $('#btn-accept-order').prop('disabled', false)
            $('.modal-order, .overlay').hide('fast')
            Swal.fire({
                position: 'top-end',
                icon: 'success',
                title: 'Ваш заказ успешно обработан',
                showConfirmButton: false,
                timer: 1500
              })
        },
        error: function(err){
            console.log(err)
            $('.loader-order').hide('fast')
            $('#btn-accept-order').prop('disabled', false)
            $('.modal-order, .overlay').hide('fast')
            Swal.fire({
                icon: 'error',
                title: 'Упс....',
                text: err.responseText,
                footer: '<a href="#">Почему возникла ошибка?</a>'
              })
        }
    })
})

//////////////////////// Бургер меню

$('#trigger-burger').click(function(){
    $('.modal, .overlay').hide(0)
    $('.burger-menu').toggle('fast')
})



