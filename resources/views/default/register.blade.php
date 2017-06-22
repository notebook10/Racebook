<form method="post" action="insertuser">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="text" name="firstname">
    <input type="text" name="lastname">
    <input type="text" name="username">
    <input type="password" name="password">
    <input type="hidden" name="usertype" value="1">
    <input type="submit" value="Insert">
</form>