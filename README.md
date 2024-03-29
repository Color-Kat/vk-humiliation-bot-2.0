# VK humilination bot 2.0

Дата рождения: 25.08.2021
Возраст: 1 годик.

## ВК бот-унижатель 2.0

Вторая версия бота унижателя, который в ответ на сообщения пользователя отвечает различными оскарблениями унижениями и шутками. А также может рассказывать различные истории анегдоты, отвечать на сообщения пользователя, рифмовать их ответы и мн. др

Но это вторая улучшенная версия Григория.
Здесь уже более продуманная структура с использование паттернов проектирования (в отличии от первой версии, где один файл, в котором с помощью условий определось, что ответить пользователю),
работа с БД, или, возможно, redis и memcached - бот будет сохранять сообщения, чтобы ответить пользователю уже с использованием предыдущих реплик. Бот берет ответы и фразы, на которые нужно ответить из раличных словарей - json файлы, которые очень удобно расширять.

---

## Инструкция к словарю

Бот генерирует ответ для пользователя с помощью json словаря.
Файлы словаря находятся в `app/humiliationBot/dictionaries/`.
Словарь разбит на части, которые удобно писать самому - они находятся в папке `parts`.
Все части, которые вы написали собираются в единый словарь, который в коде удобнее читать.

```
php ./createBigDictionary.php
```

# Как писать словарь

Словарь делится на два вида:

- wordbooks - здесь записываются значение переменных
- dictionaries - целые фразы с контекстом их употребления.

### Wordbooks

Итак, рассмотрим wordbooks:
словарь Wordbook должен лежать в папке `wordbooks`, и название файла обязательно начинатся с префикса `w_`. После префикса идёт название переменной. Файл должен содержать следующий код:

```
{
  "type": "wordbook",
  "name": "var_name",
  "content": []
}
```

Name указывает название переменной, а content - список значений переменной.
В dictionaries, чтобы не писать длинную конструкцию для того, чтобы вставить в строку случайное слово, мы будем использовать переменные. Чтобы вставить переменную, пишем `(@var_name) - вместо этой конструкции в строку подставится случайное значение из списка `content`.

### Dictionaries

С dictionaries всё сложнее.
Начнем с того, что эти словари находятся в папке `dictionaries`, название обязательно начинается с префикса `d_`. А сам файл дожен содержать следующий код:

```
{
  "type": "dictionary",
  "content": {
    "name": "event",
    "answers": []
  }
}
```

Поле `content` содержит название `name` этого словаря и ответы, которые бот будет использовать при событии "event".

В проекте уже есть словари со всеми существующими типами событий, например: audio, long_text и тд.

> P.s. Как работают события? В коде отслеживается, какое действие совершил пользователь. Например, если он отправил голосовое сообщение, то ответ берется из словаря с событием audio_message и т.п. Когда нет ни одного подходящего события или нет ответа на какую-то фразу, то ответ берется из словаря `d_standart.json`. Если к тому же генератор случайных чисел сработал так, что выполняется шанс 10%/25%/50%/100%.., то ответ берется из специальных словарей: d_change_10/25/50...

Среди всех ответов answers рандомно выбирается один ответ, если нет дополнительных условий.

Ответ в поле "answers" может быть 2х типов:

- строка - если этот ответ был случайно выбран, то бот отвечает пользователю этой строкой
- объект - более сложное поведение, можно указать шаблон сообщения пользователя, выполнение какой-либо функции и т.д. Далее более подробно

### Объект

Объект может быть такой структуры:

```
{
    "pattern": "/regEx/",
    "priority" : 10,
    "execFunc": [],
    "conditions": {},
    "messages": []
}
```

- Поле `messages` обязательно! В нём содержится список ответов, один из которых будет случайно выбран, если для ответа выбран этот объект (В качестве ответа может быть так же использоваться объект)

- Поле `pattern` используется, чтобы указать шаблон сообщения пользователя в формате regEx - если сообщение пользователя подходит под шаблон, то из списка всех ответов к данномы event'у выбирается именно этот ответ.
  ЕСЛИ СООБЩЕНИЕ ПОДХОДИТ ПО ШАБЛОНУ, ТО БОТ ОТВЕЧАЕТ ЭТИМ ОБЪЕКТОМ!!! То есть если у вас 10 ответов в виде строки и один объект, и сообщение пользователя подходит под шаблон в объекте, то ответ берется из объекта.

- Поле `priority`. Бывает, что один шаблон похож на другой: напримерЖ "Привет" и "Привет!". Чтобы бот приоритетно выбрал ответ, вы можете указать priority - чем больше значение, тем ответ приоритетнее.

- поле `execFunc`. Когда для ответа выбирается этот объект, то выполняется функция из списка execFunc. Пока доступна только функция `createAliasName` - она создаст в базе данных кличку пользователю, использовать в словаре кличку можно, написав (@aliasNames)

- поле `conditions` - используется, чтобы проверить какое-то условие. Если условие не выполняется, то этот ответ не подходит, бот ищет другой ответ. Примеры:

```
"conditions": {
    "isset": ["aliasName"] // В словаре обязательно должна быть переменная aliasName.
    "false": "isSubscribed" // Чтобы этот ответ сработал, пользователь должен быть подписан на группу.
}
```

---

### Теперь как писать сами ответы?

Самый банальный пример, когда ответ простая строка:

```
{
    // Бот случайно ывберет ответ:
    // или "Ты дурак" или "Ты срамник"
    "messages": [
        "Ты дурак",
        "Ты срамник"
    ]
}
```

В строку можно вставлять значение из переменных

```
{
    "messages": [
        "(@hi), ты (@insult)"
    ]
}
```

Бот подставит вместо (@hi) случайную строку из словаря `w_hi.json`, вместо (@insult) - из словаря `w_insult.json`. Например, может получиться такая строка "Дарова, ты лиходейка".

И наконец последнее - испоьзование функций в строке:
{
"messages": [
"{@rand(Подпишись, |Подпишись на меня, |Подпишись на группу, ||)} (@insult)"
]
}
Функции записываются в виде `{@func_name(param1|param2)}` - вместо этой конструкции будет подставлен результат выполнения функции. Например, в данном случае может получиться строка "Подпишись, Люся" или "Подпишись на меня, порось" или "Дурак" (|| - тоже параметр - пустая строка). Пока существует только несколько функций:

- {@rand()} - выбирает случайное значение среди всех параметров.
- {@caps(строка)} - делает все буквы в строке в верхнем регистре
- {@repeat(строка|2)} - посторяет строку указанное кол-во раз (здесь: 2)

## Последовательные ответы

Последовательные ответы сложны для понимания. Когда выбирается такой ответ, то сразу определяется то, что бот ответит в следующем сообщении. Вот пример:

```
{
    "with_prev_mess_id": "lets_dance_mazurka",
    "messages": "Давай станцуем мазурку",
    "next": [
        {
            "pattern": "/((@yes)|п.шли|п.йд.м|х.р.ш|ладн)/",
            "messages": "*Включается весёлая музыка для мазурки, но Гриша всё путает и начинает танцевать грузинскую чечётку*"
        },
        "По-нормальному ответь!"
    ]
},
```

Когда выпадет этот ответ, бот отправит пользователю сообщение "Давай станцуем мазурку". Когда пользователь отправит следующее сообщение, бот найдёт в словаре ответ по ключу "with_prev_mess_id", то есть от будет выбирать ответ из поля next. Здесь ситуация как с обычными ответами. Сначала бот смотрит, есть ли совпадение по шаблону: если есть - выбирает этот ответ, если нет, то ищет другой ответ в поле next (Если и там не выполняется условие или шаблон, то бот ищет в поле next другой ответ и т.д.). И так, если в полу next нет подходящего ответа, то бот отвечает стандартным сообщение из словаря standart.json.

P.s. У каждого последовательного ответа должен быть уникальный id (`with_prev_mess_id`)!
