<?php 
require 'vendor/vendor/autoload.php';
use League\HTMLToMarkdown\HtmlConverter;


abstract class WordPressToDev_Meta_Box {
 
 
    /**
     * Set up and add the meta box.
     */
    public static function add() {
        $screens = [ 'post' ];
        foreach ( $screens as $screen ) {
            add_meta_box(
                'WordPressToDev_API_Metabox_ID',          // Unique ID
                'Publish to DEV.to?', // Box title
                [ self::class, 'html' ],   // Content callback, must be of type callable
                $screen                  // Post type
            );
        }
    }
 
 
    /**
     * Save the meta box selections.
     *
     * @param int $post_id  The post ID.
     */
    public static function save( int $post_id ) {

        // Update post meta
        if (array_key_exists('organization_list', $_POST)) {
            update_post_meta($post_id, 'organization_list', $_POST['organization_list']);
        } 

        if (array_key_exists('custom_organization_id', $_POST)) {
            update_post_meta($post_id, 'custom_organization_id', $_POST['custom_organization_id']);
        }        
        

        if (array_key_exists('dev_to_article_tags', $_POST)) {
            update_post_meta($post_id, 'dev_to_article_tags', $_POST['dev_to_article_tags']);
        }        

        if (array_key_exists('dev_alt_title', $_POST)) {
            update_post_meta($post_id, 'dev_alt_title', $_POST['dev_alt_title']);
        }

        if (array_key_exists('cover_image_dev', $_POST)) {
            update_post_meta($post_id, 'cover_image_dev', $_POST['cover_image_dev']);
        }

        if (array_key_exists('post_to_dev_to', $_POST)) {
            update_post_meta($post_id, 'post_to_dev_to', $_POST['post_to_dev_to']);
        }

        if (array_key_exists('canonical_link', $_POST)) {
            update_post_meta($post_id, 'canonical_link', $_POST['canonical_link']);
        }


    }
 
 
    /**
     * Display the meta box HTML to the user.
     *
     * @param \WP_Post $post   Post object.
     */
    public static function html( $post ) {
        $organization_list = get_post_meta( $post->ID, 'organization_list', true );
        $custom_organization_id = get_post_meta( $post->ID, 'custom_organization_id', true );
        $getarticletags = get_post_meta( $post->ID, 'dev_to_article_tags', true );
        $getalttitle = get_post_meta( $post->ID, 'dev_alt_title', true );
        $cover_image_dev = get_post_meta( $post->ID, 'cover_image_dev', true );
        $post_to_dev_to = get_post_meta( $post->ID, 'post_to_dev_to', true );
        $canonical_link = get_post_meta( $post->ID, 'canonical_link', true );
        $getwpDevoption = get_option('WordPressToDevAPI_Setting');
        //print_r(get_post_meta($post->ID, 'artice_error_status', true));
        // print_r(get_post_meta($post->ID, 'artice_success_status', true));
        ?>
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <style type="text/css">
            .field_div{margin-bottom: 15px;}
            .field_div label{width: 20%;display: inline-block;font-size: 15px;}
            .field_div input{margin-top: 10px;height: 35px;font-size: 15px;width: 78%;}
            .field_div input[type="checkbox"]{margin-top: 0px;height:15px;font-size: 15px;width: auto;}
        </style>
        <div class="field_div">
            <label for="post_to_dev_to">Post to dev.to?</label>
            <input type="checkbox" name="post_to_dev_to" value="yes" <?php if ($post_to_dev_to == "yes") {
                echo "checked";
            } ?> >      
        </div>
        <div class="field_div">
            <label for="organization_list">Organization</label>
            <select name="organization_list">
                <option value="">Select</option>
                <?php   
                    $response = wp_remote_post( 'https://dev.to/api/articles/me', array(
                        'method'      => 'GET',
                        'timeout'     => 45,
                        'redirection' => 5,
                        'httpversion' => '1.0',
                        'blocking'    => true,
                        'headers'     => array(
                            'content-type' => 'application/json',
                            'api-key'     => $getwpDevoption['WordPressToDevAPI_API_key'],
                        ),
                        'body'        => array(
                        'page'     => 1,
                        'per_page' => 100,
                        ),
                        'cookies'     => array()
                        )
                    );
                    $responseBody = json_decode($response['body'], true);

                    $collectOrgusernames[] = "";
                    if( !empty($responseBody) ) {
                    foreach ($responseBody as $UserArticleskey => $UserArticlesvalue) {
                            if (!empty($UserArticlesvalue['organization'])) {
                                $collectOrgusernames[] = $UserArticlesvalue['organization']['username'];
                            }
                        }
                    }

                    $getfinallistoforgs = array_unique(array_filter($collectOrgusernames));
                    if (!empty($getfinallistoforgs)) {
                        foreach ($getfinallistoforgs as $Orgkey => $Orgvalue) {
                            $Orgresponse = wp_remote_get( 'https://dev.to/api/organizations/'.$Orgvalue );
                            $OrgresponseBody = json_decode($Orgresponse['body'], true); ?>
                            <option value="<?php echo $OrgresponseBody['id']; ?>" 
                            <?php if ($organization_list == $OrgresponseBody['id']) {
                             echo 'selected="selected"';
                        } ?>><?php echo $OrgresponseBody['name']; ?></option>
                            <?php
                        }
                    }
                 ?>
            </select>     
        </div>
        <div class="field_div">
            <label for="custom_organization_id">Custom Organization Username</label>
            <input type="text" name="custom_organization_id" value="<?php echo $custom_organization_id; ?>" >  
        </div>
        <div class="field_div">
            <label for="canonical_link">Canonical Link</label>
            <input type="text" name="canonical_link" value="<?php echo $canonical_link; ?>" >      
        </div>        
        <div class="field_div">
            <label for="cover_image">Cover image</label>
            <input type="test" name="cover_image_dev"  id="cover_image_dev" value="<?php echo $cover_image_dev; ?>" >      
        </div>
        <?php 
        $response = wp_remote_post( 'https://dev.to/api/tags', array(
            'method'      => 'GET',
            'timeout'     => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array(),
            'body'        => array(
            'page'     => 1,
            'per_page' => 50,
            ),
            'cookies'     => array()
            )
        );

        $responseTags = json_decode($response['body'], true);

        if (empty($getarticletags)) {
            $getarticletags = array();
        }

         ?>
        <div class="field_div">
            <label for="dev_to_article_tags">Tags</label>
            <select class="js-example-responsive" name="dev_to_article_tags[]" multiple="multiple" style="width: 75%">
                <?php if( !empty($responseTags) ) {
                    foreach ($responseTags as $Tagskey => $Tagsvalue) { ?>
                        <option value="<?php echo $Tagsvalue['name']; ?>" <?php if (in_array($Tagsvalue['name'], $getarticletags)) {
                            echo 'selected="selected"';
                        } ?>><?php echo $Tagsvalue['name']; ?></option>
                    <?php } } ?>
            </select>      
        </div>

        <div class="field_div">
            <label for="dev_alt_title">Dev.to title</label>
            <input type="text" name="dev_alt_title" value="<?php echo $getalttitle; ?>" >      
        </div>
        <?php wp_enqueue_media();?>
        <script type="text/javascript">
            jQuery(document).ready(function( $ ) {
                $(".js-example-responsive").select2({
                   width: 'resolve', // need to override the changed default
                   maximumSelectionLength: 4
                });
  
            // ADD IMAGE LINK

            var mediaUploader;

            $('#cover_image_dev').on('click',function(e) {
                e.preventDefault();
                var buttonID = $(this).data('group');

                if( mediaUploader ){
                    mediaUploader.open();
                    return;
                }

              mediaUploader = wp.media.frames.file_frame =wp.media({
                title: 'Choose a Cover Picture',
                button: {
                    text: 'Choose Picture'
                },
                multiple:false
              });

              mediaUploader.on('select', function(){
                attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#cover_image_dev').val(attachment.url);
              });
              mediaUploader.open();
            });

            });    
        </script>
        <?php 
    }

    public static function post_published_to_devTo( $post_id, $post ) {
        $converter = new HtmlConverter();
        if (isset($_POST['post_content']) && !empty($_POST['post_content'])) {
            $getmaincontent = $_POST['post_content'];
        }elseif (!empty($post->post_content)) {
            $getmaincontent = $post->post_content;
        }else{
            $getmaincontent = "No Content";
        }

        $markdown = $converter->convert($getmaincontent);
        
        if (!empty($_POST['cover_image_dev'])) {
            $cover_image_url = $_POST['cover_image_dev'];
        }else{
            $cover_image_url = "";
        }
        $getwpDevoption = get_option('WordPressToDevAPI_Setting');

        if (!empty($_POST['dev_alt_title'])) {
            $gettitle = $_POST['dev_alt_title'];
        }else{
            $gettitle = get_the_title($post_id);
        }

        if (!empty($_POST['canonical_link'])) {
            $canonical_link = $_POST['canonical_link'];
        }else{
            $canonical_link = "";
        }

        if (!empty($_POST['dev_to_article_tags'])) {
            $getarticlestags = $_POST['dev_to_article_tags'];
        }else{
            $getarticlestags = "";
        }

        if (!empty($_POST['organization_list'])) {
            $organization_id = $_POST['organization_list'];
        }elseif(!empty($_POST['custom_organization_id'])){
        $Orgresponse = wp_remote_get( 'https://dev.to/api/organizations/'.$_POST['custom_organization_id'] );
        $OrgresponseBody = json_decode($Orgresponse['body'], true);
        $organization_id = $OrgresponseBody['id'];
        }else{
            $organization_id = "";
        }
   
        if (isset($_POST['post_to_dev_to']) && $_POST['post_to_dev_to'] == 'yes') {
            if (get_post_meta($post_id, 'Dev_to_artice_ID', true)) {
                $getthearticleID = get_post_meta($post_id, 'Dev_to_artice_ID', true);
                if(!empty($getthearticleID)){
                    $response = wp_remote_post( "https://dev.to/api/articles/".$getthearticleID, array(
                        'method'      => 'PUT',
                        'timeout'     => 500,
                        'redirection' => 5,
                        'httpversion' => '1.0',
                        'blocking'    => true,
                        'headers'     => array(
                            'content-type' => 'application/json',
                            'api-key'     => $getwpDevoption['WordPressToDevAPI_API_key'],
                        ),
                        'body'        => json_encode(array(
                            'article' => array(
                                'title' => $gettitle,
                                'body_markdown' => $markdown ,
                                'main_image' => $cover_image_url,
                                'tags' => $getarticlestags,
                                'canonical_url' => $canonical_link,
                                'organization_id' => $organization_id
                            )
                        )),
                        'cookies'     => array()
                        )
                    );

                    if ( is_wp_error( $response ) ) {
                        $error_message = $response->get_error_message();
                        update_post_meta($post_id, 'artice_error_status', $error_message);
                    } else {
                        update_post_meta($post_id, 'artice_success_status', $response['body']);
                    }
                }
            }else{   
                $response = wp_remote_post( "https://dev.to/api/articles", array(
                    'method'      => 'POST',
                    'timeout'     => 500,
                    'redirection' => 5,
                    'httpversion' => '1.0',
                    'blocking'    => true,
                    'headers'     => array(
                        'content-type' => 'application/json',
                        'api-key'     => $getwpDevoption['WordPressToDevAPI_API_key'],
                    ),
                    'body'        => json_encode(array(
                        'article' => array(
                            'title' => $gettitle,
                            'body_markdown' => $markdown ,
                            'main_image' => $cover_image_url,
                            'published' => false,
                            'tags' => $getarticlestags,
                            'canonical_url' => $canonical_link,
                            'organization_id'=> $organization_id
                            
                        )
                    )),
                    'cookies'     => array()
                    )
                );

                if ( is_wp_error( $response ) ) {
                    $error_message = $response->get_error_message();
                    update_post_meta($post_id, 'artice_error_status', $error_message);
                } else {
                    update_post_meta($post_id, 'artice_success_status', $response['body']);
                    $getthearticledetails = json_decode($response['body'], true);
                    if (!empty($getthearticledetails['id'])) {
                        update_post_meta($post_id, 'Dev_to_artice_ID', $getthearticledetails['id']);
                    }
                }
            }
        }
    }
}
 
add_action( 'add_meta_boxes', [ 'WordPressToDev_Meta_Box', 'add' ] );
add_action( 'save_post', [ 'WordPressToDev_Meta_Box', 'save' ] );
add_action( 'publish_post', [ 'WordPressToDev_Meta_Box', 'post_published_to_devTo'], 10, 2 );
add_action( 'draft_post', [ 'WordPressToDev_Meta_Box', 'post_published_to_devTo'], 10, 2 );

