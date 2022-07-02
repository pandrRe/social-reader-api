# Social Reader API

## Cloning

To work on the repository, clone it with:
```
git clone https://github.com/pandrRe/social-reader-api.git
```

## Environment Configuration

This project was created with Laravel Sail, which automatizes the process of creating
Docker containers for the project's services.

### Setup with Docker

After cloning, you must run the following command to install all dependencies:
```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php81-composer:latest \
    composer install --ignore-platform-reqs
```

## Starting it!

To start the project using Sail, run the following command:
```
./vendor/bin/sail up
```

Add the `-d` flag if you want to run it detached from the current terminal session. `./vendor/bin/sail stop` puts it down.
More information about Sail can be found [here](https://laravel.com/docs/9.x/sail).

## Mocking a PHP executable

If your project is setup with Sail, its PHP executable is found within the container. You can access with the following command:
```
docker exec {{PROJECT FOLDER NAME}}_laravel.test_1 php *args*
```
To make it less verbose, there's a `sail` executable in the project. Run `./sail -v` to test its output. `./sail` is a drop-in for
`php` and works exactly like it. You can use any `artisan` command with `./sail artisan *args*`.
```

The source for this method can be found [here](https://stackoverflow.com/a/66376387).
