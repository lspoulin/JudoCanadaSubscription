/* -- DO NOT REMOVE --
 * jQuery DCalendar and DCalendar Picker 2.1 plugin
 * 
 * Author: Dionlee Uy
 * Email: dionleeuy@gmail.com
 *
 * Date: Thursday, May 12 2016
 *
 * @requires jQuery
 * -- DO NOT REMOVE --
 */
if (typeof jQuery === 'undefined') { throw new Error('DCalendar.Picker: This plugin requires jQuery'); }
+function ($) {

	Date.prototype.getDays = function() { return new Date(this.getFullYear(), this.getMonth() + 1, 0).getDate(); };

	var months = ['January','February','March','April','May','June','July','August','September','October','November','December'],
		short_months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
		daysofweek = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],
		short_days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'],
		ex_keys = [9,112,113,114,115,116,117,118,119,120,121,122,123],
		DCAL_DATA = 'dcalendar',

		DCalendar = function(elem, options) {
			this.elem = $(elem);
			this.options = options;
			this.calendar = null;		//calendar container
			this.today = new Date();	//current date

			//current selected date, default is today if no value given
			this.date = this.elem.val() === '' ? new Date() : this.reformatDate(this.elem.val()).date;
			this.viewMode = 'days';
			this.minDate = this.elem.data('mindate');
			this.maxDate = this.elem.data('maxdate');
			this.rangeFromEl = this.elem.data('rangefrom');
			this.rangeToEl = this.elem.data('rangeto');
			
			this.selected = new Date(this.date.getFullYear(), this.date.getMonth(), this.date.getDate());
			
			var that = this;

			this.create(this.viewMode);

			this.calendar.find('.calendar-head-card .calendar-date-wrapper').click(function () {
				that.selected = new Date(that.today.getFullYear(), that.today.getMonth(), that.today.getDate());

				//Trigger select event
				that.selectDate();
				that.selectedView();
			});
			this.calendar.find('.calendar-prev').click(function () { that.getNewMonth('left', true); });
			this.calendar.find('.calendar-next').click(function () { that.getNewMonth('right', true); });
			this.calendar.find('.calendar-curr-month').click(function () { that.getMonths(); });
		    this.calendar.find('.calendar-date-holder').on('click', '.calendar-dates .date:not(.date.month) a', function () {
		    	var span = $(this).parent(),
		    		day = parseInt($(this).text()),
					plus = span.hasClass('pm') ? -1 : span.hasClass('nm') ? 1 : 0,
					selectedDate = new Date(that.date.getFullYear(), that.date.getMonth() + plus, day);
				
				if(that.disabledDate(selectedDate)) return;

				that.selected = selectedDate;
				that.calendar.find('.calendar-dates .date').removeClass('selected');
				span.addClass('selected');

				//Trigger select event
				that.selectDate();
			}).on('click', '.calendar-dates .date.month a', function () {
				var selMonth = parseInt($(this).parent().attr('data-month'));
				that.viewMode = 'days';
				that.date.setMonth(selMonth);
				that.getNewMonth(null, false);
			});

			this.getNewMonth(null, false);
		};

	DCalendar.prototype = {
		constructor : DCalendar,
		/* Parses date string using default or specified format. */
		reformatDate : function (date, dateFormat) {
			var that = this,
				format = typeof dateFormat === 'undefined' ? that.options.format : dateFormat,
				dayLength = (format.match(/d/g) || []).length,
				monthLength = (format.match(/m/g) || []).length,
				yearLength = (format.match(/y/g) || []).length,
				isFullMonth = monthLength == 4,
				isMonthNoPadding = monthLength == 1,
				isDayNoPadding = dayLength == 1,
				lastIndex = date.length,
				firstM = format.indexOf('m'), firstD = format.indexOf('d'), firstY = format.indexOf('y'),
				month = '', day = '', year = '';

			// Get month on given date string using the format (default or specified)
			if(isFullMonth) {
				var monthIdx = -1;
				$.each(months, function (i, m) { if (date.indexOf(m) >= 0) monthIdx = i; });
				month = months[monthIdx];
				format = format.replace('mmmm', month);
				firstD = format.indexOf('d');
				firstY = firstY < firstM ? format.indexOf('y') : format.indexOf('y', format.indexOf(month) + month.length);
			} else if (!isDayNoPadding && !isMonthNoPadding || (isDayNoPadding && !isMonthNoPadding && firstM < firstD)) {
				month = date.substr(firstM, monthLength);
			} else {
				var lastIndexM = format.lastIndexOf('m'),
					before = format.substring(firstM - 1, firstM),
					after = format.substring(lastIndexM + 1, lastIndexM + 2);

				if (lastIndexM == format.length - 1) {
					month = date.substring(date.indexOf(before, firstM - 1) + 1, lastIndex);
				} else if (firstM == 0) {
					month = date.substring(0, date.indexOf(after, firstM));
				} else {
					month = date.substring(date.indexOf(before, firstM - 1) + 1, date.indexOf(after, firstM + 1));
				}
			}

			// Get date on given date string using the format (default or specified)
			if (!isDayNoPadding && !isMonthNoPadding || (!isDayNoPadding && isMonthNoPadding && firstD < firstM)) {
				day = date.substr(firstD, dayLength);
			} else {
				var lastIndexD = format.lastIndexOf('d');
					before = format.substring(firstD - 1, firstD),
					after = format.substring(lastIndexD + 1, lastIndexD + 2);

				if (lastIndexD == format.length - 1) {
					day = date.substring(date.indexOf(before, firstD - 1) + 1, lastIndex);
				} else if (firstD == 0) {
					day = date.substring(0, date.indexOf(after, firstD));
				} else {
					day = date.substring(date.indexOf(before, firstD - 1) + 1, date.indexOf(after, firstD + 1));
				}
			}

			// Get year on given date string using the format (default or specified)
			if (!isMonthNoPadding && !isDayNoPadding || (isMonthNoPadding && isDayNoPadding && firstY < firstM && firstY < firstD)
				|| (!isMonthNoPadding && isDayNoPadding && firstY < firstD) || (isMonthNoPadding && !isDayNoPadding && firstY < firstM)) {
				year = date.substr(firstY, yearLength);
			} else {
				var before = format.substring(firstY - 1, firstY);
				year = date.substr(date.indexOf(before, firstY - 1) + 1, yearLength);
			}

			return { m: month, d: day, y: year, date: isNaN(parseInt(month)) ? new Date(month + " " + day + ", " + year) : new Date(year, month - 1, day) };
		},
		/* Returns formatted string representation of selected date */
		formatDate : function (format) {
			var d = new Date(this.selected), day = d.getDate(), m = d.getMonth(), y = d.getFullYear();
			return format.replace(/(yyyy|yy|mmmm|mmm|mm|m|dd|d)/gi, function (e) {
				switch(e.toLowerCase()){
					case 'd': return day;
					case 'dd': return (day < 10 ? "0"+day: day);
					case 'm': return m+1;
					case 'mm': return (m+1 < 10 ? "0"+(m+1): (m+1));
					case 'mmm': return short_months[m];
					case 'mmmm': return months[m];
					case 'yy': return y.toString().substr(2,2);
					case 'yyyy': return y;
				}
			});
		},
		/* Selects date and trigger event (for other actions - if specified) */
		selectDate : function () {
			var that = this,
				newDate = that.formatDate(that.options.format),
				e = $.Event('dateselected', {date: newDate});

			that.elem.trigger(e);
		},
		/* Determines if date is disabled */
		disabledDate: function (date) {
			var that = this, rangeFrom = null, rangeTo = null, rangeMin = null, rangeMax = null, min = null, max = null,
				now = new Date(), today = new Date(now.getFullYear(), now.getMonth(), now.getDate());

			if (that.minDate) min = that.minDate === "today" ? today : new Date(that.minDate);
			if (that.maxDate) max = that.maxDate === "today" ? today : new Date(that.maxDate);

			if (that.rangeFromEl) {
				var fromEl = $(that.rangeFromEl),
					fromData = fromEl.data(DCAL_DATA);
					fromFormat = fromData.options.format,
					fromVal = fromEl.val();

				rangeFrom = that.reformatDate(fromVal, fromFormat).date;
				rangeMin = fromData.minDate === "today" ? today : new Date(fromData.minDate);
			}

			if (that.rangeToEl) {
				var toEl = $(that.rangeToEl),
					toData = toEl.data(DCAL_DATA);
					toFormat = toData.options.format,
					toVal = toEl.val();

				rangeTo = that.reformatDate(toVal, toFormat).date;
				rangeMax = toData.maxDate === "today" ? today : new Date(toData.maxDate);
			}

			return (min && date < min) || (max && date > max) || (rangeFrom && date < rangeFrom) || (rangeTo && date > rangeTo) ||
				(rangeMin && date < rangeMin) || (rangeMax && date > rangeMax);
		},
		/* Gets list of months (for month view) */
		getMonths : function () {
			var that = this,
				currentYear = that.today.getFullYear(),
				currentMonth = that.today.getMonth();

			if(that.viewMode !== 'days') return;
			var cal = that.calendar;
				curr = cal.find('.calendar-dates'),
				dayLabel = cal.find('.calendar-labels'),
				currMonth = cal.find('.calendar-curr-month'),
				container = cal.find('.calendar-date-holder'),
				cElem = curr.clone(),
				rows = [], cells = [], count = 0;

			that.viewMode = 'months';
			currMonth.text(that.date.getFullYear());
			dayLabel.addClass('invis');
			for (var i = 1; i < 4; i++) {
				var row = [$("<span class='date month'></span>"), $("<span class='date month'></span>"), $("<span class='date month'></span>"), $("<span class='date month'></span>")];
				for (var a = 0; a < 4; a++) {
					row[a].html("<a href='javascript:void(0);'>" + short_months[count] + "</a>").attr('data-month', count);
					count++;
				}
				rows.push(row);
			}
			$.each(rows, function(i, v){
			    var row = $('<span class="cal-row"></span>'), l = v.length;
				for(var i = 0; i < l; i++) { row.append(v[i]); }
				cells.push(row);
			});
			container.parent().height(container.parent().outerHeight(true));
			cElem.empty().append(cells).addClass('months load').appendTo(container);
			curr.addClass('hasmonths');
			setTimeout(function () { cElem.removeClass('load'); }, 10);
			setTimeout(function () { curr.remove(); }, 300);
		},
		/* Gets days for month of 'newDate'*/
		getDays : function (newDate, callback) {
		    var that = this,
				ndate = new Date(newDate.getFullYear(), newDate.getMonth(), newDate.getDate()),
				today = new Date(that.today.getFullYear(), that.today.getMonth(), that.today.getDate()),
				days = ndate.getDays(), day = 1,
		        d = new Date(newDate.getFullYear(), newDate.getMonth(), newDate.getDate()),
				nmStartDay = 1, weeks = [], dates = [];

			for(var i = 1; i <= 6; i++){
				var week = [$('<span class="date"></span>'), $('<span class="date"></span>'), $('<span class="date"></span>'),
							$('<span class="date"></span>'), $('<span class="date"></span>'), $('<span class="date"></span>'),
							$('<span class="date"></span>')];

				while(day <= days) {
					d.setDate(day);
					var dayOfWeek = d.getDay();

					if (d.getTime() == today.getTime()) week[dayOfWeek].addClass('current');

                    if (that.disabledDate(d)) week[dayOfWeek].addClass('disabled');

					if(i === 1 && dayOfWeek === 0){
						break;
					} else if (dayOfWeek < 6) {
					    if (d.getTime() == that.selected.getTime()) week[dayOfWeek].addClass('selected');

						week[dayOfWeek].html('<a href="javascript:void(0);">' + (day++) + '</a>');
					} else {
					    if (d.getTime() == that.selected.getTime()) week[dayOfWeek].addClass('selected');

						week[dayOfWeek].html('<a href="javascript:void(0);">' + (day++) + '</a>');
						break;
					}
				}
				/* For days of previous and next month */
				if (i === 1 || i > 4) {
					// First week
				    if (i === 1) {
				        var pmDate = new Date(newDate.getFullYear(), newDate.getMonth() - 1, 1);
				        var pMonth = pmDate.getMonth(), pDays = 0;
				        pDays = pmDate.getDays();
						for (var a = 6; a >= 0; a--) {
						    if (week[a].text() !== '') continue;

						    pmDate.setDate(pDays);
						    week[a].html('<a href="javascript:void(0);">' + (pDays--) + '</a>').addClass('pm');

							if (that.disabledDate(pmDate)) week[a].addClass('disabled');

							if (pmDate.getTime() == that.selected.getTime()) week[a].addClass('selected');
							if (pmDate.getTime() == today.getTime()) week[a].addClass('current');
						}
					} 
					// Last week
					else if (i > 4) {
					    var nmDate = new Date(d.getFullYear(), d.getMonth() + 1, 1);
						for (var a = 0; a <= 6; a++) {
						    if (week[a].text() !== '') continue;

						    nmDate.setDate(nmStartDay);
						    week[a].html('<a href="javascript:void(0);">' + (nmStartDay++) + '</a>').addClass('nm');

							if (that.disabledDate(nmDate)) week[a].addClass('disabled');

							if (nmDate.getTime() == that.selected.getTime()) week[a].addClass('selected');
							if (nmDate.getTime() == today.getTime()) week[a].addClass('current');
						}
					}
				}
				weeks.push(week);
			}
			$.each(weeks, function(i, v){
				var row = $('<span class="cal-row"></span>'), l = v.length;
				for(var i = 0; i < l; i++) { row.append(v[i]); }
				dates.push(row);
			});
			callback(dates);
		},
		/* Sets current view based on user interaction (on arrows) */
		getNewMonth : function (dir, isTrigger) {
			var that = this,
				cal = that.calendar;
				curr = cal.find('.calendar-dates:not(.left):not(.right)'),
				lblTodayDay = cal.find('.calendar-dayofweek'),
				lblTodayMonth = cal.find('.calendar-month'),
				lblTodayDate = cal.find('.calendar-date'),
				lblTodayYear = cal.find('.calendar-year'),
				lblMonth = cal.find('.calendar-curr-month'),
				container = cal.find('.calendar-date-holder');

			if (that.viewMode === 'days') {
				if (isTrigger) {
					that.date.setDate(1);
					that.date.setMonth(that.date.getMonth() + ( dir === 'right' ? 1 : -1));
				}
				if(isTrigger || that.options.mode === 'calendar' || curr.hasClass('months')) {
					that.getDays(that.date, function (dates) {
						if (isTrigger) {
							var cElem = curr.clone();
							cElem.addClass(dir).empty().append(dates)[dir == 'left' ? 'prependTo' : 'appendTo'](container);
							setTimeout(function() {
								curr.addClass(dir == 'left' ? 'right' : 'left');
								cElem.removeClass(dir);
								setTimeout(function () { cal.find('.calendar-dates.'+(dir == 'left' ? 'right' : 'left')+'').remove(); }, 300);
							}, 10);
						} else {
							if (curr.hasClass('months')) {
								var cElem = curr.clone();
								$('.calendar-labels').removeClass('invis');
								cElem.empty().append(dates).addClass('hasmonths').appendTo(container);
								curr.addClass('load');
								setTimeout(function () { cElem.removeClass('hasmonths'); }, 10);
								container.parent().removeAttr('style');
								setTimeout(function () {
									cElem.removeClass('months');
									setTimeout(function () { cal.find('.calendar-dates.months').remove(); }, 300);
								}, 10);
							} else {
								curr.append(dates);
							}
						}
					});
				}
				
				lblMonth.text(months[that.date.getMonth()] + ' ' + that.date.getFullYear());
				
				if (!isTrigger && !curr.hasClass('months')) {
					lblTodayDay.text(short_days[that.today.getDay()]);
					lblTodayMonth.text(short_months[that.today.getMonth()]);
					lblTodayDate.text(that.today.getDate());
					lblTodayYear.text(that.today.getFullYear());
				}
			} else {
				that.date.setYear(that.date.getFullYear() + ( dir === 'right' ? 1 : -1))
				lblMonth.text(that.date.getFullYear());
			}
		},
		/* Sets current view to selected date */
		selectedView : function () {
			var that = this,
				cal = that.calendar;
				curr = cal.find('.calendar-dates:eq(0)'),
				lblMonth = cal.find('.calendar-curr-month'),
				lblDays = cal.find('.calendar-labels');

			that.getDays(that.selected, function (dates) {
				curr.html(dates);
			});

			lblMonth.text(months[that.selected.getMonth()] + ' ' + that.selected.getFullYear());
			lblDays.removeClass('invis');
			that.viewMode = 'days';
		},
		/* Creates components for the calendar */
		create : function(){
			var that = this,
				mode = that.options.mode,
				theme = that.options.theme,
				overlay = $('<div class="calendar-overlay"></div>'),
				wrapper = $('<div class="calendar-wrapper load"></div>'),
				cardhead = $('<section class="calendar-head-card"><span class="calendar-year"></span><span class="calendar-date-wrapper" title="Select current date."><span class="calendar-dayofweek"></span>, <span class="calendar-month"></span> <span class="calendar-date"></span></span></section>'),
				container = $('<div class="calendar-container"></div>'),
				calhead = $('<section class="calendar-top-selector"><span class="calendar-prev">&lsaquo;</span><span class="calendar-curr-month"></span><span class="calendar-next">&rsaquo;</span></section>'),
				datesgrid = $('<section class="calendar-grid">'
							+ '<div class="calendar-labels"><span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span></div>'
							+ '<div class="calendar-date-holder"><section class="calendar-dates"></section></div></section>');

			calhead.appendTo(container);
			datesgrid.appendTo(container);

			overlay.click(function (e) { that.hide(); });
			wrapper.click(function (e) { e.stopPropagation(); });

			wrapper.append(cardhead).append(container).appendTo(mode === 'calendar' ? that.elem : overlay);
			that.calendar = mode === 'calendar' ? that.elem : wrapper;

			switch(theme) {
				case 'red':
				case 'blue':
				case 'green':
				case 'purple':
				case 'indigo':
				case 'teal':
					wrapper.attr('data-theme', theme);
				break;
				default:
					wrapper.attr('data-theme', $.fn.dcalendar.defaults.theme);
				break;
			}

			if(mode !== 'calendar') { 
				wrapper.addClass('picker');
				overlay.appendTo('body');
			}
		},
		/* Shows the calendar (date picker) */
		show : function () {
			$('body').attr('datepicker-display', 'on');
			this.date = new Date(this.selected.getFullYear(), this.selected.getMonth(), this.selected.getDate());
			this.selectedView();
			this.calendar.parent().fadeIn('fast');
			this.calendar.removeClass('load');
		},
		/* Hides the calendar (date picker) */
		hide : function (callback) {
			var that = this;
			that.calendar.addClass('load');
			that.calendar.parent().fadeOut(function () {
				$('body').removeAttr('datepicker-display');
				if(callback) callback();
				if(that.elem.is('input')) that.elem.focus();
			});
		}
	};

	/* DEFINITION FOR DCALENDAR */
	$.fn.dcalendar = function(opts){
		return $(this).each(function(index, elem){
			var that = this;
 			var $this = $(that),
 				data = $(that).data(DCAL_DATA),
 				options = $.extend({}, $.fn.dcalendar.defaults, $this.data(), typeof opts === 'object' && opts);
 			if(!data){
 				$this.data(DCAL_DATA, (data = new DCalendar(this, options)));
 			}
 			if(typeof opts === 'string') data[opts]();
		});
	};

	$.fn.dcalendar.defaults = {
		mode : 'calendar',
		format: 'mm/dd/yyyy',
		theme: 'blue',
		readOnly: true
	};

	$.fn.dcalendar.Constructor = DCalendar;

	/* DEFINITION FOR DCALENDAR PICKER */
	$.fn.dcalendarpicker = function(opts){
		return $(this).each(function(){
			var that = $(this);

			if(opts){
				opts.mode = 'datepicker';
				that.dcalendar(opts);
			} else{
				that.dcalendar({mode: 'datepicker'});
			}

			that.on('click', function (e) {
				var cal = that.data(DCAL_DATA);
				cal.show();
				this.blur();
			}).on('dateselected', function (e) {
				var cal = that.data(DCAL_DATA);
				that.val(e.date).trigger('onchange');
				cal.hide(function () {
					that.trigger($.Event('datechanged', {date: e.date}));
				});				
			}).on('keydown', function(e){
				if(ex_keys.indexOf(e.which) < 0 && that.data(DCAL_DATA).options.readOnly) return false; 
			});
			$(document).on('keydown', function (e) {
				if(e.keyCode != 27) return;
				that.data(DCAL_DATA).hide();
			});
		});
	};
}(jQuery);
const COUNTRY_SELECT_HTML = '<select class="w3-input" type="text" id="idCountry" name="country" ><option value=""></option><option value="Afghanistan" >Afghanistan</option><option value="Albania" >Albania</option><option value="Algeria" >Algeria</option><option value="Andorra" >Andorra</option><option value="Angola" >Angola</option><option value="Antigua and Barbuda" >Antigua and Barbuda</option><option value="Argentina" >Argentina</option><option value="Armenia" >Armenia</option><option value="Australia" >Australia</option><option value="Austria" >Austria</option><option value="Azerbaijan" >Azerbaijan</option><option value="Bahamas" >Bahamas</option><option value="Bahrain" >Bahrain</option><option value="Bangladesh" >Bangladesh</option><option value="Barbados" >Barbados</option><option value="Belarus" >Belarus</option><option value="Belgium" >Belgium</option><option value="Belize" >Belize</option><option value="Benin" >Benin</option><option value="Bhutan" >Bhutan</option><option value="Bolivia" >Bolivia</option><option value="Bosnia and Herzegovina" >Bosnia and Herzegovina</option><option value="Botswana" >Botswana</option><option value="Brazil" >Brazil</option><option value="Brunei" >Brunei</option><option value="Bulgaria" >Bulgaria</option><option value="Burkina Faso" >Burkina Faso</option><option value="Burundi" >Burundi</option><option value="Cambodia" >Cambodia</option><option value="Cameroon" >Cameroon</option><option value="Canada" selected="selected">Canada</option><option value="Cape Verde" >Cape Verde</option><option value="Central African Republic" >Central African Republic</option><option value="Chad" >Chad</option><option value="Chile" >Chile</option><option value="China" >China</option><option value="Colombia" >Colombia</option><option value="Comoros" >Comoros</option><option value="Congo (Brazzaville)" >Congo (Brazzaville)</option><option value="Congo" >Congo</option><option value="Costa Rica" >Costa Rica</option><option value="Cote d\'Ivoire" >Cote d\'Ivoire</option><option value="Croatia" >Croatia</option><option value="Cuba" >Cuba</option><option value="Cyprus" >Cyprus</option><option value="Czech Republic" >Czech Republic</option><option value="Denmark" >Denmark</option><option value="Djibouti" >Djibouti</option><option value="Dominica" >Dominica</option><option value="Dominican Republic" >Dominican Republic</option><option value="East Timor (Timor Timur)" >East Timor (Timor Timur)</option><option value="Ecuador" >Ecuador</option><option value="Egypt" >Egypt</option><option value="El Salvador" >El Salvador</option><option value="Equatorial Guinea" >Equatorial Guinea</option><option value="Eritrea" >Eritrea</option><option value="Estonia" >Estonia</option><option value="Ethiopia" >Ethiopia</option><option value="Fiji" >Fiji</option><option value="Finland" >Finland</option><option value="France" >France</option><option value="Gabon" >Gabon</option><option value="Gambia, The" >Gambia, The</option><option value="Georgia" >Georgia</option><option value="Germany" >Germany</option><option value="Ghana" >Ghana</option><option value="Greece" >Greece</option><option value="Grenada" >Grenada</option><option value="Guatemala" >Guatemala</option><option value="Guinea" >Guinea</option><option value="Guinea-Bissau" >Guinea-Bissau</option><option value="Guyana" >Guyana</option><option value="Haiti" >Haiti</option><option value="Honduras" >Honduras</option><option value="Hungary" >Hungary</option><option value="Iceland" >Iceland</option><option value="India" >India</option><option value="Indonesia" >Indonesia</option><option value="Iran" >Iran</option><option value="Iraq" >Iraq</option><option value="Ireland" >Ireland</option><option value="Israel" >Israel</option><option value="Italy" >Italy</option><option value="Jamaica" >Jamaica</option><option value="Japan" >Japan</option><option value="Jordan" >Jordan</option><option value="Kazakhstan" >Kazakhstan</option><option value="Kenya" >Kenya</option><option value="Kiribati" >Kiribati</option><option value="Korea, North" >Korea, North</option><option value="Korea, South" >Korea, South</option><option value="Kuwait" >Kuwait</option><option value="Kyrgyzstan" >Kyrgyzstan</option><option value="Laos" >Laos</option><option value="Latvia" >Latvia</option><option value="Lebanon" >Lebanon</option><option value="Lesotho" >Lesotho</option><option value="Liberia" >Liberia</option><option value="Libya" >Libya</option><option value="Liechtenstein" >Liechtenstein</option><option value="Lithuania" >Lithuania</option><option value="Luxembourg" >Luxembourg</option><option value="Macedonia" >Macedonia</option><option value="Madagascar" >Madagascar</option><option value="Malawi" >Malawi</option><option value="Malaysia" >Malaysia</option><option value="Maldives" >Maldives</option><option value="Mali" >Mali</option><option value="Malta" >Malta</option><option value="Marshall Islands" >Marshall Islands</option><option value="Mauritania" >Mauritania</option><option value="Mauritius" >Mauritius</option><option value="Mexico" >Mexico</option><option value="Micronesia" >Micronesia</option><option value="Moldova" >Moldova</option><option value="Monaco" >Monaco</option><option value="Mongolia" >Mongolia</option><option value="Morocco" >Morocco</option><option value="Mozambique" >Mozambique</option><option value="Myanmar" >Myanmar</option><option value="Namibia" >Namibia</option><option value="Nauru" >Nauru</option><option value="Nepal" >Nepal</option><option value="Netherlands" >Netherlands</option><option value="New Zealand" >New Zealand</option><option value="Nicaragua" >Nicaragua</option><option value="Niger" >Niger</option><option value="Nigeria" >Nigeria</option><option value="Norway" >Norway</option><option value="Oman" >Oman</option><option value="Pakistan" >Pakistan</option><option value="Palau" >Palau</option><option value="Panama" >Panama</option><option value="Papua New Guinea" >Papua New Guinea</option><option value="Paraguay" >Paraguay</option><option value="Peru" >Peru</option><option value="Philippines" >Philippines</option><option value="Poland" >Poland</option><option value="Portugal" >Portugal</option><option value="Qatar" >Qatar</option><option value="Romania" >Romania</option><option value="Russia" >Russia</option><option value="Rwanda" >Rwanda</option><option value="Saint Kitts and Nevis" >Saint Kitts and Nevis</option><option value="Saint Lucia" >Saint Lucia</option><option value="Saint Vincent" >Saint Vincent</option><option value="Samoa" >Samoa</option><option value="San Marino" >San Marino</option><option value="Sao Tome and Principe" >Sao Tome and Principe</option><option value="Saudi Arabia" >Saudi Arabia</option><option value="Senegal" >Senegal</option><option value="Serbia and Montenegro" >Serbia and Montenegro</option><option value="Seychelles" >Seychelles</option><option value="Sierra Leone" >Sierra Leone</option><option value="Singapore" >Singapore</option><option value="Slovakia" >Slovakia</option><option value="Slovenia" >Slovenia</option><option value="Solomon Islands" >Solomon Islands</option><option value="Somalia" >Somalia</option><option value="South Africa" >South Africa</option><option value="Spain" >Spain</option><option value="Sri Lanka" >Sri Lanka</option><option value="Sudan" >Sudan</option><option value="Suriname" >Suriname</option><option value="Swaziland" >Swaziland</option><option value="Sweden" >Sweden</option><option value="Switzerland" >Switzerland</option><option value="Syria" >Syria</option><option value="Taiwan" >Taiwan</option><option value="Tajikistan" >Tajikistan</option><option value="Tanzania" >Tanzania</option><option value="Thailand" >Thailand</option><option value="Togo" >Togo</option><option value="Tonga" >Tonga</option><option value="Trinidad and Tobago" >Trinidad and Tobago</option><option value="Tunisia" >Tunisia</option><option value="Turkey" >Turkey</option><option value="Turkmenistan" >Turkmenistan</option><option value="Tuvalu" >Tuvalu</option><option value="Uganda" >Uganda</option><option value="Ukraine" >Ukraine</option><option value="United Arab Emirates" >United Arab Emirates</option><option value="United Kingdom" >United Kingdom</option><option value="United States" >United States</option><option value="Uruguay" >Uruguay</option><option value="Uzbekistan" >Uzbekistan</option><option value="Vanuatu" >Vanuatu</option><option value="Vatican City" >Vatican City</option><option value="Venezuela" >Venezuela</option><option value="Vietnam" >Vietnam</option><option value="Yemen" >Yemen</option><option value="Zambia" >Zambia</option><option value="Zimbabwe" >Zimbabwe</option></select>';

const yearMin = 2010;
const point_category_order = [];
const rules_tournament = "<h3>REGISTRE DES POINTS SHIAI ET KATA</h3><p><small><strong>SHIAI</strong><br>Ippon = 10 pts<br>Wazari = 7 pts <br><strong>KATA</strong> <br>Les points seront attribués à 2 points de moins que le classement de leurs équipe.<br><strong>KATA et SHIAI</strong><br>5 points pour participation <br></small></p>";
const rules_technical_points = "<h3>REGISTRE DES POINTS TECHNIQUE ET NON-TECHNIQUE</h3><p> <small><b>POINTS TECHNIQUE</b><br>Certification PNCE (Code T1) (MAXIMUM DE 30pts/année)<br>DA - 5 points<br>DI - 10 points<br>CDev - 20 points<br>IV - 20 points<br>V - 20 points<br>Entraîneur (PNCE Certifié avec min. de 120h/année) (Code T2) (MAXIMUM DE 30pts/année)<br>DA - 5 points<br>DI - 10 points<br>CDev - 20 points<br>IV - 20 points<br>V - 20 points<br>Développement de club - Sensei - minimum de 25 membres (Code T9)<br>30 points/année<br>Directeur de Clinique (Code T3) (MAXIMUM DE 30pts/année)<br>Prov - 10<br>InterProv - 15 <br>Nat - 15<br>Int\'l - 20<br>Participant aux cliniques (Code T4) (MAXIMUM DE 20pts/année)<br>Prov - 5<br>Nat - 5<br>Int\'l - 5<br>Certification d\'arbitre (Code T5)<br>Prov - 10<br>Nat - 15<br>Int\'l - 20/20/20<br>Arbitrage (Code T6) (MAXIMUM DE 60pts/année)<br>Prov - 5 (MAXIMUM DE 25pts/année)<br>Nat - 10 (MAXIMUM DE 20pts/année)<br>Int\'l - 20<br>Certification de kata (Code T7)<br>Prov - 10<br>Nat - 15<br>Cont - 15<br>Int\'l - 20/20/20<br>Activité de Kata (Code T8) (MAXIMUM DE 30pts/année)<br>Prov - 5<br>InterProv - 10<br>Nat - 15<br>Int\'l - 20<br><br><b>POINTS NON-TECHNIQUE</b><br>Actif en judo (Code N1)<br>1kyu - 30 <br>1D/2D - 20 <br>3D+ - 10  <br>Bénévole de tournoi (Code N2) (MAXIMUM DE 10pts/année) <br>Prov - 3 <br>InterProv - 4 <br>Nat - 5 <br>Int\'l - 5 <br> </small></p>";

const pages = [
    "idDivFormPersonalInformations",
    "idDivJudoCanadaInformation",
    "idDivCertification",
    "idDivGrade",
    "idDivTechnicalPoint",
    "idDivFinalPoint",
    "idDivIJFOnly",
    "idDivPayForm"
];

const labels = [
    {label:"Actif en judo", type:"year_active"},
    {label:"Tournois de kata", type:"tournois_kata"},
    {label:"Participation en kata", type:"participation_kata"},
    {label:"Tournois en shiai", type:"tournois_shiai"},
    {label:"Participation en Shiai", type:"participation_shiai"},
    {label:"Certification PNCE", type:""},
    {label:"Directeur Technique", type:"T9"},
    {label:"Assistant Entraîneur", type:"T2"},
    {label:"Entraîneur", type:"T2"},
    {label:"Directeurs de clinique", type:"T3"},
    {label:"Participant aux cliniques", type:"T4"},
    {label:"Certification en kata", type:"T7"},
    {label:"Évaluation en kata", type:"T8"},
    {label:"Certification d'arbitre", type:"T5"},
    {label:"Arbitrage", type:"T7"},
    {label:"Bénévole de tournoi", type:"N2"}
];

const pointYearActive = {
    "":0,
    "Ikkyu":30,
    "Shodan":20,
    "Nidan":20,
    "Sandan":10,
    "Superieure":10
};

const pointTechniques ={
  "T1":{
      MAX:30,
      DA: 5,
      DI: 10,
      CDev:20,
      IV:20,
      V:20
  },
  "T2":{
      MAX:30,
      DA: 5,
      DI: 10,
      CDev:20,
      IV:20,
      V:20
  },
  "T3":{
      MAX:30,
      Prov:10,
      InterProv: 15,
      Nat: 15,
      Int:20
  },
 "T4":{
      MAX:20,
      Prov:5,
      Nat: 5,
      Int: 20
  },
  "T5":{
      MAX:1000,
      Prov:10,
      Nat: 15,
      Int: 20
  },
  "T6":{
      MAX:60,
      Prov:5,
      Nat: 10,
      Int: 20
  },
  "T7":{
      MAX:1000,
      Prov:10,
      Nat: 15,
      Cont: 15,
      Int: 20
  },
    "T8":{
      MAX:30,
      Prov:5,
      InterProv: 10,
      Nat: 15,
      Int: 20
  },
  "T9":{
      MAX:1000,
      T9:30
  },
  "N2":{
      MAX:10,
      Prov:3,
      InterProv: 4,
      Nat: 5,
      Int: 5
  }
};
const prices =[[
    {type:"Shodan", prix:"275"},
    {type:"Nidan", prix:"275"},
    {type:"Sandan", prix:"275"},
    {type:"Yondan", prix:"275"},
    {type:"Godan", prix:"275"},
    {type:"Rokudan", prix:"275"},
    {type:"Shichidan", prix:"275"},
    {type:"Hachidan", prix:"275"},
    {type:"Kudan", prix:"275"},
    {type:"Replacement Diploma", prix:"35"}
      ],[
    {type:"Shodan", prix:"100"},
    {type:"Nidan", prix:"125"},
    {type:"Sandan", prix:"150"},
    {type:"Yondan", prix:"220"},
    {type:"Godan", prix:"325"},
    {type:"Rokudan", prix:"575"},
    {type:"Shichidan", prix:"700"},
    {type:"Hachidan", prix:"950"},
    {type:"Replacement Diploma", prix:"35"}
     ]];
const labelsPromotionDan = [
    "Dan - PJC",
    "Dan - IJF",
    "Dan - National"
];
var data = {};
    var points = {};
    var points = {};
    var index = 0;
    var currentPage = 0;

    var instructorsInput = new ArrayInput("input_instructor_wrapper");
    var pointInput = new ArrayInput("input_point_system_wrapper");
    var pointInput2 = new ArrayInput("input_point_system_wrapper2");
    var sportResult = new ArrayInput("input_sport_result_wrapper", 5);
    var trainer = new ArrayInput("input_trainer_wrapper", 4);
    var instructorTraining = new ArrayInput("input_instructor_training_wrapper", 4);
    var katalist = new ArrayInput("input_kata_list_wrapper", 4);
    var contributionList = new ArrayInput("input_contribution_wrapper", 4);

    $(document).ready( function(){
        setPage(currentPage);
        $(".button-next-page").click(function(){calculatePoints();createTableSummaryPoint();changePageUp();});
        $(".button-previous-page").click(function(){changePageDown();});
        instructorsInput.init();
        pointInput.init();
        pointInput2.init();
        sportResult.init();
        trainer.init();

        createInputPromotionDan();
        createInputYearActive();
        createTableSummaryPoint();
        initData();
        $("input.date").dcalendarpicker();
        $("#rule_technical_points").html(rules_technical_points);
        $("#rules_tournament").html(rules_tournament);
        $("#idSelectCountry").html(COUNTRY_SELECT_HTML);

    } );

    function initData(){
      for (index = 0; index < pages.length ; index++){
        data[pages[index]] = {};
        $("#"+pages[index]+" :input").each(function( ) {
          let member = $(this).attr('name');
            data[pages[index]][member] = "";
        });
      }
    }

    function collectData(page){
      $("#"+page+" :input").each(function( ) {
          let member = $(this).attr('name');
          let value = $(this).val();
          if(value != null && value.length!=0){
            data[page][member] = value;
          }
        });
    }

    function validate(page){
      return true;
      let message = "";
       $("#"+page+" :input").each(function( ) {
          if($(this).attr('required') == "required"){
            let member = $(this).attr('placeholder') || $(this).attr('name');
            let value = $(this).val();
            if(value == null || value.length==0){
              message += member + " cannot be empty.<br>";
            }
          }
        });
       data[page]["message"] = message;
       return message.length == 0;
    }

    function createInputPromotionDan(){
      let html = "";
        for (let i = 1; i <= 9; i++)
          for (let j=0; j < 3 ; j++)
            html += "<input type='text' class='w3-input date' placeholder='"+i + ((i==1)?"er ":"ieme ") + labelsPromotionDan[j]+"'/>";

        $("#idDivPromotionDanInput").html(html);
        $("input.date").dcalendarpicker();
    }

    function createInputYearActive(){
      let html = "<table id='idTablePoints'><tr><th>&nbsp;</th>";

        let year = getCurrentYear();
        for (let i = year ; i >= yearMin; i--){
          html+="<th>" + i +"</th>";
        }
        for (let i = 0 ; i <= year - yearMin + 1; i++){
            if (i==0){
              html+="<tr><td>Niveau</td>";
            }
            else{
              let index = "select"+(getCurrentYear()-(i-1));
              html+="<td><select id=\""+index+"\" name=\""+index+"\" class=\"w3-input\"><option value=\"\" selected=\"selected\">";

              for(let key in pointYearActive) {

                html+="<option value=\""+key+"\">"+key+"</option>";
              }
              html+="</select></td>";
            }
          }
         html+="</tr></table>";
        $("#divYearActive").html(html);
    }

    function createTableSummaryPoint(){
        let html = "<table id='idTablePoints'><tr><th>&nbsp;</th>";
        let year = getCurrentYear();
        for (let i = year ; i >= yearMin; i--){
          html+="<th>" + i +"</th>";
        }

        for(let j= 0 ; j < labels.length ; j++){
          html+="</tr><tr>";
          let label =  labels[j].label;
          let type = labels[j].type;

          for (let i = 0 ; i <= year - yearMin + 1; i++){
            if (i==0){
              html+="<td>" + label +"</td>";
            }
            else{
              let index = getCurrentYear()-(i-1);
              let point = 0;
              if(typeof points[type] !== 'undefined' && typeof points[type][index] !== 'undefined'){
                 point =  parseInt(points[type][index]);
              }
              html+="<td>"+point +"</td>";
            }
          }
        }
        html +="</tr></table>";

        $('#idPointTableSummary').html(html);
        $('#idTablePoints tr:odd').addClass("w3-grey");
    }

    function calculatePoints(){
        calculateYearsInJudo();
        calculateParticipationKata();
        calculatePointTechniques();
        calculatePointNonTechniques();
        calculGrandTotal();
    }

    function  calculateYearsInJudo(){
        points["year_active"] = [];
        let year = getCurrentYear();
        for (let i = year;i >= yearMin; i--) {
           let index = "select"+i;
            let key = $("#"+index).val() || "";
            points["year_active"][i] = pointYearActive[key];
        }
    }

    function calculateParticipationKata(){
        points["participation_kata"] = [];
        points["participation_shiai"] = [];
        points["tournois_shiai"] = [];
        points["tournois_kata"] = [];
        let contestdates = $( "input[name='grade_date[]']" );
        let pointscontest = $( "input[name='points[]']" );
        let gradetypes = $( "select[name='grade_type[]']" );

        contestdates.each(function(index, value){
            let val = value.value;
            if(val.length>0){
                let d = new Date(val);
                let n = d.getFullYear();
                let suffix = (gradetypes.get(index).value || "shiai");
                let pts = parseInt(pointscontest.get(index).value);

                let index_point = "participation_"+ suffix;
                let index_point_contest = "tournois_"+ suffix;

                if(suffix.length>0) {
                    if (index == 0 ){
                        points["participation_kata"][n] =points["participation_kata"][n] || 0;
                        points["participation_shiai"][n] = points["participation_shiai"][n] || 0;
                        points["tournois_kata"][n] = points["tournois_kata"][n] || 0;
                        points["tournois_shiai"][n] = points["tournois_shiai"][n] || 0;
                    }
                     let participation = points[index_point][n];
                     points[index_point_contest][n]+= pts;

                     if(participation < 60){
                         participation = Math.min(60, participation + 5);
                     }
                     points[index_point][n] = participation;
                 }
            }
         });
    }

    function calculatePointTechniques(){
        for (let i in pointTechniques) {
            points[i] = [];
        }
        let contestdates = $( "input[name='grade_date2[]']" );
        let gradeCodes = $( "select[name='grade_code2[]']" );

        contestdates.each(function(index, value){
             let val = value.value;
             if(val.length>0){
                let d = new Date(val);
                let n = d.getFullYear();

                 let code =  $( "select[name='grade_code2[]'] option:selected" ).attr("value");
                 let categorie =  $( "select[name='grade_code2[]'] option:selected" ).attr("category");

                 if(code.length>0 && categorie.length>0){
                     if (index == 0 ){
                         for (let i in pointTechniques) {
                            points[i][n] = points[i][n] || 0;
                         }
                         points[categorie][n] += pointTechniques[categorie][code];
                         points[categorie][n] = Math.min(points[categorie][n], pointTechniques[categorie]['MAX']) ;
                    }
                 }
             }
        });
    }

    function calculatePointNonTechniques(){

    }

    function calculGrandTotal(){
        let total = 0;
        let tc_total = 0;

        for(let i in points){

            for (let j in points[i]) {
                if (i!= 'N2' || i!='year_active'){
                    tc_total += parseInt(points[i][j]);
                }
                 total += parseInt(points[i][j]);
            }
        }
        $("#total_tc_points").text(tc_total);
        $("#total_points").text(total);
    }

function addStepSpan(){
    $("#idDivPageIndicator").html("");
    var html = "<span class=\"step\"></span>";
    var $newdiv1 = $(html);
    for(var i = 0; i < pages.length; i++){
       if(i == currentPage){
           $newdiv1.addClass("active");
       }
       else if (i < currentPage){
            $newdiv1.addClass("finish");
       }
       $("#idDivPageIndicator").append($newdiv1);
       $newdiv1 = $(html);
    }
}

function changePageUp(){
    if(currentPage >= pages.length - 1 ){
        alert("congratulations, you reached the end of the form");
    }
    else{
      collectData(pages[currentPage]);
      if(validate(pages[currentPage])){
        changePage(pages[currentPage], pages[++currentPage]);
      }
      else{
        alert(data[pages[currentPage]].message);
      }
    }
 }


function changePageDown(){
    if(currentPage <= 0){
        alert("Wow something went wrong body");
    }
    else
        changePage(pages[currentPage], pages[--currentPage]);
}

function changePage(idPage, idNextPage){
    animate(idPage, idNextPage);
    scrollToDiv("idDivPageIndicator");
    addStepSpan();
}

function setPage(pageId) {
    currentPage = pageId;

    for(var i=0; i<= pages.length ; i++){
        if(i == currentPage)
            $('#'+ pages[i]).show();
        else
            $('#'+ pages[i]).hide();
    }
    addStepSpan();
}

function animate(idPage, idNextPage) {
    $('#'+idPage).fadeOut(1000, function(){
    $('#'+idNextPage).fadeIn();
    });
}
function scrollToDiv(div){
    $('html, body').animate({
        scrollTop: $("#"+div).offset().top
    }, 'slow');
}

function scrollToPosition(n){
   $('html, body').animate({
        scrollTop:n
        }, 'slow');
}
        
        function ArrayInput (wrapperId, max_fields){
            var o = this;
            this.max_fields      =  max_fields || 10; //maximum input boxes allowed
            this.wrapperId       =  wrapperId;
            this.wrapperSelector       = "#" + wrapperId;
            this.buttonAddSelector = this.wrapperSelector + " .add_field_button";
            this.duplicatableSelector = this.wrapperSelector + " .duplicatable";
            this.x = 1;
            this.init = function(){
                $(this.buttonAddSelector).click(function(e){ //on add input button click
                    e.preventDefault();
                    if(o.x < o.max_fields){ //max input box allowed
                        o.x++; //text box increment
                        var html = '<div>' + $(o.duplicatableSelector).html() + '<a href="#" class="remove_field">Enlever</a></div>';
                        $(o.wrapperSelector).append(html);
                    }
                });

                $(this.wrapperSelector).on("click",".remove_field", function(e){ //user click on remove text
                    e.preventDefault();
                    $(this).parent('div').remove();
                    o.x--;
                });
            };
        };


        function ArrayInputToTable (wrapperId, max_fields){
            var o = this;
            this.max_fields      =  max_fields || 10; //maximum input boxes allowed
            this.wrapperId       =  wrapperId;
            this.wrapperSelector       = "#" + wrapperId;
            this.buttonAddSelector = this.wrapperSelector + " button";
            this.tableSelector = this.wrapperSelector + " .appendable tbody";
            this.elements =
            this.x = 1;
            this.init = function(){
                $(o.wrapperSelector).append("<button class=\"w3-button w3-circle w3-red\">+</button>");
                    var $elements = $(o.wrapperSelector).find("input").not("input[type='button']");
                    var html="";
                      for(var i=0; i<$elements.length;i++) {
                            html+='<th>'+ $elements[i].placeholder +'</th>';
                        }
                 $(o.wrapperSelector).append("<table class=\"appendable\"><tr>"+html+"<th></th></tr></table>");
                $(o.tableSelector).hide();
                $(this.buttonAddSelector).click(function(e){ //on add input button click
                    e.preventDefault();
                    if(o.x < o.max_fields){ //max input box allowed
                        o.x++; //text box increment

                        var $elements = $(o.wrapperSelector).find("input").not("input[type='button']");

                        var html = '<tr>';
                        for(var i=0; i<$elements.length;i++) {
                            html+='<td>'+ $elements[i].value +'</td>';
                        }
                        html+='<td><button class=\"remove_field w3-button w3-circle w3-red\">x</button></td></tr>';

                        $(o.tableSelector).append(html);
                        $(o.tableSelector).show();
                    }
                });

                $(this.wrapperSelector).on("click",".remove_field", function(e){ //user click on remove text
                    e.preventDefault();
                    $(this).closest('tr').remove();
                    o.x--;
                    if(o.x == 1){
                       $(o.tableSelector).hide();
                    }

                });
            };
        };

function getCurrentYear(){
  var dt = new Date();
  var year = dt.getYear() + 1900;
  return year;
}

function dump(obj) {
    let out = '';
    for (let i in obj) {
        out += i + ": " + obj[i] + "\n";
    }
    alert(out);
}