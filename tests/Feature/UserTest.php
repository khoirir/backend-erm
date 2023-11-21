<?php

namespace Tests\Feature;

use App\Models\UserModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        DB::delete("DELETE FROM user_erm WHERE kd_dokter = 'TEST'");
    }

    public function testLoginSuccess()
    {
        $response = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "kdDokter" => "TEST"
                ]
            ])
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
        $user = UserModel::query()->where('kd_dokter', 'TEST')->first();
        self::assertNotNull($user);

    }

    public function testLoginStatusPegawaiKeluar()
    {
        $response = $this->post('/api/user/login', [
            "username" => "DENOK",
            "password" => "DENOK123"
        ])->assertStatus(401)
            ->assertJson(
                [
                    "error" => [
                        "pesan" => "USERNAME ATAU PASSWORD SALAH"
                    ]
                ]
            )
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testLoginStatusDokterTidakAktif()
    {
        $response = $this->post('/api/user/login', [
            "username" => "NIXIE",
            "password" => "NIXIE123"
        ])->assertStatus(401)
            ->assertJson(
                [
                    "error" => [
                        "pesan" => "USERNAME ATAU PASSWORD SALAH"
                    ]
                ]
            )
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testLoginTidakValid()
    {
        $response = $this->post('/api/user/login', [
            "username" => "",
            "password" => ""
        ])->assertStatus(400)
            ->assertJson(
                [
                    "error" => [
                        "pesan" => []
                    ]
                ]
            )
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testLoginUsernameSalah()
    {
        $response = $this->post('/api/user/login', [
            "username" => "SALAH",
            "password" => "TEST"
        ])->assertStatus(401)
            ->assertJson(
                [
                    "error" => [
                        "pesan" => "USERNAME ATAU PASSWORD SALAH"
                    ]
                ]
            )
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testLoginPasswordSalah()
    {
        $response = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "SALAH"
        ])->assertStatus(401)
            ->assertJson(
                [
                    "error" => [
                        "pesan" => "USERNAME ATAU PASSWORD SALAH"
                    ]
                ]
            )
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testLogoutSukses()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();
        $response = $this->delete(uri: '/api/user/logout', headers: [
            'Authorization' => $login['data']['token']
        ])->assertStatus(200)
            ->assertJson(
                [
                    "data" => [
                        "pesan" => "LOGOUT BERHASIL"
                    ]
                ]
            )->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testLogoutTidakValid()
    {
        $response = $this->delete(uri: '/api/user/logout', headers: [
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

    public function testLogoutTokenExpired()
    {
        $login = $this->post('/api/user/login', [
            "username" => "TEST",
            "password" => "TEST"
        ])->json();

        UserModel::query()->where('id', $login['data']['token'])
            ->update(['expired_at' => Carbon::now()]);

        $response = $this->delete(uri: '/api/user/logout', headers: [
            'Authorization' => $login['data']['token']
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

}
