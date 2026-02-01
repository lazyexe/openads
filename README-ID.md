# OpenAds <a href="./README.md">EN</a>

Lightweight & extensible OpenAds untuk Laravel, cocok untuk internal ads, marketplace, CMS, atau monetization platform. Semua metrik (CTR, relevance, landing score) dan biaya klik/view dihitung otomatis berdasarkan performa iklan.

---

## ✨ Fitur Utama

- 🔍 Search Ads (keyword-based)
- 🖼️ Image & 🎥 Video Ads (URL / local / CDN)
- 🎯 Campaign → Ad Group → Ads → Keywords → Assets
- ⏰ Tayang berdasarkan waktu (start_time & end_time, default 24 jam)
- 🌍 Target lokasi: negara / kota (multi, default semua)
- 📱 Target device: android / ios / desktop (multi, default semua)
- 🏆 Auction & ranking (bid × quality score)
- 📊 Tracking impression & click otomatis
- 💰 Biaya klik = bid, biaya view = bid × 20% (configurable)
- 🔄 CTR, relevance, landing score & balance campaign otomatis

---

## 📦 Instalasi

```bash
composer require lazyexe/openads
```

Atau Pengembangan Manual / Lokal

```
"require": {
	"lazyexe/openads": "*"
},
"repositories": [
	{
		"type": "path",
		"url": "OpenAds"
	}
],
```

Publish config & migration:

```bash
php artisan vendor:publish --tag=ads-config
php artisan vendor:publish --tag=ads-migrations
php artisan migrate
```

---

## 🚀 Cara Pakai

### Routes

```php
<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdsClickController;

Route::get('/', [HomeController::class, 'index']);
Route::get('/ads/click/{adId}', [AdsClickController::class, 'click'])->name('ads.click');
```

### HomeController

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAds\Facades\Ads;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q', '');

		$adsCollection = $query ? Ads::search($query)->limit(3) : collect();

        return view('index', [
            'ads' => $adsCollection,
            'query' => $query,
        ]);
    }
}
```

### AdsClickController

```php
use OpenAds\Facades\Ads;

public function click(int $adId)
{
    $url = Ads::logClick($adId);

    if (!$url) abort(404, 'Iklan tidak ditemukan atau saldo tidak mencukupi.');

    return redirect()->away($url);
}
```

### index.blade

```
@if($ads->all())
    @foreach($ads->all() as $ad)
        <div>
            <div>Sponsored</div>
            <h4>{{ $ad->title }}</h4>
            @if(!empty($ad->assets))
                @php $asset = $ad->assets[0]; @endphp
                @if($asset->type === 'image')
                    <img src="{{ $asset->source }}" alt="">
                @elseif($asset->type === 'video')
                    <video controls width="300">
                        <source src="{{ $asset->source }}">
                    </video>
                @endif
            @endif
            <p>
                <a href="{{ route('ads.click', $ad->id) }}" target="_blank">
                    {{ $ad->url }}
                </a>
            </p>
        </div>
    @endforeach
@else
    <p>Tidak ada iklan.</p>
@endif
```

## 🔧 API Reference

| Method                        | Keterangan                                                                   | Return             |                     |
| ----------------------------- | ---------------------------------------------------------------------------- | ------------------ | ------------------- |
| `Ads::search(string $query)`  | Ambil iklan berdasarkan query keyword, otomatis log impression & charge view | `AdCollection`     |                     |
| `AdCollection->all()`         | Ambil semua iklan                                                            | `array` of `AdDTO` |                     |
| `AdCollection->limit(int $n)` | Ambil n iklan terbaik berdasarkan score                                      | `AdCollection`     |                     |
| `Ads::logClick(int $adId)`    | Catat klik & charge campaign sesuai bid                                      | `string            | null` (URL landing) |

## ⚙️ Konfigurasi

`config/ads.php`

```
return [
    'default_platform' => 'search',
    'view_cost_percent' => 0.2, // biaya tayang = bid * 20%
];
```

#### 💡 Catatan

- Ads hanya tampil jika campaign & ad group active, budget > 0, dan berada di rentang tanggal (start_date & end_date).
- CTR, relevance, landing score dihitung otomatis dari data impresi & klik saat query dijalankan.
- Semua biaya dan update performa terjadi langsung saat query dijalankan sehingga data selalu dinamis dan akurat.

## 🗄️ Struktur Database

### Tabel utama

| Tabel             | Fungsi                            |
| ----------------- | --------------------------------- |
| `ads_campaigns`   | Menyimpan campaign & budget       |
| `ads_ad_groups`   | Group keyword & ads               |
| `ads_ads`         | Unit iklan (text / image / video) |
| `ads_keywords`    | Keyword targeting                 |
| `ads_assets`      | Asset iklan (image / video)       |
| `ads_impressions` | Log iklan tampil                  |
| `ads_clicks`      | Log klik iklan                    |

#### Campaign

| Field              | Keterangan                                                     |
| ------------------ | -------------------------------------------------------------- |
| `name`             | Nama campaign                                                  |
| `daily_budget`     | Budget harian                                                  |
| `status`           | active / paused                                                |
| `start_date`       | Mulai campaign                                                 |
| `end_date`         | Akhir campaign                                                 |
| `start_time`       | Jam tayang mulai (nullable = tayang 24 jam)                    |
| `end_time`         | Jam tayang selesai (nullable = tayang 24 jam)                  |
| `target_locations` | JSON: negara/kota target, null = semua lokasi                  |
| `target_devices`   | JSON: device target (android/ios/desktop), null = semua device |

#### Ad Group

| Field         | Keterangan         |
| ------------- | ------------------ |
| `campaign_id` | Relasi ke campaign |
| `name`        | Nama ad group      |
| `default_bid` | Bid default        |
| `status`      | active / paused    |

#### Ads

| Field           | Fungsi             |
| --------------- | ------------------ |
| `ad_group_id`   | Relasi ke ad group |
| `title`         | Judul iklan        |
| `description`   | Deskripsi iklan    |
| `url`           | Landing page       |
| `bid`           | Harga bidding      |
| `ctr`           | Click-through rate |
| `relevance`     | Relevansi          |
| `landing_score` | Skor landing       |
| `status`        | active / paused    |

> ℹ️ Ads **boleh text-only** atau memiliki **image / video asset**

#### Keywords

| Field         | Fungsi                 |
| ------------- | ---------------------- |
| `ad_group_id` | Relasi ke ad group     |
| `keyword`     | Kata kunci             |
| `match_type`  | exact / phrase / broad |
| `negative`    | Exclude keyword        |

### Assets (Image / Video)

| Field        | Fungsi              |
| ------------ | ------------------- |
| `ad_id`      | Relasi ke ads       |
| `type`       | image / video       |
| `source`     | URL atau path local |
| `is_primary` | Asset utama         |

**Source** dapat berupa:

* URL CDN / external
* Local upload (`storage/ads/...`)
* S3 / object storage

<img src="./flow.png" alt="flow" />