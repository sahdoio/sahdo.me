@extends('layout.admin')

@section('styles')
    <link rel="stylesheet" href="/admin/css/dropify.css"/>
    <link rel="stylesheet" href="/admin/css/pages/banners/banners.css"/>
@endsection

@section('content')
    <div class="panel-header panel-header-sm">
    </div>
    <section id="banners-content" class="content">
        <div class="row section">
            <h2 class="section-title col s12 m6">SEUS BANNERS</h2>
            <div class="banners-btn-box col s12 m6">
                <button id="buttonAdd" class="btn btn-success add" data-href="{{ url('admin/banners/new') }}">
                    ADD BANNER
                </button>
            </div>
        </div>

        <div class="row">
            @foreach($banners as $i => $media)
                @php
                    $front_id = $i + 1;
                @endphp
                <div class="item-banner col-sm-6 grid" data-id="{{ $media->id }}" data-front-id="{{ $front_id }}">
                    <figure class="effect-roxy">
                        <img src="{{ url($media->url) }}" alt="{{"Banner $front_id"}}"/>
                        <figcaption>
                            <div class="button-wrapper">
                                <button id="btnEdit" type="submit" class="btn btn-primary edit" data-edit="{{ route('admin.banners.edit', $media->id) }}">Edit</button>
                                <button id="btnDelete" type="submit" class="btn btn-primary delete"data-delete="{{ route('admin.banners.delete', $media->id) }}">Delete</button>
                            </div>
                            <h2>
                                Banner 	&nbsp;<span> {{ $front_id }}</span>
                            </h2>
                            <p>{{ $media->title }}</p>
                            <a href="{{ route('admin.banners.edit', $media->id) }}">View more</a>
                        </figcaption>
                    </figure>
                </div>
            @endforeach
        </div>
    </section>
@endsection

@section('scripts')
    <script src="/admin/js/pages/banners/banners.js"></script>
@endsection