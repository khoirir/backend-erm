<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class PasienTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        DB::delete("DELETE FROM user_erm WHERE kd_dokter = 'TEST'");
    }

    public function testGetDetailPasienSukses()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri: '/api/irj/pasien/2023-11-14-000002', headers: [
            'Authorization' => $login['data']['token']
        ])->assertStatus(200)
            ->assertJson(
                [
                    "data" => [
                        "noRawat" => "2023/11/14/000002"
                    ]
                ]
            )->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testGetDetailPasienSukses"]);
    }

    public function testGetDetailPasienUnauthorized()
    {
        $response = $this->get(uri: '/api/irj/pasien/2023-11-14-000002', headers: [
            'Authorization' => ''
        ])->assertStatus(401)
            ->assertJson(
                [
                    "error" => [
                        "pesan" => "UNAUTHORIZED"
                    ]
                ]
            )->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testGetDetailPasienUnauthorized"]);
    }

    public function testGetDetailPasienDokterLain()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri: '/api/irj/pasien/2023-10-06-000004', headers: [
            'Authorization' => $login['data']['token']
        ])->assertStatus(404)
            ->assertJson(
                [
                    "error" => [
                        "pesan" => "PASIEN DENGAN NO. RAWAT 2023/10/06/000004 TIDAK DITEMUKAN"
                    ]
                ]
            )->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testGetDetailPasienDokterLain"]);
    }

    public function testGetDetailPasienNoRawatTidakDitemukan()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri: '/api/irj/pasien/2023-11-14-000803', headers: [
            'Authorization' => $login['data']['token']
        ])->assertStatus(404)
            ->assertJson(
                [
                    "error" => [
                        "pesan" => "PASIEN DENGAN NO. RAWAT 2023/11/14/000803 TIDAK DITEMUKAN"
                    ]
                ]
            )->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testGetDetailPasienNoRawatTidakDitemukan"]);
    }

    public function testListPasienPerDokter()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri: '/api/irj/pasien', headers: [
            'Authorization' => $login['data']['token']
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testListPasienPerDokter"]);
        self::assertCount(4, $response['data']);
    }

    public function testListPasienUnauthorized()
    {
        $response = $this->get(uri: '/api/irj/pasien', headers: [
            'Authorization' => ''
        ])->assertStatus(401)
            ->assertJson(
                [
                    "error" => [
                        "pesan" => "UNAUTHORIZED"
                    ]
                ]
            )
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testListPasienUnauthorized"]);
    }

    public function testListPasienPerDokterBerdasarkanTanggal()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(
            uri: '/api/irj/pasien?tanggalAwal=2023-11-14&tanggalAkhir='.date('Y-m-d', strtotime(Carbon::now())),
            headers: [
                'Authorization' => $login['data']['token']
            ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testListPasienPerDokterBerdasarkanTanggal"]);
        self::assertCount(4, $response['data']);
        self::assertEquals(4, $response['meta']['total']);
    }

    public function testListPasienPerDokterBerdasarkanTanggalFormatTidakValid()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri: '/api/irj/pasien?tanggalAwal=14-11-2029&tanggalAkhir=yuig-rt-hh', headers: [
            'Authorization' => $login['data']['token']
        ])->assertStatus(400)
            ->assertJson(
                [
                    "error" => [
                        "pesan" => []
                    ]
                ]
            )->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testListPasienPerDokterBerdasarkanTanggalFormatTidakValid"]);
    }

    public function testListPasienPerDokterBerdasarkanLimit()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri: '/api/irj/pasien?limit=1&halaman=1', headers: [
            'Authorization' => $login['data']['token']
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testListPasienPerDokterBerdasarkanLimit"]);
        self::assertCount(1, $response['data']);
        self::assertEquals(1, $response['meta']['halaman']);
        self::assertEquals(1, $response['meta']['limit']);
    }

    public function testListPasienPerDokterBerdasarkanLimitTidakValid()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri: '/api/irj/pasien?limit=yu&halaman=iki', headers: [
            'Authorization' => $login['data']['token']
        ])->assertStatus(400)
            ->assertJson(
                [
                    "error" => [
                        "pesan" => []
                    ]
                ]
            )->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testListPasienPerDokterBerdasarkanLimitTidakValid"]);
    }

    public function testPencarianPasienBerdasarkanNama()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri: '/api/irj/pasien?pencarian=khoir', headers: [
            'Authorization' => $login['data']['token']
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testPencarianPasienBerdasarkanNama"]);

        self::assertCount(1, $response['data']);
        self::assertEquals(1, $response['meta']['total']);
    }

    public function testPencarianPasienBerdasarkanNoRawat()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri: '/api/irj/pasien?pencarian=2023/11/14/000001', headers: [
            'Authorization' => $login['data']['token']
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testPencarianPasienBerdasarkanNoRawat"]);

        self::assertCount(1, $response['data']);
        self::assertEquals(1, $response['meta']['total']);
    }

    public function testPencarianPasienBerdasarkanNoRM()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri: '/api/irj/pasien?pencarian=123456', headers: [
            'Authorization' => $login['data']['token']
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testPencarianPasienBerdasarkanNoRM"]);

        self::assertCount(1, $response['data']);
        self::assertEquals(1, $response['meta']['total']);
    }

    public function testPencarianPasienBerdasarkanAlamat()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri: '/api/irj/pasien?pencarian=PUCANG', headers: [
            'Authorization' => $login['data']['token']
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testPencarianPasienBerdasarkanAlamat"]);

        self::assertCount(1, $response['data']);
        self::assertEquals(1, $response['meta']['total']);
    }

    public function testPencarianPasienDokterLain()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri: '/api/irj/pasien?pencarian=2023/10/17/000001', headers: [
            'Authorization' => $login['data']['token']
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testPencarianPasienDokterLain"]);

        self::assertCount(0, $response['data']);
        self::assertEquals(0, $response['meta']['total']);
    }

    public function testPencarianPasienParameterKurangDariTiga()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri: '/api/irj/pasien?pencarian=12', headers: [
            'Authorization' => $login['data']['token']
        ])->assertStatus(400)
            ->assertJson(
                [
                    "error" => [
                        "pesan" => []
                    ]
                ]
            )->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testPencarianPasienParameterKurangDariTiga"]);
    }


    public function testGetDetailPasienRujukanSukses()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri: '/api/irj/pasien-rujukan/2023-11-17-000003', headers: [
            'Authorization' => $login['data']['token']
        ])->assertStatus(200)
            ->assertJson(
                [
                    "data" => [
                        "noRawat" => "2023/11/17/000003"
                    ]
                ]
            )->json();

        self::assertNotNull($response['data']['dokterAsal']);
        self::assertNotNull($response['data']['poliAsal']);

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testGetDetailPasienRujukanSukses"]);
    }

    public function testGetDetailPasienRujukanUnauthorized()
    {
        $response = $this->get(uri: '/api/irj/pasien-rujukan/2023-11-17-000003', headers: [
            'Authorization' => ''
        ])->assertStatus(401)
            ->assertJson(
                [
                    "error" => [
                        "pesan" => "UNAUTHORIZED"
                    ]
                ]
            )->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testGetDetailPasienRujukanUnauthorized"]);
    }

    public function testGetDetailPasienRujukanDokterLain()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri: '/api/irj/pasien-rujukan/2023-10-27-000002', headers: [
            'Authorization' => $login['data']['token']
        ])->assertStatus(404)
            ->assertJson(
                [
                    "error" => [
                        "pesan" => "PASIEN RUJUKAN DENGAN NO. RAWAT 2023/10/27/000002 TIDAK DITEMUKAN"
                    ]
                ]
            )->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testGetDetailPasienRujukanDokterLain"]);
    }

    public function testGetDetailPasienRujukanNoRawatTidakDitemukan()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri: '/api/irj/pasien-rujukan/2023-11-14-000001', headers: [
            'Authorization' => $login['data']['token']
        ])->assertStatus(404)
            ->assertJson(
                [
                    "error" => [
                        "pesan" => "PASIEN RUJUKAN DENGAN NO. RAWAT 2023/11/14/000001 TIDAK DITEMUKAN"
                    ]
                ]
            )->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testGetDetailPasienRujukanNoRawatTidakDitemukan"]);
    }

    public function testListPasienRujukanPerDokter()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri: '/api/irj/pasien-rujukan', headers: [
            'Authorization' => $login['data']['token']
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testListPasienRujukanPerDokter"]);
        self::assertCount(2, $response['data']);
    }

    public function testListPasienRujukanUnauthorized()
    {
        $response = $this->get(uri: '/api/irj/pasien-rujukan', headers: [
            'Authorization' => ''
        ])->assertStatus(401)
            ->assertJson(
                [
                    "error" => [
                        "pesan" => "UNAUTHORIZED"
                    ]
                ]
            )
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testListPasienRujukanUnauthorized"]);
    }

    public function testListPasienRujukanPerDokterBerdasarkanTanggal()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(
            uri: '/api/irj/pasien-rujukan?tanggalAwal=2023-11-14&tanggalAkhir='.date('Y-m-d', strtotime(Carbon::now())),
            headers: [
                'Authorization' => $login['data']['token']
            ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testListPasienRujukanPerDokterBerdasarkanTanggal"]);
        self::assertCount(2, $response['data']);
        self::assertEquals(2, $response['meta']['total']);
    }

    public function testListPasienRujukanPerDokterBerdasarkanTanggalFormatTidakValid()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri: '/api/irj/pasien-rujukan?tanggalAwal=14-11-2029&tanggalAkhir=yuig-rt-hh', headers: [
            'Authorization' => $login['data']['token']
        ])->assertStatus(400)
            ->assertJson(
                [
                    "error" => [
                        "pesan" => []
                    ]
                ]
            )->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testListPasienRujukanPerDokterBerdasarkanTanggalFormatTidakValid"]);
    }

    public function testListPasienRujukanPerDokterBerdasarkanLimit()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri: '/api/irj/pasien-rujukan?limit=1&halaman=1', headers: [
            'Authorization' => $login['data']['token']
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testListPasienRujukanPerDokterBerdasarkanLimit"]);
        self::assertCount(1, $response['data']);
        self::assertEquals(1, $response['meta']['halaman']);
        self::assertEquals(1, $response['meta']['limit']);
    }

    public function testListPasienRujukanPerDokterBerdasarkanLimitTidakValid()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri: '/api/irj/pasien-rujukan?limit=yu&halaman=iki', headers: [
            'Authorization' => $login['data']['token']
        ])->assertStatus(400)
            ->assertJson(
                [
                    "error" => [
                        "pesan" => []
                    ]
                ]
            )->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testListPasienRujukanPerDokterBerdasarkanLimitTidakValid"]);
    }

    public function testPencarianPasienRujukanBerdasarkanNama()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri: '/api/irj/pasien-rujukan?pencarian=test', headers: [
            'Authorization' => $login['data']['token']
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testPencarianPasienRujukanBerdasarkanNama"]);

        self::assertCount(1, $response['data']);
        self::assertEquals(1, $response['meta']['total']);
    }

    public function testPencarianPasienRujukanBerdasarkanNoRawat()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri: '/api/irj/pasien-rujukan?pencarian=2023/11/20/000004', headers: [
            'Authorization' => $login['data']['token']
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testPencarianPasienRujukanBerdasarkanNoRawat"]);

        self::assertCount(1, $response['data']);
        self::assertEquals(1, $response['meta']['total']);
    }

    public function testPencarianPasienRujukanBerdasarkanNoRM()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri: '/api/irj/pasien-rujukan?pencarian=149348', headers: [
            'Authorization' => $login['data']['token']
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testPencarianPasienRujukanBerdasarkanNoRM"]);

        self::assertCount(1, $response['data']);
        self::assertEquals(1, $response['meta']['total']);
    }

    public function testPencarianPasienRujukanBerdasarkanAlamat()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri: '/api/irj/pasien-rujukan?pencarian=tondano', headers: [
            'Authorization' => $login['data']['token']
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testPencarianPasienRujukanBerdasarkanAlamat"]);

        self::assertCount(1, $response['data']);
        self::assertEquals(1, $response['meta']['total']);
    }

    public function testPencarianPasienRujukanDokterLain()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri: '/api/irj/pasien-rujukan?pencarian=2023/10/27/000002', headers: [
            'Authorization' => $login['data']['token']
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testPencarianPasienRujukanDokterLain"]);

        self::assertCount(0, $response['data']);
        self::assertEquals(0, $response['meta']['total']);
    }

    public function testPencarianPasienRujukanParameterKurangDariTiga()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri: '/api/irj/pasien-rujukan?pencarian=20', headers: [
            'Authorization' => $login['data']['token']
        ])->assertStatus(400)
            ->assertJson(
                [
                    "error" => [
                        "pesan" => []
                    ]
                ]
            )->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testPencarianPasienRujukanParameterKurangDariTiga"]);
    }
}
