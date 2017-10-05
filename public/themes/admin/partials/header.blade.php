<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="dashboard">
                <img src="http://placehold.it/150x50&text=Logo" alt="">
            </a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li>
                    <a href="tracks">Tracks</a>
                </li>
                <li>
                    <a href="timezones">Timezones</a>
                </li>
                <li>
                    <a href="horses">Horses</a>
                </li>
                <li>
                    <a href="wager">Wager</a>
                </li>
                <li>
                    <a href="bets">PastBets</a>
                </li>
                <li>
                    <a href="pendingBets">PendingBets</a>
                </li>
                <li>
                    <a href="results">Results</a>
                </li>
                <li>
                    <a href="http://58.69.12.117/racebookdata/" target="_blank">Download</a>
                </li>
                <li>
                    <a href="http://58.69.12.117/racebookdata/results.php" target="_blank">GetResults</a>
                </li>
                {{--<li>--}}
                    {{--<a href="http://58.69.12.117/racebookdata/scratch.php" target="_blank">GetScratch</a>--}}
                {{--</li>--}}
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">Scratches
                        <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="http://58.69.12.117/racebookdata/scratch.php" target="_blank">GetScratch</a></li>
                        <li><a href="scratches">ViewScratches</a></li>
                    </ul>
                </li>
                <li>
                    <a href="logout">Logout</a>
                </li>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container -->
</nav>

<style>
    body {
        padding-top: 70px; /* Required padding for .navbar-fixed-top. Change if height of navigation changes. */
    }

    .navbar-fixed-top .nav {
        padding: 15px 0;
    }

    .navbar-fixed-top .navbar-brand {
        padding: 0 15px;
    }

    @media(min-width:768px) {
        body {
            padding-top: 100px; /* Required padding for .navbar-fixed-top. Change if height of navigation changes. */
        }

        .navbar-fixed-top .navbar-brand {
            padding: 15px 0;
        }
    }
</style>