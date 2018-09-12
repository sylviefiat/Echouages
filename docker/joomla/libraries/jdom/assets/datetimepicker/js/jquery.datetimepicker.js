/*
 *  Project: datetimepickerByGiro jQuery plugin
 *  Description: a powerfull datetimepicker fully featured
 *  Author: Girolamo Tomaselli http://bygiro.com
 *  License:	MIT
 */

// extends the Date prototype
;(function (d) {
	d.getMaxDays = function(){
		var tmpDate	= new Date(this.toString()),
			d		= 28,
			m		= tmpDate.getMonth();
		while (tmpDate.getMonth() == m) {
			++d;
			tmpDate.setDate(d);
		}
		return d - 1;
	};
	d.addDays		= function (n) {
		this.setDate(this.getDate() + n);
	};
	d.addHours		= function (n) {
		this.setHours(this.getHours() + n);
	};
	d.addMins		= function (n) {
		this.setMinutes(this.getMinutes() + n);
	};
	d.addMonths	= function (n){
		var day	= this.getDate();
		this.setDate(1);
		this.setMonth(this.getMonth() + n);
		this.setDate(Math.min(day, this.getMaxDays()));
	};
	d.addYears	= function (n) {
		var day	= this.getDate();
		this.setDate(1);
		this.setFullYear(this.getFullYear() + n);
		this.setDate(Math.min(day, this.getMaxDays()));
	};
	d.setZeroSec = function (n) {
		this.setSeconds(0);
		this.setMilliseconds(0);
	};
	d.getDayOfYear	= function() {
		var now		= new Date(this.getFullYear(), this.getMonth(), this.getDate(), 0, 0, 0);
		var then	= new Date(this.getFullYear(), 0, 0, 0, 0, 0);
		var time	= now - then;
		return Math.floor(time / 24*60*60*1000);
	};
})(Date.prototype);

// the semi-colon before function invocation is a safety net against concatenated
// scripts and/or other plugins which may not be closed properly.
;(function ( $, window, document, undefined ) {
	"use strict";
	
    var pluginName = "datetimepickerByGiro",
    // the name of using in .data()
	dataPlugin = "plugin_" + pluginName,
	defaults = {
		style				: 'popup', // / inline / popup
		autoclose		: false, // autoclose the popup on date / date range selected
		uiTarget			: null, // the UI target to contain the datetime value
		firstDay			: 1, // 0 - 6:  sunday -> saturday   // default 1 (Monday)
		minView			: 'times', // times / days / months / years
		maxView			: 'years', // times / days / months / years
		startView		: 'days', // times / days / months / years
		min				: null, // starting date to allow selection
		max				: null, // ending date to allow selection
		today				: 'now', // if string is provided it will follow the option "format" provided (except for the string "now") / new Date() object
		showDaysNotInMonth : false,
		showDisabledTimes : false,
		calendars		: 1,
		format			: 'Y-m-d', // PHP standard format
		uiFormat			: 'Y-m-d', // PHP standard format. if provided different from the option "format", the original input will be hidden, and the date selected will be shown in this format
		separator		: ' | ', // this will be the separator for multiple dates or date range
		mode				: 'single', // single / multiple / range
		timeFormat		: 24, // 12 / 24
		minuteStep		: 5,
	/*	specialDates	:[ // it could be a simple array of numbers/strings 0 - 6: sunday -> saturday OR a more complex array of objects with more parameters
			0, // 0 - 6: sunday -> saturday
			'Wednesday', //you can also use the day name BUT it must be the same word (case sensitive) as specified in the language text options
			{ // you can use an object with more details
				selectable: true, // true or false, to enable or disable the user selection on this date, if null it's selectable
				date: new Date(2013, 11, 25), // string OR day name OR weekday number (0 - 6) OR Date() object
				repeatYear: true,
				repeatMonth: true,
				repeatWeek: true, // if repeatWeek is selected, repeatMonth and repeatYear are automatically enabled
				selectableTimes: [ // if null, the full day is ENABLED or DISABLED based on the "selectable" value
					// if it's not empty, the time external to the timeslots will be considere the opposite of the "selectable" value
					'10:30 - 18:45', // time ranges must be separated by "-"
					'8:10am - 8:20pm', // you can use 12 hours format using AM / PM
					'12:30 - 11:20pm' // if AM / PM is not found, the 24 hours format will be used, so in this case the first time (12:30) is in 24 hours format and the second (11:20pm) in 12 hours format.
				],
				attributes:{
					class:'special'
				},
				message:'My Birthday!!!!'
			}
		],	
	*/
		specialDates: [], // see above
		text : {
			select_a_date: 'Select a date',
			select_a_date_range: 'Select a date range',
			close: 'Close',
			closeBtn: '&times;',
			prev : '&#9664;',
			next : '&#9654;',
			ok: 'Ok',
			days		: ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'],
			daysShort	: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
			daysMin	: ['Mo','Tu','We','Th','Fr','Sa','Su'],
			months	: ['January','February','March','April','May','June','July','August','September','October','November','December'],
			monthsShort	: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']
		},
		
		// callbacks
		onDateRender: function(){return {};},
		onDateChange: function(){return true;},
		onInit: false
	};

	var trigger = function(event, callback, data) {
		
		var evt = $.Event(event);

		if(!(data instanceof Array)){
			data = [data];
		}
		data.unshift(this);
		$(document).triggerHandler(evt, data);
		this.options.$element.triggerHandler(evt, data);
		
		if ($.isFunction(callback)) {
			callback.call(this, data);
		}
	},

	addHTML = function(){
		var that = this,
			config = this.options,
			html = '',tmpEle = config.$element;

		if(tmpEle.parent().hasClass('input-append') || tmpEle.parent().hasClass('input-prepend')){
			tmpEle = config.$element.parent();
		}
		
		this.options.$parent = tmpEle.wrap('<div class="dtp-container"></div>').parent();
		
		if(config.format != config.uiFormat){
		
			if(config.mode == 'multiple'){
				// create a values container
				tmpEle.hide();
				var uiTarget = getUiTarget.call(this);
				if(!uiTarget.length || uiTarget.is('input')){
					this.options.$parent.append('<div class="dtp-values"></div>');
					var mCont = this.options.$parent.find('.dtp-values');
					if(uiTarget instanceof jQuery){
						uiTarget.add(mCont);
					} else {
						uiTarget = mCont;
					}
				}
			}
			
			updateUi.call(this);
		}
		
		// add the calendar		
		html = '<div class="dtp-calendars"></div>';

		this.options.$parent.append(html);
		
		if(config.style == 'popup'){
			this.options.$parent.find('.dtp-calendars').addClass('popup_style');
		}		
	},
	
	addCalendars = function(type,date){
		var that = this,
			config = this.options,
			type = getViewType.call(this,type),
			date = (date instanceof Date) ? date : (date ? strToDate.call(this,date,config.format) : new Date(config.today.valueOf())),
			html='';

		var fakeDate = new Date(date.valueOf()),step = (type == 'years') ? 12 : 1,
			startingStep = Math.floor(config.calendars / 2);

		fakeDate[config.methods[type]](-(step*startingStep));
		
		for(var c=0;c<config.calendars;c++){
			html += buildCalendar.call(this,type,fakeDate);
			
			fakeDate[config.methods[type]](step);
		}
		
		this.options.$parent.find('.dtp-calendars').html(html);
	},
	
	getUiTarget = function(){
		var uiTarget = false;
		if(this.options.uiTarget){
			uiTarget = this.options.$element.parent().parent().find(this.options.uiTarget);
		}
		
		return uiTarget;
	},
	
	buildHeader = function(title){
		var that = this,
			config = this.options;
			
		return '<div class="dtp-nav">'
			+'<div class="dtp-btn-prev">'+ config.text.prev +'</div>'
			+'<div class="dtp-btn-viewtype">'+ title +'</div>'
			+'<div class="dtp-btn-next">'+ config.text.next +'</div>'
			+'</div>';	
	},
		
	getViewType = function(type){
		var config = this.options,
			currentView = (config.$parent) ? config.$parent.find('[data-view-type]').attr('data-view-type') : '',
			type=/years|months|days|times/i.test(type) ? type : (currentView || config.startView);
			
		return type;
	},
	
	addZero = function(currVal){
		if(currVal.toString().length == 1){
			currVal = '0'+ currVal.toString();
		}
		
		return currVal;
	},
	
	/*
		type: 'years','months','days','times'
		date: a date Object / string
	*/
	buildCalendar = function(type,date){
		var that = this,
			config = this.options,
			date = (date instanceof Date) ? date : (date ? strToDate.call(this,date,config.format) : config.today),
			itemsXrow,it,hour,className,today=new Date(),dow,startingDay,header,title,currentYear,html,content = '',items = [];
			date = new Date(date.valueOf());
			
		switch(type){
			case 'years':
				currentYear = date.getFullYear();
				title = (currentYear -6) +' - '+ (currentYear +5);
				itemsXrow = 4;
				date.addYears(-6);
				var yearLimit = date.getFullYear() + 12;
				date.setDate(1);
				date.setMonth(1);
				date.setHours(0,0,0,0);
				for(var k=date.getFullYear();k<yearLimit;k++){
					it = {
						text		: k,
						attributes	: {
							'data-value': date.valueOf(),
							'class'		: ''
						}
					};
					items.push(it);
					date.addYears(1);
				}
				break;
				
			case 'months':
				title = dateToStr.call(this,date,'Y');
				itemsXrow = 4;
				var months = config.text.monthsShort;
				date.setDate(1);
				date.setHours(0,0,0,0);
				
				for(var k=0;k<months.length;k++){
					date.setMonth(k);
					it = {
						text		: months[k],
						attributes	: {
							'data-value': date.valueOf(),
							'class'		: ''
						}
					};
					items.push(it);
				}
				break;
				
			case 'times':
				title = dateToStr.call(this,date,'D d F, Y');
				itemsXrow = 1;
				today.setZeroSec();
				today = today.valueOf();
				
				date.setHours(0,0,0,0);

				// build hours select
				var dateValueOf = date.valueOf(),
					limitBetweenSteps = Math.round(config.minuteStep / 2) * 60000;
				
				for(var h=0;h<24;h++){
					var hh = h,extra = '',baseHour = dateValueOf;
					
					if(config.timeFormat == 12){
						if(h >= 0 && h <= 12){
							extra = ' am';
						} else {
							extra = ' pm';
							hh = h-12;
						}
					}
					
					hour = addZero(hh);
					baseHour = dateValueOf + (h * 60 * 60000);
					
					
					for(var k=0;k<60;k+=config.minuteStep){
						var val	= baseHour + (k * 60000);
						className	= [];
						it = {
							text		: hour +':'+ addZero(k) + extra,
							attributes	: {
								'data-value': val,
								'class'		: ''
							}
						};
						
						if(Math.abs(today - val) < limitBetweenSteps){
							className.push('dtp-today-time');
						}

						it = checkDate.call(this,it,type);
						it.attributes['class'] += ' '+((className instanceof Array) ? className.join(' ') : className);
						
						if(it.disabled && !config.showDisabledTimes){
							continue;
						}
						items.push(it);
					}
				}
				break;
				
			case 'days':
			default:
				title = dateToStr.call(this,date,'F, Y');
				
				// days of week
				dow=[],startingDay = config.firstDay;
				for(var i=startingDay;i<(startingDay+7);i++){
					dow.push('<div class="dtp-dow">'+ config.text.daysMin[(i-1) % 7] +'</div>');
				}					
				content += '<div class="dtp-row dow">'+ dow.join('') +'</div>';				
				
				itemsXrow = 7;
				date.setDate(1);
				today.setHours(0,0,0,0);
				today = today.valueOf();
				var month	= date.getMonth(),
					day	= (date.getDay() - config.firstDay) % 7,
					count = 0;
				date.addDays(-(day + (day < 0 ? 7 : 0)));
				
				while (count < 42) {
					var notInMonth = month != date.getMonth(),val	= date.valueOf();
					className	= [];
					it	= {
						text		: date.getDate(),
						attributes	: {
							'data-value': val,
							'class'		: '',
						}
					};
					if (notInMonth) {
						className.push('dtp-outside-month');
						if(!config.showDaysNotInMonth){
							it.disabled = true;
							it.text = '';
						}
					}
					if (date.getDay() == 0) {
						className.push('dtp-sunday');
					} else if (date.getDay() == 6) {
						className.push('dtp-saturday');
					}
					
					if (val == today && (!notInMonth || (notInMonth && config.showDaysNotInMonth))) {
						className.push('dtp-today');
					}
		
					it = checkDate.call(this,it,type);					
					it.attributes['class'] += ' '+ ((className instanceof Array) ? className.join(' ') : className);
					
					items.push(it);
					date.addDays(1);
					count++;
				}			
				
				break;
		}
		
		if(items.length > 0){
			content += '<div class="dtp-row">';
			for(var k=0;k<items.length;k++){
				var attributes = '';
				
				for(var i in items[k].attributes){
					if(i == 'class'){
						items[k].attributes[i] += ' dtp-item '+ type;
					}				
					attributes += ' '+ i +'="'+ items[k].attributes[i] +'"';
				}
				content +=('<div'+ attributes +'>'+ items[k].text +'</div>');
				
				if(((k+1) % itemsXrow) == 0 && (k+1 < items.length)){
					content += '</div><div class="dtp-row">';
				}
			}
			content += '</div>';
		}
		
		header = buildHeader.call(this,title);		
		html = '<div data-view-type="'+ type +'" class="dtp-calendar">'
			+ header
			+ '<div class="dtp-content">'+ content +'</div>'
			+'</div>';

		return html;
	},
	
	compareDates = function(date1,date2,props){
		var result,
			date1 = date1 instanceof Date ? date1 : new Date(date1),
			date2 = date2 instanceof Date ? date2 : new Date(date2),
			props = props ? props : ['y','m','d','h','i'],
			methods = {
				y: ['FullYear',1970],
				m: ['Month',0],
				d: ['Date',1],
				h: ['Hours',0],
				i: ['Minutes',0]
			};

		result = true;
		for(var i in methods){
			if(props.indexOf(i) < 0){
				continue;
			}
			var val1 = date1['get'+ methods[i][0]](),
				val2 = date2['get'+ methods[i][0]]();

			if(val1 > val2) {
				result = 1;
				break;
			} else if(val1 < val2){
				result = false;
				break;				
			}
		}
		
		return result;
	},
	
	matchingDates = function(it,type,datesToCheck,current){
		var that = this,
			config = this.options,
			minView = config.views.indexOf(config.minView),
			matched=null;

		for(var d=0;d<datesToCheck.length;d++){
			var compareResult,props = [],dateDay = datesToCheck[d],
			selectable = (typeof dateDay.selectable != 'undefined' && !dateDay.selectable) ? false: true;
			
			// properties to check
			if(!dateDay.repeatYear){
				props.push('y');
			}

			if(!dateDay.repeatMonth){
				props.push('m');
			}
			
			if(!dateDay.repeatWeek){
				props.push('d');
			} else {
				props = [];
			}
			
			if(props.length > 0){
				compareResult = compareDates.call(this,dateDay.date,current,props);
			} else {
				compareResult = dateDay.date.getDay() == current.getDay();
			}
			
			if(compareResult === true){
				if(dateDay.attributes && dateDay.attributes['class']){
					it.attributes['class'] += dateDay.attributes['class'];
					delete dateDay.attributes['class'];
				}
				$.extend(it.attributes,dateDay.attributes);
				it.message = dateDay.message;
			
				if(type == 'days'){
					if(!dateDay.selectableTimes || dateDay.selectableTimes.length == 0){
						matched = selectable;
					}
					break;
				}
				
				if(!dateDay.selectableTimes || dateDay.selectableTimes.length == 0){
					matched = selectable;
					break;						
				} else {
					matched = !selectable;
					for(var t=0;t<dateDay.selectableTimes.length;t++){
						var tslot = dateDay.selectableTimes[t],
							t1 = compareDates.call(this,current,tslot[0],['h','i']),
							t2 = compareDates.call(this,tslot[1],current,['h','i']);
						if(t1 && t2){
							matched = selectable;
							break;
						}
					}
				}
			}
			
			if(matched !== null){
				break;
			}
		}

		return matched;
	},
	
	checkDate = function(it,type){
		var that = this,
			config = this.options,
			className = [],
			val = parseInt(it.attributes['data-value']),
			date = new Date(val),
			custom = (typeof config.onDateRender == 'function') ? config.onDateRender(date,it) : {},
			disabled;

		if(config.views.indexOf(config.minView) > 0 || type == 'days'){
			// we don't need to compare TIME, day is enough
			disabled = (config.min && compareDates.call(this,config.min,date,['y','m','d']) === 1) || (config.max && compareDates.call(this,date,config.max,['y','m','d']) === 1);
		} else {
			// FULL compare, TIME is important here
			disabled = (config.min && config.min > date) || (config.max && config.max < date);
		}
		
		if((type == 'days' || type == 'times') && !disabled && !custom.disabled){			
			if(config.specialDates.length > 0){
				disabled = matchingDates.call(this,it,type,config.specialDates,date);
			}
			
			if(disabled !== null) disabled = !disabled;
		}

		if (it.disabled || custom.disabled || (disabled !== null && disabled) ) {
			className.push('dtp-disabled');
			it.disabled = true;
		}

		if (
			!it.disabled &&
			(
				custom.selected ||
				config.dateValue == val ||
				$.inArray(val, config.dateValue) > -1 ||
				(
					config.mode == 'range' && val >= config.dateValue[0] && val <= config.dateValue[1]
				)
			)
		) {
			className.push('dtp-selected');
		}
		
		if(custom.attributes){
			if(custom.attributes['class']){
				className.push(custom.attributes['class']);
			}
			delete custom['class'];
			delete custom['data-value'];
		
			$.extend(it.attributes,custom.attributes);
		}				
		
		it.attributes['class'] += ' '+ ((className instanceof Array) ? className.join(' ') : className);
		if(it.message){
			it.attributes['data-dtp-message'] = it.message;
		}
		return it;
	},
	
	createFakeDate = function(weekDay){
		var selDate = {
			repeatWeek: true,
			date: new Date()
		}
		selDate.date.addDays(weekDay - selDate.date.getDay());
		
		return selDate;
	},
	
	cleanTslots = function(slots){
		var cleanTimes = [];
		for(var t=0;t<slots.length;t++){
			var str,tdate,tformat,tslot=[],tim = slots[t].toLowerCase(),
				tparts = tim.split('-');
			if(tparts.length != 2){
				continue;
			}
			
			for(var tp=0;tp<2;tp++){
				tformat = 'Y m d H:i';
				// check Ante meridiem - Post meridiem
				if(tparts[tp].indexOf('am') >= 0 || tparts[tp].indexOf('pm') >= 0){
					tformat = 'Y m d h:i a';
				}
				
				str = '1970 1 1 '+ tparts[tp].trim().replace('pm',' pm').replace('am',' am');								
				tdate = strToDate.call(this,str,tformat);
				if (tdate.toString() != 'Invalid Date') {
					tdate.setZeroSec();
					tslot.push(tdate);
				}
			}
			
			if(tslot.length == 2){
				cleanTimes.push(tslot);
			}
		}
		
		return cleanTimes;
	},
	
	strToDate = function(date, format) {
		var that = this,
			config = this.options,
			format = (typeof format != 'undefined' && format != '') ? format : config.format;
		if(!date){
			return new Date;		
		} else if(date instanceof Date) {
			return date;
		} else if (date.indexOf('now') >= 0) {
			var method,pa,paClean,dateSTR = date.replace('now',''),
			separator	= new RegExp('(.*?(mins|min|minutes|minute|hours|hour|days|day|months|month|years|year))','gi'),
			parts		= dateSTR.match(separator),
			date = new Date();		
			if(!(parts instanceof Array)){
				return date;
			}
			
			for(var i=0;i<parts.length;i++){
				pa = parts[i];
				
				if(pa.indexOf('mins') >= 0 || pa.indexOf('min') >= 0 || pa.indexOf('minutes') >= 0 || pa.indexOf('minute') >= 0){
					method = 'addMins';
				} else if(pa.indexOf('hours') >= 0 || pa.indexOf('hour') >= 0){
					method = 'addHours';
				} else if(pa.indexOf('days') >= 0 || pa.indexOf('day') >= 0){
					method = 'addDays';
				} else if(pa.indexOf('months') >= 0 || pa.indexOf('month') >= 0){
					method = 'addMonths';
				} else if(pa.indexOf('years') >= 0 || pa.indexOf('year') >= 0){
					method = 'addYears';
				}
				
				paClean = parseInt(pa.replace(/[^0-9+-]/g, ""));
				if(!isNaN(parseFloat(paClean)) && isFinite(paClean)){
					date[method](paClean);
				}
			}

			return date;
		}
			
		var splitted_date	= date.split(config.separator);
		if (splitted_date.length > 1) {
			splitted_date.forEach(function (element, index, array) {
				array[index]	= strToDate.call(that,$.trim(element), format);
			});
			return splitted_date;
		}
		
		var months_text	= config.text.monthsShort.join(')(') + ')(' + config.text.months.join(')('),
			separator	= new RegExp('[^0-9a-zA-Z(' + months_text + ')]+'),
			parts		= date.split(separator),
			against		= format.split(separator),
			d,m,y,h,min,sec,mil,timestamp,parsed_date,
			now = new Date();

		for (var i = 0; i < parts.length; i++) {
				/*
		OK		d      Day of the month, 2 digits with leading zeros                                "01" to "31"
				D      A textual representation of a day, three letters                             "Mon" through "Sun"
		OK		j      Day of the month without leading zeros                                       "1" to "31"
				l      A full textual representation of the day of the week (lowercase "L")         "Sunday" through "Saturday"
				N      ISO-8601 numeric representation of the day of the week (added in PHP 5.1.0)  "1" (for Monday) through "7" (for Sunday)
				S      English ordinal suffix for the day of the month, 2 characters                "st", "nd", "rd" or "th". Works well with j
				w      Numeric representation of the day of the week                                "0" (for Sunday) through "6" (for Saturday)
				z      The day of the year (starting from "0")                                      "0" through "365"      
				W      ISO-8601 week number of year, weeks starting on Monday                       "00" to ("52" or "53")
		OK		F      A full textual representation of a month, such as January or March           "January" through "December"
		OK		m      Numeric representation of a month, with leading zeros                        "01" through "12"
		OK		M      A short textual representation of a month, three letters                     "Jan" through "Dec"
		OK		n      Numeric representation of a month, without leading zeros                     "1" through "12"
				t      Number of days in the given month                                            "28" through "31"
				L      Whether it's a leap year                                                     "1" if it is a leap year, "0" otherwise
				o      ISO-8601 year number. This has the same value as Y, except that if the       "2008"
					ISO week number (W) belongs to the previous or next year, that year 
					is used instead.
		OK		Y      A full numeric representation of a year, 4 digits                            "2008"
		OK		y      A two digit representation of a year                                         "08"
		OK		a      Lowercase Ante meridiem and Post meridiem                                    "am" or "pm"
		OK		A      Uppercase Ante meridiem and Post meridiem                                    "AM" or "PM"
				B      Swatch Internet time                                                         "000" through "999"
		OK		g      12-hour format of an hour without leading zeros                              "1" through "12"
		OK		G      24-hour format of an hour without leading zeros                              "0" through "23"
		Ok		h      12-hour format of an hour with leading zeros                                 "01" through "12"
		OK		H      24-hour format of an hour with leading zeros                                 "00" through "23"
		OK		i      Minutes with leading zeros                                                   "00" to "59"
		OK		s      Seconds, with leading zeros                                                  "00" through "59"
		OK		u      Milliseconds                                                                 "54321"
				e      Timezone identifier                                                          "UTC", "EST", "PST"
				I      Whether or not the date is in daylight saving time (uppercase i)             "1" if Daylight Saving Time, "0" otherwise
				O      Difference to Greenwich time (GMT) in hours                                  "+0200", "-0600"
				P      Difference to Greenwich time (GMT) with colon between hours and minutes      "+02:00", "-06:00"
				T      Timezone abbreviation                                                        "UTC", "EST", "PST"
				Z      Timezone offset in seconds. The offset for timezones west of UTC is          "-43200" through "50400"
					always negative, and for those east of UTC is always positive.
				c      ISO 8601 date                                                                "2004-02-12T15:19:21+00:00"
				r      RFC 2822 formatted date                                                      "Thu, 21 Dec 2000 16:01:07 +0200"
		OK		U      Seconds since the Unix Epoch (January 1 1970 00:00:00 GMT)                   "0"
				*/
			switch (against[i]) {
				case 'M':
					m = config.text.monthsShort.indexOf(parts[i]);
				break;
				case 'F':
					m = config.text.months.indexOf(parts[i]);
				break;
				case 'd':
				case 'j':
					d = parseInt(parts[i],10);
				break;
				case 'm':
				case 'n':
					m = parseInt(parts[i], 10)-1;
				break;
				case 'Y':
				case 'y':
					y = parseInt(parts[i], 10);
					y += y > 100 ? 0 : (y < 29 ? 2000 : 1900);
				break;
				case 'i':
					min = parseInt(parts[i], 10);
				break;
				case 's':
					sec = parseInt(parts[i], 10);
				break;
				case 'u':
					mil = parseInt(parts[i], 10);
				break;
				case 'U':
					timestamp = parseInt(parts[i], 10);
				break;
				case 'H':
				case 'G':
				case 'h':
				case 'g':
					h = parseInt(parts[i], 10);						
				break;
				case 'A':
				case 'a':
					if (/pm/i.test(parts[i]) && h < 12) {
						h += 12;
					} else if (/am/i.test(parts[i]) && h >= 12) {
						h -= 12;
					}
				break;
			}
		}
		
		if(typeof timestamp != 'undefined'){
			parsed_date = new Date((timestamp * 1000));
		} else {
			parsed_date = new Date(
				y === undefined ? now.getFullYear() : y,
				m === undefined ? now.getMonth() : m,
				d === undefined ? now.getDate() : d,
				h === undefined ? now.getHours() : h,
				min === undefined ? now.getMinutes() : min,
				sec === undefined ? now.getSeconds() : sec,
				mil === undefined ? now.getMilliseconds() : mil
			);
		}
		if (parsed_date.toString() == 'Invalid Date') {
			parsed_date = new Date;
		}
		return parsed_date;
	},
	
	dateToStr = function(date, format){
		var that = this,
			config = this.options,
			format = (typeof format != 'undefined' && format != '') ? format : config.format,
			date = (date instanceof Date) ? date : new Date(),
			m = date.getMonth(),
			d = date.getDate(),
			y = date.getFullYear(),
			w = date.getDay(),
			s = {},
			hr = date.getHours(),
			pm = (hr >= 12),
			ir = (((pm) ? (hr - 12) : hr) == 0) ? 12 : ir,
			dy = date.getDayOfYear(),
			min = date.getMinutes(),
			sec = date.getSeconds(),
			parts = format.split(''), part;
			
		for (var i = 0; i < parts.length; i++) {
			part = parts[i];
			
				/*
		OK		d      Day of the month, 2 digits with leading zeros                                "01" to "31"
		OK		D      A textual representation of a day, three letters                             "Mon" through "Sun"
		OK		j      Day of the month without leading zeros                                       "1" to "31"
		OK		l      A full textual representation of the day of the week (lowercase "L")         "Sunday" through "Saturday"
		OK		N      ISO-8601 numeric representation of the day of the week (added in PHP 5.1.0)  "1" (for Monday) through "7" (for Sunday)
				S      English ordinal suffix for the day of the month, 2 characters                "st", "nd", "rd" or "th". Works well with j
		OK		w      Numeric representation of the day of the week                                "0" (for Sunday) through "6" (for Saturday)
		OK		z      The day of the year (starting from "0")                                      "0" through "365"      
				W      ISO-8601 week number of year, weeks starting on Monday                       "00" to ("52" or "53")
		OK		F      A full textual representation of a month, such as January or March           "January" through "December"
		OK		m      Numeric representation of a month, with leading zeros                        "01" through "12"
		OK		M      A short textual representation of a month, three letters                     "Jan" through "Dec"
		OK		n      Numeric representation of a month, without leading zeros                     "1" through "12"
				t      Number of days in the given month                                            "28" through "31"
				L      Whether it's a leap year                                                     "1" if it is a leap year, "0" otherwise
				o      ISO-8601 year number. This has the same value as Y, except that if the       "2008"
					ISO week number (W) belongs to the previous or next year, that year 
					is used instead.
		OK		Y      A full numeric representation of a year, 4 digits                            "2008"
		OK		y      A two digit representation of a year                                         "08"
		OK		a      Lowercase Ante meridiem and Post meridiem                                    "am" or "pm"
		OK		A      Uppercase Ante meridiem and Post meridiem                                    "AM" or "PM"
				B      Swatch Internet time                                                         "000" through "999"
		OK		g      12-hour format of an hour without leading zeros                              "1" through "12"
		OK		G      24-hour format of an hour without leading zeros                              "0" through "23"
		OK		h      12-hour format of an hour with leading zeros                                 "01" through "12"
		OK		H      24-hour format of an hour with leading zeros                                 "00" through "23"
		OK		i      Minutes with leading zeros                                                   "00" to "59"
		OK		s      Seconds, with leading zeros                                                  "00" through "59"
		OK		u      Milliseconds                                                                 "54321"
				e      Timezone identifier                                                          "UTC", "EST", "PST"
				I      Whether or not the date is in daylight saving time (uppercase i)             "1" if Daylight Saving Time, "0" otherwise
				O      Difference to Greenwich time (GMT) in hours                                  "+0200", "-0600"
				P      Difference to Greenwich time (GMT) with colon between hours and minutes      "+02:00", "-06:00"
				T      Timezone abbreviation                                                        "UTC", "EST", "PST"
				Z      Timezone offset in seconds. The offset for timezones west of UTC is          "-43200" through "50400"
					always negative, and for those east of UTC is always positive.
				c      ISO 8601 date                                                                "2004-02-12T15:19:21+00:00"
				r      RFC 2822 formatted date                                                      "Thu, 21 Dec 2000 16:01:07 +0200"
		OK		U      Seconds since the Unix Epoch (January 1 1970 00:00:00 GMT)                   "0"
				*/			
			
			switch (part) {
				case 'D':
					part = config.text.daysShort[w];
				break;
				case 'l':
					part = config.text.days[w];
				break;
				case 'M':
					part = config.text.monthsShort[m];
				break;
				case 'F':
					part = config.text.months[m];
				break;
				case 'y':
					part = 1 + Math.floor(y / 100);
				break;
				case 'd':
					part = (d < 10) ? ("0" + d) : d;
				break;
				case 'j':
					part = d;
				break;
				case 'H':
					part = (hr < 10) ? ("0" + hr) : hr;
				break;
				case 'h':
					part = hr;
				break;
				case 'G':
					part = (ir < 10) ? ("0" + ir) : ir;
				break;
				case 'g':
					part = ir;
				break;
				case 'z':
					part = (dy < 100) ? ((dy < 10) ? ("00" + dy) : ("0" + dy)) : dy;
				break;
				break;
				case 'm':
					part = (m < 9) ? ("0" + (1+m)) : (1+m);
				break;
				case 'n':
					part = m+1;
				break;
				case 'i':
					part = (min < 10) ? ("0" + min) : min;
				break;
				case 'a':
					part = pm ? "pm" : "am";
				break;
				case 'A':
					part = pm ? "PM" : "AM";
				break;
				case 'U':
					part = Math.floor(date.getTime() / 1000);
				break;
				case 's':
					part = (sec < 10) ? ("0" + sec) : sec;
				break;
				case 'u':
					part = date.getMilliseconds();
				break;
				case 'w':
					part = w;
				break;
				case 'N':
					part = (w == 0) ? 7 : w;
				break;
				case 'y':
					part = ('' + y).substr(2, 2);
				break;
				case 'Y':
					part = y;
				break;
			}
			parts[i] = part;
		}
		return parts.join('');
	},
	
	clickDate = function(date,format){
		var that = this,
			config = this.options,
			current;
		if(!(date instanceof Date)){
			if(typeof date == 'undefined'){
				date = config.today;
			} else if(!isNaN(parseFloat(date)) && isFinite(date)){
				date = new Date(date);
			} else {
				date = strToDate.call(this,date,(format || config.format));
			}
		}
			
		date.setZeroSec();
		current = date.valueOf();
			
		switch(config.mode){
			case 'single':
				if(config.dateValue == current){
					config.dateValue = null;
				} else {
					config.dateValue = current;
				}
				break;
				
			case 'multiple':
				if(config.dateValue.indexOf(current) >= 0){
					config.dateValue.splice(config.dateValue.indexOf(current),1);
				} else {
					if(!$.isArray(config.dateValue)){
						if(config.dateValue != ''){
							config.dateValue = [config.dateValue];
						} else {
							config.dateValue = [];
						}
					}
					config.dateValue.push(current);
				}
				break;
				
			case 'range':
				if(!config.rangeStart || (config.rangeStart && config.rangeEnd)){
					config.dateValue = [];
					config.dateValue[0] = current;
					config.rangeStart = true;
					config.rangeEnd = false;
				} else if(!config.rangeEnd) {
					config.dateValue[1] = current;
					config.rangeEnd = true;
				}
				break;
		}
		if(config.dateValue instanceof Array){
			config.dateValue.sort();
		}
		
		trigger.call(this,this.options.event_date_changed, this.options.onDateChange,[]);
		updateUi.call(this);
	},
	
	updateUi = function(){
		var that = this,
			config = this.options,values,val='',uiVal='',tmpDate,uiRender,method;

		switch(config.mode){
			case 'single':
				val = '';
				uiVal = '';
				if(config.dateValue){
					tmpDate = new Date(config.dateValue);
					val = dateToStr.call(this,tmpDate,config.format);
					uiVal = dateToStr.call(this,tmpDate,config.uiFormat);
				}
				break;
				
			case 'multiple':
				val = [];uiVal = [];
				values = config.dateValue;
				for(var g=0;g<values.length;g++){
					uiRender = '';
					tmpDate = new Date(values[g]);
					val.push(dateToStr.call(this,tmpDate,config.format));
					
					uiRender += '<span class="dtp-date-multiple" data-value="'+ tmpDate.valueOf() +'">';
					uiRender += dateToStr.call(this,tmpDate,config.uiFormat);
					uiRender += '</span> ';
					uiVal.push(uiRender);
				}

				if(values.length > 0){
					val = val.join(config.separator);
					uiVal = uiVal.join('');
				} else {
					uiVal = val = '';
				}
				break;				
			case 'range':
				val = [];uiVal = [];
				values = config.dateValue;
				if(values.length == 2){
					for(var g=0;g<2;g++){
						tmpDate = new Date(values[g]);
						val.push(dateToStr.call(this,tmpDate,config.format));
						uiVal.push(dateToStr.call(this,tmpDate,config.uiFormat));
					}
					val = val.join(config.separator);
					uiVal = uiVal.join(config.separator);
				}
				break;
		}

		config.$element.val(val).trigger('keyup');
		var uiTarget = getUiTarget.call(this);
		if(uiTarget.length){
			method = 'val';
			if(config.mode == 'multiple' && !uiTarget.is('input')){
				method = 'html';
			}
			uiTarget[method](uiVal);
		}
		addCalendars.call(that);
	},
		
	clickItem = function(type,direction,value){
		var that = this,
			config = this.options,
			direction = (direction || 1),
			type = getViewType.call(this,type),
			nextView = config.views.indexOf(type) + direction,
			maxView = config.views.indexOf(getViewType.call(this,config.maxView)),
			minView = config.views.indexOf(getViewType.call(this,config.minView));			
			
		if(direction < 0 && value){
			config.today = new Date(value);
		}
		
		if(nextView < minView){
			// add/remove datetime
			if(value){
				clickDate.call(this, value);
				
				if(config.style == 'popup' && config.autoclose){
					config.$element.trigger('click');
				}
				return;
			} else {
				nextView = 4;
			}
		}
		
		if(nextView > maxView){
			nextView = (minView == 0) ? 1 : minView;
		}
		
		addCalendars.call(that,config.views[nextView]);		
	},
	
	addEventsHandlers = function(){
		var that = this,
			config = this.options;
	
		config.$parent.find('.dtp-calendars').on('click','.dtp-item',function(event){
			event.preventDefault();
			event.stopPropagation();
			
			
			var value = parseInt($(this).attr('data-value')),
			type = $(this).closest('[data-view-type]').attr('data-view-type'),
			message = $(this).attr('data-dtp-message');
			
			if(message){
				alert(message);
			}
			
			if(!$(this).hasClass('dtp-disabled')){
				clickItem.call(that,type,-1,value);
			}
		});
		
		config.$parent.parent().find('.dtp-values').on('click','.dtp-date-multiple',function(event){
			event.preventDefault();
			event.stopPropagation();
			clickDate.call(that,parseInt($(this).attr('data-value')));
		});
				
		config.$parent.find('.dtp-calendars').on('click','.dtp-btn-viewtype',function(event){
			event.preventDefault();
			event.stopPropagation();
			
			var view,type = $(this).closest('[data-view-type]').attr('data-view-type');
			clickItem.call(that,type);
		});
		
		config.$parent.find('.dtp-calendars').on('click','.dtp-btn-next, .dtp-btn-prev',function(event){
			event.preventDefault();
			event.stopPropagation();
			
			var type = $(this).closest('[data-view-type]').attr('data-view-type'),qty = (type == 'years') ? 12 : 1;			
			if($(event.currentTarget).hasClass('dtp-btn-prev')){
				qty *= -1;
			}

			that.options.today[config.methods[type]](qty);
			addCalendars.call(that);
		});

		if(config.style == 'popup'){
			var targEle = this.options.$element,
				calContainer = that.options.$parent.find('.dtp-calendars');
			calContainer.hide().removeClass('open');
			
			var uiTarget = getUiTarget.call(this);
			if(uiTarget.length){
				targEle.add(uiTarget);
			}
			targEle.on('click',function(){
				if(calContainer.hasClass('open')){
					calContainer.removeClass('open').slideUp();
				} else {
					calContainer.addClass('open').slideDown();
				}
			});
		}
	};
	
    // The actual plugin constructor
    var Plugin = function ( element ) {
        /*
         * Plugin instantiation
         */		
		
		this.others_opts = {
			// Events
			event_date_changed: pluginName +'_changed',
			event_initialized: pluginName +'_initialized',
			
			views: ['times','days','months','years'],
			
			methods: {
				years: 'addYears',
				months: 'addYears',
				days: 'addMonths',
				times: 'addDays'
			},
			
			dateValue		: '',

			initialized: false,
			
			today: (new Date).setHours(0,0,0,0).valueOf(),
			
			// just creating a jQuery object, just in case
			$element: $(element)	
		}
		
		this.options = $.extend( {}, defaults );
    };

    Plugin.prototype = {
        init: function ( options ) {
			var ele,config,maxView,minView,startView,todayDate,inlineData;
			if(this.options.initialized){
				return;
			}
		
			$.extend( this.options, options, this.others_opts );

			if(!this.options.$element.is('input[type=text]')){
				return;
			}
			
			ele = this.options.$element;
			
			if(!ele.is('input[type=text]')){
				return;
			}
			
			// check we have some data on the element
			inlineData = ele.data();

			this.options = $.extend({}, this.options, options, inlineData, this.others_opts );
						
			config = this.options;
			
			config.text.days.unshift(config.text.days[6]);
			config.text.daysShort.unshift(config.text.daysShort[6]);

			// check/clean specialDates
			if(config.specialDates instanceof Array){
				var cleanSelDates = [];
				for(var d=0;d<config.specialDates.length;d++){
					var val = config.specialDates[d],valInd;
					
					if(!$.isPlainObject(val)){
						val = {
							date: val,
							repeatWeek: true,
							selectable: true
						};
					}
					
					if(!isNaN(parseFloat(val.date)) && isFinite(val.date) && val.date >= 0 && val.date <=6){
						val.date = createFakeDate(val.day);
					} else if(typeof val.date == 'string' && val.date != ''){
						valInd = config.text.days.indexOf(val);
						if(valInd >= 0){
							val.date = createFakeDate(valInd);
						} else {
							val.date = strToDate.call(this, val.date, config.format);
						}
					}
					
					if(!(val.date instanceof Date)) continue;

					// check and clean the selectableTimes
					if(typeof val.selectableTimes == 'string'){
						val.selectableTimes = [val.selectableTimes];
					}
					
					val.selectableTimes = (val.selectableTimes instanceof Array) ? cleanTslots.call(this,val.selectableTimes) : [];
					cleanSelDates.push(val);
				}
				config.specialDates = cleanSelDates;
			}
			
			config.maxView = config.maxView ? getViewType.call(this,config.maxView) : 'years';
			config.minView = config.minView ? getViewType.call(this,config.minView) : 'times';
			config.startView = config.startView ? getViewType.call(this,config.startView) : 'days';
			maxView = config.views.indexOf(config.maxView);
			minView = config.views.indexOf(config.minView);
			startView = config.views.indexOf(config.startView);
			
			if(startView < minView){
				config.startView = config.views[minView];
			} else if(startView > maxView){
				config.startView = config.views[maxView];
			}
			
			config.calendars	= Math.max(1, parseInt(config.calendars, 10) || 1);
			config.mode		= /single|multiple|range/.test(config.mode) ? config.mode : 'single';
			config.timeFormat		= /12|24/.test(config.timeFormat) ? config.timeFormat : 24;
			if (typeof config.min == 'string' && config.min != '') {
				config.min = strToDate.call(this,config.min, config.format);
			}
			
			if(!(config.min instanceof Date) || config.min.toString() == 'Invalid Date'){
				config.min = null;
			}
			if (typeof config.max === 'string' && config.max != '') {
				config.max = strToDate.call(this, config.max, config.format);
			}
			
			if(!(config.max instanceof Date) || config.max.toString() == 'Invalid Date'){
				config.max = null;
			}

			config.today	= new Date();
			config.dateValue = '';
			if(config.$element.val() != ''){
				config.dateValue = strToDate.call(this,config.$element.val(), config.format);
						
				if (config.mode != 'single') {
					if (config.dateValue instanceof Array) {
						for (var i = 0; i < config.dateValue.length; i++) {
							config.dateValue[i] = (strToDate.call(this,config.dateValue[i], config.format).setHours(0,0,0,0)).valueOf();
						}
						if (config.mode == 'range') {
							config.dateValue[1] = ((new Date(config.dateValue[1])).setHours(23,59,59,0)).valueOf();
						}
					} else {
						config.dateValue = [config.dateValue.valueOf()];
						if (config.mode == 'range') {
							config.dateValue.push(((new Date(config.dateValue[0])).setHours(23,59,59,0)).valueOf());
						}
					}
					todayDate	= config.dateValue[0];
				} else {
					config.dateValue	= config.dateValue.valueOf();
					todayDate	= config.dateValue;
				}
				
				config.today	= new Date(todayDate);
				if (config.today.toString() == 'Invalid Date') {
					config.today = new Date;
				}
				config.today.setDate(1);
				config.today.setHours(0,0,0,0);
			}
			
			this.options = config;
			if(getUiTarget.call(this).length){
				// hide the original input
				ele.hide();
			}
			
			addHTML.call(this);
			
			addCalendars.call(this);
			addEventsHandlers.call(this);
			
			// initialization completed
			this.options.initialized = true;			
			trigger.call(this,this.options.event_initialized, this.options.onInit,[]);
        },
		
		strToDate : function(str,format){		
			return strToDate.call(this,str,format);
		},
			
		dateToStr : function(dateObj,format){		
			return dateToStr.call(this,dateObj,format);
		},
		
		getValue : function(){		
			return this.options.dateValue;
		},
		
		setValue : function(value,format){
			this.options.dateValue = null;
			this.addValue(value,format);
		},
		
		addValue : function(value,format){
			if(!$.isArray(value)){
				value = [value];
			}
			
			for(var k in value){
				value[k] = strToDate.call(this,value[k],format);
			}
			
			if(this.options.mode == 'single'){
				value = value[0];
			} else {
				if(!(this.options.dateValue instanceof Array) && this.options.dateValue != '') {
					this.options.dateValue = [this.options.dateValue];
				} else {
					this.options.dateValue = [];
				}
				value = this.options.dateValue.concat(value);
			}
			
			this.options.dateValue = value;
			
			if(this.options.dateValue instanceof Array){
				this.options.dateValue.sort();
			}
			updateUi.call(this);
		},

        destroy: function () {
		var that = this,
			config = this.options;
			
			config.$parent.find('.dtp-values').remove();
			config.$parent.find('.dtp-calendars').remove();
			
			config.$element.show();
			if(config.$element.parent().hasClass('input-append') || config.$element.parent().hasClass('input-prepend')){
				config.$element.parent().show();
			}
			
			config.$parent.replaceWith(config.$parent.html());
			
            // unset Plugin data instance
            this.element.data( dataPlugin, null );
        }
    }

    /*
     * Plugin wrapper, preventing against multiple instantiations and
     * allowing any public function to be called via the jQuery plugin,
     * e.g. $(element).pluginName('functionName', arg1, arg2, ...)
     */
    $.fn[ pluginName ] = function ( arg ) {

        var args, instance;

		if(this.length <= 0 && typeof arg != 'string'){
			return this;
		}
		
        // only allow the plugin to be instantiated once
        if (!( this.data( dataPlugin ) instanceof Plugin )) {

            // if no instance, create one
            this.data( dataPlugin, new Plugin( this ) );
        }
		
        instance = this.data( dataPlugin );

		if(typeof instance != 'undefined'){
			instance.element = this;
		} else {
			instance = new Plugin( this );
		}

        // Is the first parameter an object (arg), or was omitted,
        // call Plugin.init( arg )
        if (typeof arg === 'undefined' || typeof arg === 'object') {

            if ( typeof instance['init'] === 'function' ) {
                instance.init( arg );
				
            }

        // checks that the requested public method exists
        } else if ( typeof arg === 'string' && typeof instance[arg] === 'function' ) {

            // copy arguments & remove function name
            args = Array.prototype.slice.call( arguments, 1 );

            // call the method
            return instance[arg].apply( instance, args );
        } else {
            $.error('Method ' + arg + ' does not exist on jQuery.' + pluginName);
        }
    };

}(jQuery, window, document));
