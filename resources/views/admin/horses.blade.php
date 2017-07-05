<style>
    table tr,th{text-align: center}
</style>
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center">
                <h1>Horses</h1>
            </div>
            <div>
                <table class="table table-responsive table-bordered table-stripped" id="tblHorses">
                    <thead>
                        <tr>
                            <th>PP</th>
                            <th>Horse Name</th>
                            <th>Jockey</th>
                            <th>Race Time</th>
                            <th>Race Number</th>
                            <th>Race Track</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($horses as $key => $value)
                            <tr>
                                <td>{{ $value->pp }}</td>
                                <td>{{ $value->horse }}</td>
                                <td>{{ $value->jockey }}</td>
                                <td>{{ $value->race_time }}</td>
                                <td>{{ $value->race_number }}</td>
                                <td>{{ $value->race_track }}</td>
                                <td>
                                    <button class="btn btn-success" data-id="{{ $value->id }}" disabled>EDIT</button>
                                    <?php
                                        if($value->pp != "SCRATCHED"){
                                            echo '<button class="btn btn-danger btnScratch" data-id="'. $value->id .'">SCRATCH</button>';
                                        }
                                    ?>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    loadHorsesDataTable();
    var BASE_URL = $("#hiddenURL").val();
    function loadHorsesDataTable(){
        $("#tblHorses").DataTable({
            "aaSorting": [],
            "aoColumnDefs": [
//                { "aTargets": [ 4 ], "bSortable": false }
            ]
        });
    }
    $("body").delegate(".btnScratch","click", function(){
        var id = $(this).data("id");
        swal({
                title: "Are you sure?",
                text: "You will not be able to recover this after the changes!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, scratch it!",
                cancelButtonText: "No, cancel plx!",
                closeOnConfirm: false,
                closeOnCancel: false
            },
            function(isConfirm){
                if (isConfirm) {
                    // SCRATCHED
                    $.ajax({
                        "url" : BASE_URL + '/scratch',
                        type : "POST",
                        data : {
                            _token : $('[name="_token"]').val(),
                            id : id
                        },
                        success : function(response){
                            swal("SCRATCH IT","Success","success");
                            $("button.confirm").on("click",function(){location.reload();});
                        },
                        error : function(xhr, status, err){
                            swal("Error",err,"error");
                        }
                    });
                } else {
                    swal("Cancelled", "Your imaginary file is safe :)", "error");
                }
            });
    });
</script>