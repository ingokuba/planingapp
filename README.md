# Cost estimation tool for software projects.

## How to run for development/testing:
### Prerequesites
<img src="public/resources/docker.svg" alt="Docker" width="48"> The commands `docker` and `docker-compose` must be installed 
(available for Linux, Mac, Windows <a href="https://docs.docker.com/compose/install/">here</a>).

### Unix systems
Execute <a href="test/unix/startServers.sh">`test/unix/startServers.sh`</a> to build the Docker containers for the Apache server and MySQL database. 
To shut them down simply run <a href="test/unix/stopServers.sh">`test/unix/stopServers.sh`</a>.
### Windows
Execute <a href="test/windows/startServers.cmd">`test/windows/startServers.cmd`</a> to build exactly the same on Windows.

### Without docker
The application needs a MySQL database which is compatible to the PHP extension `mysqli` (see related blogpost <a href="https://mysqlserverteam.com/upgrading-to-mysql-8-0-default-authentication-plugin-considerations/">here</a>).
The connection parameters for the database can be configured in <a href="public/config.yml">`public/config.yml`</a>.
To create the database and the tables run <a href="test/mysql/000_create_database.sql">`test/mysql/000_create_database.sql`</a> on your database (optionally, the other sql files can also be run to create triggers and some mock data).
<br/>
Then, simply publish the content of the `public/` folder to your Server and start testing.

#### Used extensions
* yaml
* mysqli