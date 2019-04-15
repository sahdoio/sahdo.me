<!DOCTYPE html>
<html lang="pt_br">
<head>
    <style>
        .licence-card-wrapper {
            padding: 20px;
            font-family: 'Roboto', sans-serif;
        }

        .licence-card {
            width: 640px;
            height: 380px;
            background: #eeefff;
            margin: 0 auto;
            position: relative;
        }

        .licence-field .field-title {
            font-weight: 600;
        }

        .left-side {
            width: 49%;
            float: left;
        }

        .right-side {
            width: 45%;
            float: right;
            padding-top: 20px;
        }

        .licence-card-picture {
            width: 100%;
            display: block;
            padding: 5px;
        }

        .licence-card-picture img {
            width: 100%;
        }
    </style>
</head>
<body id="{{ $page or 'default'}}">
    <div class="licence-card">
        <div class="licence-card-wrapper">
            <div class="left-side">
                <div class="licence-card-picture">
                    @if(isset($member->media->url))
                        <img src="{{ isset($member->media->url) ? url($member->media->url) : 'not found' }}">
                    @else
                        <h1 class="license-not-found-image">Indisponível</h1>
                    @endif
                </div>
            </div>
            <div class="right-side">
                <div class="licence-field">
                    <p class="field-title">Nome:</p>
                    <p>{{ $member->name . ' ' . $member->lastname}}</p>
                </div>

                <div class="licence-field">
                    <p class="field-title">Número:</p>
                    <p>{{ $member->id }}</p>
                </div>

                <div class="licence-field">
                    <p class="field-title">Data nascimento:</p>
                    <p>{{ $member->birth_date }}</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>