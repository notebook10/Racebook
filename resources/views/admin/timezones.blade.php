<?php date_default_timezone_set('America/Los_Angeles'); ?>
<style>
    table#tblTracksAndTimezone tr,table#tblTracksAndTimezone th{text-align: center;}
</style>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center">
                <h1>Timezone <?php echo date('mdy',time()) ?></h1>
                <button class="btn btn-primary" id="addTmz" disabled>ADD TMZ</button>
            </div>
            <div>
                <table class="table table-responsive table-striped table-bordered table-hover" id="tblTracksAndTimezone">
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
<!--                    --><?php //dd($tracksAndTimezone); ?>
                    @foreach($tracksAndTimezone as $key => $value)
                        <tr>
                            <td>{{ $value->name }}</td>
                            <td><?php echo "<strong>". $value->code . "</strong>"; ?></td>
                            <td>{{ $value->date }}</td>
                            <td>{{ $value->time_zone === null ? "EMPTY" : $value->time_zone }}</td>
                            <td>
                                <?php
                                    if($value->time_zone === null){
                                        echo '<input type="button" class="btn btn-danger saveNewTmz" value="SAVE" data-name="'. $value->name .'" data-code="'. $value->code .'">';
                                    }else{
                                        echo '<input type="button" class="btn btn-success editTimezone" value="EDIT" data-id="' . $value->id .'" data-code="'. $value->time_zone .'">';
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
                    $("#id").val(response["id"]);
                    $("#name").val(response["name"]).attr("readonly",true);
                    $("#code").val(response["code"]).attr("readonly",true);
//                    $("#selectTmz option[value='"+ response["tmz"]+"']").attr("selected","selected");
                    $("#selectTmz").val(response["tmz"]);
                    $("#operation").val(1);
                    $("#timezoneModal").modal("show");
                },
                error : function(xhr, status, err){
                    swal("Error",err,"error");
                }
            });
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
        $("body").delegate(".saveNewTmz","click", function(){
            clearForm();
            var name = $(this).data("name");
            var code = $(this).data("code");
            $("#selectTmz").val($("#selectTmz option:first").val());
            $("#operation").val("0");
            $("#name").val(name).attr("readonly",true);
            $("#code").val(code).attr("readonly",true);
            $("#timezoneModal").modal("show");
        });
        $('#timezoneModal').on('hidden.bs.modal', function () {
            clearForm();
        });
        function clearForm(){
            var frm = $("#frmTmz");
            frm[0].reset();
        }
    });
</script>

@include('admin/modal/modalTimezone')