<?php
add_action('admin_footer', 'wikilite_overlay_popup_form');
add_action('media_buttons_context', 'wikilite_overlay_buttons');
/**
 * wikilite_overlay_buttons function.
 * 
 * @access public
 * @param mixed $context
 * @return void
 */
function wikilite_overlay_buttons( $context ) {
	
	global $post, $pagenow;
	
	if( in_array($pagenow, array( "post.php", "post-new.php" ) ) && in_array( $post->post_type , array("post","page")) ):
		$wiki_embed_overlay_image_button = plugins_url('/welcomewiki-lite/assets/images/icon.png');
	    $output_link = '<a href="#TB_inline?height=600&width=600&inlineId=wikilite_embedform" class="thickbox button" title="' .__("WelcomeWiki Lite", 'wikilite-embed') . '" id="wiki-embed-overlay-button"><span class="wplus-media-buttons-icon">Add Wiki</span></a></a><style>#wikilite_embedform{ display:none;}</style>';
	    return $context.$output_link;
    else:
    	return $context;
    endif;
}

/**
 * wikilite_overlay_popup_form function.
 * 
 * @access public
 * @return void
 */
function wikilite_overlay_popup_form() {
	
	global $wikilite_options,$pagenow,$post;
	
	if( in_array( $pagenow, array( "post.php", "post-new.php" )) && in_array( $post->post_type , array("post","page") )):
	
    ?>
    <script type="text/javascript">
        function wiki_embed_insert_overlay_form(){
                var allVals = [];
                         jQuery('[name*=wikiexcb]:checked').each(function() {
                           allVals.push(jQuery(this).val());
                         });
                         
                         var section='';
                         if(allVals.length){
                            section=allVals.join(',');
                         }
                        var allSets=[],sets='';
                        jQuery('[name*=wiki_settings]:checked').each(function() {
                               allSets.push(jQuery(this).val());
                        });
                        if(allSets.length){
                            sets=allSets.join(',');
                        }
                                 
                         var wikiex_shortcode_order= jQuery('.wikiex_shortcode_order').val();
                         
                         var wiki_url=jQuery('.wikiex_url').val();
                        //$('.wikiwc_shortcode').text(full_url);
                        var shortcode='[welcomewikilite wikiurl="'+wiki_url+'" sections="'+section+'" settings="'+sets+'"]';
                        var win = parent;
                        win.send_to_editor(shortcode); 
    	   }
    </script>

    <div class="wrap wikiembedform" id="wikilite_embedform">
    <div class="add-new-wiki"><a href="#" class="add-new-wiki-section"><a href="#" class="wikilite_frontend_sets">Settings</a></div>
    <div class="wiki_frontend_sections" style="display: none; padding: 10px; border: 1px solid #cccccc;width:80%">
        <p><input type="checkbox" name="wiki_settings[]" value="1"/> Remove links and [1]</p>
        <p><input type="checkbox" name="wiki_settings[]" value="2"/> Remove content index</p>
        <p><input type="checkbox" name="wiki_settings[]" value="3"/> Remove right column index box</p>
        <p><input type="checkbox" name="wiki_settings[]" value="4"/> Remove images and captions</p>
    </div>
    <div class="left wiki_content">
            <table class="sortable" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 200px;">WelcomeWiki Lite</th>
                        <th></th>
                    </tr>
                </thead>
                    <tbody class="ui-sortable">
                        <tr class="slide flex responsive nivo coin">    
                            <td class="col-2" colspan="2">        
                                <div class="wikiex-1">
                                    <input style="width:300px;margin-right:10px" class="wikiex_url url" type="url" value="" placeholder="URL" name="wikiex[1][url]" /> <input type="button" value="Insert into Post/ Page" onclick="wiki_embed_insert_overlay_form();" class="wikiwc_general_source" id="go_button" class="button">
                                    <input id="wikiex_shortcode1" type="hidden" name="wikiex[1][shortcode]" />
                                    <input type="hidden" class="wikiex_shortcode_order" name="wikiex_shortcode_order" value="1" /><div style="clear:both;margin-top:6px"></div>
                                    <input type="radio" value="full" name="wikiex[1][chk_type]" class="wikiex_full" />Full page  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <input type="radio" value="section" name="wikiex[1][chk_type]" class="wikiexlite_secion" />Choose sections
                                    <div class="wikiex-lib-row"></div>
                                    <div class="wikiex_shortcode1"></div>
                                </div>
                            </td>
                        </tr>                                
                </tbody>
            </table>
        </div>
       <input type="hidden" class="wikiex_section_amount" name="wikiex_section_amount" value="1" />  
    <div class="wikiwc_shortcode"></div>
</div>
    <?php
    endif;
}