FROM mysql:8.0

#####################################
# Set Timezone
#####################################

ARG TZ=UTC
ENV TZ ${TZ}
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone && chown -R mysql:root /var/lib/mysql/
# for windows. マウントではwindowsのファイルシステムが原因でmysqlの許す権限に設定できない
COPY my.cnf /etc/mysql/conf.d/my.cnf
