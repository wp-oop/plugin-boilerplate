# WP-OOP - Plugin Boilerplate
[![Continuous Integration](https://github.com/wp-oop/plugin-boilerplate/actions/workflows/continuous-integration.yml/badge.svg)](https://github.com/wp-oop/plugin-boilerplate/actions/workflows/continuous-integration.yml)
[![Latest Stable Version](https://poser.pugx.org/wp-oop/plugin-boilerplate/v)](//packagist.org/packages/wp-oop/plugin-boilerplate)
[![Latest Unstable Version](https://poser.pugx.org/wp-oop/plugin-boilerplate/v/unstable)](//packagist.org/packages/wp-oop/plugin-boilerplate)

A boilerplate for starting WordPress plugins quickly.

## Details
Use this project as a starter for your [modular][modularity] WordPress plugin!

### Features
- **Docker** - Develop and test your plugin with Docker. Use an environment
    tailored for your plugin. See changes instantly in the browser. Build
    a Docker image containing a complete WordPress installation with your
    plugin and all pre-requisites.
    
- **PHPStorm** - Configuration for integrations of arguably the best PHP
    IDE out there, including:
    
    * **Database client** - View and manipulate the database from PHPStorm.
    * **Composer** - Install and manage PHP dependencies on the correct version of PHP without leaving the IDE.
    * **PHPUnit** - Run tests and get reports directly in PHPStorm.
    * **xDebug** - Set breakpoints and inspect your code in PHPStorm.
    * **Code coverage** - See what has not been tested yet in a friendly GUI.

- **Static Code Analysis** - Maintain a consistent coding style, and catch problems early.

    * **[Psalm][]** - Inspects your code for problems.
    * **[PHPCS][]** - Checks your code style. [PHPCBF][] can fix some of them automatically.

- **Continuous Integration** - Automatically verify that all contributions comply with
    project standards with [GitHub Actions][].

- **Modularity** - Keep concerns separated into [modules][modularity], which can be freely
    moved out of the package at any time thanks to the [`composer-merge-plugin`][].

- **Build Script** - Use a single [GNU Make][] entrypoint to build the plugin, including modules,
    in place; or, build a dist version without affecting current working directory.


### Usage

#### Getting Started

1. Use this template

    This Github repository is a [template][template-repo]. [Use it][use-template-repo] to create a project of your own from.
    
    Of course, you can always clone and push it elsewhere manually, or use another method of forking, if more appropriate.

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
        * `WORDPRESS_VERSION`* - The version of WordPress, on which the plugin will be _run_. Used to determine the tag of
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

    - Module `composer.json`:
        This bootstrap uses the awesome [`composer-merge-plugin`][] to keep module depedencies
        together with modules. This allows keeping track of which dependencies belong to which
        modules, detect dependency incompatibilities, and moving modules out of the package
        into packages of their own when necessary.
        
        Modules can be installed from other packages, or included in the package. In the latter
        case, they should be added to the directory `modules`. One such module, the `core`
        module of the plugin, is already included in the package. Its `composer.json` should
        also be personalized, just like the `composer.json` of this package.

3. Build everything
    
    1. Build the environment.

        In order to develop, build, and test the plugin, certain things are required first.
        These include: the database, WordPress core, PHP, Composer, and web server.
        The project ships with all of this pre-configured, and the Docker services must first
        be built:

       ```
       docker-compose build
       ```
       
    2. Build the plugin in place.
   
        In order for the project source files to have the desired effect,
        they first must be built into their runtime version. This may include:
        installing dependencies, transpilation, copying or archiving files, whatever
        the modules require to have desired effect, etc.
        At the same time, a single entrypoint to various tasks performed as part
        of the project build or QA allows for more centralized and automated control
        over the project.

        For this reason, the Makefile is shipped with the project, declaring commands
        for commonly run tasks, including build. Run the following command to build
        the plugin, including modules, in the plugin source directory: this makes it
        possible to preview and test changes instantly after they are made.

        ```
        docker-compose run --rm build make build
        ```
            
4. Spin up the dev environment
    
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
   Machine (in a non-Linux environment), use `docker-machine ip` to find it.

   Now you should be able to visit that domain, and see the website. The admin username
   and password are both `admin` by default, and are determined by the `ADMIN_USER`
   and `ADMIN_PASS` variables from the `.env` file. Your plugin should already be
   installed and active, and no other plugins should be installed. If this is not
   the case, inspect the output you got from `docker-compose up`.

   If you use PHPStorm integrations that involve Docker, such as Composer,
   you maybe receive the error "Docker account not found". This is because, for some reason,
   PHPStorm requires the same name of the Docker deployment configuration to be used in all
   projects, and there currently does not seem to be a way to commit that to the VCS.
   Because of this, you are required to create a Docker deployment yourself. Simply go to
   _Project Settings_ > _Docker_ and create a configuration named precisely "Docker Machine".

5. Release

    When you would like to release the current working directory as an installable plugin archive,
    the shipped build script needs to perform a few transformations (like optimize prod dependencies),
    and archive the package in a specific way. The following command will result in an archive
    with name similar to `plugin-0.1.1-beta21+2023-08-12-12-37-22_105188ec9180.zip` being added
    to `build/release`:

    ```sh
     docker compose run --rm build make release RELEASE_VERSION=0.1.1-beta21
    ```
   
    As you can see, the resulting archive's name will reflect the time and commit hash
    as SemVer metadata, aside from the version itself. If `RELEASE_VERSION` is omitted,
    `dev` is used by default to indicate that this is not a tagged milestone, but work in progress.

    _Note_: If the current working directory contains any edits registerable by Git
    (disregarding any `.gitignore` rules), the commit hash will reflect a point in history
    of the files in `build/dist`, rather than of project history. To ensure that a concrete
    version is being released, clean the directory tree entirely. The best way to do that is
    probably to create a fresh clone.

#### Updating Dependencies
Composer is installed into the `build` service's image. To run composer commands,
use `docker-compose run`. For example, to update dependencies you can run the following:

```bash
docker-compose run --rm build composer update
```

~~If you use PHPStorm, you can use the [composer integration][], as the project
is already configured for this.~~

Currently, it is not possible to use PHPStorm's [composer integration][] because
managing local modules with the [`composer-merge-plugin`][] require running
`composer update --lock` instead of simply `composer update`. This is currently
unsupported by PHPStorm, but a [feature request][WI-54242] has been submitted.

**Do not run `composer update` for the modules' `composer.json` file!**
All Composer operations must be performed on the root package's `composer.json` file.

Any changes to the project folder are immediately reflected in the dev environment,
and this includes the `vendor` folder and `composer.lock` file. This is because
the project's folder is mounted into the correct place in the WordPress container.

#### Adding Modules
This boilerplate promotes modularity, and supports [Dhii modules][] out of the box.
Any such module that exposes a [`ModuleInterface`][] implementation can be loaded,
allowing it to run in the application, and making its services available.

The list of modules returned by `inc/modules.php` is the authoritative source
of modules in the application. Because it is PHP code, modules can be loaded
in any required way, including:

- Simple instantiation of a module class that will be autoloaded.

    If your module class is on one of the autoload paths registered with e.g. Composer,
    you can just instantiate it as you would any other class. This is a
    very quick and simple way to load some modules.


- Usage of a factory class or file.

    In order to make modules de-coupled from the application, but to still be able
    to provide dependencies from the application to the module, it is sometimes
    desirable to use a "padding" between the application and the module's
    initialization. In this project, as well as in some others, we use a
    `module.php` file. This file returns a function which, given some parameters
    like the root project path, will return a [`ModuleInterface`][] instance.
    Another approach could be to use a named constructor, or even a dedicated
    factory class.

- Scanning certain paths.

    If modules do not conflict in any way, the module load order may be irrelevant.
    In this case, it is possible to auto-discover modules by, for example, scanning
    certain folders for some entrypoints or config files. Implement whatever
    auto-discovery mechanism you wish, as long as the module instances
    end up in the authoritative list.

##### External Modules
To add a module from another package, require that package with Composer
and add the `ModuleInterface` instance to the list.

##### Local Modules
To add a local module, add the module to the `modules` folder,
and do the same as for any other module. Local modules may also declare their own
dependencies by adding a `composer.json` file to their root folder.
These files will be picked up by Composer when updating dependencies in
the project root, thanks to the [`composer-merge-plugin`][], provided
that `composer update --lock` is run before `composer update`. This is
a great way to separate module dependencies from other dependencies.
Consult that Composer plugin's documentation for more information.

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

#### Debugging
The bootstrap includes xDebug in the `test` service of the Docker environment,
and PHPStorm configuration. To use it, right click on any test or folder within
the `tests` directory, and choose "Debug". This will run the tests with xDebug
enabled. If you receive the error about [`xdebug.remote_host`][] being set
incorrectly and suggesting to fix the error, fix it by setting that variable
to [your machine's IP address][] on the local network in the window that
pops up. After this, breakpoints in any code reachable by PHPUnit tests,
including the code of tests themselves, will cause execution to pause,
allowing inspection of code.

If you change the PHP version of the `test` service, the debugger will stop working.
This is because different PHP versions use different versions of xDebug, and
because the path to the xDebug extension depends on its version, that path will
also change, invalidating the currently configured path.
To fix this, the "Debugger extension" fields in the interpreter settings screen
needs to be updated. You can run `docker-compose run test ls -lah /usr/local/lib/php/extensions`
to see the list of extensions. One of them should say someting like
`no-debug-non-zts-20170718`. Change the corresponding part of the "Debugger extension"
path value to that string.

At this time, inspection of code that runs _during a web request_ is not available.

#### Database UI
This bootstrap includes [phpMyAdmin][], which provides a GUI for your database.
To start working with it, you must first bring up the related container,
as it is not brought up together with the dev environment:

```bash
docker-compose up db_admin
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

#### Static Analysis
- **Psalm**

    Run Psalm in project root:

    ```bash
    docker-compose run --rm test vendor/bin/psalm
    ```

    - Will also be run automatically on CI.
    - PHPStorm [integration][phpstorm-psalm] included.

- **PHPCS**

    Run PHPCS/PHPCBF in project root:

    ```bash
    docker-compose run --rm test vendor/bin/phpcs -s --report-source --runtime-set ignore_warnings_on_exit 1
    docker-compose run --rm test vendor/bin/phpcbf
    ```

    - By default, uses [PSR-12][] and some rules from the [Slevomat Coding Standard][].
    - Will also be run automatically on CI.
    - PHPStorm [integration][phpstorm-phpcs] included.


[modularity]: https://dev.to/xedinunknown/cross-platform-modularity-in-php-30bo
[Docker Machine]: https://github.com/docker/machine
[WP-CLI]: https://wp-cli.org/
[phpMyAdmin]: https://www.phpmyadmin.net/
[PSR-12]: https://www.php-fig.org/psr/psr-12/
[Slevomat Coding Standard]: https://github.com/slevomat/coding-standard
[Psalm]: https://psalm.dev/
[PHPCS]: https://github.com/squizlabs/PHP_CodeSniffer
[PHPCBF]: https://github.com/squizlabs/PHP_CodeSniffer/wiki/Fixing-Errors-Automatically
[GNU Make]: https://www.gnu.org/software/make/manual/make.html
[GitHub Actions]: https://github.com/features/actions
[Dhii modules]: https://github.com/Dhii/module-interface
[hosts file]: https://www.howtogeek.com/howto/27350/beginner-geek-how-to-edit-your-hosts-file/
[your machine's IP address]: https://www.whatismybrowser.com/detect/what-is-my-local-ip-address
[composer integration]: https://www.jetbrains.com/help/phpstorm/using-the-composer-dependency-manager.html#updating-dependencies
[database integration]: https://www.jetbrains.com/help/phpstorm/configuring-database-connections.html
[`container_name`]: https://docs.docker.com/compose/compose-file/#container_name
[`composer-merge-plugin`]: https://github.com/wikimedia/composer-merge-plugin
[`php`]: https://hub.docker.com/_/php
[`wordpress`]: https://hub.docker.com/_/wordpress
[`docker-machine start`]: https://docs.docker.com/machine/reference/start/]
[`docker-machine env`]: https://docs.docker.com/machine/reference/env/
[`xdebug.remote_host`]: https://xdebug.org/docs/all_settings#remote_host
[`ModuleInterface`]: https://github.com/Dhii/module-interface/blob/develop/src/ModuleInterface.php
[WI-54242]: https://youtrack.jetbrains.com/issue/WI-54242
[phpstorm-psalm]: https://www.jetbrains.com/help/phpstorm/using-psalm.html
[phpstorm-phpcs]: https://www.jetbrains.com/help/phpstorm/using-php-code-sniffer.html
[template-repo]: https://docs.github.com/en/repositories/creating-and-managing-repositories/creating-a-template-repository
[use-template-repo]: https://docs.github.com/en/repositories/creating-and-managing-repositories/creating-a-repository-from-a-template
