<?php


// BDD -> elearning
// user : admin , password : admin
// user : prof , password : prof




// ENQUEUE JS + CSS
function my_theme_enqueue_styles() {
    $parent_style = 'twentysixteen-style';

    wp_register_script('script-js', '/wp-content/themes/twentysixteen-child/script.js', array('jquery'), '1.1', true);
    wp_enqueue_script('script-js');
   
    wp_enqueue_style($parent_style, get_template_directory_uri() . '/style.css');
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array($parent_style),
        wp_get_theme()->get('Version')
    );
}
add_action('wp_enqueue_scripts', 'my_theme_enqueue_styles');


// Creation de cours + INSERT dans DB + update METABOX LEARNPRESS
function processCourse() {
    if(isset($_POST['course_nonce_field']) && wp_verify_nonce($_POST['course_nonce_field'], 'course_nonce')) {
        if(strlen(trim($_POST['course_title'])) < 1 || strlen(trim($_POST['course_descr'])) < 1) {
            $redirect = add_query_arg('post', 'failed', home_url($_POST['_wp_http_referer']));

        }else{

            // Ajouter nettoyer inputs pour DB 

            // Temps local US/CANADA EAST COAST + DATE GMT + HIVER/ETE dans post_date et post_date_gmt
            $timezone = -5;             
            $date = gmdate("Y/m/j H:i:s", time() + 3600*($timezone+date("I")));
            // Temps UTC
            $gmtDate = gmdate("Y/m/d H:i:s");

            // ID de l'user current
            $user = get_current_user();
            $lp_user = learn_press_get_current_user_id();

            $course_info                =  array(
                'post_title'            => esc_attr(strip_tags($_POST['course_title'])),
                'post_type'             => 'lp_course',
                'post_content'          => esc_attr(strip_tags($_POST['course_descr'])),
                'post_status'           => 'publish',
                'post_date'             => $date,
                'post_date_gmt'         => $gmtDate,
                'post_author'           => $lp_user,
                'post_excerpt'          => esc_attr(strip_tags($_POST['excerpt']))
            );

            $course_id = wp_insert_post($course_info);

            if($course_id) {

                update_post_meta($course_id, '_lp_duration',                esc_attr($_POST['_lp_duration']));
                update_post_meta($course_id, '_lp_duration_select',         esc_attr($_POST['_lp_duration_select']));
                update_post_meta($course_id, '_lp_max_students',            esc_attr(strip_tags($_POST['_lp_max_students'])));
                update_post_meta($course_id, '_lp_price',                   esc_attr(strip_tags($_POST['_lp_price'])));
                update_post_meta($course_id, 'excerpt',                     esc_attr(strip_tags($_POST['excerpt'])));
                update_post_meta($course_id, '_lp_students',                esc_attr(strip_tags($_POST['_lp_students'])));
                update_post_meta($course_id, '_lp_retake_count',            esc_attr(strip_tags($_POST['_lp_retake_count'])));
                update_post_meta($course_id, '_lp_featured',                esc_attr(strip_tags($_POST['_lp_featured'])));
                update_post_meta($course_id, '_lp_block_lesson_content',    esc_attr(strip_tags($_POST['_lp_block_lesson_content'])));

                $redirect = add_query_arg('post', 'successfull', 'http://localhost/curriculum/');
            }

        }

        wp_redirect($redirect); 
        exit;
    }
}
add_action('init', 'processCourse');



// Creation de contenu + Insert lesson SQL
function processCurri() {
    if(isset($_POST['curri_nonce_field']) && wp_verify_nonce($_POST['curri_nonce_field'], 'curri_nonce' )){
        $l_redirect = add_query_arg('post', 'failed', home_url($_POST['_wp_http_referer']));

        global $wpdb;
        $lp_user = learn_press_get_current_user_id();
        $stmt = $wpdb->get_row( "SELECT * FROM $wpdb->posts WHERE post_status = 'publish' AND post_author = $lp_user AND post_type = 'lp_course' ORDER BY ID DESC LIMIT 0,1");
        $newCourseId = $stmt->ID;


        // **important** l'Array commence à ['section'] **important**
        $course_sale = $_POST['section'];
        // Nettoie array
        $course = cleanArray($course_sale);

        // Debut loop array post (curriculum)
        foreach($course as $cle1 => $valeur1){

            $section_names = $valeur1['titreSection'];
            // Prend la clé actuelle incrémenté en Jquery. => Peut etre changer pour vraie clé
            $section_order = $cle1;

            // Insert dans wp_learnpress_sections pour ordre des sections. Les sections sont associées au cours dans la table
            global $wpdb;
            $wpdb->insert(
                $wpdb->learnpress_sections,
                    array(
                        'section_name'          => $section_names, 
                        'section_course_id'     => $newCourseId,
                        'section_order'         => $section_order,
                        'section_description'   => 'section'
                    )
            );

            // MYSQLI
            //$sql2 = "INSERT INTO wp_learnpress_sections (section_name, section_course_id, section_order, section_description) VALUES ('$section_names', '$newCourseId', '$section_order', 'description')";
            //mysqli_query($conn, $sql2);

            foreach ($valeur1['contenu'] as $cle2=>$valeur2){

                // Insert lesson + contenu dans wp_posts avec function insert wordpress 
                // course_sale deja trimé
                $lesson = $valeur2['lessonTitle'];
                $content = $valeur2['lessonContent'];
                $key_order = $cle2;

                $curriculumUser = array(
                        'post_author'       => $lp_user,
                        'post_date'         => $date,
                        'post_date_gmt'     => $gmtDate,
                        'post_title'        => $lesson,
                        'post_type'         => 'lp_lesson',
                        'post_content'      => $content,
                        'post_status'       => 'publish',
                        'comment_status'    => 'open'                   
                );

                $insertCurri = wp_insert_post($curriculumUser);

                // Fetch ID des lesson dans wp_posts
                //$sql3 = "SELECT * FROM wp_posts WHERE post_title = '$lesson' AND post_author = '$user' AND post_type = 'lp_lesson'";
                $sql3 = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_title = '$lesson' AND post_author = '$lp_user' AND post_type = 'lp_lesson'");

                foreach($sql3 as $idsLesson) {
                    $idsL = $idsLesson->ID;
                }

                $sql4 = $wpdb->get_results("SELECT * FROM $wpdb->learnpress_sections WHERE section_name = '$section_names' AND section_course_id = '$newCourseId'");

                foreach($sql4 as $idsSection) {
                    $idsS = $idsSection->section_id;

                    // Insert dans wp_learnpress_section_items ID des sections + Id des lesson de wp_posts
                    // ORDRE DES LESSONS DEVRAIT ETRE INCREMENTÉ ET NON AVOIR LA KEY COMME INDEX
                    $wpdb->insert(
                        $wpdb->learnpress_section_items,
                            array(
                                'section_id'    => $idsS,
                                'item_id'       => $idsL,
                                'item_order'    => $key_order,
                                'item_type'     => 'lp_lesson'
                            )
                    );
                }

                // Insert dans wp_transit (au besoin) pour garder compte du ration sections/lessons --> MYSqli
                //$sql6 = "INSERT INTO wp_transit (section_name, lesson_name, course_id, user) VALUES ('$section_names', '$lesson', '$course_id', '$user')";
                //mysqli_query($conn, $sql6);
            }
        }

       
        // Insert curi ?
        if($insertCurri){

            $l_redirect = add_query_arg('post', 'successfull', 'http://localhost/profile/');
        }

    wp_redirect($l_redirect);
    exit;

    }
}
add_action('init', 'processCurri');



// Message succès ou erreur après envoie du formulaire (création de cours initial seulement. le curriculum est redirectionné)
function checkPost() {
    if(isset($_GET['post'])) {
        switch($_GET['post']) {
            case 'successfull' :
            echo '<h2 class="succ_msg">Your course has been submitted for approbation. You can now add lessons to your course</h2>';
            break;
            case 'lessonadded' :
            echo '<h2 class="succ_msg">Your lesson has been submitted for approbation. You can continue to add lessons</h2>';
            break;
            case 'failed' :
            echo '<h2 class="error_msg">Please fill the form correctly</h2>';
            break;
        }
    }
}
add_action('succ', 'checkPost');


// Nettoie ARRAY
function cleanArray($Input){
    if(!is_array($Input))

        return trim($Input);

    return array_map('cleanArray', $Input);

}
add_action('init', 'cleanArray');

// Redirect Instructeur sur CourseCreator
function redirectInst() {
    $currentUser = wp_get_current_user();
    if ($currentUser->roles[0] == 'lp_teacher'){
        wp_redirect('http://localhost/course-creator/');
        exit;
    }
}
add_action('admin_init', 'redirectInst');

// Pas de barre Admin pour Instructeur
function adminBarInst() {
    $currentUser = wp_get_current_user();
    if($currentUser->roles[0] == 'lp_teacher') {
        show_admin_bar(false);
    }
}
add_action('wp_loaded', 'adminBarInst');



// INIT SESSION (au besoin) --> Pour login custom page d'acceuil (instructor)
function myStartSession() {
    if(!session_id()) {
        session_start();
    }
}

// END SESSION (au besoin) --> Bouton deconnexion (enlevé)
function myEndSession() {
    session_destroy();
}
add_action('init', 'myStartSession', 1);
add_action('wp_logout', 'myEndSession');
add_action('wp_login', 'myEndSession');




