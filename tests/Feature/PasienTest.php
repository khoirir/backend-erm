<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class PasienTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function setUp(): void
    {
        parent::setUp();
        DB::delete("DELETE FROM user_erm WHERE kd_dokter = 'TEST'");
    }

    public function testGetDetailPasienSukses(){
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri:'/api/irj/pasien/2023/11/14/000002', headers:[
            'Authorization' => $login['data']['token']
        ])->assertStatus(200)
            ->assertJson(
                [
                    "data" => [
                        "noRawat" => "2023/11/14/000002"
                    ]
                ]
            )->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testGetDetailPasienUnauthorized(){
        $response = $this->get(uri:'/api/irj/pasien/2023/11/14/000002', headers:[
            'Authorization' => ''
        ])->assertStatus(401)
            ->assertJson(
                [
                    "error" => [
                        "pesan" => "UNAUTHORIZED"
                    ]
                ]
            )->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testGetDetailPasienDokterLain(){
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri:'/api/irj/pasien/2023/10/06/000004', headers:[
            'Authorization' => $login['data']['token']
        ])->assertStatus(404)
            ->assertJson(
                [
                    "error" => [
                        "pesan" => "DATA PASIEN TIDAK DITEMUKAN"
                    ]
                ]
            )->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testGetDetailPasienNoRawatTidakDitemukan(){
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        $response = $this->get(uri:'/api/irj/pasien/2023/11/14/000803', headers:[
            'Authorization' => $login['data']['token']
        ])->assertStatus(404)
            ->assertJson(
                [
                    "error" => [
                        "pesan" => "DATA PASIEN TIDAK DITEMUKAN"
                    ]
                ]
            )->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }
}
