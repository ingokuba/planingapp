# Cost estimation tool for software projects.

## How to run for development/testing:
### Prerequesites
<img src="public/resources/docker.svg" alt="Docker" width="48"> The commands `docker` and `docker-compose` must be installed 
(available for Linux, Mac, Windows <a href="https://docs.docker.com/compose/install/">here</a>).


### Unix systems
Execute `test/unix/startServers.sh` to build the Docker containers for the Apache server and MySQL database. 
To shut them down simply run `test/unix/stopServers.sh`.
### Windows
Execute `test/windows/startServers.cmd` to build exactly the same on Windows.