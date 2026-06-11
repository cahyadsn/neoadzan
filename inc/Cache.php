<?php
/*
BISMILLAAHIRRAHMAANIRRAHIIM - In the Name of Allah, Most Gracious, Most Merciful
================================================================================
FILENAME     : Cache.php
PURPOSE      : Simple file-based caching system
AUTHOR       : CAHYA DSN
CREATED DATE : 2026-06-11
UPDATED DATE : 2026-06-11 15:45:00
DEMO SITE    : http://neoadzan.cahyadsn.com
SOURCE CODE  : https://github.com/cahyadsn/neoadzan
================================================================================
This program is free software; you can redistribute it and/or modify it under the
terms of the MIT License.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

See the MIT License for more details

copyright (c) 2018-2026 by cahya dsn; cahyadsn@gmail.com
================================================================================*/
class Cache {
    private $cache_path;
    private $expiry;

    public function __construct($expiry = 86400) { // Default 1 day
        $this->cache_path = __DIR__ . '/../cache/';
        $this->expiry = $expiry;
        if (!is_dir($this->cache_path)) {
            mkdir($this->cache_path, 0777, true);
        }
    }

    public function get($key, $custom_expiry = null) {
        $filename = $this->cache_path . md5($key) . '.cache';
        $expiry = $custom_expiry ?? $this->expiry;
        if (file_exists($filename) && (time() - filemtime($filename) < $expiry)) {
            return unserialize(file_get_contents($filename));
        }
        return null;
    }

    public function set($key, $data) {
        $filename = $this->cache_path . md5($key) . '.cache';
        return file_put_contents($filename, serialize($data));
    }

    public function clear() {
        $files = glob($this->cache_path . '*.cache');
        foreach ($files as $file) {
            unlink($file);
        }
    }
}
