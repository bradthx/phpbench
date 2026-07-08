# phpcpubench

Small PHP CPU benchmark focused on core compute workloads with minimal setup.

## What it does

This script runs a fixed set of single threaded workloads and reports per test runtime in seconds plus total runtime.

Workloads included:
- Integer math loop with linear congruential style state updates.
- Floating point loop using `sin`, `cos`, and `sqrt` calls.
- SHA256 hashing loop over repeated payloads.
- Prime search loop up to a fixed upper bound.

Output includes:
- Hostname
- PHP version
- SAPI type
- ISO8601 timestamp
- Timing per workload
- Total elapsed benchmark time

Lower total runtime indicates better CPU execution performance for this specific mix.

## Requirements

- PHP 8.x recommended
- CLI usage recommended for repeatability

## Usage

Run one cycle of all tests:

```bash
php phpcpubench.php
```

Run multiple cycles:

```bash
php phpcpubench.php 3
```

In web mode, pass runs with query param `n`:

```text
/phpcpubench.php?n=3
```

## Notes

- This is a synthetic benchmark and should not be treated as a full system performance profile.
- Keep the same PHP version and machine state when comparing runs.
- Results are most useful for relative comparisons on the same host over time.

## License

MIT. See `LICENSE`.
