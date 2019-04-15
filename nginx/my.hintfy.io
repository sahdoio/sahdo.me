server {
    listen 80;
    listen [::]:80;

    root /data/my.hintfy.io/public;

    # Add index.php to the list if you are using PHP
    index index.html index.php index.htm index.nginx-debian.html;

    server_name my.rec-io.com; #my.hintfy.io;

    location / {
      try_files $uri $uri/ /index.php?$query_string;
    }

    # Execute PHP scripts
    location ~ \.php$ {
      fastcgi_index   index.php;
      fastcgi_pass    unix:/run/php/php5.6-fpm.sock;
      include         fastcgi_params;
      fastcgi_param   SCRIPT_FILENAME    $document_root$fastcgi_script_name;
      fastcgi_param   SCRIPT_NAME        $fastcgi_script_name;
      fastcgi_buffers 16 16k;
      fastcgi_buffer_size 32k;
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

