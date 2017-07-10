<style>
    th,td{text-align: center}
</style>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="jumbotron text-center">
                <h1>Wager Type</h1>
            </div>
            <div>
                <table class="table table-responsive table-stipped table-bordered" id="tblWager">
                    <thead>
                        <tr>
                            <th>Track Code</th>
                            <th>Race Date</th>
                            <th>Race Number</th>
                            <th>Post Time</th>
                            <th>Wager</th>
                            <th>Aksyon</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($wager as $key => $value)
                            <tr>
                                <td>{{ $value->track_code }}</td>
                                <td>{{ $value->race_date }}</td>
                                <td>{{ $value->race_number }}</td>
                                <td>{{ $value->race_time }}</td>
                                <td>
                                    <?php
                                        $wagerArr = unserialize($value->extracted);
                                        $temp = "";
                                        foreach ($wagerArr as $index => $val){
                                            $temp .= $val . ', ';
                                        }
                                        $trim = rtrim($temp,",");
                                        echo $trim;
                                    ?>
                                </td>
                                <td><input type="button" class="btn btn-primary editWager" value="EDIT"> </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $("document").ready(function(){
        loadWagerDataTable();
    });
    function loadWagerDataTable(){
        $("#tblWager").DataTable({
            "aaSorting": [],
            "aoColumnDefs": [
                { "aTargets": [ 1 ], "bSortable": false },
                { "aTargets": [ 4 ], "bSortable": false },
                { "aTargets": [ 5 ], "bSortable": false }
            ]
        });
    }
</script>