<style>
    .mis{background: #eee;height: 50px;border: 1px solid #fff;padding: 15px;}
</style>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="jumbotron text-center">
                <h1>Administrator</h1>
                <h5>{{ \Illuminate\Support\Facades\Auth::user()->firstname }}</h5>
            </div>
            <?php
                use App\Tracks;
                foreach ($mismatched as $key => $value){
                    echo "<div class='col-sm-4 mis'>". Tracks::getTrackNameWithCode($value->track_code)->name . " #" . $value->race_number . " " . $value->race_date ."</div>";
                }
            ?>
        </div>
    </div>
</div>