<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use function PHPUnit\Framework\assertNotNull;

class ReferensiResepTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        DB::delete("DELETE FROM user_erm WHERE kd_dokter = 'TEST'");
    }

    public function testListDataObat()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri: '/api/obat/DPRJ', headers: [
            'Authorization' => $login['data']['token']
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testListDataObat"]);
    }

    public function testListDataObatBerdasarkanPencarian()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri: '/api/obat/DPRJ?pencarian=non%20fornas', headers: [
            'Authorization' => $login['data']['token']
        ])->assertStatus(200)
            ->json();
        self::assertNotNull($response['data']);
        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testListDataObatBerdasarkanPencarian"]);
    }

    public function testListDataAturanPakai()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri: '/api/aturan-pakai', headers: [
            'Authorization' => $login['data']['token']
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testListDataAturanPakai"]);
    }

    public function testListDataAturanPakaiBerdasarkanPencarianTidakValid()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri: '/api/aturan-pakai?pencarian=ti', headers: [
            'Authorization' => $login['data']['token']
        ])->assertStatus(400)
            ->assertJson(
                [
                    "error" => [
                        "pesan" => [
                            "pencarian"=> [
                                "MINIMAL HARUS 3 KARAKTER"
                            ]
                        ]
                    ]
                ]
            )->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testListDataAturanPakaiBerdasarkanPencarianTidakValid"]);
    }

    public function testListDataAturanPakaiBerdasarkanPencarian()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri: '/api/aturan-pakai?pencarian=per', headers: [
            'Authorization' => $login['data']['token']
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testListDataAturanPakaiBerdasarkanPencarian"]);
    }

    public function testListDataMetodeRacik()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri: '/api/metode-racik', headers: [
            'Authorization' => $login['data']['token']
        ])->assertStatus(200)
            ->json();
        self::assertNotNull($response);
        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testListDataMetodeRacik"]);
    }

    public function testListDataMetodeRacikBerdasarkanPencarianTidakValid()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri: '/api/metode-racik?pencarian=sa', headers: [
            'Authorization' => $login['data']['token']
        ])->assertStatus(400)
            ->assertJson(
                [
                    "error" => [
                        "pesan" => [
                            "pencarian"=> [
                                "MINIMAL HARUS 3 KARAKTER"
                            ]
                        ]
                    ]
                ]
            )->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testListDataMetodeRacikBerdasarkanPencarianTidakValid"]);
    }

    public function testListDataMetodeRacikBerdasarkanPencarian()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri: '/api/metode-racik?pencarian=kaps', headers: [
            'Authorization' => $login['data']['token']
        ])->assertStatus(200)
            ->json();
        self::assertNotNull($response);

        Log::info(json_encode($response, JSON_PRETTY_PRINT), ["testListDataMetodeRacikBerdasarkanPencarian"]);
    }
}
