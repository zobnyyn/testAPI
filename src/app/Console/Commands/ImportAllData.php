<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ImportAllData extends Command
{
    protected $signature = 'import:all {--dateFrom=} {--dateTo=}';
    protected $description = 'Импортирует все данные из внешнего API (sales, orders, stocks, incomes)';

    public function handle()
    {
        $host = config('services.api_importer.host');
        $key = config('services.api_importer.key');
        $dateFrom = $this->option('dateFrom') ?? Carbon::now()->toDateString();
        $dateTo = $this->option('dateTo') ?? Carbon::now()->toDateString();
        $success = 0;
        $failed = 0;
        $this->importEntity('sales', '/api/sales', [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ], $success, $failed);
        $this->importEntity('orders', '/api/orders', [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ], $success, $failed);
        $this->importEntity('stocks', '/api/stocks', [
            'dateFrom' => Carbon::now()->toDateString(),
        ], $success, $failed);
        $this->importEntity('incomes', '/api/incomes', [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ], $success, $failed);
        $this->info("Успешно импортировано: $success, ошибок: $failed");
    }

    protected function importEntity($table, $endpoint, $params, &$success = 0, &$failed = 0)
    {
        $host = config('services.api_importer.host');
        $key = config('services.api_importer.key');
        $page = 1;
        $limit = 500;
        do {
            $query = array_merge($params, [
                'page' => $page,
                'limit' => $limit,
                'key' => $key,
            ]);
            $response = Http::get($host . $endpoint, $query);
            if (!$response->ok()) {
                $this->error("Ошибка запроса к $endpoint: " . $response->body());
                Log::channel('import_errors')->error("API error for $endpoint: " . $response->body());
                $failed++;
                break;
            }
            $data = $response->json('data') ?? $response->json();
            if (!$data || !is_array($data)) break;
            foreach ($data as $item) {
                // Пример простой валидации: id или ключевое поле должно быть
                if (!isset($item['id']) && !isset($item['income_id']) && !isset($item['g_number'])) {
                    $failed++;
                    Log::channel('import_errors')->warning("Пропущена запись без ключевого поля: ", $item);
                    continue;
                }
                try {
                    DB::table($table)->updateOrInsert(
                        ['id' => $item['id'] ?? null],
                        $item
                    );
                    $success++;
                } catch (\Throwable $e) {
                    $failed++;
                    Log::channel('import_errors')->error("Ошибка сохранения в $table: " . $e->getMessage(), $item);
                }
            }
            $this->info("Импортировано " . count($data) . " записей в $table (страница $page)");
            $page++;
        } while (count($data) === $limit);
    }
}
