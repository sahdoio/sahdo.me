<section class="tiles">
    @php
        $i_style = 1;
    @endphp
    @foreach($posts as $post)
    <article class="style{{ $i_style }}">
        <span class="image">
            <img src="{{ asset('storage/media/blog_post.jpg') }}" alt="{{ $post->title }}" />
        </span>

        <a href="{{ route('blog.post', $post->id) }}">
            <h2>{{ $post->title }}</h2>
            <div class="content">
                <div class="post-body">
                    {{ $post->body }}
                </div>
            </div>
        </a>
    </article>
    @php
        if ($i_style >= 6) $i_style = 1;
        $i_style++
    @endphp
    @endforeach
</section>