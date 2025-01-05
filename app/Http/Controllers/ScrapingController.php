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

    
    // GuzzleとSymfony\DomCrawlerのテスト
    public function scrape(Request $request){

        $url_login = $request->input('url_a');
// dd($url_login);

        // Cookie を保存するファイル名
        // $cookieFile = storage_path('app/cookies.txt');
        // $cookieJar = new SessionCookieJar($cookieFile, true);
        // $jar = new CookieJar();
        $client = new Client([
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
        // $cookieValue = $cookieKeyValue[1];
        
        $cookieName = 'laravel_session';
        $cookieValue = 'eyJpdiI6Ikw3U3pqRVEwOHVvU3AxWHdQSGlDdWc9PSIsInZhbHVlIjoiMHJrM0ZlRm5DYzZmSllwU2RCckN4QmJNU0dPRmJvcm01a0tPOTdjcHBOZTZiVlVjVUNMVVg1NzRvWEErYnRjd1NqQjJUTUhZVGJrdGtmNXFXek9sc2c9PSIsIm1hYyI6IjZkNjQzNGJjOWQ4YmNiYjA1MDNjODQwOTIyNzNjY2JhNDJiZDBmZjk2YTE1NzRhNWRlMThhY2Q5OWIxZGY4NzgifQ%3D%3D';

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
        $url_list = $request->input('url_b');
        // $response = $client->get($url_list);
        $response = $client->get($url_list, [
            'cookies' => $jar,
        ]);
        $html = $response->getBody()->getContents();
dd($url_list,$html);

        // DomCrawlerでHTMLを解析
        $crawler = new Crawler($response->getBody());
        $tables = $crawler->filter('table')->each(function (Crawler $node) {
            return $node->filter('tr')->each(function (Crawler $node) {
                return $node->filter('td')->each(function (Crawler $node) {
                    return $node->text();
                });
            });
        });
dd($tables);

        // HTMLを解析して任意のテーブルデータを取得
        $crawler = new Crawler($html);
        $tableData = [];

        $crawler->filter('table tr')->each(function ($node) use (&$tableData) {
            $row = [];
            $node->filter('td')->each(function ($cell) use (&$row) {
                $row[] = $cell->text();
            });
            $tableData[] = $row;
        });

        // テーブルデータを表示
        dd($tableData);
    }


    public function show_dev_home()
    {
        $items = develop::get();
        return view('dev-home', compact('items'));
    }

    public function scrape_to_show(Request $request){
        // URLからHTMLを取得
        $url = $request->get_web;

        // cURLを使用してデータを取得
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $html = curl_exec($ch);
        curl_close($ch);
dd($html);
        $dom = new DOMDocument;
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        // id="tablefix1"のtableを取得
        $tables = $xpath->query('//table[@id="tablefix1"]');

        $arr = array();

        foreach ($tables as $table) {
            $rows = $table->getElementsByTagName('tr');
            foreach ($rows as $row) {
                $cols = $row->getElementsByTagName('td');
                if ($cols->length > 0) {
                    // 'temperature', 'humidity', 'sunlight'の値が数値の文字列の場合は数値に変換、それ以外の場合はnullを格納
                    $temperature = is_numeric($cols->item(4)->nodeValue) ? floatval($cols->item(4)->nodeValue) : null;
                    $humidity = is_numeric($cols->item(5)->nodeValue) ? floatval($cols->item(5)->nodeValue) : null;
                    $sunlight = is_numeric($cols->item(10)->nodeValue) ? intval($cols->item(10)->nodeValue) * 10 : null;
                    // 0, 4, 5, 10番目の列のデータを取得
                    $arr[] = array(
                        'time' => $cols->item(0)->nodeValue,
                        'temperature' => $temperature,
                        'humidity' => $humidity,
                        'sunlight' => $sunlight
                    );
                }
            }
        }
// dd($arr);
        // データをビューに渡す
        return view('show-result-weather', ['weatherData' => $arr]);
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
