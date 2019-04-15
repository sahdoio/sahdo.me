@extends('layout.website') 

@section('content')
    @if($post)
        <h1>{{ $post->title }}</h1>
        <div class="post-content">
            {!! $post->body !!}
        </div>
    @else
        <h1 style="margin:auto; display:block; text-align:center; font-weight: 700">Post não encontrado :(</h1>
    @endif
@endsection