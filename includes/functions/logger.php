<?php
class Logger {
    private static $logFile = __DIR__ . '/../../logs/app.log';

    public static function info($message, $context = []) {
        self::write('INFO', $message, $context);
    }

    public static function error($message, $context = []) {
        self::write('ERROR', $message, $context);
        // Optionally notify admin via email
    }

    private static function write($level, $message, $context) {
        $entry = sprintf(
            "[%s] %s: %s %s\n",
            date('Y-m-d H:i:s'),
            $level,
            $message,
            json_encode($context, JSON_UNESCAPED_UNICODE)
        );
        file_put_contents(self::$logFile, $entry, FILE_APPEND);
    }
}
?>
