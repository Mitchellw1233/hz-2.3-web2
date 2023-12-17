# Slimfony & Anubis
Slimfony is a backend framework including its own ORM, dependency injection, simple templating engine and more (all self-made). Inspired by Symfony and Doctrine ORM.

Anubis is a website using the Slimfony framework as a PoC for Slimfony (fully operational). It's a school system for assigning and grading students to specific subjects, accessable for both students, teachers and admins.

The assignment from school in a nutshell: *Make a fully self-made framework in PHP with ORM and templating engine etc. Use this framework to make a school system website for grading students.*

### Authors
- Mitchell Wolters
- Koen Beerta

### Starting
```
docker compose up -d
docker compose exec anubis bash
cd ../
php sform generate
php sform seed
```

### Fresh migrations
```
docker compose exec anubis bash
cd ../
php sform fresh
```

# Setup: Using Docker
### Start
```
docker compose up -d
```

### Stop
```
docker compose down
```

### Shell
```
docker compose exec {service_name} bash
```

### Erase data
```
docker compose down --volumes
```

### Rebuild
For example: When changing `composer.json` and it does not automatically rebuild
```
docker compose down
docker compose up --build -d
```

### Rebuild without cache
If for some reason rebuilding without cache is necessary
```
docker compose down
docker compose build --no-cache
docker compose up -d
```
