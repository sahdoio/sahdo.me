@extends('layout.admin')

@section('styles')
    <link rel="stylesheet" href="/admin/css/dropify.css"/>
    <link rel="stylesheet" href="/admin/css/pages/banners/new.css"/>
@endsection

@section('content')
    <div class="panel-header panel-header-sm">
    </div>
    <section id="banner-content" class="content">
        <form id="banner_form" action="{{ route('admin.banners.update', $banner->id) }}" method='post' role="form" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="form-group">
                <div class="row section">
                    <div class="col s12 m4 l3">
                        <p class="left-title">Título do Banner</p>
                    </div>
                    <div class="col s12 m8 l9">
                        <textarea class="form-control banner-title" name="title" rows="3" placeholder="Enter Title..."></textarea>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row section">
                    <div class="col s12 m4 l3">
                        <p class="left-title">Subtítulo do Banner</p>
                    </div>
                    <div class="col s12 m8 l9">
                        <textarea class="form-control banner-subtitle" name="subtitle" rows="3" placeholder="Enter Title..."></textarea>
                    </div>
                </div>
            </div>

            <div class="row section">
                <div class="col s12 m4 l3">
                    <p class="left-title">Imagem do Banner</p>
                </div>
                <div class="col s12 m8 l9">
                    <p>Maximum file upload size 10MB</p>
                    <input type="file" id="input-file-max-fs" class="dropify" name="image" data-max-file-size="10M" data-default-file="{{ url($banner->url) }}"/>
                </div>
            </div>

            <div class="box-footer">
                <button id="btnCancel" class="btn btn-primary btn-edit delete">
                    Cancelar
                </button>
                <button id="btnSave" type="submit" class="btn btn-primary btn-edit save" name="btn_save">
                    Salvar
                </button>
            </div>
        </form>
    </section>
@endsection

@section('scripts')
    <script type="text/javascript">
        var title = '{{ $banner->title or '' }}';
        document.querySelector('textarea.banner-title').innerHTML = title;

        var subtitle = '{{ $banner->subtitle or '' }}';
        document.querySelector('textarea.banner-subtitle').innerHTML = subtitle;
    </script>
    <script src="/admin/js/dropify.js"></script>
    <script src="/admin/js/pages/banners/edit.js"></script>
@endsection