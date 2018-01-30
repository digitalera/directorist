
<div class="directorist  single_area">
    <div class="<?php echo is_directoria_active() ? 'container': ' container-fluid'; ?>">
        <div class="row">
            <div class="col-md-12">
                <div class="add_listing_title">

                    <h2><?php _e('Register', ATBDP_TEXTDOMAIN); ?></h2>
                    <?php if(!empty($_GET['success']) && true == $_GET['success']){ ?>
                        <!--registration succeeded, so show notification -->
                        <p class="success"><span class="fa fa-check"></span><?php _e('Registration completed. Please check your inbox and activate your account.', ATBDP_TEXTDOMAIN); ?></p>
                        <?php
                    } ?>

                    <?php
                    if(!empty($_GET['errors']) && true == $_GET['errors']){ ?>
                        <!--Registration failed, so show notification.-->
                        <p class="warning"> <span class="fa fa-exclamation"></span><?php _e('Registration failed. Please make sure you filed up all the necessary fields.', ATBDP_TEXTDOMAIN); ?></p>
                        <?php
                    }

                    ?>


                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="directory_register_form_wrap">
                            <form action="<?= esc_url(get_the_permalink()); ?>" method="post">
                                <div class="form-group">
                                    <label for="username"><?php _e('Username', ATBDP_TEXTDOMAIN); ?> <strong>*</strong></label>
                                    <input id="username" class="directory_field" type="text" name="username" value="<?= ( isset( $_POST['username'] ) ? esc_attr($_POST['username']) : null ); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="password"><?php _e('Password', ATBDP_TEXTDOMAIN); ?> <strong>*</strong></label>
                                    <input id="password" class="directory_field" type="password" name="password" value="<?= ( isset( $_POST['password'] ) ? esc_attr($_POST['password']) : null ); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="email"><?php _e('Email', ATBDP_TEXTDOMAIN); ?> <strong>*</strong></label>
                                    <input id="email" class="directory_field" type="text" name="email" value="<?= ( isset( $_POST['email']) ? $_POST['email'] : null ); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="website"><?php _e('Website', ATBDP_TEXTDOMAIN); ?></label>
                                    <input id="website" class="directory_field" type="text" name="website" value="<?= ( isset( $_POST['website']) ? esc_url($_POST['website']) : null ); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="fname"><?php _e('First Name', ATBDP_TEXTDOMAIN); ?></label>
                                    <input id="fname" class="directory_field" type="text" name="fname" value="<?= ( isset( $_POST['fname']) ? esc_attr($_POST['fname']) : null ); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="lname"><?php _e('Last Name', ATBDP_TEXTDOMAIN); ?></label>
                                    <input class="directory_field" id="lname" type="text" name="lname" value="<?= ( isset( $_POST['lname']) ? esc_attr($_POST['lname']) : null ); ?>">
                                </div>


                                <div class="form-group">
                                    <label for="bio"><?php _e('About/bio', ATBDP_TEXTDOMAIN); ?></label>
                                    <textarea id="bio" class="directory_field" name="bio" rows="10"><?= ( isset( $_POST['bio']) ? esc_textarea($_POST['bio']) : null ); ?></textarea>

                                </div>

                                <div class="directory_regi_btn">
                                    <button type="submit" class="btn btn-default" name="atbdp_user_submit"><?php _e('Sign Up', ATBDP_TEXTDOMAIN); ?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!--ends .row-->
    </div>
</div>

