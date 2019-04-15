<!DOCTYPE html>
<html lang="pt_br">
<head>
    <title>@yield('page_title', env('APP_TITLE'))</title>
    <meta charset="UTF-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('admin.inc.styles.main')
    @yield('styles')
</head>
<body id="{{ $page or 'default'}}" class="sidebar-mini">      
    <div class="wrapper">         
            @include('admin.inc.menu')
            <div class="main-panel">
                @include('admin.inc.header')     
                @yield('content')  
                @include('admin.inc.footer') 
            </div>
        </div>
    </div> 
    @include('admin.inc.components')
    @include('admin.inc.scripts.main')
    @yield('scripts')
</body>
</html>
