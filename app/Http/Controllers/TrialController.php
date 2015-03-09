<?php namespace Phonex\Http\Controllers;

use Securimage;

class TrialController extends Controller {

    public function getCaptcha(){
        $img = new Securimage();
        // You can customize the image by making changes below, some examples are included - remove the "//" to uncomment
        //$img->session_name = 'phone-x';
        //$img->ttf_file        = './Quiff.ttf';
        //$img->captcha_type    = Securimage::SI_CAPTCHA_MATHEMATIC; // show a simple math problem instead of text
        //$img->case_sensitive  = true;                              // true to use case sensitve codes - not recommended
        $img->image_height    = 120;                                // height in pixels of the image
        $img->image_width     = $img->image_height * M_E;          // a good formula for image size based on the height
        //$img->perturbation    = .75;                               // 1.0 = high distortion, higher numbers = more distortion
        //$img->image_bg_color  = new Securimage_Color("#0099CC");   // image background color
        //$img->text_color      = new Securimage_Color("#EAEAEA");   // captcha text color
        //$img->num_lines       = 8;                                 // how many lines to draw over the image
        //$img->line_color      = new Securimage_Color("#0000CC");   // color of lines over the image
        //$img->image_type      = SI_IMAGE_JPEG;                     // render as a jpeg image
        //$img->signature_color = new Securimage_Color(rand(0, 64),
        //                                             rand(64, 128),
        //                                             rand(128, 255));  // random signature color

        // see securimage.php for more options that can be set

        $img->show();  // outputs the image and content headers to the browser
        // alternate use:
        // $img->show('/path/to/background_image.jpg');
    }

}
