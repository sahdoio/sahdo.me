$(document).ready(function () {
    $('#enable_password').on('switchChange.bootstrapSwitch', function () {
        if ($(this).is(':checked')) {
            $('.password-area input').removeAttr('disabled');
        }
        else {
            $('.password-area input').attr('disabled','');
        }
    });

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

    $('.selectpicker').on('change', function (e) {
        e.preventDefault();
        var option = $(this).find("option:selected");
        if (option.hasAttr('class') && option.attr('class') == 'option-new')
            window.location = option.val();
    });

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

        var form = $('#users_form');
        var action = form.attr('action');
        var formData = new FormData(form[0]);

        formData.append('about', $('.textarea-editor').html().trim());
        formData.delete('_token');

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
                status = (typeof data['status'] !== 'undefined') ? data['status'] : 'error';
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

    $('#input_cep').on('paste keyup', function (e) {
        var cep = $(this).val().replace(/[^0-9]/g, '');
        console.log('change');
        if (cep.length == 8) {
            var url = 'https://viacep.com.br/ws/' + cep + '/json';
            $('#loading').fadeIn();
            $.ajax({
                type: "get",
                url: url,
                dataType: 'json',
                success: function (data) {
                    rua = (typeof data['logradouro'] != 'undefined') ? data['logradouro'] : '';
                    bairro = (typeof data['bairro'] != 'undefined') ? data['bairro'] : '';
                    cidade = (typeof data['localidade'] != 'undefined') ? data['localidade'] : '';
                    estado = (typeof data['uf'] != 'undefined') ? data['uf'] : '';

                    document.getElementById("input_rua").value = rua;
                    document.getElementById("input_bairro").value = bairro;
                    document.getElementById("input_cidade").value = cidade;
                    document.getElementById("input_estado").value = estado;
                },
                error: function (jqXHR, text, error) {
                    console.log('[' + error + ']: ' + 'Entre em contato com o suporte');
                }
            }).done(function (data) {
                $('#loading').fadeOut();
            });
        }
    });

    setFormValidation('#form_new_member');
});

function setFormValidation(id) {
    $(id).validate({
        highlight: function (element) {
            $(element).closest('.form-group').removeClass('has-success').addClass('has-danger');
            $(element).closest('.form-check').removeClass('has-success').addClass('has-danger');
        },
        success: function (element) {
            $(element).closest('.form-group').removeClass('has-danger').addClass('has-success');
            $(element).closest('.form-check').removeClass('has-danger').addClass('has-success');
        },
        errorPlacement: function (error, element) {
            $(element).append(error);
        },
    });
}