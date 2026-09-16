#!/bin/bash
# Importiert alle web2105*.sql-Dumps aus dem Projekt-Root (sortiert: erst Schema, dann Content).
#
# Zwei Anpassungen beim Durchleiten:
#   1. CREATE VIEW -> CREATE OR REPLACE VIEW (Schema- und Content-Dump enthalten beide die Views)
#   2. DEFINER=`web2105`@`localhost` entfernen (den User gibt es lokal nicht)
set -e

shopt -s nullglob
dumps=(/sqldump/web2105*.sql)

if [ ${#dumps[@]} -eq 0 ]; then
    echo "[init] Keine web2105*.sql im Projekt-Root gefunden - ueberspringe Import."
    exit 0
fi

IFS=$'\n' dumps=($(sort <<<"${dumps[*]}")); unset IFS

for dump in "${dumps[@]}"; do
    echo "[init] Importiere $dump ..."
    sed -e 's/^CREATE ALGORITHM=/CREATE OR REPLACE ALGORITHM=/' \
        -e 's/^CREATE VIEW /CREATE OR REPLACE VIEW /' \
        -e 's/DEFINER=`[^`]*`@`[^`]*` //g' \
        "$dump" \
    | mariadb -u root -p"${MARIADB_ROOT_PASSWORD}" "${MARIADB_DATABASE}"
done

echo "[init] Import fertig."
