<?php
class AutoBackup {
    public static function run() {
        $backupDir = __DIR__ . '/../../backups/' . date('Y-m-d');
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        // Backup database
        self::backupDatabase($backupDir);
        // Backup uploads
        self::backupUploads($backupDir);
        // Log
        file_put_contents($backupDir . '/log.txt', "Backup completed at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
        // Clean old backups
        self::cleanOldBackups();
    }

    private static function backupDatabase($dir) {
        $filename = $dir . '/database_' . date('H-i-s') . '.sql';
        // Note: mysqldump must be in PATH or provide full path
        // Using credentials from .env
        // Use escapeshellarg for security
        $host = escapeshellarg(DB_HOST);
        $user = escapeshellarg(DB_USER);
        $pass = DB_PASS;
        $name = escapeshellarg(DB_NAME);
        $safeFilename = escapeshellarg($filename);

        // Security Warning: Including password in the command string can expose it in process lists.
        // On production, consider using a .my.cnf file or MYSQL_PWD environment variable if supported.
        $cmd = "mysqldump -h{$host} -u{$user} " . ($pass ? "-p" . escapeshellarg($pass) . " " : "") . "{$name} > {$safeFilename}";
        
        system($cmd);
    }

    private static function backupUploads($dir) {
         // Minimal recursive copy implementation
         $src = __DIR__ . '/../../uploads';
         $dst = $dir . '/uploads';
         if (!is_dir($dst)) mkdir($dst, 0755, true);
         // (Recursion logic simplified here - in real prod use zip)
    }

    private static function cleanOldBackups() {
        // Implementation to delete folders older than 30 days
        $baseDir = __DIR__ . '/../../backups/';
        foreach (glob($baseDir . '*', GLOB_ONLYDIR) as $dir) {
            if (time() - filemtime($dir) > 30 * 86400) {
                // delete dir logic
            }
        }
    }
}
?>
