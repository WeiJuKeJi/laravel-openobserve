# Laravel OpenObserve

[![Tests](https://github.com/minhyung/laravel-openobserve/actions/workflows/tests.yml/badge.svg?branch=0.x)](https://github.com/minhyung/laravel-openobserve/actions/workflows/tests.yml)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/minhyung/laravel-openobserve.svg?style=flat-square)](https://packagist.org/packages/minhyung/laravel-openobserve)
[![Total Downloads](https://img.shields.io/packagist/dt/minhyung/laravel-openobserve.svg?style=flat-square)](https://packagist.org/packages/minhyung/laravel-openobserve)

[OpenObserve](https://openobserve.ai)를 Laravel 애플리케이션에 통합하기 위한 패키지입니다. 로그를 OpenObserve로 전송하여 중앙 집중식 로그 관리 및 모니터링을 제공합니다.

## 기능

- Laravel 로깅 시스템과 완벽한 통합
- 배치 처리를 통한 효율적인 로그 전송
- 설정 가능한 추가 필드
- Facade를 통한 직접 API 접근
- 예외 정보 자동 캡처 (클래스명, 메시지, 코드, 파일, 라인, 스택트레이스)
- Artisan 커맨드를 통한 연결 테스트

## 요구사항

- PHP 8.3 이상
- Laravel 11.x 또는 12.x

## 설치

Composer를 통해 패키지를 설치합니다:

```bash
composer require minhyung/laravel-openobserve
```

설정 파일을 퍼블리시합니다:

```bash
php artisan vendor:publish --tag=openobserve-config
```

## 설정

`.env` 파일에 OpenObserve 연결 정보를 추가합니다:

```env
OPENOBSERVE_ENABLED=true
OPENOBSERVE_URL=http://localhost:5080
OPENOBSERVE_ORGANIZATION=default
OPENOBSERVE_STREAM=laravel-logs
OPENOBSERVE_USERNAME=your-email@example.com
OPENOBSERVE_PASSWORD=your-password
```

### 전체 설정 옵션

| 옵션 | 환경변수 | 기본값 |
|------|---------|--------|
| `enabled` | `OPENOBSERVE_ENABLED` | `false` |
| `url` | `OPENOBSERVE_URL` | `http://localhost:5080` |
| `organization` | `OPENOBSERVE_ORGANIZATION` | `default` |
| `stream` | `OPENOBSERVE_STREAM` | `default` |
| `auth.username` | `OPENOBSERVE_USERNAME` | - |
| `auth.password` | `OPENOBSERVE_PASSWORD` | - |
| `batch_size` | `OPENOBSERVE_BATCH_SIZE` | `100` |
| `timeout` | `OPENOBSERVE_TIMEOUT` | `5` |
| `ssl_verify` | `OPENOBSERVE_SSL_VERIFY` | `true` |
| `additional_fields` | `APP_ENV`, `APP_NAME` | `['environment', 'application']` |

### Laravel 로깅 채널 설정

`config/logging.php` 파일에 OpenObserve 채널을 추가합니다:

```php
'channels' => [
    // ... 기존 채널들

    'openobserve' => [
        'driver' => 'custom',
        'via' => \Minhyung\LaravelOpenObserve\Logging\OpenObserveLogger::class,
        'level' => env('LOG_LEVEL', 'debug'),
        'name' => 'openobserve',
    ],

    // 스택 채널에 openobserve 추가 (선택사항)
    'stack' => [
        'driver' => 'stack',
        'channels' => ['single', 'openobserve'],
        'ignore_exceptions' => false,
    ],
],
```

`.env` 파일에서 기본 로그 채널을 설정합니다:

```env
LOG_CHANNEL=stack  # 또는 'openobserve'
```

## 사용법

### Laravel 로깅

일반적인 Laravel 로깅 방식으로 사용할 수 있습니다:

```php
use Illuminate\Support\Facades\Log;

Log::info('사용자 로그인', ['user_id' => 123]);
Log::error('오류 발생', ['error' => $exception->getMessage()]);
Log::warning('경고 메시지');
Log::debug('디버그 정보', ['data' => $debugData]);
```

### Facade를 통한 직접 사용

Facade를 통해 OpenObserve 클라이언트를 직접 사용할 수 있습니다:

```php
use Minhyung\LaravelOpenObserve\Facades\OpenObserve;

// 단일 로그 전송
OpenObserve::send([
    'level' => 'info',
    'message' => '사용자 액션',
    'user_id' => 123,
    'action' => 'purchase',
]);

// 배치에 추가 (배치 크기에 도달하면 자동 전송)
OpenObserve::addToBatch([
    'level' => 'info',
    'message' => '이벤트 발생',
]);

// 수동으로 배치 플러시
OpenObserve::flush();
```

### 의존성 주입

```php
use Minhyung\LaravelOpenObserve\OpenObserveClient;

class SomeController extends Controller
{
    public function __construct(
        private OpenObserveClient $openObserve
    ) {}

    public function index()
    {
        $this->openObserve->send([
            'level' => 'info',
            'message' => '컨트롤러 실행',
            'controller' => self::class,
        ]);
    }
}
```

### 연결 테스트

Artisan 커맨드로 OpenObserve 연결을 테스트할 수 있습니다:

```bash
php artisan openobserve:test
```

설정 정보를 표시하고 테스트 로그를 전송하여 연결 상태를 확인합니다.

## 테스트

```bash
composer test
```

## 보안 취약점

보안 취약점을 발견한 경우 urlinee@gmail.com으로 이메일을 보내주세요.

## 라이선스

MIT 라이선스. 자세한 내용은 [License File](LICENSE)을 참조하세요.

## 크레딧

- [Minhyung Park](https://github.com/overworks)
- [All Contributors](../../contributors)
