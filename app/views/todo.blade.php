<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">

    <title>jQuery UI Datepicker - Default functionality</title>
    {{ HTML::style('css/jquery-ui.css');}}
    {{ HTML::style('css/bootstrap.min.css');}}
    {{ HTML::style('css/bootstrap-theme.min.css');}}
    <style>
        hr{margin-top: 0;}
        .nothing-todo{ list-style: none;}
    </style>
{{--    {{ HTML::style('css/style.css');}}--}}
</head>
<body>

        {{--<p>Date: <input type="text" id="datepicker" value=""></p>--}}
        {{--<div id="textField" class="showValue" style="border:1px solid blue;height: 200px; width: 200px;"></div>--}}
        {{--<input class="todo-input" type="text" placeholder="Type New Item Here" />--}}
        {{--<button class="add-btn btn btn-info" type="submit">Add</button>--}}

    <div class="well">
        <div class="panel panel-success">
            <div class="panel-heading">
                <h3 class="panel-title">TO DO LIST!<br />{{ $date }}<hr/>Date: <input type="text" id="datepicker" value=""></h3>
            </div>
            <div class="panel-body">
                <ol id="textField"></ol>
                <input class="todo-input" type="text" placeholder="Type New Item Here" />
                <button class="add-btn btn btn-info" type="submit">Add</button>
                {{--<button class="test btn btn-warning">Current</button>--}}
                {{--<button class="test1 btn btn-warning">global vs current</button>--}}
                {{--<button class="test2 btn btn-warning">global</button>--}}
            </div>
        </div>
    </div>

    {{ HTML::script('js/jquery-1.10.2.js'); }}
    {{ HTML::script('js/jquery-ui.js'); }}
    {{ HTML::script('js/bootstrap.min.js');}}
    <script>
        $(function () {
            var globalCurrentDate = getToday();
            var globalDateValue = null;

//            $('.test2').click(function(){
//                console.log(globalDateValue);
//            });
//
//            $('.test1').click(function(){
//                console.log(globalCurrentDate > globalDateValue);
//            });
//
//            $('.test').click(function(){
//               console.log(globalCurrentDate);
//            });

            /*Enable datePicker API created by jQuery*/
            //Set the date format to yy-mm-dd
            $( "#datepicker" ).datepicker({
                dateFormat: "yy-mm-dd"
            });

            // On mouse leave date will be picked and pass the date into database to search
            $( "#ui-datepicker-div" )
                    .mouseleave(getDatePickerValue)
                    .mouseleave();

            // Add button to add items onto todo list choose a current or future date to add
            $('.add-btn').click(addTodoText);

            /* Add Todo List Function */
            // Add whatever is typed in the input box into the database
            // "Please enter something" will pop up as an alert if nothing is entered
            function addTodoText(){
                var todoTextInput = $('.todo-input').val();
                var dateValue = globalDateValue;
                if(todoTextInput){
                    $.ajax({
                        method: "POST",
                        url: "/add",
                        dataType: "json",
                        data: {todoTextInput: todoTextInput, dateValue: dateValue},
                        success: function (data) {
                            $('.todo-input').val("");
                            $('.todo-input').attr('placeholder', 'More Items to add?')
                            if(data != 'empty'){
                                $('#textField').empty();
                                $.each(data, function(index, value){
                                    $('#textField').append('<li>'+value['todo']+'</li><hr>');
                                });
                            }
                        }
                    });
                }else{
                    alert('Please enter something');
                }
            }

            /* Pick Date Function */
            // Picking a date to do a search using searchDateAjax()
            // If date is empty nothing will be done.
            function getDatePickerValue(){
                var dateValue = $( "#datepicker" ).val();

                //If no date is chosen
                if(dateValue == ''){
                    dateValue = null
                }else{
                    searchDateAjax(dateValue);
                }
                globalDateValue = dateValue;  //storing this to a global variable for addTodoText()

                //Checking if the date is older than current date, if it is disable the add button
                if(globalCurrentDate > globalDateValue){
                    $('.add-btn').attr('disabled', 'disabled');
                }else{
                    $('.add-btn').removeAttr('disabled')
                }
            }

            /* Search Function */
            // Using the date selected and passing the date value to database
            // Find the matching date and pull out the todoText column from database
            function searchDateAjax(dateValue){
                $.ajax({
                    method: "POST",
                    url: "/search",
                    dataType: "json",
                    data: {dateValue: dateValue},
                    success: function (data) {
                        if(data != 'empty'){
                            $('#textField').empty();
                            $.each(data, function(index, value){
                                $('#textField').append('<li>'+value['todo']+'</li><hr>');
                            });
                        }else{
                            $('#textField').empty();
                            $('#textField').append("<li class='nothing-todo'>Nothing Entered For Today</li>");
                        }
                    }
                });
            }

            /* getToday Function */
            // Just a simple function to get today's date
            // Currently used to compare with the date selected
            function getToday(){
                var today = new Date();
                var dd = today.getDate();
                var mm = today.getMonth()+1; //January is 0!
                var yy = today.getFullYear();

                if(dd<10) {
                    dd='0'+dd
                }

                if(mm<10) {
                    mm='0'+mm
                }

                return globalCurrentDate = yy+'-'+mm+'-'+dd;
            }
        });
    </script>
</body>
</html>