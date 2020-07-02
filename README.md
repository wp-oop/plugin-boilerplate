# WP-OOP - Plugin
A skeleton for starting WordPress plugins quickly.

## Details
Use this project as a starter for your modular WordPress plugin!

### Feaures
- **Docker** - Develop and test your plugin with Docker. Use an environment
    tailored for your plugin. See changes instantly in the browser. Build
    a Docker image containing a complete WordPress installation with your
    plugin and all pre-requisites.
    
### Usage

#### Getting Started
Use Composer to bootstrap your project.

1. Clone and install deps:

    ```bash
    composer create-project wp-oop/plugin my_plugin
    ```
   
   Here, `my_plugin` is the name of the project folder. Should correspond
   to the slug of your plugin.
   
2. Customize project

    _Note_: Asterisk `*` below denotes that changing this value requires rebuild of the images in order
    to have effect on the dev environment.

    - Copy `.env.example` to `.env`.
    - `.env`:
        * `PLUGIN_NAME` - The slug of your plugin. Must correspond to the name of the plugin folder.
        * `BASE_PATH` - If you are using [Docker Machine][], i.e. on any non-Linux system, set this
            to the absolute path to the project folder _inside the machine_. If you are on Linux,
            you do not need to change this.
        * `PROJECT_MOUNT_PATH` - The path to mount the project folder into. This should be the absolute
            path to the folder of your plugin inside the container.
        * `PROJECT_NAME` - Slug of your project. Used mainly for naming containers with [`container_name`][].
            This is helpful to run multiple projects on the same machine.
        * `PHP_BUILD_VERSION` - The version of PHP, on which the plugin will be _built_. This should
            correspond to the minimal PHP requirement of your plugin. Used to determine the tag of
            the [`php`][] image.
        * `PHP_TEST_VERSION`* - The version of PHP, on which the plugin will be _run_. This should
            correspond to the maximal PHP requirement of your plugin. Used to determine the tag of
            the [`wordpress`][] image.
        * `DB_USER_PASSWORD`* - This and other `DB_*` variables are used to determine the password
            to the WordPress database. Change these if you want to secure your deployed application.
        * `WP_DOMAIN`* - The domain name of the WordPress application, which contains your plugin.
            Among other things, used to set up the local dev image. Corresponds to the alias
            used in the `hosts` file, if local. This value is also used in the PHPStorm's DB integration.
            If this value is changed, PHPStorm's configuration must be updated.
        * `WP_TITLE`* - The title of the WordPress application, which contains your plugin.
            No quotes, because Docker does not expand variables in this file. It is used during automatic
            installation of WordPress inside the local dev image. This value is also used in the
            PHPStorm's DB integration. If this value is changed, PHPStorm's configuration must be updated.
        * `ADMIN_USER`* - This and other `ADMIN_*` variables are used to determine WordPress admin
            details during automatic WordPress installation with [WP-CLI][].
            
    - `composer.json`:
        * `name` - Name of your package.
        * `description` - Description of your package.
        * `authors` - You and/or your company details.
        * `require` - Your project's package and platform requirements. You may want to change the PHP
            version if your minimal requirement is different. Don't forget to update `PHP_BUILD_VERSION`
            in `.env`.
        * `require-dev` - Your project's development requirements. Apart from tools for testing,
            this should (for now) contain any WordPress plugins that your plugin depends on,
            and WordPress. If you add plugins here, you need to mount them as volumes in
            `docker-compose.yml`, the `wp_dev` service. The source is the absolute path to the
            `vendor` dir. The destination is an absolute path to the plugin folder in the `plugins`
            directory of WordPress; you can use absolute paths, or the `DOCROOT_PATH` or `PROJECT_MOUNT_PATH`
            from the `.env` file to help make the paths more versatile. If you want these
            plugins to be active when the container is brought up, you need to also add these
            instructions to the `docker/wp-entrypoint.sh` script. You may use WP CLI for this.
            These instructions should go right below the line that says `# Custom setup instructions`.
            This way, they will only run when WordPress is ready for action. **All changes to
            the entrypoint script require image rebuild**: use `docker-compose down` and
            `docker-compose build`. This is because these changes affect the application image,
            which the entrypoint script is baked into.
            
            In the future, this may need to go into `require`, if a plugin build script takes care
            of removing unnecessary plugins from `vendor` folder. This may be a good idea because
            the plugin _does_ actually require other plugins, but they should not be shipped with
            the plugin. Otherwise, completely Composer-managed WordPress installations will not
            automatically install other required plugins.
            
3. Spin up the dev environment
    
    Run the following command in the terminal. If you use Docker Machine, you will need to
    start it and configure your environment first with [`docker-machine start`][] and
    [`docker-machine env`].
    
    ```bash
    docker-compose up wp_dev 
    ```
   
   This will bring up only the dev environment and its dependencies, which right now is
   the database. The database is a separate service, because in a deployed environment
   you may choose to use a different DB server.
   
   After this, add an entry to your local [hosts file][]. The host should correspond to
   the value of `WP_DOMAIN` from the `.env` file. The IP would be Docker machine's IP
   address. On Linux, this is the same as [your machine's IP address][] on the local
   network, and usually `127.0.0.1` (localhost) works. If you are using Docker
   Machine (in a non-Linux environment), use [`docker-machine ip`] to find it.
   
   Now you should be able to visit that domain, and see the website. The admin username
   and password are both `admin` by default, and are determined by the `ADMIN_USER`
   and `ADMIN_PASS` variables from the `.env` file. Your plugin should already be
   installed and active, and no other plugins should be installed. If this is not
   the case, inspect the output you got from `docker-compose up`.

#### Updating dependencies
Composer is installed into the `build` service's image. To run composer commands,
use `docker-compose run`. For example, to update dependencies you can run the following:

```bash
docker-compose run --rm build composer update 
```

If you use PHPStorm, you can use the [composer integration][], as the project
is already configured for this.

Any changes to the project folder are immediately reflected in the dev environment,
and this includes the `vendor` folder and `composer.lock` file. This is because
the project's folder is mounted into the correct place in the WordPress container.

#### Testing Code
This bootstrap includes PHPUnit. It is already configured, and you can test
that it's working by running the sample tests:

```bash
docker-compose run --rm build vendor/bin/phpunit
```

If you use PHPStorm, you can use its PHPUnit integration: right-click on any
test or folder inside the `tests` directory, and choose "Run". This will do
the same as the above command. Because the `build` service is used for tests,
they will be run with its PHP version, which should correspond to your project's
minimal requirements.

#### Database UI
This bootstrap includes [phpMyAdmin][], which provides a GUI for your database.
To start working with it, you must first bring up the related container,
as it is not brought up together with the dev environment:

```bash
docker-compose up
```

You can now head over to the application's domain, defined usually by the
`WP_DOMAIN` value from the `.env` file, but access it on port `1234`, e.g.
`http://plugin.myhost:1234`. The username is `root`, and password is the one
specified by the `DB_ROOT_PASSWORD` variable in the `.env` file.

This bootstrap comes ready with configuration for PHPStorm's [database integration][].
With it, it's possible to completely avoid bringing up the `db_admin` service.
To use it, its settings must be up to date from the value of `DB_USER_PASSWORD`.
Using it is highly recommended, as it is an integrated DB client, and will
provide assistance during coding.

        
[Docker Machine]: https://github.com/docker/machine
[WP-CLI]: https://wp-cli.org/
[phpMyAdmin]: https://www.phpmyadmin.net/
[hosts file]: https://www.howtogeek.com/howto/27350/beginner-geek-how-to-edit-your-hosts-file/
[your machine's IP address]: https://www.whatismybrowser.com/detect/what-is-my-local-ip-address
[composer integration]: https://www.jetbrains.com/help/phpstorm/using-the-composer-dependency-manager.html#updating-dependencies
[database integration]: https://www.jetbrains.com/help/phpstorm/configuring-database-connections.html
[`container_name`]: https://docs.docker.com/compose/compose-file/#container_name
[`php`]: https://hub.docker.com/_/php
[`wordpress`]: https://hub.docker.com/_/wordpress
[`docker-machine start`]: https://docs.docker.com/machine/reference/start/]
[`docker-machine env`]: https://docs.docker.com/machine/reference/env/
