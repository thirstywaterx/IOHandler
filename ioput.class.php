<?php
class IOHandler {
    // 属性
    private $data = [];
    private $errors = [];
    private $allowContinue = true;
    private $outputData = [];

    // 构造函数以初始化数据
    public function __construct($method = 'post') {
        $this->data = $method === 'post' ? $_POST : $_GET;
    }

    /**
     * 获取清理后的输入值。
     *
     * @param string $key 输入的键。
     * @param string $type 预期的数据类型（'int', 'float', 'string'）。
     * @return mixed 清理后的值，如果无效则返回 null。
     */
    public function getInput($key, $type = 'string') {
        if (!isset($this->data[$key])) {
            $this->addError("缺少参数: $key");
            return null;
        }

        $value = $this->data[$key];

        switch ($type) {
            case 'int':
                if (filter_var($value, FILTER_VALIDATE_INT) !== false) {
                    return (int) $value;
                }
                $this->addError("无效的整数: $key");
                break;

            case 'float':
                if (filter_var($value, FILTER_VALIDATE_FLOAT) !== false) {
                    return (float) $value;
                }
                $this->addError("无效的浮点数: $key");
                break;

            case 'string':
                $sanitized = $this->sanitizeString($value);
                if ($sanitized !== '') {
                    return $sanitized;
                }
                $this->addError("无效的字符串: $key");
                break;

            default:
                $this->addError("不支持的数据类型: $key");
        }

        return null;
    }

    /**
     * 清理字符串输入。
     *
     * @param string $data 输入字符串。
     * @return string 清理后的字符串。
     */
    private function sanitizeString($data) {
        $data = trim($data);
        $data = stripslashes($data);
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }

    /**
     * 添加错误消息并在必要时停止执行。
     *
     * @param string $message 错误消息。
     * @param bool $die 是否终止脚本。
     */
    public function checkError($message, $die = false) {
        $this->addError($message);
        $this->allowContinue = false;
        if ($die) {
            $this->sendOutput();
            exit;
        }
    }

    /**
     * 添加错误消息。
     *
     * @param string $message 错误消息。
     */
    private function addError($message) {
        $this->errors[] = $message;
    }

    /**
     * 判断处理是否可以继续。
     *
     * @return bool
     */
    public function canContinue() {
        return $this->allowContinue;
    }

    /**
     * 初始化 JSON 输出结构。
     *
     * @param string $state 初始状态（'success' 或 'state'）。
     * @param bool $hasNotice 是否包含通知字段。
     */
    public function initializeJsonOutput($state, $hasNotice = true) {
        header("Content-Type: application/json; charset=UTF-8");
        $this->outputData = [];

        if ($hasNotice) {
            $this->outputData["notice"] = "";
        }

        if ($state === "success") {
            $this->outputData["success"] = false;
        } elseif ($state === "state") {
            $this->outputData["state"] = "";
        }
    }

    /**
     * 向通知字段添加消息。
     *
     * @param string $message 要添加的消息。
     */
    public function addMessage($message) {
        if (isset($this->outputData["notice"])) {
            $this->outputData["notice"] .= $message . " ";
        }
    }

    /**
     * 将操作标记为成功并可选地添加消息。
     *
     * @param string $message 成功消息。
     */
    public function markSuccess($message = "") {
        $this->outputData["success"] = true;
        if (!empty($message)) {
            $this->addMessage($message);
        }
    }

    /**
     * 设置状态字段。
     *
     * @param string $state 状态消息。
     */
    public function setState($state) {
        $this->outputData["state"] = $state;
    }

    /**
     * 发送 JSON 输出并终止脚本。
     */
    public function sendOutput() {
        if (!headers_sent()) {
            header("Content-Type: application/json; charset=UTF-8");
        }
        echo json_encode($this->outputData, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 如果有错误，最终输出。
     */
    public function finalizeOutput() {
        if (!empty($this->errors)) {
            $this->outputData["success"] = false;
            $this->outputData["errors"] = $this->errors;
        }
        $this->sendOutput();
    }
}