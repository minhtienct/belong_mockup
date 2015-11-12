<?php

namespace Application\Application\Service;

use Zend\ServiceManager\ServiceLocatorInterface;


/**
 * HtmlToPdf
 *
 */
class HtmlToPdf {

    /**
     * @var ServiceLocatorInterface
     */
    protected $svLocator;
    protected $ini_config = [];
    protected $basic_authentication = [];
    protected $default_option = [];
    protected $option_key = [
        'orientation' => '-O',
        'page_size' => '-s',
        'quiet' => '-q',
        'cookie' => '--cookie',
        'disable_external_links' => '--disable-external-links',
        'disable_internal_links' => '--disable-internal-links',
        'disable_javascript' => '--disable-javascript',
        'pageing_place' => '--footer-right',
        'pageing_format' => '[page]/[toPage]',
        'dpi' => '--dpi',
        'margin_top' => '-T',
        'margin_bottom' => '-B',
        'encoding' => '--encoding',
        'ignore_load_errors' => '--ignore-load-errors'
    ];
    protected $option = [
        'orientation' => 'Portrait',
        'page_size' => 'A4',
        'cookie' => 'PHPSESSID',
        'dpi' => '75',
        'margin_top' => '5',
        'margin_bottom' => '5',
        'encoding' => 'UTF-8',
    ];
    protected $option_library = [
        'orientation' => array('Landscape', 'Portrait'),
        'page_size' => array('A4'),
        'pageing_place' => array('--footer-right', '--header-center', '--header-left', '--footer-center', '--footer-left', '--footer-right'),
    ];

    /**
     * コンストラクタ
     *
     * return void
     */
    public function __construct(ServiceLocatorInterface $svLocator) {
        $this->svLocator = $svLocator;
        //$this->setServiceLocator($serviceLocator);
        //$this->EntityService = $this->getServiceManager()->get('EntityService');
        $this->ini_config = $this->svLocator->get('Configuration')['wkhtmltopdf'];
        //$this->basic_authentication = $this->svLocator->get('Configuration')['basic_authentication'];

        $this->default_option = [];
        $this->default_option['tool_path'] = $this->getHtmltopdfPath();
        //$this->default_option['ignore_load_errors'] = $this->option_key['ignore_load_errors'];
        $this->default_option['encoding'] = $this->option_key['encoding'] . ' ' . $this->option['encoding'];
        //$this->default_option['quiet'] = $this->option_key['quiet'];
        $this->default_option['disable_external_links'] = $this->option_key['disable_external_links'];
        $this->default_option['disable_internal_links'] = $this->option_key['disable_internal_links'];
        $this->default_option['disable_javascript'] = $this->option_key['disable_javascript'];
        $this->default_option['orientation'] = $this->option_key['orientation'] . ' ' . $this->option['orientation'];
        $this->default_option['dpi'] = $this->option_key['dpi'] . ' ' . $this->option['dpi'];
        $this->default_option['margin_top'] = $this->option_key['margin_top'] . ' ' . $this->option['margin_top'];
        $this->default_option['margin_bottom'] = $this->option_key['margin_bottom'] . ' ' . $this->option['margin_bottom'];
        $this->default_option['pageing_place'] = $this->option_key['pageing_place'];
        $this->default_option['pageing_format'] = '"' . $this->option_key['pageing_format'] . '"';
        $this->default_option['page_size'] = $this->option_key['page_size'] . ' ' . $this->option['page_size'];

        if (isset($this->basic_authentication['username']) && isset($this->basic_authentication['password'])) {
            $this->default_option['username'] = '--username ' . $this->basic_authentication['username'];
            $this->default_option['password'] = '--password ' . $this->basic_authentication['password'];
        }

        $this->option['cookie'] =$this->ini_config['cookie_key'];
    }

    public function getHtmltopdfPath() {
        $path = '/usr/local/bin/wkhtmltopdf';
        if (is_array($this->ini_config)) {
            $path = $this->ini_config['tool_path'];
        }

        return $path;
    }

    public function setOption($key, $option = null) {
        if (array_key_exists($key, $this->option_key)) {
            $this->default_option[$key] = $this->option_key[$key] . ' ' . $option;
        }
    }

    public function removeOption($key) {
        if (array_key_exists($key, $this->default_option)) {
            $this->default_option[$key] = '';
        }
    }

    public function createPdf($url, $file_name, $session_id = null) {
        if ($url && $file_name) {

            $this->file_path = HOME_DIR . $this->ini_config['output_path'];

            //フォルダない時作成する
            if (is_dir($this->file_path) == false) {

                mkdir($this->file_path);
                chmod($this->file_path, 0777);
            };

            $this->file_path = $this->file_path . '/' . $file_name;

            //古いファイル削除する
            $this->removeOldFile($this->file_path);
            if (is_file($this->file_path)) {
                return true;
            }
            if ($session_id !== null) {
                $this->default_option['cookie'] = $this->option_key['cookie'] . ' ' . $this->option['cookie'] . ' ' . '"' . $session_id . '"';
            }

            $cmd = join(' ', $this->default_option) . ' ' . escapeshellarg($url) . ' ' . escapeshellarg($this->file_path);
            
//ファイルシステムセッション対応
            session_write_close();
            $output = shell_exec($cmd);
            if (is_file($this->file_path)) {
                return $this->file_path;
            } else {
                trigger_error($output);
            }
            return false;
        }
    }

    public function outputPdf($pdf_file_path, $download_file_name, $removeFile = false) {
        ignore_user_abort(false);
        ini_set('output_buffering', 0);
        ini_set('zlib.output_compression', 0);

        $chunk = 10 * 1024 * 1024; // bytes per chunk (10 MB)

        $fh = fopen($pdf_file_path, "rb");

        if ($fh === false) {
            trigger_error('File can not be opend.');

            return;
        }

        $download_file_name = mb_convert_encoding($download_file_name, "CP932", "UTF-8");

        header('Content-Description: File Transfer');
        header('Content-Type: application/force-download');
        header('Content-Disposition: attachment; filename=' . $download_file_name);
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');

        // Repeat reading until EOF
        while (!feof($fh)) {
            echo fread($fh, $chunk);

            ob_flush();  // flush output
            flush();
        }
        if ($removeFile === true) {
            $this->removeFile();
        }

        exit;
    }

    public function saveFileAs($newSubFolder, $newName) {
        mb_http_output('SJIS');
        $fh = fopen($this->file_path, "rb");

        if ($fh === false) {
            trigger_error('File can not be opend.');

            return;
        }

        $newPath = HOME_DIR . $this->ini_config['output_path'] . '/' . $newSubFolder;
        //フォルダない時作成する
        if (is_dir($newPath) == false) {
            mkdir($newPath);
            chmod($newPath, 0777);
        };
        $newPath .= '/' . $newName;

        $newPath = mb_convert_encoding($newPath, "SJIS", "UTF-8");
        copy($this->file_path, $newPath);
        if (is_file($newPath)) {
            return $newPath;
        }
        return false;
    }

    /**
     * 古いファイルを削除
     *
     * @access public
     * @param  string $folder
     * @return void
     * @author Albert
     * @since  2012/02/14
     */
    private function removeOldFile($filepath) {
        if (is_file($filepath)) {
            @unlink($filepath);
        }
    }

    private function removeFile() {
        if (is_file($this->file_path)) {
            @unlink($this->file_path);
        }
    }

}
