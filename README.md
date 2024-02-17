# Little Useful Services for Laravel

Every day useful services for Laravel.

**Installing**
```bash
composer require migunov/laravel-services
```

**ResponseService**
Make right answers for API.

```php
Migunov\Services\ResponseService::json(
        array $data, // Response Data for a client.
        array $params = [], // Any parameters what you want send a client
        int $status = Response::HTTP_OK // the HTTP status
    ): JsonResponse
```

*Example of send Response*
```php
use Migunov\Services\ResponseService;

class ProductController extends Controller
{
    public function index(
        ProductsRequest $request,
        ProductsAction $action
    ): JsonResponse {
        $params = $request->validated();

        return ResponseService::json(
            $action->handle($params), // Data for a client.
            ['filters' => 'none'] // Additional paprameters for debugging.
        );
    }
}
```

*Example of response*
```json
{
    "meta": {
        "timestamp": 1708168352,
        "total": 100,
        "count": 45
        "params": {
            "filters": "none"
        }
    },
    "data": [...]
}
```
