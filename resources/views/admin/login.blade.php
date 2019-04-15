<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200"/>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.6/css/all.css">
    <link rel="stylesheet" href="/admin/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="/admin/css/now-ui-dashboard.css?v=1.1.0"/>
    <link rel="stylesheet" href="/admin/css/pages/login.css"/>
</head>
<body class="sidebar-mini">
    <section>
        <div class="wrapper wrapper-full-page ">
            <div class="full-page login-page">
                <div class="content">
                    <div class="container">
                        <div class="col-md-5 ml-auto mr-auto">
                            <form id="formLogin" class="form" method="post" action="{{ route('login.in') }}">
                                {{ csrf_field() }}
                                <div class="card card-login card-plain">
                                    <div class="card-header ">
                                        <div class="logo-container">
                                            <img src="{{ asset('/storage/media/logo/white-logo.png') }}" alt="">
                                        </div>
                                    </div>
                                    <div class="card-body ">
                                        <div class="input-group no-border form-control-lg">
                                            <span class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="now-ui-icons users_circle-08"></i>
                                                </div>
                                            </span>
                                            <input name="email" type="text" class="form-control" placeholder="E-mail">
                                        </div>
                                        <div class="input-group no-border form-control-lg">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="now-ui-icons text_caps-small"></i>
                                                </div>
                                            </div>
                                            <input name="password" type="password" placeholder="Senha" class="form-control">
                                        </div>

                                        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
                                    </div>
                                    <div class="card-footer ">
                                        <a id="btnSubmit" href="javascript:{}" class="btn btn-primary btn-round btn-lg btn-block mb-3">INICIAR</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>            
        </div>      
    </section>

    <script src="/admin/js/core/jquery.min.js"></script>
    <script src="/admin/js/core/bootstrap.min.js"></script>
    <script src="/admin/js/plugins/sweetalert2.min.js"></script>
    <script src="/admin/js/pages/login.js"></script>
</body>
</html>