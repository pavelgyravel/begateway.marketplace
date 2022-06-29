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