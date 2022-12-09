<?php
if((isset($_GET['output'])) && ($_GET['output'] == 'updated'))
{
    $notice = array('success', __('Your settings have been successfully updated.', 'powered-minifier'));
}
?>
<div class="wrap">
    <section class="wpbnd-wrapper">
        <div class="wpbnd-container">
            <div class="wpbnd-tabs">
                <?php echo $this->return_plugin_header(); ?>
                <main class="tabs-main">
                    <?php echo $this->return_tabs_menu('tab1'); ?>
                    <section class="tab-section">
                        <?php if(isset($notice)) { ?>
                        <div class="wpbnd-notice <?php echo esc_attr($notice[0]); ?>">
                            <span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
                            <span><?php echo esc_attr($notice[1]); ?></span>
                        </div>
                        <?php } elseif((isset($opts['source']) && ($opts['source']) == 'off')) { ?>
                        <div class="wpbnd-notice warning">
                            <span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
                            <span><?php echo _e('You have not set up your minifier options ! In order to do so, please use the below form.', 'powered-minifier'); ?></span>
                        </div>
                        <?php } else { ?>
                        <div class="wpbnd-notice info">
                            <span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
                            <span><?php echo _e('Your plugin is properly configured ! You can change at anytime your minifier options using the below form.', 'powered-minifier'); ?></span>
                        </div>
                        <?php } ?>
                        <form method="POST">
                            <input type="hidden" name="pwm-update-option" value="true" />
                            <?php wp_nonce_field('pwm-referer-form', 'pwm-referer-option'); ?>
                            <div class="wpbnd-form">
                                <div class="field">
                                    <?php $fieldID = uniqid(); ?>
                                    <span class="label"><?php echo _e('Minify HTML', 'powered-minifier'); ?></span>
                                    <div class="onoffswitch">
                                        <input id="<?php echo esc_attr($fieldID); ?>" type="checkbox" name="_powered_minifier[source]" class="onoffswitch-checkbox input-status" <?php if((isset($opts['source'])) && ($opts['source'] == 'on')) { echo 'checked="checked"';} ?>/>
                                        <label class="onoffswitch-label" for="<?php echo esc_attr($fieldID); ?>">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                    <small><?php echo _e('Do you want to minify your HTML HTML source ?', 'powered-minifier'); ?></small>
                                </div>
                                <div id="handler-minifier" class="subfield <?php if((isset($opts['source'])) && ($opts['source'] == 'on')) { echo 'show'; } ?>">
                                    <div class="field">
                                        <span class="label"><?php echo _e('Minification Level', 'powered-minifier'); ?></span>
                                        <?php
                                        $level = array
                                        (
                                            'soft' => __('Low (higher readability)', 'powered-minifier'),
                                            'hard' => __('Highest (no readability, smallest size)', 'powered-minifier')
                                        );

                                        if(!isset($opts['level']))
                                        {
                                            $opts = array('level' => 'soft');
                                        }

                                        foreach($level as $minkey => $minval)
                                        {
                                            $fieldID = uniqid();
                                            $output = '<label class="radiobox">';
                                            $output.= '<span>'.$minval.'</span>';
                                            $output.= '<input type="radio" id="'.$fieldID.'" name="_powered_minifier[level]" value="'.$minkey.'" class="common" '.(((isset($opts['level'])) && ($opts['level'] == $minkey)) ? 'checked="checked"' : '').' />';
                                            $output.= '<span class="checkmark"></span>';
                                            $output.= '</label>';
                                            echo $output;
                                        }
                                        ?>
                                    </div>
                                    <div class="field">
                                        <?php $fieldID = uniqid(); ?>
                                        <span class="label"><?php echo _e('Minify CSS', 'powered-minifier'); ?></span>
                                        <div class="onoffswitch">
                                            <input id="<?php echo esc_attr($fieldID); ?>" type="checkbox" name="_powered_minifier[css]" class="onoffswitch-checkbox" <?php if((isset($opts['css'])) && ($opts['css'] == 'on')) { echo 'checked="checked"';} ?>/>
                                            <label class="onoffswitch-label" for="<?php echo esc_attr($fieldID); ?>">
                                                <span class="onoffswitch-inner"></span>
                                                <span class="onoffswitch-switch"></span>
                                            </label>
                                        </div>
                                        <small><?php echo _e('Do you want to minify the CSS code present in the HTML source ?', 'powered-minifier'); ?></small>
                                    </div>
                                    <div class="field">
                                        <?php $fieldID = uniqid(); ?>
                                        <span class="label"><?php echo _e('Minify JS', 'powered-minifier'); ?></span>
                                        <div class="onoffswitch">
                                            <input id="<?php echo esc_attr($fieldID); ?>" type="checkbox" name="_powered_minifier[js]" class="onoffswitch-checkbox" <?php if((isset($opts['js'])) && ($opts['js'] == 'on')) { echo 'checked="checked"';} ?>/>
                                            <label class="onoffswitch-label" for="<?php echo esc_attr($fieldID); ?>">
                                                <span class="onoffswitch-inner"></span>
                                                <span class="onoffswitch-switch"></span>
                                            </label>
                                        </div>
                                        <small><?php echo _e('Do you want to minify the JS code present in the HTML source ?', 'powered-minifier'); ?></small>
                                    </div>
                                    <div class="field">
                                        <?php $fieldID = uniqid(); ?>
                                        <span class="label"><?php echo _e('Remove HTML / CSS and JS Comments', 'powered-minifier'); ?></span>
                                        <div class="onoffswitch">
                                            <input id="<?php echo esc_attr($fieldID); ?>" type="checkbox" name="_powered_minifier[comments]" class="onoffswitch-checkbox" <?php if((isset($opts['comments'])) && ($opts['comments'] == 'on')) { echo 'checked="checked"';} ?>/>
                                            <label class="onoffswitch-label" for="<?php echo esc_attr($fieldID); ?>">
                                                <span class="onoffswitch-inner"></span>
                                                <span class="onoffswitch-switch"></span>
                                            </label>
                                        </div>
                                        <small><?php echo _e('Do you want to remove the HTML / CSS and JS comments from the HTML source ?', 'powered-minifier'); ?></small>
                                    </div>
                                    <div class="field">
                                        <?php $fieldID = uniqid(); ?>
                                        <span class="label"><?php echo _e('Remove HTML / CSS and JS empty lines', 'powered-minifier'); ?></span>
                                        <div class="onoffswitch">
                                            <input id="<?php echo esc_attr($fieldID); ?>" type="checkbox" name="_powered_minifier[empty]" class="onoffswitch-checkbox" <?php if((isset($opts['empty'])) && ($opts['empty'] == 'on')) { echo 'checked="checked"';} ?>/>
                                            <label class="onoffswitch-label" for="<?php echo esc_attr($fieldID); ?>">
                                                <span class="onoffswitch-inner"></span>
                                                <span class="onoffswitch-switch"></span>
                                            </label>
                                        </div>
                                        <small><?php echo _e('Do you want to remove the HTML / CSS and JS empty lines from the HTML source ?', 'powered-minifier'); ?></small>
                                    </div>
                                    <div class="field">
                                        <?php $fieldID = uniqid(); ?>
                                        <span class="label"><?php echo _e('Remove XHTML closing tag from HTML5 void elements', 'powered-minifier'); ?></span>
                                        <div class="onoffswitch">
                                            <input id="<?php echo esc_attr($fieldID); ?>" type="checkbox" name="_powered_minifier[xhtml]" class="onoffswitch-checkbox" <?php if((isset($opts['xhtml'])) && ($opts['xhtml'] == 'on')) { echo 'checked="checked"';} ?>/>
                                            <label class="onoffswitch-label" for="<?php echo esc_attr($fieldID); ?>">
                                                <span class="onoffswitch-inner"></span>
                                                <span class="onoffswitch-switch"></span>
                                            </label>
                                        </div>
                                        <small><?php echo _e('Do you want to remove the XHTML closing tag ? (If you are not sure leave "ON")', 'powered-minifier'); ?></small>
                                    </div>
                                    <div class="field">
                                        <?php $fieldID = uniqid(); ?>
                                        <span class="label"><?php echo _e('Remove relative domain from internal URLs', 'powered-minifier'); ?></span>
                                        <div class="onoffswitch">
                                            <input id="<?php echo esc_attr($fieldID); ?>" type="checkbox" name="_powered_minifier[domain]" class="onoffswitch-checkbox" <?php if((isset($opts['domain'])) && ($opts['domain'] == 'on')) { echo 'checked="checked"';} ?>/>
                                            <label class="onoffswitch-label" for="<?php echo esc_attr($fieldID); ?>">
                                                <span class="onoffswitch-inner"></span>
                                                <span class="onoffswitch-switch"></span>
                                            </label>
                                        </div>
                                        <small><?php echo _e('Do you want to remove the relative domain from the internal URLs ? (If you are not sure leave "OFF")', 'powered-minifier'); ?></small>
                                    </div>
                                    <div class="field">
                                        <?php $fieldID = uniqid(); ?>
                                        <span class="label"><?php echo _e('Remove HTTP/HTTPS from all URLs', 'powered-minifier'); ?></span>
                                        <div class="onoffswitch">
                                            <input id="<?php echo esc_attr($fieldID); ?>" type="checkbox" name="_powered_minifier[scheme]" class="onoffswitch-checkbox" <?php if((isset($opts['scheme'])) && ($opts['scheme'] == 'on')) { echo 'checked="checked"';} ?>/>
                                            <label class="onoffswitch-label" for="<?php echo esc_attr($fieldID); ?>">
                                                <span class="onoffswitch-inner"></span>
                                                <span class="onoffswitch-switch"></span>
                                            </label>
                                        </div>
                                        <small><?php echo _e('Do you want to remove the HTTP & HTTPS schemes from all URLs ? (If you are not sure leave "OFF")', 'powered-minifier'); ?></small>
                                    </div>
                                    <div class="field">
                                        <?php $fieldID = uniqid(); ?>
                                        <span class="label"><?php echo _e('Support Multi-Byte UTF-8 encoding', 'powered-minifier'); ?></span>
                                        <div class="onoffswitch">
                                            <input id="<?php echo esc_attr($fieldID); ?>" type="checkbox" name="_powered_minifier[encoding]" class="onoffswitch-checkbox" <?php if((isset($opts['encoding'])) && ($opts['encoding'] == 'on')) { echo 'checked="checked"';} ?>/>
                                            <label class="onoffswitch-label" for="<?php echo esc_attr($fieldID); ?>">
                                                <span class="onoffswitch-inner"></span>
                                                <span class="onoffswitch-switch"></span>
                                            </label>
                                        </div>
                                        <small><?php echo _e('Do you want to support multi-byte encoding if you see odd encoding ? (If you are not sure leave "OFF")', 'powered-minifier'); ?></small>
                                    </div>
                                    <div class="field">
                                        <?php $fieldID = uniqid(); ?>
                                        <span class="label"><?php echo _e('Display Stats', 'powered-minifier'); ?></span>
                                        <div class="onoffswitch">
                                            <input id="<?php echo esc_attr($fieldID); ?>" type="checkbox" name="_powered_minifier[stats]" class="onoffswitch-checkbox" <?php if((isset($opts['stats'])) && ($opts['stats'] == 'on')) { echo 'checked="checked"';} ?>/>
                                            <label class="onoffswitch-label" for="<?php echo esc_attr($fieldID); ?>">
                                                <span class="onoffswitch-inner"></span>
                                                <span class="onoffswitch-switch"></span>
                                            </label>
                                        </div>
                                        <small><?php echo _e('Do you want to display the minification stats at the bottom of the HTML source ?', 'powered-minifier'); ?></small>
                                    </div>
                                    <div class="field">
                                        <?php $fieldID = uniqid(); ?>
                                        <span class="label"><?php echo _e('Debug Comments', 'powered-minifier'); ?></span>
                                        <div class="onoffswitch">
                                            <input id="<?php echo esc_attr($fieldID); ?>" type="checkbox" name="_powered_minifier[debug]" class="onoffswitch-checkbox" <?php if((isset($opts['debug'])) && ($opts['debug'] == 'on')) { echo 'checked="checked"';} ?>/>
                                            <label class="onoffswitch-label" for="<?php echo esc_attr($fieldID); ?>">
                                                <span class="onoffswitch-inner"></span>
                                                <span class="onoffswitch-switch"></span>
                                            </label>
                                        </div>
                                        <small><?php echo _e('Do you want to display the debug html comments at the header and footer of the HTML source ?', 'powered-minifier'); ?></small>
                                    </div>
                                    <div class="field">
                                        <?php $fieldID = uniqid(); ?>
                                        <span class="label"><?php echo _e('Console Load Time', 'powered-minifier'); ?></span>
                                        <div class="onoffswitch">
                                            <input id="<?php echo esc_attr($fieldID); ?>" type="checkbox" name="_powered_minifier[loadtime]" class="onoffswitch-checkbox" <?php if((isset($opts['loadtime'])) && ($opts['loadtime'] == 'on')) { echo 'checked="checked"';} ?>/>
                                            <label class="onoffswitch-label" for="<?php echo esc_attr($fieldID); ?>">
                                                <span class="onoffswitch-inner"></span>
                                                <span class="onoffswitch-switch"></span>
                                            </label>
                                        </div>
                                        <small><?php echo _e('Do you want to display the page loading time into your browser console ? (If you are not sure leave "OFF")', 'powered-minifier'); ?></small>
                                    </div>
                                </div>
                                <div class="form-footer">
                                    <input type="submit" class="button button-primary button-theme" style="height:45px;" value="<?php _e('Update Settings', 'powered-minifier'); ?>">
                                </div>
                            </div>
                        </form>
                    </section>
                </main>
            </div>
        </div>
    </section>
</div>