FROM tvial/docker-mailserver:stable

RUN touch /etc/postfix/vmailbox
RUN apt-get update
RUN apt-get install -y net-tools dnsutils iputils-ping
