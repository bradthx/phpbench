<?php
/*
 * phpcpubench
 * Copyright (c) 2026 Brad Boegler <bradthx@gmail.com>
 * Licensed under the MIT License.
 */

// Simple single-thread PHP CPU benchmark
// Tests raw CPU-ish performance with integer math, floats, hashes, and prime searching.

set_time_limit(300);
ini_set('memory_limit', '256M');

function bench(string $name, callable $fn): array {
    $start = hrtime(true);
    $result = $fn();
    $end = hrtime(true);

    return [
        'name' => $name,
        'seconds' => ($end - $start) / 1_000_000_000,
        'result' => $result,
    ];
}

// Support both CLI and web
if (PHP_SAPI === 'cli') {
    $iterations = isset($argv[1]) ? max(1, (int)$argv[1]) : 1;
} else {
    $iterations = isset($_GET['n']) ? max(1, (int)$_GET['n']) : 1;
}

$tests = [];

for ($run = 1; $run <= $iterations; $run++) {
    $tests[] = bench("Integer math run $run", function () {
        $x = 123456789;
        for ($i = 0; $i < 50_000_000; $i++) {
            $x = (($x * 1103515245 + 12345) & 0x7fffffff);
        }
        return $x;
    });

    $tests[] = bench("Floating point math run $run", function () {
        $x = 0.5;
        for ($i = 1; $i <= 20_000_000; $i++) {
            $x += sin($i) * cos($i / 2) / sqrt($i);
        }
        return round($x, 6);
    });

    $tests[] = bench("SHA256 hashing run $run", function () {
        $data = str_repeat('benchmark-data-', 100);
        $hash = '';
        for ($i = 0; $i < 1_000_000; $i++) {
            $hash = hash('sha256', $data . $i . $hash);
        }
        return substr($hash, 0, 16);
    });

    $tests[] = bench("Prime search run $run", function () {
        $count = 0;
        $limit = 500_000;

        for ($n = 2; $n <= $limit; $n++) {
            $prime = true;
            $sqrt = (int)sqrt($n);

            for ($d = 2; $d <= $sqrt; $d++) {
                if ($n % $d === 0) {
                    $prime = false;
                    break;
                }
            }

            if ($prime) {
                $count++;
            }
        }

        return $count;
    });
}

$total = array_sum(array_column($tests, 'seconds'));

header('Content-Type: text/plain');

echo "PHP CPU Benchmark\n";
echo "=================\n";
echo "Host: " . gethostname() . "\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "SAPI: " . php_sapi_name() . "\n";
echo "Date: " . date('c') . "\n";
echo "Runs per test: $iterations\n\n";

foreach ($tests as $test) {
    printf(
        "%-30s %8.4f sec   result: %s\n",
        $test['name'],
        $test['seconds'],
        (string)$test['result']
    );
}

echo "\nTotal time: " . number_format($total, 4) . " sec\n";
echo "Lower is better.\n";
