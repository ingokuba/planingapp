docker-compose -p planningapp -f docker-compose.yml up --build -d
docker exec planningapp php 'test/php/InsertTestdata.php'