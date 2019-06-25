## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

#### Init

Copy the `.env` file and replace database config :
```bash
cp .env.dist .env
```

Start docker :
```bash
docker-compose up -d
```

Install all dependencies through composer :
```bash
docker-compose exec php bash
cd sf4
composer install
```
