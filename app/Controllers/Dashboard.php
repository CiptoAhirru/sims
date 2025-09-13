<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;

class Dashboard extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        $token = session()->get('api_token');

        $email = session()->get('email');
        if (!$token) {
            return redirect()->to('/login')->with('error', 'Silakan login dulu');
        }

        $api['profile'] = $this->apiProfile($token, $email);
        $api['saldo'] = $this->apiSaldo($token);
        $api['services'] = $this->apiServices($token);
        $api['banner'] = $this->apiBanner();


        return view('dashboard/index', $api);
    }

    public function profile()
    {
        $token = session()->get('api_token');

        $email = session()->get('email');


        if (!$token) {
            return redirect()->to('/login')->with('error', 'Silakan login dulu');
        }

        $profile = [
            'first_name' => session()->get('first_name'),
            'last_name' => session()->get('last_name'),
            'email' => $email
        ];

        return view('dashboard/profile', $profile);
    }

    public function topup()
    {
        $token = session()->get('api_token');

        $email = session()->get('email');


        if (!$token) {
            return redirect()->to('/login')->with('error', 'Silakan login dulu');
        }

        $api['profile'] = $this->apiProfile($token, $email);
        $api['saldo'] = $this->apiSaldo($token);

        return view('dashboard/topup', $api);
    }

    public function actiontopup()
    {
        $token = session()->get('api_token');
        try {
            $client = \Config\Services::curlrequest();
            $payload = [
                "top_up_amount" => $this->request->getPost('nominal')
            ];
            $apiResponse = $client->post('https://take-home-test-api.nutech-integrasi.com/topup', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json'
                ],
                'json' => $payload
            ]);


            $statusCode = $apiResponse->getStatusCode();
            $result     = json_decode($apiResponse->getBody(), true);


            if ($statusCode === 200 && isset($result['status']) && $result['status'] == 0) {
                return $this->response->setJSON([
                    'status'   => 'success',
                    'message'  => $result['message'] ?? 'Top Up Balance berhasil',
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

    public function actiontransaksi()
    {
        $token = session()->get('api_token');

        try {
            $client = \Config\Services::curlrequest();
            $payload = [
                "service_code" => $this->request->getPost('service_code')
            ];

            $apiResponse = $client->post('https://take-home-test-api.nutech-integrasi.com/transaction', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json'
                ],
                'json' => $payload
            ]);


            $statusCode = $apiResponse->getStatusCode();
            $result     = json_decode($apiResponse->getBody(), true);


            if ($statusCode === 200 && isset($result['status']) && $result['status'] == 0) {
                return $this->response->setJSON([
                    'status'   => 'success',
                    'message'  => $result['message'] . ' Sebesar ' . number_format(esc($result['data']['total_amount'] ?? '0'), 0, ',', '.') ?? 'Transaksi berhasil',
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

    public function transaction()
    {
        $token = session()->get('api_token');

        $email = session()->get('email');


        if (!$token) {
            return redirect()->to('/login')->with('error', 'Silakan login dulu');
        }

        $api['profile'] = $this->apiProfile($token, $email);
        $api['saldo'] = $this->apiSaldo($token);
        $api['transactions'] = $this->apiTransaction($token, 0, 10);

        return view('dashboard/transaction', $api);
    }

    public function loadMore()
    {
        $token = session()->get('api_token');
        $page = $this->request->getGet('page');
        $offset = $this->request->getGet('page');

        $transactions = $this->apiTransaction($token, $offset, $page);
        // var_dump($transactions);
        // die;


        if (empty($transactions)) {
            return $this->respond([
                'status' => 'end',
                'message' => 'Tidak ada data lagi',
                'html' => ''
            ]);
        }

        $html = '';
        foreach ($transactions['records'] as $transaction) {
            // var_dump($transaction);
            // $timestamp = strtotime($transaction['created_on']);
            $timestamp = strtotime('2025-08-09');
            $formattedDate = date('Y-m-d H:i:s', $timestamp);

            $html .= '
            <div class="transaction-item d-flex align-items-start">
                <div class="transaction-icon ' . ($transaction['transaction_type'] == 'TOPUP' ? 'icon-income' : 'icon-expense') . '">
                    <i class="bi ' . ($transaction['transaction_type'] == 'TOPUP' ? 'bi-arrow-down-left' : 'bi-arrow-up-right') . '"></i>
                </div>
                <div class="transaction-details">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="transaction-amount ' . ($transaction['transaction_type'] == 'TOPUP' ? 'income' : 'expense') . '">
                            ' . ($transaction['transaction_type'] == 'TOPUP' ? '+' : '-') . ' Rp' . number_format($transaction['total_amount'], 0, ',', '.') . '
                        </div>
                        <div class="transaction-description">
                            ' . $transaction['description'] . '
                        </div>
                    </div>
                    <div class="transaction-date">
                        ' . $formattedDate  . '
                    </div>
                </div>
            </div>';
        }

        return $this->respond([
            'status' => 'success',
            'html' => $html,
            'limit' => $offset + 10
        ]);
    }


    public function editProfile()
    {
        $token = session()->get('api_token');
        $first_name = $this->request->getPost('first_name');
        $last_name  = $this->request->getPost('last_name');

        try {
            $client = \Config\Services::curlrequest();
            $payload = array(
                "first_name" => $first_name,
                "last_name" => $last_name,
            );

            // var_dump($payload);
            // die;

            $apiResponse = $client->put('https://take-home-test-api.nutech-integrasi.com/profile/update', [
                'headers' => [
                    'Accept'       => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
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
                    'message'  => $result['message'] ?? 'Update Profile berhasil!',
                    'redirect' => site_url('/dashboard')
                ]);
            } else {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => $result['message'] ?? 'Update Profile gagal'
                ]);
            }
        } catch (\Exception $e) {
            return $this->respond([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function apiTransaction($token, $offset, $limit)
    {
        try {
            $client = \Config\Services::curlrequest();

            $apiResponse = $client->get(
                'https://take-home-test-api.nutech-integrasi.com/transaction/history?offset=' . $offset . '&limit=' . $limit,
                [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $token
                    ],
                ]
            );

            $statusCode = $apiResponse->getStatusCode();
            $result     = json_decode($apiResponse->getBody(), true);

            // var_dump($result);
            // die;

            if ($statusCode === 200 && isset($result['status']) && $result['status'] === 0) {
                return $result['data'] ?? [];
            } else {
                return [];
            }
        } catch (\Exception $e) {
            return [];
        }
    }

    public function apiProfile($token, $email)
    {
        try {
            $client = \Config\Services::curlrequest();

            $apiResponse = $client->get('https://take-home-test-api.nutech-integrasi.com/profile', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $token
                ],
            ]);

            $statusCode = $apiResponse->getStatusCode();
            $result     = json_decode($apiResponse->getBody(), true);

            // var_dump($result);
            // die;

            if ($statusCode === 200 && isset($result['status']) && $result['status'] === 0) {
                session()->set('first_name', $result['data']['first_name']);
                session()->set('last_name', $result['data']['last_name']);
                return $result['data'] ?? [];
            } else {
                return [];
            }
        } catch (\Exception $e) {
            return [];
        }
    }

    public function apiSaldo($token)
    {
        try {
            $client = \Config\Services::curlrequest();

            $apiResponse = $client->get('https://take-home-test-api.nutech-integrasi.com/balance', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $token
                ],
            ]);

            $statusCode = $apiResponse->getStatusCode();
            $result     = json_decode($apiResponse->getBody(), true);

            // var_dump($result);
            // die;

            if ($statusCode === 200 && isset($result['status']) && $result['status'] === 0) {
                return $result['data'] ?? [];
            } else {
                return [];
            }
        } catch (\Exception $e) {
            return [];
        }
    }

    public function apiServices($token)
    {
        try {
            $client = \Config\Services::curlrequest();

            $apiResponse = $client->get('https://take-home-test-api.nutech-integrasi.com/services', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $token
                ],
            ]);

            $statusCode = $apiResponse->getStatusCode();
            $result     = json_decode($apiResponse->getBody(), true);

            // var_dump($result);
            // die;

            if ($statusCode === 200 && isset($result['status']) && $result['status'] === 0) {
                return $result['data'] ?? [];
            } else {
                return [];
            }
        } catch (\Exception $e) {
            return [];
        }
    }

    public function apiBanner()
    {
        try {
            $client = \Config\Services::curlrequest();

            $apiResponse = $client->get('https://take-home-test-api.nutech-integrasi.com/banner', [
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);

            $statusCode = $apiResponse->getStatusCode();
            $result     = json_decode($apiResponse->getBody(), true);

            // var_dump($result);
            // die;

            if ($statusCode === 200 && isset($result['status']) && $result['status'] === 0) {
                return $result['data'] ?? [];
            } else {
                return [];
            }
        } catch (\Exception $e) {
            return [];
        }
    }
}
