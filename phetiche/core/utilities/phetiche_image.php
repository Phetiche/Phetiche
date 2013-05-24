<?php

/**
 * The Phetiche image processor
 *
 * @file			phetiche/core/utilities/phetiche_image.php
 * @description		Does some basic image processing.
 * @author			Stefan Aichholzer <yo@stefan.ec>
 * @package			Phetiche/core
 * @license			BSD/GPLv2
 *
 * (c) copyright Stefan Aichholzer
 * This source file is subject to the BSD/GPLv2 License.
 */
class Phetiche_image {

	/**
	 * All the image to be processed
	 * @var array
	 */
	private $images = [];

	/**
	 * General image sizes applied to the batch
	 * @var array
	 */
	private $global_sizes = [];

	/**
	 * Where to store the images (temporary)
	 * @var string
	 */
	private $global_store_path = null;

	/**
	 * The current image (name) being used
	 * @var string
	 */
	private $current = '';


	/**
	 * Class/object constructor.
	 *
	 * @author	Stefan Aichholzer <yo@stefan.ec>
	 * @return	void
	 */
	public function __construct()
	{

	}


	/**
	 * Renders (or returns) a simple image on the fly.
	 *
	 * If $html_tag = false then the image object will be returned, in which case the proper
	 * header -header('Content-Type: image/png');- must be send on the delivery end point.
	 *
	 * @author	Stefan Aichholzer <yo@stefan.ec>
	 * @param 	integer $width The width of the image
	 * @param 	integer $height The height of the image
	 * @param 	string $text The text to be written on the image
	 * @param 	integer $text_size The text size (1 to 5)
	 * @param 	integer $text_x Position in X of the text
	 * @param 	integer $text_y Position in Y of the text
	 * @param 	string $text_color R:G:B color of the text
	 * @param 	string $bg_color R:G:B color of the background
	 * @param 	integer $quality Compression level
	 * @param 	boolean $html_tag Should the image be returned as an HTML tag
	 * @return 	mixed (string|object) The image to be returned, either as HTML or as an object
	 */
	public function render($width = null, $height = null, $text = null, $text_size = 3, $text_x = 5, $text_y = 5, $text_color = '255:255:255', $bg_color = '0:0:0', $quality = 5, $html_tag = false)
	{
		if (!$width || !$height || !$text) {
			return false;
		}

		if (!$image = imagecreate($width, $height)) {
			throw new Phetiche_error('Cannot initialize image stream.');
		}

		// Set the background color
		list($red, $green, $black) = explode(':', $bg_color);
		$background_color = imagecolorallocate($image, $red, $green, $black);

		// Set the text color
		list($red, $green, $black) = explode(':', $text_color);
		$text_color = imagecolorallocate($image, $red, $green, $black);

		imagestring($image, $text_size, $text_x, $text_y, $text, $text_color);

		// This is to prevent the image to be rendered at once.
		ob_start();
		imagepng($image, null, $quality, PNG_NO_FILTER);
		$image_object = ob_get_contents();
		imagedestroy($image);
		ob_end_clean();

		if ($html_tag) {
			$image = 'data:image/png;base64,' . base64_encode($image_object);
			return '<img width="' . $width . '" height="' . $height . '" src="' . $image . '" />';
		} else {
			//header('Content-Type: image/png');
			return $image_object;
		}
	}


	/**
	 * Inputs an image to be processed from a local resource.
	 *
	 * @author	Stefan Aichholzer <yo@stefan.ec>
	 * @param	string $name The name of the input image.
	 * @param 	string $filename The filename (path) of the input image.
	 * @return	object $this Class instance
	 */
	public function input($name = '', $filename = '')
	{
		if ($name && $filename) {
			$output_name = Phetiche_cypher::randomCode(microtime(true));
			$this->current = $output_name;
			$this->images[$this->current] = ['name' => basename($name), 'file' => $filename, 'output_name' => $output_name];
		}

		return $this;
	}

	/**
	 * Inputs an (URL) image to be processed.
	 * The image will be downloaded to the /tmp folder (or where specified)
	 * and be processed from there.
	 *
	 * @author	Stefan Aichholzer <yo@stefan.ec>
	 * @param	string $url The URL to the final image.
	 * @param 	string $temp_path The temporary path where to store the image.
	 * @return	object $this Class instance
	 */
	public function url($url = '', $temp_path = '')
	{
		if ($url) {

			$temp_name = Phetiche_cypher::randomCode(microtime(true));
			$temp_path = ($temp_path && is_writable($temp_path)) ? $temp_path : '/tmp/' . $temp_name;

			// Download the image to the local temporary folder, it will be processed from here.
			if (ini_get('allow_url_fopen')) {
				file_put_contents($temp_path, file_get_contents($url));
			} else {
				if ($fp = fopen($temp_path, 'wb')) {
					$ch = curl_init($url);
					curl_setopt($ch, CURLOPT_FILE, $fp);
					curl_setopt($ch, CURLOPT_HEADER, 0);
					curl_exec($ch);
					curl_close($ch);
					fclose($fp);
				}
			}

			$this->current = $temp_name;
			$this->images[$this->current] = ['name' => $temp_name, 'file' => $temp_path, 'output_name' => $temp_name];
		}

		return $this;
	}


	/**
	 * Define the output arguments for an image, one at a time.
	 *
	 * @author	Stefan Aichholzer <yo@stefan.ec>
	 * @param	integer $width The disired width.
	 * @param 	integer $height The desired height.
	 * @param	boolean $lock Should the resize process be locked or not (See above)
	 * @param 	string $extension The extension to be used for the output image.
	 * @param 	integer $quality The quality to be used for the output image.
	 * @return	void
	 */
	public function output($width = null, $height = null, $lock = false, $extension = 'jpg', $quality = 90)
	{
		if (!isset($this->images[$this->current])) {
			throw new Phetiche_error(5003);
		}

		$image_data = ['width' => $width, 'height' => $height, 'lock' => $lock, 'extension' => str_replace('.', '', $extension), 'quality' => $quality];
		$this->images[$this->current]['sizes'][] = $image_data;

		return $this;
	}


	/**
	 * Define the output arguments for an image, applied to all images.
	 *
	 * @author	Stefan Aichholzer <yo@stefan.ec>
	 * @param	mixed, string|array $sizes The disired sizes to be applied.
	 * @param	boolean $lock Should the resize process be locked or not (See above).
	 * @name	$name The name to be applied to the output image.
	 * @param	$extension The extension to be used for the output image.
	 * @param	$quality The quality to be used for the output image.
	 * @return	void
	 */
	public function outputs($sizes = null, $lock = false, $extension = 'jpg', $quality = 90)
	{
		if (!is_array($sizes)) {
			$sizes = explode(',', $sizes);
		}

		if ($sizes) {
			foreach ($sizes as $size) {
				list($width, $height) = explode('x', strtolower(trim($size)));
				$image_data = ['width' => $width, 'height' => $height, 'lock' => $lock, 'extension' => str_replace('.', '', $extension), 'quality' => $quality];

				$this->global_sizes[] = $image_data;
			}
		}

		return $this;
	}


	public function destination($path = '')
	{
		if (isset($this->images[$this->current])) {
			$this->images[$this->current]['store_path'] = ($path) ? $path : null;
		} else {
			$this->global_store_path = ($path) ? $path : null;
		}

		return $this;
	}


	/**
	 * Applies all changes to the images in the image stack
	 *
	 * Resize process is applied as follows:
	 *
	 * $size['width'] && !$size['height'] && !$size['lock']
	 *  Resizes the image to the given width. Maintains the aspect ratio.
	 *
	 * !$size['width'] && $size['height'] && !$size['lock']
	 *  Resizes the image to the given height. Maintains the aspect ratio.
	 *
	 * $size['width'] && $size['height'] && !$size['lock']
	 *  Resizes the image to the given width and height. Does not maintains the aspect ratio.
	 *  (Image will be distorted)
	 *
	 * $size['width'] && $size['height'] && $size['lock']
	 *  Resizes the image to the given width and height, maintaining the aspect ratio,
	 *  so that the smallest side equals the given width or height.
	 *  It will crop the remains of the largest side.
	 *
	 * @author Stefan Aichholzer <yo@stefan.ec>
	 * @return mixed: array|string $result The image name or array of image names.
	 * 		   An array is returned if more than one image is processed.
	 *
	 * @todo Apply the global settings
	 */
	public function apply()
	{
		$image_names = [];

		foreach ($this->images as $image) {

			/**
			 * Try to set the folder to be used for the temp. images.
			 * If no folder is defined /tmp/ will be used.
			 * If /tmp/ (last resort) is not available, an exception will be thrown.
			 */
			if (isset($image['store_path']) && is_dir($image['store_path'])) {
				$store_folder = $image['store_path'];
			} else if (is_dir($this->global_store_path)) {
				$store_folder = $this->global_store_path;
			} else if (is_writable('/tmp/')) {
				$store_folder = '/tmp/';
			} else {
				throw new Phetiche_error(5004);
			}

			/**
			 * Process the image size defined for each image
			 */
			foreach ($image['sizes'] as $size) {

				$crop = true;

				/**
				 * If the string "original" is provided as the width (and only parameter), then the original image
				 * will be added to the image stack as well and returned.
				 * The original image will be converted to JPG but not scaled nor cropped.
				 */
				if ($size['width'] == 'original') {
        			$final_output = $store_folder . 'original_' . $image['output_name'] .'.'. $size['extension'];
					$final_size = 'original';
					$crop = false;
				} else {

					$final_output = $store_folder . $size['width'] .'x'. $size['height'] . '_' . $image['output_name'] .'.'. $size['extension'];
					$final_size = $size['width'] .'x'. $size['height'];

					if ($size['width'] && !$size['height'] && !$crop) {
						$size['height'] = null;
					} else if (!$size['width'] && $size['height'] && !$crop) {
						$size['width'] = null;
					} else if ($size['width'] && $size['height'] && !$crop) {
						$crop = false;
					}
				}

				if (!$this->process($image['file'], $final_output, $size['width'], $size['height'], $crop, $size['extension'], $size['quality'])) {
					throw new Phetiche_error(5004);
				}

				$image_names[$image['output_name'] . '.' . $size['extension']][] = ['size' => $final_size,
																					'width' => $size['width'],
																					'heigth' => $size['height'],
																					'name' => $image['output_name'],
																					'extension' => $size['extension'],
																					'path' => $final_output];
			}
		}

		$this->images = [];
		return $image_names;
	}


	/**
	 * Applies the actual transformation.
	 *
	 * @todo Default to ImageMagick (if possible)
	 * @author	Stefan Aichholzer <yo@stefan.ec>
	 * @see Phetiche_image->apply()
	 */
	private function process($input = '', $output = '', $width = null, $height = null, $keep_ratio = true, $extension, $quality)
	{
		/**
		 * Get the properties for the current image
		 * Array (
    	 *   [0] => 1680	// Width
    	 *   [1] => 1050	// Height
    	 *   [2] => 2		// Image type
    	 *   [3] => width="1680" height="1050"
    	 *   [bits] => 8
    	 *   [channels] => 3
		 *   [mime] => image/jpeg
		 * )
		 */
		$image_properties = getimagesize($input);

		if ($width == 'original') {
			$width = $image_properties[0];
			$height = $image_properties[1];
		}

		$output_width = $width;
		$output_height = $height;
		$crop_to_width = $crop_to_height = 0;

		// Force the resize process to maintain the original aspect ratio.
		if ($keep_ratio) {
			if ($image_properties[0] > $width && $image_properties[1] > $height) {

				/**
				 * Check if the image is landscape or portrait and calculate
				 * a basic reposition value to center it (the image) on the output image.
				 *
				 * Since the image is resized while ratio is maintained, it could be possible
				 * that the resulting width or height is less than the actual desired output width or height.
				 * In either case the height and width, respectively, needs to be increased
				 * (and cropped) to accomodate the full width or height.
				 */
				if ($image_properties[0] > $image_properties[1]) {

					$crop_to_width = $this->getCropWidth($width, $output_width, $height, $image_properties);

					// Resized output width check
					if ($width < $output_width) {
						$width = $output_width;
						$height = $height + ($height * ($this->calculateIncreasePercent($output_width, $width)) * 0.01);
						$crop_to_width = 0;

						$crop_to_height = $this->getCropHeight($height, $output_height, $width, $image_properties);
					}

				} else if ($image_properties[0] < $image_properties[1]) {

					$crop_to_height = $this->getCropHeight($height, $output_height, $width, $image_properties);

					// Resized output height check
					if ($height < $output_height) {
						$height = $output_height;
						$width = $width + ($width * ($this->calculateIncreasePercent($output_height, $height)) * 0.01);
						$crop_to_height = 0;

						$crop_to_width = $this->getCropWidth($width, $output_width, $height, $image_properties);
					}

				}
			} else {
				$width = $image_properties[0];
				$crop_to_width = ($width - $output_width) / 2;

				$height = $image_properties[1];
				$crop_to_height = ($height - $output_height) / 2;
			}
		}

		/**
		 * If the image is square, then apply the resize (to the specified dimensions) and crop on the height
		 * if cropping needs to be done, of course.
		 */
		if ($image_properties[0] == $image_properties[1]) {
			$height = (100 / ($image_properties[0] / $width)) * .01;
			$height = round ($image_properties[1] * $height);
			$crop_to_height = ($height - $output_height) / 2;
		}

		// Create an image from the proper source type
		switch ($image_properties[2]) {
			case IMAGETYPE_GIF: $img_obj = ImageCreateFromGIF($input);
				break;

			case IMAGETYPE_JPEG: $img_obj = ImageCreateFromJPEG($input);
				break;

			case IMAGETYPE_PNG: $img_obj = ImageCreateFromPNG($input);
				break;

			case IMAGETYPE_WBMP: $img_obj = ImageCreateFromWBMP($input);
				break;

			case IMAGETYPE_XBM: $img_obj = ImageCreateFromXBM($input);
				break;

			default: $img_obj = null;
		}

		if (!$img_obj) {
			return false;
		} else {
			$thumb = ImageCreateTrueColor($output_width, $output_height);
			ImageCopyResampled($thumb, $img_obj, -$crop_to_width, -$crop_to_height, 0, 0, $width, $height, $image_properties[0], $image_properties[1]);

			/**
			 * Create the proper image based on the extension given.
			 * Since for .png the quality must range from 0 to 9, apply
			 * a simple conversion to get the correct value from the original input (0 - 100)
			 */
			switch ($extension) {
				case 'png': $quality = ceil(($quality * 9) / 100);
							imagepng($thumb, $output, $quality); break;

				case 'gif': imagegif($thumb, $output); break;

				case 'bmp': imagewbmp($thumb, $output); break;

				case 'jpg':
				default: imagejpeg($thumb, $output, $quality);
			}

			imagedestroy($img_obj);
			imagedestroy($thumb);

			return true;
		}

	}


	/**
	 * Calculate a difference in percent between two numbers.
	 * Used in this case to increase the size of an output image.
	 *
	 * @author	Stefan Aichholzer <yo@stefan.ec>
	 * @param	integer $big_value The large value.
	 * @param	integer $small_value The small value.
	 * @return	integer The percent in difference between the two values.
	 */
	private function calculateIncreasePercent($big_value, $small_value)
	{
		$increase_percent = (($big_value - $small_value) / $small_value) * 100;
		return $increase_percent;
	}


	/**
	 * Calculate the amount of pixels the image needs to be shifted (to the left)
	 * in order to fully accomodate it in the output (desired) image.
	 *
	 * @author	Stefan Aichholzer <yo@stefan.ec>
	 * @param	integer ref $width The output width (will vary from the original)
	 * @param	integer ref $output_width The output width (the original)
	 * @param	integer $height The output height
	 * @param	array $properties The image properties (width and height)
	 * @return	integer The number of pixels to shift the image.
	 */
	private function getCropWidth(&$width, $output_width, $height, $properties)
	{
		$width = (100 / ($properties[1] / $height)) * 0.01;
		$width = round($properties[0] * $width);

		return ($width - $output_width) / 2;
	}


	/**
	 * Calculate the amount of pixels the image needs to be shifted (to the top)
	 * in order to fully accomodate it in the output (desired) image.
	 *
	 * @author	Stefan Aichholzer <yo@stefan.ec>
	 * @param	integer ref $height The output height (will vary from the original)
	 * @param	integer ref $output_height The output height (the original)
	 * @param	integer $width The output width
	 * @param	array $properties The image properties (width and height)
	 * @return	integer The number of pixels to shift the image.
	 */
	private function getCropHeight(&$height, $output_height, $width, $properties)
	{
		$height = (100 / ($properties[0] / $width)) * 0.01;
		$height = round ($properties[1] * $height);

		return ($height - $output_height) / 2;
	}

}
