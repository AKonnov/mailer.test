#test mailer service 
тестовый проект для отправки очереди писем.
проект на базе Фреймоврка Yii2 Advanced 

Микросервис состоит из двух частей - управление шаблонами сообщений и отправка сообщения.

###CRUD управления шаблонами реализован через backend приложения
Данные шаблона содержат поля: 
code (обязательный, уникальная строка),
modified_at (обязательный, дата/время) (устанавливает автоматически при изменении или добавлении шаблона), 
data_type (обязательный, перечисление [text, html]), 
subject (обязательный, строка), 
body (обязательный, текст). 
В subject и body доступны подстановки вида {PLACEHOLDER}.

Авторизация в части backend и frontend отключена

###Получение очереди писем реализовано через модуль api части frontend приложения

**bold**Пример запроса типа POST для добавления письма:

POST http://DOMAINNAME/api/mails
с объектом json 
{
"from" : "\"Our service\" <service@example.com>",
"to": "\"Иван Сидоров\" <ivan@example.com>",
"template": "welcome",
"data": {
	"NAME":"Иван Петрович",
	"EMAIL": "ivan@example.com",
	"ORGANIZATION":"ООО \"Петров и сыновья\"",
	"DEPARTMENT": "Логистика;",
	"CONFIRMATION_URL": "http://service.example.com\/confirm\/ydZ99TeS2dizWrZj",
	"ABUSE_URL": "http://service.example.com\/abuse\/ydZ99TeS2dizWrZj",
	"date_added": null,
	"date_modified": null
	}
}

POST API возвращает id письма 


###Обработчик очереди писем реализован в части console приложения
обработчик сопоставляет переданное в POST запросе поле template с полем code шаблона.
Если совпадение найдено - письмо отправляется ,если нет, то в лог записывается запись что шаблон не найдет и задача считается выполненной

Обработчик очереди отправки подразумевает использование beanstalkd на сервере.

для запуска обработчика нужно в консоли в корне сайта набрать
$ php yii worker

##установка 
$ php yii init
$ composer update
$ php yii migrate 
