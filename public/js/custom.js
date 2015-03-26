/**
 * JS utilities
 * author: miroc
 */

$readyFn = function(){
    if (typeof jQuery != 'undefined') {
        // jQuery is loaded => print the version
        //alert(jQuery.fn.jquery);
    }

    console.log( "JS Document ready!" );
    console.log( "JQuery version loaded:" + jQuery.fn.jquery );


};

$initMultiSelect = function(){
    $('.multiselect-basic').multiselect();
};

$initDatePicker = function(){
    /* Create user functionality */
    $('.input-group.date').datepicker({
        format: "dd-mm-yyyy",
        language: "en-GB",
        todayBtn: "linked"
    });


    // Has access
    $("#users_create_has_access").click(function() {
        $(".give_access_panel input")
            .not("#users_create_has_access")
            .attr("disabled", !this.checked);
    });

    // Issue license
    $("#users_create_issue_license").click(function() {
        $(".issue_license_panel input, .issue_license_panel select, .issue_license_panel textarea")
            .not("#users_create_issue_license")
            .attr("disabled", !this.checked);
    });
};

$initDatePickerLangs = function(){
    //https://eternicode.github.io/bootstrap-datepicker/
    $.fn.datepicker.dates['en-GB'] = {
            days: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
            daysShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
            daysMin: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa", "Su"],
            months: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
            monthsShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
            today: "Today",
            clear: "Clear",
            weekStart: 1,
            format: "dd/mm/yyyy"
        };
    $.fn.datepicker.dates['cs'] = {
        days: ["Neděle", "Pondělí", "Úterý", "Středa", "Čtvrtek", "Pátek", "Sobota", "Neděle"],
        daysShort: ["Ned", "Pon", "Úte", "Stř", "Čtv", "Pát", "Sob", "Ned"],
        daysMin: ["Ne", "Po", "Út", "St", "Čt", "Pá", "So", "Ne"],
        months: ["Leden", "Únor", "Březen", "Duben", "Květen", "Červen", "Červenec", "Srpen", "Září", "Říjen", "Listopad", "Prosinec"],
        monthsShort: ["Led", "Úno", "Bře", "Dub", "Kvě", "Čer", "Čnc", "Srp", "Zář", "Říj", "Lis", "Pro"],
        today: "Dnes",
        clear: "Vymazat",
        weekStart: 1,
        format: "d.m.yyyy"
    };
    $.fn.datepicker.dates["sk"] = {
        days: ["Nedeľa", "Pondelok", "Utorok", "Streda", "Štvrtok", "Piatok", "Sobota", "Nedeľa"],
        daysShort: ["Ned", "Pon", "Uto", "Str", "Štv", "Pia", "Sob", "Ned"],
        daysMin: ["Ne", "Po", "Ut", "St", "Št", "Pia", "So", "Ne"],
        months: ["Január", "Február", "Marec", "Apríl", "Máj", "Jún", "Júl", "August", "September", "Október", "November", "December"],
        monthsShort: ["Jan", "Feb", "Mar", "Apr", "Máj", "Jún", "Júl", "Aug", "Sep", "Okt", "Nov", "Dec"],
        today: "Dnes"
    };
};

/* Bring it all up */
$(document).ready(function(){
    $initMultiSelect();
    $initDatePickerLangs();
    $initDatePicker();

    $readyFn();
});