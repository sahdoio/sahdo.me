@extends('layout.admin')

@section('styles')
    <link rel="stylesheet" href="/admin/css/pages/users/users.css">
@endsection

@section('content')
    <div class="panel-header panel-header-sm"></div>
    <div class="content">
        <div class="col-md-12">
            <div class="row">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">Lista de Usuários</h4>
                            </div>
                            <div class="col-md-6">
                                <button id="btn-new" class="btn btn-like btn-primary btn-round" style="float:right" onclick="window.location.href='{{ route('admin.users.new') }}'">
                                    Adicionar
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="toolbar">
                        </div>
                        <table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                            <tr>
                                <th>N</th>
                                <th>Nome</th>
                                <th>Sobrenome</th>
                                <th>Email</th>
                                <th>Sobre</th>
                                <th>Nível</th>
                                <th class="disabled-sorting text-center">Ações</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>N</th>
                                <th>Nome</th>
                                <th>Sobrenome</th>
                                <th>Email</th>
                                <th>Sobre</th>
                                <th>Nível</th>
                                <th class="disabled-sorting text-center">Ações</th>
                            </tr>
                            </tfoot>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <!-- end content-->
                </div>
                <!--  end card  -->
            </div>
            <!-- end col-md-12 -->
        </div>
    </div>
@endsection

@section('scripts')
    <script src="/admin/cdn/jquery/jquery.dataTables.min.js"></script>
    <script src="/admin/js/pages/users/users.js"></script>
@endsection

