@extends('layout.admin')

@section('styles')
    <link rel="stylesheet" href="/admin/css/dropify.css"/>
    <link rel="stylesheet" href="/admin/css/pages/jobs/job.css"/>
@endsection

@section('content')
    <div class="panel-header panel-header-sm">
    </div>
    <section id="job-content" class="content">
        <form id="job_form" action="{{ route('admin.jobs.create') }}" method='post' role="form" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="form-group">
                <div class="row section">
                    <div class="col s12 m4 l3">
                        <p class="left-title">Título do Job</p>
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
                                        <option value="{{ $client->id }}">
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
                        @endphp
                        <input type="text" name="date" class="form-control datepicker" value="{{ $today }}" onkeyup="mascara('##/##/####',this,event)" minlength="10" maxlength="10">
                    </div>
                </div>
            </div>

            <div id="media_group">
                <div class="media_item form-group" data-position=1>
                    <button type="button" class="btn-delete-media">
                        <i class="now-ui-icons ui-1_simple-remove"></i>
                    </button>
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