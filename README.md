# Laravel OpenObserve

[![Latest Version on Packagist](https://img.shields.io/packagist/v/minhyung/laravel-openobserve.svg?style=flat-square)](https://packagist.org/packages/minhyung/laravel-openobserve)
[![Total Downloads](https://img.shields.io/packagist/dt/minhyung/laravel-openobserve.svg?style=flat-square)](https://packagist.org/packages/minhyung/laravel-openobserve)

OpenObserve를 Laravel 애플리케이션에 통합하기 위한 패키지입니다. 로그를 OpenObserve로 전송하여 중앙 집중식 로그 관리 및 모니터링을 제공합니다.

## 기능

- Laravel 로깅 시스템과 완벽한 통합
- 배치 처리를 통한 효율적인 로그 전송
- 설정 가능한 추가 필드
- Facade를 통한 직접 API 접근
- 예외 정보 자동 포함

## 요구사항

- PHP 8.1 이상
- Laravel 11.x 또는 12.x
- cURL 확장

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
OPENOBSERVE_BATCH_SIZE=100
OPENOBSERVE_TIMEOUT=5
OPENOBSERVE_SSL_VERIFY=true
```

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

### 1. Laravel 로깅 시스템 사용

일반적인 Laravel 로깅 방식으로 사용할 수 있습니다:

```php
use Illuminate\Support\Facades\Log;

Log::info('사용자 로그인', ['user_id' => 123]);
Log::error('오류 발생', ['error' => $exception->getMessage()]);
Log::warning('경고 메시지');
Log::debug('디버그 정보', ['data' => $debugData]);
```

### 2. Facade를 통한 직접 사용

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

// 배치에 추가 (자동으로 배치 크기에 도달하면 전송)
OpenObserve::addToBatch([
	'level' => 'info',
	'message' => '이벤트 발생',
]);

// 수동으로 배치 플러시
OpenObserve::flush();

// 연결 테스트
if (OpenObserve::testConnection()) {
	echo 'OpenObserve 연결 성공!';
}
```

### 3. 의존성 주입 사용

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

## 설정 옵션

`config/openobserve.php` 파일에서 다음 옵션을 설정할 수 있습니다:

| 옵션 | 설명 | 기본값 |
|------|------|--------|
| `enabled` | OpenObserve 로깅 활성화 여부 | `false` |
| `url` | OpenObserve 인스턴스 URL | `http://localhost:5080` |
| `organization` | OpenObserve 조직 이름 | `default` |
| `stream` | 로그 스트림 이름 | `default` |
| `auth.username` | 인증 사용자 이름 | - |
| `auth.password` | 인증 비밀번호 | - |
| `batch_size` | 배치 전송 크기 | `100` |
| `timeout` | HTTP 요청 타임아웃 (초) | `5` |
| `ssl_verify` | SSL 인증서 검증 여부 | `true` |
| `additional_fields` | 모든 로그에 추가될 필드 | `['environment', 'application']` |

## 테스트

```bash
composer test
```

또는 Pest를 직접 실행:

```bash
./vendor/bin/pest
```

## 보안 취약점

보안 취약점을 발견한 경우 urlinee@gmail.com으로 이메일을 보내주세요.

## 라이선스

MIT 라이선스. 자세한 내용은 [License File](LICENSE)을 참조하세요.

## 크레딧

- [Minhyung Park](https://github.com/minhyung)
- [All Contributors](../../contributors)

## 지원

문제가 발생하거나 기능 요청이 있는 경우 [GitHub Issues](https://github.com/minhyung/laravel-openobserve/issues)를 통해 알려주세요.
