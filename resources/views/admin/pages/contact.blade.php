@extends('layout.admin')

@section('styles')
<link rel="stylesheet" href="/admin/css/dropify.css"/>
<link rel="stylesheet" href="/admin/css/pages/pages/contact.css"/>
@endsection

@section('content')
<div class="panel-header panel-header-sm">
</div>
<section id="contact-content" class="content">
    <form id="contact_form" action="{{ route('admin.pages.contact.update') }}" method="post" role="form" enctype="multipart/form-data" data-id="{{ $id or null}}">
        {{ csrf_field() }}
        <div class="form-group">
            <div class="row section">
                <h2 class="section-title">Informações de Contato</h2>
            </div>

            <div class="row section">
                <div class="col s12 m4 l3">
                    <p class="left-title">Telefone</p>
                </div>

                <div class="col s12 m8 l9">
                    <input class="form-control" value="{{ $site_info->contact->cellphone or '' }}" name="cellphone" type="text" required="true" minlength="2"/>
                </div>
            </div>

            <div class="row section">
                <div class="col s12 m4 l3">
                    <p class="left-title">Telefone 2</p>
                </div>

                <div class="col s12 m8 l9">
                    <input class="form-control" value="{{ $site_info->contact->cellphone2 or '' }}" name="cellphone2" type="text" required="true" minlength="2"/>
                </div>
            </div>

            <div class="row section">
                <div class="col s12 m4 l3">
                    <p class="left-title">E-mail</p>
                </div>

                <div class="col s12 m8 l9">
                    <input class="form-control" value="{{ $site_info->contact->email or '' }}" name="email" type="text" required="true" minlength="2"/>
                </div>
            </div>

            <div class="row section">
                <div class="col s12 m4 l3">
                    <p class="left-title">E-mail 2</p>
                </div>

                <div class="col s12 m8 l9">
                    <input class="form-control" value="{{ $site_info->contact->email2 or '' }}" name="email2" type="text" required="true" minlength="2"/>
                </div>
            </div>

            <div class="row section">
                <div class="col s12 m4 l3">
                    <p class="left-title">Facebook</p>
                </div>

                <div class="col s12 m8 l9">
                    <input class="form-control" value="{{ $site_info->contact->facebook or '' }}" name="facebook" type="text" required="true" minlength="2"/>
                </div>
            </div>

            <div class="row section">
                <div class="col s12 m4 l3">
                    <p class="left-title">Instagram</p>
                </div>

                <div class="col s12 m8 l9">
                    <input class="form-control" value="{{ $site_info->contact->instagram or '' }}" name="instagram" type="text" required="true" minlength="2"/>
                </div>
            </div>

            <div class="row section">
                <div class="col s12 m4 l3">
                    <p class="left-title">Youtube</p>
                </div>

                <div class="col s12 m8 l9">
                    <input class="form-control" value="{{ $site_info->contact->youtube or '' }}" name="youtube" type="text" required="true" minlength="2"/>
                </div>
            </div>

            <div class="row section">
                <div class="col s12 m4 l3">
                    <p class="left-title">Flickr</p>
                </div>

                <div class="col s12 m8 l9">
                    <input class="form-control" value="{{ $site_info->contact->flickr or '' }}" name="flickr" type="text" required="true" minlength="2"/>
                </div>
            </div>

            <div class="row section">
                <div class="col s12 m4 l3">
                    <p class="left-title">Twitter</p>
                </div>

                <div class="col s12 m8 l9">
                    <input class="form-control" value="{{ $site_info->contact->twitter or '' }}" name="twitter" type="text" required="true" minlength="2"/>
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
@endsection

@section('scripts')
<script src="/admin/js/dropify.js"></script>
<script src="/admin/js/pages/pages/contact.js"></script>
@endsection