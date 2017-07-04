<div id="timezoneModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">TRACK TIMEZONE</h4>
            </div>
            <div class="modal-body">
                <form>
                    <input type="hidden" id="operation" value="0" name="operation">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" id="id" value="">
                    <div class="form-group">
                        <label for="email">Track Name:</label>
                        <input type="text" class="form-control" id="name" name="name">
                    </div>
                    <div class="form-group">
                        <label for="pwd">Track Code:</label>
                        <input type="text" class="form-control" id="code" name="code">
                    </div>
                    <div class="form-group">
                        <label for="pwd">Timezone:</label>
                        <select id="selectTmz" class="form-control" name="selectTmz">
                            <option selected disabled class="text-center">--SELECT--</option>
                            <option value="PDT">Pacific</option>
                            <option value="MDT">Mountain</option>
                            <option value="CDT">Central</option>
                            <option value="EDT">Eastern</option>
                        </select>
                    </div>
                    {{--<button type="submit" class="btn btn-default">Submit</button>--}}
                </form>
            </div>
            <div class="modal-footer">
                <input type="button" id="submitTmzForm" value="Submit" class="btn btn-success">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>