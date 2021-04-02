FROM node:15.9-alpine as node
FROM nginx:1.19-alpine
SHELL ["/bin/ash", "-oeux", "pipefail", "-c"]

ENV TZ=UTC

RUN apk update && \
  apk add --update --no-cache --virtual=.build-dependencies g++

COPY --from=node /usr/local/bin /usr/local/bin
COPY --from=node /opt /opt
COPY ./default.conf /etc/nginx/conf.d/default.conf

RUN apk add --update npm

WORKDIR /work/backend
