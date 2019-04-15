@extends('layout.admin')

@section('styles')
    <link rel="stylesheet" href="/admin/css/dropify.css"/>
    <link rel="stylesheet" href="/admin/cdn/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css"/>
    <link rel="stylesheet" href="/admin/css/pages/users/user.css"/>
@endsection

@section('content')
    <div class="panel-header panel-header-sm">
    </div>
    <div class="content">
        <form id="users_form" method="post" action="{{ route('admin.users.create') }}" class="form-horizontal">
            {{ csrf_field() }}
            <div class="row">
                <div class="col-md-8 center">
                    <div class="card pessoa">
                        <div class="card-header">
                            <h4 class="card-title">Usuário</h4>
                        </div>
                        <div class="card-body">
                            <div class="group center">
                                <div class="form-group has-label">
                                    <label>Imagem de Perfil</label>
                                    <input type="file" id="input-file-max-fs" class="dropify" name="image" data-max-file-size="10M" />
                                    <label>Maximum file upload size 10MB</label>
                                </div>
                                <div class="form-group has-label">
                                    <label>
                                        Nome
                                    </label>
                                    <input class="form-control" name="name" type="text" required="true" minlength="2"/>
                                </div>
                                <div class="form-group has-label">
                                    <label>
                                        Sobrenome
                                    </label>
                                    <input class="form-control" name="lastname" type="text" required="true" minlength="2"/>
                                </div>
                                <div class="form-group has-label">
                                    <label>
                                        E-mail
                                    </label>
                                    <input class="form-control" name="email" type="text" required="true" minlength="2"/>
                                </div>
                                <div class="form-group has-label">
                                    <label>
                                        Senha
                                    </label>
                                    <input class="form-control" name="password" type="password" required="true" minlength="2"/>
                                </div>
                                <div class="form-group has-label">
                                    <label>
                                        Nível
                                    </label>
                                    <div class="select-wrapper center">
                                        <select name="level" class="selectpicker center" data-size="7" data-style="btn btn-primary btn-round" title="Selecionar">
                                            <option value='{{ \App\Models\User::ADMIN }}'>
                                                Administrador
                                            </option>
                                            <option value='{{ \App\Models\User::EDITOR }}'>
                                                Editor
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group has-label">
                                    <label>
                                        Sobre o Usuário
                                    </label>
                                    <div class='textarea-editor'></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8 center">
                    <div class="box-footer">
                        <button id="btnCancel" class="btn btn-primary btn-edit delete">
                            Cancelar
                        </button>
                        <button id="btnSave" type="submit" class="btn btn-primary btn-edit save" name="btn_save">
                            Salvar
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script src="/admin/js/dropify.js"></script>
    <script src="/admin/cdn/jquery/jquery.validate.min.js"></script>
    <script src="/admin/cdn/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
    <script src="/admin/cdn/mascara_js/mascara.min.js"></script>
    <script src="/admin/js/pages/users/user.js"></script>
@endsection