@extends('layout.admin')

@section('styles')
    <link rel="stylesheet" href="/admin/css/dropify.css"/>
    <link rel="stylesheet" href="/admin/css/pages/pages/about.css"/>
@endsection

@section('content')
    <div class="panel-header panel-header-sm">
    </div>
    <section id="about-content" class="content">
        <form id="about_form" action="{{ route('admin.pages.about.update') }}" method="post" role="form" enctype="multipart/form-data" data-id="{{ $id or null }}">
            {{ csrf_field() }}
            <div class="form-group">
                <div class="row section">
                    <div class="col s12 m4 l3">
                        <p class="left-title">Bio - Título</p>
                    </div>

                    <div class="col s12 m4 l3">
                        <p>Text Area</p>
                        <div id="about_bio_title" class='textarea-editor'>
                            {!! $site_info->about_bio_title or '' !!}
                        </div>
                    </div>
                </div>

                <div class="row section">
                    <div class="col s12 m4 l3">
                        <p class="left-title">Bio - Descrição</p>
                    </div>

                    <div class="col s12 m8 l9">
                        <p>Text Area</p>
                        <div id="about_bio" class='textarea-editor'>
                            {!! $site_info->about_bio or '' !!}
                        </div>
                    </div>
                </div>

                <div class="divider row"></div>

                <div class="row section">
                    <div class="col s12 m4 l3">
                        <p class="left-title">Imagem Perfil</p>
                    </div>

                    <div class="col s12 m8 l9">
                        <p>Maximum file upload size 20MB</p>
                        <input type="file" id="mission-image" class="dropify" name="profile_image" data-max-file-size="10M" data-default-file="{{ url($site_info->profile_image->url) }}"/>
                    </div>
                </div>

                <div class="divider row"></div>

                <div class="row section">
                    <div class="col s12 m4 l3">
                        <p class="left-title">Banner</p>
                    </div>

                    <div class="col s12 m8 l9">
                        <p>Maximum file upload size 20MB</p>
                        <input type="file" class="dropify" name="about_banner" data-max-file-size="10M" data-default-file="{{ url($site_info->about_banner->url) }}"/>
                    </div>
                </div>

                <div class="divider row"></div>

                <div class="row section">
                    <div class="col s12 m4 l3">
                        <p class="left-title">Reel</p>
                    </div>

                    <div class="col s12 m8 l9">
                        <p>Vimeo - Link do vídeo:</p>
                        <input type="text" class="form-control video-input" name="about_reel" value="{{ $site_info->about_reel->url }}"  old-value="{{ $site_info->about_reel->url }}"/>
                        <br>
                        <div class="video-box">
                            <iframe src="{{ $site_info->about_reel->url }}" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <button id="btnCancel" class="btn btn-primary btn-edit delete">
                    Cancelar
                </button>
                <button id="btnSave" type="submit" class="btn btn-primary btn-edit save">
                    Salvar
                </button>
            </div>
        </form>
    </section>
@endsection

@section('scripts')
    <script src="/admin/cdn/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
    <script src="/admin/js/dropify.js"></script>
    <script src="/admin/js/pages/pages/about.js"></script>
@endsection