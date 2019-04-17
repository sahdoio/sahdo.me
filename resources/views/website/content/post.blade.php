@extends('layout.website') 

@section('styles')
    <style>
        .comments-area {
            padding: 10px;
            margin-top: 80px;
        }

        .commentForm button.btn.btn-info.pull-right {
            margin-top: 15px;
        }

        .comment-wrapper {
            margin-bottom: 50px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
        }

        .commentForm {
            border: 1px solid #ccc;
            padding: 25px;
            border-radius: 10px;
        }
    </style>
@endsection

@section('content')
    @if($post)
        <h1>{{ $post->title }}</h1>
        <div class="post-content">
            {!! $post->body !!}
        </div>

        <div class="comments-area">
            <h3>
                Comentários
            </h3>

            <form class="commentForm" action="{{ route('posts.comments.new', $post->id) }}" method="post">
                {{ csrf_field() }}

                <div class="form-group">
                    <div class="col-lg-6">
                        <input type="text" placeholder="Nome" name="name" class="form-control">
                    </div>
                    <br>
                    <div class="col-lg-6">
                        <input type="text" placeholder="Email" name="email" class="form-control">
                    </div>
                </div>
                <br>
                <div class="form-group">
                    <div class="col-lg-12">
                        <textarea placeholder="Message" name="comment" rows="8" class="form-control"></textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-info pull-right">
                    Enviar
                </button>
            </form>

            <div class="comments-box">
                @foreach($comments as $comment)
                <div class="comment">
                    <div class="comment-wrapper">
                        <h4 class="comment-heading">
                            {{ $comment->user->name }}
                            <span>|</span>
                            @php
                                $timestamp = $comment->created_at->{'$date'}->{'$numberLong'};
                                $date = new MongoDB\BSON\UTCDateTime( $timestamp);
                                $date = $date->toDateTime()->format('d/m/Y - H:m');
                                $comment->created_at = $date;
                            @endphp
                            <span>{{ $comment->created_at }}</span>
                        </h4>
                        <div class="comment-body">
                            {!! $comment->body !!}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    @else
        <h1 style="margin:auto; display:block; text-align:center; font-weight: 700">Post não encontrado :(</h1>
    @endif
@endsection