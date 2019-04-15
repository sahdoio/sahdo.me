@extends('layout.admin')

@section('styles')
    <link rel="stylesheet" href="/admin/css/dropify.css"/>
    <link rel="stylesheet" href="/admin/css/pages/blog/blog.css"/>
@endsection

@section('content')
    <div class="panel-header panel-header-sm">
    </div>
    <section id="banners-content" class="content">
        <div class="row section">
            <h2 class="section-title col s12 m6">Blog</h2>
            <div class="banners-btn-box col s12 m6">
                <button id="buttonAdd" class="btn btn-success add" data-href="{{ url('admin/banners/new') }}">
                    Nova Publicação
                </button>
            </div>
        </div>

        <div class="row">
        </div>
    </section>
@endsection

@section('scripts')
    <script src="/admin/js/pages/blog/blog.js"></script>
@endsection