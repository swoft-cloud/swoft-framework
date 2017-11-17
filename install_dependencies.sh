cd .
wget https://github.com/redis/hiredis/archive/v0.13.3.tar.gz -O hiredis.tar.gz
mkdir -p hiredis
tar -xf hiredis.tar.gz -C hiredis --strip-components=1 & rm hiredis.tar.gz
cd hiredis
make -j$(nproc)
make install
ldconfig
rm -r hiredis

cd .
wget https://github.com/swoole/swoole-src/archive/v2.0.10-beta.tar.gz -O swoole.tar.gz \
mkdir -p swoole
tar -xf swoole.tar.gz -C swoole --strip-components=1
rm swoole.tar.gz
cd swoole
phpize
./configure --enable-async-redis --enable-mysqlnd --enable-coroutine
make -j$(nproc)
make install
rm -r swoole
echo "extension = swoole.so" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini