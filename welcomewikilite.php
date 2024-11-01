<?php
/*
Plugin Name: WelcomeWiki Lite
Description: WelcomeWiki Lite lets users embed an entire Mediawiki Page or a section from a MediaWWiki page into their Wordpress or non-WordPress site. WelcomeWiki Lite embeds content from any or all pages that follow MediaWiki protocols including Wikipedia and WikiVoyage.
Authors: vtrung, marktmattson
Author: Trung Van
Author URL: http://cartonova.com/
Version: 1.0
Tags: content framework, embed, mediawiki, wiki, wiki inc, wiki-embed, wikivoyage, wikipedia
Requires at least: 3.5
Tested up to: 3.71
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
// don't load directly
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}
if ( ! defined( 'WP_CONTENT_URL' ) )
      define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
      define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
define( 'WIKI_EX_LITE_VERSION', '1.0' );
define( 'WIKI_EX_LITE_NAME', 'WelcomeWiki Lite' );
define( 'WIKI_EX_LITE_RELEASE_DATE', date_i18n( 'F j, Y', '1375505016' ) );
define( 'WIKI_EX_LITE_DIR', WP_PLUGIN_DIR . '/welcomewiki-lite' );
define( 'WIKI_EX_LITE_URL', WP_PLUGIN_URL . '/welcomewiki-lite' );
define('WIKI_EX_LITE_ASSETS_URL', WIKI_EX_LITE_URL . '/assets');
require_once( 'admin-overlay.php' );
if (!class_exists("WelcomeWikiLite")) :
class WelcomeWikiLite {
	function __construct() {	
		add_action('init', array($this,'init') );
		//add_action('admin_init', array($this,'admin_init') );
		add_action('admin_menu', array($this,'admin_menu') );
	}

	/*
		Propagates pfunction to all blogs within our multisite setup.
		If not multisite, then we just run pfunction for our single blog.
	*/
	function network_propagate($pfunction, $networkwide) {
		global $wpdb;

		if (function_exists('is_multisite') && is_multisite()) {
			// check if it is a network activation - if so, run the activation function 
			// for each blog id
			if ($networkwide) {
				$old_blog = $wpdb->blogid;
				// Get all blog ids
				$blogids = $wpdb->get_col("SELECT blog_id FROM {$wpdb->blogs}");
				foreach ($blogids as $blog_id) {
					switch_to_blog($blog_id);
					call_user_func($pfunction, $networkwide);
				}
				switch_to_blog($old_blog);
				return;
			}	
		} 
		call_user_func($pfunction, $networkwide);
	}
	/*
		Load language translation files (if any) for our plugin.
	*/
	function init() {
       /***Call to load global variable and jquery lib***/ 
       add_action( 'wp_head', array($this,'global_urlajax'));
       wp_enqueue_script( 'wiki-global-script' );
       wp_enqueue_style('wikiexlite-admin-styles', WIKI_EX_LITE_ASSETS_URL . '/styles/wikiwelcome.css', false, WIKI_EX_LITE_VERSION);
        
       /***declare shortcode***/ 
	   add_shortcode('welcomewikilite', array($this,'wikiex_shortcode'));
       add_shortcode('welcomewikiliteform', array($this,'wikiex_welcomewikiliteform'));
       
        /**load ajax for embed**/
	   add_action('wp_ajax_wikiex_get_sections', array($this,'wikiex_get_sections'));
       add_action('wp_ajax_nopriv_wikiex_get_sections', array($this,'wikiex_get_sections'));
       /****Ajax for embed**/
       add_action( 'wp_ajax_wikilite_embed_action', array($this,'fwikilite_embed_action') );
       add_action( 'wp_ajax_nopriv_wikilite_embed_action', array($this,'fwikilite_embed_action') );       
	}
    
    /***Add menu for Pluggin***/
	function admin_menu() {
        $page = add_menu_page(WIKI_EX_LITE_NAME, WIKI_EX_LITE_NAME, 'edit_others_posts', 'wikiexlite', array(
            $this, 'wikiex_render_admin_page'),'',9700);	   
        add_submenu_page('wikiexlite', 'Add Wiki', 'Add Wiki', 'edit_others_posts', 'wikiexlite_add', array($this,'wikiex_render_add_page')); 
        add_submenu_page('wikiexlite', 'Settings', 'Settings', 'edit_others_posts', 'wikiexlite_sets', array($this,'settings_wiki_page')); 
        add_action('admin_print_scripts', array($this, 'register_admin_scripts'));
        add_action('admin_print_styles', array($this, 'register_admin_styles'));
	}
    
    function settings_wiki_page(){
        $wiki_lite_sections=get_option('wiki_lite_sections',array(0));
        if($_POST['wiki_setts']){
            $wiki_settings=$_POST['wiki_settings'];
            $wiki_lite_sections['settings']=$wiki_settings;
            update_option('wiki_lite_sections',$wiki_lite_sections);
        }
        $wiki_lite_sections=get_option('wiki_lite_sections',array(0));
        $wiki_settings=$wiki_lite_sections['settings'];
        if(!is_array($wiki_settings)){
            $wiki_settings=array();
        }
        ?>
          <h1>WelcomeWiki Lite Settings</h1>
          <form method="post">
            <p><input type="checkbox" name="wiki_settings[]" value="1" <? if(in_array(1,$wiki_settings)){echo 'checked="checked"';}?> /> Remove links and [1]</p>
            <p><input type="checkbox" name="wiki_settings[]" value="2" <? if(in_array(2,$wiki_settings)){echo 'checked="checked"';}?>/> Remove content index</p>
            <p><input type="checkbox" name="wiki_settings[]" value="3" <? if(in_array(3,$wiki_settings)){echo 'checked="checked"';}?>/> Remove right column index box</p>
            <p><input type="checkbox" name="wiki_settings[]" value="4" <? if(in_array(4,$wiki_settings)){echo 'checked="checked"';}?>/> Remove images and captions</p>
            <input type="hidden" value="action" name="wiki_setts" />
            <p><input type="submit" value="Save" /></p>
          </form>  
    <?}
    function wikiex_render_admin_page(){
        echo '<h1>WelcomeWiki Lite</h1>
            <p><br>This is WelcomeWiki Lite. The entire line of WelcomeWeb Wiki products can be seen at: <a target="_blank" href="http://www.welcomewiki.info">www.welcomewiki.info</a></p>
             <br>Thanks!!';
    }
    /*
    ** Wiki Form
    */
    function wikiex_welcomewikiliteform(){
        wp_enqueue_script('wiki-script', WIKI_EX_LITE_ASSETS_URL . '/scripts/welcomewiki_script.js', array('jquery'), WIKI_EX_LITE_VERSION);
        $html='<div class="wrap welcomelite">
        <div class="add-new-wiki"><a href="#" class="add-new-wiki-section"><a href="#" class="wikilite_frontend_sets">Settings</a></div>
        <div class="wiki_frontend_sections" style="display: none; padding: 10px; border: 1px solid #cccccc;">
        <p><input type="checkbox" name="wiki_settings[]" value="1"/> Remove links and [1]</p>
        <p><input type="checkbox" name="wiki_settings[]" value="2"/> Remove content index</p>
        <p><input type="checkbox" name="wiki_settings[]" value="3"/> Remove right column index box</p>
        <p><input type="checkbox" name="wiki_settings[]" value="4"/> Remove images and captions</p>
        </div>
        <form method="post" id="welcomeliteform" accept-charset="UTF-8">
        <div class="left wiki_content">
        <table class="widefat sortable">
        <thead>
        <tr>
        <th style="width: 100px;">Wiki section</th>
        <th></th>
        </tr>
        </thead>
        <tbody class="ui-sortable">';
        $html.='<tr class="slide flex responsive nivo coin">    
        <td class="col-2" colspan="2">        
        <div class="wikiex-1">
        <input style="width:100%" class="wikiex_url url" type="url" value="" placeholder="URL" name="wikiex[1][url]" />
        <input id="wikiex_shortcode1" type="hidden" name="wikiex[1][shortcode]" />
        <input type="hidden" class="wikiex_shortcode_order" name="wikiex_shortcode_order" value="1" />
        <input type="radio" value="full" name="wikiex[1][chk_type]" class="wikiex_full" />Full page &nbsp;&nbsp;&nbsp;&nbsp;
        <input type="radio" value="section" name="wikiex[1][chk_type]" class="wikiexlite_secion wikiex_radio_item" />Choose sections &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <div class="wikiex-lib-row"></div>
        <div class="wikiex_shortcode1"></div>
        </div>
        </td>
        </tr>                                
        </tbody>
        </table>
        </div>
        <input type="button" value="Process Request" id="save" class="button button-primary wikiwc_general_source">
        <input type="hidden" class="wikiex_section_amount" name="wikiex_section_amount" value="1" />  
        </form>
        <div class="wikiwc_shortcode" style="display:none"><h1><b>Copy and Embed Into Any Web Page</b></h1><br>
        <textarea cols="40" id="wikiwc_source" rows="10" id=""></textarea>
        </div>
        </div>';
        return $html;
    }
    /*
    **WIKI render admin page*
    */
    function wikiex_render_add_page(){
        $wiki_sections_orig=get_option('wiki_lite_sections');
        if($_POST['wikiex']){
            $wiki_sections=$_POST['wikiex'];
            $wiki_sections['settings']=$wiki_sections_orig['settings'];
            update_option('wiki_lite_sections',$wiki_sections);
            echo '<div class="success">Add Success!</div>';
        }
        $wiki_sections=get_option('wiki_lite_sections');
        if(!is_array($wiki_sections)){
            $wiki_sections=array();
        }
        $wiki=array();
        $key=0;
        ?>
            <div class="wrap wikiexlite">
                <form method="post" action="?page=wikiexlite_add" accept-charset="UTF-8">
                <div class="left wiki_content">
                <table class="widefat sortable">
                <thead>
                <tr>
                <th style="width: 200px;">WelcomeWiki Lite</th>
                <th></th>
                </tr>
                </thead>
                <tbody class="ui-sortable">
                <tr class="slide flex responsive nivo coin">    
                <td class="col-2" colspan="2">        
                <div class="wikiex-1" style="padding-left: 15px;">
                <input style="width:100%" class="wikiex_url url" type="url" value="<?=$wiki_sections[1]['url']?>" placeholder="URL" name="wikiex[1][url]" />
                <input id="wikiex_shortcode1" type="hidden" name="wikiex[1][shortcode]" />
                <input type="hidden" class="wikiex_shortcode_order" name="wikiex_shortcode_order" value="1" />
                <input type="radio" value="full" name="wikiex[1][chk_type]" class="wikiex_full"  <?=($wiki_sections[1]['chk_type']=='full'?'checked="checked"':'')?> />Full page  
                <input type="radio" value="section" name="wikiex[1][chk_type]" <?=($wiki_sections[1]['chk_type']=='section'?'checked="checked"':'')?> class="wikiex_secion" />Choose sections
                <div class="wikiex-lib-row">
                <?if(count($wiki_sections['wikiexcb'])){
                echo '<div class="wikiex-lib-row"><div class="chk-wiki clear"><input type="radio" name="wikiex[wikiexcb]" checked="checked" id="cb0" value="'.$wiki_sections['wikiexcb'].'"><label for="cb0">'.$wiki_sections['wikiexcb'].'</label></div></div>';
                }?>
                </div>
                <br /><br />
                <div class="wikiex_shortcode1"></div>
                </div>
                </td>
                </tr>
                
                </tbody>
                </table>
                </div>
                <div class="right">
                <table class="widefat settings">
                <thead>
                <tr>
                <th colspan="2">
                <span class="configuration">Shortcode</span>
                <input type="submit" value="Save" id="save" class="alignright button button-primary">
                <span class="spinner" style="display: none;"></span>
                </th>
                </tr>
                <tr>
                <td colspan="2">[welcomewikilite]</td>
                </tr>
                </thead>
                <tbody>
                <tr>
                <td class="wikiex-lib-row2" colspan="2">
                
                </td>
                </tr>
                </tbody>
                </table>
                </div>
                <input type="hidden" class="wikiex_section_amount" name="wikiex_section_amount" value="1" />
            </form>
            </div>            
    <?}  
      
    /*
        Shortcode welcomewikilite
    */
    function wikiex_shortcode( $atts ) {
        extract( shortcode_atts( array(
	      'wikiurl' => '',
	      'sections' => '',
          'settings' => '',
        ), $atts ) );
        if(trim($wikiurl)){
            $wiki_items[0]['url']=$wikiurl;
            $wiki_items[0]['wikiexcb']=$sections;
            $wiki_settings=$settings;
        }else{
            $wiki_sections=get_option('wiki_lite_sections');
            $wikiitem=$wiki_sections[1];
            $wikiitem['wikiexcb']=$wiki_sections['wikiexcb'];
            $wiki_items[]=$wikiitem;
        }
         

         $wiki_content='<div class="wiki-sections">';
         foreach($wiki_items as $wiki_item){
                 if(empty($wiki_item)) continue;
                   
                 $wiki_url=$wiki_item['url'];
                 $wiki_title='';
                 $wiki_url_root='';
                 
                 if($wiki_url){
                    $term=explode('/',$wiki_url);
                    $wiki_title=array_pop($term);
                    $parse_url=parse_url($wiki_url);
                    $wiki_url_root='http://'.$parse_url['host'].'/';
                 }

                 $array_section=array();
                 if(trim($wiki_item['wikiexcb'])){
                    $array_section[$wiki_item['wikiexcb']]=strtolower($wiki_item['wikiexcb']);   
                 }
                 $wiki_content.='<div class="wiki-item"><h3>'.$wiki_title.'</h3>';
                 if($wiki_title && count($array_section)){
                    if(in_array('short description',$array_section)){
                        $wikiurl=$wiki_url_root.'w/api.php?action=query&titles='.urlencode($wiki_title).'&exintro=&prop=extracts&format=xml';
                        $wiki_xml= wp_remote_get($wikiurl);
                        $xml = simplexml_load_string($wiki_xml['body']);
                        
                        $wiki_content.='<div class="short_description">'.$xml->query->pages->page->extract.'</div>';
                    }
                    
                    $wikiurl=$wiki_url_root.'w/api.php?action=parse&page='.urlencode($wiki_title).'&prop=sections&format=xml';
                    $wiki_xml= wp_remote_get($wikiurl);
                    
                    $xml = simplexml_load_string($wiki_xml['body']);
                                        //print_r($xml);
                    if(count($xml->parse->sections->s)){
                        foreach($xml->parse->sections->s as $key => $xmlnode){
                            $xmlnode=(array)$xmlnode;
                            if(in_array(strtolower($xmlnode['@attributes']['line']),$array_section)){
                              $section_number=$xmlnode['@attributes']['index'];
                              $wikiurl=$wiki_url_root.'w/api.php?action=parse&page='.urlencode($wiki_title).'&prop=text&format=xml&section='.$section_number;
                              $wiki_content.=$this->get_body_from_wiki($wikiurl,$wiki_url_root);
                            }
                        }    
                    }
                 }elseif($wiki_title){
                    $wikiurl=$wiki_url_root.'w/api.php?action=parse&page='.urlencode($wiki_title).'&prop=text&format=xml';
                    $wiki_xml= wp_remote_get($wikiurl);
                    $content= html_entity_decode($wiki_xml['body']);
                    $wiki_content .= preg_replace('|<strong class="error mw-ext-cite-error">.*?</strong>|i','',$content);
                    $wiki_content .= preg_replace('|href="(/wiki.*?)"|i','href="'.$wiki_url_root.'$1" target="_blank"',$wiki_content);
                     
                 }
                 $wiki_content.='</div>';
         }
         $wiki_content.='</div>';

         if(count($wiki_items)){
            
            if(count($wiki_sections)){
                $wiki_settings=$wiki_sections['settings']?$wiki_sections['settings']:array();

            }elseif($wiki_settings){
                $wiki_settings=explode(',',$wiki_settings);
            }else{
                $wiki_settings=array();
            }
         if(in_array(2,$wiki_settings)){
            $wiki_content =preg_replace('#<sup .*?>([^>]*)</sup>#i', '', $wiki_content);
            $wiki_content =preg_replace('#<div id="toctitle">.*?</div>#is', '', $wiki_content);
            $wiki_content =preg_replace('#<div id="toc" class="toc">.*?</div>#is', '', $wiki_content);
         }
         if(in_array(1,$wiki_settings)){
            $wiki_content =preg_replace('#<a .*?>(.*?)</a>#i', '$1', $wiki_content);
            $wiki_content =preg_replace('#<span class="mw-editsection">.*?]</span></span>#i', '$1', $wiki_content);
            
         }
         if(in_array(3,$wiki_settings)){
            $wiki_content =preg_replace('#<table.*?class="infobox.*?>.*?</table>#is', '', $wiki_content);
         }
         if(in_array(4,$wiki_settings)){
            $wiki_content =preg_replace('#<div class="magnify">.*?</div>#is', '', $wiki_content);
            $wiki_content =preg_replace('#<div class="thumbcaption">.*?</div>#is', '', $wiki_content);
            $wiki_content =preg_replace('#<div class="thumbinner".*?>.*?</div>#is', '', $wiki_content);
            $wiki_content =preg_replace('#<img src=".*?".*?/>#i', '', $wiki_content);
         }             
         }
                          
        return $wiki_content;
    }
    /**AJAX call**/
    function fwikilite_embed_action(){
        //$WelcomeWiki = new WelcomeWikiLite();
        if($_GET['url']){
            $urls=explode('|',stripslashes($_GET['url']));
            $array_section=array();
            if(!trim($_GET['sections'])){
                $_GET['sections']='full';
            }
            if($_GET['sections']){
                $asections=explode('|',stripslashes($_GET['sections']));
                foreach($urls as $key => $url){
                    $wiki_items[$key]['url']=$url;
                    $sections=array();
                    if($asections[$key]){
                        $sections=explode(',',$asections[$key]);
                    }
                    $wiki_items[$key]['wikiexcb']=$sections;
                }            
            }
             $wiki_content='<div class="wiki-sections">';
             foreach($wiki_items as $wiki_item){
                     if(empty($wiki_item)) continue;
                       
                     $wiki_url=$wiki_item['url'];
                     $wiki_title='';
                     $wiki_url_root='';
                     
                     if($wiki_url){
                        $term=explode('/',$wiki_url);
                        $wiki_title=array_pop($term);
                        $parse_url=parse_url($wiki_url);
                        $wiki_url_root='http://'.$parse_url['host'].'/';
                     }
                     $array_section=array();
                     if(is_array($wiki_item['wikiexcb']) && count($wiki_item['wikiexcb'])){
                        foreach($wiki_item['wikiexcb'] as $key => $item){
                            if($item !='full')
                                $array_section[$key]=strtolower($item);
                        }
                     }   
                     $wiki_content.='<div class="wiki-item"><h3>'.$wiki_title.'</h3>';
                     if($wiki_title && count($array_section)){
                        //$array_section=explode('|',strtolower($section));
    
                        if(in_array('short description',$array_section)){
                            $wikiurl=$wiki_url_root.'w/api.php?action=query&titles='.urlencode($wiki_title).'&exintro=&prop=extracts&format=xml';
                            $wiki_xml= wp_remote_get($wikiurl);
                            $xml = simplexml_load_string($wiki_xml['body']);
                            
                            $wiki_content.='<div class="short_description">'.$xml->query->pages->page->extract.'</div>';
                        }
                        
                        $wikiurl=$wiki_url_root.'w/api.php?action=parse&page='.urlencode($wiki_title).'&prop=sections&format=xml';
                        $wiki_xml= wp_remote_get($wikiurl);
                        
                        $xml = simplexml_load_string($wiki_xml['body']);
                                            //print_r($xml);
                        if(count($xml->parse->sections->s)){
                            foreach($xml->parse->sections->s as $key => $xmlnode){
                                $xmlnode=(array)$xmlnode;
                                if(in_array(strtolower($xmlnode['@attributes']['line']),$array_section)){
                                  $section_number=$xmlnode['@attributes']['index'];
                                  $wikiurl=$wiki_url_root.'w/api.php?action=parse&page='.urlencode($wiki_title).'&prop=text&format=xml&section='.$section_number;
                                  $wiki_content.=$this->get_body_from_wiki($wikiurl,$wiki_url_root);
                                }
                            }    
                        }
                     }elseif($wiki_title){
                        $wikiurl=$wiki_url_root.'w/api.php?action=parse&page='.urlencode($wiki_title).'&prop=text&format=xml';
                        $wiki_xml= wp_remote_get($wikiurl);
                        $content= html_entity_decode($wiki_xml['body']);
                        $wiki_content .= preg_replace('|<strong class="error mw-ext-cite-error">.*?</strong>|i','',$content);
                        $wiki_content .= preg_replace('|href="(/wiki.*?)"|i','href="'.$wiki_url_root.'$1" target="_blank"',$wiki_content);
                         
                     }
                     $wiki_content.='</div>';
    
                     $sets=$_GET['sets'];
                     if($sets){
                        $wiki_settings=explode(',',$sets);
                     
                         if(in_array(2,$wiki_settings)){
                            $wiki_content =preg_replace('#<sup .*?>([^>]*)</sup>#i', '', $wiki_content);
                            $wiki_content =preg_replace('#<div id="toctitle">.*?</div>#is', '', $wiki_content);
                            $wiki_content =preg_replace('#<div id="toc" class="toc">.*?</div>#is', '', $wiki_content);
                         }
                         if(in_array(1,$wiki_settings)){
                            $wiki_content =preg_replace('#<a .*?>(.*?)</a>#i', '$1', $wiki_content);
                            $wiki_content =preg_replace('#<span class="mw-editsection">.*?]</span></span>#i', '$1', $wiki_content);
                            
                         }
                         if(in_array(3,$wiki_settings)){
                            $wiki_content =preg_replace('#<table.*?class="infobox.*?>.*?</table>#is', '', $wiki_content);
                         }
                         if(in_array(4,$wiki_settings)){
                            $wiki_content =preg_replace('#<div class="magnify">.*?</div>#is', '', $wiki_content);
                            $wiki_content =preg_replace('#<div class="thumbcaption">.*?</div>#is', '', $wiki_content);
                            $wiki_content =preg_replace('#<div class="thumbinner".*?>.*?</div>#is', '', $wiki_content);
                            $wiki_content =preg_replace('#<img.*?src=".*?".*?/>#is', '', $wiki_content);
                         }
                         
                     }
                                      
             }
            $wiki_content.='</div>';
        }
        $arr_content=array('content'=>$wiki_content);
        header('content-type: application/json');
    
        $json = json_encode($arr_content);
    
        echo isset($_GET['callback'])
        ? "{$_GET['callback']}($json)"
        : $json;   exit();     
    }
    
    /***Library use for call API of Wiki****/
    function wikiex_get_from_url($wiki_url,$array_section) {
         $wiki_content='<div class="wiki-sections">';
         $wiki_title='';
         $wiki_url_root='';
         
         if($wiki_url){
            $term=explode('/',$wiki_url);
            $wiki_title=array_pop($term);
            $parse_url=parse_url($wiki_url);
            $wiki_url_root='http://'.$parse_url['host'].'/';
         }
         $wiki_content.='<div class="wiki-item"><h3>'.$wiki_title.'</h3>';
         if($wiki_title && count($array_section)){

            if(in_array('short description',$array_section)){
                $wikiurl=$wiki_url_root.'w/api.php?action=query&titles='.urlencode($wiki_title).'&exintro=&prop=extracts&format=xml';
                $wiki_xml= wp_remote_get($wikiurl);
                $xml = simplexml_load_string($wiki_xml['body']);
                $wiki_content.='<div class="short_description">'.$xml->query->pages->page->extract.'</div>';
            }
            $wikiurl=$wiki_url_root.'w/api.php?action=parse&page='.urlencode($wiki_title).'&prop=sections&format=xml';
            $wiki_xml= wp_remote_get($wikiurl);
            
            $xml = simplexml_load_string($wiki_xml['body']);
                                //print_r($xml);
            if(count($xml->parse->sections->s)){
                foreach($xml->parse->sections->s as $key => $xmlnode){
                    $xmlnode=(array)$xmlnode;
                    if(in_array(strtolower($xmlnode['@attributes']['line']),$array_section)){
                      $section_number=$xmlnode['@attributes']['index'];
                      $wikiurl=$wiki_url_root.'w/api.php?action=parse&page='.urlencode($wiki_title).'&prop=text&format=xml&section='.$section_number;
                      $wiki_content.=$this->get_body_from_wiki($wikiurl,$wiki_url_root);
                    }
                }    
            }
         }elseif($wiki_title){
            $wikiurl=$wiki_url_root.'w/api.php?action=parse&page='.urlencode($wiki_title).'&prop=text&format=xml';
            $wiki_xml= wp_remote_get($wikiurl);
            $content= html_entity_decode($wiki_xml['body']);
            $wiki_content .= preg_replace('|<strong class="error mw-ext-cite-error">.*?</strong>|i','',$content);
            $wiki_content .= preg_replace('|href="(/wiki.*?)"|i','href="'.$wiki_url_root.'$1" target="_blank"',$wiki_content);
             
         }
         $wiki_content.='</div>';
        return $wiki_content;
    }
    /*
     Get list section from url
    */
    function wikiex_get_sections(){
        $wiki_url=urldecode($_GET['url']);
        $term=explode('/',$wiki_url);
        $wiki_title=array_pop($term);
        $parse_url=parse_url($wiki_url);
        $wiki_url_root='http://'.$parse_url['host'].'/';
                         
        if($wiki_title){
            $array_section=array();
           $wikiurl=$wiki_url_root.'w/api.php?action=parse&page='.urlencode($wiki_title).'&prop=sections&format=xml';
            $wiki_xml= wp_remote_get($wikiurl);

            $xml = simplexml_load_string($wiki_xml['body']);
            $wiki_content='';
            if(count($xml->parse->sections->s)){
                foreach($xml->parse->sections->s as $key => $xmlnode){
                    $xmlnode=(array)$xmlnode;
                    $array_section[]=$xmlnode['@attributes']['line'];
                }    
            }
         }   
         if(count($array_section)){
            //sort($array_section);
         }
         $array_section=array_merge_recursive(array('Short description'),$array_section);
         echo json_encode($array_section);  
         exit();
    }    
    function get_body_from_wiki($wikiurl,$wiki_url_root){
        $wiki_xml= wp_remote_get($wikiurl);
        $content='';
        if(trim($wiki_xml['body']))
            $content= $this->remove_special_tag(html_entity_decode($wiki_xml['body']),$wiki_url_root);
            
        return $content;
    }
    function remove_special_tag($string,$wiki_url_root){
        $wiki_content= preg_replace('|<strong class="error mw-ext-cite-error">.*?</strong>|i','',$string);
        $wiki_content= preg_replace('|href="(/wiki.*?)"|i','href="'.$wiki_url_root.'$1" target="_blank"',$wiki_content);
        $wiki_content= preg_replace('|<span class="mw-editsection-bracket">.*?]</span>|i','',$wiki_content);
        return $wiki_content;
    }
    /**
     * Register admin JavaScript
     */
    public function register_admin_scripts() {
        // media library dependencies
        wp_enqueue_media();

        // plugin dependencies
        wp_enqueue_script('jquery-ui-core', array('jquery'));
        wp_enqueue_script('jquery-ui-accordion', array('jquery', 'jquery-ui-core'));
        wp_enqueue_script('jquery-ui-sortable', array('jquery', 'jquery-ui-core'));
        wp_enqueue_script('metaslider-admin-script', WIKI_EX_LITE_ASSETS_URL . '/scripts/welcomewiki_admin.js', array('jquery'), WIKI_EX_LITE_VERSION);

    }
    /*
     * Register admin styles
     */
    public function register_admin_styles() {
        wp_enqueue_style('wikiex-admin-styles', WIKI_EX_LITE_ASSETS_URL . '/styles/welcomewiki_admin.css', false, WIKI_EX_LITE_VERSION);
    }
    /***load global variable to use for ajax call***/
    function global_urlajax(){ ?>
          <script type="text/javascript">
            var ajaxurl = '<?php echo admin_url( "admin-ajax.php" ); ?>';
            var ajaxnonce = '<?php echo wp_create_nonce( "itr_ajax_nonce" ); ?>';
            var siteurl = '<?php echo get_bloginfo('url'); ?>';
          </script>
    <?php }    

} // end class
endif;

// Initialize our plugin object.
global $WelcomeWikiLite;
if (class_exists("WelcomeWikiLite") && !$WelcomeWikiLite) {
    $WelcomeWikiLite = new WelcomeWikiLite();	
}	
?>