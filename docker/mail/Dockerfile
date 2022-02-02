FROM tvial/docker-mailserver:stable

RUN touch /etc/postfix/vmailbox
RUN apt-get update && \
    apt-get install -y net-tools dnsutils iputils-ping && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*
