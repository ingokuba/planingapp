#!/bin/bash
docker-compose -p planingapp -f docker-compose.yml up --build -d
echo 'done'