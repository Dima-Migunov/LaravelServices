# Little Useful Services for Laravel

Every day useful services for Laravel.

### Installing
```bash
composer require migunov/laravel-services
```

### Use
```php
Migunov\Services\ResponseService::json(
        array $data, // Response Data for a client.
        array $params = [], // Any parameters what you want send a client
        int $status = Response::HTTP_OK // the HTTP status
    ): JsonResponse
```
```php
Migunov\Services\Helper::fileExtensionFromMime(string $contentType): string

Migunov\Services\Helper::getMetaTags(string $url): array

Migunov\Services\Helper::host(string $url): string

Migunov\Services\Helper::htmlCut($text, $max_length): string // Dangerous!

Migunov\Services\Helper::httpClient(): PendingRequest

Migunov\Services\Helper::sanitizeData(array $data): array

Migunov\Services\Helper::socialName(string $url): string

Migunov\Services\Helper::stringToArray(
    string $value,
    string $separator = ','
): array

Migunov\Services\Helper::timeFormat(int $seconds): string // Only English
```

```php
Migunov\Services\ImageService::resize(
        string $path, // Storage path to image file.
        int $width = 1200,
        int $height = 600,
        ?string $targetPath = null // Storage path to resized image file or will be replaced $path
    ): void
```