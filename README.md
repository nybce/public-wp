# Project Overview
The NYBC project is a [WordPress Multisite Network](https://wordpress.org/support/article/create-a-network/) project.

# Local Development

## Requirements
1. Docker >= 17.09.0
1. Python 3
1. BBX-cli
1. Access to GitHub
1. Access to Vaultpass credential in 1password

## Initial Setup

### System Configuration

1. Clone this repository `git clone git@github.com:blenderbox/nybc-wordpress.git`
1. Add a `.vaultpass` file to the repo root. The file should contain only the password found in onepass.
1. Edit your `/etc/hosts` file.
   Add the following lines:
   ```
    127.0.0.1  cbc.local.org
    127.0.0.1  ctblood.local.org
    127.0.0.1  delmarvablood.local.org
    127.0.0.1  innovativebloodresources.local.org
    127.0.0.1  mbc.local.org
    127.0.0.1  ncbb.local.org
    127.0.0.1  ncbgg.local.org
    127.0.0.1  ncbp2.local.org
    127.0.0.1  nybc-enterprise.local.org
    127.0.0.1  ribc.local.org
   ```
1. Configure `git flow`. See [Version Control Guidelines](https://blenderbox.atlassian.net/wiki/spaces/N2RDEV/pages/2169372744/Version+Control+Guidelines)
   ```
   git flow init

   Initialized empty Git repository in ~/project/.git/
   No branches exist yet. Base branches must be created now.
   Branch name for production releases: [master]
   Branch name for "next release" development: [develop]
   
   How to name your supporting branch prefixes?
   Feature branches? [feature/] feature/N2RDEV-
   Release branches? [release/]
   Hotfix branches? [hotfix/]   hotfix/N2RDEV-
   Support branches? [support/] support/N2RDEV-
   Version tag prefix? []
   ```
   Now you will be able to create feature branches using `git flow` commands like this `git flow feature start <JIRA TICKET ID>-authentication`.
1. Configure Git Hooks. In the project root run:
    1. `cp .githooks/pre-commit .git/hooks/pre-commit`
    1. `chmod +x .git/hooks/pre-commit`

### BBX
- Install the bbox tool if not already installed on your system. See https://bitbucket.org/blenderbox/bbox-cli
- Setup the project with bbx: run `bbx init`
  - You will be able to find the vaultpass in 1password.

### Docker
- Build your docker containers -- run `docker-compose build`
- Spin up docker containers -- run `docker-compose up -d`
- Load a database. If no remote environments are available you can run `bash scripts/loadSeedDb.sh seed.sql` for a baseline site.

## Version Control

- `develop` is the source branch.
- Create branches using `git flow feature start <JIRA TICKET ID>-branch-name` command.

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

TBD.

### Folder Structure

1. Full page templates should be placed in the `/templates` directory.
1. Template parts should be placed in the relevant `/template-parts` directory.
1. JS/SCSS Files should follow the same folder structure as template parts in the `src` directory. For example `template-parts/nav/main-nav.php` should have corresponding files `src/js/main-nav.js` and `src/js/main-nav.scss`

### Functions.php
A bare minimum of code should be contained in the functions.php file. Instead it should be placed in a relevant file in the `inc` directory and included via `require_once`.

### Gutenberg Blocks
Any Gutenberg block (including ACF blocks) should have all necessary assets (php, js, scss) files placed in an individual block directory.

For example `/template-parts/blocks/BLOCK-NAME` should contain `block-name.php`, `block-name.js`, and `block-name.scss`.

### Custom Post Types
A plugin should be created to contain custom post types.

Custom Post Types should be initialized as a class. Methods should be added to the class where possible.

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
  1. Go to http://nybc-enterprise.local.org/wp/wp-admin/network/site-new.php
  2. Add a new site with the correct title/admin email -- the site address doesn't matter
  3. Save the site (hit the `Add Site` button)
  4. Edit the site changing the Site Address (url) to the desired url.
- Add the site as `WP_DIVISION_SITE_N` in the relevant environment files.
- Add the site in your etc/hosts file.

# Server Environments

## Deployment
Deployment occurs upon merge & successful build to an environment branch.

1. PRs merged to `develop` branch will be deployed to the `develop` environment. 
1. Upon internal approval `develop` will be merged to `staging`. 
1. Upon client approval `develop` will be merged to `master` branch for a production deploy.

- `master` branch deploys to `production` environment
- `staging` branch deploys to `staging` environment
- `develop` branch should be deployed to `develop` environment

## Dockerhub
GitHub Actions will build the WordPress container and push the image to Docker Hub with an environment tag.

## Watchtower
Watchtower will be running on all servers. It will look for updates to Docker images with a specified environment tag and pull them down/deploy whenever a new release is pushed.

# MISC
## wp cli
### Search and replace
--network flag needed to run for all sites on the network. E.g. `wp search-replace ctblood.nybc-enterprise.local.org ctblood.local.org --network --allow-root`.
