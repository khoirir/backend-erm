<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class PemeriksaanTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function setUp(): void
    {
        parent::setUp();
        DB::delete("DELETE FROM user_erm WHERE kd_dokter = 'TEST'");
    }

    public function testSimpanPemeriksaanPasienSukses()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();
        $waktuSekarang = strtotime(Carbon::now());
        $response = $this->post(
            uri: '/api/irj/pasien/2023-11-14-000002/pemeriksaan',
            data: [
                "tanggalPerawatan" => date('Y-m-d', $waktuSekarang),
                "jamPerawatan" => date('H:i:s', $waktuSekarang),
                "keluhan" => "Nyeri dada",
                "pemeriksaan" => "Sesak, batuk",
                "penilaian" => "Nyeri akut",
                "suhuTubuh" => 36,
                "beratBadan" => 60,
                "tinggiBadan" => 160,
                "tensi" => "156/98",
                "nadi" => 79,
                "respirasi" => 20,
                "instruksi" => "Terapi 2X seminggu",
                "evaluasi" => "Kontrol, bila ada keluhan",
                "kesadaran" => "Compos Mentis",
                "alergi" => "Ibuprofen",
                "spo2" => 97,
                "gcs" => "4, 5, 6",
                "tindakLanjut" => "Kaji respon nyeri",
                "lingkarPerut" => 100
            ],
            headers: ['Authorization' => $login['data']['token']])
            ->assertStatus(201)
            ->assertJson(
                [
                    "data" => [
                        "noRawat" => "2023/11/14/000002",
                        "idPemeriksa" => "TEST",
                        "namaPemeriksa" => "TEST",
                        "keluhan" => "Nyeri dada",
                        "pemeriksaan" => "Sesak, batuk",
                        "penilaian" => "Nyeri akut",
                        "suhuTubuh" => 36,
                        "beratBadan" => 60,
                        "tinggiBadan" => 160,
                        "tensi" => "156/98",
                        "nadi" => 79,
                        "respirasi" => 20,
                        "instruksi" => "Terapi 2X seminggu",
                        "evaluasi" => "Kontrol, bila ada keluhan",
                        "kesadaran" => "Compos Mentis",
                        "alergi" => "Ibuprofen",
                        "spo2" => 97,
                        "gcs" => "4, 5, 6",
                        "tindakLanjut" => "Kaji respon nyeri",
                        "lingkarPerut" => 100
                    ]
                ]
            )->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testSimpanPemeriksaanPasienSukses"]);
    }

    public function testSimpanPemeriksaanPasienRujukanSukses()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();
        $waktuSekarang = strtotime(Carbon::now());
        $response = $this->post(
            uri: '/api/irj/pasien/2023-11-17-000003/pemeriksaan',
            data: [
                "tanggalPerawatan" => date('Y-m-d', $waktuSekarang),
                "jamPerawatan" => date('H:i:s', $waktuSekarang),
                "keluhan" => "Nyeri",
                "pemeriksaan" => "Sesak",
                "penilaian" => "Nyeri akut",
                "suhuTubuh" => 38,
                "beratBadan" => 60,
                "tinggiBadan" => 160,
                "tensi" => "156/98",
                "nadi" => 79,
                "respirasi" => 20,
                "instruksi" => "Terapi 2X seminggu",
                "evaluasi" => "Kontrol, bila ada keluhan",
                "kesadaran" => "Somnolence",
                "alergi" => "Ibuprofen",
                "spo2" => 97,
                "gcs" => "4, 5, 6",
                "tindakLanjut" => "Kaji respon nyeri",
                "lingkarPerut" => 100
            ],
            headers: ['Authorization' => $login['data']['token']])
            ->assertStatus(201)
            ->assertJson(
                [
                    "data" => [
                        "noRawat" => "2023/11/17/000003",
                        "idPemeriksa" => "TEST",
                        "namaPemeriksa" => "TEST",
                        "keluhan" => "Nyeri",
                        "pemeriksaan" => "Sesak",
                        "penilaian" => "Nyeri akut",
                        "suhuTubuh" => 38,
                        "beratBadan" => 60,
                        "tinggiBadan" => 160,
                        "tensi" => "156/98",
                        "nadi" => 79,
                        "respirasi" => 20,
                        "instruksi" => "Terapi 2X seminggu",
                        "evaluasi" => "Kontrol, bila ada keluhan",
                        "kesadaran" => "Somnolence",
                        "alergi" => "Ibuprofen",
                        "spo2" => 97,
                        "gcs" => "4, 5, 6",
                        "tindakLanjut" => "Kaji respon nyeri",
                        "lingkarPerut" => 100
                    ]
                ]
            )->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testSimpanPemeriksaanPasienRujukanSukses"]);
    }

    public function testSimpanPemeriksaanPasienDuplikatPrimaryKey()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();
        $response = $this->post(
            uri: '/api/irj/pasien/2023-11-14-000002/pemeriksaan',
            data: [
                "tanggalPerawatan" => "2023-11-30",
                "jamPerawatan" => "14:11:11",
                "keluhan" => "Nyeri dada",
                "pemeriksaan" => "Sesak, batuk",
                "penilaian" => "Nyeri akut",
                "suhuTubuh" => 36,
                "beratBadan" => 60,
                "tinggiBadan" => 160,
                "tensi" => "156/98",
                "nadi" => 79,
                "respirasi" => 20,
                "instruksi" => "Terapi 2X seminggu",
                "evaluasi" => "Kontrol, bila ada keluhan",
                "kesadaran" => "Compos Mentis",
                "alergi" => "Ibuprofen",
                "spo2" => 97,
                "gcs" => "4, 5, 6",
                "tindakLanjut" => "Kaji respon nyeri",
                "lingkarPerut" => 100
            ],
            headers: ['Authorization' => $login['data']['token']])
            ->assertStatus(400)
            ->assertJson([
                "error" => [
                    "pesan"=> "PEMERIKSAAN PASIEN DENGAN NO. RAWAT 2023/11/14/000002 DAN WAKTU PERAWATAN 2023-11-30 14:11:11 SUDAH DIINPUTKAN"
                ]
            ])->json();
        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testSimpanPemeriksaanPasienDuplikatPrimaryKey"]);
    }

    public function testSimpanPemeriksaanPasienTidakDitemukan()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();
        $response = $this->post(
            uri: '/api/irj/pasien/2023-05-09-000139/pemeriksaan',
            data: [
                "tanggalPerawatan" => "2023-11-30",
                "jamPerawatan" => "14:11:11",
                "keluhan" => "Nyeri dada",
                "pemeriksaan" => "Sesak, batuk",
                "penilaian" => "Nyeri akut",
                "suhuTubuh" => 36,
                "beratBadan" => 60,
                "tinggiBadan" => 160,
                "tensi" => "156/98",
                "nadi" => 79,
                "respirasi" => 20,
                "instruksi" => "Terapi 2X seminggu",
                "evaluasi" => "Kontrol, bila ada keluhan",
                "kesadaran" => "Compos Mentis",
                "alergi" => "Ibuprofen",
                "spo2" => 97,
                "gcs" => "4, 5, 6",
                "tindakLanjut" => "Kaji respon nyeri",
                "lingkarPerut" => 100
            ],
            headers: ['Authorization' => $login['data']['token']])
            ->assertStatus(404)
            ->assertJson([
                "error" => [
                    "pesan"=> "PASIEN DENGAN NO. RAWAT 2023/05/09/000139 TIDAK DITEMUKAN"
                ]
            ])->json();
        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testSimpanPemeriksaanPasienDuplikatPrimaryKey"]);
    }

    public function testSimpanPemeriksaanPasienTidakValid()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();
        $response = $this->post(
            uri: '/api/irj/pasien/2023-11-14-000002/pemeriksaan',
            data: [
                "tanggalPerawatan" => "2023-12",
                "jamPerawatan" => "17:05:12",
                "keluhan" => "Nyeri dada",
                "pemeriksaan" => "Sesak, batuk",
                "penilaian" => "",
                "suhuTubuh" => "tiga enam",
                "beratBadan" => "60",
                "tinggiBadan" => "160",
                "tensi" => "156/98",
                "nadi" => 79,
                "respirasi" => 20,
                "instruksi" => "Terapi 2X seminggu",
                "evaluasi" => "Kontrol, bila ada keluhan",
                "kesadaran" => "ComposMentis",
                "alergi" => "Ibuprofen",
                "spo2" => 97,
                "gcs" => "4, 5, 6",
                "lingkarPerut" => "Kaji respon nyeri",
            ],
            headers: ['Authorization' => $login['data']['token']])
            ->assertStatus(400)
            ->assertJson([
                "error" => [
                ]
            ])->json();
        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testSimpanPemeriksaanPasienTidakValid"]);
    }

    public function testDetailPemeriksaanPasienSukses()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(
            uri: '/api/irj/pasien/2023-11-14-000002/pemeriksaan/2023-11-30/14:11:11',
            headers: ['Authorization' => $login['data']['token']])
            ->assertStatus(200)
            ->assertJson(
                [
                    "data" => [
                        "noRawat" => "2023/11/14/000002",
                        "tanggalPerawatan" => "2023-11-30",
                        "jamPerawatan" => "14:11:11",
                        "idPemeriksa" => "TEST",
                        "namaPemeriksa" => "TEST",
                        "keluhan" => "Nyeri dada",
                        "pemeriksaan" => "Sesak, batuk",
                        "penilaian" => "Nyeri akut",
                        "suhuTubuh" => 36,
                        "beratBadan" => 60,
                        "tinggiBadan" => 160,
                        "tensi" => "156/98",
                        "nadi" => 79,
                        "respirasi" => 20,
                        "instruksi" => "Terapi 2X seminggu",
                        "evaluasi" => "Kontrol, bila ada keluhan",
                        "kesadaran" => "Compos Mentis",
                        "alergi" => "Ibuprofen",
                        "spo2" => 97,
                        "gcs" => "4, 5, 6",
                        "tindakLanjut" => "Kaji respon nyeri",
                        "lingkarPerut" => 100
                    ]
                ]
            )->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testDetailPemeriksaanPasienSukses"]);
    }

    public function testDetailPemeriksaanTidakDitemukan()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(
            uri: '/api/irj/pasien/2023-11-14-000002/pemeriksaan/2023-12-01/14:11:11',
            headers: ['Authorization' => $login['data']['token']])
            ->assertStatus(404)
            ->assertJson(
                [
                    "error" => [
                        "pesan" => "PEMERIKSAAN PASIEN DENGAN NO. RAWAT 2023/11/14/000002 DAN WAKTU PERAWATAN 2023-12-01 14:11:11 TIDAK DITEMUKAN"
                    ]
                ]
            )->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testDetailPemeriksaanTidakDitemukan"]);
    }

    public function testDetailPemeriksaanPasienTidakDitemukan()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(
            uri: '/api/irj/pasien/2023-11-14-0000023/pemeriksaan/2023-11-30/14:11:11',
            headers: ['Authorization' => $login['data']['token']])
            ->assertStatus(404)
            ->assertJson(
                [
                    "error" => [
                        "pesan" => "PASIEN DENGAN NO. RAWAT 2023/11/14/0000023 TIDAK DITEMUKAN"
                    ]
                ]
            )->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testDetailPemeriksaanPasienTidakDitemukan"]);
    }

    public function testDetailPemeriksaanPasienDokterLain()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(
            uri: '/api/irj/pasien/2023-05-09-000196/pemeriksaan/2023-05-09/10:25:54',
            headers: ['Authorization' => $login['data']['token']])
            ->assertStatus(404)
            ->assertJson(
                [
                    "error" => [
                        "pesan" => "PASIEN DENGAN NO. RAWAT 2023/05/09/000196 TIDAK DITEMUKAN"
                    ]
                ]
            )->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testDetailPemeriksaanPasienDokterLain"]);
    }

    public function testDetailPemeriksaanPasienParameterTidakValid()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(
            uri: '/api/irj/pasien/2023-11-14-000002/pemeriksaan/2023-11-3/14:1:11',
            headers: ['Authorization' => $login['data']['token']])
            ->assertStatus(400)
            ->assertJson(
                [
                    "error" => [
                        "pesan" => []
                    ]
                ]
            )->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testDetailPemeriksaanPasienParameterTidakValid"]);
    }

    public function testDetailPemeriksaanPasienUnauthorized()
    {
        $response = $this->get(
            uri: '/api/irj/pasien/2023-11-14-000002/pemeriksaan/2023-11-03/14:11:11',
            headers: ['Authorization' => 'token'])
            ->assertStatus(401)
            ->assertJson(
                [
                    "error" => [
                        "pesan" => "UNAUTHORIZED"
                    ]
                ]
            )->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testDetailPemeriksaanPasienUnauthorized"]);
    }


}
