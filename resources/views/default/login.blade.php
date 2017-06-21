<form id="frmLogin" method="post" action="login">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    Username: <input type="text" name="username"><br>
    Password: <input type="password" name="password"><br>
    <input type="submit" id="submit">
    <ul class="errormessage">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</form>