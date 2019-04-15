@extends('layout.website') 

@section('content')
    <header>
        <h1>
            Um site de um dev para devs.
        </h1>
        <p>
            O objetivo deste site é te manter informado sobre o mundo do desenvolvimento de software web. Abordaremos tópicos sobre o PHP moderno, Javascript,
            frameworks PHP como Laravel e Lumen, frameworks para frontend como Vue.js,
            servidores Linux, Docker, controle de versão, entre outros assuntos relacionados.
            Sinta-se livre para me contactar quando desejar. Bons estudos e bora programar!
        </p>
    </header>

    @include('website.content.blog_posts')
@endsection