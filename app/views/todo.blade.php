<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">

    <title>jQuery UI Datepicker - Default functionality</title>
    {{ HTML::style('css/jquery-ui.css');}}
    {{ HTML::style('css/style.css');}}
</head>
<body>

<p>Date: <input type="text" id="datepicker" value=""></p>
<p class="showValue" style="border:1px solid blue;height: 200px; width: 200px;"></p>
<div id="textField"></div>

</body>
    {{ HTML::script('js/jquery-1.10.2.js'); }}
    {{ HTML::script('js/jquery-ui.js'); }}
<script>
    $(function () {
        /*Enable datePicker API created by jQuery*/
        //Set the date format to yy-mm-dd
        $( "#datepicker" ).datepicker({
            dateFormat: "yy-mm-dd"
        });

        // On mouse leave
        $( "#ui-datepicker-div" )
                .mouseleave(storeDatePickerValue)
                .mouseleave();

        function storeDatePickerValue(){
            var dateValue = $( "#datepicker" ).val();
            //check if dateValue is empty string
//                    console.log(dateValue == '');
            if(dateValue == ''){
                dateValue = null
            }else{
                $( ".showValue" ).text( dateValue );
                // on mouseleave clicked and the dateValue picked will be in console
//            console.log('clicked');
                console.log(dateValue);

                $.ajax({
                    method: "POST",
                    url: "/search",
                    dataType: "json",
                    data: {dateValue: dateValue},
                    success: function (data) {
                        console.log(data);
                    }
                });
            }

        }
    });
</script>
</html>