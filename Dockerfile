FROM centos:6
MAINTAINER Alex & Pavel (was: Takayuki Miwa <i@tkyk.name>)

ENV code_root /code
ENV httpd_conf ${code_root}/httpd.conf

RUN rpm -ivh http://dl.fedoraproject.org/pub/epel/6/i386/epel-release-6-8.noarch.rpm
RUN rpm -ivh http://rpms.famillecollet.com/enterprise/remi-release-6.rpm
RUN yum install -y httpd
RUN yum install --enablerepo=epel,remi-php72,remi -y \
                              php \
                              php-cli \
                              php-gd \
                              php-mbstring \
                              php-mcrypt \
                              php-mysqlnd \
                              php-pdo \
                              php-xml \
                              php-xdebug
RUN sed -i -e "s|^;date.timezone =.*$|date.timezone = America/New_York|" /etc/php.ini

ADD . $code_root
###mine httpd.conf
RUN cp ${code_root}/httpd_full.conf /etc/httpd/conf/httpd.conf
RUN test -e $httpd_conf && echo "Include $httpd_conf" >> /etc/httpd/conf/httpd.conf

##RUN chown -R apache:apache /code/www/laravel


EXPOSE 80
CMD ["/usr/sbin/apachectl", "-D", "FOREGROUND"]

