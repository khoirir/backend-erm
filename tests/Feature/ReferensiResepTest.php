<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

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
}
