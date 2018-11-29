#!/bin/bash
docker-compose -p planningapp -f docker-compose.yml down -v
echo 'done'