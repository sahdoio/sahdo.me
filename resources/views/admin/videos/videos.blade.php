@extends('layout.admin')

@section('styles')
    <link rel="stylesheet" href="/admin/cdn/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css"/>
    <link rel="stylesheet" href="/admin/css/dropify.css"/>
    <link rel="stylesheet" href="/admin/css/pages/jobs/jobs.css"/>
@endsection

@section('content')
    <div class="panel-header panel-header-sm">
    </div>

    <section id="jobs-content" class="content">
        <div class="jobs">
            <div class="form-group">
                <div class="row section">
                    <h2 class="section-title col s12 m6">Suas Coleções de Vídeos</h2>
                    <div class="jobs-btn-box col s12 m6">
                        <button id="buttonAdd" type="button" class="btn btn-success add" data-href="{{ route('admin.jobs.new') }}">
                            Novo
                        </button>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    @foreach($jobs as $i => $job)
                        @php
                            $front_id = $i + 1;
                        @endphp

                        @if($job->cover_media->type_id == \App\Models\MediaType::VIMEO)
                            <div class="item-job col-sm-4 grid" data-id="{{ $job->id }}" data-front-id="{{ $front_id }}">
                                <figure class="effect-roxy">
                                    <iframe src="{{ $job->cover_media->url }}" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                                    <figcaption>
                                        <div class="button-wrapper">
                                            <button id="btnEdit" type="button" class="btn btn-primary edit" data-edit="{{ route('admin.jobs.edit', $job->id) }}">Edit</button>
                                            <button id="btnDelete" type="button" class="btn btn-primary delete" data-delete="{{ route('admin.jobs.delete', $job->id) }}">Delete</button>
                                        </div>
                                        <h2>{{ $job->title }}</h2>
                                        <a href="{{ route('admin.jobs.edit', $job->id) }}">View more</a>
                                    </figcaption>
                                </figure>
                            </div>
                        @elseif($job->cover_media->isImage() || $job->cover_media->isVideo())
                            <div class="item-job col-sm-4 grid" data-id="{{ $job->id }}" data-front-id="{{ $front_id }}">
                                <figure class="effect-roxy">
                                    <img src="{{ isset($job->cover_media->url) ? url($job->cover_media->url) : '' }}" alt="{{"Job $front_id"}}"/>
                                    <figcaption>
                                        <div class="button-wrapper">
                                            <button id="btnEdit" type="button" class="btn btn-primary edit" data-edit="{{ route('admin.jobs.edit', $job->id) }}">Edit</button>
                                            <button id="btnDelete" type="button" class="btn btn-primary delete" data-delete="{{ route('admin.jobs.delete', $job->id) }}">Delete</button>
                                        </div>
                                        <h2>{{ $job->title }}</h2>
                                        <a href="{{ route('admin.jobs.edit', $job->id) }}">View more</a>
                                    </figcaption>
                                </figure>
                            </div>
                        @else
                            <div class="folio-media">
                                <h1>Mídia desconhecida</h1>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="/admin/cdn/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
    <script src="/admin/js/pages/jobs/jobs.js"></script>
@endsection