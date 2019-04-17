<section class="tiles">
    @php
        $i_style = 1;
    @endphp
    @if($posts)
        @foreach($posts as $post)
            <article class="style{{ $i_style }}">
                <span class="image">
                    <img src="{{ asset('media/blog_post.jpg') }}" alt="{{ $post->title }}" />
                </span>

                <a href="{{ route('blog.post', $post->id) }}">
                    <h2>{{ $post->title }}</h2>
                    <div class="content">
                        <div class="post-body">
                            {{ \Illuminate\Support\Str::limit($post->body, 50, '...') }}
                        </div>
                    </div>
                </a>
            </article>
            @php
                if ($i_style >= 6) $i_style = 1;
                $i_style++
            @endphp
        @endforeach
    @else
        <div style="text-align: center;
                    display: block;
                    width: 100%;
                    margin-top: 10%;">
            <h2>Nenhuma publicação encontrada...</h2>
        </div>
    @endif
</section>