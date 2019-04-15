$(document).ready(function() {
    $('.btn.add').on('click', function () {
        console.log('click add');
        window.location.href = $(this).data('href');
    });

    $('.btn.edit').on('click', function () {
        console.log('bla');
        var url = $(this).data('edit');
        window.location.href = url;
    });

    $('.btn.delete').on('click', function () {
        var id = $(this).closest('.item-banner').data('id');
        var url = $(this).data('delete');
        var data = {
            id: id
        };

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
        }).then(function(isConfirm) {
            if (isConfirm) {
                $.ajax({
                    type: "POST",
                    url: url,
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        status = (typeof data['status'] !== 'undefined') ? data['status'] : 'error';
                        message = (typeof data['message'] !== 'undefined') ? data['message'] : 'Erro ao executar ação. Contactar suporte';
                        redirect = (typeof data['redirect'] !== 'undefined') ? data['redirect'] : false;

                        if (status == "ok") {
                            swal({
                                title: "Feito!",
                                text: "Mídia deletada com sucesso",
                                type: 'success'
                            }).then(function() {
                                window.location.href = redirect;
                            });
                        }
                        else {
                            swal("Oops...", message, "error");
                        }
                    }
                });
            }
            else {
                swal("Cancelado", "Mídia está segura :)", "error");
            }
        });
    });
});