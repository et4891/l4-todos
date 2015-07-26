<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Todo List with DatePicker</title>
    {{ HTML::style('css/jquery-ui.css');}}
    {{ HTML::style('css/bootstrap.min.css');}}
    {{ HTML::style('css/bootstrap-theme.min.css');}}
    {{ HTML::style('css/style.css');}}
</head>
<body>
    <div class="well">
        <div class="panel panel-success">
            <div class="panel-heading">
                <h3 class="panel-title">TO DO LIST!<br />{{ $date }}<hr/>Date: <input type="text" id="datepicker" value=""></h3>
            </div>
            <div class="panel-body">
                <ol id="textField"></ol>
                <input class="todo-input" type="text" placeholder="Type New Item Here" />
                <button class="add-btn btn btn-info" type="submit">Add</button>
            </div>
        </div>
    </div>

    {{ HTML::script('js/jquery-1.10.2.js'); }}
    {{ HTML::script('js/jquery-ui.js'); }}
    {{ HTML::script('js/bootstrap.min.js');}}
    {{ HTML::script('js/main.js');}}
</body>
</html>