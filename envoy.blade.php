@servers(['staging' => 'root@188.166.86.110', 'production' => 'root@178.62.200.93'])

@setup
    $servers = ['staging', 'production'];
    if (isset($server)) $servers = [$server];
@endsetup

@macro('deploy')
    down
    pull
    composer
    migrate
    up
@endmacro

@task('down', ['on' => $servers, 'parallel' => true])
    cd /usr/share/nginx/html
    php artisan down --no-interaction
@endtask

@task('up', ['on' => $servers, 'parallel' => true])
    cd /usr/share/nginx/html
    php artisan up --no-interaction
@endtask

@task('pull', ['on' => $servers, 'parallel' => true])
    cd /usr/share/nginx/html
    git reset --hard
    git pull origin master
    chmod -R 755 *
    chmod -R 777 storage
    chmod -R 777 public
    chmod -R 777 bootstrap/cache
    php artisan route:cache
@endtask

@task('composer', ['on' => $servers, 'parallel' => true])
    cd /usr/share/nginx/html
    composer update --no-interaction
    chmod -R 755 *
    chmod -R 777 storage
    chmod -R 777 public
    chmod -R 777 bootstrap/cache
    php artisan route:cache
@endtask

@task('migrate', ['on' => $servers, 'parallel' => true])
    cd /usr/share/nginx/html
    php artisan migrate --force
    chmod -R 755 *
    chmod -R 777 storage
    chmod -R 777 public
    chmod -R 777 bootstrap/cache
    php artisan route:cache
@endtask