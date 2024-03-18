<?php

namespace App\Console\Commands;

use App\Models\Mint;
use App\Models\Mintpass;
use Illuminate\Console\Command;
use Storage;

class MintPassNft extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mint:pass';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Nfts for Mintpass';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $mintpasses = Mintpass::query()
            ->withCount(['mints as freeMints' => fn ($query) => $query->where('txhash', null)])
            ->get();
        $total = Mint::query()->whereHasMorph(
            'mintable',
            [Mintpass::class]
        )
            ->whereNull('txhash')
            ->latest()
            ->take(5)
            ->get()->max('tokenId');

        foreach ($mintpasses as $mintpass) {
            if ($mintpass->freeMints > 10) continue;
            for (
                $i = $total + 1;
                $i < $total + 51;
                $i++
            ) {
                $metaUrl = "mintpass/metadata/{$i}.json";
                if (Storage::disk('nft')->fileExists($metaUrl))
                    continue;
                $data = [
                    'name' => $mintpass->name,
                    'tokenId' => $i,
                    'description' =>  $mintpass->description,
                    'external_url' =>  $mintpass->external_url,
                    'image' => $mintpass->image,
                    'attributes' => collect($mintpass->attributes)
                        ->filter(fn ($attr) => !!$attr['value'])
                        ->all()
                ];
                $metadata = json_encode($data);
                Storage::disk('nft')->put($metaUrl, $metadata, ['ACL' => 'public-read']);
                $mintpass->mints()->updateOrCreate([
                    'tokenId' => $i,
                    'chain_id' => $mintpass->chain_id,
                ], [
                    'chainId' => $mintpass->chainId,
                ]);
            }
        }
    }
}
