<?php

class PostController extends WP_REST_Controller
{
    private $nameSpace = API_NAME . '/v1';
    public function registerRoutes()
    {
        register_rest_route($this->nameSpace, 'top', array(
            array(
                'methods' => 'GET',
                'callback' => array($this, 'getTop')
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
        register_rest_route($this->nameSpace, 'post/(?P<post_slug>[a-zA-Z0-9-_]+)', array(
            array(
                'methods' => 'GET',
                'callback' => array($this, 'getPostDetail')
            ),
        ));
        
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
                    'thumbnail' => get_post_meta(get_the_ID(), 'post_images_icon', true),
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
                    'thumbnail' => get_post_meta(get_the_ID(), 'post_images_icon', true),
                    'date' => get_the_date('Y/m/d'),
                ];
            }

            wp_reset_postdata();
        } else {
            return new WP_Error('no_posts', __('No post found'), array('status' => 404));
        }
        return new WP_REST_Response($results, 200);
    }
    // public function getCategory($request)
    // {

    //     $results = [];


    //     $queryParams = $request->get_query_params();
    //     //Pagination param
    //     $page = 1;
    //     $postPerPage = (int)get_option('posts_per_page');
    //     if (isset($queryParams['page']) && $queryParams['page'] > 1) {
    //         $page = (int)$queryParams['page'];
    //     }
    //     //Get Post of category
    //     $args = array(
    //         'post_type' => POST_TYPE,
    //         'post_status' => array('publish'),
    //         'order' => 'DESC',
    //         'category_name' => $request['category'],
    //         'posts_per_page' => $postPerPage,
    //         'paged' => $page,
    //     );


    //     //Get data for glossary
    //     $posts = new WP_Query($args);
    //     if ($posts->have_posts()) {

    //         $results['code'] = 'success';
    //         $key = 0;
    //         // Set default data null
    //         while ($posts->have_posts()) {
    //             $posts->the_post();
    //             $getTitle =  get_the_title();
    //             $category_detail=get_the_category(get_the_ID());//$post->ID
    //             //Get content without caption
    //             $results['data'][$key] = [
    //                 'title' => $getTitle,
    //                 'slug' => get_post_field('post_name', get_the_ID()),
    //                 'location' =>  get_post_meta(get_the_ID(), KEY_SUMMARY . '_location', true),
    //                 'thumbnail' => has_post_thumbnail() ? get_the_post_thumbnail_url() : '',
    //                 'slug_category' => (!empty($category_detail))?$category_detail[0]->slug:"",
    //                 'date' => get_the_date('Y/m/d')
    //             ];

    //             $key++;
    //         }
    //         //Pagination data
    //         $results['pagination'] = [
    //             'current_page' => $page,
    //             'total' => (int)$posts->found_posts,
    //             'post_per_page' => $postPerPage,
    //         ];
    //         wp_reset_postdata();
    //         return new WP_REST_Response($results, 200);
    //     } else {
    //         return new WP_Error('no_posts', __('No post found'), array('status' => 404));
    //     }
    // }

    // public function search($request)
    // {

    //     $results = [];
    //     $search = (isset($_GET['s'])) ? $_GET['s'] : "";

    //     $queryParams = $request->get_query_params();
    //     //Pagination param
    //     $page = 1;
    //     $postPerPage = (int)get_option('posts_per_page');
    //     if (isset($queryParams['page']) && $queryParams['page'] > 1) {
    //         $page = (int)$queryParams['page'];
    //     }

    //     //Get Post of category
    //     $args = array(
    //         'post_type' => POST_TYPE,
    //         'post_status' => array('publish'),
    //         'order' => 'DESC',
    //         'category_name' => 'project',
    //         's' => $search,
    //         'posts_per_page' => $postPerPage,
    //         'paged' => $page,
    //     );


    //     //Get data for glossary
    //     $posts = new WP_Query($args);

    //     if ($posts->have_posts()) {

    //         $results['code'] = 'success';
    //         $key = 0;
    //         // Set default data null
    //         while ($posts->have_posts()) {
    //             $posts->the_post();
    //             $getTitle =  get_the_title();
    //             $category_detail=get_the_category(get_the_ID());//$post->ID


    //             //Get content without caption
    //             $results['data'][$key] = [
    //                 'title' => $getTitle,
    //                 'slug' => get_post_field('post_name', get_the_ID()),
    //                 'location' =>  get_post_meta(get_the_ID(), KEY_SUMMARY . '_location', true),
    //                 'thumbnail' => has_post_thumbnail() ? get_the_post_thumbnail_url() : '',
    //                 'slug_category' => (!empty($category_detail))?$category_detail[0]->slug:"",
    //                 'date' => get_the_date('Y/m/d')

    //             ];

    //             $key++;
    //         }
    //         //Pagination data
    //         $results['pagination'] = [
    //             'current_page' => $page,
    //             'total' => (int)$posts->found_posts,
    //             'post_per_page' => $postPerPage,
    //         ];
    //         wp_reset_postdata();

    //         return new WP_REST_Response($results, 200);
    //     } else {
    //         return new WP_Error('no_posts', __('No post found'), array('status' => 404));
    //     }
    // }
    public function getSize($image)
    {
        return number_format((int)filesize($image) / 1024, 2, '.', '') . 'KB';
    }
    public function getObjectSize($image, $imageName)
    {
        $result['oldSize'] = $this->getSize($image);
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
    public function urlPathFile()
    {

        if (isset($_SERVER['HTTPS'])) {
            $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
        } else {
            $protocol = 'http';
        }
        return $protocol . "://" . $_SERVER['HTTP_HOST'] . "/" . "file/";
    }
    public function formatFile($request)
    {
        try {
            set_error_handler(function ($severity, $message, $file, $line) {
                throw new \ErrorException($message, 0, $severity, $file, $line);
            });
            $file = $_FILES["file"];
            $file['name'] = time() . "_" . rand(1, 9999) . "_" . $file['name'];
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
                        echo json_encode(array("success" => true, "message" => $this->urlPathFile() . $output, 'data' => json_encode($result)));
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

                        echo json_encode(array("success" => true, "message" => $this->urlPathFile() . $outputGif, 'data' => json_encode($result)));                    // echo $gifFilePath;
                        break;
                    case 'pdf':
                        require('fpdf/fpdf.php');
                        $output = str_replace([".jpg", ".jpeg"], "", $file['name']) . ".pdf";

                        $jpegFilePath = $tempFilePath;
                        $pdfFilePath = $currentFloderDomain . $output;

                        $pdf = new FPDF();
                        $pdf->AddPage();

                        $pdf->SetAutoPageBreak(true, 10);
                        $pdf->Image($jpegFilePath, 10, 10, 190, 0, 'JPEG');

                        $pdf->Output($pdfFilePath, 'F');
                        $result = $this->getObjectSize($tempFilePath, 'file/' . $output);

                        echo json_encode(array("success" => true, "message" => $this->urlPathFile() . $output, 'data' => json_encode($result)));
                        break;
                    case 'jpg':
                        $outputJpg = $currentFloderDomain . str_replace(".jpeg", "", $file['name']) . ".jpg";
                        rename($tempFilePath, $outputJpg);

                        $result = $this->getObjectSize($tempFilePath, $outputJpg);

                        echo json_encode(array("success" => true, "message" => $this->urlPathFile() . $outputJpg));
                        break;
                    case 'jpeg':
                        $outputJpeg = $currentFloderDomain . str_replace(".jpg", "", $file['name']) . ".jpeg";
                        rename($tempFilePath, $outputJpeg);


                        $result = $this->getObjectSize($tempFilePath, $outputJpeg);

                        echo json_encode(array("success" => true, "message" => $this->urlPathFile() . $outputJpeg, 'data' => json_encode($result)));
                        break;
                    case "ico":
                        require('php-ico/class-php-ico.php');
                        $output = str_replace([".jpg", ".jpeg"], "", $file['name']) . ".ico";
                        $tempIcoFilePath =  $currentFloderDomain . $output;
                        $ico_lib = new PHP_ICO($tempFilePath);
                        $ico_lib->save_ico($tempIcoFilePath);
                        $result = $this->getObjectSize($tempFilePath, 'file/' . $output);

                        echo json_encode(array("success" => true, "message" => $this->urlPathFile() . $output, 'data' => json_encode($result)));
                        break;
                    case "tinyPNG":
                        $sourceImg = $tempFilePath;
                        $fileName = $file['name'];

                        $d = $this->resizeImage($sourceImg, $fileName, 50);


                        echo json_encode(array("success" => true, "message" => $this->urlPathFile() . 'compress_' . $fileName, 'data' => json_encode($d)));
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

                        echo json_encode(array("success" => true, "message" => $this->urlPathFile() . $output, 'data' => json_encode($result)));
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

                        echo json_encode(array("success" => true, "message" => $this->urlPathFile() . $output, 'data' => json_encode($result)));
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

                        echo json_encode(array("success" => true, "message" => $this->urlPathFile() . $output, 'data' => json_encode($result)));
                        break;
                    case "ico":
                        require('php-ico/class-php-ico.php');
                        $output = str_replace([".png"], "", $file['name']) . ".ico";
                        $tempIcoFilePath =  $currentFloderDomain . $output;
                        $ico_lib = new PHP_ICO($tempFilePath);
                        $ico_lib->save_ico($tempIcoFilePath);
                        $result = $this->getObjectSize($tempFilePath, 'file/' . $output);
                        echo json_encode(array("success" => true, "message" => $this->urlPathFile() . $output, 'data' => json_encode($result)));
                        break;
                    case "tinyPNG":
                        $sourceImg = $tempFilePath;
                        $fileName = $file['name'];

                        $d = $this->resizeImage($sourceImg, $fileName, 50);


                        echo json_encode(array("success" => true, "message" => $this->urlPathFile() . 'compress_' . $fileName, 'data' => json_encode($d)));
                        break;
                    default:
                        echo json_encode(array("error" => "Failed to load file."));
                }
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
