# Setup: Using Docker
### Start
```
docker compose up -d
```

### Stop
```
docker compose down
```

### Erase data
```
docker compose down --volumes
```

### Rebuild
For example: When changing `composer.json` and it does not automatically rebuild
```
docker compose up --build -d
```

### Rebuild without cache
If for some reason rebuilding without cache is necessary
```
docker compose build --no-cache
docker compose up -d
```
