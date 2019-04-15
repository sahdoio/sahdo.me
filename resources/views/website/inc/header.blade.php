<header id="header">
    <div class="inner">
        <!-- Logo -->
        <a href="{{ route('home') }}" class="logo">
            <span class="symbol"><img src="{{ asset('storage/media/logo.png') }}" alt="" /></span><span class="title">Sahdo.me</span>
        </a>
        <!-- Nav -->
        <nav>
            <ul>
                <li><a href="#menu">Menu</a></li>
            </ul>
        </nav>
    </div>
</header>

<nav id="menu">
    <h2>Menu</h2>
    <ul>
        <li><a href="{{ route('home') }}">Home</a></li>
        <li><a href="{{ route('blog') }}">Blog</a></li>
    </ul>
</nav>