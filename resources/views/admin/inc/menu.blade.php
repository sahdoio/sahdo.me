<div class="sidebar" data-color="drekod">
    <div class="logo">
        <a href="/admin/dashboard" class="simple-text logo-mini">
            {{--<img class="logo-p1" src="/admin/img/logo_p1.png"></img>--}}
            <h1 class="logo-p1-text">S</h1>
        </a>
        <a href="/admin/dashboard" class="simple-text logo-normal">            
            <h1 class="logo-p2">Sahdo.me</h1>
        </a>
    </div>

    <div class="sidebar-wrapper">
        <ul class="nav">            
            <h2 class="sidebar-group-title">Conteúdo</h2>
{{--            <li class="{{ AppUtils::isActiveRoute('admin.dashboard') }}">--}}
{{--                <a href="{{ route('admin.dashboard') }}">--}}
{{--                    <i class="now-ui-icons design_app"></i>--}}
{{--                    <p>Dashboard</p>--}}
{{--                </a>--}}
{{--            </li>--}}

            @php
                if (
                    !empty(AppUtils::isActiveRoute('admin.dashboard')) ||
                    !empty(AppUtils::isActiveRoute('admin.blog')) ||
                    !empty(AppUtils::isActiveRoute('admin.blog.edit')) ||
                    !empty(AppUtils::isActiveRoute('admin.blog.new'))
                ) {
                    $active = 'active';
                }
                else {
                    $active = null;
                }
            @endphp

            <li class="{{ $active }}">
                <a href="{{ route('admin.blog') }}">
                    <i class="now-ui-icons education_paper"></i>
                    <p>Blog</p>
                </a>
            </li>

{{--            <h2 class="sidebar-group-title">Configurações</h2>--}}
{{--            <li class="{{ AppUtils::isActiveRoute('admin.users.edit') }}">--}}
{{--                <a href="{{ route('admin.users.edit', 1) }}">--}}
{{--                    <i class="now-ui-icons users_circle-08"></i>--}}
{{--                    <p>Perfil</p>--}}
{{--                </a>--}}
{{--            </li> --}}

            {{--@if(auth()->user()->level == \App\Models\User::ADMIN)--}}
{{--            <li class="{{ AppUtils::isActiveRoute('admin.users') }}">--}}
{{--                <a href="{{ route('admin.users') }}">--}}
{{--                    <i class="now-ui-icons users_single-02"></i>--}}
{{--                    <p>Usuários</p>--}}
{{--                </a>--}}
{{--            </li>--}}
            {{--@endif--}}

            <h2 class="sidebar-group-title">Sessão</h2>
            <li class="{{ AppUtils::isActiveRoute('admin.login.out') }}">
                <a id="menu_logout" href="{{ route('admin.login.out') }}">
                    <i class="now-ui-icons media-1_button-power"></i>
                    <p>Sair</p>
                </a>
            </li>       
        </ul>
    </div>
</div>