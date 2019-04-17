@extends('layout.admin')

@section('styles')
    <link rel="stylesheet" href="/admin/css/dropify.css"/>
    <link rel="stylesheet" href="/admin/css/pages/blog/blog.css"/>
@endsection

@section('content')
    <div class="panel-header panel-header-sm">
    </div>
    <section id="blog-content" class="content">
        <div class="row section">
            <h2 class="section-title col s12 m6">Publicações</h2>
            <div class="blogs-btn-box col s12 m6">
                <a id="buttonAdd" class="btn btn-success add" href="{{ route('admin.blog.new') }}">
                    Nova Publicação
                </a>
            </div>
        </div>

        <div class="row">
            @if($posts)
            @foreach($posts as $i => $post)
                @php
                    $front_id = $i + 1;
                @endphp
                <div class="item-blog col-sm-6 grid" data-id="{{ $post->id }}" data-front-id="{{ $front_id }}">
                    <figure class="effect-roxy">
                        <img src="{{ asset('media/blog_post.jpg') }}" alt="{{"Artigo $front_id"}}"/>
                        <figcaption>
                            <div class="button-wrapper">
                                <button id="btnEdit" type="submit" class="btn btn-primary edit" data-edit="{{ route('admin.blog.edit', $post->id) }}">Edit</button>
                                <button id="btnDelete" type="submit" class="btn btn-primary delete"data-delete="{{ route('admin.blog.delete', $post->id) }}">Delete</button>
                            </div>
                            <h2>
                                {{ \Illuminate\Support\Str::limit($post->title, 20, '...') }}
                            </h2>
                            <p>{{ \Illuminate\Support\Str::limit($post->body, 100, '...') }}</p>
                            <a href="{{ route('admin.blog.edit', $post->id) }}">View more</a>
                        </figcaption>
                    </figure>
                </div>
            @endforeach
            @else
                <div style="text-align: center;
                    display: block;
                    width: 100%;
                    margin-top: 10%;">
                    <h2>Nenhuma publicação encontrada...</h2>
                </div>
            @endif
        </div>
    </section>
@endsection

@section('scripts')
    <script src="/admin/js/pages/blog/blog.js"></script>
@endsection