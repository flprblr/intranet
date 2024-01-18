
# Intranet YNK

## DEV(localhost)
```bash
npm cache verify && \
npm update --no-audit && \
composer update --no-interaction --prefer-dist --optimize-autoloader && \
php artisan optimize
```
    
## Prepare to QAS/PRD
```bash
npm cache verify && \
npm ci && \
composer install --no-dev --optimize-autoloader  && \
npm run build && \
rm -rf node_modules && \
rm -f storage/logs/laravel.log && \
cd .. && \
zip -q -r intranet.zip intranet -x ".DS_Store" -x "__MACOSX"
```

## Deploy to QAS(WHM/cPanel)
```bash
unzip -qo intranet.zip && \
rm -f intranet.zip && \
rm -rf public_html && \
ln -s /home/qasintranet/intranet/public /home/qasintranet/public_html && \
cd intranet && \
php artisan optimize && \
composer clear-cache && \
composer update --no-dev --optimize-autoloader  --no-interaction && \
find /home/qasintranet -name ".DS_Store" -type f -delete
```

## Deploy to PRD(WHM/cPanel)
```bash
cd intranet && \
php artisan down && \
cd .. && \
date=$(date +'%d-%m-%Y') && mv /home/intranet/intranet /home/intranet/intranet-$date && \
unzip -qo intranet.zip && \
rm -f intranet.zip && \
rm -rf public_html && \
ln -s /home/intranet/intranet/public /home/intranet/public_html && \
cd intranet && \
php artisan optimize && \
composer clear-cache && \
composer update --no-dev --optimize-autoloader  --no-interaction && \
find /home/intranet -name ".DS_Store" -type f -delete
```

## DSN WHM
```bash
[HSPR]
servernode = 192.168.30.227:30241
driver = /usr/sap/hdbclient/libodbcHDB.so
encrypt = true
sslValidateCertificate = false

[HCPR]
servernode = 192.168.31.181:30241
driver = /usr/sap/hdbclient/libodbcHDB.so
encrypt = true
sslValidateCertificate = false
```

## DSN MacOS
```bash
[HSPR]
servernode = 192.168.30.227:30241
driver = /Applications/sap/hdbclient/libodbcHDB.dylib
encrypt = true
sslValidateCertificate = false

[HCPR]
servernode = 192.168.31.181:30241
driver = /Applications/sap/hdbclient/libodbcHDB.dylib
encrypt = true
sslValidateCertificate = false
```