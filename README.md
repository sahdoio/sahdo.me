# sahdo.me

[![Build Status](https://travis-ci.org/laravel/lumen-framework.svg)](https://travis-ci.org/laravel/lumen-framework)
[![Total Downloads](https://poser.pugx.org/laravel/lumen-framework/d/total.svg)](https://packagist.org/packages/laravel/lumen-framework)
[![Latest Stable Version](https://poser.pugx.org/laravel/lumen-framework/v/stable.svg)](https://packagist.org/packages/laravel/lumen-framework)
[![Latest Unstable Version](https://poser.pugx.org/laravel/lumen-framework/v/unstable.svg)](https://packagist.org/packages/laravel/lumen-framework)
[![License](https://poser.pugx.org/laravel/lumen-framework/license.svg)](https://packagist.org/packages/laravel/lumen-framework)

sahdo.me é uma aplicação desenvolvida em PHP com o framework Laravel 5.5. Trata-se de um website convencional com blog e controle de conteúdo via painel de controle.
Para testar a aplicação acesse [sahdo.me](http://sahdo.me) 

Para acessar o painel de controle acesse [sahdo.me/adm](http://sahdo.me/adm) 

Credenciais:

    login: lucassahdo@gmail.com
    senha: 123456

## Setup

Para simular o projeto em ambiente local você vai precisar configurar um servidor nginx ou apache. Não entrarei em muitos detahes, mas fornecerei o setup que utilizei com nginx.

O primiero passo é baixar o projeto e executar o seguinte comando:

    composer update
        
Esse comando irá instalar todas a dependências do composer na pasta vendor do Laravel.

Feito isso você irá precisar criar um arquivo de configuração .env na raiz do projeto.
    
Crie o arquivo .env e cole a seguinte configuração:

    APP_NAME=sahdo.me
    APP_ENV=local
    APP_KEY=base64:3vj780Twhir3YXQZIwYgcHJZmPyOuceeSr+nHQxoCDc=
    APP_DEBUG=true
    APP_URL=http://localhost
    
    LOG_CHANNEL=stack
    
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=homestead
    DB_USERNAME=homestead
    DB_PASSWORD=secret
    
    BROADCAST_DRIVER=log
    CACHE_DRIVER=file
    QUEUE_CONNECTION=sync
    SESSION_DRIVER=file
    SESSION_LIFETIME=120
    
    REDIS_HOST=127.0.0.1
    REDIS_PASSWORD=null
    REDIS_PORT=6379
    
    MAIL_DRIVER=smtp
    MAIL_HOST=smtp.mailtrap.io
    MAIL_PORT=2525
    MAIL_USERNAME=null
    MAIL_PASSWORD=null
    MAIL_ENCRYPTION=null
    
    AWS_ACCESS_KEY_ID=
    AWS_SECRET_ACCESS_KEY=
    AWS_DEFAULT_REGION=us-east-1
    AWS_BUCKET=
    
    PUSHER_APP_ID=
    PUSHER_APP_KEY=
    PUSHER_APP_SECRET=
    PUSHER_APP_CLUSTER=mt1
    
    MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
    MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
    
    API_PREFIX=http://api.sahdo.me/

Se a pasta bootstrap/cache não existir, crie a mesma:
    
    mkdir bootstrap/cache                                              
    
Vamos precisar alterar algumas permissões, primeiramente digite:
    
    sudo chgrp -R www-data storage bootstrap/cache

Em seguida:

    sudo chmod -R ug+rwx storage bootstrap/cache
                  
Agora, conforme disse anteriormente mostrarei como configurei o virtualhost do meu servidor nginx:

    server {
        listen 80;
        listen [::]:80;
    
        root /var/www/sahdo.me/public;
    
        # Add index.php to the list if you are using PHP
        index index.html index.php index.htm index.nginx-debian.html;
    
        server_name sahdo.me;
    
        location / {
            try_files $uri $uri/ /index.php?$query_string;
        }
    
        # Execute PHP scripts
        location ~ \.php$ {
           fastcgi_pass unix:/var/run/php/php7.2-fpm.sock;
           fastcgi_split_path_info ^(.+\.php)(/.*)$;
           include fastcgi_params;
           fastcgi_param  SCRIPT_FILENAME    $document_root$fastcgi_script_name;
           fastcgi_param  HTTPS              off;
        }
    
        # deny access to .htaccess files, if Apache's document root
        # concurs with nginx's one
        location ~ /\.ht {
            deny all;
        }
    
        location ~* \.(eot|ttf|woff|woff2)$ {
           add_header Access-Control-Allow-Origin *;
        }
    }

