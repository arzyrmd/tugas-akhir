<?php

namespace Database\Seeders;

use App\Models\DeliveryArea;
use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeliveryAreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nonaktifkan pengecekan foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Hapus data dari tabel mapping terlebih dahulu
        DB::table('area_city_mappings')->truncate();

        // Hapus data lama dari delivery areas
        DeliveryArea::truncate();

        // Aktifkan kembali pengecekan foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Buat area JABODETABEK
        $jabodetabek = DeliveryArea::create([
            'name' => 'JABODETABEK',
            'description' => 'Jakarta, Bogor, Depok, Tangerang, dan Bekasi',
            'is_active' => true,
        ]);

        // Kota-kota di JABODETABEK
        $jabodetabekCities = [
            // Jakarta
            'JKTP', // Jakarta Pusat
            'JKTU', // Jakarta Utara
            'JKTB', // Jakarta Barat
            'JKTS', // Jakarta Selatan
            'JKTT', // Jakarta Timur
            'JKTK', // Kepulauan Seribu

            // Bogor
            'KBGR', // Kota Bogor
            'BGR',  // Kabupaten Bogor

            // Depok
            'KDPK', // Kota Depok

            // Tangerang
            'TNG',  // Kabupaten Tangerang
            'KTNG', // Kota Tangerang
            'TGS',  // Tangerang Selatan

            // Bekasi
            'KBKS', // Kota Bekasi
            'BKS',  // Kabupaten Bekasi
        ];

        $this->attachCitiesToArea($jabodetabek->id, $jabodetabekCities);

        // Buat area Jawa Barat (selain JABODETABEK)
        $jabar = DeliveryArea::create([
            'name' => 'Jawa Barat',
            'description' => 'Wilayah Jawa Barat diluar JABODETABEK',
            'is_active' => true,
        ]);

        // Kota-kota di Jawa Barat (selain JABODETABEK)
        $jabarCities = [
            'BDG',  // Kab. Bandung
            'BDB',  // Kab. Bandung Barat
            'BJR',  // Kab. Banjar
            'CMS',  // Kab. Ciamis
            'CJR',  // Kab. Cianjur
            'CRB',  // Kab. Cirebon
            'GRT',  // Kab. Garut
            'IDM',  // Kab. Indramayu
            'KRW',  // Kab. Karawang
            'KNG',  // Kab. Kuningan
            'MJL',  // Kab. Majalengka
            'PGD',  // Kab. Pangandaran
            'PWK',  // Kab. Purwakarta
            'SBG',  // Kab. Subang
            'SKB',  // Kab. Sukabumi
            'SMD',  // Kab. Sumedang
            'TSK',  // Kab. Tasikmalaya
            'KBDG', // Kota Bandung
            'KBJR', // Kota Banjar
            'KCMH', // Kota Cimahi
            'KCRB', // Kota Cirebon
            'KSKB', // Kota Sukabumi
            'KTSK', // Kota Tasikmalaya
        ];

        $this->attachCitiesToArea($jabar->id, $jabarCities);

        // Buat area Jawa Tengah
        $jateng = DeliveryArea::create([
            'name' => 'Jawa Tengah',
            'description' => 'Wilayah Jawa Tengah',
            'is_active' => true,
        ]);

        // Kota-kota di Jawa Tengah
        $jatengCities = [
            'TGL',  // Kab. Tegal (lokasi toko)
            'BNJ',  // Kab. Banjarnegara
            'BYS',  // Kab. Banyumas
            'BTG',  // Kab. Batang
            'BLR',  // Kab. Blora
            'BYL',  // Kab. Boyolali
            'BRB',  // Kab. Brebes
            'CLP',  // Kab. Cilacap
            'DMK',  // Kab. Demak
            'GRB',  // Kab. Grobogan
            'JPR',  // Kab. Jepara
            'KRA',  // Kab. Karanganyar
            'KBM',  // Kab. Kebumen
            'KDL',  // Kab. Kendal
            'KLT',  // Kab. Klaten
            'KDS',  // Kab. Kudus
            'MGL',  // Kab. Magelang
            'PTI',  // Kab. Pati
            'PKL',  // Kab. Pekalongan
            'PML',  // Kab. Pemalang
            'PBG',  // Kab. Purbalingga
            'PWR',  // Kab. Purworejo
            'RMB',  // Kab. Rembang
            'SMG',  // Kab. Semarang
            'SRG',  // Kab. Sragen
            'SKH',  // Kab. Sukoharjo
            'TMG',  // Kab. Temanggung
            'WNG',  // Kab. Wonogiri
            'WNS',  // Kab. Wonosobo
            'KMGL', // Kota Magelang
            'KPKL', // Kota Pekalongan
            'KSTG', // Kota Salatiga
            'KSMG', // Kota Semarang
            'SOL',  // Kota Surakarta
            'KTGL', // Kota Tegal
        ];

        $this->attachCitiesToArea($jateng->id, $jatengCities);

        // Buat area DIY (Yogyakarta)
        $diy = DeliveryArea::create([
            'name' => 'D.I. Yogyakarta',
            'description' => 'Daerah Istimewa Yogyakarta',
            'is_active' => true,
        ]);

        // Kota-kota di DIY
        $diyCities = [
            'BTL',  // Kab. Bantul
            'GKD',  // Kab. Gunungkidul
            'KLP',  // Kab. Kulon Progo
            'SLM',  // Kab. Sleman
            'YOG',  // Kota Yogyakarta
        ];

        $this->attachCitiesToArea($diy->id, $diyCities);

        // Buat area Jawa Timur
        $jatim = DeliveryArea::create([
            'name' => 'Jawa Timur',
            'description' => 'Wilayah Jawa Timur',
            'is_active' => true,
        ]);

        // Kota-kota di Jawa Timur
        $jatimCities = [
            'BKL',  // Kab. Bangkalan
            'BYW',  // Kab. Banyuwangi
            'BLT',  // Kab. Blitar
            'BJN',  // Kab. Bojonegoro
            'BDW',  // Kab. Bondowoso
            'GRS',  // Kab. Gresik
            'JMB',  // Kab. Jember
            'JBG',  // Kab. Jombang
            'KDR',  // Kab. Kediri
            'LMG',  // Kab. Lamongan
            'LMJ',  // Kab. Lumajang
            'MDN',  // Kab. Madiun
            'MGT',  // Kab. Magetan
            'MLG',  // Kab. Malang
            'MJK',  // Kab. Mojokerto
            'NGJ',  // Kab. Nganjuk
            'NGW',  // Kab. Ngawi
            'PCT',  // Kab. Pacitan
            'PMK',  // Kab. Pamekasan
            'PSR',  // Kab. Pasuruan
            'PNR',  // Kab. Ponorogo
            'PRB',  // Kab. Probolinggo
            'SPG',  // Kab. Sampang
            'SDJ',  // Kab. Sidoarjo
            'STB',  // Kab. Situbondo
            'SMN',  // Kab. Sumenep
            'TRG',  // Kab. Trenggalek
            'TBN',  // Kab. Tuban
            'TLG',  // Kab. Tulungagung
            'KBTU', // Kota Batu
            'KBLT', // Kota Blitar
            'KKDR', // Kota Kediri
            'KMDN', // Kota Madiun
            'KMLG', // Kota Malang
            'KMJK', // Kota Mojokerto
            'KPSR', // Kota Pasuruan
            'KPRB', // Kota Probolinggo
            'SBY',  // Kota Surabaya
        ];

        $this->attachCitiesToArea($jatim->id, $jatimCities);

        // Buat area Banten (selain Tangerang)
        $banten = DeliveryArea::create([
            'name' => 'Banten',
            'description' => 'Wilayah Banten diluar Tangerang',
            'is_active' => true,
        ]);

        // Kota-kota di Banten (selain Tangerang)
        $bantenCities = [
            'LBK',  // Kab. Lebak
            'PDG',  // Kab. Pandeglang
            'SER',  // Kab. Serang
            'CLG',  // Kota Cilegon
            'KSRG', // Kota Serang
        ];

        $this->attachCitiesToArea($banten->id, $bantenCities);

        // Buat area Brebes-Tegal dan sekitarnya
        $brebesTegal = DeliveryArea::create([
            'name' => 'Brebes-Tegal dan Sekitarnya',
            'description' => 'Wilayah Brebes, Tegal dan kota-kota sekitarnya',
            'is_active' => true,
        ]);

        // Kota-kota di sekitar Brebes-Tegal
        $brebesTegalCities = [
            'TGL',  // Kab. Tegal
            'KTGL', // Kota Tegal
            'BRB',  // Kab. Brebes
            'PML',  // Kab. Pemalang
            'PKL',  // Kab. Pekalongan
            'KPKL', // Kota Pekalongan
            'BTG',  // Kab. Batang
        ];

        $this->attachCitiesToArea($brebesTegal->id, $brebesTegalCities);

        // Buat area Pekalongan dan sekitarnya
        $pekalonganArea = DeliveryArea::create([
            'name' => 'Pekalongan dan Sekitarnya',
            'description' => 'Wilayah Pekalongan dan kota-kota sekitarnya',
            'is_active' => true,
        ]);

        // Kota-kota di sekitar Pekalongan
        $pekalonganCities = [
            'PKL',  // Kab. Pekalongan
            'KPKL', // Kota Pekalongan
            'BTG',  // Kab. Batang
            'PML',  // Kab. Pemalang
            'KDL',  // Kab. Kendal
        ];

        $this->attachCitiesToArea($pekalonganArea->id, $pekalonganCities);

        // Buat area Banyumas dan sekitarnya
        $banyumasArea = DeliveryArea::create([
            'name' => 'Banyumas dan Sekitarnya',
            'description' => 'Wilayah Banyumas, Cilacap dan sekitarnya',
            'is_active' => true,
        ]);

        // Kota-kota di sekitar Banyumas
        $banyumasCities = [
            'BYS',  // Kab. Banyumas
            'PBG',  // Kab. Purbalingga
            'BNJ',  // Kab. Banjarnegara
            'CLP',  // Kab. Cilacap
            'KBM',  // Kab. Kebumen
        ];

        $this->attachCitiesToArea($banyumasArea->id, $banyumasCities);

        // Buat area Semarang dan sekitarnya
        $semarangArea = DeliveryArea::create([
            'name' => 'Semarang dan Sekitarnya',
            'description' => 'Wilayah Semarang dan kota-kota sekitarnya',
            'is_active' => true,
        ]);

        // Kota-kota di sekitar Semarang
        $semarangCities = [
            'KSMG', // Kota Semarang
            'SMG',  // Kab. Semarang
            'KDL',  // Kab. Kendal
            'DMK',  // Kab. Demak
            'KSTG', // Kota Salatiga
        ];

        $this->attachCitiesToArea($semarangArea->id, $semarangCities);
    }

    /**
     * Menghubungkan kota-kota ke area pengiriman
     *
     * @param int $areaId
     * @param array $cityCodes
     * @return void
     */
    private function attachCitiesToArea($areaId, $cityCodes)
    {
        foreach ($cityCodes as $code) {
            $city = City::where('code', $code)->first();
            if ($city) {
                DB::table('area_city_mappings')->insert([
                    'delivery_area_id' => $areaId,
                    'city_id' => $city->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
