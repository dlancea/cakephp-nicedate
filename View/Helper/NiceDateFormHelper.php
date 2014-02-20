<?php

App::import('View/Helper', 'FormHelper');

/**
 * Form helper library.
 *
 * Automatic generation of HTML FORMs from given data.
 *
 * @package       Cake.View.Helper
 * @property      HtmlHelper $Html
 * @link http://book.cakephp.org/view/1383/Form
 */
class NiceDateFormHelper extends FormHelper {
	var $helpers = array('Html');
	
	/**
	 * Creates a "duration" input, which is essentialy two DateTimes smooshed together.
	 * 
	 * Requires some javascript in form.js to auto-update dates.
	 * 
	 * @param string $fieldName
	 * @param array $attributes Standard input attributes. (Currently only supports 'empty')
	 * 
	 * @return string Generated duration input HTML 
	 */
	public function duration($fieldName, $attributes){

		if( $attributes['empty'] ){
			$default_start = null;
			$default_end = null;
			$empty = true;
		}else{
			$default_start = 'today 08:00';
			$default_end = 'today 09:00';
			$empty = false;
		}

		$output = $this->dateTime( $fieldName.'.start', 'm/d/Y', 'g:i a', array('class' => 'start', 'default'=>$default_start, 'checkErrorField'=>$fieldName, 'error' => $error) );
		$output .= ' to ';
		$output .= $this->dateTime( $fieldName.'.end', 'm/d/Y', 'g:i a', array('class' => 'end', 'default'=>$default_end, 'checkErrorField'=>$fieldName, 'error' => $error) );
		
		return $output;
	}
	
	/**
	 * Creates a "duration" input, which is essentialy two DateTimes smooshed together.
	 * 
	 * Requires some javascript in form.js to auto-update dates.
	 * 
	 * @param string $fieldName
	 * @param array $attributes Standard input attributes. (Currently only supports 'empty')
	 * 
	 * @return string Generated duration input HTML 
	 */
	public function duration_hours($fieldName, $attributes){

		if( $attributes['empty'] ){
			$default_start = null;
			$default_hours = null;
			$empty = true;
		}else{
			$default_start = 'today';
			$default_hours = '1';
			$empty = false;
		}

		$output = $this->dateTime( $fieldName.'.duration_date', 'm/d/Y', null, array('class' => 'start', 'default'=>$default_start, 'checkErrorField'=>$fieldName, 'error' => $error) );
		$output .= ' ';
		$output .= $this->number( $fieldName.'.hours', array('style' => 'width: 6em;', 'step'=>'any', 'default'=>$default_hours, 'error' => $error) );
		$output .= ' hours';
		
		return $output;
	}
	
	/**
	 * Returns two text inputs, one for date, the other time. Each has a class of date and time respectively. The
	 * classes should be used to trigger jQuery Date and Time pickers
	 *
	 * TODO - Pass extra options to input boxes
	 * 
	 * ### Attributes:
	 *
	 * - `empty` - If true, the empty select option is shown.
	 * - `value` | `default` - The default value to be used by the input.  A value in `$this->data`
	 *   matching the field name will override this value.  If no default is provided `time()` will be used.
	 * - `default` - The default value to use in "strottime" format for setting an empty value, if attribute
	 *	 `empty` is false.
	 *
	 * @param string $fieldName Prefix name for the SELECT element
	 * @param string $dateFormat PHP date() function format used for date field. Also 'MDY' or 'YMD' for CakePHP compatability
	 * @param string $timeFormat PHP date() function format used for time field. Also '12' or '24' for CakePHP compatability
	 * @param string $attributes array of Attributes
	 * @return string Generated set of input boxes for the date and time formats chosen.
	 * @link http://book.cakephp.org/view/1418/dateTime
	 */
	public function dateTime($fieldName, $dateFormat = 'm/d/Y', $timeFormat = 'g:i a', $attributes = array()) {

		$this->Html->css('/nice_date/js/jquery/timepicker/jquery.timepicker.css', 'stylesheet', array('inline'=>false, 'once'=>true));
		$this->Html->script('/nice_date/js/jquery/timepicker/jquery.timepicker.min.js', array('inline'=>false, 'once'=>true) );

		$this->Html->script('/nice_date/js/moment/moment.min.js', array('inline'=>false, 'once'=>true) );

		$this->Html->script('/nice_date/js/nice_date.js', array('inline'=>false, 'once'=>true) );

		$attributes += array('empty' => true, 'value' => null);
		$year = $month = $day = $hour = $min = $meridian = null;

		if (empty($attributes['value'])) {
			$attributes = $this->value($attributes, $fieldName);
		}

		// If there's an error on the field, don't try to format the date. Print as is and return
		if( $this->isFieldError( $fieldName ) || $this->isFieldError( $attributes['checkErrorField']) ){
			$classes = trim('date ' . $attributes['class']);
			if($dateFormat !== null){
				$output .= $this->text($fieldName.'.date', array('value'=>$attributes['value']['date'], 'class'=>$classes));
			}

			if($dateFormat !== null && $timeFormat !== null)
				$output .= ' ';

			$classes = trim('time ' . $attributes['class']);
			if($timeFormat !== null){
				$output .= $this->text($fieldName.'.time', array('value'=>$attributes['value']['time'], 'class'=>$classes));
			}
			return $output;
		}

		// Check for missing value
		if ($attributes['value'] === null && $attributes['empty'] != true) {
			if($attributes['default']){
				$value = strtotime($attributes['default']);
			}else{
				$value = time();
			}
		}else{
			$value = $attributes['value'];
		}
		
		// Convert timestamp to something strtotime can read
		if( is_numeric($value) && $value > 1000000000)
			$value = '@'.$value;
		
		// If value is array (the format submitted by forms), convert to strtotime readable string
		if( is_array($value) && isset($value['date']) && $value['date'])
			$value = '@'.strtotime($value['date'] . (isset($value['time']) ? ' ' . $value['time'] : ''));
		
		// If value is array (the format submitted by forms), convert to strtotime readable string
		if( is_array($value) && !isset($value['date']) && isset($value['time']) && $value['time'])
			$value = '@'.strtotime('2000-01-01 ' . (isset($value['time']) ? ' ' . $value['time'] : ''));


		// If value is empty, or if the date or time keys are empty
		if(
			$value == ''
			|| (
				is_array($value)
				&& (
					isset($value['date']) && $value['date'] == ''
					|| isset($value['time']) && $value['time'] == ''
				)
			)
		){
			$value = null;
		}

		$output = '';

		unset($attributes['value']);
		unset($attributes['empty']);
		
		$classes = trim('date ' . $attributes['class']);
		if($dateFormat !== null){
			
			// Create date field
			if($dateFormat == 'MDY'){
				$dateFormat = 'n/j/Y';
			}

			if($dateFormat == 'YMD'){
				$dateFormat = 'Y-m-d';
			}

			if($value === null){
				$output .= $this->text($fieldName.'.date', array('value'=>'', 'class'=>$classes));
			}else{
				if( strtotime($value) !== false ){
					$dateFormatted = date( $dateFormat, strtotime($value) );
				}else{
					$dateFormatted = $value;
				}
				$output .= $this->text($fieldName.'.date', array('value'=>$dateFormatted, 'class'=>$classes));
			}

		}
		
		if($dateFormat !== null && $timeFormat !== null)
			$output .= ' ';
		
		$classes = trim('time ' . $attributes['class']);
		if($timeFormat !== null){
		
			// Create time field
			if($timeFormat == '24'){
				$timeFormat = 'H:i';
			}

			if($timeFormat == '12'){
				$timeFormat = 'g:i a';
			}

			if($dateFormat !== null){
				$timeFormatted = date( $timeFormat, strtotime($value) );
			}else{
				$timeFormatted = date( $timeFormat, strtotime('2000-01-01 '.$value) );
			}

			if($value === null){
				$output .= $this->text($fieldName.'.time', array('value'=>'', 'class'=>$classes));
			}else{
				$output .= $this->text($fieldName.'.time', array('value'=>$timeFormatted, 'class'=>$classes));
			}
		}

		return $output;
	}

}