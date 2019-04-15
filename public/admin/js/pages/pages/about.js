$(document).ready(function () {
    setTimeout(function () {
        $('.textarea-editor').trumbowyg({
            toolbar: 'toolbar',
        });
    }, 500);

    (function dropifyInit() {
        // Basic
        $('.dropify').dropify();

        // Translated
        $('.dropify-fr').dropify({
            messages: {
                default: 'Glissez-déposez un fichier ici ou cliquez',
                replace: 'Glissez-déposez un fichier ou cliquez pour remplacer',
                remove:  'Supprimer',
                error:   'Désolé, le fichier trop volumineux'
            }
        });

        // Used events
        var drEvent = $('.dropify-event').dropify();

        drEvent.on('dropify.beforeClear', function(event, element){
            return confirm("Do you really want to delete \"" + element.filename + "\" ?");
        });

        drEvent.on('dropify.afterClear', function(event, element){
            alert('File deleted');
        });
    })();

    $('.btn#btnCancel').on('click', function (e) {
        e.preventDefault();
        swal({
            title: 'Tem certeza?',
            text: "Suas modificações não serão salvas",
            type: 'warning',
            showCancelButton: true,
            confirmButtonClass: 'btn btn-success',
            cancelButtonClass: 'btn btn-danger',
            confirmButtonText: 'Sim, continuar',
            cancelButtonText: 'Não',
            buttonsStyling: false
        }).then(function () {
            setTimeout(() => {
                window.location = '/admin/dashboard';
            }, 300);
        });
    });

    $('.btn#btnSave').on('click', function (e) {
        e.preventDefault();

        var form = $('#about_form');
        var action = form.attr('action');
        var formData = new FormData(form[0]);

        // ajax
        $.ajax({
            type: "post",
            url: action,
            data: formData,
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (data) {
                status = (typeof data['status'] !== 'undefined') ? data['status'] : 'error'
                message = (typeof data['message'] !== 'undefined') ? data['message'] : 'Erro ao executar ação. Contactar suporte';
                redirect = (typeof data['redirect'] !== 'undefined') ? data['redirect'] : false;

                switch (status) {
                    case 'ok':
                        swal({
                            title: "Feito!",
                            text: "Ação executada com sucesso",
                            buttonsStyling: false,
                            confirmButtonClass: "btn btn-success",
                            type: "success"
                        }).then(function (isConfirm) {
                            if (isConfirm) {
                                setTimeout(() => {
                                    console.log('REDIRECT: ', redirect);
                                    if (redirect) {
                                        window.location = redirect;
                                    }
                                    else {
                                        window.location.reload(true);
                                    }
                                }, 300);
                            }
                        });
                        break;
                    case 'warning':
                        swal("Oops...", message, "warning");
                        break;
                    case 'error':
                        swal("Oops...", message, "error");
                        break;
                    default:
                        swal("Oops...", 'Sem resposta do servidor. Entre em contato com o suporte', "warning");
                        break;
                }
            },
            error: function (jqXHR, text, error) {
                swal("Oops...", '[' + error + ']: ' + 'Entre em contato com o suporte', "error");
            },
            contentType: false,
            processData: false
        });

        return true;
    });

    videoInputEvent($('.video-input'));
    videoInputFormat($('.video-input'), true);
});

function videoInputEvent(input) {
    $(input).on('keyup', function () {
        videoInputFormat(input);
    });
}

function videoInputFormat(input, first) {
    first = (typeof first !== 'undefined') ? first : false;
    videobox = $(input).parent().find('.video-box');
    old_value = $(input).attr('old-value');
    value = $(input).val();
    $(input).attr('old-value', value);

    if (value !== old_value || first) {
        if (value.indexOf('//vimeo.com/') > 0) {
            value = value.replace('//vimeo.com/', '//player.vimeo.com/video/');
        }

        if (value.length < 4) {
            html = '';
        }
        else if (
            value.indexOf('//player.vimeo.com/') > 0 ||
            value.indexOf('//youtu.be/') > 0
        )
            html = '<iframe src="' + value + '" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
        else
            html = '<p style="color: #f00; display: block;"><strong>* Digite um link válido</strong></p>';
        videobox.html(html);
    }
}