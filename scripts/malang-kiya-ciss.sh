#!/bin/bash

CONTAINER="reservation-salles-db"

if ! docker ps --format '{{.Names}}' | grep -q "^${CONTAINER}$"; then
    echo "❌ Le conteneur ${CONTAINER} n'est pas démarré."
    echo "Lance d'abord :"
    echo "docker compose -f docker-compose.prod.yml up -d"
    exit 1
fi

if [ -z "$1" ]; then
    echo "❌ Aucune requête SQL fournie."
    echo "Exemple :"
    echo "./scripts/malang-kiya-ciss.sh \"SELECT * FROM salles;\""
    exit 1
fi

docker exec -i "$CONTAINER" mysql \
    -ureservation_user \
    -preservation_password \
    reservation_salles \
    -e "$1"
