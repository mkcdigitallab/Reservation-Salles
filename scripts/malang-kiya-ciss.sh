#!/usr/bin/env bash

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

CONTAINER="${DB_CONTAINER:-reservation-salles-db}"

if [ -f .env ]; then
    set -a
    # shellcheck disable=SC1091
    source .env
    set +a
fi

DB_NAME="${DB_DATABASE:-reservation_salles}"
DB_USER="${DB_USERNAME:-reservation_user}"
DB_PASSWORD="${DB_PASSWORD:-}"

usage() {
    cat <<'EOF'
Usage:
  ./scripts/malang-kiya-ciss.sh "SELECT ..."
  ./scripts/malang-kiya-ciss.sh test
  ./scripts/malang-kiya-ciss.sh security
  ./scripts/malang-kiya-ciss.sh tables
  ./scripts/malang-kiya-ciss.sh terrains
  ./scripts/malang-kiya-ciss.sh reservations
  ./scripts/malang-kiya-ciss.sh logs

Variables facultatives:
  DB_CONTAINER   Nom du conteneur MySQL
  DB_DATABASE    Nom de la base
  DB_USERNAME    Utilisateur MySQL
  DB_PASSWORD    Mot de passe MySQL
EOF
}

if [ "$#" -lt 1 ]; then
    echo "❌ Aucun argument fourni."
    usage
    exit 1
fi

if ! docker ps --format '{{.Names}}' | grep -Fxq "$CONTAINER"; then
    echo "❌ Le conteneur ${CONTAINER} n'est pas démarré."
    echo "Lance d'abord : docker compose up -d"
    exit 1
fi

mysql_exec() {
    docker exec -i "$CONTAINER" mysql \
        --default-character-set=utf8mb4 \
        -u"$DB_USER" \
        -p"$DB_PASSWORD" \
        "$DB_NAME" \
        -e "$1"
}

case "$1" in
    test)
        ./vendor/bin/phpunit
        ;;

    security)
        echo "🔐 Vérification des fichiers sensibles..."
        if git ls-files | grep -E '(^|/)(\.env|.*\.log)$' >/dev/null; then
            echo "❌ Un fichier sensible est suivi par Git."
            git ls-files | grep -E '(^|/)(\.env|.*\.log)$'
            exit 1
        fi
        echo "✅ Aucun .env ou fichier .log suivi par Git."

        echo "🔐 Vérification de la configuration PHP..."
        php -l public/index.php
        php -l src/Security/CsrfToken.php
        echo "✅ Syntaxe PHP valide."
        ;;

    tables)
        mysql_exec "SHOW TABLES;"
        ;;

    terrains)
        mysql_exec "SELECT id, nom, batiment, capacite, type, active FROM salles ORDER BY id;"
        ;;

    reservations)
        mysql_exec "SELECT id, salle_id, responsable, email, date_debut, date_fin, statut FROM reservations ORDER BY date_debut;"
        ;;

    logs)
        docker logs --tail 100 "$CONTAINER"
        ;;

    help|-h|--help)
        usage
        ;;

    *)
        mysql_exec "$1"
        ;;
esac
