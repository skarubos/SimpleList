<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\develop;
use Carbon\Carbon;
use DOMDocument;
use DOMXPath;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use GuzzleHttp\Cookie\SessionCookieJar;
use Symfony\Component\DomCrawler\Crawler;



class ScrapingController extends Controller
{

    function getNo($string) {
        $string = substr($string, strlen('month02?jutyuno='));
        $string = substr($string, 0, -15);
        return $string;
    }
    
    // GuzzleとSymfony\DomCrawlerのテスト
    public function scrape(Request $request){

        $url_base = $request->input('url_a');
        $url_login = $request->input('url_b');
        $url_list = $request->input('url_c');
// dd($url_base);

        $client = new Client([
            'base_uri' => $url_base, // サイトのベースURL
            'cookies' => true,
            'allow_redirects' => true,
        ]);



//         $response = $client->get($url_login);
// // dd($response);
//         $html = $response->getBody()->getContents();
// // dd($html);
//         $crawler = new Crawler($html);
//         $token = $crawler->filter('input[name="_token"]')->attr('value');
// // dd($token);
        
//         // $client = new Client([
//         //     'base_uri' => 'https://www.mikigroup.jp', // サイトのベースURL
//         //     'cookies' => true,
//         //     'allow_redirects' => true,
//         // ]);

//         // ログインリクエストを送信
//         $response = $client->post($url_login, [
//             'form_params' => [
//                 'dairiten_cd' => '3851',
//                 'password' => 'vs6ky99j',
//                 '_token' => $token,
//             ],
//         ]);

//         // ログイン成功を確認
//         if ($response->getStatusCode() !== 200) {
//             die('ログインに失敗しました。');
//         }

//         // Set-Cookieヘッダーからクッキーを取得
//         $setCookieHeader = $response->getHeader('Set-Cookie');
//         $cookieParts = explode(';', $setCookieHeader[0]);
//         $cookieKeyValue = explode('=', $cookieParts[0]);
//         $cookieName = $cookieKeyValue[0];
//         $cookieValue = $cookieKeyValue[1];



        $cookieName = 'laravel_session';
        $cookieValue = 'eyJpdiI6ImhaRFJiaGYxODQ4dDVIczJNUlU3T1E9PSIsInZhbHVlIjoiUXBBdks5TjJrSm9cL21mTDVkZE8xRUlcLzRZRUl4MzJGc0tTaVhuNnhvYVVLZDU4Y3lEVVNjbXd4dDR1Tzhub25Oa2lJV0NoZTNJbUYwMEZWRTA0bkFYUT09IiwibWFjIjoiNWYyNTkyYmMxZTY2MDViMjAyODgwMDhlNWNkNWQ5ZDdlOWRjNjE3OGNmMjg5YjA5ZjQzNDQwMDFhNWY3OGVhMSJ9';

        // Secure属性をtrueにしてクッキーを作成
        $setCookie = new SetCookie([
            'Name' => $cookieName,
            'Value' => $cookieValue,
            'Domain' => 'www.mikigroup.jp',
            'Path' => '/',
            'Secure' => true,
            'HttpOnly' => true,
        ]);

        // CookieJarにクッキーを追加
        $jar = new CookieJar();
        $jar->setCookie($setCookie);

        // 現在のクッキー情報を取得
        $cookies = $jar->toArray();
// dd($cookies);

        // ログイン後のページにアクセス
        $response = $client->get($url_list, [
            'cookies' => $jar,
        ]);
        $html = $response->getBody()->getContents();
// dd($url_list,$cookieValue,$html);


        // DomCrawlerでHTMLを解析
        $crawler = new Crawler($html);

        // titleタグを取得
        $title = $crawler->filter('h3.title')->text();
        // tableの内容を取得
        $tradeData = [];
        $crawler->filter('tbody.table-hover tr')
            ->each(function ($node) use (&$tradeData) {
                $row = [
                    'link' => $node->filter('td')->eq(1)->filter('a')->attr('href'),
                    'date' => $node->filter('td')->eq(3)->text(),
                    'name' => $node->filter('td')->eq(6)->text(),
                    'salse' => $node->filter('td')->eq(7)->text(),
                    'in' => $node->filter('td')->eq(8)->text(),
                    'out' => $node->filter('td')->eq(9)->text(),
                ];
                $tradeData[] = $row;
            });
        // salse, in, outの3つ全てに値が存在しない要素をフィルタリング
        $tradeData = array_filter($tradeData, function($row) {
            return !empty($row['salse']) || !empty($row['in']) || !empty($row['out']);
        });
        // 'link'から'jutyuno'を抽出して追加
        foreach ($tradeData as &$row) {
            $row['jutyuno'] = $this->getNo($row['link']);
        }
// dd($title,$tradeData,$html);

        $arr = [];
        // 未登録の取引のみ抽出
        $newTrade = [];
        foreach ($tradeData as $trade) {
            if (!in_array($trade['jutyuno'], $arr)) {
                $newTrade[] = $trade;
            }
        }
        if ($newTrade === []) {
            return("未登録の取引は存在しませんでした。");
        }
        
        $num = 0;
        $details = [];
        foreach ($newTrade as $trade) {
            $num++;
            // 既に登録されている取引はスキップ
            if (in_array($trade['jutyuno'], $arr)) {
                continue;
            }
            if ($num > 3) {
                continue;
            }
            // 取引詳細ページにアクセス
            $response = $client->get($trade['link'], [
                'cookies' => $jar,
            ]);
            $html = $response->getBody()->getContents();
// dd($html);

            // DomCrawlerでHTMLを解析
            $crawler = new Crawler($html);

            // tableの内容を取得
            $detail = [];
            $crawler->filter('tbody.table-hover')->first()->filter('tr')
                ->each(function ($node) use (&$detail) {
                    if (!($node->filter('th')->eq(0)->count() > 0)) {
                        $row = [
                            'name' => $node->filter('td')->eq(0)->text(),
                            'salse' => $node->filter('td')->eq(1)->text(),
                            'in' => $node->filter('td')->eq(2)->text(),
                            'out' => $node->filter('td')->eq(3)->text(),
                        ];
                        $detail[] = $row;
                    }
                });
            $details[] = $detail;
        }

dd($tradeData,$newTrade,$details);

        return $data = [
            'newTrade' => $newTrade,
            'details' => $details,
        ];
    }


    public function test()
    {
        // ログイン情報
        // $loginUrl = 'https://looop-denki.com/mypage/auth/login/';
        $loginUrl = 'https://www.mikigroup.jp/login';
        $dairiten_cd = '3851';
        $password = 'vs6ky99j';
        $token = 'qAuMcDUoeRZj0lb2OQ8Gw728yolX8oaYgs5DSOAf';
    
        // 初期化
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $loginUrl);
        // curl_setopt($ch, CURLOPT_POST, 1);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        //     'dairiten_cd' => $dairiten_cd,
        //     'password' => $password,
        //     '_token' => $token
        // ]));
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($ch, CURLOPT_COOKIEJAR, storage_path('app/cookie.txt'));
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, [
        //     'User-Agent: Mozilla/5.0',
        //     'Accept: text/html',
        //     'Referer: https://www.mikigroup.jp/login', // リファラーを追加
        // ]);
    
        // $response = curl_exec($ch);
    
        // ログイン後のページからデータを取得
        // $dataUrl = 'https://www.mikigroup.jp/month02?jutyuno=60376&dendt=20241025';
        // $dataUrl = 'https://www.mikigroup.jp/jisseki02';
        $dataUrl = 'https://www.mikigroup.jp/month01';
        
        $tables = $this->get_tables_url($dataUrl);

        $firstTable = $tables->item(0);
        // tableの行を取得
        $rows = $firstTable->getElementsByTagName('tr');
        // 行ごとにデータを取得
        $tradeList = [];
        foreach ($rows as $i => $row) {
            if ($i < 5) continue; // 先頭の5行をスキップ
        
            $cols = $row->getElementsByTagName('td');
            $rowData = [];
            foreach ($cols as $j => $col) {
                if ($j == 0) {
                    // aタグのhref属性を取得
                    $link = $col->getElementsByTagName('a');
                    if ($link->length > 0) {
                        $rowData[] = $link->item(0)->getAttribute('href');
                    }
                } elseif ($j > 1) {
                    $rowData[] = trim($col->nodeValue);
                }
            }
            $tradeList[] = $rowData;
        }  
    // dd($tradeList);
    
        $details = [];
        foreach ($tradeList as $index => $trade) {
            $tradeUrl = "https://www.mikigroup.jp/" . $trade[0];
            $tables = $this->functionsController->get_tables_url($tradeUrl);
            $detail = [];
            for ($k = 0; $k < count($tables); $k++) {
                if ($k < 1) continue;
                $firstTable = $tables->item($k);
                // tableの行を取得
                $rows = $firstTable->getElementsByTagName('tr');
                // 行ごとにデータを取得
                $tableData = [];
                foreach ($rows as $i => $row) {
                    $cols = $row->getElementsByTagName('td');
                    $rowData = [];
                    foreach ($cols as $col) {
                        $rowData[] = trim($col->nodeValue);
                    }
                    $tableData[] = $rowData;
                }
                $detail[] = $tableData;
            }
            $details[] = $detail;
        }
    dd($tradeList, $details);
        
        $tradeType = TradeType::all();
        // データをビューに渡す
        return view('test', compact('tradeList', 'details', 'tradeType'));
    }

    public function get_tables_url($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_COOKIEFILE, storage_path('app/cookie.txt'));
        $data = curl_exec($ch);
        curl_close($ch);

        // DOMパーサーを使用してデータを解析
        $dom = new DOMDocument();
        @$dom->loadHTML($data);

        // table要素を取得
        $tables = $dom->getElementsByTagName('table');
        return $tables;
    }
}
