<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class Auth extends BaseController
{
    use ResponseTrait;
    public function login()
    {
        return view('auth/login');
    }

    public function actionlogin()
    {
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        try {
            $client = \Config\Services::curlrequest();

            $payload = [
                "email"    => $email,
                "password" => $password
            ];

            $apiResponse = $client->post('https://take-home-test-api.nutech-integrasi.com/login', [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'json' => $payload
            ]);

            $statusCode = $apiResponse->getStatusCode();
            $result     = json_decode($apiResponse->getBody(), true);

            if ($statusCode === 200 && isset($result['status']) && $result['status'] === 0) {
                if (isset($result['data']['token'])) {
                    // session()->set([
                    //     'isLoggedIn' => true,
                    //     'api_token'    => $$result['data']['token'],
                    //     'email'      => $email
                    // ]);

                    session()->set('api_token', $result['data']['token']);
                    session()->set('email', $email);
                    session()->set('isLoggedIn', true);
                }
                return $this->response->setJSON([
                    'status'   => 'success',
                    'message'  => $result['message'] ?? 'Login berhasil!',
                    'redirect' => site_url('/dashboard')
                ]);
            } else {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => $result['message'] ?? 'Login gagal'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function register()
    {
        return view('auth/register');
    }

    public function registrasi()
    {
        $email      = $this->request->getPost('email');
        $first_name = $this->request->getPost('namadepan');
        $last_name  = $this->request->getPost('namabelakang');
        $password   = $this->request->getPost('password');

        try {
            $client = \Config\Services::curlrequest();
            $payload = array(
                "email" => $email,
                "first_name" => $first_name,
                "last_name" => $last_name,
                "password" => $password
            );

            // var_dump($payload);
            // die;

            $apiResponse = $client->post('https://take-home-test-api.nutech-integrasi.com/registration', [
                'headers' => [
                    'Accept'       => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload
            ]);

            $statusCode = $apiResponse->getStatusCode();
            $result = json_decode($apiResponse->getBody(), true);
            // var_dump($statusCode);
            // var_dump($result);
            // die;

            if ($statusCode === 200 && isset($result['status']) && $result['status'] === 0) {
                return $this->response->setJSON([
                    'status'   => 'success',
                    'message'  => $result['message'] ?? 'Registrasi berhasil!',
                    'redirect' => site_url('/login')
                ]);
            } else {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => $result['message'] ?? 'Registrasi gagal'
                ]);
            }
        } catch (\Exception $e) {
            return $this->respond([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function logout()
    {
        session()->destroy();
        return $this->response->setJSON([
            'status'   => 'success',
            'message'  => 'Berhasil logout!',
            'redirect' => site_url('/login')
        ]);
    }
}
