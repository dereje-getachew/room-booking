#!/bin/bash
echo "Testing database connection..."
docker-compose exec db mysql -u root -ppassword -e "SHOW DATABASES;"