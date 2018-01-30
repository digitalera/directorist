
<?php

$categories = get_terms(ATBDP_CATEGORY, array('hide_empty' => 0));
$locations = get_terms(ATBDP_LOCATION, array('hide_empty' => 0));
// get bg image if our directorist theme is active else, use the default bg.
$bgimg = get_theme_mod('directorist_home_bg', ATBDP_PUBLIC_ASSETS.'images/home_page_bg.jpg');
// get search page title and sub title from the plugin settings page
$search_title = get_directorist_option('search_title', '');
$search_subtitle = get_directorist_option('search_subtitle', '');
$search_placeholder = get_directorist_option('search_placeholder', __('What are you looking for?', ATBDP_TEXTDOMAIN));

$show_popular_category = get_directorist_option('show_popular_category', 'yes');

$popular_cat_title = get_directorist_option('popular_cat_title', __('Browse by popular categories', ATBDP_TEXTDOMAIN));
$popular_cat_num = get_directorist_option('popular_cat_num', 10);

?>
<!-- start search section -->
<div class="directorist directory_search_area single_area" style="background-image: url('<?php echo esc_url($bgimg);?>')" >
    <!-- start search area container -->
    <div class="<?php echo is_directoria_active() ? 'container': ' container-fluid'; ?>">

        <div class="row">
            <!-- start col-md-12 -->
            <div class="col-md-12">
                <!-- start directory_main_area -->
                <div class="directory_main_content_area">
                    <!-- start search area -->
                    <div class="search_area">
                        <div class="title_area">
                            <h2 class="title"><?php  echo esc_html($search_title);?></h2>
                            <p class="sub_title"><?php  echo esc_html($search_subtitle); ?></p>
                        </div><!--- end title area -->

                        <div class="search_form_wrapper">
                            <form action="<?php echo ATBDP_Permalink::get_search_result_page_link(); ?>" role="form">
                                <!-- @todo; if the input fields break in different themes, use bootstrap form inputs then -->
                                <div class="single_search_field search_query" >
                                    <input type="text" name="q" placeholder="<?php  echo esc_html($search_placeholder); ?>">
                                </div>

                                <div class="single_search_field search_category" >
                                    <select name="in_cat" class="directory_field" id="at_biz_dir-category">
                                        <option value=""><?php _e('Select a category', ATBDP_TEXTDOMAIN ); ?></option>

                                        <?php
                                        foreach ( $categories as $category ) {
                                            echo "<option id='atbdp_category' value='$category->slug'>$category->name</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="single_search_field search_location">
                                    <select name="in_loc" class="directory_field" id="at_biz_dir-location">
                                        <!--This text comes from js, translate them later @todo; translate js text-->
                                        <option value=""><?php _e('Select a location', ATBDP_TEXTDOMAIN); ?></option>

                                        <?php foreach ($locations as $location) {
                                            echo "<option id='atbdp_location' value='$location->slug'>$location->name</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <!--Hidden input fields for our custom post-->
                                <div class="submit_btn"><button type="submit"><span class="fa fa-search"></span></button></div>
                            </form>
                        </div><!-- end /.search_form_wrapper-->
                    </div><!-- end search area -->


                    <?php if ('yes'== $show_popular_category){ ?>
                    <div class="directory_home_category_area">

                        <span><?php _e('Or', ATBDP_TEXTDOMAIN); ?></span>
                        <p><?php echo esc_html($popular_cat_title); ?></p>

                        <?php

                        $args = array(
                            'type' => ATBDP_POST_TYPE,
                            'parent' => 0,          // Gets only top level categories
                            'orderby' => 'count',   // Orders the list by post count
                            'order' => 'desc',
                            'hide_empty' => 1,      // Hides categories with no posts
                            'number' => (int) $popular_cat_num,         // No of categories to return
                            'taxonomy' => ATBDP_CATEGORY
                        );
                        $top_categories = get_categories( $args );

                       ?>

                        <ul class="categories">
                            <?php
                            foreach ( $top_categories as $cat ) { ?>
                                <li>
                                    <a href="<?= ATBDP_Permalink::get_category_archive($cat); ?>">
                                        <span class="fa <?= get_cat_icon($cat->term_id); ?>" aria-hidden="true"></span>
                                        <p><?= $cat->name; ?></p>
                                    </a>
                                </li>

                            <?php }
                            ?>

                        </ul>
                    </div><!-- End category area -->
                    <?php } ?>



                </div><!-- end directory_main_area -->
            </div><!--- end col-md-12  -->
        </div>
    </div><!-- end search area container -->
</div>
<!-- end search section -->

