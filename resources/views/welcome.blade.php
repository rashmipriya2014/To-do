<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="csrf-token" content="{{ csrf_token() }}" />
        <title>To-Do</title>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

        <!-- Bootbox-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.5.2/bootbox.min.js"></script>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <!-- Styles -->
        <link href="{{ asset('css/style.css') }}" rel="stylesheet" type="text/css">

    </head>
    <body>
        <div class="td_container"> 
            <h1 class="todo_heading">To Do Corner</h1>
            <table class="table table-striped">
                <thead>
                    <tr class="thd1">
                        <th class="col" colspan="3">
                            <div class="checkbox">
                                <label><input type="checkbox" value="" id="show_all" onclick="show_all()">Show All</label>
                            </div>
                        </th>           
                    </tr>
                    <tr class="thd2">
                        <th colspan="3">
                            <div class="form-group td_field_wrao input">
                                <label for="to_do_text">Add To Do</label>
                                <input type="text" class="form-control" id="to_do_text" placeholder="Enter To Do">
								<button type="button" class="btn btn-success" onclick="addToDo()">Add</button>
                            </div>							
                        </th>                        
                    </tr>
                </thead>
                <tbody id="td_tbody">
                    <!-- To Do Details -->
                    @foreach($all_to_do as $todo) 
                        <tr>
                            <td>
                                <div class="checkbox">
                                    <label><input type="checkbox" value="" onclick="updateToDo(this,{{$todo->id}})"></label>
                                </div>
                            </td>
                            <td>{{$todo->to_do}}</td>
                            <td><i class="fa fa-trash-o" style="font-size:24px" onclick="deleteTodo(this,{{$todo->id}})"></i></td>
                        </tr>
                    @endforeach
					
                </tbody>
            </table>           
        </div>
		<script>
			function addToDo(){
				var todo = $('#to_do_text').val();
				var isValid = validateTodo(todo);
                
                if(isValid == 1){
                    $.ajax({
                        type:'POST',
                        url:'{{route("add")}}',
                        data:{_token: $('meta[name="csrf-token"]').attr('content'),todo: todo},
                        success:function(data) {
                            show_all('incomplete');
                            $('#to_do_text').val('');
                        }
                    });	
                }							
            }
            
            function validateTodo(toDo){
                var is_valid = 1;
                if(toDo == ''){
                    is_valid = 0;
                    showErrorMsg('Input field can not be empty !');
                }

                // Validate duplicacy
                $.ajax({
                    type:'POST',
                    url:'{{route("validate_duplicacy")}}',
                    data:{_token: $('meta[name="csrf-token"]').attr('content'),todo: toDo},
                    async: false,
                    success:function(data) {
                        if(data.status == 1){
                            is_valid = 0;
                            showErrorMsg('Entered to do already exist!');
                        }
                    }
                });	
                
                return is_valid;
            }

            function showErrorMsg(msg){
                bootbox.alert({
                    message: msg,
                    backdrop: true
                });
            }
            
            
            function updateToDo(elem,id){
                var checkbox_val = elem.value; 
                // update to do as done in database                
                if($(elem).prop('checked')==true){
                    $.ajax({
                        type:'POST',
                        url:'{{route("update_as_done")}}',
                        data:{_token: $('meta[name="csrf-token"]').attr('content'),id:id},
                        success:function(data) {
                            if(data.status == 1){
                                $(elem).closest("tr").remove();
                            }
                        }
                    });
                }
            }

            function deleteTodoProceed(elem,id){
                $.ajax({
                    type:'POST',
                    url:'{{route("delete_to_do")}}',
                    data:{_token: $('meta[name="csrf-token"]').attr('content'),id:id},
                    success:function(data) {
                        if(data.status == 1){
                            $(elem).closest("tr").remove();
                        }
                    }
                });
            }

            function deleteTodo(elem,id){
                bootbox.confirm({
                    title: "Delete To Do",
                    message: "Are you sure want to delete this To do? This cannot be undone.",
                    buttons: {
                        cancel: {
                            label: '<i class="fa fa-times"></i> Cancel'
                        },
                        confirm: {
                            label: '<i class="fa fa-check"></i> Confirm'
                        }
                    },
                    callback: function (result) {
                        if(result){
                            // delete from database
                            deleteTodoProceed(elem,id);
                        }
                        
                    }
                });
            }            
            
            function show_all(){
                var type = '';
                if($('#show_all').prop('checked')==true){
                    var type = 'show_all';
                }
                getRefreshTable(type);
            }

            function getRefreshTable(type) {                
                
                var token = "{{ csrf_token() }}";
                var data = { '_token' : token,'type' : type};
                var address = "{{route('get_details')}}";
                
                $.post(address,data)
                .done( res =>{
                console.log(res);
                refreshTable(res);
                })
                .fail( err =>{ 
                    console.log(err); 
                    errorMessage('Something went wrong');
                })
            }

            function refreshTable(data){
                $('#td_tbody').empty();
                var str = '';
                data.forEach((element,index) => {
                    var checked = '';
                    if(element.is_done == 1){
                        checked = 'checked';
                    }

                    var template= 
                    '<tr>'+                        
                        '<td>'+
                            '<div class="checkbox">'+
                                '<label><input class="done_task" type="checkbox" value="" onclick="updateToDo(this,'+element.id+')" '+checked+'></label>'+
                            '</div>'+
                        '</td>'+
                        '<td>'+element.to_do+'</td>'+                     
                        '<td>'+
                            '<i class="fa fa-trash-o" style="font-size:24px" onclick="deleteTodo(this,'+element.id+')"></i>'+
                        '</td>'+
                    '</tr>';
                    str=str+template;
                });
                
                $('#td_tbody').append(str);
            }
				
		</script>
    </body>
</html>
