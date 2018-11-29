#!/bin/bash
docker-compose -p planningapp -f docker-compose.yml up --build -d
echo 'done'