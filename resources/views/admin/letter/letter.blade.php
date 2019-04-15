@extends('layout.admin')

@section('styles')
    <link rel="stylesheet" href="/admin/css/dropify.css"/>
    <link rel="stylesheet" href="/admin/css/pages/license/license.css"/>
@endsection

@section('content')
    <div class="panel-header panel-header-sm">
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-8 center">
                <div class="card">
                    <div class="card-header">
                        <div class="row section">
                            <h2 class="section-title col s12 m6">Carta de Recomendação</h2>
                            <div class="btn-box col s12 m6">
                                <a class="btn btn-success add" href="{{ route('admin.letter.export', $member->id) }}">
                                    Exportar
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="license-box group center">
                            <div class="license-card-template">
                                @include('admin.license.card_template', ['member' => $member])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="/admin/js/dropify.js"></script>
    <script src="/admin/cdn/jquery/jquery.validate.min.js"></script>
    <script src="/admin/cdn/mascara_js/mascara.min.js"></script>
    <script src="/admin/js/pages/license/license.js"></script>
@endsection

