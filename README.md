# HY Brain & Mind

HY Brain & Mind Drupal project.

## Environments

| Env                | Branch | Drush alias | URL                                       |
|--------------------|--------|-------------|-------------------------------------------|
| local              | *      | -           | https://bnm.docker.so                     |
| development        | dev    | -           | http://hbm-dev-20.it.helsinki.fi/         |
| production (alias) | main   | -           | https://hbm-prod-20.it.helsinki.fi/       |
| production         | main   | -           | https://research.helsinkibrainandmind.fi/ |

## Requirements

You need to have these applications installed to operate on all environments:

- [Docker](https://github.com/druidfi/guidelines/blob/master/docs/docker.md)
- [Stonehenge](https://github.com/druidfi/stonehenge)
- Github CLI
- Optional: For the new person: Your SSH public key needs to be added to servers

## Create and start the environment

For the first time:

```shell
make fresh
```

Ready! Now go to https://bnm.docker.so/ to see your site.

## Login to Drupal container

This will log you inside the app container:

```shell
make shell
```

## VueJS environment

We have a VueJS application that is attached to the Drupal site. It can be accessed from `/search-app`. It is a simple search interface for Drupal's Search API with suggestions.

### Requirements

You need to have these applications installed to develop this application:

- Node 18

### Development

#### Installing

Install node dependencies:

```shell
make js-install
```

Start development:

```shell
make build-js-search-dev
```

#### Ready with your modifications and want to create a releasable PR version of your code?

Compile your application in production mode:

```shell
make build-js-search-prod
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

We follow Airbnb Javascript coding standards. More info: https://github.com/airbnb/javascript
