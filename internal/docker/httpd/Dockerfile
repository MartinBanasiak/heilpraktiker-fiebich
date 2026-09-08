FROM httpd:latest

RUN apt-get update
RUN apt-get install vim -y

RUN chmod -R g+x /usr/local/apache2/htdocs

EXPOSE 80