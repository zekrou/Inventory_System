<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Generate_ar extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Admin check (login page first, adaptez)
        if (!isset($_SESSION['user_id'])) {
            redirect('login');
        }
    }

    public function index() {
        // Téléchargez https://raw.githubusercontent.com/Stichoza/google-translate-php/master/src/GoogleTranslate.php
        // Copiez dans application/third_party/GoogleTranslate.php
        require_once APPPATH.'third_party/GoogleTranslate.php';
        
        $english_file = APPPATH.'language/english/app_lang.php';
        $arabic_dir = APPPATH.'language/arabic/';
        $arabic_file = $arabic_dir.'app_lang.php';
        
        // Crée dossier arabe
        if (!is_dir($arabic_dir)) mkdir($arabic_dir, 0755, true);
        
        // Charge english
        $lang = [];
        include $english_file;
        
        // Traduction
        $tr = new \Stichoza\GoogleTranslate\GoogleTranslate('en', 'ar');
        $new_lang = [];
        
        foreach ($lang as $key => $value) {
            if (trim($value) && $key != 'direction') {
                $new_lang[$key] = $tr->translate($value);
            }
        }
        $new_lang['direction'] = 'rtl';
        
        // Écrit fichier
        $content = "<?php defined('BASEPATH') OR exit('No direct script access allowed');\n\n";
        $content .= '$direction = \'rtl\';' . "\n\n";
        $content .= var_export($new_lang, true) . ";\n?>";
        
        file_put_contents($arabic_file, $content);
        
        echo "<h2>✅ app_lang.php ARABE GÉNÉRÉ !</h2>";
        echo "<p><a href='/'>← Retour Login</a></p>";
        echo "<p>Fichier: $arabic_file</p>";
    }
}
