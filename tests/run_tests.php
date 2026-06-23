<?php
/*
BISMILLAAHIRRAHMAANIRRAHIIM - In the Name of Allah, Most Gracious, Most Merciful
================================================================================
FILENAME     : run_tests.php
PURPOSE      : Unit testing suite for NeoAdzan
AUTHOR       : CAHYA DSN
CREATED DATE : 2026-06-23
================================================================================*/

// Enable full error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include dependencies
require_once __DIR__ . '/../inc/NeoAdzan.php';
require_once __DIR__ . '/../inc/Cache.php';

// A simple test runner class
class TestRunner {
    private $passed = 0;
    private $failed = 0;
    private $failures = [];

    public function run($name, $callback) {
        echo "Running test: {$name}... ";
        try {
            $callback($this);
            echo "\033[32mPASSED\033[0m\n";
            $this->passed++;
        } catch (Exception $e) {
            echo "\033[31mFAILED\033[0m\n";
            $this->failed++;
            $this->failures[] = [
                'name' => $name,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ];
        }
    }

    public function assertEquals($expected, $actual, $message = '') {
        if ($expected !== $actual) {
            $msg = $message ?: "Expected " . var_export($expected, true) . ", got " . var_export($actual, true);
            throw new Exception($msg);
        }
    }

    public function assertNear($expected, $actual, $precision = 0.0001, $message = '') {
        if (abs($expected - $actual) > $precision) {
            $msg = $message ?: "Expected near " . var_export($expected, true) . ", got " . var_export($actual, true) . " (diff: " . abs($expected - $actual) . ")";
            throw new Exception($msg);
        }
    }

    public function assertTrue($condition, $message = '') {
        if ($condition !== true) {
            throw new Exception($message ?: "Expected true, got false");
        }
    }

    public function assertFalse($condition, $message = '') {
        if ($condition !== false) {
            throw new Exception($message ?: "Expected false, got true");
        }
    }

    public function assertNotNull($actual, $message = '') {
        if ($actual === null) {
            throw new Exception($message ?: "Expected not null");
        }
    }

    public function printSummary() {
        echo "\n=========================================\n";
        echo "TEST SUMMARY\n";
        echo "=========================================\n";
        echo "Passed: \033[32m{$this->passed}\033[0m\n";
        echo "Failed: " . ($this->failed > 0 ? "\033[31m{$this->failed}\033[0m" : "0") . "\n";
        
        if ($this->failed > 0) {
            echo "\nFailures details:\n";
            foreach ($this->failures as $failure) {
                echo "- \033[31m{$failure['name']}\033[0m: {$failure['message']}\n";
            }
            exit(1);
        } else {
            echo "\n\033[32mAll tests passed successfully!\033[0m\n";
            exit(0);
        }
    }
}

// Start testing
$runner = new TestRunner();

// 1. Test TrigonometriTraits
$runner->run('TrigonometriTraits - Math Functions', function($t) {
    // Create a class that uses the trait for testing
    $math = new class { use TrigonometriTraits; };
    
    $t->assertNear(0.0, $math->dsin(0));
    $t->assertNear(1.0, $math->dsin(90));
    $t->assertNear(0.5, $math->dsin(30));
    
    $t->assertNear(1.0, $math->dcos(0));
    $t->assertNear(0.0, $math->dcos(90));
    $t->assertNear(0.5, $math->dcos(60));
    
    $t->assertNear(1.0, $math->dtan(45));
    $t->assertNear(30.0, $math->darcsin(0.5));
    $t->assertNear(60.0, $math->darccos(0.5));
    $t->assertNear(45.0, $math->darctan(1.0));
    
    $t->assertEquals(45.0, $math->fixangle(405));
    $t->assertEquals(315.0, $math->fixangle(-45));
});

// 2. Test TimeTraits
$runner->run('TimeTraits - Time Functions', function($t) {
    $time = new class { use TimeTraits; };
    
    $t->assertEquals(1.5, $time->fixhour(25.5));
    $t->assertEquals(22.5, $time->fixhour(-1.5));
    
    $t->assertEquals('05:30', $time->floatToTime24(5.5));
    $t->assertEquals('17:45', $time->floatToTime24(17.75));
    
    $t->assertEquals('5:30 am', $time->floatToTime12(5.5));
    $t->assertEquals('5:45 pm', $time->floatToTime12(17.75));
    $t->assertEquals('5:45', $time->floatToTime12(17.75, true));
    
    $t->assertEquals(1.5, $time->timeDiff(4.0, 5.5));
});

// 3. Test HijriTraits
$runner->run('HijriTraits - Calendar Conversion', function($t) {
    $hijri = new class { use HijriTraits; };
    
    // Test conversion from Gregorian to Hijri for a known date
    // June 23, 2026
    $ts = strtotime('2026-06-23');
    $hDate = $hijri->fromGregorianToHijri($ts);
    
    $t->assertNotNull($hDate);
    $t->assertEquals(3, count($hDate)); // Array containing [month, day, year]
    
    // Verify conversion round-trip
    $jd = cal_to_jd(CAL_GREGORIAN, 6, 23, 2026);
    $hijri_from_jd = $hijri->fromJDToHijri($jd);
    $jd_back = $hijri->fromHijriToJD($hijri_from_jd[0], $hijri_from_jd[1], $hijri_from_jd[2]);
    $t->assertEquals($jd, $jd_back);
});

// 4. Test Cache system
$runner->run('Cache - Read and Write', function($t) {
    $cache = new Cache(3600); // 1 hour expiry
    $cacheKey = 'test_key_unq_123';
    $cacheValue = ['foo' => 'bar', 'val' => 42];
    
    // Clear any leftover
    $cacheFilename = __DIR__ . '/../cache/' . md5($cacheKey) . '.cache';
    if (file_exists($cacheFilename)) {
        unlink($cacheFilename);
    }
    
    $t->assertTrue(is_null($cache->get($cacheKey)));
    
    // Save to cache
    $cache->set($cacheKey, $cacheValue);
    
    // Retrieve and check
    $retrieved = $cache->get($cacheKey);
    $t->assertEquals($cacheValue, $retrieved);
    
    // Clean up
    if (file_exists($cacheFilename)) {
        unlink($cacheFilename);
    }
});

// 5. Test NeoAdzan calculation
$runner->run('NeoAdzan - Calculation verification', function($t) {
    // Jakarta Coordinates
    $lat = -6.1751;
    $lng = 106.8272;
    $tz = 7; // GMT+7
    
    $adzan = new NeoAdzan();
    $adzan->setLatLng($lat, $lng);
    $adzan->setTimeZone($tz);
    
    // Get monthly schedule for June 2026
    $monthly = $adzan->getMonthly(2026, 6);
    
    $t->assertTrue($monthly['status']);
    $t->assertEquals($lat, $monthly['data']['lokasi']['lat']);
    $t->assertEquals($lng, $monthly['data']['lokasi']['lng']);
    
    // Ensure we have 30 days in June
    $t->assertEquals(30, count($monthly['data']['jadwal']));
    
    // Check first day has valid times format (HH:MM)
    $day1 = $monthly['data']['jadwal'][1];
    $t->assertTrue((bool)preg_match('/^\d{2}:\d{2}$/', $day1['shubuh']));
    $t->assertTrue((bool)preg_match('/^\d{2}:\d{2}$/', $day1['dhuhur']));
    $t->assertTrue((bool)preg_match('/^\d{2}:\d{2}$/', $day1['ashar']));
    $t->assertTrue((bool)preg_match('/^\d{2}:\d{2}$/', $day1['maghrib']));
    $t->assertTrue((bool)preg_match('/^\d{2}:\d{2}$/', $day1['isya']));
});

$runner->printSummary();
