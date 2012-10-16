<?php

class NiceDateBehavior extends ModelBehavior {

	/**
	 * Validation rule, overwriting CakePHP's date validation
	 */
	public function datetime( Model $model, $check, $format = 'ymd', $regex = null ) {
		// CakePHP throws a junk array into the last specified paramter of validation functions. apparently this is intentional:
		// http://cakephp.lighthouseproject.com/projects/42648/tickets/149-custom-validation-parameters#ticket-149-6
		// Need to check for this junk and get rid of it.
		if( is_array($format) && isset($format['rule']) ){
			$format = 'ymd';
		}
		if( is_array($regex) && isset($regex['rule']) ){
			$regex = null;
		}

		// $check array is passed using the form field name as the key
		// have to extract the value to make the function generic
		list($key, $check) = each($check);

		if( !is_array( $check ) ){
			throw new CakeException('Invalid date format for Validation');
		}

		$value = Utils::datetimeArrToString($check);

		if( isset($check['date']) && isset( $check['time'] ) ){

			if($check['date'] == '' && $check['time'] == '')
				return true;

			return Validation::datetime( $value, $format, $regex );

		}
		if( isset($check['date']) && !isset( $check['time'] ) ){

			if($check['date'] == '')
				return true;

			return Validation::date( $value, $format, $regex );

		}
		if( !isset($check['date']) && isset( $check['time'] ) ){

			if($check['time'] == '')
				return true;

			return Validation::time( $value );

		}
	}

	/**
	 * Validation rule, overwriting CakePHP's date validation
	 */
	public function date( Model $model, $check, $format = 'ymd', $regex = null ) {

		return $this->datetime($model, $check, $format, $regex);

	}

	/**
	 * Validation rule, overwriting CakePHP's time validation
	 */
	public function time( Model $model, $check ) {

		return $this->datetime( $model, $check );

	}

	/**
	 * Validation rule for the custom form element type "duration". Each date must be checked separate (to show different error messages)
	 */
	public function duration(Model $model, $check, $which_date){

		// $check array is passed using the form field name as the key
		// have to extract the value to make the function generic
		list($key, $check) = each($check);

		if( !isset($check[$which_date]) ){
			throw new CakeException('Invalid input format for duration type');
		}

		return $this->datetime( $model, array('a' => $check[$which_date]), 'mdy' );

	}


	/**
	 * Validation rule, overwriting CakePHP's date validation
	 */
	public function nicenotempty(Model $model, $check){

		// $check array is passed using the form field name as the key
		// have to extract the value to make the function generic
		list($key, $check) = each($check);

		// Check for date array
		if(is_array($check) && isset($check['date'])){
			$check = $check['date'] . ( isset($check['time']) ? ' ' . $check['time'] : '');
		}

		// Check for time array
		if(is_array($check) && !isset($check['date']) && isset($check['time'])){
			$check = $check['time'];
		}

		// Check for duration array
		if(is_array($check) && isset($check['start'])){

			$result1 = Validation::notEmpty( $check['start']['date'] );
			$result2 = Validation::notEmpty( $check['start']['time'] );
			$result3 = Validation::notEmpty( $check['end']['date'] );
			$result4 = Validation::notEmpty( $check['end']['time'] );

			return $result1 && $result2 && $result3 && $result4;

		}

		// Check for any other array
		if(is_array($check) && count($check) > 0){
			$check = '1';
		}

		$check = Utils::datetimeArrToString($check);

		return Validation::notEmpty( $check );
	}

}