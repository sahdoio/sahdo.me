@extends('layout.admin')

@section('styles')
    <link rel="stylesheet" href="/admin/css/dropify.css"/>
    <link rel="stylesheet" href="/admin/css/pages/banners/new.css"/>
@endsection

@section('content')
    <div class="panel-header panel-header-sm">
    </div>
    <section id="banner-content" class="content"> 
        <form id="banner_form" action="{{ route('admin.banners.create') }}" method='post' role="form" enctype="multipart/form-data">
            {{ csrf_field() }}        
            <div class="form-group">
                <div class="row section">
                    <div class="col s12 m4 l3"> 
                        <p class="left-title">Título do Banner</p>
                    </div>
                    
                    <div class="col s12 m8 l9">
                        <p>Text Area</p>
                        <textarea class="form-control" name="title" rows="3" placeholder="Enter Title..."></textarea>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row section">
                    <div class="col s12 m4 l3"> 
                        <p class="left-title">Subtítulo do Banner</p>
                    </div>
                    
                    <div class="col s12 m8 l9">
                        <p>Text Area</p>
                        <textarea class="form-control" name="subtitle" rows="3" placeholder="Enter Title..."></textarea>
                    </div>
                </div>
            </div>

            <div class="row section">
                <div class="col s12 m4 l3">
                    <p class="left-title">Imagem do Banner</p>
                </div>
                
                <div class="col s12 m8 l9">
                    <p>Maximum file upload size 2MB</p>
                    <input type="file" id="input-file-max-fs" class="dropify" name="image" data-max-file-size="10M" />
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
    <script src="/admin/js/dropify.js"></script>
    <script src="/admin/js/pages/banners/new.js"></script>
@endsection