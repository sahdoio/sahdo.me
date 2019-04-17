@if($errors->any())
    <div class="error-box"
         style="
                position: fixed;
                background: transparent;
                left: 10px;
                top: 10px;
                display: block;
                margin: 0 auto;
                width: 80%;">
        <div class="error-wrapper" style="background: #ff0909; border-radius: 13px;">
            <p style="color: #ffffff; padding: 10px 20px; font-weight: bold">
            {{$errors->first()}}
            <h1>
            </h1>
        </div>
    </div>
@endif