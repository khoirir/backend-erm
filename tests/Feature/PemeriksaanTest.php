<?php

namespace Tests\Feature;

use App\Models\PemeriksaanIrj;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class PemeriksaanTest extends TestCase
{
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
            )
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testSimpanPemeriksaanPasienSukses"]);
        PemeriksaanIrj::query()
            ->where("no_rawat", $response['data']['noRawat'])
            ->where('tgl_perawatan', $response['data']['tanggalPerawatan'])
            ->where('jam_rawat', $response['data']['jamPerawatan'])
            ->delete();
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
        PemeriksaanIrj::query()
            ->where("no_rawat", $response['data']['noRawat'])
            ->where('tgl_perawatan', $response['data']['tanggalPerawatan'])
            ->where('jam_rawat', $response['data']['jamPerawatan'])
            ->delete();
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
                    "pesan" => "PEMERIKSAAN PASIEN DENGAN NO. RAWAT 2023/11/14/000002 DAN WAKTU PERAWATAN 2023-11-30 14:11:11 SUDAH DIINPUTKAN"
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
                    "pesan" => "PASIEN DENGAN NO. RAWAT 2023/05/09/000139 TIDAK DITEMUKAN"
                ]
            ])->json();
        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testSimpanPemeriksaanPasienTidakDitemukan"]);
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
                        "beratBadan" => 70,
                        "tinggiBadan" => 170,
                        "tensi" => "156/98",
                        "nadi" => 79,
                        "respirasi" => 20,
                        "instruksi" => "Terapi 2X seminggu",
                        "evaluasi" => "Kontrol, bila ada keluhan",
                        "kesadaran" => "Compos Mentis",
                        "alergi" => "Ibuprofen",
                        "spo2" => 99,
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
            ->assertStatus(404)
            ->assertJson(
                [
                    "error" => [
                        "pesan" => 'URL TIDAK DITEMUKAN'
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

    public function testEditPemeriksaanPasienSukses()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();
        $response = $this->put(
            uri: '/api/irj/pasien/2023-11-14-000002/pemeriksaan/2023-12-01/09:00:00',
            data: [
                "tanggalPerawatan" => "2023-12-01",
                "jamPerawatan" => "09:00:00",
                "keluhan" => "Nyeri dada edit",
                "pemeriksaan" => "Sesak, batuk edit",
                "penilaian" => "Nyeri akut edit",
                "suhuTubuh" => 36,
                "beratBadan" => 70,
                "tinggiBadan" => 170,
                "tensi" => "156/98",
                "nadi" => 79,
                "respirasi" => 20,
                "instruksi" => "Terapi 2X seminggu edit",
                "evaluasi" => "Kontrol, bila ada keluhan edit",
                "kesadaran" => "Compos Mentis",
                "alergi" => "Ibuprofen edit",
                "spo2" => 99,
                "gcs" => "4, 5, 6",
                "tindakLanjut" => "Kaji respon nyeri edit",
                "lingkarPerut" => 100
            ],
            headers: ['Authorization' => $login['data']['token']])
            ->assertStatus(200)
            ->assertJson(
                [
                    "data" => [
                        "noRawat" => "2023/11/14/000002",
                        "tanggalPerawatan" => "2023-12-01",
                        "jamPerawatan" => "09:00:00",
                        "idPemeriksa" => "TEST",
                        "namaPemeriksa" => "TEST",
                        "keluhan" => "Nyeri dada edit",
                        "pemeriksaan" => "Sesak, batuk edit",
                        "penilaian" => "Nyeri akut edit",
                        "suhuTubuh" => 36,
                        "beratBadan" => 70,
                        "tinggiBadan" => 170,
                        "tensi" => "156/98",
                        "nadi" => 79,
                        "respirasi" => 20,
                        "instruksi" => "Terapi 2X seminggu edit",
                        "evaluasi" => "Kontrol, bila ada keluhan edit",
                        "kesadaran" => "Compos Mentis",
                        "alergi" => "Ibuprofen edit",
                        "spo2" => 99,
                        "gcs" => "4, 5, 6",
                        "tindakLanjut" => "Kaji respon nyeri edit",
                        "lingkarPerut" => 100
                    ]
                ]
            )->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testEditPemeriksaanPasienSukses"]);
    }

    public function testEditPemeriksaanPasienDuplikatPrimaryKey()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();
        $response = $this->put(
            uri: '/api/irj/pasien/2023-11-14-000002/pemeriksaan/2023-12-01/09:00:00',
            data: [
                "tanggalPerawatan" => "2023-11-30",
                "jamPerawatan" => "14:11:11",
                "keluhan" => "Nyeri dada edit",
                "pemeriksaan" => "Sesak, batuk edit",
                "penilaian" => "Nyeri akut edit",
                "suhuTubuh" => 36,
                "beratBadan" => 70,
                "tinggiBadan" => 170,
                "tensi" => "156/98",
                "nadi" => 79,
                "respirasi" => 20,
                "instruksi" => "Terapi 2X seminggu",
                "evaluasi" => "Kontrol, bila ada keluhan",
                "kesadaran" => "Compos Mentis",
                "alergi" => "Ibuprofen edit",
                "spo2" => 99,
                "gcs" => "4, 5, 6",
                "tindakLanjut" => "Kaji respon nyeri",
                "lingkarPerut" => 100
            ],
            headers: ['Authorization' => $login['data']['token']])
            ->assertStatus(400)
            ->assertJson(
                [
                    "error" => [
                        "pesan" => "PEMERIKSAAN PASIEN DENGAN NO. RAWAT 2023/11/14/000002 DAN WAKTU PERAWATAN 2023-11-30 14:11:11 SUDAH DIINPUTKAN"
                    ]
                ]
            )->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testEditPemeriksaanPasienDuplikatPrimaryKey"]);
    }

    public function testEditPemeriksaanPasienTidakValid()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();
        $response = $this->put(
            uri: '/api/irj/pasien/2023-11-14-000002/pemeriksaan/2023-12-01/09:00:00',
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
        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testEditPemeriksaanPasienTidakValid"]);
    }

    public function testEditPemeriksaanTidakDitemukan()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();
        $response = $this->put(
            uri: '/api/irj/pasien/2023-11-14-000002/pemeriksaan/2023-12-01/09:30:00',
            data: [
                "tanggalPerawatan" => "2023-12-01",
                "jamPerawatan" => "10:00:00",
                "keluhan" => "Nyeri dada edit",
                "pemeriksaan" => "Sesak, batuk edit",
                "penilaian" => "Nyeri akut edit",
                "suhuTubuh" => 36,
                "beratBadan" => 70,
                "tinggiBadan" => 170,
                "tensi" => "156/98",
                "nadi" => 79,
                "respirasi" => 20,
                "instruksi" => "Terapi 2X seminggu",
                "evaluasi" => "Kontrol, bila ada keluhan",
                "kesadaran" => "Compos Mentis",
                "alergi" => "Ibuprofen edit",
                "spo2" => 99,
                "gcs" => "4, 5, 6",
                "tindakLanjut" => "Kaji respon nyeri",
                "lingkarPerut" => 100
            ],
            headers: ['Authorization' => $login['data']['token']])
            ->assertStatus(404)
            ->assertJson(
                [
                    "error" => [
                        "pesan" => "PEMERIKSAAN PASIEN DENGAN NO. RAWAT 2023/11/14/000002 DAN WAKTU PERAWATAN 2023-12-01 09:30:00 TIDAK DITEMUKAN"
                    ]
                ]
            )->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testEditPemeriksaanTidakDitemukan"]);
    }

    public function testEditPemeriksaanPasienDokterLain()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();
        $response = $this->put(
            uri: '/api/irj/pasien/2023-10-27-000002/pemeriksaan/2023-12-05/14:22:53',
            data: [
                "tanggalPerawatan" => "2023-12-05",
                "jamPerawatan" => "09:00:00",
                "keluhan" => "Nyeri dada edit lagi",
                "pemeriksaan" => "Sesak, batuk edit lagi",
                "penilaian" => "Nyeri akut edit lagi",
                "suhuTubuh" => 36,
                "beratBadan" => 70,
                "tinggiBadan" => 170,
                "tensi" => "156/98",
                "nadi" => 79,
                "respirasi" => 20,
                "instruksi" => "Terapi 2X seminggu",
                "evaluasi" => "Kontrol, bila ada keluhan",
                "kesadaran" => "Compos Mentis",
                "alergi" => "Ibuprofen edit",
                "spo2" => 99,
                "gcs" => "4, 5, 6",
                "tindakLanjut" => "Kaji respon nyeri",
                "lingkarPerut" => 100
            ],
            headers: ['Authorization' => $login['data']['token']])
            ->assertStatus(401)
            ->assertJson(
                [
                    "error" => [
                        "pesan" => "PEMERIKSAAN PASIEN HANYA DAPAT DIEDIT OLEH PEMERIKSA"
                    ]
                ]
            )->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testEditPemeriksaanPasienDokterLain"]);
    }

    public function testHapusPemeriksaanPasienSukses()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();
        $waktuSekarang = strtotime(Carbon::now());
        $post = $this->post(
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
            headers: ['Authorization' => $login['data']['token']])->json();

        $response = $this->delete(
            uri: '/api/irj/pasien/2023-11-14-000002/pemeriksaan/' . $post['data']['tanggalPerawatan'] . '/' . $post['data']['jamPerawatan'],
            headers: ['Authorization' => $login['data']['token']])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "pesan" => "PEMERIKSAAN DIHAPUS"
                ]
            ])->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testHapusPemeriksaanPasienSukses"]);

    }

    public function testHapusPemeriksaanPasienDokterLain()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();
        $response = $this->delete(
            uri: '/api/irj/pasien/2023-10-27-000002/pemeriksaan/2023-12-05/14:22:53',
            headers: ['Authorization' => $login['data']['token']])
            ->assertStatus(401)
            ->assertJson(
                [
                    "error" => [
                        "pesan" => "PEMERIKSAAN PASIEN HANYA DAPAT DIHAPUS OLEH PEMERIKSA"
                    ]
                ]
            )->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testHapusPemeriksaanPasienDokterLain"]);
    }

    public function testListPemeriksaanPasien()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri: '/api/irj/pasien/00164660/pemeriksaan', headers: [
            'Authorization' => $login['data']['token']
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testListPemeriksaanPasien"]);
        self::assertCount(1, $response['data']);
    }

    public function testListPemeriksaanPasienBerdasarkanTanggal()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(
            uri: '/api/irj/pasien/00153605/pemeriksaan?tanggalAwal=2023-11-30&tanggalAkhir=' . date('Y-m-d', strtotime(Carbon::now())),
            headers: [
                'Authorization' => $login['data']['token']
            ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testListPemeriksaanPasienBerdasarkanTanggal"]);
        self::assertCount(4, $response['data']);
        self::assertEquals(4, $response['meta']['total']);
    }

    public function testListPememriksaanPasienBerdasarkanTanggalFormatTidakValid()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri: '/api/irj/pasien/00153605/pemeriksaan?tanggalAwal=14-11-2029&tanggalAkhir=yuig-rt-hh', headers: [
            'Authorization' => $login['data']['token']
        ])->assertStatus(400)
            ->assertJson(
                [
                    "error" => [
                        "pesan" => []
                    ]
                ]
            )->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testListPememriksaanPasienBerdasarkanTanggalFormatTidakValid"]);
    }

    public function testListPemeriksaanPasienBerdasarkanLimit()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri: '/api/irj/pasien/00153605/pemeriksaan?limit=1&halaman=2', headers: [
            'Authorization' => $login['data']['token']
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testListPemeriksaanPasienBerdasarkanLimit"]);
        self::assertCount(1, $response['data']);
        self::assertEquals(2, $response['meta']['halaman']);
        self::assertEquals(1, $response['meta']['limit']);
    }

    public function testListPemeriksaanPasienBerdasarkanLimitTidakValid()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri: '/api/irj/pasien/00153605/pemeriksaan?limit=yu&halaman=iki', headers: [
            'Authorization' => $login['data']['token']
        ])->assertStatus(400)
            ->assertJson(
                [
                    "error" => [
                        "pesan" => []
                    ]
                ]
            )->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testListPemeriksaanPasienBerdasarkanLimitTidakValid"]);
    }

    public function testPencarianPemeriksaanPasien()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(
            uri: '/api/irj/pasien/00153605/pemeriksaan?tanggalAwal=2023-11-30&tanggalAkhir=' . date('Y-m-d', strtotime(Carbon::now())) . '&pencarian=nyeri%20dada',
            headers: ['Authorization' => $login['data']['token']])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testPencarianPemeriksaanPasien"]);

        self::assertCount(4, $response['data']);
        self::assertEquals(4, $response['meta']['total']);
    }

    public function testListPemeriksaanPasienParameterTidakValid()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(
            uri: '/api/irj/pasien/2023-11-14-000002/pemeriksaan',
            headers: ['Authorization' => $login['data']['token']])
            ->assertStatus(405)
            ->assertJson(
                [
                    "error" => [
                        "pesan" => 'METHOD TIDAK DIIZINKAN'
                    ]
                ]
            )->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testListPemeriksaanPasienParameterTidakValid"]);
    }


}
