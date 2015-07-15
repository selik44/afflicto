@extends('admin.layout')

@section('title')
    Products - @parent
@stop

@section('page')
    <h2>Products</h2>

    {!! $filters !!}

    <hr>

    {!! $table !!}
    <br>
    {!! $pagination !!}

    <hr/>

    <div id="tableActions">
        <button disabled class="large move"><i class="fa fa-move"></i> Move</button>
    </div>
@stop

@section('scripts')
    @parent
    <script>

        var actions = $("#tableActions");
        var selectAll = $('table input[type="checkbox"][name="laratable-select-all"]');
        var selectRows = $('table input[type="checkbox"].laratable-select-row');

        //request move
        var moveAction = "{{route('admin.products.move')}}";
        actions.find('.move').click(function() {
            var products = [];
            selectRows.each(function() {
                if ($(this).prop('checked')) {
                    products.push($(this).attr('value'));
                }
            });

            var url = "{{route('admin.products.move', '')}}/" + products.join(',');
            window.location.href = url;
        });

        //quickly select all or none
        selectAll.change(function() {
            selectRows.prop('checked', $(this).prop('checked'));
        });

        //change select state of a single row
        selectRows.change(function() {
            selectAll.prop('checked', false);

            //is anything checked?
            var numChecked = 0;
            selectRows.each(function() {
                console.log('checking select row');
                if ($(this).prop('checked')) {
                    numChecked++;
                }
            });

            //is all checked?
            if (numChecked == selectRows.length) {
                selectAll.prop('checked', true);
            }

            //is anything checked?
            if (numChecked > 0) {
                //enable actions
                actions.find('input, button').removeAttr('disabled');
            }else {
                //disable actions
                actions.find('input, button').attr('disabled', 'disabled');
            }
        });

    </script>
@stop