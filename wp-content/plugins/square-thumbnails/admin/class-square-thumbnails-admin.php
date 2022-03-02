<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://ilmdesigns.com/
 * @since      1.0.0
 *
 * @package    Square_Thumbnails
 * @subpackage Square_Thumbnails/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Square_Thumbnails
 * @subpackage Square_Thumbnails/admin
 * @author     ILMDESIGNS <narcisbodea@gmail.com>
 */
class Square_Thumbnails_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;
        
        
        private $option_name = 'square_thumbnails';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

        public function display_admin_page(){
            add_submenu_page('upload.php',
                    'Square Thumbnails Options',
                    'Square Thumbnails',
                    'manage_options',
                    'square-thumbnails-admin-page',
                    array($this,'showPage'),
                    '',
                    '3.0'
           );
           do_action( 'square-thumbnails-settings');            
        }
        
        
	public function link_settings( $links ) {

		$links[] = sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'upload.php?page=square-thumbnails-admin-page' ) ), esc_html__( 'Settings', 'square-thumbnails' ) );

		return $links;

	} // link_settings()        
        
        
        public function showPage(){
            include 'partials/square-thumbnails-admin-display.php';
        }
            
        private function floodFill($newim){
//              $dofill=get_option($this->option_name.'_dofill');                                  
//              if(!empty($dofill)){
//                    $red=255;
//                    $green=255;
//                    $blue=255;
//                    $htmlcolor=get_option($this->option_name.'_bgcolor');
//                    $ret=$this->hex2RGB($htmlcolor);
//                        $red =  $ret['red'] ; 
//                        $green =  $ret['green']; 
//                        $blue =  $ret['blue'] ;     
//                    $white=imagecolorallocate($newim,$red,$green,$blue);  
//                    imagefill($newim, 0, 0, $white);
//              }              
        }
        
        private function getColor($im){
                  $getimbg=get_option($this->option_name.'_getimcolor');
                  if(!empty($getimbg)){
                        $rgb = imagecolorat($im, 0, 0);
                        $colors = imagecolorsforindex($im, $rgb);    
                        $red=$colors['red'];
                        $green=$colors['green'];
                        $blue=$colors['blue'];
                  }
                  else {
                        $red=255;
                        $green=255;
                        $blue=255;
                        $htmlcolor=get_option($this->option_name.'_bgcolor');
                        $ret=$this->hex2RGB($htmlcolor);
                            $red =  $ret['red']; 
                            $green =  $ret['green']; 
                            $blue =  $ret['blue'];   
                  }
                  return array('red'=>$red,'green'=>$green,'blue'=>$blue);
        }
        
        

        
        private function getPaths($filename){
                $updir = wp_upload_dir();
                $file = trailingslashit($updir['basedir']).$filename;                
                $dir=trailingslashit(dirname($file));                  
                $path=new stdClass();
                $path->upload=$updir;
                $path->file=$file;
                $path->dir=$dir;
                return $path;                
        }
        
        
        private function createIm($mime,$file,&$im){
              if($mime=='image/png'){
                  $im= imagecreatefrompng($file);
              }
              elseif($mime=='image/jpeg'){
                  $im= imagecreatefromjpeg($file);
              }
              elseif($mime=='image/gif'){
                  $im= imagecreatefromgif($file);
              }
              elseif($mime=='image/bmp'){
                  $im= imagecreatefromgif($file);
              }
              elseif($mime=='image/vnd.wap.wbmp'){
                  $im= imagecreatefromwbmp($file);
              }             
        }
        
        private function saveIm($mime,&$newim,$f){
                  if($mime=='image/png'){
                      imagepng($newim, $f);
                  }
                  elseif($mime=='image/jpeg'){
                      imagejpeg($newim, $f);
                  }
                  elseif($mime=='image/bmp'){
                      imagebmp($newim, $f);
                  }
                  elseif($mime=='image/gif'){
                      imagegifg($newim, $f);
                  }
                  elseif($mime=='image/vnd.wap.wbmp'){
                      imagewbmp($newim, $f);
                  }          
        }
        
        
        private  function getSizes($imw,$imh){
                $sizes=new stdClass();

                $originalW=$imw;
                $originalH=$imh;                    
                
                $sizes->originalW=$originalW;
                $sizes->originalH=$originalH;
                
                
                $sw=$imw;
                $sh=$imh;
                if($imw>$imh){
                    $sh=$imw;
                }
                else{
                    $sw=$imh;
                }     
                $sizes->sqW=$sw;
                $sizes->sqH=$sh;
                
                
                //resize fom original
                if($this->width>$this->height){
                    $raport=($this->width/$this->height);
                    $twidth=$sw;
                    $theight=$twidth/$raport;
                }
                else{
                    $raport=($this->width/$this->height);
                    $theight=$sw;
                    $twidth=$theight*$raport;
                }

                    $sizes->resizedW=$twidth;
                    $sizes->resizedH=$theight;
                //end resize
          
                $newimx=0;
                $newimy=0;
                $proportion=$twidth/$theight;
                $h=$sw;
                $w=$sw;
                $halign= get_option($this->option_name.'_halign');
                $valign= get_option($this->option_name.'_valign');
                if(empty($halign)) $halign='center';
                if(empty($valign)) $valign='middle';
                if($twidth>$theight){
                         $h=$w/$proportion;
                         switch ($valign){
                             case 'top':
                                 $newimy=0;
                                 break;
                             case 'middle':
                                 $newimy=($w-$h)/2;
                                 break;
                             case 'bottom':
                                 $newimy=($w-$h);
                                 break;
                         }

                }
                else{
                         $w=$w*$proportion;
                         $h=$sh;
                         switch ($halign){
                             case 'left':
                                 $newimx=0;
                                 break;
                             case 'center':
                                 $newimx=($sw-$w)/2;
                                 break;
                             case 'right':
                                 $newimx=($sw-$w);
                                 break;
                         }                                         

                }
                $sizes->x=$newimx;
                $sizes->y=$newimy;
                return $sizes;
        }

        private function allSizes(){
                global $_wp_additional_image_sizes;
                $sizes=$_wp_additional_image_sizes;
                $allS= get_intermediate_image_sizes();
                              
               foreach($allS as $t){
                   if(!isset($sizes[$t])){
                        $sizes[$t]=array(
                            'width'=>get_option( "{$t}_size_w" ),
                            'height'=>get_option( "{$t}_size_h" ),
                            'crop'=>(bool)get_option( "{$t}_size_crop" ),      
                        );                       
                   }
                   
               }  
               return $sizes;
        }
        public function create_square($meta){
                if(!function_exists('imagecreatefromjpeg')) return;                
                //get paths
                $path=$this->getPaths($meta['file']);                
                $file=$this->dir. basename($meta['file']);
                
                //set mime type if missing in $meta                
                if(!isset($meta['mime-type']) || empty($meta['mime-type'])){
                    $meta['mime-type']=image_type_to_mime_type (exif_imagetype($file));
                }                

                    $sizes=$this->getSizes($meta['width'],$meta['height'],false);
                
                
                //create image from file 
                //the image will be reurned as refference in $im
                //$this->createIm($meta['mime-type'], $file, $im);
                
                //create the canvas for the square
                $newim = imagecreatetruecolor($sizes->sqW, $sizes->sqH);	

                //get the color of new bg                                  
                $bgcolor=$this->getColor($this->im);   
                //create color
                $imcolor=imagecolorallocate($newim,$bgcolor['red'],$bgcolor['green'],$bgcolor['blue']);                                  
                //set color
                imagefilledrectangle($newim,0,0,$sizes->sqW, $sizes->sqH,$imcolor);

                //aici era size dar o mutam sus
                imagecopyresampled ( $newim, $this->im, $sizes->x, $sizes->y, 0, 0 ,  $sizes->resizedW, $sizes->resizedH, $this->width, $this->height );
                //flood fill
                //echo '<pre>'.$file.'</pre>';
                $this->saveIm($meta['mime-type'],$newim,$file);
                imagedestroy($newim); 
                return $sizes;
  
        }
        public function make_square_size_image($meta){
            
                if(!function_exists('imagecreatefromjpeg')) return;
                if($meta['width']===$meta['height']){
                    return $meta;
                }   

                $file=$meta['file'];                
                $path=$this->getPaths($file);    
                if(!isset($meta['mime-type']) || empty($meta['mime-type'])){
                    $meta['mime-type']=image_type_to_mime_type (exif_imagetype($path->file));
                } 
                $this->file=$path->file;
                $this->dir=$path->dir;
                $this->width=$meta['width'];
                $this->height=$meta['height'];
                //load the original image in $this->im
                $this->createIm($meta['mime-type'], $path->file, $this->im);
                
                $allsizes=$this->allSizes();
                
                //create all sizes
                $isallsizes=get_option($this->option_name.'_addallsizes');
                if(!empty($isallsizes)){
                        $parts = pathinfo($file);             
                        $name=$parts['filename'];
                        $ext=$parts['extension'];
                        foreach($allsizes as $szname=>$sz){
                            if(!isset($meta['sizes'][$szname])){
                                if(empty($sz['width'])) $sz['width']=$sz['height'];
                                if(empty($sz['height'])) $sz['height']=$sz['width'];
                                $meta['sizes'][$szname]=array(
                                    'file'=>$name.'-'.$sz['width'].'x'.$sz['height'].'.'.$ext,
                                    'width'=>$sz['width'],
                                    'height'=>$sz['height'],
                                    'mime-type'=>$meta['mime-type'],
                                );
                            }
                        }
                }                
                
                //end create all sizes
                
                foreach($meta['sizes'] as $size=>$m){
                    //$m['file']=$path->dir.$m['file'];
                    $result=$this->create_square($m);                    
                    $meta[$size]['width']=$result->sqW;
                    $meta[$size]['height']=$result->sqH;
                }
                

                
                $original=get_option($this->option_name.'_tooriginal');
                if(!empty($original)){
                    $this->create_square($meta,array(
                        'width'=>$meta['width'],
                        'height'=>$meta['height'],
                            ));                                        
                            if($meta['width']>$meta['height']){
                                $meta['height']=$meta['width'];
                            }
                            else{
                                $meta['width']=$meta['height'];
                            }                    
                }                
                imagedestroy($this->im);
                return $meta;
        }

        
 function hex2RGB($hexStr, $returnAsString = false, $seperator = ',') {
    $hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); 
    $rgbArray = array();
    if (strlen($hexStr) == 6) { 
        $colorVal = hexdec($hexStr);
        $rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
        $rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
        $rgbArray['blue'] = 0xFF & $colorVal;
    } elseif (strlen($hexStr) == 3) { 
        $rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
        $rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
        $rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
    } else {
        return false; 
    }
    return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray; 
}       
        
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
        
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Square_Thumbnails_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Square_Thumbnails_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
                wp_enqueue_style( 'wp-color-picker' ); 
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/square-thumbnails-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Square_Thumbnails_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Square_Thumbnails_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

         
        // Include our custom jQuery file with WordPress Color Picker dependency
                //wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/square-thumbnails-admin.js', array( 'wp-color-picker' ), false, true ); 
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/square-thumbnails-admin.js', array( 'jquery','wp-color-picker','jquery-ui-tabs' ), $this->version, false );

	}
        
        
        function sqt_settings_save() {
            // Do whatever you need with update_option() here.
            // You have full access to the $_POST object.
            update_option($this->option_name.'_halign', $_POST['halign']);
            update_option($this->option_name.'_valign', $_POST['valign']);
            update_option($this->option_name.'_bgcolor', $_POST['bgcolor']);
            update_option($this->option_name.'_getimcolor', $_POST['getimcolor']);
            update_option($this->option_name.'_dofill', $_POST['dofill']);
            update_option($this->option_name.'_tooriginal', $_POST['tooriginal']);
            update_option($this->option_name.'_addallsizes', $_POST['addallsizes']);
            wp_die();
        }
        
        
        public function square_settings(){
            //$this->enqueue_scripts();


            
        
        }
        public function old_wp_version_error(){
            return;
            ?>
                <div class="notice-dismiss">
                    bla bla bla
                </div>
                <?php
        }
}
