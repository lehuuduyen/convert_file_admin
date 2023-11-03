<?php

class PostController extends WP_REST_Controller
{
    private $nameSpace = API_NAME . '/v1';
    public function registerRoutes()
    {
        register_rest_route($this->nameSpace, 'call-news', array(
            array(
                'methods' => 'GET',
                'callback' => array($this, 'callNews')
            ),
        ));
        register_rest_route($this->nameSpace, 'get/file/(?P<image>[a-zA-Z0-9-_]+)', array(
            array(
                'methods' => 'GET',
                'callback' => array($this, 'getImage')
            ),
        ));
        register_rest_route($this->nameSpace, 'call-news-popular', array(
            array(
                'methods' => 'GET',
                'callback' => array($this, 'callNewsPopular')
            ),
        ));
        register_rest_route($this->nameSpace, 'format-file', array(
            array(
                'methods' => 'POST',
                'callback' => array($this, 'formatFile')
            ),
        ));
        register_rest_route($this->nameSpace, 'posts-category/(?P<category>[a-zA-Z0-9-_]+)', array(
            array(
                'methods' => 'GET',
                'callback' => array($this, 'getCategory')
            ),
        ));
        register_rest_route($this->nameSpace, 'post', array(
            array(
                'methods' => 'GET',
                'callback' => array($this, 'getPost')
            ),
        ));
        register_rest_route($this->nameSpace, 'post-popular', array(
            array(
                'methods' => 'GET',
                'callback' => array($this, 'getPostPopular')
            ),
        ));
        register_rest_route($this->nameSpace, 'post/(?P<post_slug>[a-zA-Z0-9-_]+)', array(
            array(
                'methods' => 'GET',
                'callback' => array($this, 'getPostDetail')
            ),
        ));
        register_rest_route($this->nameSpace, 'news/(?P<post_slug>[a-zA-Z0-9-_]+)', array(
            array(
                'methods' => 'GET',
                'callback' => array($this, 'getNewsDetail')
            ),
        ));
    }
    public function getImage($request)
    {
        $type = $request['type'];
        $file = $request['image'];
        // header("Content-Disposition: attachment; filename=$file.$type");
        header('Content-Type: image/' . $type);
        readfile(get_site_url() . "/file/$file." . $type);
    }
    public function getNewsDetail($request)
    {
        $results = [];
        $htmlString = "";
        $args = array(
            'post_type' => POST_TYPE_FEED,
            'post_status' => array('publish'),
            'name' => $request['post_slug']

        );
        $posts = new WP_Query($args);
        if ($posts->have_posts()) {
            $results['code'] = 'success';
            while ($posts->have_posts()) {

                $posts->the_post();

                $getTitle =  get_the_title();
                try {
                    $url = get_post_meta(get_the_ID(), 'wprss_item_permalink', true);
                    $args = array(
                        'timeout'     => 5,
                        'redirection' => 5,
                        'httpversion' => '1.0',
                        'user-agent'  => 'WordPress/1',
                        'blocking'    => true,
                        'headers'     => array(),
                        'cookies'     => array(),
                        'body'        => null,
                        'compress'    => false,
                        'decompress'  => true,
                        'sslverify'   => true,
                        'stream'      => false,
                        'filename'    => null
                    );

                    $response = wp_remote_get($url, $args);
                    $htmlString = (string) $response['body'];


                    libxml_use_internal_errors(true);
                    $doc = new DOMDocument();
                    $doc->loadHTML($htmlString);
                    $xpath = new DOMXPath($doc);
                    $xpath_resultset =  $xpath->query("//div[@class='sidebar-1']");

                    $htmlString = $doc->saveHTML($xpath_resultset->item(0));


                    if (strlen($htmlString) < 200 || str_contains($htmlString, 'alt="VnExpress"')) {
                        wp_update_post(array(
                            'ID'    => get_the_ID(),
                            'post_status'   =>  'draft'
                        ));
                        return new WP_Error('no_posts', __('No post found'), array('status' => 404));
                    }


                    $pattern = '/<img[^>]+data-src="([^"]+)"[^>]*>/i';
                    $htmlString = preg_replace($pattern, '<img src="$1">', $htmlString);

                    // $title = $xpath->evaluate('//div[@class="sidebar-1"]//h1[@class="title-detail"]')[0]->textContent;
                    // $titles = $xpath->evaluate('//div[@class="sidebar-1"]');
                    // // $prices = $xpath->evaluate('//ol[@class="row"]//li//article//div[@class="product_price"]//p[@class="price_color"]');

                    // echo '<pre>';
                    // print_r($titles[0]->textContent);
                    // die;


                } catch (\Throwable $th) {
                }



                //Get content without caption
                $results['data'] = [
                    'title' => $getTitle,
                    'slug' => get_post_field('post_name', get_the_ID()),
                    'content' => $htmlString,
                    'urlToImage' => get_post_meta(get_the_ID(), 'wprss_item_permalink', true),
                    'date' => date('d/m/Y H:i', strtotime('+7 hours', strtotime(get_the_date('Y/m/d H:i')))),
                ];
            }

            wp_reset_postdata();
        } else {
            return new WP_Error('no_posts', __('No post found'), array('status' => 404));
        }
        return new WP_REST_Response($results, 200);
    }

    public function callNews($request)
    {


        $page = $_GET['page'];
        $results = [];

        $args = array(
            'post_type' => POST_TYPE_FEED,
            'post_status' => array('publish'),
            'posts_per_page' => -1,
        );
        $postsTotal = new WP_Query($args);

        $results['total'] = $postsTotal->found_posts;

        $args = array(
            'post_type' => POST_TYPE_FEED,
            'post_status' => array('publish'),
            'posts_per_page' => 5,
            'paged' => $page,
            'orderby'   => array(
                'date' => 'DESC',
            )
        );

        $posts = new WP_Query($args);

        if ($posts->have_posts()) {
            $results['code'] = 'success';
            while ($posts->have_posts()) {

                $posts->the_post();
                $getTitle =  get_the_title();
                $content = get_the_content();
                preg_match("/\<img.+src\=(?:\"|\')(.+?)(?:\"|\')(?:.+?)\>/", $content, $image);
                $image = str_replace('amp;', '', $image);
                preg_match("/<\/br>(.+)/", $content, $contentFormat);
                //Get content without caption
                $meeting_time = strtotime('+7 hours', time()) - strtotime('+7 hours', strtotime(get_the_date('Y/m/d H:i:s')));
                $hours = floor($meeting_time / 3600); // Tính số giờ
                if ($hours > 0) {
                    $hours = "Hơn 1 giờ";
                } else {
                    $hours = "";
                }
                $minutes = floor(($meeting_time % 3600) / 60); // Tính số phút
                $results['data'][] = [
                    'title' => $getTitle,
                    'slug' => get_post_field('post_name', get_the_ID()),
                    'type' => get_post_field('post_type', get_the_ID()),
                    'content' =>  get_post_field('post_excerpt', get_the_ID()),
                    'urlToImage' => (isset($image[1])) ? $image[1] : "",
                    'date' => ($hours) ? $hours : $minutes . " phút trước",
                ];
            }

            wp_reset_postdata();
        } else {
            return new WP_Error('no_posts', __('No post found'), array('status' => 404));
        }


        return new WP_REST_Response($results, 200);
    }
    public function callNewsPopular($request)
    {




        $results = [];
        $args = array(
            'post_type' => POST_TYPE_FEED,
            'post_status' => array('publish'),
            'posts_per_page' => 3,
            'orderby'        => 'rand',
        );



        $posts = new WP_Query($args);

        if ($posts->have_posts()) {
            $results['code'] = 'success';
            while ($posts->have_posts()) {

                $posts->the_post();
                $getTitle =  get_the_title();
                $content = get_the_content();
                preg_match("/\<img.+src\=(?:\"|\')(.+?)(?:\"|\')(?:.+?)\>/", $content, $image);
                $image = str_replace('amp;', '', $image);
                preg_match("/<\/br>(.+)/", $content, $contentFormat);
                //Get content without caption
                $meeting_time = strtotime('+7 hours', time()) - strtotime('+7 hours', strtotime(get_the_date('Y/m/d H:i:s')));
                $hours = floor($meeting_time / 3600); // Tính số giờ
                if ($hours > 0) {
                    $hours = "Hơn 1 giờ";
                } else {
                    $hours = "";
                }
                $minutes = floor(($meeting_time % 3600) / 60); // Tính số phút
                $results['data'][] = [
                    'title' => $getTitle,
                    'slug' => get_post_field('post_name', get_the_ID()),
                    'type' => get_post_field('post_type', get_the_ID()),
                    'content' => (isset($contentFormat[1])) ? $contentFormat[1] : $content,
                    'urlToImage' => (isset($image[1])) ? $image[1] : "",
                    'date' => ($hours) ? $hours : $minutes . " phút trước",
                ];
            }

            wp_reset_postdata();
        } else {
            return new WP_Error('no_posts', __('No post found'), array('status' => 404));
        }
        return new WP_REST_Response($results, 200);
    }
    public function getPostPopular($request)
    {
        $results = [];
        $args = array(
            'post_type' => POST_TYPE,
            'post_status' => array('publish'),
            'posts_per_page' => 3,
            'orderby'        => 'rand',


        );
        $posts = new WP_Query($args);
        if ($posts->have_posts()) {
            $results['code'] = 'success';
            while ($posts->have_posts()) {
                $posts->the_post();
                $getTitle =  get_the_title();


                //Get content without caption
                $results['data'][] = [
                    'title' => $getTitle,
                    'slug' => get_post_field('post_name', get_the_ID()),
                    'content' => get_the_content(),
                    'short_description' => get_post_meta(get_the_ID(), 'post_summary', true),
                    'urlToImage' => get_post_meta(get_the_ID(), 'post_images_icon', true),
                    'date' => get_the_date('Y/m/d'),
                ];
            }

            wp_reset_postdata();
        } else {
            return new WP_Error('no_posts', __('No post found'), array('status' => 404));
        }
        return new WP_REST_Response($results, 200);
    }
    public function getPost($request)
    {
        $results = [];
        $args = array(
            'post_type' => POST_TYPE,
            'post_status' => array('publish'),
        );
        $posts = new WP_Query($args);
        if ($posts->have_posts()) {
            $results['code'] = 'success';
            while ($posts->have_posts()) {
                $posts->the_post();
                $getTitle =  get_the_title();


                //Get content without caption
                $results['data'][] = [
                    'title' => $getTitle,
                    'slug' => get_post_field('post_name', get_the_ID()),
                    'content' => get_the_content(),
                    'short_description' => get_post_meta(get_the_ID(), 'post_summary', true),
                    'urlToImage' => get_post_meta(get_the_ID(), 'post_images_icon', true),
                    'date' => get_the_date('Y/m/d'),
                ];
            }

            wp_reset_postdata();
        } else {
            return new WP_Error('no_posts', __('No post found'), array('status' => 404));
        }
        return new WP_REST_Response($results, 200);
    }
    public function getPostDetail($request)
    {



        $results = [];
        $args = array(
            'post_type' => POST_TYPE,
            'post_status' => array('publish'),
            'name' => $request['post_slug']

        );
        $posts = new WP_Query($args);


        if ($posts->have_posts()) {
            $results['code'] = 'success';
            while ($posts->have_posts()) {
                $posts->the_post();
                $getTitle =  get_the_title();

                //Get content without caption
                $results['data'] = [
                    'title' => $getTitle,
                    'slug' => get_post_field('post_name', get_the_ID()),
                    'content' => get_the_content(),
                    'urlToImage' => get_post_meta(get_the_ID(), 'post_images_icon', true),
                    'date' => get_the_date('Y/m/d'),
                ];
            }

            wp_reset_postdata();
        } else {
            return new WP_Error('no_posts', __('No post found'), array('status' => 404));
        }
        return new WP_REST_Response($results, 200);
    }

    public function getSize($image)
    {


        return number_format((int)filesize($image) / 1024, 2, '.', '') . 'KB';
    }
    public function getObjectSize($image, $imageName)
    {
        $result['oldSize'] = number_format((int)$_FILES['file']['size'] / 1024, 2, '.', '') . 'KB';
        // $result['newSize'] = $this->getSize('file/compress_' . $imageName);
        $result['newSize'] = $this->getSize($imageName);
        return $result;
    }
    public function resizeImage($image, $imageName, $outputQuality)
    {
        try {
            //code...
            $result = [];

            $imageInfo = getimagesize($image);


            $result['oldSize'] = $this->getSize($image);
            $result['mime'] = $imageInfo['mime'];


            if ($imageInfo['mime'] == 'image/gif') {

                $imageLayer = imagecreatefromgif($image);
            } elseif ($imageInfo['mime'] == 'image/jpeg') {

                $imageLayer = imagecreatefromjpeg($image);
            } elseif ($imageInfo['mime'] == 'image/png') {

                $imageLayer = imagecreatefrompng($image);
            }
           
            $compressedImage = imagejpeg($imageLayer, 'file/compress_' . $imageName, $outputQuality);
            

            if ($compressedImage) {

                $result['newSize'] = $this->getSize('file/compress_' . $imageName);
                $result['percent'] = '-' . number_format(100 - ((int)$result['newSize'] * 100 / (int)$result['oldSize']), 2, '.', '') . "%";
                return $result;
                exit;
            } else {
                return 'An error occured!';
                exit;
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function urlPathFile($fileName, $type)
    {
        $fileName = explode('.', $fileName)[0];
        if (isset($_SERVER['HTTPS'])) {
            $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
        } else {
            $protocol = 'http';
        }
        return $protocol . "://" . $_SERVER['HTTP_HOST'] . "/" . "wp-json/convert/v1/get/$fileName?type=$type";
    }
    public function formatFile($request)
    {
        try {
            set_error_handler(function ($severity, $message, $file, $line) {
                throw new \ErrorException($message, 0, $severity, $file, $line);
            });
            $file = $_FILES["file"];
            $file['name'] = time() . "_" . rand(1, 9999) . "_" . str_replace(['(', ')', ' ',], '', $file['name']);
            $lastDot = strrpos($file['name'], ".");
            $file['name'] = str_replace(".", "", substr($file['name'], 0, $lastDot)) . substr($file['name'], $lastDot);
            $tempFilePath = $file['tmp_name'];
            $to = $_POST['to'];
            $targetDirectory = './file/';
            $currentFloderDomain = 'file/';

            if (!is_dir($targetDirectory)) {
                mkdir($targetDirectory, 0777, true);
            }

            
            
            if (mime_content_type($tempFilePath) == "image/jpeg") {
                switch ($to) {
                    case "png":
                        $output = str_replace([".jpg", ".jpeg"], "", $file['name']) . ".png";
                        $jpegImage = imagecreatefromjpeg($tempFilePath);
                        // Create a new blank PNG image
                        $width = imagesx($jpegImage);
                        $height = imagesy($jpegImage);
                        $pngImage = imagecreatetruecolor($width, $height);
                        $whiteColor = imagecolorallocate($pngImage, 255, 255, 255);
                        imagefill($pngImage, 0, 0, $whiteColor);
                        imagecopy($pngImage, $jpegImage, 0, 0, 0, 0, $width, $height);

                        $tempPngFilePath =  $currentFloderDomain . $output;
                        imagepng($pngImage, $tempPngFilePath);
                        // Clean up memory
                        imagedestroy($jpegImage);
                        imagedestroy($pngImage);
                        $result = $this->getObjectSize($tempFilePath, 'file/' . $output);
                        echo json_encode(array("success" => true, "message" => $this->urlPathFile('file/' . $output, 'png'), 'data' => json_encode($result)));
                        break;
                    case 'gif':
                        $outputGif = str_replace([".jpg", ".jpeg"], "", $file['name']) . ".gif";
                        $jpegImage = imagecreatefromjpeg($tempFilePath);
                        $width = imagesx($jpegImage);
                        $height = imagesy($jpegImage);
                        $gifImage = imagecreatetruecolor($width, $height);
                        $whiteColor = imagecolorallocate($gifImage, 255, 255, 255);
                        imagefill($gifImage, 0, 0, $whiteColor);
                        imagecopy($gifImage, $jpegImage, 0, 0, 0, 0, $width, $height);
                        $gifFilePath =  $currentFloderDomain . $outputGif;
                        imagegif($gifImage, $gifFilePath);
                        imagedestroy($jpegImage);
                        imagedestroy($gifImage);
                        $result = $this->getObjectSize($tempFilePath, 'file/' . $outputGif);

                        echo json_encode(array("success" => true, "message" => $this->urlPathFile('file/' . $outputGif, 'gif'), 'data' => json_encode($result)));                    // echo $gifFilePath;
                        break;
                    case 'pdf':
                        require('fpdf/fpdf.php');
                        $output = str_replace([".jpg", ".png", ".jpeg"], "", $file['name']) . ".pdf";

                        $jpegFilePath = $tempFilePath;
                        $pdfFilePath = $currentFloderDomain . $output;

                        $pdf = new FPDF();
                        $pdf->AddPage();

                        $pdf->SetAutoPageBreak(true, 10);
                        $pdf->Image($jpegFilePath, 10, 10, 190, 0, 'JPEG');

                        $pdf->Output($pdfFilePath, 'F');
                        $result = $this->getObjectSize($tempFilePath, 'file/' . $output);

                        echo json_encode(array("success" => true, "message" => $this->urlPathFile('file/' . $output, 'pdf'), 'data' => json_encode($result)));
                        break;
                    case 'jpg':
                        $outputJpg = $currentFloderDomain . str_replace([".png", ".jpeg"], "", $file['name']) . ".jpg";
                        rename($tempFilePath, $outputJpg);
                        $result = $this->getObjectSize($tempFilePath, $outputJpg);
                        echo json_encode(array("success" => true, "message" => $this->urlPathFile($outputJpg, 'jpg'), 'data' => json_encode($result)));
                        break;
                    case 'jpeg':
                        $outputJpeg = $currentFloderDomain . str_replace([".png", ".jpg"], "", $file['name']) . ".jpeg";
                        rename($tempFilePath, $outputJpeg);

                        $result = $this->getObjectSize($tempFilePath, $outputJpeg);


                        echo json_encode(array("success" => true, "message" => $this->urlPathFile($outputJpeg, 'jpeg'), 'data' => json_encode($result)));
                        break;
                    case "ico":
                        require('php-ico/class-php-ico.php');
                        $output = str_replace([".jpg", ".jpeg"], "", $file['name']) . ".ico";
                        $tempIcoFilePath =  $currentFloderDomain . $output;
                        $ico_lib = new PHP_ICO($tempFilePath);
                        $ico_lib->save_ico($tempIcoFilePath);
                        $result = $this->getObjectSize($tempFilePath, 'file/' . $output);

                        echo json_encode(array("success" => true, "message" => $this->urlPathFile('file/' . $output, 'ico'), 'data' => json_encode($result)));
                        break;
                    case "tinyPNG":
                        $sourceImg = $tempFilePath;
                        $fileName = $file['name'];
                        $arr = explode('.', $file['name']);
                        
                        $d = $this->resizeImage($sourceImg, $fileName, 50);
                        echo json_encode(array("success" => true, "message" => $this->urlPathFile('file/compress_' . $fileName, $arr[count($arr) - 1]), 'data' => json_encode($d)));
                        break;
                    default:
                        echo json_encode(array("error" => "Failed to load file."));
                }
            } else if (mime_content_type($tempFilePath) == "image/png") {

                switch ($to) {
                    case "jpeg":


                        $output = str_replace(".png", "", $file['name']) . ".jpeg";
                        $pngImage = imagecreatefrompng($tempFilePath);
                        // Create a new blank PNG image
                        $width = imagesx($pngImage);
                        $height = imagesy($pngImage);
                        $jpegImage = imagecreatetruecolor($width, $height);
                        $whiteColor = imagecolorallocate($pngImage, 255, 255, 255);
                        imagefill($jpegImage, 0, 0, $whiteColor);
                        imagecopy($jpegImage, $pngImage, 0, 0, 0, 0, $width, $height);

                        $tempJpegFilePath =  $currentFloderDomain . $output;
                        imagejpeg($jpegImage, $tempJpegFilePath, 90);

                        imagedestroy($pngImage);
                        imagedestroy($jpegImage);
                        $result = $this->getObjectSize($tempFilePath, 'file/' . $output);

                        echo json_encode(array("success" => true, "message" => $this->urlPathFile('file/' .  $output, 'jpeg'), 'data' => json_encode($result)));
                        break;
                    case "jpg":

                        $output = str_replace(".png", "", $file['name']) . ".jpg";
                        $pngImage = imagecreatefrompng($tempFilePath);
                        // Create a new blank PNG image
                        $width = imagesx($pngImage);
                        $height = imagesy($pngImage);
                        $jpegImage = imagecreatetruecolor($width, $height);
                        $whiteColor = imagecolorallocate($pngImage, 255, 255, 255);
                        imagefill($jpegImage, 0, 0, $whiteColor);
                        imagecopy($jpegImage, $pngImage, 0, 0, 0, 0, $width, $height);

                        $tempJpgFilePath =  $currentFloderDomain . $output;
                        imagejpeg($jpegImage, $tempJpgFilePath, 90);

                        imagedestroy($pngImage);
                        imagedestroy($jpegImage);
                        $result = $this->getObjectSize($tempFilePath, 'file/' . $output);

                        echo json_encode(array("success" => true, "message" => $this->urlPathFile('file/' . $output, 'jpg'), 'data' => json_encode($result)));
                        break;
                    case 'pdf':
                        require('fpdf/fpdf.php');
                        $output = str_replace(".png", "", $file['name']) . ".pdf";
                        $pngFilePath = $tempFilePath;
                        $pdfFilePath = $currentFloderDomain . $output;

                        $pdf = new FPDF();
                        $pdf->AddPage();

                        $pdf->SetAutoPageBreak(true, 10);
                        $pdf->Image($pngFilePath, 10, 10, 190, 0, 'PNG');

                        $pdf->Output($pdfFilePath, 'F');
                        $result = $this->getObjectSize($tempFilePath, 'file/' . $output);

                        echo json_encode(array("success" => true, "message" => $this->urlPathFile('file/' . $output, 'pdf'), 'data' => json_encode($result)));
                        break;
                        // case "ico":
                        //     require('php-ico/class-php-ico.php');
                        //     $output = str_replace([".png"], "", $file['name']) . ".ico";
                        //     $tempIcoFilePath =  $currentFloderDomain . $output;
                        //     $ico_lib = new PHP_ICO($tempFilePath);
                        //     $ico_lib->save_ico($tempIcoFilePath);

                        //     $result = $this->getObjectSize($tempFilePath, 'file/' . $output);

                        //     echo json_encode(array("success" => true, "message" => $this->urlPathFile( $output,'ico'), 'data' => json_encode($result)));
                        //     break;
                    case "tinyPNG":
                        $sourceImg = $tempFilePath;
                        $fileName = $file['name'];

                        $d = $this->resizeImage($sourceImg, $fileName, 50);


                        echo json_encode(array("success" => true, "message" => $this->urlPathFile('file/compress_' . $fileName, 'png'), 'data' => json_encode($d)));
                        break;
                    default:
                        echo json_encode(array("error" => "Failed to load file."));
                }
            } else {
                $imageInfo = getimagesize($tempFilePath);
                move_uploaded_file($tempFilePath, 'file/' . $file['name']);

                $result['oldSize'] = $this->getSize($tempFilePath);
                $result['mime'] = $imageInfo['mime'];

                $result['newSize'] =  $result['oldSize'];
                $result['percent'] = "-0%";
                echo json_encode(array("success" => true, "message" => $this->urlPathFile('file/' . $file['name'], $arr[count($arr) - 1]), 'data' => json_encode($result)));
            }
        } catch (Exception $e) {
            echo '<pre>';
            print_r($e);
            die;

            echo json_encode(array("error" => "Failed to load convert. Please try again."));
        } finally {
            restore_error_handler();
        }
    }
}

add_action('rest_api_init', function () {
    $shareController = new PostController();
    $shareController->registerRoutes();
});
