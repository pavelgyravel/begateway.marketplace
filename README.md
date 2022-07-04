# Модуль 1C-Битрикс для авторизации пользователей bePaid marketplace
## Установка модуля
- Загрузить директорию begateway.marketplace в bitrix/modules/
- В административной части 1C-Битрикс перейти в раздел Marketplace - Установленные решения
- В списке доступных решений появится модуль begateway.marketplace, его необходимо установить
- В разделе Настройки - Настройки модулей - Социальные сервисы необходимо активировать сервис Begateway marketplace
- В разделе Настройки - Настройки модулей - Авторизация пользователей сервиса bePaid marketplace необходимо добавить новую запись для вашего сайта с данными для подключния к bePaid marketplace, а именно:
    - HOST - адрес Begateway marketplace (например https://idp-demo.marketplace.ecomcharge.com/)
    - CLIENT ID
    - CLIENT SECRET
- Также необходимо сообщить в bePaid marketplace RETURN URL (адрес по котоому будет перенаправляться пользователь из Begateway marketplace)

## Работа модуля
- Пользователь редиректом переходит из bePaid marketplace на сайт магазина по адресу **{{SHOP_DOMAIN}}/bitrix/tools/oauth/bemarketplace.php?access_code=&marketplace_id=&client_id=**
- Модуль, по GET параметру client_id находит в БД соотвествующую запись (ранее были добавлены в  Настройки модулей - Авторизация пользователей сервиса bePaid marketplace).
- Модуль, используя  HOST, CLIENT ID, CLIENT SECRET из настроек модуля и access_code из GET параметров, делает запрос в bePaid marketplace, где получает access_token и refresh_token.
- Модуль, используя  HOST, CLIENT ID, CLIENT SECRET и access_token делает запрос в bePaid marketplace, где получает user_id пользователя в bePaid marketplace.
- Модуль авторизирует пользователя в магазине и перекидывает его на главную страницу магазина. 