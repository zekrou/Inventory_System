<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Generate_ar extends CI_Controller {

    public function index() {
        $english_file = APPPATH.'language/english/app_lang.php';
        $arabic_dir = APPPATH.'language/arabic/';
        $arabic_file = $arabic_dir.'app_lang.php';
        
        if (!is_dir($arabic_dir)) mkdir($arabic_dir, 0755, true);
        
        $lang_en = [];
        include $english_file;
        
        $lang_ar = [];
        echo "<h2>Traduction auto (cURL gratuit)...</h2>";
        
        foreach ($lang_en as $key => $value) {
            if (trim($value) && $key != 'direction') {
                $lang_ar[$key] = $this->translate_text($value, 'en', 'ar');
                echo "<p><strong>$key</strong>: '$value' → '" . htmlspecialchars($lang_ar[$key]) . "'</p>";
                flush(); // Live update
            }
        }
        $lang_ar['direction'] = 'rtl';
        
        $content = "<?php defined('BASEPATH') OR exit('No direct script access allowed');\n\n";
        $content .= var_export($lang_ar, true) . "\n?>";
        
        file_put_contents($arabic_file, $content);
        
        echo "<h2 style='color:green'>✅ GÉNÉRÉ ! <a href='/'>← Login</a></h2>";
    }
    
    private function translate_text($text, $source = 'en', $target = 'ar') {
        $url = 'https://translate.googleapis.com/translate_a/single';
        $data = [
            'client' => 'gtx',
            'sl' => $source,
            'tl' => $target,
            'dt' => 't',
            'q' => $text
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
        $response = curl_exec($ch);
        curl_close($ch);
        
        $json = json_decode($response, true);
        return $json[0][0][0] ?? $text; // Fallback original
    }
}
