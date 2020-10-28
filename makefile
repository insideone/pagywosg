include .env
export

# make import-database-dump from=$(date -I).sql
.PHONY: import-database-dump
import-database-dump:
	docker exec -i $(shell docker-compose ps -q database) mysql -uroot -p$(DATABASE_ROOT_PASSWORD) $(DATABASE_NAME) < $(from)

# make dump-database to=$(date -I).sql
.PHONY: dump-database
dump-database:
	docker-compose exec database mysqldump -q -uroot -p$(DATABASE_ROOT_PASSWORD) $(DATABASE_NAME) 2>/dev/null > $(to)
	sed -i '1d' $(to)

.PHONY: stop
stop:
	docker-compose stop
