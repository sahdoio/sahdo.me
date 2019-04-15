@extends('layout.admin')

@section('styles')
    <link rel="stylesheet" href="/admin/css/dropify.css"/>
    <link rel="stylesheet" href="/admin/css/pages/jobs/job.css"/>
@endsection

@section('content')
    <div class="panel-header panel-header-sm">
    </div>
    <section id="job-content" class="content">
        <form id="job_form" action="{{ route('admin.jobs.update', $job->id) }}" method='post' role="form" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="form-group">
                <div class="row section">
                    <div class="col s12 m4 l3">
                        <p class="left-title">Título do Job</p>
                    </div>
                    <div class="col s12 m8 l9">
                        <p>Text Area</p>
                        <textarea class="form-control" name="title" rows="3" placeholder="Enter Title...">{{ $job->title or '' }}</textarea>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row section">
                    <div class="col s12 m4 l3">
                        <p class="left-title">Descrição do Job</p>
                    </div>
                    <div class="col s12 m8 l9">
                        <p>Text Area</p>
                        <div id="description" class='textarea-editor'>
                            {!! $job->description or '' !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row section">
                    <div class="col s12 m4 l3">
                        <p class="left-title">Cliente</p>
                    </div>
                    <div class="col s12 m8 l9">
                        <div class="select-wrapper center">
                            <select id="doctor-select" class="selectpicker center" name="client_id" data-size="7" data-live-search="true" data-style="btn btn-primary btn-round" title="Selecionar">
                                <option class="option-new" value='/clients/new'>
                                    + Novo
                                </option>
                                <optgroup label="Clientes">
                                    <option value="">
                                        Sem vínculo
                                    </option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" {{ ($job->client_id == $client->id) ? 'selected' : ''}}>
                                            {{ isset($client->company_name) ? $client->company_name . ' - ' . $client->title . ' ' . $client->lastname : $client->title . ' ' . $client->lastname }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row section">
                    <div class="col s12 m4 l3">
                        <p class="left-title">Data de Criação</p>
                    </div>
                    <div class="col s12 m8 l9">
                        <p>Escolha a data</p>
                        @php
                            $today = date("d/m/y");
                            $date = isset($schedule->date) ? (new DateTime($schedule->date))->format('d/m/Y') : $today;
                        @endphp
                        <input type="text" name="date" class="form-control datepicker" value="{{ $job->date or '' }}" onkeyup="mascara('##/##/####',this,event)" minlength="10" maxlength="10">
                    </div>
                </div>
            </div>

            @if(isset($job->medias))
                <div id="media_group">
                    @foreach($job->medias as $i => $media)
                        <div class="media_item form-group" data-position={{ $i + 1 }} data-id={{ $media->id }}>
                            <button type="button" class="btn-delete-media">
                                <i class="now-ui-icons ui-1_simple-remove"></i>
                            </button>
                            <div class="row section">
                                <div class="col s12 m4 l3">
                                    <p class="left-title">Mídia {{ $i + 1 }}</p>
                                </div>

                                @if($media->type_id == \App\Models\MediaType::VIMEO)
                                    <div class="col s12 m8 l9">
                                        <p>Vimeo - Link do vídeo:</p>
                                        <input type="text" class="form-control video-input" name="media{{ $i + 1 }}" value="{{ $media->url }}"  old-value="{{ $media->url }}"/>
                                        <br>
                                        <div class="video-box">
                                            <iframe src="{{ $media->url }}" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                                        </div>
                                    </div>
                                @elseif(
                                    $media->type_id == \App\Models\MediaType::GENERIC ||
                                    $media->type_id == \App\Models\MediaType::JPG ||
                                    $media->type_id == \App\Models\MediaType::PNG ||
                                    $media->type_id == \App\Models\MediaType::GIF
                                )
                                    <div class="col s12 m8 l9">
                                        <p>Tipos: PNG, JPG, GIF</p>
                                        <p>Tamanho máximo de 10MB</p>
                                        <input type="file" class="dropify" name="media{{ $i + 1 }}" data-max-file-size="10M" data-default-file="{{ url($media->url) }}" />
                                    </div>
                                @else
                                    <div class="col s12 m8 l9">
                                        <h1>Mídia desconhecida</h1>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div id="media_group">
                    <div class="media_item form-group" data-position=1>
                        <div class="row section">
                            <div class="col s12 m4 l3">
                                <p class="left-title">Mídia 1</p>
                            </div>

                            <div class="col s12 m8 l9">
                                <p>Tipos: PNG, JPG, GIF, MP4</p>
                                <p>Tamanho máximo de 10MB</p>
                                <input type="file" id="input-file-max-fs" class="dropify" name="media1" data-max-file-size="10M" />
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="form-group">
                <div class="row section">
                    <div class="col-md-12 center">
                        <button id="add-media" type="button" class="btn btn-primary btn-round">
                            <i class="now-ui-icons ui-1_simple-add"></i>
                            Adicionar Mídia
                        </button>
                    </div>
                </div>
            </div>

            <div id="deleted-checkbox-group" class="form-group" style="display: none"></div>

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

    @include('admin.jobs.type_modal')
@endsection

@section('scripts')
    <script src="/admin/js/dropify.js"></script>
    <script src="/admin/js/pages/jobs/job.js"></script>
@endsection