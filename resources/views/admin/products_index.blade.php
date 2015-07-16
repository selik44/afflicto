@extends('admin.layout')

@section('title')
	Products - @parent
@stop

@section('page')
    <div class="row end">
        <h3 class="pull-left end" style="margin-right: 2rem">Products</h3>
        <div class="pull-left">{!! $filters !!}</div>
    </div>
    <hr class="small">

    {!! $table !!}
    <hr class="end">
    {!! $pagination !!}

    <div id="tableActions">
        <button disabled class="large move"><i class="fa fa-plane"></i> Move</button>
    </div>

    <div class="modal fade center" id="moveModal">
        <a href="#" data-toggle-modal="#moveModal" class="modal-dismiss"><i class="fa fa-close"></i> close</a>
        <header class="modal-header">
            <h3 class="end">Move Products</h3>
        </header>

        <article class="modal-content">

        </article>
    </div>
@stop

@section('scripts')
    @parent
    <script>
        var actions = $("#tableActions");
        var selectAll = $('table input[type="checkbox"][name="laratable-select-all"]');
        var selectRows = $('table input[type="checkbox"].laratable-select-row');

        //open move modal
        var packListAction = "{{route('admin.orders.multipacklist')}}";
        actions.find('.move').click(function() {
            var products = [];
            selectRows.each(function() {
                if ($(this).prop('checked')) {
                    products.push($(this).attr('value'));
                }
            });


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