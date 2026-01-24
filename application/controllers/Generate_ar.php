<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Generate_ar extends CI_Controller {
    public function index() {
        // Vérifiez admin/login d'abord
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        // Installez library via composer ou manuellement
        require_once FCPATH.'vendor/autoload.php'; // OU third_party
        use Stichoza\GoogleTranslate\GoogleTranslate;
        
        $english_file = APPPATH.'language/english/app_lang.php';
        $arabic_file = APPPATH.'language/arabic/app_lang.php';
        
        // Charge english
        $_lang = [];
        include $english_file;
        
        $tr = new GoogleTranslate('en', 'ar');
        $new_lang = [];
        
        foreach ($_lang as $key => $value) {
            if (trim($value) && !in_array($key, ['direction'])) {
                $new_lang[$key] = $tr->translate($value);
            }
        }
        $new_lang['direction'] = 'rtl';
        
        $content = "<?php defined('BASEPATH') OR exit('No direct script access allowed');\n\n";
        $content .= '$direction = \'rtl\';' . "\n\n";
        $content .= "return " . var_export($new_lang, true) . ";";
        
        file_put_contents($arabic_file, $content);
        
        echo "✅ app_lang.php arabe généré ! <a href='/'>Retour login</a>";
    }
}
