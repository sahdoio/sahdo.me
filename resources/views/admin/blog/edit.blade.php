@extends('layout.admin')

@section('styles')
    <link rel="stylesheet" href="/admin/css/pages/blog/post.css"/>
@endsection

@section('content')
    <div class="panel-header panel-header-sm">
    </div>
    <section id="blog-content" class="content">
        @if(isset($post) && $post)
        <form id="blog_form" action="{{ route('admin.blog.update', $post->id) }}" method='post' role="form" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="form-group">
                <div class="row section">
                    <div class="col s12 m4 l3">
                        <p class="left-title">Título da Publicação</p>
                    </div>
                    <div class="col s12 m8 l9">
                        <textarea class="form-control blog-title" name="title" rows="3" placeholder="Enter Title...">{!! $post->title or '' !!}</textarea>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row section">
                    <div class="col s12 m4 l3">
                        <p class="left-title">Coteúdo</p>
                    </div>
                    <div class="col s12 m8 l9">
                        <textarea class="form-control blog-body" name="body" rows="3" placeholder="Enter Title...">{!! $post->body or '' !!}</textarea>
                    </div>
                </div>
            </div>

            <div class="box-footer">
                <div class="row">
                    <button id="btnCancel" type="button" class="btn btn-primary btn-edit delete">
                        Cancelar
                    </button>
                    <button id="btnSave" type="button" class="btn btn-primary btn-edit save" name="btn_save">
                        Salvar
                    </button>
                </div>
            </div>
        </form>
        @endif
    </section>
@endsection

@section('scripts')
    <script src="/admin/js/pages/blog/post.js"></script>
@endsection