<?php
/*
 * Simple File-Based Caching Class
 */
class Cache {
    private static $cacheDir = APP_ROOT . '/cache/';

    /**
     * Get an item from the cache.
     *
     * @param string $key The unique key for the cache item.
     * @return mixed The cached data, or false if not found or expired.
     */
    public static function get($key) {
        $file = self::$cacheDir . sha1($key) . '.cache';

        if (!file_exists($file)) {
            return false;
        }

        $content = file_get_contents($file);
        $data = unserialize($content);

        // Check for expiration
        if (time() > $data['expires']) {
            // Cache has expired, delete the file
            unlink($file);
            return false;
        }

        return $data['data'];
    }

    /**
     * Store an item in the cache.
     *
     * @param string $key The unique key for the cache item.
     * @param mixed $value The data to be cached.
     * @param int $duration The cache lifetime in seconds.
     * @return bool True on success, false on failure.
     */
    public static function set($key, $value, $duration = 3600) { // Default 1 hour
        $data = [
            'expires' => time() + $duration,
            'data' => $value,
        ];

        $file = self::$cacheDir . sha1($key) . '.cache';
        $content = serialize($data);

        return file_put_contents($file, $content) !== false;
    }

    /**
     * Delete an item from the cache.
     *
     * @param string $key The unique key for the cache item.
     * @return bool True on success, or if the file doesn't exist.
     */
    public static function delete($key) {
        $file = self::$cacheDir . sha1($key) . '.cache';

        if (file_exists($file)) {
            return unlink($file);
        }

        return true;
    }
}
?>