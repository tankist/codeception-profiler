# Codeception profiler

Codeception extension used to profile unit tests

[![Code Climate](https://codeclimate.com/github/tankist/codeception-profiler/badges/gpa.svg)](https://codeclimate.com/github/tankist/codeception-profiler) [![Test Coverage](https://codeclimate.com/github/tankist/codeception-profiler/badges/coverage.svg)](https://codeclimate.com/github/tankist/codeception-profiler/coverage) [![Issue Count](https://codeclimate.com/github/tankist/codeception-profiler/badges/issue_count.svg)](https://codeclimate.com/github/tankist/codeception-profiler)

## Minimum Requirements

- Codeception 2.1.0
- PHP 5.4

## Installation using [Composer](https://getcomposer.org)

```bash
$ composer require tankist/codeception-profiler
```

Be sure to enable the extension in `codeception.yml` as shown in
[configuration](#configuration) below.

## Configuration

All enabling and configuration is done in `codeception.yml`.

### Enabling Profiler with defaults

```yaml
extensions:
    enabled:
        - Codeception\Extension\Profiler
```

### Enabling Profiler with alternate settings

```yaml
extensions:
    enabled:
        - Codeception\Extension\Profiler
    config:
        Codeception\Extension\Profiler:
            warningTimeLimit: 5
            errorTimeLimit: 30
```

### Available options

- `warningTimeLimit: {warningTimeLimit}`
    - If test execution time will exceed this limit test will be marked as `warning` (yellow)
    - Default: `1`
- `errorTimeLimit: {errorTimeLimit}`
    - If test execution time will exceed this limit test will be marked as `error` (red)
    - Default: `10`
