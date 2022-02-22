FROM mailserver/docker-mailserver:10.4.0

RUN touch /etc/postfix/vmailbox
RUN apt-get update && \
    apt-get install -y net-tools dnsutils iputils-ping && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*
