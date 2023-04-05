<?php

namespace App\Console\Commands;

use App\Http\SaloonRequests\GetVCountDataRequest;
use App\Models\Shop;
use App\Models\VisitorCount;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportVisitorData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:visitor-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import visitor count from V-Count and save it in the database.';

    private GetVCountDataRequest $request;

    public function __construct(GetVCountDataRequest $request)
    {
        parent::__construct();

        $this->request = $request;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $response = $this->request->send();

        $data = $response->json();

        $this->saveVisitorData($this->formatVisitorData($data));

        return Command::SUCCESS;
    }

    public function formatVisitorData(array $data): array
    {
        $shops = [];
        $visitorCounts = [];

        foreach ($data as $record) {
            $storeName = $record['store'];
            $timeFormatted = $record['timeformatted'];
            $visitors = $record['in'];

            // Create or update the shop
            if (!isset($shops[$storeName])) {
                $shops[$storeName] = [
                    'name' => $storeName,
                ];
            }

            // Format visitor count record
            $visitorCounts[] = [
                'shop_name' => $storeName,
                'date' => substr($timeFormatted, 0, 10),
                'hour' => (int) substr($timeFormatted, 11, 2),
                'visitors' => $visitors,
            ];
        }

        return [
            'shops' => array_values($shops),
            'visitor_counts' => $visitorCounts,
        ];
    }

    public function saveVisitorData(array $formattedData)
    {
        $shops = $formattedData['shops'];
        $visitorCounts = $formattedData['visitor_counts'];

        DB::transaction(function () use ($shops, $visitorCounts) {
            // Save shop data
            foreach ($shops as $shopData) {
                $shop = Shop::firstOrNew(['name' => $shopData['name']]);
                $shop->fill($shopData);
                $shop->save();

                $shopIdMapping[$shop->name] = $shop->id;
            }

            // Save visitor count data
            foreach ($visitorCounts as $visitorCountData) {
                $attributes = [
                    'shop_id' => $shopIdMapping[$visitorCountData['shop_name']],
                    'date' => $visitorCountData['date'],
                    'hour' => $visitorCountData['hour']
                ];

                $visitorCount = VisitorCount::firstOrNew($attributes);

                // If the visitor count record is new, set the 'visitors' attribute
                if (!$visitorCount->exists) {
                    $visitorCount->visitors = $visitorCountData['visitors'];
                }

                // Save the record only if it's new or has been modified
                if ($visitorCount->isDirty()) {
                    $visitorCount->save();
                }
            }
        });
    }
}
