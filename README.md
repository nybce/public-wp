# Project Overview
The NYBC project is a wordpress multisite project.


# Local Development

## Initial Setup

### System Configuration
1. Add a `.vaultpass` file to the repo root. The file should contain only the password found in onepass.


2. Edit your `/etc/hosts` file.
   Add the following lines:
```
127.0.0.1 nybc-enterprise.local.org
127.0.0.1	nybc-division-one.local.org
127.0.0.1	nybc-division-two.local.org
```

### BBX
- Install the bbox tool if not already installed on your system. See https://bitbucket.org/blenderbox/bbox-cli
- Setup the project with bbx: run `bbx init`

### Docker
- Build your docker containers -- run `docker-compose build`
- Load a database.


## Version Control
- Develop is the source branch.
- Create branches from jira tickets.


## Workflow
Run docker-compose up to spin up all docker containers
This will spin up:
- a database container
- a wordpress container -- version controlled via composer.
- a theme container -- featuring hot reloading.
- a phpmyadmin container

The theme container runs npm and features hot reloading.
The phpmyadmin interface is accessible at localhost:8081.


## Plugins
Wherever possible plugins should be managed via composer.
Many plugins can be found in the https://wpackagist.org/ repository.
Some paid ones have other methods of incorporating themselves into composer.
If a plugin is unable to be managed via composer it will need to be added to the `/site/.gitignore` file.
Following the line `web/app/plugins/*` add `!web/app/plugins/PLUGIN_DIRECTORY` to the `/site/.gitignore` file.


## Theme Development

### Folder Structure
Full page templates should be placed in the `/templates` directory
Template parts should be placed in the relevant `/template-parts` directory
JS/SCSS Files should follow the same folder structure as template parts in the `src` directory.
For example `template-parts/nav/main-nav.php` should have corresponding files `src/js/main-nav.js` and `src/js/main-nav.scss`

### Functions.php
A bare minimum of code should be contained in the functions.php file. Instead it should be placed in a relevant file in the `inc` directory and included via `require_once`.


### Gutenberg Blocks
Any Gutenberg block (including acf blocks) should have all neccesary assets (php, js, scss) files placed in an individual block directory. For example `/template-parts/blocks/BLOCK-NAME` should contain `block-name.php`, `block-name.js`, and `block-name.scss`.

### Custom Post Types
A plugin should be created to contain custom post types.
Custom Post Types should be initialized as a class. Where possible methods should be added to the class.

### ACF
ACF Fields must be controlled in code and loaded via php (rather than json) files.

### SCSS
#### Misc
The following should always be controlled via variables:
- Colors
- Fonts
- Transitions
- Breakpoints

Heading classes (`h1`, `h2`, etc.) Should only be used when it is semantically and structually correct. Use the equivalent class (`.h1`, `.h2`, etc for presentation purposes).

Wherever possible centralize definitions using variables or mixins.


# Multisite
## Adding a new site
- Add a site in the wp-admin.
- Edit the site to give it the appropriate domain.
- Add the site as `WP_DIVISION_SITE_N` in the relevant environment files.
- Add the site in your etc/hosts file.

# Server Environments

## Deployment
Deployment occurs upon merge & successful build to an environment branch.
Branch off of develop
Make a pr to develop which will be deployed to the develop environment. Upon internal
approval develop will be merged to staging. Upon client approval develop will be merged to master for a production deploy.

- `master` branch deploys to production environment
- `staging` deploys to staging environment
- `develop` branch should be deployed to develop environment

## Dockerhub
Github actions will build the wordpress container and push to dockerhub with an environment tag.

## Watchtower
Watchtower will be running on all servers. It will look for updates to docker containers with a specified environment tag and pull them down whenever a new release is pushed.
