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
        .nothing-todo{list-style: none;}
        .done-btn, .delete-btn{float: right;}
        .done-strike{text-decoration: line-through;}
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

            $('ol#textField').on('click', '.done-btn', doneButton);

            $('ol#textField').on('click', '.delete-btn', deleteButton);

//            function doubleD(action){
//                var $clicked = $(this); //Making sure only work with the current element
//
//                var $cLI = $clicked.closest('li');  //Find the closest li element clicked
//
//                //Goes to the closest li element and get only the content in li
//                //Does not include the child element which is what I want
//                //Or else the done / delete text will be shown too
//                var todoText = $cLI.clone().children().remove().end().text();
//
//                var getID = $cLI.attr('id');  //get the id of the todoText
//
//                if(action == 'done'){
//                    var $cSpan = $clicked.closest('span');  //Find the closest span element clicked
//                    $.ajax({
//                        method: "POST",
//                        url: "/done",
//                        dataType: "json",
//                        data: {todoText: todoText, getID: getID},
//                        success: function () {
//                            $cLI.addClass('done-strike');
//                            $cSpan.removeClass('done-btn btn-success');
//                            $cSpan.closest('span').text('Delete');
//                            $cSpan.closest('span').addClass('delete-btn btn-warning');
//
//                        }
//                    });
//                }
//
//                if(action == 'delete'){
//                    $.ajax({
//                        method: "POST",
//                        url: "/delete",
//                        dataType: "json",
//                        data: {todoText: todoText, getID: getID},
//                        success: function () {
//                            $cLI.next('hr').remove();
//                            $cLI.remove();
//                        }
//                    });
//                }
//            }

            function deleteButton(e){
                e.preventDefault();

                var $clicked = $(this); //Making sure only work with the current element

                var $cLI = $clicked.closest('li');  //Find the closest li element clicked

                //Goes to the closest li element and get only the content in li
                //Does not include the child element which is what I want
                //Or else the done / delete text will be shown too
                var todoText = $cLI.clone().children().remove().end().text();

                var getID = $cLI.attr('id');  //get the id of the todoText

                $.ajax({
                    method: "POST",
                    url: "/delete",
                    dataType: "json",
                    data: {todoText: todoText, getID: getID},
                    success: function () {
                        $cLI.next('hr').remove();
                        $cLI.remove();
                    }
                });
            }

            function doneButton(e){
                e.preventDefault();

                var $clicked = $(this); //Making sure only work with the current element

                var $cLI = $clicked.closest('li');  //Find the closest li element clicked
                var $cSpan = $clicked.closest('span');  //Find the closest span element clicked

                //Goes to the closest li element and get only the content in li
                //Does not include the child element which is what I want
                //Or else the done / delete text will be shown too
                var todoText = $cLI.clone().children().remove().end().text();

                var getID = $cLI.attr('id');  //get the id of the todoText

                $.ajax({
                    method: "POST",
                    url: "/done",
                    dataType: "json",
                    data: {todoText: todoText, getID: getID},
                    success: function () {
                        $cLI.addClass('done-strike');
                        $cSpan.removeClass('done-btn btn-success');
                        $cSpan.closest('span').text('Delete');
                        $cSpan.closest('span').addClass('delete-btn btn-warning');

                    }
                });
            }


            /********************************************************************************/

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
                                appendData(data);
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
                            appendData(data);
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

            /* appendData Function */
            // Use each loop to loop through the data
            // Using the done value passed back from PHP from database to check if the item is done or not
            // If not done, data will be append with a green done button
            // If done, data will be append with a delete button and item with strikethrough
            function appendData(data){
                $.each(data, function(index, value){
                    if(value['done'] == 0){
                        $('#textField').append('<li id=' + '"' + value['id'] + '"' + '>' + value['todo'] + '<span class="btn btn-xs btn-success done-btn">Done</span></li><hr>');
                    }else if(value['done'] === 1){
                        $('#textField').append('<li id=' + '"' + value['id'] + '"' + 'class="done-strike">'+value['todo']+'<span class="btn btn-xs btn-warning delete-btn">Delete</span></li><hr>');
                    }
                });
            }
        });
    </script>
</body>
</html>