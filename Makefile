start:
	docker-compose up -d

build:
	docker-compose up -d --build

stop:
	docker stop $$(docker ps -a -q)

down:
	docker stop $$(docker ps -a -q)
	docker rm $$(docker ps -a -q)

restart:
	docker stop $$(docker ps -a -q)
	docker rm $$(docker ps -a -q)
	docker-compose up -d