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
        register_rest_route($this->nameSpace, 'about', array(
            array(
                'methods' => 'GET',
                'callback' => array($this, 'getAbout')
            ),
        ));
        register_rest_route($this->nameSpace, 'posts-category/(?P<category>[a-zA-Z0-9-_]+)', array(
            array(
                'methods' => 'GET',
                'callback' => array($this, 'getCategory')
            ),
        ));
        register_rest_route($this->nameSpace, 'post/(?P<post_slug>[a-zA-Z0-9-_]+)', array(
            array(
                'methods' => 'GET',
                'callback' => array($this, 'getPost')
            ),
        ));
        register_rest_route($this->nameSpace, 'search', array(
            array(
                'methods' => 'GET',
                'callback' => array($this, 'search')
            ),
        ));
        register_rest_route($this->nameSpace, 'send', array(
            array(
                'methods' => 'POST',
                'callback' => array($this, 'send')
            ),
        ));
    }
    public function send()
    {
  
        $data = json_decode(file_get_contents('php://input'), true);
     
        $emailForm = (isset($data['to'])) ? $data['to'] : "";
        if (!filter_var($emailForm, FILTER_VALIDATE_EMAIL)) {
            return new WP_Error('Mail contact', __('Invalid email format'), array('status' => 500));
        }
        $emailTo = get_option('mail_from_name',true);
        $fullname = (isset($data['fullname'])) ? $data['fullname'] : "";
        $phone = (isset($data['phone'])) ? $data['phone'] : "";
        $company = (isset($data['company'])) ? $data['company'] : "";
        $content = (isset($data['content'])) ? $data['content'] : "";
        $subject = 'Liên hệ được gửi tử email: ' . $emailForm;
        $message = "<p>Fullname: $fullname</p>
        <p>Phone: $phone</p>
        <p>Company: $company</p>
        <p>Content: $content</p>";


        // Set SMTPDebug to true
        //$phpmailer->SMTPDebug = true;

        // Start output buffering to grab smtp debugging output
        //ob_start();

        // Send the test mail
        $result = wp_mail($emailTo, $subject, $message);
        $results = [];

        $results['code'] = 'success';
        $results['data'] = new stdClass;

        return new WP_REST_Response($results, 200);
    }
    public function getPost($request)
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
                $listImage = get_post_meta(get_the_ID(), KEY_LIST_IMAGES . '_list', true);

                //Get content without caption
                $results['data'] = [
                    'title' => $getTitle,
                    'slug' => get_post_field('post_name', get_the_ID()),
                    'template' => (int)get_post_meta(get_the_ID(), KEY_TEMPLATE_SERVICE . '_id', true),
                    'content' => get_the_content(),
                    'thumbnail' => has_post_thumbnail() ? get_the_post_thumbnail_url() : '',
                    'date' => get_the_date('Y/m/d'),
                ];
                
                $images = (is_array($listImage))?array_values($listImage):[];
                $results['data']['images'] = $images;
            }
            
            wp_reset_postdata();
        } else {
            return new WP_Error('no_posts', __('No post found'), array('status' => 404));
        }
        return new WP_REST_Response($results, 200);
    }
    public function getCategory($request)
    {

        $results = [];


        $queryParams = $request->get_query_params();
        //Pagination param
        $page = 1;
        $postPerPage = (int)get_option('posts_per_page');
        if (isset($queryParams['page']) && $queryParams['page'] > 1) {
            $page = (int)$queryParams['page'];
        }
        //Get Post of category
        $args = array(
            'post_type' => POST_TYPE,
            'post_status' => array('publish'),
            'order' => 'DESC',
            'category_name' => $request['category'],
            'posts_per_page' => $postPerPage,
            'paged' => $page,
        );


        //Get data for glossary
        $posts = new WP_Query($args);
        if ($posts->have_posts()) {

            $results['code'] = 'success';
            $key = 0;
            // Set default data null
            while ($posts->have_posts()) {
                $posts->the_post();
                $getTitle =  get_the_title();
                $category_detail=get_the_category(get_the_ID());//$post->ID
                //Get content without caption
                $results['data'][$key] = [
                    'title' => $getTitle,
                    'slug' => get_post_field('post_name', get_the_ID()),
                    'location' =>  get_post_meta(get_the_ID(), KEY_SUMMARY . '_location', true),
                    'thumbnail' => has_post_thumbnail() ? get_the_post_thumbnail_url() : '',
                    'slug_category' => (!empty($category_detail))?$category_detail[0]->slug:"",
                    'date' => get_the_date('Y/m/d')
                ];

                $key++;
            }
            //Pagination data
            $results['pagination'] = [
                'current_page' => $page,
                'total' => (int)$posts->found_posts,
                'post_per_page' => $postPerPage,
            ];
            wp_reset_postdata();
            return new WP_REST_Response($results, 200);
        } else {
            return new WP_Error('no_posts', __('No post found'), array('status' => 404));
        }
    }
    
    public function search($request)
    {

        $results = [];
        $search = (isset($_GET['s'])) ? $_GET['s'] : "";

        $queryParams = $request->get_query_params();
        //Pagination param
        $page = 1;
        $postPerPage = (int)get_option('posts_per_page');
        if (isset($queryParams['page']) && $queryParams['page'] > 1) {
            $page = (int)$queryParams['page'];
        }

        //Get Post of category
        $args = array(
            'post_type' => POST_TYPE,
            'post_status' => array('publish'),
            'order' => 'DESC',
            'category_name' => 'project',
            's' => $search,
            'posts_per_page' => $postPerPage,
            'paged' => $page,
        );


        //Get data for glossary
        $posts = new WP_Query($args);
        
        if ($posts->have_posts()) {

            $results['code'] = 'success';
            $key = 0;
            // Set default data null
            while ($posts->have_posts()) {
                $posts->the_post();
                $getTitle =  get_the_title();
                $category_detail=get_the_category(get_the_ID());//$post->ID


                //Get content without caption
                $results['data'][$key] = [
                    'title' => $getTitle,
                    'slug' => get_post_field('post_name', get_the_ID()),
                    'location' =>  get_post_meta(get_the_ID(), KEY_SUMMARY . '_location', true),
                    'thumbnail' => has_post_thumbnail() ? get_the_post_thumbnail_url() : '',
                    'slug_category' => (!empty($category_detail))?$category_detail[0]->slug:"",
                    'date' => get_the_date('Y/m/d')

                ];

                $key++;
            }
            //Pagination data
            $results['pagination'] = [
                'current_page' => $page,
                'total' => (int)$posts->found_posts,
                'post_per_page' => $postPerPage,
            ];
            wp_reset_postdata();
            
            return new WP_REST_Response($results, 200);
        } else {
            return new WP_Error('no_posts', __('No post found'), array('status' => 404));
        }
    }
    public function getTop()
    {
        $language = getLanguageId(qtranxf_getLanguage());
        $topPage = get_post_meta(getTopPageId());
        $data = [];
        if (isset($topPage['banner_slide_top_post'])) {
            $slideTops = [];
            $tempSlideTop = [];
            $listSlideTop = unserialize($topPage['banner_slide_top_post'][0]);
            $listSlideTop = array_slice($listSlideTop, 0, 10);
            $listSlideTops = get_posts([
                'include' => $listSlideTop,
                'order' => 'ASC',
                'orderby' => 'post__in'
            ]);
            foreach ($listSlideTops as $key => $val) {
                if (isset($val->ID)) {
                    $tempSlideTop[$val->ID] = $val;
                }
            }


            foreach ($listSlideTop as $val) {
                if (isset($tempSlideTop[$val])) {
                    $slideTops[] = $tempSlideTop[$val];
                }
            }
            foreach ($slideTops as $keySlideTop => $post) {
                $image_pc = has_post_thumbnail($post->ID) ? get_the_post_thumbnail_url($post->ID) : '';
                $tempArr = [
                    'thumbnail' => $image_pc,
                    'title' => qtranxf_use($language, $post->post_title, true, true),
                    'slug' => $post->post_name,
                ];

                $data[] = $tempArr;
            }
        }
        $results['banner'] = $data;


        $data = [];
        $data['title'] = isset($topPage['service_slide_top_title']) ?  qtranxf_use($language, $topPage['service_slide_top_title'][0], true, true) : "";

        if (isset($topPage['service_slide_top_post'])) {
            $slideTops = [];
            $tempSlideTop = [];
            $listSlideTop = unserialize($topPage['service_slide_top_post'][0]);
            $listSlideTop = array_slice($listSlideTop, 0, 10);
            $listSlideTops = get_posts([
                'include' => $listSlideTop,
                'order' => 'ASC',
                'orderby' => 'post__in'
            ]);
            foreach ($listSlideTops as $key => $val) {
                if (isset($val->ID)) {
                    $tempSlideTop[$val->ID] = $val;
                }
            }


            foreach ($listSlideTop as $val) {
                if (isset($tempSlideTop[$val])) {
                    $slideTops[] = $tempSlideTop[$val];
                }
            }
            foreach ($slideTops as $keySlideTop => $post) {
                $summary = qtranxf_use($language, get_post_meta($post->ID, 'post_summary', true), true, true);
                $image_pc = has_post_thumbnail($post->ID) ? get_the_post_thumbnail_url($post->ID) : '';
                $tempArr = [
                    'thumbnail' => $image_pc,
                    'title' => qtranxf_use($language, $post->post_title, true, true),
                    'summary' => $summary,
                    'slug' => $post->post_name,
                ];

                $data['list'][] = $tempArr;
            }
        }
        $results['service'] = $data;

        $data = [];


        $data['title'] = isset($topPage['information_slide_top_title']) ?  qtranxf_use($language, $topPage['information_slide_top_title'][0], true, true) : "";
        $data['slug'] = isset($topPage['information_slide_top_slug']) ? $topPage['information_slide_top_slug'][0] : "";
        $data['short_description'] = isset($topPage['information_slide_top_short_description']) ?  qtranxf_use($language, $topPage['information_slide_top_short_description'][0], true, true) : "";
        $data['image'] = isset($topPage['information_slide_top_image']) ? $topPage['information_slide_top_image'][0] : "";
        $results['information'] = $data;



        $data = [];
        $data['title'] = isset($topPage['project_slide_top_title']) ?  qtranxf_use($language, $topPage['project_slide_top_title'][0], true, true) : "";

        if (isset($topPage['project_slide_top_post'])) {
            $slideTops = [];
            $tempSlideTop = [];
            $listSlideTop = unserialize($topPage['project_slide_top_post'][0]);
            $listSlideTop = array_slice($listSlideTop, 0, 10);
            $listSlideTops = get_posts([
                'include' => $listSlideTop,
                'order' => 'ASC',
                'orderby' => 'post__in'
            ]);
            foreach ($listSlideTops as $key => $val) {
                if (isset($val->ID)) {
                    $tempSlideTop[$val->ID] = $val;
                }
            }


            foreach ($listSlideTop as $val) {
                if (isset($tempSlideTop[$val])) {
                    $slideTops[] = $tempSlideTop[$val];
                }
            }
            
            
            foreach ($slideTops as $keySlideTop => $post) {
                $summary = qtranxf_use($language, get_post_meta($post->ID, 'post_summary', true), true, true);
                $image_pc = has_post_thumbnail($post->ID) ? get_the_post_thumbnail_url($post->ID) : '';
                $category_detail=get_the_category($post->ID);//$post->ID
                
                $tempArr = [
                    'thumbnail' => $image_pc,
                    'title' => qtranxf_use($language, $post->post_title, true, true),
                    'summary' => $summary,
                    'slug' => $post->post_name,
                    'slug_category' => (!empty($category_detail))?$category_detail[0]->slug:"",
                ];

                $data['list'][] = $tempArr;
            }
        }
        $results['project'] = $data;


        $data = [];


        $data['title'] = isset($topPage['partner_slide_title']) ?  qtranxf_use($language, $topPage['partner_slide_title'][0], true, true) : "";

        if (isset($topPage['partner_slide_list'])) {
            $listSlidePartner = unserialize($topPage['partner_slide_list'][0]);
            $data['list'] = array_slice($listSlidePartner, 0);
        }
        $results['partner'] = $data;




        return new WP_REST_Response(['code' => 'success', 'data' => $results], 200);
    }

    public function getAbout()
    {
        $language = getLanguageId(qtranxf_getLanguage());
        $aboutPage = get_post_meta(getAboutPageId());

        $data = [];

        $data['title'] = isset($aboutPage['about_slide_top_title']) ?  qtranxf_use($language, $aboutPage['about_slide_top_title'][0], true, true) : "";
        $data['long_description'] = isset($aboutPage['about_slide_top_description_long']) ?  qtranxf_use($language, $aboutPage['about_slide_top_description_long'][0], true, true) : "";
        $data['short_description'] = isset($aboutPage['about_slide_top_description_short']) ?  qtranxf_use($language, $aboutPage['about_slide_top_description_short'][0], true, true) : "";
        $data['image'] = isset($aboutPage['about_slide_top_image']) ? $aboutPage['about_slide_top_image'][0] : "";
        $data['profile_download'] = isset($aboutPage['about_slide_top_download_profile']) ? $aboutPage['about_slide_top_download_profile'][0] : "";
        $results['header'] = $data;



        $data = [];
        if (isset($aboutPage['about_slide_special_group'])) {
            $slideTops = [];
            $tempSlideTop = [];
            $listSlideTop = unserialize($aboutPage['about_slide_special_group'][0]);


            foreach ($listSlideTop as $keySlideTop => $special) {
                $tempArr = [
                    'number' => $special['about_slide_special_number'],
                    'title' => qtranxf_use($language, $special['about_slide_special_title'], true, true),
                    'summary' => qtranxf_use($language, $special['about_slide_special_summary'], true, true),
                    'class_icon' => $special['about_slide_special_icon'],
                ];

                $data[] = $tempArr;
            }
        }
        $results['special'] = $data;

        $data = [];

        $data['title'] = isset($aboutPage['about_slide_philosophy_business_title']) ?  qtranxf_use($language, $aboutPage['about_slide_philosophy_business_title'][0], true, true) : "";
        $data['description'] = isset($aboutPage['about_slide_philosophy_business_description']) ?  qtranxf_use($language, $aboutPage['about_slide_philosophy_business_description'][0], true, true) : "";
        $data['image'] = isset($aboutPage['about_slide_philosophy_business_image']) ? $aboutPage['about_slide_philosophy_business_image'][0] : "";
        $results['philosophy_business'] = $data;


        $data = [];

        $data['title'] = isset($aboutPage['about_slide_mission_business_title']) ?  qtranxf_use($language, $aboutPage['about_slide_mission_business_title'][0], true, true) : "";
        if (isset($aboutPage['about_slide_mission_business_group'])) {
            $slideTops = [];
            $tempSlideTop = [];
            $listSlideTop = unserialize($aboutPage['about_slide_mission_business_group'][0]);


            foreach ($listSlideTop as $keySlideTop => $special) {
                $tempArr = [
                    'title' => qtranxf_use($language, $special['about_slide_mission_business_group_title'], true, true),
                    'summary' => qtranxf_use($language, $special['about_slide_mission_business_group_summary'], true, true),
                ];

                $data['list'][] = $tempArr;
            }
        }
        $results['mission_business'] = $data;


        $data = [];
        $data2 = [];

        $data['title'] = isset($aboutPage['service_slide_about_title']) ?  qtranxf_use($language, $aboutPage['service_slide_about_title'][0], true, true) : "";
        $data['thumbnail_about'] = isset($aboutPage['service_slide_about_thumbnail']) ?  $aboutPage['service_slide_about_thumbnail'][0] : "";
        if (isset($aboutPage['service_slide_about_post'])) {
            $slideTops = [];
            $tempSlideTop = [];
            $listSlideTop = unserialize($aboutPage['service_slide_about_post'][0]);


            $listSlideTop = array_slice($listSlideTop, 0, 10);
            $listSlideTops = get_posts([
                'include' => $listSlideTop,
                'order' => 'ASC',
                'orderby' => 'post__in'
            ]);
            foreach ($listSlideTops as $key => $val) {
                if (isset($val->ID)) {
                    $tempSlideTop[$val->ID] = $val;
                }
            }


            foreach ($listSlideTop as $val) {
                if (isset($tempSlideTop[$val])) {
                    $slideTops[] = $tempSlideTop[$val];
                }
            }

            foreach ($slideTops as $keySlideTop => $post) {
                $summary = qtranxf_use($language, get_post_meta($post->ID, 'post_summary', true), true, true);
                $icon = get_post_meta($post->ID, 'post_images_icon', true);

                $tempArr = [
                    'title' => qtranxf_use($language, $post->post_title, true, true),
                    'summary' => $summary,
                    'icon' => $icon,

                ];
                $listImage = get_post_meta($post->ID, KEY_LIST_IMAGES . '_list', true);
              
                
                if ($listImage) {
                    $listImage = array_values($listImage);
                } else {
                    $listImage = [];
                }

                $tempArr2 = [
                    'title' => qtranxf_use($language, $post->post_title, true, true),
                    'content' => qtranxf_use($language, $post->post_content, true, true),
                    'images' => $listImage,

                ];

                $data['list'][] = $tempArr;
                $data2[] = $tempArr2;
            }
        }
        $results['service'] = $data;
        $results['service_detail'] = $data2;



        $data = [];

        $topPage = get_post_meta(getTopPageId());

        $data['title'] = isset($topPage['partner_slide_title']) ?  qtranxf_use($language, $topPage['partner_slide_title'][0], true, true) : "";

        if (isset($topPage['partner_slide_list'])) {
            $listSlidePartner = unserialize($topPage['partner_slide_list'][0]);
            $data['list'] = array_slice($listSlidePartner, 0);
        }
        $results['partner'] = $data;
        $data = [];
        $data['title'] = isset($aboutPage['about_slide_technology_in_dochina_title']) ?  qtranxf_use($language, $aboutPage['about_slide_technology_in_dochina_title'][0], true, true) : "";
        $data['image'] = isset($aboutPage['about_slide_technology_in_dochina_image']) ?  $aboutPage['about_slide_technology_in_dochina_image'][0] : "";
        $results['technology'] = $data;



        return new WP_REST_Response(['code' => 'success', 'data' => $results], 200);
    }
}

add_action('rest_api_init', function () {
    $shareController = new PostController();
    $shareController->registerRoutes();
});
