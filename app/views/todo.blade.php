<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">

    <title>jQuery UI Datepicker - Default functionality</title>
    {{ HTML::style('css/jquery-ui.css');}}
    {{ HTML::style('css/bootstrap.min.css');}}
    {{ HTML::style('css/bootstrap-theme.min.css');}}
{{--    {{ HTML::style('css/style.css');}}--}}
</head>
<body>

        {{--<p>Date: <input type="text" id="datepicker" value=""></p>--}}
        {{--<div id="textField" class="showValue" style="border:1px solid blue;height: 200px; width: 200px;"></div>--}}
        {{--<input class="todo-input" type="text" placeholder="Type New Item Here" />--}}
        {{--<button class="add-btn" type="submit">Add</button>--}}

    <div class="well">
        <div class="panel panel-success">
            <div class="panel-heading">
                <h3 class="panel-title">TO DO LIST! <hr/>Date: <input type="text" id="datepicker" value=""></h3>
            </div>
            <div class="panel-body">
                <div id="textField" class="showValue" ></div>
                <input class="todo-input" type="text" placeholder="Type New Item Here" />
                <button class="add-btn" type="submit">Add</button>
            </div>
        </div>
    </div>

    {{ HTML::script('js/jquery-1.10.2.js'); }}
    {{ HTML::script('js/jquery-ui.js'); }}
    {{ HTML::script('js/bootstrap.min.js');}}
    <script>
        $(function () {
            /*Enable datePicker API created by jQuery*/
            //Set the date format to yy-mm-dd
            $( "#datepicker" ).datepicker({
                dateFormat: "yy-mm-dd"
            });

            // On mouse leave date will be picked and pass the date into database to search
            $( "#ui-datepicker-div" )
                    .mouseleave(getDatePickerValue)
                    .mouseleave();

            $('.add-btn').click(addTodoText);


            /* Add Todo List Function */
            // Add whatever is typed in the input box into the database
            function addTodoText(){
                var todoTextInput = $('.todo-input').val();
                if(todoTextInput){
                    $.ajax({
                        method: "POST",
                        url: "/add",
                        dataType: "json",
                        data: {todoTextInput: todoTextInput},
                        success: function (data) {
                            console.log(data);
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
                //check if dateValue is empty string
//                    console.log(dateValue == '');
                if(dateValue == ''){
                    dateValue = null
                }else{
                    searchDateAjax(dateValue);
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
                                $('#textField').append('<p>'+value['todo']+'</p>');
                            });
                        }else{
                            $('#textField').empty();
                            $('#textField').append('<p>NO DATA IN THIS DATE</p>');
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>