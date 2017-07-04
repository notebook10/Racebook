<?php date_default_timezone_set('America/Los_Angeles'); ?>
<style>
    table#tblTracksAndTimezone tr,table#tblTracksAndTimezone th{text-align: center;}
</style>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center">
                <h1>Timezone <?php echo date('mdy',time()) ?></h1>
                <button class="btn btn-primary" id="addTmz">ADD TMZ</button>
            </div>
            <div>
                <table class="table table-responsive table-striped table-bordered" id="tblTracksAndTimezone">
                    <thead>
                    <tr>
                        <th>Track Name</th>
                        <th>Track Code</th>
                        <th>Date</th>
                        <th>Timezone</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($tracksAndTimezone as $key => $value)
                        <tr>
                            <td>{{ $value->name }}</td>
                            <td>{{ $value->code }}</td>
                            <td>{{ $value->date }}</td>
                            <td>{{ $value->time_zone === "" ? "#########" : $value->time_zone }}</td>
                            <td><input type="button" class="btn btn-success editTimezone" value="EDIT" data-id="{{ $value->id }}"></td>
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
        loadTimezoneDataTable();
        var BASE_URL = $("#hiddenURL").val();
        function loadTimezoneDataTable(){
            $("#tblTracksAndTimezone").DataTable({
                "aaSorting": [],
                "aoColumnDefs": [
                    { "aTargets": [ 4 ], "bSortable": false }
                ]
            });
        }
        $("body").delegate(".editTimezone","click", function(){
            var id = $(this).data("id");
            $.ajax({
                "url" : BASE_URL + '/getTmzValues',
                type : "POST",
                data : {
                    _token : $('[name="_token"]').val(),
                    id : id
                },
                success : function(response){
                    $("#name").val(response["name"]).attr("readonly",true);
                    $("#code").val(response["code"]).attr("readonly",true);
                    $("#selectTmz option[value='"+ response["tmz"]+"']").attr("selected","selected");
                    $("#id").val(response["id"]);
                },
                error : function(xhr, status, err){
                    swal("Error",err,"error");
                }
            });
            $("#operation").val(1);
            $("#timezoneModal").modal("show");
        });
        $("#submitTmzForm").on("click",function(){
            $.ajax({
                "url" : BASE_URL + '/submitTmz',
                type : "POST",
                data : {
                    _token : $('[name="_token"]').val(),
                    name : $("#name").val(),
                    code : $("#code").val(),
                    selectTmz : $("#selectTmz").val(),
                    operation : $("#operation").val(),
                    id : $("#id").val()
                },
                success : function(response){
                    $("#timezoneModal").modal("hide");
                    if(response == 0){
                        swal("Success","Saved!","success");
                    }else if(response == 1){
                        swal("Success","Updated","success");
                    }
                    $("button.confirm").on("click",function(){
                        location.reload();
                    });
                },
                error : function(xhr, status, error){
                    swal("Error",error,"error");
                }
            });
        });
        $("#addTmz").on("click", function(){
            $("#operation").val(0);
            $("#id").val("");
            $("#name").val("").attr("readonly",false);
            $("#code").val("").attr("readonly",false);
            $("#timezoneModal").modal("show");
        });
    });
</script>

@include('admin/modal/modalTimezone')