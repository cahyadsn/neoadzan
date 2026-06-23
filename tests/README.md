# NeoAdzan Tests

This directory contains the unit test suite for the NeoAdzan application. The tests are written using a zero-dependency PHP test runner to allow quick, local testing without needing Composer or external testing frameworks.

## 🚀 How to Run Tests

From the root directory of the project, run:
```bash
php tests/run_tests.php
```

## 📂 Test Cases Covered

The test suite in `run_tests.php` covers the following components:
1. **TrigonometriTraits**: Validates degree-based trigonometric calculations and angle corrections.
2. **TimeTraits**: Validates timezone adjustments, hour bounds, and 12-hour/24-hour formatting.
3. **HijriTraits**: Validates conversions between Gregorian, Hijri, and Julian Day numbers.
4. **Cache**: Validates file-based caching mechanism read, write, expiry, and clean-up processes.
5. **NeoAdzan**: Validates prayer time calculation logic and data payload structure.

## ➕ Adding New Tests

To add a new test case:
1. Open `tests/run_tests.php`.
2. Add a new test registration block using the `$runner->run()` method:
   ```php
   $runner->run('Your Component Name - Test Scenario', function($t) {
       $instance = new YourClass();
       // Assertions:
       $t->assertTrue($instance->someCondition());
       $t->assertEquals('expected_val', $instance->getValue());
       $t->assertNear(1.23, $instance->getFloat(), 0.01);
   });
   ```
