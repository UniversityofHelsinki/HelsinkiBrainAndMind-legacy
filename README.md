# HY Brain & Mind

HY Brain & Mind Drupal project.

## Requirements

- Docker
- DDEV - [Get started](https://ddev.com/get-started/)
- Volta - [Get started](https://docs.volta.sh/guide/getting-started)
- Get database dump from [Anni Kinnari](https://www.helsinki.fi/en/about-us/people/people-finder?search=anni%20kinnari)

## Create and start the environment

For the first time:

1. Get database dump and place it to repo root as `dump.sql`.
2. Then run following commands:

```console
ddev start
ddev import-db --file=dump.sql
ddev composer install
ddev drush sapi-rt research_group
ddev drush sapi-i research_group
ddev describe
```

Ready! Now go to http://hy-brain-and-mind.ddev.site/ to see your site.

## Login to Drupal container

This will log you inside the app container:

```console
ddev ssh
```

## VueJS environment

We have a VueJS application that is attached to the Drupal site. It can be accessed from `/search-app`. It is a simple
search interface for Drupal's Search API with suggestions.

### Development

#### Installing

Install node dependencies:

```console
(cd public/modules/custom/bnm_frontend/js/search && pnpm install --frozen-lockfile)
```

Start development:

```console
(cd public/modules/custom/bnm_frontend/js/search && pnpm run serve)
```

#### Ready with your modifications and want to create a releasable PR version of your code?

Compile your application in production mode:

```console
(cd public/modules/custom/bnm_frontend/js/search && pnpm run build)
```

#### Compiled webpack bundles

You'll find your compiled application in the `/dist` directory in two different bundles:

- `/js/app.js` is the compiled VueJS application
- `/css/app.css` is the compiled VueJS applications styles

#### Commands

| `npm run <script>`  | Description                                                                                                     |
|---------------------|-----------------------------------------------------------------------------------------------------------------|
| `serve`             | Starts to watch files and recompiles the application whenever they change.                                      |
| `serve:production`  | Same as `serve`, but will run in production mode, meaning it will fetch production data for the search results. |
| `build`             | Compiles the application in production mode into the `/dist` directory.                                         |
| `build:development` | Same as `build`, but will run in development mode, meaning it will fetch localhost data for the search results. |

#### Coding standards

We follow Airbnb JavaScript coding standards. More info: https://github.com/airbnb/javascript
