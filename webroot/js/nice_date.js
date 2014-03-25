/* 
 * JS lib to use jQuery date picker and time picker for date and time fields
 */

$(function(){
	// Setup date and time pickers
	$('input.date').datepicker({ dateFormat: 'm/d/yy' });

	if($('input.time').timepicker)
		$('input.time').timepicker({ step: 30 });

	// Setup duration types. A change to the start time changes the end time to match.
	$('.duration').each(function(id, div){
		$( 'input.date.start', div ).datepicker("option", "onClose", function( selectedDate ) {
			$( 'input.date.end', div ).datepicker( "option", "minDate", selectedDate );
		});

		$( 'input.date.end', div ).datepicker("option", "onClose", function( selectedDate ) {
			$( 'input.date.start', div ).datepicker( "option", "maxDate", selectedDate );
		});

		$('input.time.start', div).change(function(){

			// Use moment.js to convert time
			var new_time = moment(this.value, 'h:mma');

			// Add an hour to new time
			new_time.add('hours', 1);

			$('input.time.end', div)[0].value = new_time.format('h:mma');
		});
	});
});
