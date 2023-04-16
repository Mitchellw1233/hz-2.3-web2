# Slimfony
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
