

## MB Support Bot
Бот предназнчен в помощь операторам тех поддержки когда не совсем удобно заходить в админ панель

### 1. Установка

Устанвливаем пакеты и зависимости
```shell script
cd /var/www/
git clone https://github.com/kagatan/mb-support-bot.git
cd mb-support-bot

composer install

# даем права
sudo chown -R www-data:www-data /var/www/mb-support-bot
sudo chmod -R 775 /var/www/mb-support-bot/storage/

```

### 2. Настраиваем .env
Необходимые к заполнению:

```shell script
APP_URL=https://my-domen.ru
TELEGRAM_BOT_TOKEN="11111:xxxxxxxxxxxx"
TELEGRAM_BOT_NAME="name_bot"
TELEGRAM_BOT_ALLOWED_ID="[1234345, 4789456]"

MIKBILL_CABINET_HOST="https://stat.my-domen.ru"
MIKBILL_HOST="https://admin.my-domen.ru"
MIKBILL_LOGIN=admin
MIKBILL_PASSWORD=admin

```


### 3. Webhook

Установить webhook
```php
php artisan telebot:webhook --setup
```

Удалить webhook
```php
php artisan telebot:webhook --remove
```

### 4. Long pooling

Запустить в режиме пулинга без вебхука.

Чтоб запустить необходимо сначала выполнить команду 
"удалить вебхук" если он установлен
```php
php artisan telebot:polling --all
```
