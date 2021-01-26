#!/bin/bash

DATE=$(date +"%m-%d-%Y")

echo "Backup database"
if [ ! -f "${BACKUP_DIR}/dump-${DATE}.sql.gz" ]; then
  echo "Create a new database dump"
  cd ${DOCROOT_DIR}
  drush sql-dump --structure-tables-key='common' --gzip --result-file=/var/www/hbm/shared/backups/dump-${DATE}.sql
  ls -t ${BACKUP_DIR}/*.sql.gz | tail -n +11 | xargs rm
fi
