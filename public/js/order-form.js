let currentSlide = '.step-one-phone'
let prevSlide

let isImage = true

$('#checkbox-image').change(function(){
    if (this.checked) {
        $('input[name=file]').attr('accept','.zip, .rar')
        return isImage = false
    }
    $('input[name=file]').attr('accept','.png, .jpg, .jpeg')
    isImage = true
})

$('.btn-next').click(function(){
    $('.btn-back').show(0)
    prevSlide = this.value
    switch (this.value) {
        case '.step-one-phone':
            currentSlide = '.step-two-service'

            $('.btn-back').val(this.value)
            $(this.value).hide(0)
            $(currentSlide).show('fast')
            break;
        case '.step-two-service':
            currentSlide = '.step-three-photo'

            $('.btn-back').val(this.value)

            $(this.value).hide(0)
            $(currentSlide).show('fast')
            break;
        case '.step-three-photo':
            if (isImage) {
                currentSlide = '.step-four-preview'
                $('.btn-back').val(this.value)
            }
            else{
                totalSum()
                currentSlide = '.step-five-total'
                $('.btn-back').val(".step-three-photo")
            }

            $(this.value).hide(0)
            $(currentSlide).show('fast')
            break;
        case '.step-four-preview':
            currentSlide = '.step-five-total'

            $('.btn-back').val(this.value)

            $(this.value).hide(0)
            $(currentSlide).show('fast')
            break;
        default:
            prevSlide = ".step-three-photo"
            break;
    }
})

$('.btn-back').click(function(){
    $('.step-two-service, .step-three-photo, .step-five-total, .step-four-preview').hide(0)
    $(prevSlide).show('fast')
    currentSlide = this.value

    if (this.value === ".step-one-phone") {
        $(this).hide(0)
    }
    if (prevSlide === '.step-two-service') {
        prevSlide = ".step-one-phone"
        $(this).val(prevSlide)
    }
    if (prevSlide === '.step-three-photo') {
        prevSlide = ".step-two-service"
        $(this).val(prevSlide)
    }
    if (prevSlide === '.step-four-preview') {
        prevSlide = ".step-three-photo"
        $(this).val(prevSlide)
    }
})

function convertToObject(){
    let object = {}

    for (let index = 0; index < cropCounter; index++) {
        object[`size_${index}`] = dataCrop[`crop${index + 1}`][5]
        object[`count_${index}`] = dataCrop[`crop${index + 1}`][4]
    }

    return object
}

function totalSum(){
    let object = convertToObject()
    console.log(object)

    $.ajax({
        type: 'POST',
        headers: {
            'X-Csrf-Token': $('meta[name="csrf-token"]').attr('content')
        },
        url: 'order/sum',
        datatype: 'json',
        data: object,
        success: function(data){
            console.log(data)
            $('#input-sum').val(data)
            $('#total-sum').find('b').html(`${data} руб`)

            let html = `<tr>
                            <td>Услуга</td>
                            <td>Размер</td>
                            <td>Количество</td>
                        </tr>`
            for (let index = 0; index < cropCounter; index++) {
                html += `<tr>
                            <td>Заказ фото</td>
                            <td>${dataCrop[`crop${index + 1}`][5]}</td>
                            <td>${dataCrop[`crop${index + 1}`][4]}</td>
                         </tr>`
            }
            $('table').empty()
            $('table').append(html)
        },
        error: function(err){
            console.log(err)
        }
    })
}

$('#phone').keyup(function(){
    if (!this.value.includes('_')) {
        $(this).css('border','2px solid green')
        $('#btn-step-one').prop("disabled", false)
    }
    else{
        $(this).css('border','2px solid #000')
        $('#btn-step-one').prop("disabled", true)
    }
})

/////////// Счётчик фото
let currentCount
$('#count-photo').val(1)

$('#minus-photo').click(function(){
    currentCount = $('#count-photo').val()
    if (currentCount <= 1) {
        $('#count-photo').val(1)
        return false
    }

    $('#count-photo').val(--currentCount)
})

$('#plus-photo').click(function(){
    currentCount = $('#count-photo').val()
    if (currentCount >= 100) {
        $('#count-photo').val(99)
        return false
    }

    $('#count-photo').val(++currentCount)
})

// добавление фото

$('#add-file').click(async function(){
    createInput()

    console.log('trigger')
    console.log($('#image-1').length)
    $(`#image-${cropCounter}`).trigger("click")

    $(`#image-${cropCounter}`).change(checkBeforeCrop)
})

async function createInput(){
    // let inputFile = $('<input>').attr({
    //     'type': 'file',
    //     'name': 'file[]',
    //     'class': 'input__file',
    //     'accept': '.png, .jpg, .jpeg',
    //     'id': `hidden__file_${cropCounter}`
    // }).hide(0)

    let newInput = `<input name='file' type='file' id='image-${cropCounter}'class='input__file' accept='.png, .jpg, .jpeg'>`

    $('.hidden-inputs').append(newInput)
}
