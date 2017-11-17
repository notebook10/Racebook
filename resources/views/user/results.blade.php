<?php date_default_timezone_set('America/Los_Angeles'); ?>
<style>
    #datepickerResults{text-align: center;cursor: pointer;}
    .trk{padding:2px 15px;background: #00724b;color:#fff;border-radius:10px;margin:5px;
        cursor: pointer;}
    h5{font-weight: bold;}
    #datepickerDiv{text-align: center;}
    #resultsDiv{margin-top: 20px;}
    .loader{
        background: url("../images/ajax-loader.gif");
        background-repeat: no-repeat;
        background-position: center;
        width:100%;
        height:80px;
        position: absolute;
        top:200px;
        display: none;
    }
    thead,th{background: #bab7ad;text-align: center;}
    .raceHeader{background: #00724b;font-weight: bold;padding: 8px;color:#fff;}
    #trkHeader{font-weight: bold;color:#00724b; }
    .pnumTD{width: 60px;}
</style>
<script src="{{ asset('js/homeResults.js') }}"></script>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="jumbotron text-center">
                <?php
                if (!isset($_SESSION)) session_start();
                if(!isset($_SESSION["username"])){
                    echo "<h1>Session Expired! Please login again.</h1>";
                }else{
                    echo "<h1>Summary Results</h1>";

                ?>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <h1 id="trackHeader">Tracks <?php echo date('Y-m-d',time()); ?></h1>
                    <div id="trkDiv">
                        @foreach($tracks as $index => $value)
                            <div class="trk">
                                <h5 data-date="{{ $value->date }}" data-code="{{ $value->code }}">{{ $value->name }}</h5>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-8">
                    <div id="datepickerDiv">
                        View results for: <input type="text" id="datepickerResults"  value="<?php echo date('Y-m-d',time()) ?>">
                        <h1 id="trkHeader"></h1>
                    </div>
                    <div class="loader"></div>
                    <div id="resultsDiv">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>