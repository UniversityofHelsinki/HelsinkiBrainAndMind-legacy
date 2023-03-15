#!/bin/bash

ROOT_DIR=/var/www/hbm
DOCROOT_DIR=/var/www/hbm/public
SHARED_DIR=/var/www/hbm/shared
BACKUP_DIR=/var/www/hbm/shared/backups
GIT_DIR=/var/www/hbm
FILES_DIR=/var/www/hbm/shared/files
DATE=$(date +"%m-%d-%Y")

run_deployment() {
  if [ -z "$1" ]
  then
    echo "Environment variable is missing" >&2
    exit 1
  else
     GIT_BRANCH=$1
  fi
  echo "### Deploy starting... ###"
  backup_database
  run_git
  run_composer
  create_symlinks_settings
  run_drush
  echo "### Deploy done. ###"
}

run_backup() {
  if [ -z "$1" ]
  then
    echo "Environment variable is missing" >&2
    exit 1
  else
     GIT_BRANCH=$1
  fi
  echo "### Backup starting... ###"
  backup_database
  echo "### Backup done. ###"
}

backup_database() {
  echo "Backup database"
  if [ ! -f "${BACKUP_DIR}/dump-${DATE}.sql.gz" ]; then
    echo "Create a new database dump"
    cd ${DOCROOT_DIR}
    drush sql-dump --structure-tables-key='common' --gzip --result-file=${BACKUP_DIR}/dump-${DATE}.sql
    ls -t ${BACKUP_DIR}/*.sql.gz | tail -n +11 | xargs rm
  fi
}

run_git() {
  echo "Git commands"
  cd ${GIT_DIR}
  echo "Git checkout environment branch"
  git checkout ${GIT_BRANCH} || { echo "Error with git checkout dev command."; exit 1; }
  echo "Git pull update latest changes"
  git pull || { echo "Error with git pull command."; exit 1; }
}

run_composer() {
  echo "Composer install"
  cd ${GIT_DIR}
  composer install --no-dev
}

run_drush() {
  echo "Drush commands ###"
  cd ${DOCROOT_DIR}
  # clear/rebuild the cache
  drush cr
  # Run any database updates
  drush updb -y
  # Import new config
  drush cim -y
  # Clear/rebuild the cache again
  drush cr
}

create_symlinks_settings() {
  echo "Creating symlink for settings.local.php & files."
  cd ${DOCROOT_DIR}/sites/default
  rm -f settings.local.php
  ln -s ${SHARED_DIR}/settings.local.php local.settings.php
  rm -f files
  ln -s ${SHARED_DIR}/files files
}
