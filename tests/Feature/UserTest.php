<?php

namespace Tests\Feature;

use App\Models\PegawaiModel;
use App\Models\UserModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        DB::delete("DELETE FROM user_erm");
    }

    public function testLoginSuccess(){
        $response = $this->post('/api/user/login', [
            "username" => "DRDEDEN",
            "password" => "DRDEDEN123"
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "kdDokter" => "DRDEDEN"
                ]
            ])
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
        $user = UserModel::where('kd_dokter', 'DRDEDEN')->first();
        self::assertNotNull($user);

    }

    public function testLoginTidakValid(){
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

    public function testLoginUsernameSalah(){
        $response = $this->post('/api/user/login', [
            "username" => "DRFERDII",
            "password" => "DRFERDI123"
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

    public function testLoginPasswordSalah(){
        $response = $this->post('/api/user/login', [
            "username" => "DRFERDI",
            "password" => "DRFERDI122"
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

    public function testLogoutSukses(){
        $login = $this->post('/api/user/login', [
            "username" => "DRFERDI",
            "password" => "DRFERDI123"
        ])->json();
        $response = $this->delete(uri:'/api/user/logout', headers:[
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

    public function testLogoutTidakValid(){
        $response = $this->delete(uri:'/api/user/logout', headers:[
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

    public function testLogoutTokenExpired(){
        $login = $this->post('/api/user/login', [
            "username" => "DRFERDI",
            "password" => "DRFERDI123"
        ])->json();

        UserModel::where('id', $login['data']['token'])
            ->update(['expired_at' => now()]);

        $response = $this->delete(uri:'/api/user/logout', headers:[
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
