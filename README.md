# HY Brain&Mind Drupal 9 project

Description of your project.

## Environments

Env | Branch | Drush alias | URL
--- | ------ | ----------- | ---
development | development | - | http://dev.bnm.druidfi.wod.by/
production | master | - | TBD

## Requirements

You need to have these applications installed to operate on all environments:

- [Docker](https://github.com/druidfi/guidelines/blob/master/docs/docker.md)
- [Stonehenge](https://github.com/druidfi/stonehenge)
- For the new person: Your SSH public key needs to be added to servers

## Create and start the environment

For the first time (new project):

```
$ make new
```

And following times to create and start the environment:

```
$ make fresh
```

NOTE: Change these according of the state of your project.

## Login to Drupal container

This will log you inside the app container:

```
$ make shell
```

## VueJS environment

We have a VueJS application that is attached to the Drupal site. It can be accessed from `/search-app`. It is a simple search interface for Drupal's Search API with suggestions.

### Requirements

You need to have these applications installed to develop this application:

- [NodeJS](https://nodejs.org/en/)
- Node version that is newer than 14.0.0.

### Development

#### Installing

Navigate to the VueJS applications directory:
```
$ cd public/modules/custom/bnm_frontend/js/search/
```

Install node dependencies:
```
$ npm i
```

Start development:
```
$ npm run serve
```

#### Ready with your modifications and want to create a releasable PR version of your code?

Compile your application in production mode:
```
$ npm run build
```

#### Compiled webpack bundles

You'll find your compiled application in the `/dist` directory in two different bundles:

- `/js/app.js` is the compiled VueJS application
- `/css/app.css` is the compiled VueJS applications styles

#### Commands

|`npm run <script>`|Description|
|------------------|-----------|
|`serve`|Starts to watch files and recompiles the application whenever they change.|
|`serve:production`|Same as `serve`, but will run in production mode, meaning it will fetch production data for the search results.|
|`build`|Compiles the application in production mode into the `/dist` directory.|
|`build:development`|Same as `build`, but will run in development mode, meaning it will fetch localhost data for the search results.|

#### Coding standards

We follow Airbnb Javascript coding standards. More info: https://github.com/airbnb/javascript