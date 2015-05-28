@servers(['staging' => 'root@188.166.86.110'])

@macro('staging')
    staging.pull
    staging.composer
    staging.migrate
@endmacro

@task('staging.pull', ['on' => 'staging'])
    cd /usr/share/nginx/html
    git reset --hard
    git pull origin master
    chmod -R 755 *
    chmod -R 777 storage
    chmod -R 777 public
@endtask

@task('staging.composer', ['on' => 'staging'])
    cd /usr/share/nginx/html
    composer update
    chmod -R 755 *
    chmod -R 777 storage
    chmod -R 777 public
@endtask

@task('staging.migrate', ['on' => 'staging'])
    cd /usr/share/nginx/html
    php artisan migrate
    chmod -R 755 *
    chmod -R 777 storage
    chmod -R 777 public
@endtask