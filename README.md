### 使用方法
```php
return [
     'components' => [
          'request' => [
              'parsers' => [
                  'protobuf' => 'Language\Protobuf\ProtobufParser'
              ],
          ],
          'response' => [
            'formatters'=>[
                'protobuf' => 'Language\Protobuf\ProtobufResponseFormatter'
            ],
          ],
          // ...
      ],
      // ...
  ];

```