# Пример создания заказа покупателя через Record

В этом примере показано, как сформировать заказ с большим числом заполненных полей при помощи объектного подхода. Основные сведения об объектном подходе можно найти в файле [Объектный подход (Record)](/docs/active_record.md).

Существует два пути формирования заказа:
1. Заполнить все данные при инициализации и сразу отправить запрос на создание.
2. Создавать заказ постепенно, добавляя товары и атрибуты по ходу бизнес‑логики, а затем отправить его методом `create()`.

Ниже приведён пример первого подхода. Поля `name` и `moment` необязательны и могут не задаваться.

```php
use Evgeek\Moysklad\Api\Record\Objects\Documents\CustomerOrder;
use Evgeek\Moysklad\Api\Record\Objects\Entities\Counterparty;
use Evgeek\Moysklad\Tools\Meta;
use Evgeek\Moysklad\MoySklad;

$ms = new MoySklad(['token']);

$order = CustomerOrder::make($ms, [
    'name'    => '10044',
    'code'    => '3374',
    'externalCode' => '9LduQ3OHjvJ4x1YGfY-dQ1',
    'moment'  => '2025-05-13 07:17:00.000',
    'applicable' => true,
    'rate' => [
        'currency' => Meta::currency('e03f64a6-2225-11ed-0a80-073a00365127'),
        'value'    => 0.235,
    ],
    'store'  => Meta::store('dbe32a4b-d5cc-11ec-0a80-0f75000aa95c'),
    'agent'  => Meta::counterparty('028cdc05-2fbf-11f0-0a80-10a600131e5e'),
    'organization' => Meta::organization('0c1d64fa-69e7-11ed-0a80-098f00003a92'),
    'owner' => Meta::employee('b7a74652-91a1-11ed-0a80-0c2c000f0115'),
    'state' => Meta::create(['entity','customerorder','metadata','states','e1c20aa2-cc82-11ec-0a80-08ab00701a43'], 'state'),
    'shipmentAddress' => '41-800, Польша, Zabrze, pl Krakowski, 8, 5',
    'attributes' => [
        [
            'meta'  => Meta::create(['entity','customerorder','metadata','attributes','df8c772e-5391-11ed-0a80-0c8100377928'], 'attributemetadata'),
            'value' => false,
        ],
        [
            'meta'  => Meta::create(['entity','customerorder','metadata','attributes','6cebb0f2-544d-11ed-0a80-05c9000c337d'], 'attributemetadata'),
            'value' => '2336',
        ],
        [
            'meta'  => Meta::create(['entity','customerorder','metadata','attributes','b9c91faf-8d4b-11ef-0a80-13080012affe'], 'attributemetadata'),
            'value' => Meta::employee('b7a74652-91a1-11ed-0a80-0c2c000f0115'),
        ],
    ],
    'positions' => [
        [
            'quantity' => 1,
            'price'    => 137100.0,
            'assortment' => Meta::product('e526e504-0b8f-11ee-0a80-114d000d36d5'),
            'reserve' => 1.0,
        ],
        [
            'quantity' => 1,
            'price'    => 0.0,
            'assortment' => Meta::service('9caefbd1-a128-11ef-0a80-183100010acb'),
        ],
    ],
]);

$order->create();
```

Формируемый объект будет отправлен в API Моего Склада и вернётся с заполненными полями, как в следующем ответе:

```json
{
    "meta": {
        "href": "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/…"
    },
    "name": "10044",
    "code": "3374",
    "moment": "2025-05-13 07:17:00.000",
    "agent": {
        "meta": {
            "href": "https://api.moysklad.ru/api/remap/1.2/entity/counterparty/028cdc05-2fbf-11f0-0a80-10a600131e5e",
            "type": "counterparty"
        }
    },
    "positions": {
        "meta": {
            "href": "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/…/positions",
            "type": "customerorderposition"
        }
    }
    …
}
```

## Постепенное заполнение

При наличии сложной бизнес-логики заказ можно создавать поэтапно. Сначала инициализируйте пустой объект и по мере необходимости добавляйте в него данные. Когда все свойства заполнены, вызовите `create()`:

```php
$order = CustomerOrder::make($ms);

// при необходимости создаём контрагента
$agent = Counterparty::make($ms, ['name' => 'Oskar Karski'])->create();
$order->agent = $agent->meta;

$order->positions[] = [
    'quantity'   => 1,
    'assortment' => Meta::product('e526e504-0b8f-11ee-0a80-114d000d36d5'),
];

$order->attributes[] = [
    'meta'  => Meta::create(['entity','customerorder','metadata','attributes','6cebb0f2-544d-11ed-0a80-05c9000c337d'], 'attributemetadata'),
    'value' => '2336',
];

$order->shipmentAddressFull = [
    'postalCode' => '41-800',
    'country'    => Meta::country('59fcf432-65d3-4180-8420-fe61c7816581'),
    'city'       => 'Zabrze',
    'street'     => 'pl Krakowski',
    'house'      => '8',
    'apartment'  => '5',
];

$order->create();
```

## Обновление заказа

Чтобы изменить заказ, задайте `id` нужной сущности и передайте обновляемые поля в метод `update()`. Поля можно задать как параметром метода, так и через свойства объекта.

```php
use Evgeek\Moysklad\Api\Record\Objects\Documents\CustomerOrder;
use Evgeek\Moysklad\MoySklad;

$ms = new MoySklad(['token']);

// Вариант 1: поля в параметре update()
CustomerOrder::make($ms, ['id' => 'c7a48c56-f252-11ed-0a80-0f6000639033'])
    ->update([
        'name' => '10045',
        'shipmentAddress' => 'Новый адрес доставки',
    ]);

// Вариант 2: поля в свойствах объекта
$order = CustomerOrder::make($ms);
$order->id = 'c7a48c56-f252-11ed-0a80-0f6000639033';
$order->name = '10045';
$order->shipmentAddress = 'Новый адрес доставки';
$order->update();
```

| [<< Объектный подход (Record)](/docs/active_record.md) | [Оглавление](/docs/index.md) | [Форматтеры >>](/docs/formatters.md) |
|:-----------------------------------------|:----------------------------:|-------------------------------------:|
