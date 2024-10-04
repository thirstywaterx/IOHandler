# IOHandler Class

`IOHandler` 类是一个用于处理 HTTP 输入和输出的简单 PHP 类。它可以清理和验证输入数据，并以 JSON 格式输出响应。该类支持对 GET 和 POST 请求的处理，确保数据的安全性和有效性。

## 特性

- **数据清理**：自动清理和验证输入数据，支持字符串、整数和浮点数类型。
- **错误处理**：支持错误消息的收集与处理，方便调试。
- **JSON 输出**：可以方便地初始化 JSON 输出结构，并发送响应。
- **可扩展性**：类的设计易于扩展，可以根据需要添加更多功能。

## 安装

将 `IOHandler` 类文件添加到您的 PHP 项目中，确保您可以正确引用该文件。

```php
require_once 'IOHandler.php';
```

## 用法示例

以下是如何使用 `IOHandler` 类的示例：

```php
<?php
// 初始化 IOHandler 类以处理 POST 数据
$io = new IOHandler('post');

// 初始化 JSON 输出
$io->initializeJsonOutput('success', true);

// 获取和验证输入
$username = $io->getInput('username', 'string');
$age = $io->getInput('age', 'int');
$salary = $io->getInput('salary', 'float');

// 检查是否有任何错误
if (!$io->canContinue()) {
    $io->sendOutput();
    exit;
}

// 处理业务逻辑
// ...

// 如果成功
$io->markSuccess("数据处理成功。");
$io->sendOutput();
```


```javascript
ajax(someoption).then((response)=>{
    if(response.success){
        console.log("success")
    }else{
        console.log("response.error");
    }
})
```

## 方法说明

### `__construct($method)`

构造函数，初始化输入数据。

- **参数**:
  - `$method` (string): 请求的方法（'post' 或 'get'）。默认值为 'post'。

### `getInput($key, $type)`

获取并清理输入值。

- **参数**:
  - `$key` (string): 输入的键。
  - `$type` (string): 预期的数据类型（'int', 'float', 'string'）。
- **返回**: 清理后的值，如果无效则返回 `null`。

### `checkError($message, $die)`

添加错误消息并在必要时停止执行。

- **参数**:
  - `$message` (string): 错误消息。
  - `$die` (bool): 是否终止脚本。默认值为 `false`。

### `initializeJsonOutput($state, $hasNotice)`

初始化 JSON 输出结构。

- **参数**:
  - `$state` (string): 初始状态（'success' 或 'state'）。
  - `$hasNotice` (bool): 是否包含通知字段。默认值为 `true`。

### `markSuccess($message)`

将操作标记为成功并可选地添加消息。

- **参数**:
  - `$message` (string): 成功消息。

### `sendOutput()`

发送 JSON 输出并终止脚本。

## 许可证

MIT许可证
