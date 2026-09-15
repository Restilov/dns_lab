# Egitim amacli, KASITLI zafiyetli web hedefi.
# Alpine + Nginx + PHP-FPM. Kucuk imaj, tek komutla ayaga kalkar.
FROM alpine:3.20

# Nginx ve PHP-FPM (json + session eklentileriyle) + HTTPS icin ca-certificates
RUN apk add --no-cache \
      nginx \
      php82 \
      php82-fpm \
      php82-json \
      php82-session \
      ca-certificates \
 && ln -sf /usr/bin/php82 /usr/bin/php \
 && mkdir -p /run/nginx /var/lib/php/session \
 && chown -R nginx:nginx /var/lib/php/session

# Nginx sanal sunucu yapilandirmasi (ozel HTTP basliklari burada)
COPY nginx/default.conf /etc/nginx/http.d/default.conf

# PHP-FPM havuzunu 127.0.0.1:9000 uzerinde sabitle
COPY nginx/www.conf /etc/php82/php-fpm.d/www.conf
COPY nginx/php-hardening.ini /etc/php82/conf.d/99-hardening.ini
COPY nginx/panel.conf /etc/nginx/http.d/panel.conf

# Site dosyalari
COPY src/ /var/www/html/
COPY panel/ /var/www/panel/

# gobuster wordlist'i imaj kurulurken OTOMATIK indir (dirb common.txt, SecLists).
# Katilimci GitHub'a gitmeden lab'den ceker: curl -o common.txt http://localhost:1337/common.txt
RUN wget -q -O /var/www/panel/common.txt \
    https://raw.githubusercontent.com/danielmiessler/SecLists/master/Discovery/Web-Content/common.txt

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80 1337
CMD ["/entrypoint.sh"]
