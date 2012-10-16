/* 
 * JS lib to use jQuery date picker and time picker for date and time fields
 */

$(function(){
	// Setup date and time pickers
	$('input.date').datepicker({ dateFormat: 'm/d/yy' });
	$('input.time').timePicker({ show24Hours: false, step: 30 });

	// Setup duration types. A change to the start time changes the end time to match.
	$('div.input.duration').each(function(id, div){
		$('input.date.start', div).change(function(){
			$('input.date.end', div)[0].value = this.value;
		});
		$('input.time.start', div).change(function(){
			// Add an hour to time.
			var time_split = this.value.split(/:| /);

			// Convert 12 hour time to military
			if(time_split[2] == 'am' && time_split[0] == '12')
				time_split[0] = 0;

			if(time_split[2] == 'am' && time_split[0] != '12')
				time_split[0] = time_split[0];

			if(time_split[2] == 'pm' && time_split[0] == '12')
				time_split[0] = 12;

			if(time_split[2] == 'pm' && time_split[0] != '12')
				time_split[0] = parseInt(time_split[0]) + 12;

			var new_date = new Date('2000', '01', '01', parseInt(time_split[0]) + 1, time_split[1], 00);

			// Convert military time to 12 hour time
			var hour = new_date.getHours();
			var minutes = ('0' + new_date.getMinutes()).slice(-2); // Pad the minutes string
			var new_date_str = '';
			if(hour == 0){
				new_date_str = '12:'+minutes+' am';
			}
			if(hour > 0 && hour < 12){
				new_date_str = hour+':'+minutes+' am';
			}
			if(hour == 12){
				new_date_str = '12:'+minutes+' pm';
			}
			if(hour > 12){
				new_date_str = (hour-12)+':'+minutes+' pm';
			}

			$('input.time.end', div)[0].value = new_date_str;
		});
	});
});
